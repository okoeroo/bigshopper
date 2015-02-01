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


echo '<h2>Info</h2>';
echo '<h3 class="info_h3">Levertijd</h3>';
echo '<p class="info_p">Allkidslove.nl hanteert geen voorraad. Alle producten die u besteld worden daarna bij mijn leverancier in het buitenland besteld. Vandaar dat er een verwachte levertijd is van 35 dagen. De 35 dagen levertijd is een indicatie, het kan zijn dat producten soms al na 21 dagen geleverd kunnen worden, maar het kan ook langer duren.
<br><br>
Een uitzondering geld voor alle producten die in de map voorraad staan, alle producten in deze map zijn wel op voorraad en direct leverbaar.
</p>';

echo '<h3 class="info_h3">Prijs producten op voorraad</h3>';
echo '<p class="info_p">Het kan voorkomen dat de prijs van producten die op voorraad zijn hoger is dan dezelfde producten die op bestelling gaan. Dit komt omdat producten die boven een bepaalde waarde zijn invoerrechten en BTW over geheven worden, en dit maakt sommige producten dan duurder.</p>';

echo '<h3 class="info_h3">Verzendkosten</h3>';
echo '<p class="info_p">De verzendkosten voor leveringen binnen Nederland worden later berekend. Kleding en andere kleine dingen kunnen per brievenbuspost verzonden worden. Voor grote bestellingen en producten geld dat deze per pakketpost verzonden moeten worden. U kunt deze via de postnl laten verzenden naar uw huis, de verzendkosten zijn dan 6,95 euro, of u kunt deze laten verzenden naar een Kiala punt bij u in de buurt, de verzendkosten zijn dan 3,95 euro.</p>';

echo '<h3 class="info_h3">Producten zelf ophalen</h3>';
echo '<p class="info_p">U mag uw producten ook altijd op afspraak bij mij in Voorburg op komen halen.</p>';

echo '<h3 class="info_h3">Verzending naar het buitenland</h3>';
echo '<p class="info_p">Indien u niet woonachtend bent in Nederland kunt u wel gewoon bij mij bestellen. Geeft u dan aan dat u de producten bij mij wilt ophalen, en neemt u dan contact met mij op met uw ordernummer en vermeld in uw mail waar u woont. Zo kan ik de verzendkosten voor u berekenen, en kan uw bestelling ook naar het buitenland worden verzonden.</p>';

echo '<h3 class="info_h3">Betaling</h3>';
echo '<p class="info_p">Voor producten die op bestelling gaan kunt u er voor kiezen om gelijk het totaal bedrag over te maken, of u mag 50% van het productbedrag overmaken bij bestelling en de overige 50% + de verzendkosten als ik alle producten binnen heb, en voordat deze geleverd worden.
<br><br>
Voor producten die op voorraad zijn geld dat de volledige betaling gelijk voldaan moet worden.</p>'; 

echo '<h3 class="info_h3">Kledingmaten</h3>';
echo '<p class="info_p">Besteld u altijd de kleding maat die u nodig heeft, en geen andere maat omdat u gehoord heeft dat bepaalde kleding klein of groot valt. De kleding (ook verkleedkleding en prinsessenjurkjes) zijn allemaal voorzien van Amerikaanse kledingmaten. Veel van de kleding valt vaak 1 maat kleiner en prinsessenjurkjes vallen al snel 2 kledingmaten kleiner. Hier houden wij rekening mee voor u. U besteld dus de maat die u nodig heeft en wij zorgen ervoor dat u de juiste maat krijgt. Kijkt u dus niet raar op als u bijvoorbeeld een prinsessenjurkje besteld in maat 122, en u een maat 140 jurkje toegestuurd krijgt. De maat klopt dan, omdat dit jurkje gewoon zo klein valt.</p>';


$tail = new Tail;
$tail->display();


?>
