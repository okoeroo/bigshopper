<?php

require_once 'globals.php';
require_once 'config.php';
require_once 'database.php';
require_once 'product.php';
require_once 'category.php';
require_once 'session.php';

date_default_timezone_set('Etc/UTC');

function cart_del_record($token, $shoppingcart_id) {
    $db = $GLOBALS['db'];

    $session = session_search_by_token($token);
    if ($session === NULL) {
        return False;
    }

    $sql = 'DELETE FROM shoppingcart '.
           '      WHERE id = :id '.
           '        AND session_id = :session_id';
    $sth = $db->handle->prepare($sql);
    if (! $sth->execute(array(
            ':session_id'=>$session->id,
            ':id'=>$shoppingcart_id))) {
        return False;
    }
    return True;
}


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

if (!isset($_POST) or !isset($_POST['shoppingcart_id'])) {
    echo "Error in request";
    http_response_code(400);
    return;
}

/* Fetch POST info and generate a Product */
$token           = session_get_cookie_value();
$shoppingcart_id = $_POST['shoppingcart_id'];

if (!cart_del_record($token,
                     $shoppingcart_id)) {
    echo "Failure";
}

echo '<!DOCTYPE HTML>'."\n";
echo '<html lang="en-US"><head><meta charset="UTF-8">'."\n";
echo '<meta http-equiv="refresh" content="1;url='.$_SERVER['HTTP_REFERER'].'">';
echo '</head></html>';

?>
