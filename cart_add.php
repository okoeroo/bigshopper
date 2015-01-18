<?php

require_once 'globals.php';
require_once 'config.php';
require_once 'database.php';
require_once 'product.php';
require_once 'category.php';
require_once 'session.php';

date_default_timezone_set('Etc/UTC');

function cart_product_and_session_count($session_id, $product_id, $dimensions, $clothing_size) {
    $db = $GLOBALS['db'];

    $sql = 'SELECT count '.
           '  FROM shoppingcart '.
           ' WHERE session_id    = :session_id '.
           '   AND product_id    = :product_id ';

    $params = array(
        ':session_id'=>$session_id,
        ':product_id'=>$product_id);
    if ($dimensions === NULL) {
        $sql = $sql . ' AND dimensions IS NULL ';
    } else {
        $sql =  $sql . ' AND dimensions = :dimensions ';
        $params[':dimensions'] = $dimensions;
    }

    if ($clothing_size === NULL) {
        $sql = $sql . ' AND clothing_size IS NULL ';
    } else {
        $sql = $sql . ' AND clothing_size = :clothing_size ';
        $params[':clothing_size'] = $clothing_size;
    }

    $sth = $db->handle->prepare($sql);
    if (! $sth->execute($params)) { 
        return -2; /* Error */
    }
    /* $rs = $sth->fetchAll(PDO::FETCH_ASSOC);  */
    $rs = db_cast_query_results($sth);
    foreach($rs as $row) {
        return intval($row['count']); /* On found. report the number */
    }
    return -1; /* Not found, does not exist */
}

function cart_add_product_to_cart($token, $product_id, $dimensions, $clothing_size) {
    $db = $GLOBALS['db'];

    $session = session_search_by_token($token);
    if ($session === NULL) {
        return False;
    }

    /* Check if it already exists, if yes, perform a plus one */
    $cur_count = cart_product_and_session_count($session->id, $product_id, $dimensions, $clothing_size);

    /* Checked value: Failure reported, fail here */
    if ($cur_count === -2) {
        return False;

    /* Checked value: Reported as non-existant, insert as new */
    } else if ($cur_count === -1) {
        $sql = 'INSERT INTO shoppingcart ('.
               '            session_id, product_id, '.
               '            dimensions, clothing_size, '.
               '            count)'.
               '     VALUES ('.
               '            :session_id, :product_id, '.
               '            :dimensions, :clothing_size, '.
               '            1)';
        $sth = $db->handle->prepare($sql);
        if (! $sth->execute(array(
                ':session_id'=>$session->id,
                ':product_id'=>$product_id,
                ':dimensions'=>$dimensions,
                ':clothing_size'=>$clothing_size))) {
            return False;
        }
        return True;

    /* Checked value: Session ID and Product ID exist, count will be updated */
    } else {
        $sql = 'UPDATE shoppingcart '.
               '   SET count = :new_count'.
               ' WHERE session_id = :session_id '.
               '   AND product_id = :product_id';

        $params = array(
            ':new_count'=>$cur_count + 1,
            ':session_id'=>$session->id,
            ':product_id'=>$product_id);
        if ($dimensions === NULL) {
            $sql = $sql . ' AND dimensions IS NULL ';
        } else {
            $sql =  $sql . ' AND dimensions = :dimensions ';
            $params[':dimensions'] = $dimensions;
        }

        if ($clothing_size === NULL) {
            $sql = $sql . ' AND clothing_size IS NULL ';
        } else {
            $sql = $sql . ' AND clothing_size = :clothing_size ';
            $params[':clothing_size'] = $clothing_size;
        }

        $sth = $db->handle->prepare($sql);
        if (! $sth->execute($params)) {
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

if (isset($_POST['dimensions'])) {
    $dimensions = $_POST['dimensions'];
} else {
    $dimensions = NULL;
}

if (isset($_POST['clothing_size'])) {
    $clothing_size = $_POST['clothing_size'];
} else {
    $clothing_size = NULL;
}


if (!cart_add_product_to_cart(session_get_cookie_value(),
                              $product_id,
                              $dimensions,
                              $clothing_size)) {
    echo "Failure";
}

echo '<!DOCTYPE HTML>'."\n";
echo '<html lang="en-US"><head><meta charset="UTF-8">'."\n";
echo '<meta http-equiv="refresh" content="1;url='.$_SERVER['HTTP_REFERER'].'">';
echo '</head></html>';

?>
