<?php

function url($append = '') {
    return SITE_URL . $append;
}

function redirect($append = '') {
    header('Location: ' . SITE_URL . $append);

    die();
}

function get_slug($string, $delimiter = '_') {

    /* Replace all non words characters with the specified $delimiter */
    $string = preg_replace('/[^a-zA-Z0-9._-]+/', $delimiter, $string);

    /* Check for double $delimiters and remove them so it only will be 1 delimiter */
    $string = preg_replace('/' . $delimiter . '+/', $delimiter, $string);

    /* Remove the $delimiter character from the start and the end of the string */
    $string = trim($string, $delimiter);

    return $string;
}

function phishtank_check($url, $api_key = '') {
    $api_url = 'https://checkurl.phishtank.com/checkurl/';

    $body = [
        'format'    => 'json',
        'url'       => urlencode($url),
        'app_key'   => $api_key
    ];

    $response = Unirest\Request::post($api_url, null, $body);

    return isset($response->body->results->valid) && $response->body->results->valid;
}

function google_safe_browsing_check($url, $api_key = '') {
    $api_url = 'https://safebrowsing.googleapis.com/v4/threatMatches:find?key=' . $api_key;

    $body = [
        'client' => [
            'clientId' => '',
            'clientVersion' => '1.5.2'
        ],
        'threatInfo' => [
            'threatTypes' => ['MALWARE', 'SOCIAL_ENGINEERING','THREAT_TYPE_UNSPECIFIED'],
            'platformTypes' => ['ANY_PLATFORM'],
            'threatEntryTypes' => ['URL'],
            'threatEntries' => [
                ['url' => $url]
            ]
        ]

    ];

    $response = Unirest\Request::post($api_url, null, $body);

    if(isset($response->body->matches[0]->threatType) && $response->body->matches[0]->threatType) return true;

    return false;
}
