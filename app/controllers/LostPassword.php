<?php

namespace Altum\Controllers;

use Altum\Captcha;
use Altum\Database\Database;
use Altum\Language;
use Altum\Logger;
use Altum\Middlewares\Authentication;

class LostPassword extends Controller {

    public function index() {

        Authentication::guard('guest');

        /* Default values */
        $values = [
            'email' => ''
        ];

        /* Initiate captcha */
        $captcha = new Captcha([
            'recaptcha' => $this->settings->captcha->recaptcha_is_enabled,
            'recaptcha_public_key' => $this->settings->captcha->recaptcha_public_key,
            'recaptcha_private_key' => $this->settings->captcha->recaptcha_private_key
        ]);

        if(!empty($_POST)) {
            /* Clean the posted variable */
            $_POST['email'] = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $values['email'] = $_POST['email'];

            /* Check for any errors */
            if(!$captcha->is_valid()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_captcha;
            }

            /* If there are no errors, resend the activation link */
            if(empty($_SESSION['error'])) {

                $this_account = Database::get(['user_id', 'email', 'name', 'active', 'language'], 'users', ['email' => $_POST['email']]);

                if($this_account && $this_account->active != 2) {
                    /* Define some variables */
                    $lost_password_code = md5($_POST['email'] . microtime());

                    /* Update the current activation email */
                    Database::$database->query("UPDATE `users` SET `lost_password_code` = '{$lost_password_code}' WHERE `user_id` = {$this_account->user_id}");

                    /* Get the language for the user */
                    $language = Language::get($this_account->language);

                    /* Prepare the email */
                    $email_template = get_email_template(
                        [
                            '{{NAME}}' => $this_account->name,
                        ],
                        $language->global->emails->user_lost_password->subject,
                        [
                            '{{LOST_PASSWORD_LINK}}' => url('reset-password/' . $_POST['email'] . '/' . $lost_password_code),
                            '{{NAME}}' => $this_account->name,
                        ],
                        $language->global->emails->user_lost_password->body
                    );

                    /* Send the email */
                    send_mail($this->settings, $this_account->email, $email_template->subject, $email_template->body);

                    Logger::users($this_account->user_id, 'lost_password.request');
                }

                /* Set success message */
                $_SESSION['success'][] = $this->language->lost_password->notice_message->success;
            }
        }

        /* Prepare the View */
        $data = [
            'values'    => $values,
            'captcha'   => $captcha
        ];

        $view = new \Altum\Views\View('lost-password/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
