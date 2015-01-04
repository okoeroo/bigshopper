<?php

require_once 'globals.php';
require_once 'config.php';
require_once 'build.php';
require_once 'product.php';
require_once 'navigation.php';
require_once 'article.php';
require_once 'session.php';
require_once 'category.php';



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
           '       shoppingcart.clothing_size, shoppingcart.dimensions, '.
           '       products.changed_on, '.
           '       shoppingcart.count, '.
           '       shoppingcart.id as shoppingcart_id'.
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

        $cart_item['prod']            = $prod;
        $cart_item['count']           = $row['count'];
        $cart_item['shoppingcart_id'] = $row['shoppingcart_id'];

        array_push($cart, $cart_item);
    }
    return $cart;
}

/* Get all the selected products */
$cart = cart_get_session_products();
if (count($cart) == 0) {
    echo '<h3>Uw winkelmandje is leeg</h3>';
} else {
    echo '<table id=cart_product_list>';
    echo '<tr>';
        echo '<th>Voorbeeld</th>';
        echo '<th>SKU</th>';
        echo '<th>Naam</th>';
        echo '<th>Maat</th>';
        echo '<th>Afmetingen</th>';
        echo '<th>Prijs</th>';
        echo '<th>Aantal</th>';
        echo '<th>Subtotaal</th>';
        echo '<th>Verwijderen?</th>';
    echo '</tr>';

    $cnt = 0;
    foreach ($cart as $cart_row) {
        if ($cnt % 2 === 0) {
            echo '<tr>';
        } else {
            echo '<tr class=cart_product_list_alt_row>';
        }
            echo '<td>plaatje</td>';
            echo '<td>'.$cart_row['prod']->sku.'</td>';
            echo '<td>'.$cart_row['prod']->name.'</td>';
            $s = strlen($cart_row['prod']->clothing_size) > 0 ? $cart_row['prod']->clothing_size : 'nvt';
            echo '<td>'.$s.'</td>'; /* Must change to choosen option */
            $s = strlen($cart_row['prod']->dimensions) > 0 ? $cart_row['prod']->dimensions : 'nvt';
            echo '<td>'.$s.'</td>'; /* Must change to choosen option */
            echo '<td>'.
                    number_format($cart_row['prod']->price, 2, ',', '.')
                .' euro</td>';
            echo '<td>'.$cart_row['count'].'</td>';
            echo '<td>'.
                    number_format($cart_row['prod']->price * $cart_row['count'], 2, ',', '.')
                .' euro</td>';

            echo '<td>';
                echo '<center>';
                echo '<form action="cart_del.php" method="POST" enctype="multipart/form-data">' . "\n";
                echo '<input type="hidden" name="shoppingcart_id" value="'.$cart_row['shoppingcart_id'].'">';
                echo '<input type="submit" name="submit" value="     Delete     ">'."\n";
                echo '</form>';
                echo '</center>';
            echo '</td>';
        echo '</tr>';
    }

    $total = 0.00;
    foreach ($cart as $cart_row) {
        $total += $cart_row['prod']->price * $cart_row['count'];
    }

        echo '<tr>'; 
            echo '<th>Totaal</td>';
            echo '<th>&nbsp;</td>';
            echo '<th>&nbsp;</td>';
            echo '<th>&nbsp;</td>';
            echo '<th>&nbsp;</td>';
            echo '<th>&nbsp;</td>';
            echo '<th>&nbsp;</td>';
            echo '<th>'.
                    number_format($total, 2, ',', '.')
                .' euro</td>';
        echo '</tr>';
    echo '</table>';
}

$tail = new Tail;
$tail->display();

?>
