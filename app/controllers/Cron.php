<?php

namespace Altum\Controllers;

class Cron extends Controller {

    public function index() {

        /* Initiation */
        set_time_limit(0);

        /* Make sure the key is correct */
        if(!isset($_GET['key']) || (isset($_GET['key']) && $_GET['key'] != $this->settings->cron->key)) {
            die();
        }

        die();
    }

}
