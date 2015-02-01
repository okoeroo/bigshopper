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


echo '<h2>Algemene voorwaarden</h2>';
echo '<h3 class="info_h3">Overeenkomst</h3>';
echo '<p class="info_p">Als er door de klant opdracht gegeven wordt aan allkidslove.nl om één of meerdere artikelen te bestellen en leveren, dan is er sprake van een overeenkomst die wettelijk bindend is (koop op afstand).</p>';

echo '<h3 class="info_h3">Privacy</h3>';
echo '<p class="info_p">Allkidslove.nl slaat geen persoonsgegevens op. Uw gegevens worden gebruikt voor uw bestelling en de verzending daarvan, en daarna worden uw gegevens vernietigd. Om deze reden hebben wij ook geen mailinglist. Als u op de hoogte gehouden wilt worden van alle nieuwtje volg dan onze facebook pagina.</p>';

echo '<h3 class="info_h3">Betalen</h3>';
echo '<p class="info_p">Na het plaatsen van uw bestelling ontvangt u een mail met hierin uw bestelling en de gegevens om de betaling over te kunnen maken. Pas als uw betaling / aanbetaling bij mij binnen is worden uw producten besteld en gaat de levertermijn in. Vermeld in de omschrijving van uw betaling altijd duidelijk uw ordernummer, indien u dit niet vermeld kan uw bestelling vertraging oplopen of als de betaling niet kan worden geplaatst kan het geld zelfs retour gestort worden en wordt uw bestelling niet in behandeling genomen.</p>';

echo '<h3 class="info_h3">Levertermijn</h3>';
echo '<p class="info_p">Voor alle spullen geld een verwachte levertijd van 35 dagen. Er kan vooraf geen exacte leverdatum genoemd worden, omdat de levering afhankelijk is van vluchten, inklaring, douane e.d. Mails hierover worden ook niet beantwoord.
<br><br>
Een uitzondering geld voor de producten in de map \'voorraad\' deze producten kunnen wel gelijk geleverd worden.</p>';

echo '<h3 class="info_h3">Levering</h3>';
echo '<p class="info_p">Producten die op voorraad leverbaar zijn worden normaliter binnen 24 uur na ontvangst van de volledige betaling verstuurd. Voor producten die besteld moeten worden geld dat zodra de bestelde producten binnen zijn en de volledige betaling is binnen de producten normaliter ook binnen 24 uur worden verstuurd. De gestelde levertijd is een indicatie hier kunnen geen rechten aan worden ontleend.</p>';

echo '<h3 class="info_h3">Verzending</h3>';
echo '<p class="info_p">Artikelen worden verzonden via PostNL en Kiala als de volledige betaling ontvangen is. Allkidslove.nl is niet aansprakelijk voor verlies of beschadiging door PostNL en Kiala.
<br><br>
Brievenbuspost wordt altijd aangeboden bij de PostNL voor verzending. Voor pakketpost geld dat u de optie heeft voor verzending via PostNL of via Kiala. Voor verzending via PostNL geld dat uw pakket op werkdagen binnen 24 uur na ontvangst van de betaling ter verzending wordt aangeboden. Voor verzendingen door Kiala geld dat de pakketjes binnen 6 werkdagen ter verzending aan worden geboden. De extra dagen zijn omdat het dichtbijzijnde Kiala punt hier 3 kwartier vandaan ligt en ik hier niet dagelijks langs kan gaan.
<br><br>
Wilt u extra veiligheid voor u verzending kunt u bij brievenbuspost de keuze maken voor brievenbuspakketje. U betaald dan het totaalbedrag van 3,95 euro, uw levering wordt dan in een brievenbusdoosje verzonden en hierbij krijgt u een verzendbewijs zodat de zending te volgen is. Voor pakketpost met de PostNL kunt u ook kiezen voor meer veiligheid door het pakket aangetekend te laten versturen. U betaald dan het bedrag van 8,60 euro (normaal zijn de kosten 6,95 euro), u moet dan tekenen voor de ontvangst van uw pakket en uw levering is verzekerd voor een bedrag tot maximaal 500,- euro.
<br><br>
Pakketten die worden verzonden door de PostNL worden normaal de eerste werkdag na verzending aangeboden. Voor pakketten die worden verzonden door Kiala geld dat deze meestal 2 a 3 werkdagen later af te halen zijn bij het door u aangegeven Kiala punt.
<br><br>
U kunt uw bestelling ook bij mij ophalen in Voorburg. Ik heb geen fysieke winkel en u haalt het op bij mij thuis. Dit kan dan ook alleen op afspraak. De betaling dient ook contant plaats te vinden, en houdt u er a.u.b. rekening mee dat dit het liefst gepast gebeurt.
<br><br>
Wij proberen u altijd een mailtje te sturen als uw bestelling op de bus zit, en indien het een pakket post pakket betreft u de track en trace code te mailen zodat u het pakketje kunt volgen.</p>';

echo '<h3 class="info_h3">Prijzen</h3>';
echo '<p class="info_p">De prijzen in de webwinkel zijn vaste prijzen inclusief BTW. Hierover kan niet onderhandeld worden.</p>';

echo '<h3 class="info_h3">Ruilen / retourneren</h3>';
echo '<p class="info_p">Mocht er desondanks een artikel niet aan uw verwachting voldoen, dan kan deze geretourneerd worden. Artikelen kunnen altijd retour ongeacht of deze zijn aangekocht met korting of met actie\'s. Echter gelden er voor retournering de volgende voorwaarden:';

echo '<ul>';
echo '
<li>Artikelen kunnen retour gestuurd worden binnen 14 werkdagen.</li>
<li>Bij retournering dient u duidelijk een briefje bij te voegen met uw NAW gegevens, IBAN nummer en het ordernummer.</li>
<li>Artikelen dienen in de oorspronkelijke staat teruggestuurd te worden (artikelen waar de kaartjes vanaf zijn geknipt of niet in de originele verpakking zitten kunnen dus niet meer retour).</li>
<li>Artikelen dienen ongedragen en onbeschadigd te zijn (artikelen worden ook niet terug genomen als deze gewassen zijn, omdat u zelf een gebrek getracht heeft te herstellen).</li>
<li>De verzendkosten voor de retourzending zijn voor rekening van de koper, en de retourzending dient voldoende gefrankeerd te zijn.</li>
';
echo '</ul>';

echo 'Alleen als er aan al deze voorwaarden is voldaan, wordt de zending als retour geaccepteerd.</p>';

echo '<h3 class="info_h3">Klachtenafhandeling</h3>';
echo '<p class="info_p">Indien u onverhoopt niet tevreden bent met de levering van de producten van allkidslove.nl dan kunt u een e-mail sturen naar <a href="mailto:info@allkidslove.nl">info@allkidslove.nl</a>. Wij zullen uw klacht binnen 14 dagen in behandeling nemen en u hierover via de e-mail benaderen.</p>';


$tail = new Tail;
$tail->display();


?>
