<?php
require 'class.iCalReader.php';

// initialisation des paramètres du GET si non-renseignés
if(!(isset($_GET["filters"])))
    $_GET["filters"] = "";

//

if(!(isset($_GET["group"])))
    $_GET["group"] = "";

//

if(!(isset($_GET["anglais"])))
    $_GET["anglais"] = 0;

if(!(isset($_GET["groupBD"])))
    $_GET["groupBD"] = 0;

if(!(isset($_GET["groupAlgo"])))
    $_GET["groupAlgo"] = 0;

if(!(isset($_GET["groupAS"])))
    $_GET["groupAS"] = 0;

//

if(!(isset($_GET["alarm"])))
    $_GET["alarm"] = 0;

// Récupération du calendrier du groupe de TD depuis le site d'HackJack
$ical = new ical('http://www.hackjack.info/et/' . $_GET["semester"] . '_A' . intval($_GET["group"]) . '/ical');

// En-tête HTML spécifique au calendriers
header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: inline; filename=calendar.ics');

$events = $ical->events();

// Début du calendrier...
echo "BEGIN:VCALENDARr
PRODID:-//HackJack//Emplois du temps BdxI//FR\r
VERSION:2.0\r
METHOD:PUBLISH\r
X-WR-CALNAME:Emploi du temps\r
X-WR-CALDESC:Emploi du temps\r
X-PUBLISHED-TTL:PT1H\r
";

// Génération du tableau servant de base pour le tri dans l'ordre chronologique des événements de $events
foreach ($events as $key => $value) {
    $order[$key] =  substr($value['DTSTART'], 0, -8) . substr($value['DTSTART'], 9, -1);
}

// Tri dans l'ordre chronologique des événements de $events
array_multisort($order, $events);

foreach($events as $event)
{
    // Ajout au calendrier des événements correspondants au sous-groupes choisis
    switch (substr($event["SUMMARY"], 0, -11)) {
        case "TD BD":
            if(substr_compare($event["DESCRIPTION"], "GROUPE " . intval($_GET["groupBD"]), -8) == 0)
                addEvent($event);
            break;
        case "TD Machine BD":
            if(substr_compare($event["DESCRIPTION"], "GROUPE " . intval($_GET["groupBD"]), -8) == 0)
                addEvent($event);
            break;
        case "TD Machine Algo3":
            if(substr_compare($event["DESCRIPTION"], (intval($_GET["groupAlgo"]) == 4 ? "groupe 4" : "AU CREMI"), -8) == 0)
                addEvent($event);
            break;
        case "TD AS et PP3":
            if(intval($_GET["groupAS"]) == 4 && substr_compare($event["DESCRIPTION"], " GROUPE4", -8) == 0)
                addEvent($event);
            else if(intval($_GET["groupAS"]) == 0 && substr_compare($event["DESCRIPTION"], " GROUPE4", -8) != 0)
                addEvent($event);
            break;
        case "TD Machine AS et PP3":
            if(intval($_GET["groupAS"]) == 4 && substr_compare($event["DESCRIPTION"], " GROUPE4", -8) == 0)
                addEvent($event);
            else if(intval($_GET["groupAS"]) == 0 && substr_compare($event["DESCRIPTION"], " GROUPE4", -8) != 0)
                addEvent($event);
            break;
        case "TD Anglais S1":
        case "TD Anglais S2":
        case "TD Anglais S3":
        case "TD Anglais S4":
        case "TD Anglais S5":
        case "TD Anglais S6":
            addEvent($event, $_GET["anglais"]);
            break;
        default:
            addEvent($event);
            break;
    }
}

// ...fin du calendrier
echo 'END:VCALENDAR';

// Filtrage des modules à exclure
function filter($event)
{
    $filters = explode(",", $_GET["filters"]);
    return !in_array(substr($event["SUMMARY"], -9, -1), $filters);
}

// Ajout d'un événement au calendrier
function addEvent($event, $alarm = 0, $room = 0)
{
    if(filter($event))
    {
        echo 'BEGIN:VEVENT' . "\r\n";
        foreach($event as $key => $param)
        {
            if($room && $key == "LOCATION")
            {
                echo $key . ':A22/ Salle ' . $room . "\r\n";
            }
            else
            {
                echo $key . ':' . $param . "\r\n";
            }
        }
        if($alarm)
        {

        }
        echo 'END:VEVENT' . "\r\n";
    }
}

// Fonction de test...
function test($event)
{
    if(filter($event))
        echo $event["SUMMARY"] . " --> " . $event["DESCRIPTION"] . "<br><br>";
}
?>