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

?>
