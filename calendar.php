<?php

require 'vendor/autoload.php';

use QnNguyen\EdtUbxNS\EdtIndex;
use QnNguyen\EdtUbxNS\EdtUbx;

//Todo: validation des données ?
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

//récupérer les urls
//todo: gérer les exeptions ?
$urls = get_urls();

//Init un emploi du temps: Semestre2 est choisi par défaut
//todo: modifier modules.php & index.html pour gérer les cours du premier semestre ?
$edt = new EdtUbx($urls['Licence']['Semestre2'][$_GET["semester"]][$_GET["group"] == 0 ? 'SERIE A' : ('GROUPE A' . $_GET["group"])]);

$criteria = [];
$filters = empty($_GET['filters']) ? [] : explode(",", $_GET["filters"]);

//exclure les ue choisies
foreach ($filters as $code) {
    $criteria[$code] = [];
}

//si BD n'est pas exclu
if (!in_array('J1IN6013', $filters)) {
    $criteria['J1IN6013'] = [
        'category' => ['in' => 'td( machine)?'],
        'notes' => ['notIn' => 'groupe( )?' . $_GET['groupBD']]
    ];
}

//si Algo3 n'est pas exclu
if (!in_array('J1IN6011', $filters)) {
    $criteria['J1IN6011'] = [
        'category' => ['in' => 'td( machine)?'],
        'notes' => [
            ($_GET['groupAlgo'] == 4 ? 'notIn' : 'in') => 'groupe( )?4'
        ]
    ];
}

//si ASPP3 n'est pas exclu
if (!in_array('J1IN6012', $filters)) {
    $criteria['J1IN6012'] = [
        'category' => ['in' => 'td( machine)?'],
        'notes' => [
            ($_GET['groupAS'] == 4 ? 'notIn' : 'in') => 'groupe( )?4'
        ]
    ];
}

//si la salle d'anglais est personnalisée
if ($_GET['anglais'] && !in_array('J1IN6012', $filters)) {
    /** @var \QnNguyen\EdtUbxNS\EdtUbxItem $item */
    foreach ($edt->getItems() as $item) {
        if (strpos($item->getName(), 'Anglais') !== false) {
            $item->setLocations(['A21/ Salle ' . $_GET['anglais']]);
        }
    }
}

$edt->apply_filter($criteria);

//todo: mettre en cache le contenu généré ?
echo $edt->toICS();

/**
 * Récupérer tous les urls des emplois du temps depuis le serveur de l'ubx et les mettre en cache pour 24h.
 * @return array
 * @throws Exception
 */
function get_urls()
{
    //vérifier si la version cache n'existe pas ou elle est obsolète (> 1 jours)
    if (!file_exists('urls.bin') || time() - filemtime('urls.bin') > 24 * 3600) {
        $urls = EdtIndex::fetch();
        file_put_contents('urls.bin', serialize($urls));
    } else {
        $urls = unserialize(file_get_contents('urls.bin'));
    }

    return $urls;
}
