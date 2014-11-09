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
    public $images;

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

        /* Main image */
        $this->images            = new array();
        if (! empty($_POST["image"])) {
            array_push($this->images, $_POST["image"]);
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
            $sth = $db->handle->prepare($sql);
            $sth->execute(array(
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
        return True;
    }
}

?>
