<?php

require_once 'globals.php';
require_once 'config.php';
require_once 'build.php';
require_once 'product.php';
require_once 'navigation.php';
require_once 'article.php';
require_once 'category.php';


/* Global initializers */
if (!initialize()) {
    http_response_code(500);
    return;
}

$head = new Head;
$head->display();


echo '<h2>Contact</h2>';
echo '<p class="info_p">Heeft u vragen? Neem dan contact via <a href="mailto:info@allkidslove.nl">info@allkidslove.nl</a>.';



$tail = new Tail;
$tail->display();


?>
