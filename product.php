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

    /* function __construct() { */
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

    function fillFromRow($row) {
        $this->id               = $row['id'];
        $this->sku              = $row['sku'];
        $this->name             = $row['name'];
        $this->description      = $row['description'];
        $this->price            = $row['price'];
        $this->clothing_size    = $row['clothing_size'];
        $this->dimensions       = $row['dimensions'];
        $this->changed_on       = $row['changed_on'];
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

                $db->handle->commit();
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

?>
