<?php

class Customer {
    public $id      ;
    public $firstname     ;
    public $lastname      ;
    public $streetname    ;
    public $house_number  ;
    public $streetname_2  ;
    public $house_number_2;
    public $zipcode       ;
    public $city          ;
    public $country       ;
    public $email         ;
    public $transport     ;

    function fillFromRow($row) {
        $this->id                 = $row['id'];
        $this->firstname          = $row['firstname'];
        $this->lastname           = $row['lastname'];
        $this->streetname         = $row['streetname'];
        $this->house_number       = $row['house_number'];
        $this->streetname_2       = $row['streetname_2'];
        $this->house_number_2     = $row['house_number_2'];
        $this->zipcode            = $row['zipcode'];
        $this->city               = $row['city'];
        $this->country            = $row['country'];
        $this->email              = $row['email'];
        $this->transport          = $row['transport'];
    }

    function fillFromPost() {
        if (isset($_POST["id"])) {
            $this->id               = trim($_POST["id"]);
            if (! filter_var($this->id, FILTER_SANITIZE_NUMBER_INT)) {
                throw new Exception('input check failure id');
            }
        }

        if (isset($_POST["firstname"])) {
            $this->firstname               = trim($_POST["firstname"]);
            if (! filter_var($this->firstname, FILTER_SANITIZE_STRING)) {
                throw new Exception('yo input check failure firstname');
            }
        }
        if (isset($_POST["lastname"])) {
            $this->lastname               = trim($_POST["lastname"]);
            if (! filter_var($this->lastname, FILTER_SANITIZE_STRING)) {
                throw new Exception('input check failure lastname');
            }
        }
        if (isset($_POST["streetname"])) {
            $this->streetname               = trim($_POST["streetname"]);
            if (! filter_var($this->streetname, FILTER_SANITIZE_STRING)) {
                throw new Exception('input check failure streetname');
            }
        }
        if (isset($_POST["house_number"])) {
            $this->house_number               = trim($_POST["house_number"]);
            if (! filter_var($this->house_number, FILTER_SANITIZE_STRING)) {
                throw new Exception('input check failure house_number');
            }
        }
        if (isset($_POST["streetname_2"])) {
            $this->streetname_2               = trim($_POST["streetname_2"]);
            if (! filter_var($this->streetname_2, FILTER_SANITIZE_STRING)) {
                throw new Exception('input check failure streetname_2');
            }
        }
        if (isset($_POST["house_number__2"])) {
            $this->house_number__2               = trim($_POST["house_number__2"]);
            if (! filter_var($this->house_number__2, FILTER_SANITIZE_STRING)) {
                throw new Exception('input check failure house_number__2');
            }
        }
        if (isset($_POST["zipcode"])) {
            $this->zipcode               = trim($_POST["zipcode"]);
            if (! filter_var($this->zipcode, FILTER_SANITIZE_STRING)) {
                throw new Exception('input check failure zipcode');
            }
        }
        if (isset($_POST["city"])) {
            $this->city               = trim($_POST["city"]);
            if (! filter_var($this->city, FILTER_SANITIZE_STRING)) {
                throw new Exception('input check failure city');
            }
        }
        if (isset($_POST["country"])) {
            $this->country               = trim($_POST["country"]);
            if (! filter_var($this->country, FILTER_SANITIZE_STRING)) {
                throw new Exception('input check failure country');
            }
        }
        if (isset($_POST["email"])) {
            $this->email               = trim($_POST["email"]);
            if (! filter_var($this->email, FILTER_SANITIZE_STRING)) {
                throw new Exception('input check failure email');
            }
        }

        if (isset($_POST["transport"])) {
            if (trim($_POST["transport"]) !== 'sending' and 
                trim($_POST["transport"]) !== 'pickup') {
                throw new Exception('input check failure transport');
            }
        }
    }

