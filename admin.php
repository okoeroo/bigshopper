<?php

require_once 'globals.php';
require_once 'build.php';
require_once 'category.php';
require_once 'product.php';

/* Slotje */
if ($_SERVER["REMOTE_ADDR"] === '::ffff:195.241.201.236') {
    echo 'Authorized';
} else {
    echo '<!DOCTYPE HTML>'."\n";
    echo '<html lang="en-US"><head><meta charset="UTF-8">'."\n";
    echo '<meta http-equiv="refresh" content="1;url=/">';
    echo '</head></html>';
    return;
}

/* Global initializers */
if (!initialize()) {
    http_response_code(500);
    return;
}



function categories_to_dropdown_list() {
    $categories = categories_load(NULL);
    $list = array();

    foreach ($categories as $cat) {
        $row['name'] = $cat->name;
        $row['value'] = $cat->id;
        array_push($list, $row);
    }
    return $list;
}


function category_display_edit($cat) {
    /* Begin of article */
    echo '      <div class="section">';
    echo '          <p>'."\n";


    /* Draw category header */
    /* category_display_header($cat); */
    echo '<form action="category_edit.php" method="POST" enctype="multipart/form-data">' . "\n";
    echo '<input type="hidden" name="id" id="id" value="'.$cat->id.'">';

    echo '<h2>'; form_field_text('name',        'Categorie naam',           $cat->name,         42,     60, 'Categorie naam', False, True, False); echo '</h2>';
    echo '<h3>'; form_field_text('description', 'Categorie omschrijving',   $cat->description,  42,     200, 'Categorie omschrijving', False, False, False);  echo '</h3>';
    echo '<h3>'; form_field_text('priority',    'Prioriteit',               $cat->priority,     3,      3, 'Prioriteit', False, False, False);  echo '</h3>';
    echo '<input type="submit" name="submit" value="Update">'  . "\n";
    echo '</form>';

    /* Don't offer delete button */
    if (product_to_category_count_products($cat->id) > 0) {
        echo '<h5><i>Verwijder blokkade: Categorie is nog niet leeg</i></h5>';
    } else {
        echo '<form action="category_del.php" method="POST" enctype="multipart/form-data">' . "\n";
        echo '<input type="hidden" name="id" id="id" value="'.$cat->id.'">';
        echo '<input type="submit" name="'.$cat->id.'" value="Delete">'  . "\n"; echo '<br>';
        echo '</form>'  . "\n";
    }



    /* Products in category display */
    $products = products_by_category_id($cat->id);
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

                form_field_text('sku',          'SKU',              $prod->sku,             15,     15,     'SKU nummer',               True, True, True); echo '<br>';
                form_field_text('name',         'Naam',             $prod->name,            42,     50,     'Naam van artikel',         False, True, False); echo '<br>';
                form_field_text('description',  'Omschrijving',     $prod->description,     42,     180,    'Omschrijving van artikel', False, False, False); echo '<br>';
                form_field_text('price',        'Prijs',            $prod->price,           13,     13,     '0,00',                     False, True, False); echo '<br>';
                form_field_text('clothing_size','Kleding maat (*)', $prod->clothing_size,   42,     50,     '',                         False, False, False); echo '<br>';
                form_field_text('dimensions',   'HxBxD (*)',        $prod->dimensions,      42,     50,     '10x15x2',                  False, False, False); echo '<br>';
                form_field_file('image',        'Foto',                                                                         False, False); echo '<br>';
                $list = categories_to_dropdown_list();
                form_field_dropdown('category', 'Category',         $list,                  $cat->id,                           False, True); echo '<br>';

                echo '<br>';
                echo '<input type="submit" name="'.$prod->id.'" value="Update">'  . "\n"; echo '<br>';
                echo '</div>' . "\n";
                echo '</form>'  . "\n";
                echo '<br>';
                echo '<form action="product_del.php" method="POST" enctype="multipart/form-data">' . "\n";
                echo '<input type="hidden" name="id" id="id" value="'.$prod->id.'">';
                echo '<input type="submit" name="'.$prod->id.'" value="Delete">'  . "\n"; echo '<br>';
                echo '</form>'  . "\n";

                echo '<br>*: Gebruik een ; (punt-komma teken) als scheiding om meerdere maten of afmetingen aan te geven.';
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

echo '<li>'; form_field_text('sku',          'SKU',              '',    15,     15,     'SKU nummer',               True, True, True); echo '</li>';
echo '<li>'; form_field_text('name',         'Naam',             '',    42,     50,     'Naam van artikel',          False, True, False); echo '</li>';
echo '<li>'; form_field_text('description',  'Omschrijving',     '',    42,     180,    'Omschrijving van artikel',  False, False, False); echo '</li>';
echo '<li>'; form_field_text('price',        'Prijs',            '',    13,     13,     '0,00',                     False, True, False); echo '</li>';
echo '<li>'; form_field_text('clothing_size','Kleding maat (*)', '',    42,     50,     '92;105;120',               False, False, False); echo '</li>';
echo '<li>'; form_field_text('dimensions',   'HxBxD (*)',        '',    42,     50,     '10x15x2;10x30x4',          False, False, False); echo '</li>';
echo '<li>'; form_field_file('image',        'Foto',                                                     False, True); echo '</li>';
$list = categories_to_dropdown_list();
echo '<li>';form_field_dropdown('category', 'Category',          $list,       NULL,                      False, True); echo '</li>';

echo '      <input type="submit" name="submit" value="Toevoegen">'  . "\n";

echo '    </div>' . "\n";
echo '    </form>'  . "\n";
echo '<br>*: Gebruik een ; (punt-komma teken) als scheiding om meerdere maten of afmetingen aan te geven.';


echo '<hr>';


echo '<h2>Nieuwe categorie toevoegen</h2>';

echo '    <form action="category_add.php" method="POST" enctype="multipart/form-data">' . "\n";
echo '    <div class=list>' . "\n";

echo '<li>'; form_field_text('name',         'Naam',         '',    42,     50,    'Naam van category',         False, True,  False); echo '</li>';
echo '<li>'; form_field_text('description',  'Omschrijving', '',    42,     180,   'Omschrijving van category', False, False, False); echo '</li>';
echo '<li>'; form_field_text('priority',     'Prioriteit',   '0',   3,      3,     'Prioriteit',                False, False, False); echo '</li>';

echo '      <input type="submit" name="submit" value="Toevoegen">'  . "\n";

echo '    </div>' . "\n";
echo '    </form>'  . "\n";


echo '<hr>';

$categories = categories_load(NULL);
foreach ($categories as $cat) {
    category_display_edit($cat);
}

$tail = new Tail;
$tail->display();

?>
