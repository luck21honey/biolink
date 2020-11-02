<?php

/* Enabling debug mode is only for debugging / development purposes. */
define('DEBUG', false);

/* Enabling mysql debug mode is only for debugging / development purposes. */
define('MYSQL_DEBUG', false);

require_once realpath(__DIR__) . '/app/init.php';

$App = new Altum\App();
