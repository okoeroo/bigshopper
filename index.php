<?php

require_once 'config.php';
require_once 'database.php';
require_once 'build.php';
require_once 'product.php';
require_once 'navigation.php';
require_once 'article.php';
require_once 'session.php';


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

/* Hard coded: article 2 */
section_display($db, 2);

/* Load all products in memory from DB */
$products = products_load($db);

$tail = new Tail;
$tail->display();


?>
