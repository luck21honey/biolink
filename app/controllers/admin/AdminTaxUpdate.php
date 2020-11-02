<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Csrf;
use Altum\Middlewares\Authentication;

class AdminTaxUpdate extends Controller {

    public function index() {

        Authentication::guard('admin');

        $tax_id = isset($this->params[0]) ? $this->params[0] : false;

        if(!$tax = Database::get('*', 'taxes', ['tax_id' => $tax_id])) {
            redirect('admin/taxes');
        }

        $tax->countries = json_decode($tax->countries);

        if(!empty($_POST)) {
            /* Filter some the variables */
            $_POST['internal_name'] = Database::clean_string($_POST['internal_name']);
            $_POST['name'] = Database::clean_string($_POST['name']);
            $_POST['description'] = Database::clean_string($_POST['description']);

            if(!Csrf::check()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            }

            if(empty($_SESSION['error'])) {

                $stmt = $this->database->prepare("UPDATE `taxes` SET `internal_name` = ?, `name` = ?, `description` = ? WHERE `tax_id` = ?");
                $stmt->bind_param('ssss', $_POST['internal_name'], $_POST['name'], $_POST['description'], $tax_id);
                $stmt->execute();
                $stmt->close();

                /* Set a nice success message */
                $_SESSION['success'][] = $this->language->global->success_message->basic;

                /* Refresh the page */
                redirect('admin/tax-update/' . $tax_id);

            }

        }

        /* Main View */
        $data = [
            'tax_id'       => $tax_id,
            'tax'          => $tax,
        ];

        $view = new \Altum\Views\View('admin/tax-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
