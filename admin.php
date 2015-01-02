<?php

require_once 'build.php';
require_once 'database.php';
require_once 'category.php';
require_once 'product.php';


/* Open DB */
$db = db_connect();
if ($db && $db->handle === NULL) {
    http_response_code(500);
    return;
}


function product_form_field_text($name, $text, $default_value, $max_chars, $placeholder, $autofocus, $required, $autocomplete) {
    echo '<label for="'.$name.'">'.$text.':</label>';
    echo '<input type="text" name="'.$name.'" id="'.$name.'"';
    echo ' value="'.$default_value.'" size="'.$max_chars.'" maxlength="'.$max_chars.'" placeholder="'.$placeholder.'"';
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
    echo "\n";
}

function product_form_field_file($name, $text, $autofocus, $required) {
    echo '<label for="'.$name.'">'.$text.':</label>';
    echo '<input type="file" name="'.$name.'" id="'.$name.'"';
    if ($autofocus === True) {
        echo ' autofocus';
    }
    if ($required === True) {
        echo ' required';
    }
    echo ' />';
    echo "\n";
}

function product_form_field_dropdown($name, $text, $list, $autofocus, $required) {
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
}

/*
function product_form_field_radio($name, $text, $max, $placeholder, $autofocus, $required, $autocomplete) {
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


function category_display_edit($db, $cat) {
    /* Begin of article */
    echo '      <div class="section">';
    echo '          <p>'."\n";


    /* Draw category header */
    /* category_display_header($cat); */
    echo '<form action="category_edit.php" method="POST" enctype="multipart/form-data">' . "\n";
    echo '<input type="hidden" name="id" id="id" value="'.$cat->id.'">';

    echo '<h2>'; product_form_field_text('name', 'Categorie naam', $cat->name, 60, 'Categorie naam', False, True, False); echo '</h2>';
    echo '<h3>'; product_form_field_text('description', 'Categorie omschrijving', $cat->description, 200, 'Categorie omschrijving', False, False, False);  echo '</h3>';
    echo '<input type="submit" name="submit" value="Update">'  . "\n";
    echo '</form>';

    /* Don't offer delete button */
    if (product_to_category_count_products($db, $cat->id) > 0) {
        echo '<h5><i>Verwijder blokkade: Categorie is nog niet leeg</i></h5>';
    } else {
        echo '<form action="category_del.php" method="POST" enctype="multipart/form-data">' . "\n";
        echo '<input type="hidden" name="id" id="id" value="'.$cat->id.'">';
        echo '<input type="submit" name="'.$cat->id.'" value="Delete">'  . "\n"; echo '<br>';
        echo '</form>'  . "\n";
    }



    /* Products in category display */
    $products = products_by_category_id($db, $cat->id);
    if ($products === NULL or count($products) === 0) {
        echo '<h4>Geen producten in deze categorie</h4>';
        return;
    }

    echo "\n";
    echo '<table>'."\n";

    foreach($products as $prod) {
        echo '<td>'."\n";

        echo '<form action="product_edit.php" method="POST" enctype="multipart/form-data">' . "\n";
        echo '<table class="article" border=1">';
            echo '<col width="300x" />';
            echo '<col width="450px" />';

            echo '<tr>';
                echo '<td>';
                if (count($prod->images) > 0) { 

                    /* Hardcode the first image to be displayed only */
                    $img_html = display_image_data($prod->images[0]);
                    echo $img_html;
                }
                echo '</td>';
                echo '<td>';

                echo '<form action="product_edit.php" method="POST" enctype="multipart/form-data">' . "\n";
                echo '<div class=list>' . "\n";
                echo '<input type="hidden" name="id" id="id" value="'.$prod->id.'">';

                product_form_field_text('sku',          'SKU',          $prod->sku,             15,     'SKU nummer',               True, True, True); echo '<br>';
                product_form_field_text('name',         'Naam',         $prod->name,            50,     'Naam van artikel',         False, True, False); echo '<br>';
                product_form_field_text('description',  'Omschrijving', $prod->description,    180,     'Omschrijving van artikel', False, False, False); echo '<br>';
                product_form_field_text('price',        'Prijs',        $prod->price,           13,     '0,00',                     False, True, False); echo '<br>';
                product_form_field_text('clothing_size','Kleding maat', $prod->clothing_size,   50,     '',                         False, False, False); echo '<br>';
                product_form_field_text('dimensions',   'HxBxD',        $prod->dimensions,      50,     '10x15x2',                  False, False, False); echo '<br>';
                product_form_field_file('image',        'Foto',                                                                     False, False); echo '<br>';
                $list = categories_to_dropdown_list($db);
                product_form_field_dropdown('category', 'Category',     $list,                                  False, True); echo '<br>';

                echo '<input type="submit" name="'.$prod->id.'" value="Update">'  . "\n"; echo '<br>';
                echo '</div>' . "\n";
                echo '</form>'  . "\n";

                echo '<form action="product_del.php" method="POST" enctype="multipart/form-data">' . "\n";
                echo '<input type="hidden" name="id" id="id" value="'.$prod->id.'">';
                echo '<input type="submit" name="'.$prod->id.'" value="Delete">'  . "\n"; echo '<br>';
                echo '</form>'  . "\n";

                echo '</td>';
            echo '</tr>';
        echo '</table>'."\n";
        echo '</form>' . "\n";

        echo '</td>'."\n";

        echo '<tr>'."\n";
    }
    echo '</table>'."\n";

    /* End of article */
    echo '          </p>'."\n";
    echo '<hr>'. "\n";
    echo '      </div>' . "\n";

}


$head = new Head;
$head->display();

echo '<h2>Nieuw artikel toevoegen</h2>';

echo '    <form action="product_add.php" method="POST" enctype="multipart/form-data">' . "\n";
echo '    <div class=list>' . "\n";

echo '<li>'; product_form_field_text('sku',          'SKU',          '', 15,     'SKU nummer',               True, True, True); echo '</li>';
echo '<li>'; product_form_field_text('name',         'Naam',         '', 50,    'Naam van artikel',         False, True, False); echo '</li>';
echo '<li>'; product_form_field_text('description',  'Omschrijving', '', 180,   'Omschrijving van artikel', False, False, False); echo '</li>';
echo '<li>'; product_form_field_text('price',        'Prijs',        '', 13,     '0,00',                     False, True, False); echo '</li>';
echo '<li>'; product_form_field_text('clothing_size','Kleding maat', '', 50,     '',                         False, False, False); echo '</li>';
echo '<li>'; product_form_field_text('dimensions',   'HxBxD',        '', 50,     '10x15x2',                  False, False, False); echo '</li>';
echo '<li>'; product_form_field_file('image',        'Foto',                                                 False, True); echo '</li>';
$list = categories_to_dropdown_list($db);
echo '<li>';product_form_field_dropdown('category', 'Category',     $list,                                  False, True); echo '</li>';

echo '      <input type="submit" name="submit" value="Toevoegen">'  . "\n";

echo '    </div>' . "\n";
echo '    </form>'  . "\n";


echo '<hr>';


$categories = categories_load($db);
foreach ($categories as $cat) {
    category_display_edit($db, $cat);
}

$tail = new Tail;
$tail->display();

?>
