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


/* Get all the selected products */
$token = session_get_cookie_value();
$cart = cart_get_session_products($token);
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
            echo '<th>Totaal</th>';
            echo '<th>&nbsp;</th>';
            echo '<th>&nbsp;</th>';
            echo '<th>&nbsp;</th>';
            echo '<th>&nbsp;</th>';
            echo '<th>&nbsp;</th>';
            echo '<th>&nbsp;</th>';
            echo '<th>';
                echo number_format($total, 2, ',', '.').' euro';
            echo '</th>';
        echo '</tr>';
    echo '</table>';

    echo '<br>';
    echo '<a href="/order.php" class="button">Bestel</a>';
    echo '</p>';


    echo '<div class="section">';
    echo '</div>';

}



$tail = new Tail;
$tail->display();

?>
