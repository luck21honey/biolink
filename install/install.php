<?php
define('ROOT', realpath(__DIR__ . '/..') . '/');
require_once ROOT . 'vendor/autoload.php';
require_once ROOT . 'app/includes/product.php';

$altumcode_api = 'https://api.altumcode.com/validate';

/* Make sure the product wasn't already installed */
if(file_exists(ROOT . 'install/installed')) {
    die();
}

/* Make sure all the required fields are present */
$required_fields = ['license', 'database_host', 'database_name', 'database_username', 'database_password', 'url'];

foreach($required_fields as $field) {
    if(!isset($_POST[$field])) {
        die(json_encode([
            'status' => 'error',
            'message' => 'One of the required fields are missing.'
        ]));
    }
}

/* Make sure the database details are correct */
$database = @new mysqli(
    $_POST['database_host'],
    $_POST['database_username'],
    $_POST['database_password'],
    $_POST['database_name']
);

if($database->connect_error) {
    die(json_encode([
        'status' => 'error',
        'message' => 'The database connection has failed!'
    ]));
}

/* Make sure the license is correct */
$response = Unirest\Request::post($altumcode_api, [], [
    'license'           => $_POST['license'],
    'url'               => $_POST['url'],
    'product_key'       => PRODUCT_KEY,
    'product_name'      => PRODUCT_NAME,
    'product_version'   => PRODUCT_VERSION,
    'client_email'      => $_POST['client_email'],
    'client_name'       => $_POST['client_name']
]);

if($response->body->status == 'error') {
    die(json_encode([
        'status' => 'error',
        'message' => $response->body->message
    ]));
}

/* Success check */
if($response->body->status == 'success') {

    /* Prepare the config file content */
    $config_content =
        <<<ALTUM
<?php

/* Configuration of the site */
define('DATABASE_SERVER',   '{$_POST['database_host']}');
define('DATABASE_USERNAME', '{$_POST['database_username']}');
define('DATABASE_PASSWORD', '{$_POST['database_password']}');
define('DATABASE_NAME',     '{$_POST['database_name']}');
define('SITE_URL',          '{$_POST['url']}');

ALTUM;

    /* Write the new config file */
    file_put_contents(ROOT . 'config.php', $config_content);

    /* Run SQL */
    $dump_content = file_get_contents(ROOT . 'install/dump.sql');

    $dump = explode('-- SEPARATOR --', $dump_content);

    foreach($dump as $query) {
        $database->query($query);

        if($database->error) {
            die(json_encode([
                'status' => 'error',
                'message' => 'Error when running the database queries: ' . $database->error
            ]));
        }
    }

    /* Run external SQL if needed */
    if(!empty($response->body->sql)) {
        $dump = explode('-- SEPARATOR --', $response->body->sql);

        foreach($dump as $query) {
            $database->query($query);

            if($database->error) {
                die(json_encode([
                    'status' => 'error',
                    'message' => 'Error when running the database queries: ' . $database->error
                ]));
            }
        }
    }

    /* Create the installed file */
    file_put_contents(ROOT . 'install/installed', '');

    die(json_encode([
        'status' => 'success',
        'message' => ''
    ]));
}
