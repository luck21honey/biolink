<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Csrf;
use Altum\Middlewares\Authentication;

class AdminPagesCategoryUpdate extends Controller {

    public function index() {

        Authentication::guard('admin');

        $pages_category_id = (isset($this->params[0])) ? $this->params[0] : false;

        /* Check if user exists */
        if(!$pages_category = Database::get('*', 'pages_categories', ['pages_category_id' => $pages_category_id])) {
            redirect('admin/pages');
        }

        if(!empty($_POST)) {
            /* Filter some the variables */
            $_POST['url'] = get_slug(Database::clean_string($_POST['url']), '-');
            $_POST['title'] = Database::clean_string($_POST['title']);
            $_POST['description'] = Database::clean_string($_POST['description']);
            $_POST['icon'] = Database::clean_string($_POST['icon']);
            $_POST['order'] = (int) $_POST['order'] ?? 0;

            $required_fields = ['url', 'title'];

            /* Check for the required fields */
            foreach($_POST as $key => $value) {
                if(empty($value) && in_array($key, $required_fields)) {
                    $_SESSION['error'][] = $this->language->global->error_message->empty_fields;
                    break 1;
                }
            }

            if(!Csrf::check()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            }

            /* If there are no errors continue the process */
            if(empty($_SESSION['error'])) {
                /* Update the database */
                $stmt = Database::$database->prepare("UPDATE `pages_categories` SET `url` = ?, `title` = ?, `description` = ?, `icon` = ?, `order` = ? WHERE `pages_category_id` = ?");
                $stmt->bind_param('ssssss', $_POST['url'], $_POST['title'], $_POST['description'], $_POST['icon'], $_POST['order'], $pages_category->pages_category_id);
                $stmt->execute();
                $stmt->close();

                /* Set a nice success message */
                $_SESSION['success'][] = $this->language->global->success_message->basic;
                redirect('admin/pages-category-update/' . $pages_category->pages_category_id);

            }
        }

        /* Delete Modal */
        $view = new \Altum\Views\View('admin/pages/pages_category_delete_modal', (array) $this);
        \Altum\Event::add_content($view->run(), 'modals');

        /* Main View */
        $data = [
            'pages_category' => $pages_category
        ];

        $view = new \Altum\Views\View('admin/pages-category-update/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
