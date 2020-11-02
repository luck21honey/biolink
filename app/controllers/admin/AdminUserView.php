<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Models\Plan;
use Altum\Middlewares\Authentication;

class AdminUserView extends Controller {

    public function index() {

        Authentication::guard('admin');

        $user_id = (isset($this->params[0])) ? $this->params[0] : false;

        /* Check if user exists */
        if(!$user = Database::get('*', 'users', ['user_id' => $user_id])) {
            $_SESSION['error'][] = $this->language->admin_user_update->error_message->invalid_account;
            redirect('admin/users');
        }

        /* Get the projects total */
        $user_projects_total = Database::$database->query("SELECT COUNT(*) AS `total` FROM `projects` WHERE `user_id` = {$user_id}")->fetch_object()->total ?? 0;

        /* Get the links total */
        $user_links_total = Database::$database->query("SELECT COUNT(*) AS `total` FROM `links` WHERE `user_id` = {$user_id}")->fetch_object()->total ?? 0;

        /* Get the domains total */
        $user_domains_total = Database::$database->query("SELECT COUNT(*) AS `total` FROM `domains` WHERE `user_id` = {$user_id}")->fetch_object()->total ?? 0;

        /* Get the payments made from this account */
        $user_payments_total = in_array($this->settings->license->type, ['SPECIAL', 'Extended License']) ? Database::$database->query("SELECT COUNT(*) AS `total` FROM `payments` WHERE `user_id` = {$user_id}")->fetch_object()->total ?? 0 : 0;

        /* Get last X logs */
        $user_logs_result = Database::$database->query("SELECT * FROM `users_logs` WHERE `user_id` = {$user_id} ORDER BY `id` DESC LIMIT 15");

        /* Get the current plan details */
        $user->plan = (new Plan(['settings' => $this->settings]))->get_plan_by_id($user->plan_id);

        /* Check if its a custom plan */
        if($user->plan_id == 'custom') {
            $user->plan->settings = $user->plan_settings;
        }

        /* Login Modal */
        $view = new \Altum\Views\View('admin/users/user_login_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Delete Modal */
        $view = new \Altum\Views\View('admin/users/user_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Main View */
        $data = [
            'user'                  => $user,
            'user_projects_total'  => $user_projects_total,
            'user_links_total'  => $user_links_total,
            'user_domains_total'  => $user_domains_total,
            'user_payments_total'  => $user_payments_total,
            'user_logs_result'      => $user_logs_result
        ];

        $view = new \Altum\Views\View('admin/user-view/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
