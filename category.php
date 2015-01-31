<?php

require_once 'database.php';

class Category {
    public $id;
    public $name;
    public $description;
    public $priority;
    public $sticky;
    public $changed_on;

    function fillFromRow($row) {
        /* var_dump($row); */
        $this->id           = $row['id'];
        $this->name         = $row['name'];
        $this->description  = $row['description'];
        $this->priority     = $row['priority'];
        $this->sticky       = $row['sticky'];
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

        $this->priority         = intval(trim($_POST["priority"]));
        if (! empty($this->priority)) {
            if (! filter_var($this->priority, FILTER_SANITIZE_NUMBER_INT)) {
                throw new Exception('input check failure priority');
            }
        }

        /* TODO: hardwired to a non-sticky category */
        $this->sticky = 0;
    }

    function store() {
        $db = $GLOBALS['db'];

        /* TODO: hardwired to a non-sticky category */
        $sql = 'INSERT INTO categories' .
               '            (name,  description,  priority, sticky)'.
               '     VALUES (:name, :description, 0,        0)';

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

    function update() {
        $db = $GLOBALS['db'];

        $sql = 'UPDATE categories' .
               '   SET name = :name, description = :description, priority = :priority, sticky = :sticky'.
               ' WHERE id = :id';

        try {
            $sth = $db->handle->prepare($sql);
            $sth->execute(array(
                ':id'=>$this->id,
                ':name'=>$this->name,
                ':description'=>$this->description,
                ':priority'=>$this->priority,
                ':sticky'=>$this->sticky
                ));

        } catch (Exception $e) {
            if ($db->debug === True) {
                var_dump($e);
            }
            return False;
        }

        return True;
    }

    function delete() {
        $db = $GLOBALS['db'];

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

function category_delete_by_id($cat_id) {
    $db = $GLOBALS['db'];

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


function categories_load($sticky) {
    $db = $GLOBALS['db'];
    $categories = array();

    $sql = 'SELECT id, name, description, priority, sticky, changed_on '.
           '  FROM categories';

    if ($sticky === NULL) {
        $sql = $sql . ' ORDER BY priority';
        $sth = $db->handle->prepare($sql);
        if (! $sth->execute()) {
            return NULL;
        }
    } else {
        $sql = $sql . ' WHERE sticky = :sticky';
        $sql = $sql . ' ORDER BY priority';

        $sth = $db->handle->prepare($sql);
        if (! $sth->execute(array(
                ':sticky'=>$sticky))) {
            return NULL;
        }
    }

    /* Cast to native types */
    $rs = db_cast_query_results($sth);

    foreach($rs as $row) {
        $cat = new Category();
        $cat->fillFromRow($row);

        array_push($categories, $cat);
    }
    return $categories;
}

function category_search_by_id($id) {
    $db = $GLOBALS['db'];

    $sql = 'SELECT id, name, description, priority, sticky, changed_on '.
           '  FROM categories '.
           ' WHERE categories.id = :id';

    $sth = $db->handle->prepare($sql);
    if (! $sth->execute(array(
        ':id'=>$id))) {
        return NULL;
    }
    /* $rs = $sth->fetchAll(PDO::FETCH_ASSOC);  */
    $rs = db_cast_query_results($sth);

    foreach($rs as $row) {
        $cat = new Category();
        $cat->fillFromRow($row);
        return $cat;
    }
    return NULL;
}

function category_search_by_name($name) {
    $db = $GLOBALS['db'];

    $sql = 'SELECT id, name, description, priority, sticky, changed_on '.
           '  FROM categories '.
           ' WHERE categories.name = :name';

    $sth = $db->handle->prepare($sql);
    if (! $sth->execute(array(
        ':name'=>$name))) {
        return NULL;
    }
    /* $rs = $sth->fetchAll(PDO::FETCH_ASSOC);  */
    $rs = db_cast_query_results($sth);

    foreach($rs as $row) {
        $cat = new Category();
        $cat->fillFromRow($row);
        return $cat;
    }
    return NULL;
}


function product_to_category_count_products($cat_id) {
    $db = $GLOBALS['db'];

    $sql = 'SELECT COUNT(product_id) as count '.
           '  FROM products_categories'.
           ' WHERE category_id = :category_id';

    $sth = $db->handle->prepare($sql);
    if (! $sth->execute(array(
        ':category_id'=>$cat_id))) {
        return -1;
    }
    /* $rs = $sth->fetchAll(PDO::FETCH_ASSOC);  */
    $rs = db_cast_query_results($sth);

    foreach($rs as $row) {
        $cnt = intval($row['count']);
        return $cnt;
    }
    return -1;
}

function product_to_category_add_by_sku_id($prod_sku, $cat_id) {
    $db = $GLOBALS['db'];

    $prod = product_search_by_sku($prod_sku);

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

function product_to_category_edit_by_sku_id($prod_sku, $cat_id) {
    $db = $GLOBALS['db'];
    $prod = product_search_by_sku($prod_sku);

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


function product_to_category_add_by_name_name($prod_name, $cat_name) {
    $db = $GLOBALS['db'];
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

/* Deprecated, only in admin.php */
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
}

function product_image_to_url($img) {
    $site = new Site;

    $hash = hash('whirlpool', $img);
    $img_path = dirname($_SERVER["SCRIPT_FILENAME"]).'/'.$site->image_dir.'/'.$hash;
    if (! file_exists($img_path)) {
        $t = file_put_contents($site->image_dir.'/'.$hash, $img);
    }

    /* Return url, relative to main URL, in the images directory and the hash
     * of the image (which is uniquely stored. */
    $s = '/'.$site->image_dir.'/'.$hash;
    return $s;
}

function category_display_load($cat_id) {
    $db = $GLOBALS['db'];
    $cat = category_search_by_id($cat_id);

    /* Begin of article */
    echo '      <div class="section">';
    echo '          <p>'."\n";


    /* Draw category header */
    category_display_header($cat);

    /* Products in category display */
    $products = products_by_category_id($cat_id);
    if ($products === NULL or count($products) === 0) {
        echo '<h4>Geen producten in deze categorie</h4>';
        return;
    }

    echo "\n";
    echo '<table class="product_list">'."\n";
    $cnt = 0;
    foreach($products as $prod) {
        if ($cnt % 2 == 0 && $cnt > 0) {
            echo '<tr>'."\n";
        }

        echo '<td>'."\n";

        echo '<table class="product">';
            echo '<col width="300x" />';
            echo '<col width="250px" />';

            echo '<tr>';
                echo '<th rowspan="5">';
                if (count($prod->images) > 0) { 

                    /* Hardcode the first image to be displayed only */
                    $img_url = product_image_to_url($prod->images[0]);

                    echo '<a href="'. $img_url .'" data-lightbox="'.$prod->sku.':'.$prod->name.'" data-title="'.$prod->sku.': '.$prod->name.'">';
                    echo '<img class="product_img" class="html5lightbox" src="'. $img_url .'">';
                    echo '</a>';

                }
                echo '</th>';

                echo '<td>';
                    echo '<ul class="product_summary">';
                        echo '<form action="cart_add.php" method="POST" enctype="multipart/form-data">';
                        echo '<input type="hidden" name="id" value="'.$prod->id.'">';

                        echo '<li>';
                        echo '<b>Naam</b>: '.$prod->name;
                        echo '</li>';
                        echo '<li>';
                        echo '<b>Product nr</b>: '.$prod->sku;
                        echo '</li>';
                        echo '<li>';
                        echo '<b>Prijs</b>: '. number_format($prod->price, 2, ',', '.') .' euro';
                        echo '</li>';
                        echo '<li>';
                        if (trim($prod->clothing_size) !== '') {
                            $clothing_sizes = explode (";", $prod->clothing_size);
                            if (count($clothing_sizes) > 1) {
                                $list = array();
                                foreach ($clothing_sizes as $option) {
                                    $row = array();
                                    $row['name']  = $option;
                                    $row['value'] = $option;
                                    array_push($list, $row);
                                }
                                form_field_dropdown('clothing_size', '<b>Maat</b>', $list, '', False, True);
                            } else {
                                echo '<b>Maat</b>: '.$prod->clothing_size;
                                echo '<input type="hidden" name="clothing_size" value="'.$prod->clothing_size.'">';
                            }
                        }
                        echo '</li>';
                        echo '<li>';
                        if (trim($prod->dimensions) !== '') {
                            $dimensions = explode (";", $prod->dimensions);
                            if (count($dimensions) > 1) {
                                $list = array();
                                foreach ($dimensions as $option) {
                                    $row = array();
                                    $row['name']  = $option;
                                    $row['value'] = $option;
                                    array_push($list, $row);
                                }
                                form_field_dropdown('dimensions', '<b>Afmetingen</b>', $list, '', False, True);
                            } else {
                                echo '<b>Afmetingen</b>: '.$prod->dimensions;
                                echo '<input type="hidden" name="dimensions" value="'.$prod->dimensions.'">';
                            }
                        }
                        echo '</li>';
                        echo '<li>';
                        echo '<b>Omschrijving</b>: <br>'.$prod->description;
                        echo '</li>';
                        echo '<li>';
                        echo '<input type="submit" name="submit" value="Toevoegen aan Winkelmandje">';
                        echo '</li>';
                        echo '</form>';
                    echo '</ul>';
                echo '</td>';
/*
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
*/
        echo '</table>'."\n";

        echo '</td>'."\n";

        $cnt++;
    }
    echo '</table>'."\n";

    /* End of article */
    echo '          </p>'."\n";
    echo '      </div>' . "\n";
}


?>
