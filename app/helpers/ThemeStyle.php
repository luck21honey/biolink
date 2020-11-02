<?php

namespace Altum;

use Altum\Database\Database;

class ThemeStyle {
    public static $themes = [
        'light' => [
            'file' => 'bootstrap.min.css'
        ],
        'dark' => [
            'file' => 'bootstrap-dark.min.css'
        ],
    ];
    public static $theme = 'light';

    public static function get() {
        if(isset($_COOKIE['theme_style']) && array_key_exists($_COOKIE['theme_style'], self::$themes)) {
            self::$theme = Database::clean_string($_COOKIE['theme_style']);
        }

        return self::$theme;
    }

    public static function get_file() {
        return self::$themes[self::get()]['file'];
    }

    public static function set() {

    }

}
