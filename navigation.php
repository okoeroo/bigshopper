<?php

require 'category.php';

function navigation_load() {
    /* Select non-sticky categories */
    $categories = categories_load(NULL);
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

    /* Categories, non-sticky/normal */
    foreach ($categories as $cat) {
        if ($cat->sticky !== 0) {
            continue;
        }
        echo '<a href="/index.php?category=' . $cat->id . '">';
        echo '    <li class="nav_elem">' . $cat->name . '</li>';
        echo '</a>';
    }

    /* Spacer */
    echo '    <li class="nav_elem_spacer">&nbsp;</li>';

    /* Categories, sticky/special */
    foreach ($categories as $cat) {
        if ($cat->sticky !== 1) {
            continue;
        }
        echo '<a href="/index.php?category=' . $cat->id . '">';
        echo '    <li class="nav_elem">' . $cat->name . '</li>';
        echo '</a>';
    }

    echo '</ul></div>';
}


?>
