<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Csrf;
use Altum\Middlewares\Authentication;

class AdminPageCreate extends Controller {

    public function index() {

        Authentication::guard('admin');

        if(!empty($_POST)) {
            /* Filter some the variables */
            $_POST['title'] = Database::clean_string($_POST['title']);
            $_POST['description'] = Database::clean_string($_POST['description']);
            $_POST['type'] = in_array($_POST['type'], ['internal', 'external']) ? Database::clean_string($_POST['type']) : 'internal';
            $_POST['position'] = in_array($_POST['position'], ['hidden', 'top', 'bottom']) ? $_POST['position'] : 'top';
            $_POST['pages_category_id'] = empty($_POST['pages_category_id']) ? null : (int) $_POST['pages_category_id'];
            $_POST['order'] = (int) $_POST['order'];

            switch($_POST['type']) {
                case 'internal':
                    $_POST['url'] = get_slug(Database::clean_string($_POST['url']), '-');
                    break;


                case 'external':
                    $_POST['url'] = Database::clean_string($_POST['url']);
                    break;
            }

            $required_fields = ['title', 'url'];

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

            /* If there are no errors continue the updating process */
            if(empty($_SESSION['error'])) {
                $stmt = Database::$database->prepare("INSERT INTO `pages` (`pages_category_id`, `url`, `title`, `description`, `content`, `type`, `position`, `order`, `date`, `last_date`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('ssssssssss', $_POST['pages_category_id'], $_POST['url'], $_POST['title'], $_POST['description'], $_POST['content'], $_POST['type'], $_POST['position'], $_POST['order'], \Altum\Date::$date, \Altum\Date::$date);
                $stmt->execute();
                $stmt->close();

                /* Clear cache */
                \Altum\Cache::$adapter->deleteItem('pages_' . $_POST['position']);

                /* Set a nice success message */
                $_SESSION['success'][] = $this->language->global->success_message->basic;
                redirect('admin/pages');
            }

        }

        /* Get the pages categories available */
        $pages_categories_result = $this->database->query("SELECT `pages_category_id`, `title` FROM `pages_categories`");

        /* Main View */
        $data = ['pages_categories_result' => $pages_categories_result];

        $view = new \Altum\Views\View('admin/page-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));
    }

}
