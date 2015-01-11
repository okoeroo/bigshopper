<?php

require_once 'globals.php';
require_once 'config.php';
require_once 'database.php';
require_once 'session.php';
require_once 'cart.php';

date_default_timezone_set('Etc/UTC');

require_once '/usr/share/php/libphp-phpmailer/class.phpmailer.php';



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

if (!isset($_POST) or !isset($_POST['token'])) {
    echo "Error in request";
    http_response_code(400);
    return;
}

/* Fetch shoppingcart by session */
$cart = cart_get_session_products($_POST['token']);

/* var_dump($cart); */


echo '<!DOCTYPE HTML>'."\n";
echo '<html lang="en-US"><head><meta charset="UTF-8">'."\n";
echo '<meta http-equiv="refresh" content="1;url='.$_SERVER['HTTP_REFERER'].'">';
echo '</head></html>';

?>
