<?php

require 'category.php';

function navigation_load($db) {
    $categories = categories_load($db);
    return $categories;
}

function navigation_display($categories) {
    echo '<div class="nav"><ul class="nav_block">';

    foreach ($categories as $cat) {
        echo '<li class="nav_elem">' . $cat->name . '</li>';
    }

    echo '</ul></div>';
}


?>
