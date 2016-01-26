<?php
// Librairie du parser d'iCal
require 'class.iCalReader.php';

$modules= array();

$semestres = array("MI101", "MI201", "IN300", "IN400", "IN501", "IN601");

foreach($semestres as $semestre)
{
    // URL du calendrier d'HackJack
    $url = 'http://www.hackjack.info/et/' . $semestre . '_A/ical';

    // Vérification de la validité du semestre séléctionné
    if(get_headers($url)[0] != 'HTTP/1.1 200 OK')
    {
        echo 'Error<br>';
    }

    // Si le semestre est valide
    else
    {
        $ical = new ical($url);

        // Liste des modules
        foreach($ical->events() as $event)
        {
            array_push($modules, $semestre . ' ---> ' . ltrim(substr($event["SUMMARY"], 0, -11)));
        }
    }
}

$modules = array_unique($modules);

ksort($modules);

// Retour des modules sous forme de tableau JSON
echo('<pre>');
print_r($modules);
echo('</pre');
?>
