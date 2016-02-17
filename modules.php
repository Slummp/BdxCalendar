<?php
// Librairie du parser d'iCal
require 'class.iCalReader.php';

// URL du calendrier d'HackJack
$url = 'http://www.hackjack.info/et/' . $_POST["semester"] . '_A/ical';

// Vérification de la validité du semestre séléctionné
if(get_headers($url)[0] != 'HTTP/1.1 200 OK')
{
    echo 'error';
}

// Si le semestre est valide
else
{
    $ical = new ical($url);

    $modules= array();

    $sub = array("Cours", "cours", "/", "TD", "td", "TP", "tp", "Machine");
    
    // Liste des modules
    foreach($ical->events() as $event)
    {
        $modules[substr($event["SUMMARY"], -9, -1)] = ltrim(str_replace($sub, "", substr($event["SUMMARY"], 0, -11)));
    }

    ksort($modules);

    // Retour des modules sous forme de tableau JSON
    echo json_encode($modules);
}
?>
