<?php

require_once 'config.php';
require_once 'database.php';

function initialize() {
    date_default_timezone_set('Etc/UTC');

    $GLOBALS['site'] = new Site;

    /* Open DB */
    $db = db_connect();
    if ($db && $db->handle === NULL) {
        http_response_code(500);
        return False;
    }
    $GLOBALS['db'] = $db;

    return True;
}
