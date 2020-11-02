<?php

namespace Altum\Database;

class Database {

    public static $database;

    public static function initialize() {

        self::$database = new \mysqli(
            DATABASE_SERVER,
            DATABASE_USERNAME,
            DATABASE_PASSWORD,
            DATABASE_NAME
        );

        /* Debugging */
        if(self::$database->connect_error) {
            die('The connection to the database failed ! Please edit the "app/config/config.php" file and make sure your database connection details are correct!');
        }

        /* Mysql profiling */
        if(MYSQL_DEBUG) {
            self::$database->query("set profiling_history_size=100");
            self::$database->query("set profiling=1");
        }

        self::$database->set_charset('utf8mb4');

        return self::$database;
    }

    public static function get($what, $from, Array $conditions = [], $order = false, $clean = true) {

        $what = ($what == '*') ? '*' : '`' . implode('`, `', $what) . '`';
        $from = '`' . $from . '`';
        $where = [];

        foreach($conditions as $key => $value) {
            $value = ($clean) ? self::clean_string($value) : $value;
            $where[] = '`' . $key . '` = \'' . $value . '\'';
        }
        $where = implode(' AND ', $where);

        $order_by = ($order) ? 'ORDER BY ' . $order : null;

        $result = self::$database->query("SELECT {$what} FROM {$from} WHERE {$where} {$order_by}");

        return ($result->num_rows) ? $result->fetch_object() : false;

    }

    public static function simple_get($raw_what, $from, Array $conditions, $clean = true) {

        $what = '`' . $raw_what . '`';

        $from = '`' . $from . '`';

        $where = [];
        foreach($conditions as $key => $value) {
            $value = ($clean) ? self::clean_string($value) : $value;
            $where[] = '`' . $key . '` = \'' . $value . '\'';
        }
        $where = implode(' AND ', $where);

        $result = self::$database->query("SELECT {$what} FROM {$from} WHERE {$where}");
        $data = $result->fetch_object();

        return ($result->num_rows) ? $data->{$raw_what} : false;

    }

    public static function exists($what = [], $from, $conditions = []) {

        $what = (!is_array($what)) ? '`' . $what . '`' : '`' . implode('`, `', $what) . '`';
        $from = '`' . $from . '`';
        $where = [];

        foreach($conditions as $key => $value) $where[] = '`' . $key . '` = \'' . $value . '\'';
        $where = implode(' AND ', $where);


        $result = self::$database->query("SELECT {$what} FROM {$from} WHERE {$where}");

        return ($result->num_rows) ? $result->num_rows : false;

    }

    public static function update($what, $fields = [], $conditions = []) {

        $what = '`' . $what . '`';
        $parameters = [];
        $where = [];

        foreach($fields as $key => $value) $parameters[] = '`' . $key . '` = \'' . $value . '\'';
        $parameters = implode(', ', $parameters);

        foreach($conditions as $key => $value) $where[] = '`' . $key . '` = \'' . $value . '\'';
        $where = implode(' AND ', $where);


        return self::$database->query("UPDATE {$what} SET {$parameters} WHERE {$where}");

    }

    public static function insert($table, $data = [], $clean = true) {

        $parameters = [];
        $values = [];

        foreach($data as $key => $value) {
            $parameters[] = $key;
            $values[] = ($clean) ? self::clean_string($value) : $value;
        }

        $parameters_string = '`' . implode('`, `', $parameters) . '`';
        $values_string = '\'' . implode('\', \'', $values) . '\'';

        return self::$database->query("INSERT INTO `{$table}` ({$parameters_string}) VALUES ({$values_string})");
    }

    public static function clean_string($data) {
        return self::$database->escape_string(filter_var($data, FILTER_SANITIZE_STRING));
    }

    public static function clean_array(Array $data) {
        foreach($data as $key => $value) {
            $data[$key] = self::clean_string($value);
        }

        return $data;
    }

    public static function close() {

        if(MYSQL_DEBUG) {
            $result = self::$database->query("show profiles");

            while($profile = $result->fetch_object()) {
                echo $profile->Query_ID . ' - ' . round($profile->Duration, 4) * 1000 . ' ms - ' . $profile->Query . '<br />';
            }
        }

        self::$database->close();
    }
}
