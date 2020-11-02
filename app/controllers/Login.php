<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Language;
use Altum\Logger;
use Altum\Middlewares\Authentication;
use Altum\Models\User;
use MaxMind\Db\Reader;

class Login extends Controller {

    public function index() {

        Authentication::guard('guest');

        $method	= (isset($this->params[0])) ? $this->params[0] : false;
        $redirect = isset($_GET['redirect']) ? Database::clean_string($_GET['redirect']) : 'dashboard';

        /* Default values */
        $values = [
            'email' => $_GET['email'] ?? '',
            'password' => '',
        ];

        /* Detect the location and other data */
        if(!empty($_POST) || $method) {
            $ip = get_ip();
            $maxmind = (new Reader(APP_PATH . 'includes/GeoLite2-Country.mmdb'))->get($ip);
            $country = $maxmind ? $maxmind['country']['iso_code'] : null;
            $user_agent = Database::clean_string($_SERVER['HTTP_USER_AGENT']);
        }

        /* Facebook Login / Register */
        if($this->settings->facebook->is_enabled && !empty($this->settings->facebook->app_id) && !empty($this->settings->facebook->app_secret)) {

            $facebook = new \Facebook\Facebook([
                'app_id' => $this->settings->facebook->app_id,
                'app_secret' => $this->settings->facebook->app_secret,
                'default_graph_version' => 'v3.2',
            ]);

            $facebook_helper = $facebook->getRedirectLoginHelper();
            $facebook_login_url = $facebook->getRedirectLoginHelper()->getLoginUrl(url('login/facebook'), ['email', 'public_profile']);

            /* Check for the redirect after the oauth checkin */
            if($method == 'facebook') {
                try {
                    $facebook_access_token = $facebook_helper->getAccessToken(url('login/facebook'));
                } catch(Facebook\Exceptions\FacebookResponseException $e) {
                    $_SESSION['error'][] = 'Graph returned an error: ' . $e->getMessage();
                } catch(Facebook\Exceptions\FacebookSDKException $e) {
                    $_SESSION['error'][] = 'Facebook SDK returned an error: ' . $e->getMessage();
                }
            }

            if(isset($facebook_access_token)) {

                /* The OAuth 2.0 client handler helps us manage access tokens */
                $facebook_oAuth2_client = $facebook->getOAuth2Client();

                /* Get the access token metadata from /debug_token */
                $facebook_token_metadata = $facebook_oAuth2_client->debugToken($facebook_access_token);

                /* Validation */
                $facebook_token_metadata->validateAppId($this->settings->facebook->app_id);
                $facebook_token_metadata->validateExpiration();

                if(!$facebook_access_token->isLongLived()) {
                    /* Exchanges a short-lived access token for a long-lived one */
                    try {
                        $facebook_access_token = $facebook_oAuth2_client->getLongLivedAccessToken($facebook_access_token);
                    } catch (Facebook\Exceptions\FacebookSDKException $e) {
                        $_SESSION['error'][] = 'Error getting long-lived access token: ' . $facebook_helper->getMessage();
                    }
                }

                try {
                    $response = $facebook->get('/me?fields=id,name,email', $facebook_access_token);
                } catch(Facebook\Exceptions\FacebookResponseException $e) {
                    $_SESSION['error'][] = 'Graph returned an error: ' . $e->getMessage();
                } catch(Facebook\Exceptions\FacebookSDKException $e) {
                    $_SESSION['error'][] = 'Facebook SDK returned an error: ' . $e->getMessage();
                }

                if(isset($response)) {
                    $facebook_user = $response->getGraphUser();
                    $facebook_user_id = $facebook_user->getId();
                    $email = $facebook_user->getEmail();

                    /* Check if email is actually not null */
                    if(is_null($email)) {
                        $_SESSION['error'][] = $this->language->login->error_message->email_is_null;

                        redirect('login');
                    }

                    /* If the user is already in the system, log him in */
                    if($user = Database::get(['user_id'], 'users', ['email' => $email])) {
                        $_SESSION['user_id'] = $user->user_id;

                        (new User())->login_aftermath_update($user->user_id, $ip, $country, $user_agent);

                        redirect($redirect);
                    }

                    /* Create a new account */
                    else {

                        if(empty($_SESSION['error'])) {
                            $password                   = password_hash(string_generate(8), PASSWORD_DEFAULT);
                            $name                       = $facebook_user->getName();
                            $active                     = 1;
                            $total_logins               = 1;
                            $timezone                   = $this->settings->default_timezone;

                            /* Determine what plan is set by default */
                            $plan_id                    = 'free';
                            $plan_settings              = json_encode($this->settings->plan_free->settings);
                            $plan_expiration_date       = \Altum\Date::$date;

                            /* When only the trial plan is available make that the default one */
                            if(!$this->settings->plan_free->status && $this->settings->plan_trial->status) {
                                $plan_id                = 'trial';
                                $plan_settings          = json_encode($this->settings->plan_trial->settings);
                                $plan_expiration_date   = (new \DateTime())->modify('+' . $this->settings->plan_trial->days . ' days')->format('Y-m-d H:i:s');
                            }

                            /* Add the user to the database */
                            $stmt = Database::$database->prepare("INSERT INTO `users` (`password`, `email`, `name`, `facebook_id`, `plan_id`, `plan_expiration_date`, `plan_settings`, `language`, `timezone`, `active`, `date`, `ip`, `country`, `last_user_agent`, `total_logins`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                            $stmt->bind_param('sssssssssssssss', $password, $email, $name, $facebook_user_id, $plan_id, $plan_expiration_date, $plan_settings, Language::$language, $timezone, $active, \Altum\Date::$date, $ip, $country, $user_agent, $total_logins);
                            $stmt->execute();
                            $registered_user_id = $stmt->insert_id;
                            $stmt->close();

                            /* Log the action */
                            Logger::users($registered_user_id, 'register.facebook_register');

                            /* Send notification to admin if needed */
                            if($this->settings->email_notifications->new_user && !empty($this->settings->email_notifications->emails)) {

                                send_mail(
                                    $this->settings,
                                    explode(',', $this->settings->email_notifications->emails),
                                    $this->language->global->emails->admin_new_user_notification->subject,
                                    sprintf($this->language->global->emails->admin_new_user_notification->body, $name, $email)
                                );

                            }

                            /* Log the user in and redirect him */
                            $_SESSION['user_id'] = $registered_user_id;
                            $_SESSION['success'][] = $this->language->register->success_message->login;

                            Logger::users($registered_user_id, 'login.success');

                            redirect($redirect);
                        }
                    }
                }
            }
        }

        if(!empty($_POST)) {
            /* Clean email and encrypt the password */
            $_POST['email'] = Database::clean_string($_POST['email']);
            $_POST['twofa_token'] = isset($_POST['twofa_token']) ? Database::clean_string($_POST['twofa_token']) : null;
            $values['email'] = $_POST['email'];
            $values['password'] = $_POST['password'];

            /* Check for any errors */
            if(empty($_POST['email']) || empty($_POST['password'])) {
                $_SESSION['error'][] = $this->language->global->error_message->empty_fields;
            }

            /* Try to get the user from the database */
            $result = Database::$database->query("SELECT `user_id`, `email`, `active`, `password`, `token_code`, `total_logins`, `twofa_secret` FROM `users` WHERE `email` = '{$_POST['email']}'");
            $login_account = $result->num_rows ? $result->fetch_object() : false;

            if(!$login_account) {
                $_SESSION['error'][] = $this->language->login->error_message->wrong_login_credentials;
            } else {

                if($login_account->active != 1) {
                    $_SESSION['error'][] = $this->language->login->error_message->user_not_active;
                } else

                    if(!password_verify($_POST['password'], $login_account->password)) {
                        Logger::users($login_account->user_id, 'login.wrong_password');

                        $_SESSION['error'][] = $this->language->login->error_message->wrong_login_credentials;
                    }

            }

            /* Check if the user has Two-factor Authentication enabled */
            if($login_account && $login_account->twofa_secret) {

                if($_POST['twofa_token']) {

                    $twofa = new \RobThree\Auth\TwoFactorAuth($this->settings->title, 6, 30);
                    $twofa_check = $twofa->verifyCode($login_account->twofa_secret, $_POST['twofa_token']);

                    if(!$twofa_check) {
                        $_SESSION['error'][] = $this->language->login->error_message->twofa_token;
                    }

                } else {

                    $_SESSION['info'] = $this->language->login->info_message->twofa_token;

                }

            }

            if(empty($_SESSION['error']) && empty($_SESSION['info'])) {

                /* If remember me is checked, log the user with cookies for 30 days else, remember just with a session */
                if(isset($_POST['rememberme'])) {
                    $token_code = $login_account->token_code;

                    /* Generate a new token */
                    if(empty($login_account->token_code)) {
                        $token_code = md5($login_account->email . microtime());

                        Database::update('users', ['token_code' => $token_code], ['user_id' => $login_account->user_id]);
                    }

                    setcookie('email', $login_account->email, time()+60*60*24*30);
                    setcookie('token_code', $token_code, time()+60*60*24*30);

                } else {
                    $_SESSION['user_id'] = $login_account->user_id;
                }

                (new User())->login_aftermath_update($login_account->user_id, $ip, $country, $user_agent);

                $_SESSION['info'][] = $this->language->login->info_message->logged_in;
                redirect($redirect);
            }
        }

        /* Prepare the View */
        $data = [
            'values' => $values,
            'facebook_login_url' => $facebook_login_url ?? null,
            'login_account' => $login_account ?? null
        ];

        $view = new \Altum\Views\View('login/index', (array) $this);

        $this->add_view_content('content', $view->run($data));

    }

}
