<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Date;
use Altum\Logger;
use Altum\Middlewares\Csrf;
use Altum\Middlewares\Authentication;
use Altum\Response;

class AdminPayments extends Controller {

    public function index() {

        Authentication::guard('admin');

        /* Delete Modal */
        $view = new \Altum\Views\View('admin/payments/payment_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Approve Modal */
        $view = new \Altum\Views\View('admin/payments/payment_approve_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Main View */
        $view = new \Altum\Views\View('admin/payments/index', (array) $this);

        $this->add_view_content('content', $view->run());

    }


    public function read() {

        Authentication::guard('admin');

        $datatable = new \Altum\DataTable();
        $datatable->set_accepted_columns(['type', 'email', 'name', 'total_amount', 'date', 'user_email', 'actions']);
        $datatable->process($_POST);

        $result = Database::$database->query("
            SELECT 
                `payments` . *, `users` . `user_id`, `users` . `email` AS `user_email`,
                (SELECT COUNT(*) FROM `payments`) AS `total_before_filter`,
                (SELECT COUNT(*) FROM `payments` LEFT JOIN `users` ON `payments` . `user_id` = `users` . `user_id` WHERE `users` . `email` LIKE '%{$datatable->get_search()}%' OR `users` . `name` LIKE '%{$datatable->get_search()}%' OR `payments` . `name` LIKE '%{$datatable->get_search()}%' OR `payments` . `email` LIKE '%{$datatable->get_search()}%') AS `total_after_filter`
            FROM 
                `payments`
            LEFT JOIN
                `users` ON `payments` . `user_id` = `users` . `user_id`
            WHERE 
                `users` . `email` LIKE '%{$datatable->get_search()}%' 
                OR `users` . `name` LIKE '%{$datatable->get_search()}%'
                OR `payments` . `name` LIKE '%{$datatable->get_search()}%'
                OR `payments` . `email` LIKE '%{$datatable->get_search()}%'
            ORDER BY 
                " . $datatable->get_order() . "
            LIMIT
                {$datatable->get_start()}, {$datatable->get_length()}	
        ");

        $total_before_filter = 0;
        $total_after_filter = 0;

        $data = [];

        while($row = $result->fetch_object()):

            $row->user_email = '<a href="' . url('admin/user-view/' . $row->user_id) . '"> ' . $row->user_email . '</a>';

            $row->type = '
            <div class="d-flex flex-column">
                <span>' . $this->language->pay->custom_plan->{$row->type . '_type'} . '</span>
                <span class="text-muted">' . $this->language->pay->custom_plan->{$row->processor} . '</span>
            </div>
            ';

            $row->date = '<span data-toggle="tooltip" title="' . \Altum\Date::get($row->date, 1) . '">' . \Altum\Date::get($row->date, 2) . '</span>';
            $row->total_amount = '<span class="text-success">' .  $row->total_amount . '</span> ' . $row->currency;

            $row->actions = include_view(THEME_PATH . 'views/admin/partials/admin_payment_dropdown_button.php', [
                'id' => $row->id,
                'payment_proof' => $row->payment_proof,
                'processor' => $row->processor,
                'status' => $row->status
            ]);

            $data[] = $row;

            $total_before_filter = $row->total_before_filter;
            $total_after_filter = $row->total_after_filter;

        endwhile;


        Response::simple_json([
            'data' => $data,
            'draw' => $datatable->get_draw(),
            'recordsTotal' => $total_before_filter,
            'recordsFiltered' =>  $total_after_filter
        ]);

    }

    public function delete() {

        Authentication::guard();

        $payment_id = (isset($this->params[0])) ? $this->params[0] : false;

        if(!Csrf::check('global_token')) {
            $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            redirect('admin/users');
        }

        if(empty($_SESSION['error'])) {
            $payment = Database::get(['payment_proof'], 'payments', ['id' => $payment_id]);

            /* Delete the saved proof, if any */
            if($payment->payment_proof) {
                unlink(UPLOADS_PATH . 'offline_payment_proofs/' . $payment->payment_proof);
            }

            /* Delete the payment */
            Database::$database->query("DELETE FROM `payments` WHERE `id` = {$payment_id}");

            /* Success message */
            $_SESSION['success'][] = $this->language->admin_payment_delete_modal->success_message;

            redirect('admin/payments');

        }

        die();
    }

    public function approve() {

        Authentication::guard();

        $payment_id = (isset($this->params[0])) ? $this->params[0] : false;

        if(!Csrf::check('global_token')) {
            $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            redirect('admin/users');
        }

        if(empty($_SESSION['error'])) {
            $payment = Database::get(['plan_id', 'user_id', 'frequency', 'email', 'code', 'payment_proof', 'payer_id'], 'payments', ['id' => $payment_id]);
            $plan = (new \Altum\Models\Plan(['settings' => $this->settings]))->get_plan_by_id($payment->plan_id);

            /* Make sure the code that was potentially used exists */
            $codes_code = Database::get('*', 'codes', ['code' => $payment->code, 'type' => 'discount']);

            if($codes_code) {
                /* Check if we should insert the usage of the code or not */
                if(!Database::exists('id', 'redeemed_codes', ['user_id' => $payment->user_id, 'code_id' => $codes_code->code_id])) {
                    /* Update the code usage */
                    $this->database->query("UPDATE `codes` SET `redeemed` = `redeemed` + 1 WHERE `code_id` = {$codes_code->code_id}");

                    /* Add log for the redeemed code */
                    Database::insert('redeemed_codes', [
                        'code_id'   => $codes_code->code_id,
                        'user_id'   => $payment->user_id,
                        'date'      => \Altum\Date::$date
                    ]);

                    Logger::users($payment->user_id, 'codes.redeemed_code=' . $codes_code->code);
                }
            }

            /* Give the plan to the user */
            switch($payment->frequency) {
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
                    'plan_id' => $payment->plan_id,
                    'plan_settings' => json_encode($plan->settings),
                    'plan_expiration_date' => $plan_expiration_date
                ],
                [
                    'user_id' => $payment->payer_id
                ]
            );

            /* Send notification to the user */
            /* Prepare the email */
            $email_template = get_email_template(
                [],
                $this->language->global->emails->user_payment->subject,
                [
                    '{{PLAN_EXPIRATION_DATE}}' => Date::get($plan_expiration_date, 2),
                    '{{USER_PLAN_LINK}}' => url('account-plan'),
                    '{{USER_PAYMENTS_LINK}}' => url('account-payments'),
                ],
                $this->language->global->emails->user_payment->body
            );

            send_mail(
                $this->settings,
                $payment->email,
                $email_template->subject,
                $email_template->body
            );

            /* Update the payment */
            Database::$database->query("UPDATE `payments` SET `status` = 1 WHERE `id` = {$payment_id}");

            /* Success message */
            $_SESSION['success'][] = $this->language->admin_payment_approve_modal->success_message;

            redirect('admin/payments');

        }

        die();
    }
}
