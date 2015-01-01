<?php

require_once 'config.php';
require_once 'database.php';
require_once 'product.php';
require_once 'category.php';

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

/* Fetch POST info and generate a Product */
try {
    $product = new Product;
    $product->fillFromPost();
} catch (Exception $e) {
    var_dump($e);
    return;
}

/* Store product */
if (!$product->store($db)) {
    display_failure();
    return;
}

product_to_category_add_by_sku_id($db, $_POST['sku'], $_POST['category']);


echo '<!DOCTYPE HTML>'."\n";
echo '<html lang="en-US"><head><meta charset="UTF-8">'."\n";
/* echo '<meta http-equiv="refresh" content="1;url=/">'; */
echo '</head></html>';


?>
