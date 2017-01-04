<?php

require_once __DIR__ . '/vendor/autoload.php';

$formation = explode(",", $_GET["formation"]);

$id = $formation[0];
$code = $formation[1];

// initialisation des paramètres du GET si non-renseignés
if(!(isset($_GET["filtres"])))
    $_GET["filtres"] = "";

$filtres = explode(",", $_GET["filtres"]);

if(!(isset($_GET["groupes"])))
    $_GET["groupes"] = "";

$groupes = explode(",", $_GET["groupes"]);
array_push($groupes, $code);

$url = "https://edt-st.u-bordeaux.fr/etudiants/";

$diplome = array(   "L" =>  "Licence/",
                    "M1"    =>  "Master/Master1/",
                    "M2"    =>  "Master/Master2/");

$semestre = array(  "S1"    =>  "Semestre1/",
                    "S2"    =>  "Semestre2/");

$url .= $diplome[$_GET["diplome"]];
$url .= $semestre[$_GET["semestre"]];
$url .= "g" . $id . ".xml";

// Vérification de la validité de la requête
if(get_headers($url)[0] != 'HTTP/1.1 200 OK')
{
    echo 'error';
}

// Si la requête est valide
else
{
    $xml = file_get_contents($url);
    $parser = simplexml_load_string($xml);

	$calendar = new \Eluceo\iCal\Component\Calendar('-//Slummmp//Emplois du temps BdxI//FR');

    foreach ($parser->event as $event) {
    	$module = explode(" ", (string)$event->resources->module->item);
    	$codeModule = $module[0];
    	unset($module[0]);
    	$items = array();

    	foreach ($event->resources->group->item as $item) {
    		array_push($items, (string)$item);
    	}

    	if (!in_array($codeModule, $filtres) && in_array($item, $groupes)) {
	    	$date = explode("/", $event->attributes()->date);
    		$date = new DateTime($date[2] . "-" . $date[1] . "-" . $date[0]);
    		$date->modify('+' . (string)$event->day . ' day');

    		$dateStart = clone $date;
    		$time = explode(":", (string)$event->starttime);
    		$dateStart->modify('+' . $time[0] . ' hours');
    		$dateStart->modify('+' . $time[1] . ' minutes');
    		
    		$dateEnd = clone $date;
    		$time = explode(":", (string)$event->endtime);
    		$dateEnd->modify('+' . $time[0] . ' hours');
    		$dateEnd->modify('+' . $time[1] . ' minutes');

			$vEvent = new \Eluceo\iCal\Component\Event();
			$vEvent->setUseUtc(false);
    		
			$vEvent
				->setDtStart($dateStart)
				->setDtEnd($dateEnd)
			    ->setSummary($event->category . " " . implode(" ", $module))
			    ->setLocation((string)$event->resources->room->item)
			    ->setDescription($codeModule . "\n" . (string)$event->resources->staff->item . "\nNotes : " . (string)$event->notes)
			;

			$calendar->addComponent($vEvent);
		}
	}


	header('Content-Type: text/calendar; charset=utf-8');
	header('Content-Disposition: attachment; filename="calendar.ics"');

	echo $calendar->render();
}

?>