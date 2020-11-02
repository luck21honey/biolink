<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Date;
use Altum\Middlewares\Csrf;
use Altum\Middlewares\Authentication;

class AdminTaxCreate extends Controller {

    public function index() {

        Authentication::guard('admin');

        if(!empty($_POST)) {
            /* Filter some the variables */
            $_POST['internal_name'] = Database::clean_string($_POST['internal_name']);
            $_POST['name'] = Database::clean_string($_POST['name']);
            $_POST['description'] = Database::clean_string($_POST['description']);
            $_POST['value'] = (int) $_POST['value'];
            $_POST['value_type'] = in_array($_POST['value_type'], ['percentage', 'fixed']) ? Database::clean_string($_POST['value_type']) : 'fixed';
            $_POST['type'] = in_array($_POST['type'], ['inclusive', 'exclusive']) ? Database::clean_string($_POST['type']) : 'inclusive';
            $_POST['billing_type'] = in_array($_POST['billing_type'], ['personal', 'business', 'both']) ? Database::clean_string($_POST['billing_type']) : 'both';
            $_POST['countries'] = isset($_POST['countries']) ? Database::clean_array($_POST['countries']) : null;

            if(!Csrf::check()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            }

            if(empty($_SESSION['error'])) {
                /* Update the database */
                $stmt = Database::$database->prepare("INSERT INTO `taxes` (`internal_name`, `name`, `description`, `value`, `value_type`, `type`, `billing_type`, `countries`, `datetime`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('sssssssss', $_POST['internal_name'], $_POST['name'], $_POST['description'], $_POST['value'], $_POST['value_type'], $_POST['type'], $_POST['billing_type'], $_POST['countries'], Date::$date);
                $stmt->execute();
                $stmt->close();

                /* Set a nice success message */
                $_SESSION['success'][] = $this->language->global->success_message->basic;

                redirect('admin/taxes');
            }
        }

        /* Main View */
        $data = [];

        $view = new \Altum\Views\View('admin/tax-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
