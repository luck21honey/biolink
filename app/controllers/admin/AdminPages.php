<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Csrf;
use Altum\Models\Plan;
use Altum\Models\User;
use Altum\Middlewares\Authentication;
use Altum\Response;
use Altum\Routing\Router;

class AdminPages extends Controller {

    public function index() {

        Authentication::guard('admin');

        /* Get all the pages categories */
        $pages_categories_result = $this->database->query("
            SELECT 
                `pages_categories`.*,
                COUNT(`pages`.`page_id`) AS `total_pages`
            FROM `pages_categories`
            LEFT JOIN `pages` ON `pages`.`pages_category_id` = `pages_categories`.`pages_category_id`
            GROUP BY `pages_categories`.`pages_category_id`
            ORDER BY `pages_categories`.`order` ASC
        ");

        $pages_result = Database::$database->query("
            SELECT 
                `pages`.*,
                `pages_categories`.`icon` AS `pages_category_icon`,
                `pages_categories`.`title` AS `pages_category_title`
            FROM `pages`
            LEFT JOIN `pages_categories` ON `pages_categories`.`pages_category_id` = `pages`.`pages_category_id`
            ORDER BY `pages`.`order` ASC
        ");

        /* Delete Modal */
        $view = new \Altum\Views\View('admin/pages/page_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Delete Modal */
        $view = new \Altum\Views\View('admin/pages/pages_category_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Main View */
        $data = [
            'pages_categories_result' => $pages_categories_result,
            'pages_result' => $pages_result
        ];

        $view = new \Altum\Views\View('admin/pages/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function delete() {

        Authentication::guard();

        $page_id = (isset($this->params[0])) ? $this->params[0] : false;

        if(!Csrf::check('global_token')) {
            $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
        }

        if(empty($_SESSION['error'])) {

            /* Delete the page */
            Database::$database->query("DELETE FROM `pages` WHERE `page_id` = {$page_id}");

            /* Clear cache */
            \Altum\Cache::$adapter->deleteItems(['pages_top', 'pages_bottom', 'pages_hidden']);

            /* Success message */
            $_SESSION['success'][] = $this->language->admin_page_delete_modal->success_message;

            redirect('admin/pages');

        }

        die();
    }

}
