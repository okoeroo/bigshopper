<?php

class Product {
    public $id;
    public $sku;
    public $name;
    public $description;
    public $price;
    public $clothing_size;
    public $dimensions;
    public $changed_on;
    public $images = array();

    function fillFromRow($row) {
        $this->id            = $row['id'];
        $this->sku           = $row['sku'];
        $this->name          = $row['name'];
        $this->description   = $row['description'];
        $this->price         = $row['price'];
        $this->clothing_size = $row['clothing_size'];
        $this->dimensions    = $row['dimensions'];
        $this->changed_on    = $row['changed_on'];

        if (isset($row['img'])) {
            $img                 = $row['img'];
            array_push($this->images, $img);
        } else {
            $this->load_associated_images();
        }
    }

    function fillFromPost() {
        if (isset($_POST["id"])) {
            $this->id               = trim($_POST["id"]);
            if (! filter_var($this->id, FILTER_SANITIZE_NUMBER_INT)) {
                throw new Exception('input check failure id');
            }
        }

        $this->sku              = trim($_POST["sku"]);
        if (! filter_var($this->sku, FILTER_SANITIZE_STRING)) {
            throw new Exception('input check failure sku');
        }
        $this->name             = trim($_POST["name"]);
        if (! filter_var($this->sku, FILTER_SANITIZE_STRING)) {
            throw new Exception('input check failure name');
        }
        $this->description      = trim($_POST["description"]);
        if (! empty($this->description)) {
            if (! filter_var($this->description, FILTER_SANITIZE_STRING)) {
                throw new Exception('input check failure description');
            }
        }
        $this->price            = trim($_POST["price"]);
        if (! filter_var($this->price, FILTER_SANITIZE_NUMBER_FLOAT)) {
            throw new Exception('input check failure price');
        }
        $this->clothing_size    = trim($_POST["clothing_size"]);
        if (! empty($this->clothing_size)) {
            if (! filter_var($this->clothing_size, FILTER_SANITIZE_STRING)) {
                throw new Exception('input check failure clothing_size');
            }
        }
        $this->dimensions       = trim($_POST["dimensions"]);
        if (! empty($this->dimensions)) {
            if (! filter_var($this->dimensions, FILTER_SANITIZE_STRING)) {
                throw new Exception('input check failure dimensions');
            }
        }

        if (isset($_FILES['image'])
                && isset($_FILES['image']['tmp_name'])
                && $_FILES['image']['size'] > 0) {

            $this->images = array();
            $file = $_FILES['image']['tmp_name'];
            $image_check = getimagesize($_FILES['image']['tmp_name']);
            if($image_check==false) {
                echo 'Not a Valid Image';
            } else {
                $image = file_get_contents($_FILES['image']['tmp_name']);
                array_push($this->images, $image);
            }
        } else {
            /* Try searching for existing pictures assosicated to this products */
            $this->load_associated_images();
        }
    }

    function load_associated_images() {
        $db = $GLOBALS['db'];

        $sql = 'SELECT img '.
               '  FROM product_images '.
               ' WHERE product_id = :product_id';
        $sth = $db->handle->prepare($sql);
        if (! $sth->execute(array(
                ':product_id'=>$this->id))) {
            return False;
        }
        $rs = $sth->fetchAll(PDO::FETCH_ASSOC); 
        foreach($rs as $row) {
            $img = $row['img'];
            array_push($this->images, $img);
        }
        return True;
    }

    function store() {
        $db = $GLOBALS['db'];
        $sql = 'INSERT INTO products' .
               '            (sku, name, description, price, '.
               '             clothing_size, dimensions) '.
               '     VALUES (:sku, :name, :description, :price, '.
               '             :clothing_size, :dimensions)';

        try {
            $db->handle->beginTransaction();
            $sth = $db->handle->prepare($sql);
            $sth->execute(array(
                ':sku'=>$this->sku,
                ':name'=>$this->name,
                ':description'=>$this->description,
                ':price'=>$this->price,
                ':clothing_size'=>$this->clothing_size,
                ':dimensions'=>$this->dimensions));

            $last_id = $db->handle->lastInsertId();


        } catch (Exception $e) {
            if ($db->debug === True) {
                var_dump($e);
            }
            $db->handle->rollback(); 
            return False;
        }


        /* Store images */
        foreach ($this->images as $value) {
            $sql = 'INSERT INTO product_images' .
                   '            (product_id, img) '.
                   '     VALUES (:product_id, :img)';

            try {
                $sth = $db->handle->prepare($sql);
                $sth->execute(array(
                    ':product_id'=>$last_id,
                    ':img'=>$value));

            } catch (Exception $e) {
                if ($db->debug === True) {
                    var_dump($e);
                }
                $db->handle->rollback(); 
                return False;
            }
        }


        /* Commit */
        $db->handle->commit();
        return True;
    }

