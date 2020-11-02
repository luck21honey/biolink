<?php

namespace Altum\Middlewares;

use Altum\Database\Database;

class Authentication extends Middleware {

    public static $is_logged_in = null;
    public static $user_id = null;
    public static $user = null;

    public static function check() {

        /* Verify if the current route allows use to do the check */
        if(\Altum\Routing\Router::$controller_settings['no_authentication_check']) {
            return false;
        }

        /* Already logged in from previous checks */
        if(self::$is_logged_in) {
            return self::$user_id;
        }

        /* Check the cookies first */
        if(isset($_COOKIE['email']) && isset($_COOKIE['token_code']) && strlen($_COOKIE['token_code']) > 0 && $user = Database::get('*', 'users', ['email' => $_COOKIE['email'], 'token_code' => $_COOKIE['token_code']])) {
            self::$is_logged_in = true;
            self::$user_id = $user->user_id;

            $user->plan_settings = json_decode($user->plan_settings);
            $user->billing = json_decode($user->billing);
            self::$user = $user;

            return true;
        }

        /* Check the Session */
        if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && $user = Database::get('*', 'users', ['user_id' => $_SESSION['user_id']])) {
            self::$is_logged_in = true;
            self::$user_id = $user->user_id;

            $user->plan_settings = json_decode($user->plan_settings);
            $user->billing = json_decode($user->billing);
            self::$user = $user;

            return true;
        }

        return false;
    }


    public static function is_admin() {

        if(!self::check()) {
            return false;
        }

        return self::$user->type > 0;
    }


    public static function guard($permission = 'user') {

        switch ($permission) {
            case 'guest':

                if(self::check()) {
                    redirect(isset($_GET['redirect']) ? $_GET['redirect'] : 'dashboard');
                }

                break;

            case 'user':

                if(!self::check() || (self::check() && !self::$user->active)) {
                    redirect();
                }

                break;

            case 'admin':

                if(!self::check() || (self::check() && (!self::$user->active || self::$user->type != '1'))) {
                    redirect();
                }

                break;
        }

    }


    public static function logout($page = '') {

        if(self::check()) {
            Database::update('users', ['token_code' => ''], ['user_id' => self::$user_id]);
        }

        session_destroy();
        setcookie('username', '', time()-30);
        setcookie('token_code', '', time()-30);

        if($page !== false) {
            redirect($page);
        }
    }
}
