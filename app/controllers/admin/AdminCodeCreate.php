<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Date;
use Altum\Middlewares\Csrf;
use Altum\Middlewares\Authentication;

class AdminCodeCreate extends Controller {

    public function index() {

        Authentication::guard('admin');

        if(!empty($_POST)) {
            /* Filter some the variables */
            $_POST['type'] = in_array($_POST['type'], ['discount', 'redeemable']) ? Database::clean_string($_POST['type']) : 'discount';
            $_POST['days'] = $_POST['type'] == 'redeemable' ? (int) $_POST['days'] : null;
            $_POST['plan_id'] = empty($_POST['plan_id']) ? null : (int) $_POST['plan_id'];
            $_POST['discount'] = $_POST['type'] == 'redeemable' ? 100 : (int) $_POST['discount'];
            $_POST['quantity'] = (int) $_POST['quantity'];

            if(!Csrf::check()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            }

            if(empty($_SESSION['error'])) {
                /* Update the database */
                $stmt = Database::$database->prepare("INSERT INTO `codes` (`type`, `days`, `plan_id`, `code`, `discount`, `quantity`, `date`) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('sssssss', $_POST['type'], $_POST['days'], $_POST['plan_id'], $_POST['code'], $_POST['discount'], $_POST['quantity'], Date::$date);
                $stmt->execute();
                $stmt->close();

                /* Set a nice success message */
                $_SESSION['success'][] = $this->language->global->success_message->basic;

                redirect('admin/codes');
            }
        }

        /* Get all the plans available */
        $plans_result = $this->database->query("SELECT plan_id, `name` FROM plans WHERE `status` = 1");

        /* Main View */
        $data = [
            'plans_result' => $plans_result
        ];

        $view = new \Altum\Views\View('admin/code-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
