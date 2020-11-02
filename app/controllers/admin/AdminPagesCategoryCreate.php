<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Csrf;
use Altum\Models\Plan;
use Altum\Models\User;
use Altum\Middlewares\Authentication;
use Altum\Response;
use Altum\Routing\Router;

class AdminPagesCategoryCreate extends Controller {

    public function index() {

        Authentication::guard('admin');

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
                $stmt = Database::$database->prepare("INSERT INTO `pages_categories` (`url`, `title`, `description`, `icon`, `order`) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param('sssss', $_POST['url'], $_POST['title'], $_POST['description'], $_POST['icon'], $_POST['order']);
                $stmt->execute();
                $stmt->close();

                /* Set a nice success message */
                $_SESSION['success'][] = $this->language->global->success_message->basic;
                redirect('admin/pages');
            }

        }

        /* Main View */
        $view = new \Altum\Views\View('admin/pages-category-create/index', (array) $this);

        $this->add_view_content('content', $view->run());
    }

}
