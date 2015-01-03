<?php

require 'category.php';

function navigation_load() {
    $categories = categories_load();
    return $categories;
}

function navigation_display($categories) {
    echo '<div class="nav"><ul class="nav_block">';

    /* Home */
    echo '<a href="/index.php">';
    echo '    <li class="nav_elem">Home</li>';
    echo '</a>';
    /* Cart */
    echo '<a href="/cart.php">';
    echo '    <li class="nav_elem">Winkelmandje</li>';
    echo '</a>';

    /* Spacer */
    echo '    <li class="nav_elem_spacer">&nbsp;</li>';

    /* Categories */
    foreach ($categories as $cat) {
        echo '<a href="/index.php?category=' . $cat->id . '">';
        echo '    <li class="nav_elem">' . $cat->name . '</li>';
        echo '</a>';
    }

    echo '</ul></div>';
}


?>
