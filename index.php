<?php

require 'config.php';
require 'database.php';
require 'build.php';
require 'product.php';

$head = new Head;
$head->display();
?>

<div class="header">
<h1>All kids love</h1>
</div>


<div class="nav">
<ul class="nav_block">
<li class="nav_elem">
Frozen
</li>
<li class="nav_elem"> 
Hello Kitty
</li>
<li class="nav_elem">
Spiderman
</li>
</ul>
</div>



<div class="section">
<h1>London</h1>
<p>
London is the capital city of England. It is the most populous city in the United Kingdom,
with a metropolitan area of over 13 million inhabitants.
</p>
<p>
Standing on the River Thames, London has been a major settlement for two millennia,
its history going back to its founding by the Romans, who named it Londinium.
</p>

<h1>lijst</h1>
<?php
/* Open DB */
$db = db_connect();
if ($db && $db->handle === NULL) {
    http_response_code(500);
    return;
}

/* Load all products in memory from DB */
$products = products_load($db);
?>

</div>


Aside.

<div class="footer">
Effe proberen met HTML5
</div>

<?php
$tail = new Tail;
$tail->display();

?>
