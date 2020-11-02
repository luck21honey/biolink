<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Csrf;
use Altum\Middlewares\Authentication;
use Altum\Models\User;

class AdminCodes extends Controller {

    public function index() {

        Authentication::guard('admin');

        /* Delete Modal */
        $view = new \Altum\Views\View('admin/codes/code_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        $codes_result = $this->database->query("
            SELECT `codes`.*, plans.`name` AS `plan_name`
            FROM `codes`
            LEFT JOIN plans ON `codes`.`plan_id` = plans.plan_id
        ");

        /* Main View */
        $data = [
            'codes_result' => $codes_result
        ];

        $view = new \Altum\Views\View('admin/codes/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function delete() {

        Authentication::guard();

        $code_id = (isset($this->params[0])) ? $this->params[0] : false;

        if(!Csrf::check('global_token')) {
            $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            redirect('admin/codes');
        }

        if(empty($_SESSION['error'])) {

            /* Delete the code */
            Database::$database->query("DELETE FROM `codes` WHERE `code_id` = {$code_id}");

            /* Success message */
            $_SESSION['success'][] = $this->language->admin_code_delete_modal->success_message;

            redirect('admin/codes');

        }

        die();
    }

}
