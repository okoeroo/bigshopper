<?php

require_once 'globals.php';
require_once 'config.php';
require_once 'build.php';
require_once 'product.php';
require_once 'navigation.php';
require_once 'article.php';
require_once 'category.php';


/* Global initializers */
if (!initialize()) {
    http_response_code(500);
    return;
}

$head = new Head;
$head->display();


echo '<h2>Bestelling afronden</h2>';

/* Get all the selected products */
$token = session_get_cookie_value();
$cart = cart_get_session_products($token);
if (count($cart) == 0) {
    echo '<h3>Uw winkelmandje is leeg, niets te bestellen.</h3>';
} else {
    /* Begin of article */
    echo '      <div class="section">';
    echo '          <p>'."\n";


    /* Draw category header */
    echo '<form action="mail_send.php" method="POST" enctype="multipart/form-data">' . "\n";
    echo '<input type="hidden" name="id" id="id" value="'.$token.'">';

/*
    form_field_text('firstname',    'Voornaam',    '',                  24,     88,  '',        False, True, False); echo '</br>';
    form_field_text('lastname',     'Achternaam',  '',                  24,     88,  '',        False, True, False); echo '</br>';
    form_field_text('streetname',   'Straatnaam',  '',                  24,     160, '',        False, True, False); echo '</br>';
    form_field_text('house_number', 'Huisnummer en toevoeging',  '',    15,     15,  '',        False, True, False); echo '</br>';
    form_field_text('zipcode',      'Postcode',    '',                   6,     6,   '1234AB',  False, True, False); echo '</br>';
    form_field_text('stad',         'Woonplaats',  '',                  24,     100, '',        False, True, False); echo '</br>';
    form_field_email('email',       'Emailadres',  '',                  24,     150, '',        False, True, False); echo '</br>';
    */

    $list = array();
    $row  = array(); $row['value'] = 'sending';      $row['text'] = 'Versturen';     array_push($list, $row);
    $row  = array(); $row['value'] = 'pickup';       $row['text'] = 'Ophalen';       array_push($list, $row);

    form_field_radio('transport', NULL, $list, 'sending', False, False);
    echo '<br>';
    /* echo '<textarea name="description" style="width:250px;height:150px;"></textarea>'; */

/*
    ->         order_id            INT,
    ->         firstname           VARCHAR(88),
    ->         lastname            VARCHAR(88),
    ->         streetname          VARCHAR(160),
    ->         house_number        VARCHAR(15),
    ->         streetname_2        VARCHAR(160),
    ->         house_number_2      VARCHAR(15),
    ->         zipcode             VARCHAR(15),
    ->         city                VARCHAR(100),
    ->         country             VARCHAR(100),
    ->         email               VARCHAR(150),
    ->         transport           VARCHAR(50),
*/

    echo '<br>';
    echo '<input type="submit" name="submit" value="Bestelling plaatsen">'  . "\n";
    echo '</form>';


    echo '          </p>'."\n";
    echo '      </div>';
}


$tail = new Tail;
$tail->display();

?>
