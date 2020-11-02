<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Csrf;
use Altum\Models\Plan;
use Altum\Models\User;
use Altum\Middlewares\Authentication;
use Altum\Response;
use Altum\Routing\Router;

class AdminPagesCategories extends Controller {

    public function index() {

        Authentication::guard('admin');

       redirect('pages');

    }

    public function delete() {

        Authentication::guard();

        $pages_category_id = (isset($this->params[0])) ? $this->params[0] : false;

        if(!Csrf::check('global_token')) {
            $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
        }

        if(empty($_SESSION['error'])) {

            /* Delete the page */
            Database::$database->query("DELETE FROM `pages_categories` WHERE `pages_category_id` = {$pages_category_id}");

            /* Success message */
            $_SESSION['success'][] = $this->language->admin_pages_category_delete_modal->success_message;

            redirect('admin/pages');

        }

        die();
    }

}
