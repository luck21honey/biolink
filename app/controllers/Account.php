<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Middlewares\Authentication;
use Altum\Middlewares\Csrf;
use Altum\Models\User;

class Account extends Controller {

    public function index() {

        Authentication::guard();

        /* Prepare the TwoFA codes just in case we need them */
        $twofa = new \RobThree\Auth\TwoFactorAuth($this->settings->title, 6, 30);
        $twofa_secret = $twofa->createSecret();
        $twofa_image = $twofa->getQRCodeImageAsDataUri($this->user->name, $twofa_secret);

        if(!empty($_POST)) {

            /* Clean some posted variables */
            $_POST['email']		        = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $_POST['name']		        = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
            $_POST['timezone']          = in_array($_POST['timezone'], \DateTimeZone::listIdentifiers()) ? Database::clean_string($_POST['timezone']) : $this->settings->default_timezone;
            $_POST['twofa_is_enabled']  = (bool) $_POST['twofa_is_enabled'];
            $_POST['twofa_token']       = trim(filter_var($_POST['twofa_token'], FILTER_SANITIZE_STRING));
            $twofa_secret               = $_POST['twofa_is_enabled'] ? $this->user->twofa_secret : null;

            /* Billing */
            if(empty($this->user->payment_subscription_id)) {
                $_POST['billing_type'] = in_array($_POST['billing_type'], ['personal', 'business']) ? Database::clean_string($_POST['billing_type']) : 'personal';
                $_POST['billing_name'] = trim(Database::clean_string($_POST['billing_name']));
                $_POST['billing_address'] = trim(Database::clean_string($_POST['billing_address']));
                $_POST['billing_city'] = trim(Database::clean_string($_POST['billing_city']));
                $_POST['billing_county'] = trim(Database::clean_string($_POST['billing_county']));
                $_POST['billing_zip'] = trim(Database::clean_string($_POST['billing_zip']));
                $_POST['billing_country'] = array_key_exists($_POST['billing_country'], get_countries_array()) ? Database::clean_string($_POST['billing_country']) : 'US';
                $_POST['billing_phone'] = trim(Database::clean_string($_POST['billing_phone']));
                $_POST['billing_tax_id'] = $_POST['billing_type'] == 'business' ? trim(Database::clean_string($_POST['billing_tax_id'])) : '';
                $_POST['billing'] = json_encode([
                    'type' => $_POST['billing_type'],
                    'name' => $_POST['billing_name'],
                    'address' => $_POST['billing_address'],
                    'city' => $_POST['billing_city'],
                    'county' => $_POST['billing_county'],
                    'zip' => $_POST['billing_zip'],
                    'country' => $_POST['billing_country'],
                    'phone' => $_POST['billing_phone'],
                    'tax_id' => $_POST['billing_tax_id'],
                ]);
            }

            /* Check for any errors */
            if(!Csrf::check()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            }
            if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == false) {
                $_SESSION['error'][] = $this->language->register->error_message->invalid_email;
            }
            if(Database::exists('user_id', 'users', ['email' => $_POST['email']]) && $_POST['email'] !== $this->user->email) {
                $_SESSION['error'][] = $this->language->register->error_message->email_exists;
            }

            if(strlen($_POST['name']) < 3 || strlen($_POST['name'] > 32)) {
                $_SESSION['error'][] = $this->language->register->error_message->name_length;
            }

            if(!empty($_POST['old_password']) && !empty($_POST['new_password'])) {
                if(!password_verify($_POST['old_password'], $this->user->password)) {
                    $_SESSION['error'][] = $this->language->account->error_message->invalid_current_password;
                }
                if(strlen(trim($_POST['new_password'])) < 6) {
                    $_SESSION['error'][] = $this->language->account->error_message->short_password;
                }
                if($_POST['new_password'] !== $_POST['repeat_password']) {
                    $_SESSION['error'][] = $this->language->account->error_message->passwords_not_matching;
                }
            }

            if($_POST['twofa_is_enabled'] && $_POST['twofa_token']) {
                $twofa_check = $twofa->verifyCode($_SESSION['twofa_potential_secret'], $_POST['twofa_token']);

                if(!$twofa_check) {
                    $_SESSION['error'][] = $this->language->account->error_message->twofa_check;

                    /* Regenerate */
                    $twofa_secret = $twofa->createSecret();
                    $twofa_image = $twofa->getQRCodeImageAsDataUri($this->user->name, $twofa_secret);

                } else {
                    $twofa_secret = $_SESSION['twofa_potential_secret'];
                }

            }

            if(empty($_SESSION['error'])) {

                /* Only update the billing if no active subscriptions are found */
                if(!empty($this->user->payment_subscription_id)) {
                    $_POST['billing'] = json_encode($this->user->billing);
                }

                /* Prepare the statement and execute query */
                $stmt = Database::$database->prepare("UPDATE `users` SET `name` = ?, `billing` = ?, `timezone` = ?, `twofa_secret` = ? WHERE `user_id` = ?");
                $stmt->bind_param('sssss', $_POST['name'], $_POST['billing'], $_POST['timezone'], $twofa_secret, $this->user->user_id);
                $stmt->execute();
                $stmt->close();

                $_SESSION['success'][] = $this->language->account->success_message->account_updated;

                /* Check for an email address change */
                if($_POST['email'] != $this->user->email) {

                    if($this->settings->email_confirmation) {
                        $email_activation_code = md5($_POST['email'] . microtime());

                        /* Prepare the email */
                        $email_template = get_email_template(
                            [],
                            $this->language->global->emails->user_pending_email->subject,
                            [
                                '{{ACTIVATION_LINK}}' => url('activate-user?email=' . md5($_POST['email']) . '&email_activation_code=' . $email_activation_code . '&type=user_pending_email'),
                                '{{NAME}}' => $this->user->name,
                                '{{CURRENT_EMAIL}}' => $this->user->email,
                                '{{NEW_EMAIL}}' => $_POST['email']
                            ],
                            $this->language->global->emails->user_pending_email->body
                        );

                        send_mail($this->settings, $_POST['email'], $email_template->subject, $email_template->body);

                        /* Save the potential new email as pending */
                        $stmt = Database::$database->prepare("UPDATE `users` SET `pending_email` = ?, `email_activation_code` = ? WHERE `user_id` = ?");
                        $stmt->bind_param('sss', $_POST['email'], $email_activation_code, $this->user->user_id);
                        $stmt->execute();
                        $stmt->close();

                        $_SESSION['info'][] = $this->language->account->info_message->user_pending_email;

                    } else {

                        /* Save the new email without verification */
                        $stmt = Database::$database->prepare("UPDATE `users` SET `email` = ? WHERE `user_id` = ?");
                        $stmt->bind_param('ss', $_POST['email'], $this->user->email);
                        $stmt->execute();
                        $stmt->close();

                    }

                }

                if(!empty($_POST['old_password']) && !empty($_POST['new_password'])) {
                    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

                    Database::update('users', ['password' => $new_password], ['user_id' => $this->user->user_id]);

                    /* Set a success message and log out the user */
                    Authentication::logout();
                }

                redirect('account');
            }

        }

        /* Store the potential secret */
        $_SESSION['twofa_potential_secret'] = $twofa_secret;

        /* Establish the account header view */
        $menu = new \Altum\Views\View('partials/account_header', (array) $this);
        $this->add_view_content('account_header', $menu->run());

        /* Prepare the View */
        $data = [
            'twofa_secret'  => $twofa_secret,
            'twofa_image'   => $twofa_image
        ];

        $view = new \Altum\Views\View('account/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

    public function cancelsubscription() {

        Authentication::guard();

        if(!Csrf::check()) {
            $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            redirect('account');
        }

        if(empty($_SESSION['error'])) {

            try {
                (new User(['settings' => $this->settings, 'user' => $this->user]))->cancel_subscription();
            } catch (\Exception $exception) {

                /* Output errors properly */
                if (DEBUG) {
                    echo $exception->getCode() . '-' . $exception->getMessage();

                    die();
                } else {

                    $_SESSION['error'][] = $exception->getMessage();
                    redirect('account');

                }
            }

            /* Set a message */
            $_SESSION['success'][] = $this->language->account->success_message->subscription_canceled;

            redirect('account');

        }

    }

}
