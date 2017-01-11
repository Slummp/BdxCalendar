<?php

$formation = explode(",", $_POST["formation"]);

$id = $formation[0];
$code = $formation[1];

$url = "https://edt-st.u-bordeaux.fr/etudiants/";

$diplome = array(   "L" =>  "Licence/",
                    "M1"    =>  "Master/Master1/",
                    "M2"    =>  "Master/Master2/");

$semestre = array(  "S1"    =>  "Semestre1/",
                    "S2"    =>  "Semestre2/");

$url .= $diplome[$_POST["diplome"]];
$url .= $semestre[$_POST["semestre"]];
$url .= "g" . $id . ".xml";

$groupes = array();
$modules = array();

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

    foreach ($parser->event as $event) {
        $module = explode(" ", (string)$event->resources->module->item);
        $codeModule = $module[0];
        unset($module[0]);

        if ($codeModule != "")
            $modules[$codeModule] = implode(" ", $module);

        foreach ($event->resources->group->item as $groupe) {
            if ($groupe != $code && strstr($groupe, $code)) {
                array_push($groupes, (string)$groupe);
            }
        }
    }

    $groupes = array_unique($groupes);
    sort($groupes);

    $modules = array_unique($modules);
}

$result = array("Groupes" => $groupes,
                "Modules" => $modules);

header('Content-Type: application/json');

echo json_encode($result);

?>
