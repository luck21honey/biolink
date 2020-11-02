<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Logger;
use Altum\Models\User;

class WebhookStripe extends Controller {

    public function index() {

        /* Initiate Stripe */
        \Stripe\Stripe::setApiKey($this->settings->stripe->secret_key);

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $this->settings->stripe->webhook_secret
            );

            if(!in_array($event->type, ['invoice.paid', 'checkout.session.completed'])) {
                die();
            }

            $session = $event->data->object;

            $payment_id = $session->id;
            $payer_id = $session->customer;
            $payer_object = \Stripe\Customer::retrieve($payer_id);
            $payer_email = $payer_object->email;
            $payer_name = $payer_object->name;

            switch($event->type) {
                /* Handling recurring payments */
                case 'invoice.paid':

                    $payment_total = in_array($this->settings->payment->currency, ['MGA', 'BIF', 'CLP', 'PYG', 'DJF', 'RWF', 'GNF', 'UGX', 'JPY', 'VND', 'VUV', 'XAF', 'KMF', 'KRW', 'XOF', 'XPF']) ? $session->amount_paid : $session->amount_paid / 100;
                    $payment_currency = strtoupper($session->currency);

                    /* Process meta data */
                    $metadata = $session->lines->data[0]->metadata;

                    $user_id = (int) $metadata->user_id;
                    $plan_id = (int) $metadata->plan_id;
                    $payment_frequency = $metadata->payment_frequency;
                    $code = isset($metadata->code) ? $metadata->code : '';
                    $discount_amount = isset($metadata->discount_amount) ? $metadata->discount_amount : 0;
                    $base_amount = isset($metadata->base_amount) ? $metadata->base_amount : 0;
                    $taxes_ids = isset($metadata->taxes_ids) ? $metadata->taxes_ids : null;

                    /* Vars */
                    $payment_type = $session->subscription ? 'recurring' : 'one_time';
                    $payment_subscription_id =  $payment_type == 'recurring' ? 'stripe###' . $session->subscription : '';

                    break;

                /* Handling one time payments */
                case 'checkout.session.completed':

                    /* Exit when the webhook comes for recurring payments as the invoice.paid event will handle it */
                    if($session->subscription) {
                        die();
                    }

                    $payment_total = in_array($this->settings->payment->currency, ['MGA', 'BIF', 'CLP', 'PYG', 'DJF', 'RWF', 'GNF', 'UGX', 'JPY', 'VND', 'VUV', 'XAF', 'KMF', 'KRW', 'XOF', 'XPF']) ? $session->amount_total : $session->amount_total / 100;
                    $payment_currency = strtoupper($session->currency);

                    /* Process meta data */
                    $metadata = $session->metadata;

                    $user_id = (int) $metadata->user_id;
                    $plan_id = (int) $metadata->plan_id;
                    $payment_frequency = $metadata->payment_frequency;
                    $code = isset($metadata->code) ? $metadata->code : '';
                    $discount_amount = isset($metadata->discount_amount) ? $metadata->discount_amount : 0;
                    $base_amount = isset($metadata->base_amount) ? $metadata->base_amount : 0;
                    $taxes_ids = isset($metadata->taxes_ids) ? $metadata->taxes_ids : null;

                    /* Vars */
                    $payment_type = $session->subscription ? 'recurring' : 'one_time';
                    $payment_subscription_id =  $payment_type == 'recurring' ? 'stripe###' . $session->subscription : '';

                    break;
            }

            /* Get the plan details */
            $plan = Database::get('*', 'plans', ['plan_id' => $plan_id]);

            /* Just make sure the plan is still existing */
            if(!$plan) {
                http_response_code(400);
                die();
            }

            /* Make sure the transaction is not already existing */
            if(Database::exists('id', 'payments', ['payment_id' => $payment_id, 'processor' => 'stripe'])) {
                http_response_code(400);
                die();
            }

            /* Make sure the account still exists */
            $user = Database::get(['user_id', 'email', 'payment_subscription_id', 'billing'], 'users', ['user_id' => $user_id]);

            if(!$user) {
                http_response_code(400);
                die();
            }