    function update() {
        $db = $GLOBALS['db'];

        $sql = 'UPDATE products' .
               '   SET sku = :sku,'.
               '       name = :name,'.
               '       description = :description,'.
               '       price = :price,'.
               '       clothing_size = :clothing_size,'.
               '       dimensions = :dimensions '.
               ' WHERE id = :id';

        try {
            $sth = $db->handle->prepare($sql);
            $sth->execute(array(
                    ':id'=>$this->id,
                    ':sku'=>$this->sku,
                    ':name'=>$this->name,
                    ':description'=>$this->description,
                    ':price'=>$this->price,
                    ':clothing_size'=>$this->clothing_size,
                    ':dimensions'=>$this->dimensions));
        } catch (Exception $e) {
            if ($db->debug === True) {
                var_dump($e);
            }
            return False;
        }

        /* TODO Hack: remove all product_id images first, make it the first
         * image always */
        $sql = 'DELETE FROM product_images' .
               '      WHERE product_id = :product_id';
        try {
            $sth = $db->handle->prepare($sql);
            $sth->execute(array(
                ':product_id'=>$this->id));
        } catch (Exception $e) {
            if ($db->debug === True) {
                var_dump($e);
            }
            return False;
        }

        /* Store images */
        foreach ($this->images as $value) {
            $sql = 'INSERT INTO product_images' .
                   '            (product_id, img) '.
                   '     VALUES (:product_id, :img)';

            try {
                $sth = $db->handle->prepare($sql);
                $sth->execute(array(
                    ':product_id'=>$this->id,
                    ':img'=>$value));

            } catch (Exception $e) {
                if ($db->debug === True) {
                    var_dump($e);
                }
                return False;
            }
        }
        return True;
    }

