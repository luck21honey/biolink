<?php

namespace Altum;

/* Simple wrapper for phpFastCache */

class Cache {
    public static $adapter;

    public static function initialize($force_enable = false) {

        $driver = $force_enable ? 'Files' : (DEBUG ? 'Devnull' : 'Files');

        /* Cache adapter for phpFastCache */
        if($driver == 'Files') {
            $config = new \Phpfastcache\Drivers\Files\Config([
                'securityKey' => 'biolinks',
                'path' => UPLOADS_PATH . 'cache',
                'preventCacheSlams' => true,
                'cacheSlamsTimeout' => 20,
                'secureFileManipulation' => true
            ]);
        } else {
            $config = new \Phpfastcache\Config\Config([
                'path' => UPLOADS_PATH . 'cache',
            ]);
        }

        \Phpfastcache\CacheManager::setDefaultConfig($config);

        self::$adapter = \Phpfastcache\CacheManager::getInstance($driver);
    }

}
