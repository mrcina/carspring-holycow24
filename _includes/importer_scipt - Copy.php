<?php



if( !$xml = simplexml_load_string('http://carspring.holycow24.com/_includes/rssfeed.php') ) {
    die('Fehler beim Einlesen der XML Datei!');
}

echo $xml->cars->car->count();
echo "<br>";
echo "<br>";

foreach($xml->cars->car as $car)
{
    echo (string)$car->index;
	echo "<br>";
    echo (string)$car->make;
	echo "<br>";
}

?>


