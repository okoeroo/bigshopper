<?php

require_once 'globals.php';
require_once 'config.php';
require_once 'customer.php';


/* Only POST */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'Error: POST only';
    http_response_code(400);
    return;
}

/* Global initializers */
if (!initialize()) {
    http_response_code(500);
    return;
}


/* Fetch POST info and generate a Product */
try {
    $customer = new Customer();
    $customer->fillFromPost();
} catch (Exception $e) {
    var_dump($e);
    return;
}

/* Store customer */
if (!$customer->store()) {
    display_failure();
    return;
}


echo '<!DOCTYPE HTML>'."\n";
echo '<html lang="en-US"><head><meta charset="UTF-8">'."\n";
echo '<meta http-equiv="refresh" content="1;url='.$_SERVER['HTTP_REFERER'].'">';
echo '</head></html>';


?>
