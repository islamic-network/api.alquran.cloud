<?php
require_once realpath(__DIR__) . '/../config/doctrineBootstrap.php';

set_time_limit(0);

use Doctrine\ORM\EntityManager;

$entityManager = EntityManager::create($dbParams, $dbConfig);

$sajda= [
    [7,206,'recommended'],[13,15,'recommended'],[16,50,'recommended'],[17,109,'recommended'],[19,58,'recommended'],[22,18,'recommended'],[22,77,'recommended'],[25,60,'recommended'],[27,26,'recommended'],[32,15,'obligatory'],[38,24,'recommended'],[41,38,'obligatory'],[53,62,'obligatory'],[84,21,'recommended'],[96,19,'obligatory']
];
$sajdas = count($sajda);

$currPage = 0;
foreach($sajda as $p) {
    $currPage++;
    $surat = $p[0];
    $ayat = $p[1];
    $recommended = $p[2] == 'recommended' ? 1 : 0;
    $obligatory = $p[2] == 'obligatory' ? 1 : 0;
    $sql = "INSERT INTO sajda (id, recommended, obligatory) VALUES ($currPage, $recommended, $obligatory);\n";
    $sql .= "UPDATE ayat SET sajda_id = '$currPage' WHERE (surat_id = '{$surat}' AND numberinsurat = '{$ayat}');" . "\n";

    echo $sql;
}
