<?php

require_once 'globals.php';
require_once 'config.php';
require_once 'mailer.php';

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


/* Get all the selected products */
$token = session_get_cookie_value();
$cart = cart_get_session_products($token);

function body_create_cart_table($cart) {
    $body = '';
    $body = $body . '<table id=cart_product_list>';
    $body = $body . '<tr>';
        $body = $body . '<th>Voorbeeld</th>';
        $body = $body . '<th>SKU</th>';
        $body = $body . '<th>Naam</th>';
        $body = $body . '<th>Maat</th>';
        $body = $body . '<th>Afmetingen</th>';
        $body = $body . '<th>Prijs</th>';
        $body = $body . '<th>Aantal</th>';
        $body = $body . '<th>Subtotaal</th>';
    $body = $body . '</tr>';

    $cnt = 0;
    foreach ($cart as $cart_row) {
        if ($cnt % 2 === 0) {
            $body = $body . '<tr>';
        } else {
            $body = $body . '<tr class=cart_product_list_alt_row>';
        }
            $body = $body . '<td><center>';
            /* The ' < count($prod->images) - 1;' is a bug, the amount if always two */
            for ($x = 0; $x < count($cart_row['prod']->images); $x++) {
                /* Hardcode the first image to be displayed only */
                $img_url = product_image_to_url($cart_row['prod']->images[$x]);

                $body = $body . '<a href="'. $img_url .'" data-lightbox="'.$cart_row['prod']->sku.':'.$cart_row['prod']->name.'" data-title="'.$cart_row['prod']->sku.': '.$cart_row['prod']->name.' ' .count($cart_row['prod']->images). '">';
                if ($x === 0) {
                    /* Only show the first picture out of a gallery per product catelog */
                    $body = $body . '<img class="product_img_cart" src="'. $img_url .'">';
                }
                $body = $body . '</a>';
            }
            $body = $body . '</center></td>';
            $body = $body . '<td>'.$cart_row['prod']->sku.'</td>';
            $body = $body . '<td>'.$cart_row['prod']->name.'</td>';
            $s = strlen($cart_row['prod']->clothing_size) > 0 ? $cart_row['prod']->clothing_size : 'nvt';
            $body = $body . '<td>'.$s.'</td>'; /* Must change to choosen option */
            $s = strlen($cart_row['prod']->dimensions) > 0 ? $cart_row['prod']->dimensions : 'nvt';
            $body = $body . '<td>'.$s.'</td>'; /* Must change to choosen option */
            $body = $body . '<td>'.
                    number_format($cart_row['prod']->price, 2, ',', '.')
                .' euro</td>';
            $body = $body . '<td>'.$cart_row['count'].'</td>';
            $body = $body . '<td>'.
                    number_format($cart_row['prod']->price * $cart_row['count'], 2, ',', '.')
                .' euro</td>';

        $body = $body . '</tr>';
    }

    $total = 0.00;
    foreach ($cart as $cart_row) {
        $total += $cart_row['prod']->price * $cart_row['count'];
    }


        $body = $body . '<tr>'; 
            $body = $body . '<th>Totaal</th>';
            $body = $body . '<th>&nbsp;</th>';
            $body = $body . '<th>&nbsp;</th>';
            $body = $body . '<th>&nbsp;</th>';
            $body = $body . '<th>&nbsp;</th>';
            $body = $body . '<th>&nbsp;</th>';
            $body = $body . '<th>';
                $body = $body . number_format($total, 2, ',', '.').' euro';
            $body = $body . '</th>';
        $body = $body . '</tr>';
    $body = $body . '</table>';

    return $body;
}

$body = body_create_cart_table($cart);


send_mail_from_mailer('Bestelling', $body, 'okoeroo@gmail.com', 'Oscar Koeroo');


echo '<!DOCTYPE HTML>'."\n";
echo '<html lang="en-US"><head><meta charset="UTF-8">'."\n";
echo '<meta http-equiv="refresh" content="1;url='.$_SERVER['HTTP_REFERER'].'">';
echo '</head></html>';


?>
