<?php

namespace Altum\Models;

use Altum\Database\Database;

class Domain extends Model {

    public function get_domains($user) {

        if($user->plan_settings->additional_global_domains) {
            $where = "(user_id = {$user->user_id} OR `type` = 1)";
        } else {
            $where = "user_id = {$user->user_id}";
        }

        $where .= " AND `is_enabled` = 1";

        $result = Database::$database->query("SELECT * FROM `domains` WHERE {$where}");
        $data = [];

        while($row = $result->fetch_object()) {

            /* Build the url */
            $row->url = $row->scheme . $row->host . '/';

            $data[] = $row;
        }

        return $data;
    }

    public function get_domain($domain_id) {

        $domain_id = (int) Database::clean_string($domain_id);

        $result = Database::$database->query("SELECT * FROM `domains` WHERE `domain_id` = {$domain_id}");

        if(!$result->num_rows) return false;

        $row = $result->fetch_object();

        /* Build the url */
        $row->url = $row->scheme . $row->host . '/';

        return $row;
    }

}
