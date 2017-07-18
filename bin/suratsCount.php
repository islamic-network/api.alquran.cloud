<?php

require_once realpath(__DIR__) . '/../config/doctrineBootstrap.php';

use Doctrine\ORM\EntityManager;

$entityManager = EntityManager::create($dbParams, $dbConfig);

echo 'Safety Measure in place';
exit;

$surat = $entityManager->getRepository('Quran\Entity\Surat');
$ayat = $entityManager->getRepository('Quran\Entity\Ayat');
foreach ($surat->findAll() as $surah) {
    $numberofAyahs = $ayat->findBy(['surat' => $surah->getId(), 'edition' => '1']);
    $count = count($numberofAyahs);
    $surah->setNumberOfAyats($count);
    $entityManager->persist($surah);
    echo 'Updated ' . $surah->getEnglishName() . ' with ' . $count; echo "\n";
}

$entityManager->flush();

echo "Update completed.";