<?php
require_once realpath(__DIR__) . '/../config/doctrineBootstrap.php';

set_time_limit(0);

use Doctrine\ORM\EntityManager;

$entityManager = EntityManager::create($dbParams, $dbConfig);

$manazil = [[1,1],[5,1],[10,1],[17,1],[26,1],[37,1],[50,1]];
//echo 'Safety Measure in place';
//exit;

// Create 7 Manzils
for ($i=1; $i<=count($manazil); $i++) {
    $surat = $manazil[$i-1][0];
    if ($i <= 6) {
        $suratNext = $manazil[$i][0];
    } else {
        $suratNext = 115;
    }
    $sql = "INSERT INTO manzil (id) VALUES ($i);\n";
    $sql .= "UPDATE ayat SET manzil_id = '{$i}' WHERE (surat_id >= '{$surat}') AND (surat_id < '{$suratNext}')" . ";\n";
    echo $sql;
}
