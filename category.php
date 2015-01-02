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

    function fillFromPost() {
        if (isset($_POST["id"])) {
            $this->id               = trim($_POST["id"]);
            if (! filter_var($this->id, FILTER_SANITIZE_NUMBER_INT)) {
                throw new Exception('input check failure id');
            }
        }
        $this->name             = trim($_POST["name"]);
        if (! filter_var($this->name, FILTER_SANITIZE_STRING)) {
            throw new Exception('input check failure name');
        }
        $this->description      = trim($_POST["description"]);
        if (! empty($this->description)) {
            if (! filter_var($this->description, FILTER_SANITIZE_STRING)) {
                throw new Exception('input check failure description');
            }
        }
    }

    function store($db) {
        $sql = 'INSERT INTO categories' .
               '            (name, description)'.
               '     VALUES (:name, :description)';

        try {
            $sth = $db->handle->prepare($sql);
            $sth->execute(array(
                ':name'=>$this->name,
                ':description'=>$this->description));

        } catch (Exception $e) {
            if ($db->debug === True) {
                var_dump($e);
            }
            return False;
        }

        return True;
    }

    function update($db) {
        $sql = 'UPDATE categories' .
               '   SET name = :name, description = :description'.
               ' WHERE id = :id';

        try {
            $sth = $db->handle->prepare($sql);
            $sth->execute(array(
                ':id'=>$this->id,
                ':name'=>$this->name,
                ':description'=>$this->description));

        } catch (Exception $e) {
            if ($db->debug === True) {
                var_dump($e);
            }
            return False;
        }

        return True;
    }

    function delete($db) {
        /* Check if category is empty */
        if (product_to_category_count_products($db, $this->id) > 0) {
            return False;
        }

        /* Test concluded safe to remove */
        $sql = 'DELETE FROM categories' .
               ' WHERE id = :id';

        try {
            $sth = $db->handle->prepare($sql);
            $sth->execute(array(
                ':id'=>$this->id));

        } catch (Exception $e) {
            if ($db->debug === True) {
                var_dump($e);
            }
            return False;
        }

        return True;
    }
}

function category_delete_by_id($db, $cat_id) {
    /* Check if category is empty */
    if (product_to_category_count_products($db, $cat_id) > 0) {
        return False;
    }

    /* Test concluded safe to remove */
    $sql = 'DELETE FROM categories' .
           ' WHERE id = :id';

    try {
        $sth = $db->handle->prepare($sql);
        $sth->execute(array(
            ':id'=>$cat_id));

    } catch (Exception $e) {
        if ($db->debug === True) {
            var_dump($e);
        }
        return False;
    }

    return True;
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


function product_to_category_count_products($db, $cat_id) {
    $sql = 'SELECT COUNT(product_id) as count '.
           '  FROM products_categories'.
           ' WHERE category_id = :category_id';

    $sth = $db->handle->prepare($sql);
    if (! $sth->execute(array(
        ':category_id'=>$cat_id))) {
        return -1;
    }
    $rs = $sth->fetchAll(PDO::FETCH_ASSOC); 

    foreach($rs as $row) {
        $cnt = intval($row['count']);
        return $cnt;
    }
    return -1;
}

function product_to_category_add_by_sku_id($db, $prod_sku, $cat_id) {
    $prod = product_search_by_sku($db, $prod_sku);

    if ($prod === NULL) {
        return False;
    }

    $sql = 'INSERT INTO products_categories '.
           '            (product_id, category_id) '.
           '     VALUES (:product_id, :category_id)';

    try {
        $sth = $db->handle->prepare($sql);
        $sth->execute(array(
            ':product_id'=>$prod->id,
            ':category_id'=>$cat_id));

    } catch (Exception $e) {
        if ($db->debug === True) {
            var_dump($e);
        }
        return False;
    }

    return True;
}

function product_to_category_edit_by_sku_id($db, $prod_sku, $cat_id) {
    $prod = product_search_by_sku($db, $prod_sku);

    if ($prod === NULL) {
        return False;
    }

    $sql = 'UPDATE products_categories '.
           '   SET category_id = :category_id '.
           ' WHERE product_id = :product_id';

    try {
        $sth = $db->handle->prepare($sql);
        $sth->execute(array(
            ':product_id'=>$prod->id,
            ':category_id'=>$cat_id));

    } catch (Exception $e) {
        if ($db->debug === True) {
            var_dump($e);
        }
        return False;
    }

    return True;
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

function display_image_data($img) {
    $site = new Site;

    $hash = hash('whirlpool', $img);
    $img_path = dirname($_SERVER["SCRIPT_FILENAME"]).'/'.$site->image_dir.'/'.$hash;
    /* $img_path = $site->image_dir.'/'.$hash; */
    if (! file_exists($img_path)) {
        $t = file_put_contents($site->image_dir.'/'.$hash, $img);

    }

    $s = '<img width="300px" src="'.'/'.$site->image_dir.'/'.$hash.'">';
    return $s;
    return $hash;
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

    echo "\n";
    echo '<table>'."\n";
    $cnt = 0;
    foreach($products as $prod) {
        if ($cnt % 2 == 0 && $cnt > 0) {
            echo '<tr>'."\n";
        }

        echo '<td>'."\n";

        echo '<table class="article" border=1">';
            echo '<col width="300x" />';
            echo '<col width="250px" />';

            echo '<tr>';
                echo '<th rowspan="5">';
                if (count($prod->images) > 0) { 

                    /* Hardcode the first image to be displayed only */
                    $img_html = display_image_data($prod->images[0]);
                    echo $img_html;
                }
                echo '</th>';

                echo '<td>';
                echo $prod->name;
                echo '</td>';
            echo '</tr>';
            echo '<tr>';
                echo '<td>';
                echo "Product nr: ";
                echo $prod->sku;
                echo '</td>';
            echo '</tr>';
            echo '<tr>';
                echo '<td>';
                echo 'Prijs: ';
                echo $prod->price;
                echo ' euro';
                echo '</td>';
            echo '</tr>';
            echo '<tr>';
                echo '<td>';
                echo $prod->description;
                echo '</td>';
            echo '</tr>';
            echo '<tr>';
                echo '<td>';
                echo 'Voeg toe aan winkelwagentje';
                echo '</td>';
            echo '</tr>';
        echo '</table>'."\n";
        echo '</form>' . "\n";

        echo '</td>'."\n";

        $cnt++;
    }
    echo '</table>'."\n";

    /* End of article */
    echo '          </p>'."\n";
    echo '      </div>' . "\n";
}


?>
