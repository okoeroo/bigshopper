<?php

require 'config.php';
require 'database.php';
require 'build.php';
require 'product.php';
require 'navigation.php';
require 'article.php';


/* Open DB */
$db = db_connect();
if ($db && $db->handle === NULL) {
    http_response_code(500);
    return;
}

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
