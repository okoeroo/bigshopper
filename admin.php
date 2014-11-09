<?php

require 'build.php';


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


/*
function product_form_field_dropdown($name, $text, $max, $placeholder, $autofocus, $required, $autocomplete) {
                    <li>
                        <label for="faction">Faction:</label>
                        <select class="formelement" id="faction" name="faction">
                            <option value="hacker">Hackers</option>
                            <option value="infosec">Infosec professionals</option>
                            <option value="anon">Anonymous</option>
                            <option value="gov">Government agents</option>
                        </select>
                    </li>
}

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

echo '      <input type="submit" name="submit" value="Submit">'  . "\n";

echo '    </div>' . "\n";
echo '    </form>'  . "\n";

$tail = new Tail;
$tail->display();

?>
