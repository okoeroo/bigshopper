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

        $img                 = $row['img'];
        array_push($this->images, $img);
    }

    function fillFromPost() {
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

        $this->images = array();
        $file = $_FILES['image']['tmp_name'];
        $image_check = getimagesize($_FILES['image']['tmp_name']);
        if($image_check==false) {
            echo 'Not a Valid Image';
        } else {
            $image = file_get_contents($_FILES['image']['tmp_name']);
            array_push($this->images, $image);
        }
    }

    function store($db) {
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

        $db->handle->commit();
        return True;
    }
}

function products_load($db) {
    $sql = 'SELECT products.id, sku, name, description, price, clothing_size, dimensions, products.changed_on, img FROM products, product_images WHERE product_images.product_id = products.id';
    $products = array();

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

function sql_to_html_table($dbh, $sql) {
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
