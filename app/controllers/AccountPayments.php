<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Authentication;
use Altum\Middlewares\Csrf;
use Altum\Models\Plan;
use Altum\Models\User;
use Altum\Routing\Router;

class AccountPayments extends Controller {

    public function index() {

        Authentication::guard();

        if(!$this->settings->payment->is_enabled) {
            redirect('dashboard');
        }

        /* Prepare the paginator */
        $total_rows = Database::$database->query("SELECT COUNT(*) AS `total` FROM `payments` WHERE `user_id` = {$this->user->user_id}")->fetch_object()->total ?? 0;
        $paginator = (new \Altum\Paginator($total_rows, 25, $_GET['page'] ?? 1, url('account-payments?page=%d')));

        /* Get the payments list for the user */
        $payments = [];
        $payments_result = Database::$database->query("SELECT `payments`.*, plans.`name` AS `plan_name` FROM `payments` LEFT JOIN plans ON `payments`.plan_id = plans.plan_id WHERE `user_id` = {$this->user->user_id} ORDER BY `id` DESC LIMIT {$paginator->getSqlOffset()}, {$paginator->getItemsPerPage()}");
        while($row = $payments_result->fetch_object()) $payments[] = $row;

        /* Prepare the pagination view */
        $pagination = (new \Altum\Views\View('partials/pagination', (array) $this))->run(['paginator' => $paginator]);

        /* Establish the account header view */
        $menu = new \Altum\Views\View('partials/account_header', (array) $this);
        $this->add_view_content('account_header', $menu->run());

        /* Prepare the View */
        $data = [
            'payments' => $payments,
            'pagination' => $pagination
        ];

        $view = new \Altum\Views\View('account-payments/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
