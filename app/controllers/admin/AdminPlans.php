<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Csrf;
use Altum\Middlewares\Authentication;
use Altum\Models\User;

class AdminPlans extends Controller {

    public function index() {

        Authentication::guard('admin');

        /* Delete Modal */
        $view = new \Altum\Views\View('admin/plans/plan_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        $plans_result = Database::$database->query("SELECT * FROM plans ORDER BY plan_id ASC");

        /* Main View */
        $data = [
            'plans_result' => $plans_result
        ];

        $view = new \Altum\Views\View('admin/plans/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function delete() {

        Authentication::guard();

        $plan_id = (isset($this->params[0])) ? $this->params[0] : false;

        if(!Csrf::check('global_token')) {
            $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
        }

        if(empty($_SESSION['error'])) {

            /* Get all the users with this plan that have subscriptions and cancel them */
            $result = $this->database->query("SELECT `user_id`, `payment_subscription_id` FROM `users` WHERE `plan_id` = {$plan_id} AND `payment_subscription_id` <> ''");

            while($row = $result->fetch_object()) {
                try {
                    (new User(['settings' => $this->settings, 'user' => $row]))->cancel_subscription();
                } catch (\Exception $exception) {

                    /* Output errors properly */
                    if(DEBUG) {
                        echo $exception->getCode() . '-' . $exception->getMessage();

                        die();
                    }

                }

                /* Change the user plan to custom and leave their current features they paid for on */
                $this->database->query("UPDATE `users` SET `plan_id` = 'custom' WHERE `user_id` = {$row->user_id}");

            }

            /* Delete the plan */
            Database::$database->query("DELETE FROM plans WHERE plan_id = {$plan_id}");

            redirect('admin/plans');

        }

        die();
    }

}
