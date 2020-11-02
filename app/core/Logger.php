<?php

namespace Altum;

use Altum\Database\Database;

class Logger {

    public static function users($user_id, $type, $public = 1) {

        $user_agent = Database::clean_string($_SERVER['HTTP_USER_AGENT']);
        $ip         = get_ip();

        Database::insert('users_logs', [
            'user_id'   => $user_id,
            'type'      => $type,
            'date'      => Date::$date,
            'ip'        => $ip,
            'public'    => $public
        ]);
    }

}
