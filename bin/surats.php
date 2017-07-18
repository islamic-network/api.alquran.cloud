<?php

require_once realpath(__DIR__) . '/../config/doctrineBootstrap.php';

use Doctrine\ORM\EntityManager;

$entityManager = EntityManager::create($dbParams, $dbConfig);

echo 'Safety Measure in place';
exit;

$row = 0;
if (($handle = fopen("../data/surats.csv", "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
    if ($row > 0) {
        $s = new \Quran\Entity\Surat();
        $s->setName($data[1]);
        $s->setEnglishName($data[2]);
        $s->setEnglishTranslation($data[3]);
        $s->setRevelationCity($data[5]);
        $entityManager->persist($s);
        $entityManager->flush();
        echo "{$row}. Processed Surat " . $data[2] . "\n";
    }
    $row++;
  }
  fclose($handle);
}

echo "Import completed.";