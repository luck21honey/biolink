<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Logger;
use Altum\Middlewares\Authentication;

class ResetPassword extends Controller {

    public function index() {

        Authentication::guard('guest');

        $email = (isset($this->params[0])) ? $this->params[0] : false;
        $lost_password_code = (isset($this->params[1])) ? $this->params[1] : false;

        if(!$email || !$lost_password_code) redirect();

        /* Check if the lost password code is correct */
        $user_id = Database::simple_get('user_id', 'users', ['email' => $email, 'lost_password_code' => $lost_password_code]);

        if($user_id < 1 || strlen($lost_password_code) < 1) redirect();

        if(!empty($_POST)) {
            /* Check for any errors */
            if(strlen(trim($_POST['new_password'])) < 6) {
                $_SESSION['error'][] = $this->language->reset_password->error_message->short_password;
            }
            if($_POST['new_password'] !== $_POST['repeat_password']) {
                $_SESSION['error'][] = $this->language->reset_password->error_message->passwords_not_matching;
            }

            if(empty($_SESSION['error'])) {
                /* Encrypt the new password */
                $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

                /* Update the password & empty the reset code from the database */
                $stmt = Database::$database->prepare("UPDATE `users` SET `password` = ?, `twofa_secret` = NULL, `lost_password_code` = 0  WHERE `user_id` = ?");
                $stmt->bind_param('ss', $new_password, $user_id);
                $stmt->execute();
                $stmt->close();

                Logger::users($user_id, 'reset_password.reset');

                /* Store success message */
                $_SESSION['success'][] = $this->language->reset_password->success_message->password_updated;

                redirect('login');
            }
        }

        /* Prepare the View */
        $data = [
            'values' => [
                'email' => $email
            ]
        ];

        $view = new \Altum\Views\View('reset-password/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
