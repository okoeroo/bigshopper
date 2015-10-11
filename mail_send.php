<?php

require_once 'globals.php';
require_once 'config.php';
require_once 'category.php';
require_once 'product.php';
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


var_dump($_POST);


/* Get all the selected products */
$token = session_get_cookie_value();
$cart = cart_get_session_products($token);

function body_create_cart_table($cart) {
    $body = '<html><head><body>';

    $body = $body . 'Bedankt voor uw bestelling bij Allkidslove.nl';


    $body = $body . '<table border=1>';
    $body = $body . '<tr>';
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
        $body = $body . '<tr>';
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
    $body = $body . '</body>';
    $body = $body . '</html>';

    return $body;
}

$body = body_create_cart_table($cart);


send_mail_from_mailer('Bestelling', $body, 'okoeroo@gmail.com', 'Oscar Koeroo');


echo '<!DOCTYPE HTML>'."\n";
echo '<html lang="en-US"><head><meta charset="UTF-8">'."\n";
echo '<meta http-equiv="refresh" content="10;url='.$_SERVER['HTTP_REFERER'].'">';

print $body;

echo '</head></html>';


?>
