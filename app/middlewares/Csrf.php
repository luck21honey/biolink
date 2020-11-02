<?php

namespace Altum\Middlewares;

class Csrf extends Middleware {

    /* CSRF Protection for ajax requests */
    public static function set($name = 'token', $regenerate = false) {

        $token =  md5(time() . rand());

        if(!isset($_SESSION[$name])) {
            $_SESSION[$name] = $token;
        } else {

            if($regenerate) $_SESSION[$name] = $token;

        }

    }

    public static function get($name = 'token') {

        return $_SESSION[$name] ?? false;

    }

    public static function get_url_query($name = 'token') {

        return '&token=' . self::get($name);

    }

    public static function check($name = 'token') {
        return (
            (isset($_GET[$name]) && $_GET[$name] == self::get($name)) ||
            (isset($_POST[$name]) && $_POST[$name] == self::get($name))
        );
    }

//    public static function csrf_page_protection_check($name = 'default', $response = true) {
//        global $language;
//
//
//        if(!self::csrf_check_session_token($name)) {
//            if($response) Response::json($this->language->global->error_message->command_denied, 'error');
//            die();
//        }
//    }

}
