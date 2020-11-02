<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Logger;
use Altum\Middlewares\Csrf;
use Altum\Models\Plan;
use Altum\Middlewares\Authentication;

class AdminUserCreate extends Controller {

    public function index() {

        Authentication::guard('admin');

        /* Default variables */
        $values = [
            'name' => '',
            'email' => '',
            'password' => ''
        ];

        if(!empty($_POST)) {

            /* Clean some posted variables */
            $_POST['name']		= filter_var($_POST['name'], FILTER_SANITIZE_STRING);
            $_POST['email']		= filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

            /* Default variables */
            $values['name'] = $_POST['name'];
            $values['email'] = $_POST['email'];
            $values['password'] = $_POST['password'];

            /* Define some variables */
            $fields = ['name', 'email' ,'password'];

            /* Check for any errors */
            foreach($_POST as $key=>$value) {
                if(empty($value) && in_array($key, $fields) == true) {
                    $_SESSION['error'][] = $this->language->global->error_message->empty_fields;
                    break 1;
                }
            }

            if(!Csrf::check()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            }

            if(strlen($_POST['name']) < 3 || strlen($_POST['name']) > 32) {
                $_SESSION['error'][] = $this->language->admin_user_create->error_message->name_length;
            }
            if(Database::exists('user_id', 'users', ['email' => $_POST['email']])) {
                $_SESSION['error'][] = $this->language->admin_user_create->error_message->email_exists;
            }
            if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'][] = $this->language->admin_user_create->error_message->invalid_email;
            }
            if(strlen(trim($_POST['password'])) < 6) {
                $_SESSION['error'][] = $this->language->admin_user_create->error_message->short_password;
            }

            /* If there are no errors continue the registering process */
            if(empty($_SESSION['error'])) {
                /* Define some needed variables */
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $active = 1;
                $email_code = '';
                $last_user_agent = Database::clean_string($_SERVER['HTTP_USER_AGENT']);
                $total_logins = 0;
                $plan_id = 'free';
                $plan_expiration_date = \Altum\Date::$date;
                $ip = get_ip();
                $plan_settings = json_encode($this->settings->plan_free->settings);
                $timezone = $this->settings->default_timezone;
                $language = \Altum\Language::$default_language;

                /* Add the user to the database */
                $stmt = Database::$database->prepare("INSERT INTO `users` (`password`, `email`, `email_activation_code`, `name`, `plan_id`, `plan_expiration_date`, `plan_settings`, `active`, `date`, `ip`, `last_user_agent`, `total_logins`, `timezone`, `language`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('ssssssssssssss', $password, $_POST['email'], $email_code, $_POST['name'], $plan_id, $plan_expiration_date, $plan_settings, $active, \Altum\Date::$date, $ip, $last_user_agent, $total_logins, $timezone, $language);
                $stmt->execute();
                $registered_user_id = $stmt->insert_id;
                $stmt->close();

                /* Log the action */
                Logger::users($registered_user_id, 'register.admin_register');

                /* Success message */
                $_SESSION['success'][] = $this->language->admin_user_create->success_message->created;

                /* Redirect */
                redirect('admin/user-update/' . $registered_user_id);
            }

        }

        /* Main View */
        $data = [
            'values' => $values
        ];

        $view = new \Altum\Views\View('admin/user-create/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
