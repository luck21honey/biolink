<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Date;
use Altum\Logger;
use Altum\Middlewares\Authentication;
use Altum\Middlewares\Csrf;
use Altum\Models\User;
use Altum\Response;
use Exception;
use PayPal\Api\Agreement;
use PayPal\Api\Amount;
use PayPal\Api\Currency;
use PayPal\Api\FlowConfig;
use PayPal\Api\InputFields;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Plan;
use PayPal\Api\Presentation;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\WebProfile;
use PayPal\Common\PayPalModel;

class Pay extends Controller {
    public $plan_id;
    public $return_type;
    public $payment_processor;
    public $plan;
    public $plan_taxes;
    public $applied_taxes_ids = [];
    public $code;

    public function index() {

        Authentication::guard();

        if(!$this->settings->payment->is_enabled) {
            redirect();
        }

        $this->plan_id = isset($this->params[0]) ? $this->params[0] : false;
        $this->return_type = isset($_GET['return_type']) && in_array($_GET['return_type'], ['success', 'cancel']) ? $_GET['return_type'] : null;
        $this->payment_processor = isset($_GET['payment_processor']) && in_array($_GET['payment_processor'], ['paypal', 'stripe', 'offline_payment']) ? $_GET['payment_processor'] : null;

        /* Make sure it is either the trial / free plan or normal plans */
        switch($this->plan_id) {

            case 'free':

                /* Get the current settings for the free plan */
                $this->plan = $this->settings->plan_free;

                break;

            case 'trial':

                /* Get the current settings for the trial plan */
                $this->plan = $this->settings->plan_trial;

                break;

            default:

                $this->plan_id = (int) $this->plan_id;

                /* Check if plan exists */
                $this->plan = (new \Altum\Models\Plan(['settings' => $this->settings]))->get_plan_by_id($this->plan_id);

                if($this->plan->plan_id == 'custom') {
                    redirect('plan');
                }

                /* Check for potential taxes */
                $this->plan_taxes = (new \Altum\Models\Plan())->get_plan_taxes_by_taxes_ids($this->plan->taxes_ids);

                /* Filter them out */
                if($this->plan_taxes) {
                    foreach ($this->plan_taxes as $key => $value) {

                        /* Type */
                        if ($value->billing_type != $this->user->billing->type && $value->billing_type != 'both') {
                            unset($this->plan_taxes[$key]);
                        }

                        /* Countries */
                        if ($value->countries && !in_array($this->user->billing->country, $value->countries)) {
                            unset($this->plan_taxes[$key]);
                        }

                        if (isset($this->plan_taxes[$key])) {
                            $this->applied_taxes_ids[] = $value->tax_id;
                        }

                    }

                    $this->plan_taxes = array_values($this->plan_taxes);
                }

                break;
        }

        /* Make sure the plan is enabled */
        if(!$this->plan->status) {
            redirect('plan');
        }

        /* More checks depending on the user plan and what it has been chosen */
        if($this->plan_id == 'free') {
            if($this->user->plan_id == 'free') {
                $_SESSION['info'][] = $this->language->pay->free->free_already;
            } else {
                $_SESSION['info'][] = $this->language->pay->free->other_plan_not_expired;
            }

            redirect('plan');
        }

        elseif($this->plan_id == 'trial') {
            if($this->user->plan_trial_done) {
                $_SESSION['info'][] = $this->language->pay->trial->trial_done;
                redirect('plan');
            }
        }

        /* Form submission processing */
        /* Make sure that this only runs on user click submit post and not on callbacks / webhooks */
        if(!empty($_POST) && !$this->return_type) {

            /* Check for any errors */
            if(!Csrf::check()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            }

            switch($this->plan_id) {

                case 'free':

                    redirect('pay/' . $this->plan_id);

                    break;

                case 'trial':

                    if($this->user->plan_trial_done) {
                        redirect('pay/' . $this->plan_id);
                    }

                    break;

                default:

                    $_POST['payment_frequency'] = Database::clean_string($_POST['payment_frequency']);
                    $_POST['payment_processor'] = Database::clean_string($_POST['payment_processor']);
                    $_POST['payment_type'] = Database::clean_string($_POST['payment_type']);

                    /* Make sure the chosen option comply */
                    if(!in_array($_POST['payment_frequency'], ['monthly', 'annual', 'lifetime'])) {
                        redirect('pay/' . $this->plan_id);
                    }

                    if(!in_array($_POST['payment_processor'], ['paypal', 'stripe', 'offline_payment'])) {
                        redirect('pay/' . $this->plan_id);
                    } else {

                        /* Make sure the payment processor is active */
                        switch($_POST['payment_processor']) {
                            case 'paypal':

                                if(!$this->settings->paypal->is_enabled) {
                                    redirect('pay/' . $this->plan_id);
                                }

                                break;

                            case 'stripe':

                                if(!$this->settings->stripe->is_enabled) {
                                    redirect('pay/' . $this->plan_id);
                                }

                                break;

                            case 'offline_payment':

                                if(!$this->settings->offline_payment->is_enabled) {
                                    redirect('pay/' . $this->plan_id);
                                }

                                break;
                        }

                    }

                    if(!in_array($_POST['payment_type'], ['one_time', 'recurring'])) {
                        redirect('pay/' . $this->plan_id);
                    }

                    /* Lifetime */
                    if($_POST['payment_frequency'] == 'lifetime') {
                        $_POST['payment_type'] = 'one_time';
                    }

                    /* Offline payment */
                    if($_POST['payment_processor'] == 'offline_payment') {
                        $_POST['payment_type'] = 'one_time';
                    }

                    break;
            }

            if(empty($_SESSION['error'])) {

                switch($this->plan_id) {

                    case 'trial':

                        /* Determine the expiration date of the plan */
                        $plan_expiration_date = (new \DateTime())->modify('+' . $this->plan->days . ' days')->format('Y-m-d H:i:s');
                        $plan_settings = json_encode($this->settings->plan_trial->settings);

                        Database::$database->query("UPDATE `users` SET `plan_id` = 'trial', `plan_settings` = '{$plan_settings}', `plan_expiration_date` = '{$plan_expiration_date}', `plan_trial_done` = '1' WHERE `user_id` = {$this->user->user_id}");

                        /* Success message and redirect */
                        $this->redirect_pay_thank_you();

                        break;

                    default:

                        /* Check for code usage */
                        $this->code = false;

                        if($this->settings->payment->codes_is_enabled && isset($_POST['code'])) {

                            $_POST['code'] = Database::clean_string($_POST['code']);

                            $this->code = $this->database->query("SELECT `code_id`, `code`, `discount` FROM `codes` WHERE (`plan_id` IS NULL OR `plan_id` = '{$this->plan_id}') AND `code` = '{$_POST['code']}' AND `redeemed` < `quantity` AND `type` = 'discount'")->fetch_object();

                            if($this->code && Database::exists('id', 'redeemed_codes', ['user_id' => $this->user->user_id, 'code_id' => $this->code->code_id])) {
                                redirect('pay/' . $this->plan_id);
                            }
                        }

                        switch($_POST['payment_processor']) {

                            case 'paypal':

                                $this->paypal_create();

                                break;

                            case 'stripe':

                                $stripe_session = $this->stripe_create();

                                break;

                            case 'offline_payment':

                                $stripe_session = $this->offline_payment_process();

                                break;
                        }

                        break;

                }

            }

        }

        /* Include the detection of paypal callbacks processing */
        $this->paypal_process();

        /* Include the detection of stripe callbacks processing */
        $this->stripe_process();

        /* Prepare the View */
        $data = [
            'plan_id'           => $this->plan_id,
            'plan'              => $this->plan,
            'plan_taxes'        => $this->plan_taxes,
            'stripe_session'    => $stripe_session ?? false
        ];

        $view = new \Altum\Views\View('pay/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    private function paypal_create() {

        /* Initiate paypal */
        $paypal = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential($this->settings->paypal->client_id, $this->settings->paypal->secret));
        $paypal->setConfig(['mode' => $this->settings->paypal->mode]);

        /* Payment details */
        $product = $this->plan->name;
        $price = $base_amount = (float) $this->plan->{$_POST['payment_frequency'] . '_price'};
        $shipping = 0;
        $code = '';
        $discount_amount = 0;

        /* Check for code usage */
        if($this->code) {

            /* Discount amount */
            $discount_amount = number_format(($price * $this->code->discount / 100), 2, '.', '');

            /* Calculate the new price */
            $price = $price - $discount_amount;

            $code = $this->code->code;

        }

        /* Taxes */
        $price = $this->calculate_price_with_taxes($price);

        /* Make sure the price is right depending on the currency */
        $price = in_array($this->settings->payment->currency, ['JPY', 'TWD', 'HUF']) ? number_format($price, 0, '.', '') : number_format($price, 2, '.', '');

        switch($_POST['payment_type']) {
            case 'one_time':

                /* Payment experience */
                $flowConfig = new FlowConfig();
                $flowConfig->setLandingPageType('Billing');
                $flowConfig->setUserAction('commit');
                $flowConfig->setReturnUriHttpMethod('GET');

                $presentation = new Presentation();
                $presentation->setBrandName($this->settings->payment->brand_name);

                $inputFields = new InputFields();
                $inputFields->setAllowNote(true)
                    ->setNoShipping(1)
                    ->setAddressOverride(0);

                $webProfile = new WebProfile();
                $webProfile->setName($this->settings->payment->brand_name . uniqid())
                    ->setFlowConfig($flowConfig)
                    ->setPresentation($presentation)
                    ->setInputFields($inputFields)
                    ->setTemporary(true);

                /* Create the experience profile */
                try {
                    $createdProfileResponse = $webProfile->create($paypal);
                } catch (Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo $exception->getCode();
                        echo $exception->getData();

                        die();
                    } else {

                        $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                        redirect('pay/' . $this->plan_id);

                    }

                }

                $payer = new Payer();
                $payer->setPaymentMethod('paypal');

                $item = new Item();
                $item->setName($product)
                    ->setCurrency($this->settings->payment->currency)
                    ->setQuantity(1)
                    ->setPrice($price);

                $itemList = new ItemList();
                $itemList->setItems([$item]);

                $amount = new Amount();
                $amount->setCurrency($this->settings->payment->currency)
                    ->setTotal($price);

                $transaction = new Transaction();
                $transaction->setAmount($amount)
                    ->setItemList($itemList)
                    ->setInvoiceNumber(uniqid());

                $redirectUrls = new RedirectUrls();
                $redirectUrls->setReturnUrl(url('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $price, $code, $discount_amount)))
                    ->setCancelUrl(url('pay/' . $this->plan_id . $this->return_url_parameters('cancel', $base_amount, $price, $code, $discount_amount)));

                $payment = new Payment();
                $payment->setIntent('sale')
                    ->setPayer($payer)
                    ->setRedirectUrls($redirectUrls)
                    ->setTransactions([$transaction])
                    ->setExperienceProfileId($createdProfileResponse->getId());

                try {
                    $payment->create($paypal);
                } catch (Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo $exception->getCode();
                        echo $exception->getData();

                        die();
                    } else {

                        $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                        redirect('pay/' . $this->plan_id);

                    }
                }

                $payment_url = $payment->getApprovalLink();

                header('Location: ' . $payment_url);

                break;

            case 'recurring':

                $plan = new \PayPal\Api\Plan();
                $plan->setName($product)
                    ->setDescription($product)
                    ->setType('fixed');

                /* Set billing plan definitions */
                $payment_definition = new PaymentDefinition();
                $payment_definition->setName('Regular Payments')
                    ->setType('REGULAR')
                    ->setFrequency($_POST['payment_frequency'] == 'monthly' ? 'Month' : 'Year')
                    ->setFrequencyInterval('1')
                    ->setCycles($_POST['payment_frequency'] == 'monthly' ? '60' : '5')
                    ->setAmount(new Currency(['value' => $price, 'currency' => $this->settings->payment->currency]));


                /* Set merchant preferences */
                $merchant_preferences = new MerchantPreferences();
                $merchant_preferences->setReturnUrl(url('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $price, $code, $discount_amount)))
                    ->setCancelUrl(url('pay/' . $this->plan_id . $this->return_url_parameters('cancel', $base_amount, $price, $code, $discount_amount)))
                    ->setAutoBillAmount('yes')
                    ->setInitialFailAmountAction('CONTINUE')
                    ->setMaxFailAttempts('0')
                    ->setSetupFee(new Currency(['value' => $price, 'currency' => $this->settings->payment->currency]));

                $plan->setPaymentDefinitions([$payment_definition]);
                $plan->setMerchantPreferences($merchant_preferences);

                /* Create the plan */
                try {
                    $plan = $plan->create($paypal);
                } catch (Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo '1';
                        echo $exception->getCode();
                        echo $exception->getData();

                        die();
                    } else {

                        $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                        redirect('pay/' . $this->plan_id);

                    }
                }

                /* Make sure to activate the plan */
                try {
                    $patch = new Patch();
                    $value = new PayPalModel('{"state":"ACTIVE"}');
                    $patch->setOp('replace')
                        ->setPath('/')
                        ->setValue($value);
                    $patchRequest = new PatchRequest();
                    $patchRequest->addPatch($patch);
                    $plan->update($patchRequest, $paypal);
                    $plan = Plan::get($plan->getId(), $paypal);
                } catch (Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo '2';
                        echo $exception->getCode();
                        echo $exception->getData();

                        die();
                    } else {

                        $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                        redirect('pay/' . $this->plan_id);

                    }
                }

                /* Start creating the agreement */
                $agreement = new Agreement();
                $agreement->setName($product)
                    ->setDescription(
                        $this->user->user_id
                        . '!!' . $this->plan_id
                        . '!!' . $base_amount
                        . '!!' . $_POST['payment_frequency']
                        . '!!' . $code
                        . '!!' . $discount_amount
                        . '!!' . json_encode($this->applied_taxes_ids)
                    )
                    ->setStartDate((new \DateTime())->modify($_POST['payment_frequency'] == 'monthly' ? '+30 days' : '+1 year')->format(DATE_ISO8601));

                /* Set the plan id to the agreement */
                $agreement_plan = new Plan();
                $agreement_plan->setId($plan->getId());
                $agreement->setPlan($agreement_plan);

                /* Add Payer */
                $payer = new Payer();
                $payer->setPaymentMethod('paypal');
                $agreement->setPayer($payer);

                /* Create the agreement */
                try {
                    $agreement = $agreement->create($paypal);
                } catch (Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo '3';
                        echo $exception->getCode();
                        echo $exception->getData();

                        die();
                    } else {

                        $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                        redirect('pay/' . $this->plan_id);

                    }
                }

                $payment_url = $agreement->getApprovalLink();

                header('Location: ' . $payment_url);

                break;
        }


    }

    private function paypal_process() {

        /* Initiate paypal */
        $paypal = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential($this->settings->paypal->client_id, $this->settings->paypal->secret));
        $paypal->setConfig(['mode' => $this->settings->paypal->mode]);

        /* Return confirmation processing */
        if($this->return_type && $this->payment_processor && $this->return_type == 'success' && $this->payment_processor = 'paypal' && isset($_GET['payment_frequency'], $_GET['code'])) {
            $payment_frequency = $_GET['payment_frequency'];
            $code = $_GET['code'];
            $discount_amount = $_GET['discount_amount'];
            $base_amount = $_GET['base_amount'];

            /* Return confirmation processing one time payment */
            if(isset($_GET['paymentId'], $_GET['PayerID'])) {

                /* Initiate paypal */
                $paypal = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential($this->settings->paypal->client_id, $this->settings->paypal->secret));
                $paypal->setConfig(['mode' => $this->settings->paypal->mode]);

                $payment_id = $_GET['paymentId'];
                $payer_id = $_GET['PayerID'];
                $payment_type = 'one_time';
                $subscription_id = '';
                $payment_subscription_id =  '';

                try {
                    $payment = Payment::get($payment_id, $paypal);

                    $payer_info = $payment->getPayer()->getPayerInfo();
                    $payer_email = $payer_info->getEmail();
                    $payer_name = $payer_info->getFirstName() . ' ' . $payer_info->getLastName();

                    $payment_total = $payment->getTransactions()[0]->getAmount()->getTotal();
                    $payment_currency = $payment->getTransactions()[0]->getAmount()->getCurrency();

                    /* Execute the payment */
                    $execute = new PaymentExecution();
                    $execute->setPayerId($payer_id);

                    $result = $payment->execute($execute, $paypal);

                    /* Get status after execution */
                    $payment_status = $payment->getState();

                } catch (Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo $exception->getCode();
                        echo $exception->getData();

                        die();
                    } else {

                        $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                        redirect('pay/' . $this->plan_id);

                    }
                }

                /* Make sure the transaction is not already existing */
                if(Database::exists('id', 'payments', ['payment_id' => $payment_id, 'processor' => 'paypal'])) {
                    redirect('pay/' . $this->plan_id);
                }

                /* Make sure the payment is approved */
                if($payment_status != 'approved') {
                    $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                    redirect('pay/' . $this->plan_id);
                }

                /* Make sure the chosen option comply */
                if(!in_array($payment_frequency, ['monthly', 'annual', 'lifetime'])) {
                    redirect('pay/' . $this->plan_id);
                }

                /* Unsubscribe from the previous plan if needed */
                if(!empty($this->user->payment_subscription_id) && $this->user->payment_subscription_id != $payment_subscription_id) {
                    try {
                        (new User(['settings' => $this->settings, 'user' => $this->user]))->cancel_subscription();
                    } catch (\Exception $exception) {

                        /* Output errors properly */
                        if (DEBUG) {
                            echo $exception->getCode() . '-' . $exception->getMessage();

                            die();
                        } else {
                            $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                            redirect('pay/' . $this->plan_id);
                        }

                    }
                }

                /* Make sure the code exists */
                $codes_code = Database::get('*', 'codes', ['code' => $code, 'type' => 'discount']);

                if($codes_code) {
                    $code = $codes_code->code;

                    /* Check if we should insert the usage of the code or not */
                    if(!Database::exists('id', 'redeemed_codes', ['user_id' => $this->user->user_id, 'code_id' => $codes_code->code_id])) {
                        /* Update the code usage */
                        $this->database->query("UPDATE `codes` SET `redeemed` = `redeemed` + 1 WHERE `code_id` = {$codes_code->code_id}");

                        /* Add log for the redeemed code */
                        Database::insert('redeemed_codes', [
                            'code_id'   => $codes_code->code_id,
                            'user_id'   => $this->user->user_id,
                            'date'      => \Altum\Date::$date
                        ]);

                        Logger::users($this->user->user_id, 'codes.redeemed_code=' . $codes_code->code);
                    }
                }

                /* Add a log into the database */
                Database::insert(
                    'payments',
                    [
                        'user_id' => $this->user->user_id,
                        'plan_id' => $this->plan_id,
                        'processor' => 'paypal',
                        'type' => $payment_type,
                        'frequency' => $payment_frequency,
                        'code' => $code,
                        'discount_amount' => $discount_amount,
                        'base_amount' => $base_amount,
                        'email' => $payer_email,
                        'payment_id' => $payment_id,
                        'subscription_id' => $subscription_id,
                        'payer_id' => $payer_id,
                        'name' => $payer_name,
                        'billing' => $this->settings->payment->taxes_and_billing_is_enabled && $this->user->billing ? json_encode($this->user->billing) : null,
                        'taxes_ids' => !empty($this->applied_taxes_ids) ? json_encode($this->applied_taxes_ids) : null,
                        'total_amount' => $payment_total,
                        'currency' => $payment_currency,
                        'date' => Date::$date
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
                        'plan_id' => $this->plan_id,
                        'plan_settings' => json_encode($this->plan->settings),
                        'plan_expiration_date' => $plan_expiration_date
                    ],
                    [
                        'user_id' => $this->user->user_id
                    ]
                );

                /* Send notification to the user */
                /* Prepare the email */
                $email_template = get_email_template(
                    [],
                    $this->language->global->emails->user_payment->subject,
                    [
                        '{{PACKAGE_EXPIRATION_DATE}}' => Date::get($plan_expiration_date, 2),
                        '{{USER_PACKAGE_LINK}}' => url('account-plan'),
                        '{{USER_PAYMENTS_LINK}}' => url('account-payments'),
                    ],
                    $this->language->global->emails->user_payment->body
                );

                send_mail(
                    $this->settings,
                    $this->user->email,
                    $email_template->subject,
                    $email_template->body
                );

                /* Send notification to admin if needed */
                if($this->settings->email_notifications->new_payment && !empty($this->settings->email_notifications->emails)) {

                    send_mail(
                        $this->settings,
                        explode(',', $this->settings->email_notifications->emails),
                        sprintf($this->language->global->emails->admin_new_payment_notification->subject, 'paypal', $payment_total, $payment_currency),
                        sprintf($this->language->global->emails->admin_new_payment_notification->body, $payment_total, $payment_currency)
                    );

                }

                /* Success message and redirect */
                $this->redirect_pay_thank_you();

            }

            /* Return confirmation processing recurring payment */
            if(isset($_GET['token'], $_GET['payment_type'])) {

                /* Initiate paypal */
                $paypal = new \PayPal\Rest\ApiContext(new \PayPal\Auth\OAuthTokenCredential($this->settings->paypal->client_id, $this->settings->paypal->secret));
                $paypal->setConfig(['mode' => $this->settings->paypal->mode]);

                $token = $_GET['token'];
                $agreement = new \PayPal\Api\Agreement();
                $payment_type = 'recurring';

                try {
                    $agreement->execute($token, $paypal);
                } catch (Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo $exception->getCode();
                        echo $exception->getData();

                        die();
                    } else {

                        $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                        redirect('pay/' . $this->plan_id);

                    }
                }

                /* Get details about the executed agreement */
                try {
                    $agreement = \PayPal\Api\Agreement::get($agreement->getId(), $paypal);
                } catch (Exception $exception) {

                    /* Output errors properly */
                    if (DEBUG) {
                        echo $exception->getCode();
                        echo $exception->getData();

                        die();
                    } else {

                        $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                        redirect('pay/' . $this->plan_id);

                    }
                }

                /* Get the needed details from the agreement */
                $agreement_status = $agreement->getState();

                /* Make sure the payment is approved */
                if($agreement_status != 'Active' && $agreement_status != 'Pending') {
                    $_SESSION['error'][] = $this->language->pay->error_message->failed_payment;
                    redirect('pay/' . $this->plan_id);
                }

                /* Success message and redirect */
                $this->redirect_pay_thank_you();

            }

        }


        /* Return confirmation processing if failed */
        if($this->return_type && $this->payment_processor && $this->return_type == 'cancel' && $this->payment_processor = 'paypal') {
            $_SESSION['error'][] = $this->language->pay->error_message->canceled_payment;
            redirect('pay/' . $this->plan_id);
        }

    }

    private function stripe_create() {

        /* Initiate Stripe */
        \Stripe\Stripe::setApiKey($this->settings->stripe->secret_key);

        /* Payment details */
        $product = $this->plan->name;
        $price = $base_amount = $this->plan->{$_POST['payment_frequency'] . '_price'};
        $shipping = 0;
        $code = '';
        $discount_amount = 0;

        /* Check for code usage */
        if($this->code) {

            /* Discount amount */
            $discount_amount = number_format(($price * $this->code->discount / 100), 2, '.', '');

            /* Calculate the new price */
            $price = $price - $discount_amount;

            $code = $this->code->code;

        }

        /* Taxes */
        $price = $this->calculate_price_with_taxes($price);

        /* Final price */
        $stripe_formatted_price = in_array($this->settings->payment->currency, ['MGA', 'BIF', 'CLP', 'PYG', 'DJF', 'RWF', 'GNF', 'UGX', 'JPY', 'VND', 'VUV', 'XAF', 'KMF', 'KRW', 'XOF', 'XPF']) ? number_format($price, 0, '.', '') : number_format($price, 2, '.', '') * 100;

        $price = number_format($price, 2, '.', '');

        switch($_POST['payment_type']) {
            case 'one_time':

                $stripe_session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'name' => $product,
                        'description' => $_POST['payment_frequency'],
                        'amount' => $stripe_formatted_price,
                        'currency' => $this->settings->payment->currency,
                        'quantity' => 1,
                    ]],
                    'metadata' => [
                        'user_id' => $this->user->user_id,
                        'plan_id' => $this->plan_id,
                        'payment_frequency' => $_POST['payment_frequency'],
                        'base_amount' => $base_amount,
                        'code' => $code,
                        'discount_amount' => $discount_amount,
                        'taxes_ids' => json_encode($this->applied_taxes_ids)
                    ],
                    'success_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $price, $code, $discount_amount)),
                    'cancel_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('cancel', $base_amount, $price, $code, $discount_amount)),
                ]);

                break;

            case 'recurring':

                /* Try to get the product related to the plan */
                try {
                    $stripe_product = \Stripe\Product::retrieve($this->plan_id);
                } catch (\Exception $exception) {
                    /* The product probably does not exist */
                }

                if(!isset($stripe_product)) {
                    /* Create the product if not already created */
                    $stripe_product = \Stripe\Product::create([
                        'id'    => $this->plan_id,
                        'name'  => $product,
                        'type'  => 'service',
                    ]);
                }

                /* Generate the plan id with the proper parameters */
                $stripe_plan_id = $this->plan_id . '_' . $_POST['payment_frequency'] . '_' . $stripe_formatted_price . '_' . $this->settings->payment->currency;

                /* Check if we already have a payment plan created and try to get it */
                try {
                    $stripe_plan = \Stripe\Plan::retrieve($stripe_plan_id);
                } catch (\Exception $exception) {
                    /* The plan probably does not exist */
                }

                /* Create the plan if it doesnt exist already */
                if(!isset($stripe_plan)) {
                    try {
                        $stripe_plan = \Stripe\Plan::create([
                            'amount' => $stripe_formatted_price,
                            'interval' => $_POST['payment_frequency'] == 'monthly' ? 'month' : 'year',
                            'product' => $stripe_product->id,
                            'currency' => $this->settings->payment->currency,
                            'id' => $stripe_plan_id
                        ]);
                    } catch (\Exception $exception) {
                        $_SESSION['error'][] = $exception->getMessage();
                        redirect('pay/' . $this->plan_id);
                    }
                }

                $stripe_session = \Stripe\Checkout\Session::create([
                    'payment_method_types' => ['card'],
                    'subscription_data' => [
                        'items' => [
                            ['plan' => $stripe_plan->id]
                        ],
                        'metadata' => [
                            'user_id' => $this->user->user_id,
                            'plan_id' => $this->plan_id,
                            'payment_frequency' => $_POST['payment_frequency'],
                            'code' => $code,
                            'taxes_ids' => json_encode($this->applied_taxes_ids)
                        ],
                    ],
                    'metadata' => [
                        'user_id' => $this->user->user_id,
                        'plan_id' => $this->plan_id,
                        'payment_frequency' => $_POST['payment_frequency'],
                        'base_amount' => $base_amount,
                        'code' => $code,
                        'discount_amount' => $discount_amount,
                        'taxes_ids' => json_encode($this->applied_taxes_ids)
                    ],
                    'success_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $price, $code, $discount_amount)),
                    'cancel_url' => url('pay/' . $this->plan_id . $this->return_url_parameters('cancel', $base_amount, $price, $code, $discount_amount)),
                ]);

                break;
        }

        return $stripe_session;

    }

    private function stripe_process() {

        /* Return confirmation processing if successfuly */
        if($this->return_type && $this->payment_processor && $this->return_type == 'success' && $this->payment_processor = 'stripe') {

            /* Redirect to the thank you page */
            $this->redirect_pay_thank_you();
        }

        /* Return confirmation processing if failed */
        if($this->return_type && $this->payment_processor && $this->return_type == 'cancel' && $this->payment_processor = 'stripe') {
            $_SESSION['error'][] = $this->language->pay->error_message->canceled_payment;
            redirect('pay/' . $this->plan_id);
        }

    }


    private function offline_payment_process() {

        /* Return confirmation processing if successfuly */
        if($this->return_type && $this->payment_processor && $this->return_type == 'success' && $this->payment_processor = 'offline_payment') {

            /* Redirect to the thank you page */
            $this->redirect_pay_thank_you();
        }

        /* Payment details */
        $price = $base_amount = $this->plan->{$_POST['payment_frequency'] . '_price'};
        $code = '';
        $discount_amount = 0;

        /* Check for code usage */
        if($this->code) {

            /* Discount amount */
            $discount_amount = number_format(($price * $this->code->discount / 100), 2, '.', '');

            /* Calculate the new price */
            $price = $price - $discount_amount;

            $code = $this->code->code;

        }

        /* Taxes */
        $price = number_format($this->calculate_price_with_taxes($price), 2, '.', '');

        /* Other vars */
        $payment_id = md5($this->user->user_id . $this->plan_id . $_POST['payment_type'] . $_POST['payment_frequency'] . $this->user->email . Date::$date);
        $file_allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
        $offline_payment_proof = (!empty($_FILES['offline_payment_proof']['name']));

        /* Error checks */
        if(!$offline_payment_proof) {
            $_SESSION['error'][] = $this->language->pay->error_message->offline_payment_proof_missing;
            redirect('pay/' . $this->plan_id);
        }

        $offline_payment_proof_file_name = $_FILES['offline_payment_proof']['name'];
        $offline_payment_proof_file_extension = explode('.', $offline_payment_proof_file_name);
        $offline_payment_proof_file_extension = strtolower(end($offline_payment_proof_file_extension));
        $offline_payment_proof_file_temp = $_FILES['offline_payment_proof']['tmp_name'];

        if(!in_array($offline_payment_proof_file_extension, $file_allowed_extensions)) {
            $_SESSION['error'][] = $this->language->global->error_message->invalid_file_type;
            redirect('pay/' . $this->plan_id);
        }

        if(!is_writable(UPLOADS_PATH . 'offline_payment_proofs/')) {
            $_SESSION['error'][] = sprintf($this->language->global->error_message->directory_not_writable, UPLOADS_PATH . 'offline_payment_proofs/');
            redirect('pay/' . $this->plan_id);
        }

        /* Generate new name for offline_payment_proof */
        $offline_payment_proof_new_name = $payment_id . '.' . $offline_payment_proof_file_extension;

        /* Upload the original */
        move_uploaded_file($offline_payment_proof_file_temp, UPLOADS_PATH . 'offline_payment_proofs/' . $offline_payment_proof_new_name);

        /* Add a log into the database */
        Database::insert(
            'payments',
            [
                'user_id' => $this->user->user_id,
                'plan_id' => $this->plan_id,
                'processor' => 'offline_payment',
                'type' => $_POST['payment_type'],
                'frequency' => $_POST['payment_frequency'],
                'code' => $code,
                'discount_amount' => $discount_amount,
                'base_amount' => $base_amount,
                'email' => $this->user->email,
                'payment_id' => $payment_id,
                'subscription_id' => '',
                'payer_id' => $this->user->user_id,
                'name' => $this->user->name,
                'billing' => $this->settings->payment->taxes_and_billing_is_enabled && $this->user->billing ? json_encode($this->user->billing) : null,
                'taxes_ids' => !empty($this->applied_taxes_ids) ? json_encode($this->applied_taxes_ids) : null,
                'total_amount' => $price,
                'currency' => $this->settings->payment->currency,
                'payment_proof' => $offline_payment_proof_new_name,
                'status' => 0,
                'date' => Date::$date
            ],
            false
        );

        /* Send notification to admin if needed */
        if($this->settings->email_notifications->new_payment && !empty($this->settings->email_notifications->emails)) {

            send_mail(
                $this->settings,
                explode(',', $this->settings->email_notifications->emails),
                sprintf($this->language->global->emails->admin_new_payment_notification->subject, 'offline_payment', $price, $this->settings->payment->currency),
                sprintf($this->language->global->emails->admin_new_payment_notification->body, $price, $this->settings->payment->currency)
            );

        }

        redirect('pay/' . $this->plan_id . $this->return_url_parameters('success', $base_amount, $price, $code, $discount_amount));

    }

    /* Ajax to check if discount codes are available */
    public function code() {
        Authentication::guard();

        if(!Csrf::check('global_token')) {
            die();
        }

        if(!$this->settings->payment->is_enabled || !$this->settings->payment->codes_is_enabled) {
            die();
        }

        if(empty($_POST)) {
            die();
        }

        $_POST['plan_id'] = !$_POST['plan_id'] ? null : (int) $_POST['plan_id'];
        $_POST['code'] = Database::clean_string($_POST['code']);

        /* Make sure the discount code exists */
        $code = $this->database->query("SELECT * FROM `codes` WHERE (`plan_id` IS NULL OR `plan_id` = '{$_POST['plan_id']}') AND `code` = '{$_POST['code']}' AND `redeemed` < `quantity` AND `type` = 'discount'")->fetch_object();

        if(!$code) {
            Response::json($this->language->pay->error_message->code_invalid, 'error');
        }

        if(Database::exists('id', 'redeemed_codes', ['user_id' => $this->user->user_id, 'code_id' => $code->code_id])) {
            Response::json($this->language->pay->error_message->code_used, 'error');
        }


        Response::json(sprintf($this->language->pay->success_message->code, '<strong>' . $code->discount . '%</strong>'), 'success', ['discount' => $code->discount]);
    }


    /* Generate the generic return url parameters */
    private function return_url_parameters($return_type, $base_amount, $total_amount, $code, $discount_amount) {
        return
            '&return_type=' . $return_type
            . '&payment_processor=' . $_POST['payment_processor']
            . '&payment_frequency=' . $_POST['payment_frequency']
            . '&payment_type=' . $_POST['payment_type']
            . '&code=' . $code
            . '&discount_amount=' . $discount_amount
            . '&base_amount=' . $base_amount
            . '&total_amount=' . $total_amount;
    }

    /* Simple url generator to return the thank you page */
    private function redirect_pay_thank_you() {
        $thank_you_url_parameters_raw = array_filter($_GET, function($key) {
            return $key != 'altum';
        }, ARRAY_FILTER_USE_KEY);

        $thank_you_url_parameters = '&plan_id=' . $this->plan_id;
        $thank_you_url_parameters .= '&user_id=' . $this->user->user_id;

        foreach($thank_you_url_parameters_raw as $key => $value) {
            $thank_you_url_parameters .= '&' . $key . '=' . $value;
        }

        $thank_you_url_parameters .= '&unique_transaction_identifier=' . md5(\Altum\Date::get('', 4) . $thank_you_url_parameters);

        redirect('pay-thank-you?' . $thank_you_url_parameters);
    }

    private function calculate_price_with_taxes($discounted_price) {

        $price = $discounted_price;

        if($this->plan_taxes) {

            /* Check for the inclusives */
            $inclusive_taxes_array = [];

            foreach($this->plan_taxes as $row) {

                if($row->type == 'exclusive') {
                    continue;
                }

                $inclusive_tax = $price - ($price / (1 + $row->value / 100));

                $inclusive_taxes_array[] = $inclusive_tax;

            }

            $inclusive_taxes = array_sum($inclusive_taxes_array);

            $price_without_inclusive_taxes = $price - $inclusive_taxes;


            /* Check for the exclusives */
            $exclusive_taxes_array = [];

            foreach($this->plan_taxes as $row) {

                if($row->type == 'inclusive') {
                    continue;
                }

                $exclusive_tax = $row->value_type == 'percentage' ? $price_without_inclusive_taxes * ($row->value / 100) : $row->value;

                $exclusive_taxes_array[] = $exclusive_tax;

            }

            $exclusive_taxes = array_sum($exclusive_taxes_array);

            /* Price with all the taxes */
            $price_with_taxes = $price + $exclusive_taxes;

            $price = $price_with_taxes;
        }

        return $price;

    }
}
