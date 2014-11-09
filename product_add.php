<?php

require 'config.php';
require 'product.php';

date_default_timezone_set('Etc/UTC');

function db_connect() {
    try {
        $db = new Database;
        $db->handle = new PDO($db->dsn, $db->user, $db->pass);

        if ($db->debug) {
            $db->handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    } catch (Exception $e) {
        return NULL;
    }

    return $db;
}



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
    echo 'new product';
    $product->fillFromPost();
    echo 'fillFromPost';
} catch (Exception $e) {
    var_dump($e);
    return;
}

/* Store player */
if (!$product->store($db)) {
    display_failure();
    return;
}

echo 'Great success!';

?>

