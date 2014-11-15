<?php

require 'config.php';
require 'database.php';
require 'product.php';

date_default_timezone_set('Etc/UTC');


/* Only POST */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'Error: POST only';
    http_response_code(400);
    return;
}

/* Open DB */
$db = db_connect();
if ($db && $db->handle === NULL) {
    http_response_code(500);
    return;
}

/* Fetch POST info and generate a Player */
try {
    $product = new Product;
    $product->fillFromPost();
} catch (Exception $e) {
    var_dump($e);
    return;
}

/* Store player */
if (!$product->store($db)) {
    display_failure();
    return;
}


echo '<!DOCTYPE HTML>'."\n";
echo '<html lang="en-US"><head><meta charset="UTF-8">'."\n";
echo '<meta http-equiv="refresh" content="1;url=http://devel.koeroo.net/admin.php">';
echo '</head></html>';


?>
