<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Logger;

class ActivateUser extends Controller {

    public function index() {

        $md5email = isset($_GET['email']) ? $_GET['email'] : false;
        $email_activation_code = isset($_GET['email_activation_code']) ? $_GET['email_activation_code'] : false;
        $type = isset($_GET['type']) && in_array($_GET['type'], ['user_activation', 'user_pending_email']) ? $_GET['type'] : 'user_activation';

        $redirect = 'dashboard';
        if(isset($_GET['redirect']) && $redirect = $_GET['redirect']) {
            $redirect = Database::clean_string($redirect);
        }

        if(!$md5email || !$email_activation_code) redirect();

        /* Check if the activation code is correct */
        switch($type) {
            case 'user_activation':

                if(!$profile_account = Database::get(['user_id', 'email'], 'users', ['email_activation_code' => $email_activation_code])) redirect();

                if(md5($profile_account->email) != $md5email) redirect();

                $user_agent = Database::clean_string($_SERVER['HTTP_USER_AGENT']);

                /* Activate the account and reset the email_activation_code */
                $stmt = Database::$database->prepare("UPDATE `users` SET `active` = 1, `email_activation_code` = NULL, `last_user_agent` = ?, `total_logins` = `total_logins` + 1 WHERE `user_id` = ?");
                $stmt->bind_param('ss', $user_agent, $profile_account->user_id);
                $stmt->execute();
                $stmt->close();

                Logger::users($profile_account->user_id, 'activate.success');

                /* Login and set a successful message */
                $_SESSION['user_id'] = $profile_account->user_id;
                $_SESSION['success'][] = $this->language->activate_user->user_activation;

                Logger::users($profile_account->user_id, 'login.success');

                redirect($redirect);

                break;

            case 'user_pending_email':

                if(!$profile_account = Database::get(['user_id', 'pending_email'], 'users', ['email_activation_code' => $email_activation_code])) redirect();

                if(md5($profile_account->pending_email) != $md5email) redirect();

                /* Confirm the new email address and reset the email_activation_code */
                $stmt = Database::$database->prepare("UPDATE `users` SET `email` = ?, `pending_email` = NULL, `email_activation_code` = NULL WHERE `user_id` = ?");
                $stmt->bind_param('ss', $profile_account->pending_email, $profile_account->user_id);
                $stmt->execute();
                $stmt->close();

                Logger::users($profile_account->user_id, 'email_change.success');

                $_SESSION['success'][] = $this->language->activate_user->user_pending_email;

                redirect('account');

                break;
        }

    }

}
