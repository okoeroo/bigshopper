<?php

class Category {
    public $id;
    public $name;
    public $description;
    public $changed_on;

    function fillFromRow($row) {
        $this->id           = $row['id'];
        $this->name         = $row['name'];
        $this->description  = $row['description'];
        $this->changed_on   = $row['changed_on'];
    }
}


function categories_load($db) {
    $categories = array();

    $sql = 'SELECT id, name, description, changed_on '.
           '  FROM categories';

    $sth = $db->handle->prepare($sql);
    if (! $sth->execute()) {
        return NULL;
    }
    $rs = $sth->fetchAll(PDO::FETCH_ASSOC); 

    foreach($rs as $row) {
        $cat = new Category();
        $cat->fillFromRow($row);

        array_push($categories, $cat);
    }
    return $categories;
}

function category_search_by_id($db, $id) {
    $sql = 'SELECT id, name, description, changed_on '.
           '  FROM categories '.
           ' WHERE categories.id = :id';

    $sth = $db->handle->prepare($sql);
    if (! $sth->execute(array(
        ':id'=>$id))) {
        return NULL;
    }
    $rs = $sth->fetchAll(PDO::FETCH_ASSOC); 

    foreach($rs as $row) {
        $cat = new Category();
        $cat->fillFromRow($row);
        return $cat;
    }
    return NULL;
}

function category_search_by_name($db, $name) {
    $sql = 'SELECT id, name, description, changed_on '.
           '  FROM categories '.
           ' WHERE categories.name = :name';

    $sth = $db->handle->prepare($sql);
    if (! $sth->execute(array(
        ':name'=>$name))) {
        return NULL;
    }
    $rs = $sth->fetchAll(PDO::FETCH_ASSOC); 

    foreach($rs as $row) {
        $cat = new Category();
        $cat->fillFromRow($row);
        return $cat;
    }
    return NULL;
}

function product_to_category_add_by_name_name($db, $prod_name, $cat_name) {
    $prod = product_search_by_name($prod_name);
    $cat  = category_search_by_name($cat_name);

    if ($prod === NULL or $cat === NULL) {
        return False;
    }

    $sql = 'INSERT INTO products_categories '.
           '            (product_id, category_id) '.
           '     VALUES (:product_id, :category_id)';

    try {
        $sth = $db->handle->prepare($sql);
        $sth->execute(array(
            ':product_id'=>$prod->id,
            ':category_id'=>$cat->id));

    } catch (Exception $e) {
        if ($db->debug === True) {
            var_dump($e);
        }
        return False;
    }

    return True;
}

function category_display_header($cat) {
    echo '<h2>'.$cat->name.'</h2>'."\n";

    if ($cat->description !== NULL && strlen($cat->description) > 0) {
        echo '<h3>'.$cat->description.'</h3>';
    }
}

function category_display_load($db, $cat_id) {
    $cat = category_search_by_id($db, $cat_id);

    /* Begin of article */
    echo '      <div class="section">';
    echo '          <p>'."\n";


    /* Draw category header */
    category_display_header($cat);

    /* Products in category display */
    $products = products_by_category_id($db, $cat_id);
    if ($products === NULL or count($products) === 0) {
        echo '<h4>Geen producten in deze categorie</h4>';
        return;
    }


    echo '<table border=1 width="400px">';
    foreach($products as $prod) {

        echo '<tr>';
            echo '<th rowspan="4">';
            echo 'picture';
            echo '</th>';

            echo '<td>';
            echo $prod->name;
            echo '</td>';
        echo '</tr>';
        echo '<tr>';
            echo '<td>';
            echo $prod->sku;
            echo '</td>';
        echo '</tr>';
        echo '<tr>';
            echo '<td>';
            echo $prod->price;
            echo '</td>';
        echo '</tr>';
        echo '<tr>';
            echo '<td>';
            echo $prod->description;
            echo '</td>';
        echo '</tr>';
    }
    echo '</table>';

    /* End of article */
    echo '          </p>'."\n";
    echo '      </div>' . "\n";
}

?>