    function store() {
        $db = $GLOBALS['db'];
        $sql = 'INSERT INTO customers' .
               '            (firstname, lastname, streetname, house_number, '.
               '             streetname_2, house_number_2, zipcode, city, '.
               '             country, email, transport)'.
               '     VALUES (:firstname, :lastname, :streetname, :house_number, '.
               '             :streetname_2, :house_number_2, :zipcode, :city, '.
               '             :country, :email, :transport)';

        try {
            $sth = $db->handle->prepare($sql);
            $sth->execute(array(
                ':firstname'=>$this->firstname,
                ':lastname'=>$this->lastname,
                ':streetname'=>$this->streetname,
                ':house_number'=>$this->house_number,
                ':streetname_2'=>$this->streetname_2,
                ':house_number_2'=>$this->house_number_2,
                ':zipcode'=>$this->zipcode,
                ':city'=>$this->city,
                ':country'=>$this->country,
                ':email'=>$this->email,
                ':transport'=>$this->transport));

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

        $sql = 'UPDATE customers' .
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
                    ':firstname'=>$this->firstname,
                    ':lastname'=>$this->lastname,
                    ':streetname'=>$this->streetname,
                    ':house_number'=>$this->house_number,
                    ':streetname_2'=>$this->streetname_2,
                    ':house_number_2'=>$this->house_number_2,
                    ':zipcode'=>$this->zipcode,
                    ':city'=>$this->city,
                    ':country'=>$this->country,
                    ':email'=>$this->email,
                    ':transport'=>$this->transport));

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
    /* $rs = $sth->fetchAll(PDO::FETCH_ASSOC);  */
    $rs = db_cast_query_results($sth);

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
    /* $rs = $sth->fetchAll(PDO::FETCH_ASSOC);  */
    $rs = db_cast_query_results($sth);

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
    /* $rs = $sth->fetchAll(PDO::FETCH_ASSOC);  */
    $rs = db_cast_query_results($sth);

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
    /* $rs = $sth->fetchAll(PDO::FETCH_ASSOC);  */
    $rs = db_cast_query_results($sth);

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
    /* $rs = $sth->fetchAll(PDO::FETCH_ASSOC);  */
    $rs = db_cast_query_results($sth);
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
    /* $rs = $sth->fetchAll(PDO::FETCH_ASSOC);  */
    $rs = db_cast_query_results($sth);

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
    /* $rs = $sth->fetchAll(PDO::FETCH_ASSOC);  */
    $rs = db_cast_query_results($sth);
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

function cart_get_session_products($token) {
    $db = $GLOBALS['db'];
    $cart = array();

    $sql = 'SELECT products.id, products.sku, products.name, '.
           '       products.description, products.price, '.
           '       shoppingcart.clothing_size, shoppingcart.dimensions, '.
           '       products.changed_on, '.
           '       shoppingcart.count, '.
           '       shoppingcart.id as shoppingcart_id'.
           '  FROM products, sessions, shoppingcart '.
           ' WHERE shoppingcart.product_id = products.id'.
           '   AND sessions.id = shoppingcart.session_id'.
           '   AND sessions.token = :token';

    /* $token = session_get_cookie_value(); */

    $sth = $db->handle->prepare($sql);
    if (! $sth->execute(array(
        ':token'=>$token))) {
        return NULL;
    }
    /* $rs = $sth->fetchAll(PDO::FETCH_ASSOC); */
    $rs = db_cast_query_results($sth);

    /* Empty cart? */
    if (count($rs) === 0) {
        return NULL;
    }

    /* Create the cart array, filled with items that are of class Product and
     * their registered amount */
    foreach($rs as $row) {
        $cart_item = array();

        $prod = new Product();
        $prod->fillFromRow($row);

        $cart_item['prod']            = $prod;
        $cart_item['count']           = $row['count'];
        $cart_item['shoppingcart_id'] = $row['shoppingcart_id'];

        array_push($cart, $cart_item);
    }
    return $cart;
}

?>
