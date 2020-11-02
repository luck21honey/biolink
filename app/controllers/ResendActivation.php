<?php

namespace Altum\Controllers;

use Altum\Captcha;
use Altum\Database\Database;
use Altum\Language;
use Altum\Middlewares\Authentication;

class ResendActivation extends Controller {

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
            if (!$captcha->is_valid()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_captcha;
            }

            /* If there are no errors, resend the activation link */
            if(empty($_SESSION['error'])) {
                $this_account = Database::get(['user_id', 'active', 'name', 'email', 'language'], 'users', ['email' => $_POST['email']]);

                if($this_account && !(bool) $this_account->active) {
                    /* Generate new email code */
                    $email_code = md5($_POST['email'] . microtime());

                    /* Update the current activation email */
                    Database::$database->query("UPDATE `users` SET `email_activation_code` = '{$email_code}' WHERE `user_id` = {$this_account->user_id}");

                    /* Get the language for the user */
                    $language = Language::get($this_account->language);

                    /* Prepare the email */
                    $email_template = get_email_template(
                        [
                            '{{NAME}}' => $this_account->name,
                        ],
                        $language->global->emails->user_activation->subject,
                        [
                            '{{ACTIVATION_LINK}}' => url('activate-user?email=' . md5($_POST['email']) . '&email_activation_code=' . $email_code . '&type=user_activation'),
                            '{{NAME}}' => $this_account->name,
                        ],
                        $language->global->emails->user_activation->body
                    );

                    /* Send the email */
                    send_mail($this->settings, $_POST['email'], $email_template->subject, $email_template->body);

                }

                /* Store success message */
                $_SESSION['success'][] = $this->language->resend_activation->notice_message->success;
            }
        }

        /* Prepare the View */
        $data = [
            'values'    => $values,
            'captcha'   => $captcha
        ];

        $view = new \Altum\Views\View('resend-activation/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
