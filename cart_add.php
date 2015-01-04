<?php

require_once 'globals.php';
require_once 'config.php';
require_once 'database.php';
require_once 'product.php';
require_once 'category.php';
require_once 'session.php';

date_default_timezone_set('Etc/UTC');

function cart_product_and_session_count($session_id, $product_id) {
    $db = $GLOBALS['db'];

    $sql = 'SELECT count '.
           '  FROM shoppingcart '.
           ' WHERE session_id = :session_id '.
           '   AND product_id = :product_id';
    $sth = $db->handle->prepare($sql);
    if (! $sth->execute(array(
        ':session_id'=>$session_id,
        ':product_id'=>$product_id))) {
        return -2; /* Error */
    }
    $rs = $sth->fetchAll(PDO::FETCH_ASSOC); 
    foreach($rs as $row) {
        return intval($row['count']); /* On found. report the number */
    }
    return -1; /* Not found, does not exist */
}

function cart_add_product_to_cart($token, $product_id) {
    $db = $GLOBALS['db'];

    $session = session_search_by_token($token);
    if ($session === NULL) {
        return False;
    }

    /* Check if it already exists, if yes, perform a plus one */
    $cur_count = cart_product_and_session_count($session->id, $product_id);

    /* Checked value: Failure reported, fail here */
    if ($cur_count === -2) {
        return False;

    /* Checked value: Reported as non-existant, insert as new */
    } else if ($cur_count === -1) {
        $sql = 'INSERT INTO shoppingcart ('.
               '            session_id, product_id, count)'.
               '     VALUES (:session_id, :product_id, 1)';
        $sth = $db->handle->prepare($sql);
        if (! $sth->execute(array(
            ':session_id'=>$session->id,
            ':product_id'=>$product_id))) {
            return False;
        }
        return True;

    /* Checked value: Session ID and Product ID exist, count will be updated */
    } else {
        $cur_count++;

        $sql = 'UPDATE shoppingcart '.
               '   SET count = :new_count'.
               ' WHERE session_id = :session_id '.
               '   AND product_id = :product_id';
        $sth = $db->handle->prepare($sql);
        if (! $sth->execute(array(
            ':new_count'=>$cur_count,
            ':session_id'=>$session->id,
            ':product_id'=>$product_id))) {
            return False;
        }
        return True;
    }
    return False;
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

if (!isset($_POST) or !isset($_POST['id'])) {
    echo "Error in request";
    http_response_code(400);
    return;
}

/* Fetch POST info and generate a Product */
$token = session_get_cookie_value();
$product_id = $_POST['id'];

if (!cart_add_product_to_cart($token, $product_id)) {
    echo "Failure";
}


echo '<!DOCTYPE HTML>'."\n";
echo '<html lang="en-US"><head><meta charset="UTF-8">'."\n";
echo '<meta http-equiv="refresh" content="1;url='.$_SERVER['HTTP_REFERER'].'">';
echo '</head></html>';

?>
