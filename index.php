<?php

require 'build.php';

$head = new Head;
$head->display();
?>

<header>
<h1>City Gallery</h1>
</header>


<nav>
London<br>
Paris<br>
Tokyo<br>
</nav>

<section>
<h1>London</h1>
<p>
London is the capital city of England. It is the most populous city in the United Kingdom,
with a metropolitan area of over 13 million inhabitants.
</p>
<p>
Standing on the River Thames, London has been a major settlement for two millennia,
its history going back to its founding by the Romans, who named it Londinium.
</p>
</section>


Aside.

<footer>
Effe proberen met HTML5
</footer>

<?php
$tail = new Tail;
$tail->display();

?>
