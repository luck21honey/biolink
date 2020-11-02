<?php

namespace Altum\Controllers;

use Altum\Database\Database;
use Altum\Date;
use Altum\Middlewares\Csrf;
use Altum\Models\User;
use Altum\Title;
use MaxMind\Db\Reader;

class Link extends Controller {
    public $link;
    public $user;
    public $is_qr;

    public function index() {

        $link_url = isset($this->params[0]) ? Database::clean_string($this->params[0]) : false;
        $this->is_qr = isset($this->params[1]) && $this->params[1] == 'qr' ? Database::clean_string($this->params[1]) : false;
        $link_id = isset($_GET['link_id']) ? (int) $_GET['link_id'] : false;

        /* Check if the current link accessed is actually the original url or not ( multi domain use ) */
        $original_url_host = parse_url(url())['host'];
        $request_url_host = Database::clean_string($_SERVER['HTTP_HOST']);

        if($original_url_host == $request_url_host) {

            /* If we have the link id, get it via the link id */
            /* This is used for the preview iframe */
            if($link_id) {
                $this->link = Database::get('*', 'links', ['link_id' => $link_id]);
            } else {
                $this->link = Database::get('*', 'links', ['url' => $link_url, 'is_enabled' => 1, 'domain_id' => 0]);
            }

        } else {
            $this->link = $this->database->query("
                SELECT `links`.*, `domains`.`host`, `domains`.`scheme`
                FROM `links`
                LEFT JOIN `domains` ON `links`.`domain_id` = `domains`.`domain_id`
                WHERE
                    `links`.`url` = '{$link_url}' AND 
                    `links`.`is_enabled` = 1 AND 
                    `domains`.`host` = '{$request_url_host}' AND 
                    (`links`.`user_id` = `domains`.`user_id` OR `domains`.`type` = 1)
            ")->fetch_object() ?? null;
        }

        if(!$this->link) {
            redirect();
        }

        $this->user = (new User())->get($this->link->user_id);

        /* Make sure to check if the user is active */
        if($this->user->active != 1) {
            redirect();
        }

        /* Check if its a scheduled link and we should show it or not */
        if(
            $this->user->plan_settings->scheduling &&

            !empty($this->link->start_date) &&
            !empty($this->link->end_date) &&
            (
                \Altum\Date::get('', null) < \Altum\Date::get($this->link->start_date, null, \Altum\Date::$default_timezone) ||
                \Altum\Date::get('', null) > \Altum\Date::get($this->link->end_date, null, \Altum\Date::$default_timezone)
            )
        ) {
            redirect();
        }

        /* Parse the settings */
        $this->link->settings = json_decode($this->link->settings);

        /* Determine the actual full url */
        $this->link->full_url = $this->link->domain_id && !isset($_GET['link_id']) ? $this->link->scheme . $this->link->host . '/' . $this->link->url : url($this->link->url);

        /* If is QR code only return a QR code */
        if($this->is_qr) {

            $qr = new \Endroid\QrCode\QrCode($this->link->full_url);

            header('Content-Type: ' . $qr->getContentType());

            echo $qr->writeString();

            die();
        }

        /* Check if the user has access to the link */
        $has_access = !$this->link->settings->password || ($this->link->settings->password && isset($_COOKIE['link_password_' . $this->link->link_id]) && $_COOKIE['link_password_' . $this->link->link_id] == $this->link->settings->password);

        /* Do not let the user have password protection if the plan doesnt allow it */
        if(!$this->user->plan_settings->password) {
            $has_access = true;
        }

        /* Check if the password form is submitted */
        if(!$has_access && !empty($_POST) && isset($_POST['type']) && $_POST['type'] == 'password') {

            /* Check for any errors */
            if(!Csrf::check()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            }

            if(!password_verify($_POST['password'], $this->link->settings->password)) {
                $_SESSION['error'][] = $this->language->link->password->error_message;
            }

            if(empty($_SESSION['error'])) {

                /* Set a cookie */
                setcookie('link_password_' . $this->link->link_id, $this->link->settings->password, time()+60*60*24*30);

                header('Location: ' . $this->link->full_url);

                die();

            }

        }

        /* Check if the user has access to the link */
        $can_see_content = !$this->link->settings->sensitive_content || ($this->link->settings->sensitive_content && isset($_COOKIE['link_sensitive_content_' . $this->link->link_id]));

        /* Do not let the user have password protection if the plan doesnt allow it */
        if(!$this->user->plan_settings->sensitive_content) {
            $can_see_content = true;
        }

        /* Check if the password form is submitted */
        if(!$can_see_content && !empty($_POST) && isset($_POST['type']) && $_POST['type'] == 'sensitive_content') {

            /* Check for any errors */
            if(!Csrf::check()) {
                $_SESSION['error'][] = $this->language->global->error_message->invalid_csrf_token;
            }

            if(empty($_SESSION['error'])) {

                /* Set a cookie */
                setcookie('link_sensitive_content_' . $this->link->link_id, 'true', time()+60*60*24*30);

                header('Location: ' . $this->link->full_url);

                die();

            }

        }

        /* Display the password form */
        if(!$has_access && !isset($_GET['preview'])) {

            /* Set a custom title */
            Title::set($this->language->link->password->title);

            /* Main View */
            $view = new \Altum\Views\View('link-path/partials/password', (array) $this);

            $this->add_view_content('content', $view->run());

        }

        else if(!$can_see_content && !isset($_GET['preview'])) {

            /* Set a custom title */
            Title::set($this->language->link->sensitive_content->title);

            /* Main View */
            $view = new \Altum\Views\View('link-path/partials/sensitive_content', (array) $this);

            $this->add_view_content('content', $view->run());

        }

        else {

            /* Only parse and add statistics if its not coming from inside the preview iframe from the settings */
            if(!isset($_GET['preview'])) {
                /* Generate an id for the log */
                $dynamic_id = md5(
                    $this->link->link_id . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . (new \DateTime())->format('Y-m-d')
                );

                /* Detect extra details about the user */
                $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);

                /* Do not track bots */
                if($whichbrowser->device->type !== 'bot') {

                    /* Detect the location */
                    $maxmind = (new Reader(APP_PATH . 'includes/GeoLite2-Country.mmdb'))->get(get_ip());
                    $country_code = $maxmind ? $maxmind['country']['iso_code'] : null;

                    /* Detect extra details about the user */
                    $browser_name = $whichbrowser->browser->name;
                    $os_name = $whichbrowser->os->name;
                    $browser_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
                    $device_type = get_device_type($_SERVER['HTTP_USER_AGENT']);

                    /* Process referrer */
                    $referrer = isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;

                    /* Insert or update the log */
                    $is_insert = true;
                    $stmt = Database::$database->prepare("
                        INSERT INTO 
                            `track_links` (`project_id`, `link_id`, `dynamic_id`, `ip`, `country_code`, `os_name`, `browser_name`, `referrer`, `device_type`, `browser_language`, `date`, `last_date`) 
                        VALUES 
                            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE
                            `count` = `count` + 1,
                            `last_date` = VALUES (last_date)  
                    ");
                    $stmt->bind_param(
                        'ssssssssssss',
                        $this->link->project_id,
                        $this->link->link_id,
                        $dynamic_id,
                        $_SERVER['REMOTE_ADDR'],
                        $country_code,
                        $os_name,
                        $browser_name,
                        $referrer,
                        $device_type,
                        $browser_language,
                        Date::$date,
                        Date::$date
                    );
                    $stmt->execute();
                    if($stmt->affected_rows > 1) {
                        $is_insert = false;
                    }
                    $stmt->close();

                    /* Add the unique hit to the link table as well */
                    if ($is_insert) {
                        Database::$database->query("UPDATE `links` SET `clicks` = `clicks` + 1 WHERE `link_id` = {$this->link->link_id}");
                    }
                }
            }

            /* Check what to do next */
            if($this->link->type == 'biolink' && $this->link->subtype == 'base') {

                /* Check for a leap link */
                if($this->link->settings->leap_link && $this->user->plan_settings->leap_link && !isset($_GET['preview'])) {
                    header('Location: ' . $this->link->settings->leap_link, true, 301);
                } else {
                    $this->process_biolink();
                }

            } else {

                $this->process_redirect();

            }

        }

    }

    public function process_biolink() {

        /* Get all the links inside of the biolink */
        $cache_instance = \Altum\Cache::$adapter->getItem('biolink_links_' . $this->link->link_id);

        /* Set cache if not existing */
        if(is_null($cache_instance->get())) {

            $result = Database::$database->query("SELECT * FROM `links` WHERE `biolink_id` = {$this->link->link_id} AND `type` = 'biolink' AND `subtype` <> 'base' AND `is_enabled` = 1 ORDER BY `order` ASC");
            $links = [];

            while($row = $result->fetch_object()) {
                $links[] = $row;
            }

            \Altum\Cache::$adapter->save($cache_instance->set($links)->expiresAfter(86400)->addTag('biolinks_links_user_' . $this->link->user_id));

        } else {

            /* Get cache */
            $links = $cache_instance->get();

        }

        /* Prepare the View */
        $data = [
            'link' => $this->link,
            'links' => $links
        ];

        $view_content = \Altum\Link::get_biolink($this, $this->link, $this->user, $links);

        $this->add_view_content('content', $view_content);

        /* Set a custom title */
        Title::set($this->link->settings->title, true);
    }

    public function process_redirect() {

        /* Check if we should redirect the user or kill the script */
        if(isset($_GET['no_redirect'])) {
            die();
        }

        header('Location: ' . $this->link->location_url, true, 301);

    }
}
