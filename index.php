<?php

require_once 'config.php';
require_once 'database.php';
require_once 'build.php';
require_once 'product.php';
require_once 'navigation.php';
require_once 'article.php';
require_once 'session.php';
require_once 'category.php';


function show_hardcoded_frontpage($db) {
    /* Hard coded: article 2 */
    section_display($db, 2);

    /* Load all products in memory from DB */
    $products = products_load($db);
}


/* Open DB */
$db = db_connect();
if ($db && $db->handle === NULL) {
    http_response_code(500);
    return;
}

/* Session management */
session_mngt($db);

$head = new Head;
$head->display();

/* Navigation bar */
navigation_display(navigation_load($db));


/* Based on the query string, load various content */
$query = explode("&", $_SERVER['QUERY_STRING']);

/* Weird effect, after exploding the query string always has one element in the
 * array with a zero length string. Bug in PHP? */
if (count($query) === 1 && strlen($query[0]) === 0) {
    show_hardcoded_frontpage($db);
} else {
    /* Display a product */
    foreach($query as $q) {
        list($key, $value) = explode("=", $q);

        /* Show an entire category based on the query string data */
        if ($key == "category") {
            /* Load an entire category display here */
            category_display_load($db, $value);
        } else {
            /* On error / experimentation, just show the front page */
            show_hardcoded_frontpage($db);
        }
    }
}



$tail = new Tail;
$tail->display();


?>