    function delete() {
        $db = $GLOBALS['db'];

        $sql = 'DELETE FROM products' .
               '      WHERE id = :id';
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

        $sql = 'DELETE FROM product_images' .
               '      WHERE product_id = :id';
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

        $sql = 'DELETE FROM products_categories' .
               '      WHERE product_id = :id';
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

function product_delete_by_id($prod_id) {
    $db = $GLOBALS['db'];

    $sql = 'DELETE FROM products' .
           ' WHERE id = :id';

    try {
        $sth = $db->handle->prepare($sql);
        $sth->execute(array(
            ':id'=>$prod_id));

    } catch (Exception $e) {
        if ($db->debug === True) {
            var_dump($e);
        }
        return False;
    }

    return True;
}


function products_load() {
    $db = $GLOBALS['db'];
    $products = array();

    $sql = 'SELECT products.id, sku, name, description, '.
           '       price, clothing_size, dimensions, '.
           '       products.changed_on, img '.
           '  FROM products, product_images '.
           ' WHERE product_images.product_id = products.id';

    $sth = $db->handle->prepare($sql);
    if (! $sth->execute()) {
        return NULL;
    }
    $rs = $sth->fetchAll(PDO::FETCH_ASSOC); 

    foreach($rs as $row) {
        $prod = new Product();
        $prod->fillFromRow($row);

        array_push($products, $prod);
    }
    return $products;
}

function product_search_by_id($id) {
    $db = $GLOBALS['db'];

    $sql = 'SELECT products.id, sku, name, description, '.
           '       price, clothing_size, dimensions, '.
           '       products.changed_on, img '.
           '  FROM products, product_images '.
           ' WHERE product_images.product_id = products.id '.
           '   AND products.id = :product_id';

    $sth = $db->handle->prepare($sql);
    if (! $sth->execute(array(
        ':product_id'=>$id))) {
        return NULL;
    }
    $rs = $sth->fetchAll(PDO::FETCH_ASSOC); 

    foreach($rs as $row) {
        $prod = new Product();
        $prod->fillFromRow($row);
        return $prod;
    }
    return NULL;
}

function product_search_by_sku($sku) {
    $db = $GLOBALS['db'];

    $sql = 'SELECT products.id, sku, name, description, '.
           '       price, clothing_size, dimensions, '.
           '       products.changed_on, img '.
           '  FROM products, product_images '.
           ' WHERE product_images.product_id = products.id '.
           '   AND products.sku = :sku';

    $sth = $db->handle->prepare($sql);
    if (! $sth->execute(array(
        ':sku'=>$sku))) {
        return NULL;
    }
    $rs = $sth->fetchAll(PDO::FETCH_ASSOC); 

    foreach($rs as $row) {
        $prod = new Product();
        $prod->fillFromRow($row);
        return $prod;
    }
    return NULL;
}

function product_search_by_name($name) {
    $db = $GLOBALS['db'];

    $sql = 'SELECT products.id, sku, name, description, '.
           '       price, clothing_size, dimensions, '.
           '       products.changed_on, img '.
           '  FROM products, product_images '.
           ' WHERE product_images.product_id = products.id '.
           '   AND products.name = :name';

    $sth = $db->handle->prepare($sql);
    if (! $sth->execute(array(
        ':name'=>$name))) {
        return NULL;
    }
    $rs = $sth->fetchAll(PDO::FETCH_ASSOC); 

    foreach($rs as $row) {
        $prod = new Product();
        $prod->fillFromRow($row);
        return $prod;
    }
    return NULL;
}

function products_by_category_id($cat_id) {
    $db = $GLOBALS['db'];
    $products = array();

    $sql = 'SELECT products.id as id, products.name as name, products.description as description, '.
           '       products.price as price, products.clothing_size as clothing_size, '.
           '       products.dimensions as dimensions, products.sku as sku, '.
           '       products.changed_on as changed_on '.
           '  FROM products, products_categories '.
           ' WHERE products_categories.product_id = products.id '.
           '   AND products_categories.category_id = :category_id';

    $sth = $db->handle->prepare($sql);
    if (! $sth->execute(array(
            ':category_id'=>$cat_id))) {
        return NULL;
    }
    $rs = $sth->fetchAll(PDO::FETCH_ASSOC); 
    foreach($rs as $row) {
        $prod = new Product();
        $prod->fillFromRow($row);

        /* Load images, if available */
        $prod->load_associated_images();

        array_push($products, $prod);
    }

    return $products;




    $sql = 'SELECT products.id, products.sku, products.name, products.description, '.
           '       products.price, products.clothing_size, products.dimensions, '.
           '       products.changed_on, product_images.img '.
           '  FROM products, product_images, products_categories'.
           ' WHERE product_images.product_id = products.id'.
           '   AND products_categories.product_id = products.id'.
           '   AND products_categories.category_id = :category_id';

    $sth = $db->handle->prepare($sql);
    if (! $sth->execute(array(
            ':category_id'=>$cat_id))) {
        return NULL;
    }
    $rs = $sth->fetchAll(PDO::FETCH_ASSOC); 

    foreach($rs as $row) {
        $prod = new Product();
        $prod->fillFromRow($row);

        array_push($products, $prod);
    }
    return $products;
}


function sql_to_html_table($dbh, $sql) {
    $db = $GLOBALS['db'];
    $dbh = $db->handle;

    $sth = $dbh->prepare($sql);
    if (! $sth->execute()) {
        return NULL;
    }
    echo '<table border=1>';
    $rs = $sth->fetchAll(PDO::FETCH_ASSOC); 
    echo '<tr>';
    for ($i = 0; $i < $sth->columnCount(); $i++) {
        $meta = $sth->getColumnMeta($i);
        echo '<th>' . $meta['name'] . '</th>';
    }
    echo '</tr>';
    foreach($rs as $row) {
        echo '<tr>';
        foreach($row as $field) {
            echo '<td>' . $field . '</td>';
        }
        echo '</tr>';
    }
    echo '</table>';
}


?>
