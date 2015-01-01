<?php

require_once 'build.php';
require_once 'category.php';
require_once 'database.php';


/* Open DB */
$db = db_connect();
if ($db && $db->handle === NULL) {
    http_response_code(500);
    return;
}


function product_form_field_text($name, $text, $default_value, $max_chars, $placeholder, $autofocus, $required, $autocomplete) {
    echo '<li>';
    echo '<label for="'.$name.'">'.$text.':</label>';
    echo '<input type="text" name="'.$name.'" id="'.$name.'"';
    echo ' value="'.$default_value.'" maxlength="'.$max_chars.'" placeholder="'.$placeholder.'"';
    if ($autofocus === True) {
        echo ' autofocus';
    }
    if ($required === True) {
        echo ' required';
    }
    if ($autocomplete === True) {
        echo 'autocomplete="on"';
    }
    echo ' />';
    echo '</li>';
    echo "\n";
}

function product_form_field_file($name, $text, $autofocus, $required) {
    echo '<li>';
    echo '<label for="'.$name.'">'.$text.':</label>';
    echo '<input type="file" name="'.$name.'" id="'.$name.'"';
    if ($autofocus === True) {
        echo ' autofocus';
    }
    if ($required === True) {
        echo ' required';
    }
    echo ' />';
    echo '</li>';
    echo "\n";
}

function product_form_field_dropdown($name, $text, $list, $autofocus, $required) {
    echo '<li>';
    echo '<label for="'.$name.'">'.$text.':</label>';

    echo '<select name="'.$name.'" id="'.$name.'"';
    if ($autofocus === True) {
        echo ' autofocus';
    }
    if ($required === True) {
        echo ' required';
    }
    echo '>';
    foreach ($list as $row) {
        echo '<option value="'.$row['value'].'">'.$row['name'].'</option>';
    }
    echo '</select>';
    echo '</li>';
}

/*
function product_form_field_radio($name, $text, $max, $placeholder, $autofocus, $required, $autocomplete) {
                    <li>
                        <label for="faction">Speler of observator?</label>
                        <ul>
                            <li>
                                <div class="tip">
                                <label for="rd_speler">Paintball speler (12,50 euro)</label>
                                <input id="rd_speler" class="radio" type="radio"
                                    name="gotgame" value="speler"
                                    checked="checked"></input>
                                </div>
                            </li>
                            <li>
                                <div class="tip">
                                <label for="rd_nonspeler">Observator (5,- euro)</label>
                                <input id="rd_nonspeler" class="radio" type="radio"
                                    name="gotgame"
                                    value="observator"></input>
                                </div>
                            </li>
                        </ul>
                    </li>
*/

function categories_to_dropdown_list($db) {
    $categories = categories_load($db);
    $list = array();

    foreach ($categories as $cat) {
        $row['name'] = $cat->name;
        $row['value'] = $cat->id;
        array_push($list, $row);
    }
    return $list;
}

$head = new Head;
$head->display();


echo '    <form action="product_add.php" method="POST" enctype="multipart/form-data">' . "\n";
echo '    <div class=list>' . "\n";

product_form_field_text('sku',          'SKU',          '', 10,     'SKU nummer',               True, True, True);
product_form_field_text('name',         'Naam',         '', 100,    'Naam van artikel',         False, True, False);
product_form_field_text('description',  'Omschrijving', '', 1024,   'Omschrijving van artikel', False, False, False);
product_form_field_text('price',        'Prijs',        '', 13,     '0,00',                     False, True, False);
product_form_field_text('clothing_size','Kleding maat', '', 50,     '',                         False, False, False);
product_form_field_text('dimensions',   'HxBxD',        '', 50,     '10x15x2',                  False, False, False);
product_form_field_file('image',        'Foto',                                                 False, True);
$list = categories_to_dropdown_list($db);
product_form_field_dropdown('category', 'Category',     $list,                                  False, True);

echo '      <input type="submit" name="submit" value="Submit">'  . "\n";

echo '    </div>' . "\n";
echo '    </form>'  . "\n";

$tail = new Tail;
$tail->display();

?>
