<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Csrf;
use Altum\Middlewares\Authentication;

class AdminTaxes extends Controller {

    public function index() {

        Authentication::guard('admin');

        $taxes_result = $this->database->query("SELECT * FROM `taxes`");

        /* Main View */
        $data = [
            'taxes_result' => $taxes_result
        ];

        $view = new \Altum\Views\View('admin/taxes/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function delete() {

        Authentication::guard();

        $tax_id = (isset($this->params[0])) ? $this->params[0] : false;

        if(!Csrf::check('global_token')) {
            $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            redirect('admin/taxes');
        }

        if(empty($_SESSION['error'])) {

            /* Delete the tax */
            Database::$database->query("DELETE FROM `taxes` WHERE `tax_id` = {$tax_id}");

            /* Success message */
            $_SESSION['success'][] = $this->language->admin_tax_delete_modal->success_message;

            redirect('admin/taxes');

        }

        die();
    }

}
