<?php

namespace Altum\Models;

use Altum\Database\Database;

class Page extends Model {

    public function get_pages($position) {

        $data = [];

        $cache_instance = \Altum\Cache::$adapter->getItem('pages_' . $position);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            $result = Database::$database->query("SELECT `url`, `title`, `type` FROM `pages` WHERE `position` = '{$position}' ORDER BY `order`");

            while($row = $result->fetch_object()) {

                if($row->type == 'internal') {

                    $row->target = '_self';
                    $row->url = url('page/' . $row->url);

                } else {

                    $row->target = '_blank';

                }

                $data[] = $row;
            }

            \Altum\Cache::$adapter->save($cache_instance->set($data)->expiresAfter(86400));

        } else {

            /* Get cache */
            $data = $cache_instance->get();

        }

        return $data;
    }

}
