<?php
require 'class.iCalReader.php';

if(!(isset($_GET["filters"])))
    $_GET["filters"] = "";

//

if(!(isset($_GET["group"])))
    $_GET["group"] = "";

//

if(!(isset($_GET["groupBD"])))
    $_GET["groupBD"] = 0;

if(!(isset($_GET["groupAlgo"])))
    $_GET["groupAlgo"] = 0;

if(!(isset($_GET["groupAS"])))
    $_GET["groupAS"] = 0;

$ical = new ical('http://www.hackjack.info/et/' . $_GET["semester"] . '_A' . intval($_GET["group"]) . '/ical');

header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: inline; filename=calendar.ics');

echo 'BEGIN:VCALENDAR
PRODID:-//HackJack//Emplois du temps BdxI//FR
VERSION:2.0
METHOD:PUBLISH
X-WR-CALNAME:Emploi du temps
X-WR-CALDESC:Emploi du temps
X-PUBLISHED-TTL:PT1H
';

foreach($ical->events() as $event)
{
    switch ($event["SUMMARY"]) {
        case "TD BD (J1IN6013)":
            if(substr_compare($event["DESCRIPTION"], "GROUPE " . intval($_GET["groupBD"]), -8) == 0)
               addEvent($event);
            break;
        case "TD Machine BD (J1IN6013)":
            if(substr_compare($event["DESCRIPTION"], "GROUPE " . intval($_GET["groupBD"]), -8) == 0)
               addEvent($event);
            break;
        case "TD Machine Algo3 (J1IN6011)":
            if(substr_compare($event["DESCRIPTION"], (intval($_GET["groupAlgo"]) == 4 ? "groupe 4" : "AU CREMI"), -8) == 0)
               addEvent($event);
            break;
        case "TD AS et PP3 (J1IN6012)":
            if(intval($_GET["groupAS"]) == 4 && substr_compare($event["DESCRIPTION"], " GROUPE4", -8) == 0)
               addEvent($event);
            else if(intval($_GET["groupAS"]) == 0 && substr_compare($event["DESCRIPTION"], " GROUPE4", -8) != 0)
               addEvent($event);
            break;
        case "TD Machine AS et PP3 (J1IN6012)":
            if(intval($_GET["groupAS"]) == 4 && substr_compare($event["DESCRIPTION"], " GROUPE4", -8) == 0)
               addEvent($event);
            else if(intval($_GET["groupAS"]) == 0 && substr_compare($event["DESCRIPTION"], " GROUPE4", -8) != 0)
               addEvent($event);
            break;
        default:
            addEvent($event);
            break;
    }
}

echo 'END:VCALENDAR';

function filter($event)
{
    $filters = explode(",", $_GET["filters"]);
    return !in_array(substr($event["SUMMARY"], -9, -1), $filters);
}

function addEvent($event)
{
    if(filter($event))
    {
        echo 'BEGIN:VEVENT' . "\n";
        foreach($event as $key => $param)
        {
          echo $key . ':' . $param . "\n";
        }
        echo 'END:VEVENT' . "\n";
    }
}
               
function test($event)
{
    if(filter($event))
        echo $event["SUMMARY"] . " --> " . $event["DESCRIPTION"] . "<br><br>";
}
?>