            /* Unsubscribe from the previous plan if needed */
            if(!empty($user->payment_subscription_id) && $user->payment_subscription_id != $payment_subscription_id) {
                try {
                    (new User(['settings' => $this->settings]))->cancel_subscription($user_id);
                } catch (\Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo $exception->getCode() . '-' . $exception->getMessage();

                        die();
                    }
                }
            }

            /* Make sure the code exists */
            $codes_code = Database::get('*', 'codes', ['code' => $code, 'type' => 'discount']);

            if($codes_code) {
                $code = $codes_code->code;

                /* Check if we should insert the usage of the code or not */
                if(!Database::exists('id', 'redeemed_codes', ['user_id' => $user_id, 'code_id' => $codes_code->code_id])) {
                    /* Update the code usage */
                    $this->database->query("UPDATE `codes` SET `redeemed` = `redeemed` + 1 WHERE `code_id` = {$codes_code->code_id}");

                    /* Add log for the redeemed code */
                    Database::insert('redeemed_codes', [
                        'code_id'   => $codes_code->code_id,
                        'user_id'   => $user_id,
                        'date'      => \Altum\Date::$date
                    ]);

                    Logger::users($user_id, 'codes.redeemed_code=' . $codes_code->code);
                }
            }

            /* Add a log into the database */
            Database::insert(
                'payments',
                [
                    'user_id' => $user_id,
                    'plan_id' => $plan_id,
                    'processor' => 'stripe',
                    'type' => $payment_type,
                    'frequency' => $payment_frequency,
                    'code' => $code,
                    'discount_amount' => $discount_amount,
                    'base_amount' => $base_amount,
                    'email' => $payer_email,
                    'payment_id' => $payment_id,
                    'subscription_id' => $session->subscription,
                    'payer_id' => $payer_id,
                    'name' => $payer_name,
                    'billing' => $this->settings->payment->taxes_and_billing_is_enabled && $user->billing ? $user->billing : null,
                    'taxes_ids' => $taxes_ids,
                    'total_amount' => $payment_total,
                    'currency' => $payment_currency,
                    'date' => \Altum\Date::$date
                ],
                false
            );

            /* Update the user with the new plan */
            switch($payment_frequency) {
                case 'monthly':
                    $plan_expiration_date = (new \DateTime())->modify('+30 days')->format('Y-m-d H:i:s');
                    break;

                case 'annual':
                    $plan_expiration_date = (new \DateTime())->modify('+12 months')->format('Y-m-d H:i:s');
                    break;

                case 'lifetime':
                    $plan_expiration_date = (new \DateTime())->modify('+100 years')->format('Y-m-d H:i:s');
                    break;
            }

            Database::update(
                'users',
                [
                    'plan_id' => $plan_id,
                    'plan_expiration_date' => $plan_expiration_date,
                    'plan_settings' => $plan->settings,
                    'payment_subscription_id' => $payment_subscription_id
                ],
                [
                    'user_id' => $user_id
                ]
            );

            /* Send notification to the user */
            /* Prepare the email */
            $email_template = get_email_template(
                [],
                $this->language->global->emails->user_payment->subject,
                [
                    '{{PACKAGE_EXPIRATION_DATE}}' => \Altum\Date::get($plan_expiration_date, 2),
                    '{{USER_PACKAGE_LINK}}' => url('account-plan'),
                    '{{USER_PAYMENTS_LINK}}' => url('account-payments'),
                ],
                $this->language->global->emails->user_payment->body
            );

            send_mail(
                $this->settings,
                $user->email,
                $email_template->subject,
                $email_template->body
            );

            /* Send notification to admin if needed */
            if($this->settings->email_notifications->new_payment && !empty($this->settings->email_notifications->emails)) {

                send_mail(
                    $this->settings,
                    explode(',', $this->settings->email_notifications->emails),
                    sprintf($this->language->global->emails->admin_new_payment_notification->subject, 'stripe', $payment_total, $payment_currency),
                    sprintf($this->language->global->emails->admin_new_payment_notification->body, $payment_total, $payment_currency)
                );

            }

            echo 'successful';

        } catch(\UnexpectedValueException $e) {

            // Invalid payload
            http_response_code(400);
            exit();

        } catch(\Stripe\Error\SignatureVerification $e) {

            // Invalid signature
            http_response_code(400);
            exit();

        }

        die();

    }

}
