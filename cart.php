<?php

require_once 'globals.php';
require_once 'config.php';
require_once 'build.php';
require_once 'product.php';
require_once 'navigation.php';
require_once 'article.php';
require_once 'session.php';
require_once 'category.php';


function show_hardcoded_frontpage($db) {
    /* Hard coded: article 2 */
    section_display(2);

    /* Load all products in memory from DB */
    $products = products_load();
}

/* Global initializers */
if (!initialize()) {
    http_response_code(500);
    return;
}

/* Session management */
session_mngt();

$head = new Head;
$head->display();

/* Navigation bar */
navigation_display(navigation_load());


echo '<h2>Winkelwagen</h2>';

function cart_get_session_products() {
    $db = $GLOBALS['db'];
    $cart = array();

    $sql = 'SELECT products.id, products.sku, products.name, '.
           '       products.description, products.price, '.
           '       products.clothing_size, products.dimensions, '.
           '       products.changed_on, '.
           '       shoppingcart.count '.
           '  FROM products, sessions, shoppingcart '.
           ' WHERE shoppingcart.product_id = products.id'.
           '   AND sessions.id = shoppingcart.session_id'.
           '   AND sessions.token = :token';

    $token = session_get_cookie_value();

    $sth = $db->handle->prepare($sql);
    if (! $sth->execute(array(
        ':token'=>$token))) {
        return NULL;
    }
    $rs = $sth->fetchAll(PDO::FETCH_ASSOC);

    /* Empty cart? */
    if (count($rs) === 0) {
        return NULL;
    }

    /* Create the cart array, filled with items that are of class Product and
     * their registered amount */
    foreach($rs as $row) {
        $cart_item = array();

        $prod = new Product();
        $prod->fillFromRow($row);

        $cart_item['prod']  = $prod;
        $cart_item['count'] = $row['count'];

        array_push($cart, $cart_item);
    }
    return $cart;
}

$cart = cart_get_session_products();
if (count($cart) == 0) {
    echo '<h3>Uw winkelmandje is leeg</h3>';
} else {
    var_dump($cart);
}


$tail = new Tail;
$tail->display();


?>
