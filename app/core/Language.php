<?php

namespace Altum;

class Language {
    public static $language;
    public static $languages = [];
    public static $path;
    public static $default_language;
    public static $language_objects = [];

    public static function initialize($path, $default_language) {

        self::$path = $path;
        self::$default_language = $default_language;
        self::$language = self::$default_language;

        /* Determine all the langauges available in the directory */
        foreach(glob(self::$path . '*.json') as $file) {
            $file = explode('/', $file);
            self::$languages[] = str_replace('.json', '', trim(end($file)));
        }

        /* If the cookie is set and the language file exists, override the default language */
        if(isset($_COOKIE['language']) && in_array($_COOKIE['language'], self::$languages)) self::$language = $_COOKIE['language'];

        /* Check if the language wants to be checked via the GET variable */
        if(isset($_GET['language'])) {
            $_GET['language'] = filter_var($_GET['language'], FILTER_SANITIZE_STRING);

            /* Check if the requested language exists and set it if needed */
            self::set($_GET['language']);
        }

    }

    public static function get($language = null) {

        if(!$language) $language = self::$language;

        /* Make sure we have access to the requested language */
        if(!in_array($language, self::$languages)) {

            /* Try and use the default one if available */
            if(in_array(self::$default_language, self::$languages)) {
                $language = self::$default_language;
            } else {
                die('Requested language "' . $language . '" does not exist and the default language "' . self::$default_language . '" does not exist as well.');
            }

        }

        /* Check if we already processed the language file */
        if(isset(self::$language_objects[$language])) {
            return self::$language_objects[$language];
        }

        /* Include the language file */
        self::$language_objects[$language] = json_decode(file_get_contents(self::$path . $language . '.json'));

        /* Check the language file */
        if(is_null(self::$language_objects[$language])) {
            die('The language file is corrupted. Please make sure your JSON Language file is JSON Validated ( you can do that with an online JSON Validator by searching on Google ).');
        }

        return self::$language_objects[$language];
    }

    public static function set($language, $cookie = true) {

        if(in_array($language, self::$languages)) {
            if($cookie) {
                setcookie('language', $language, time()+60*60*24*3);
            }
            self::$language = $language;
        }

    }
}
