<?php

$url = "https://edt-st.u-bordeaux.fr/etudiants/";

$diplome = array(   "L" =>  "Licence/",
                    "M1"    =>  "Master/Master1/",
                    "M2"    =>  "Master/Master2/");

$semestre = array(  "S1"    =>  "Semestre1/",
                    "S2"    =>  "Semestre2/");

$url .= $diplome[$_POST["diplome"]];
$url .= $semestre[$_POST["semestre"]];
$url .= "finder.xml";

$formations = array();

// Vérification de la validité de la requête
if(get_headers($url)[0] != 'HTTP/1.1 200 OK')
{
    echo 'error';
}

// Si la requête est valide
else
{
    $delimiter = $_POST["diplome"] == "L" ? ' ' : ', ';

    $xml = file_get_contents($url);
    $parser = simplexml_load_string($xml);

    foreach ($parser->resource as $rec)
    {
        if ($rec->attributes()->type == "group")
        {
            $formation = explode($delimiter, $rec->name);
            $code = $formation[0];

            if($_POST["diplome"] != "L")
                unset($formation[0]);
            
            $formations[(string)$rec->attributes()->id . "," . $code] = implode($delimiter, $formation);
        }
    }
}

header('Content-Type: application/json');

echo json_encode($formations);

?>
