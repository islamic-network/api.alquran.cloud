<?php
// This script specifically imports the text from https://github.com/khaledhosny/quran-data.
// It should be used to insert / update data for quran-uthmani-2
// Clone the repo, remove the meta file from the quran folder and then execute the script.

require ('../config/doctrineBootstrap.php');

use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\EntityManager;
use Quran\Entity\Edition;
use Quran\Entity\Ayat;

global $dbParams, $dbConfig;

$ayahs = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(realpath(__DIR__) . '/quran-data/quran'));
$files = array_keys(array_filter(iterator_to_array($iterator), function($file) {
    return $file->isFile();
}));
sort($files);
foreach ($files as $file) {
    $ayahs = array_merge($ayahs, file($file));
}
// Strip the numbers
foreach ($ayahs as $key => $value) {
    $text = explode('۝', $value);
    $t = rtrim(ltrim($text[0]));
    $ayahs[$key] = $t;
}

$em = EntityManager::create($dbParams, $dbConfig);

$yml = (object) Yaml::parseFile('edition.yml');

// Add Edition
$edition = new Edition();
$edition->setIdentifier($yml->identifier);
$edition->setName($yml->name);
$edition->setLanguage($yml->language);
$edition->setEnglishName($yml->englishname);
$edition->setFormat($yml->format);
$edition->setType($yml->type);
$edition->setLastUpdated($yml->lastupdated);
$edition->setDirection($yml->direction);
$edition->setSource($yml->source);
$em->persist($edition);
$em->flush();

$count = 0;
foreach ($ayahs as $ayah) {

    $count++;
    /**
     * @var Ayat $refAyat
     */
    $refAyat = $em->getRepository('\Quran\Entity\Ayat')->findOneBy(['edition' => '1', 'number' => $count]);
    $ayat = new Ayat();
    $ayat->setEdition($edition);
    $ayat->setSurat($refAyat->getSurat());
    $ayat->setJuz($refAyat->getJuz());
    $ayat->setNumber($refAyat->getNumber());
    $ayat->setNumberInSurat($refAyat->getNumberInSurat());
    $ayat->setManzil($refAyat->getManzil());
    $ayat->setPage($refAyat->getPage());
    $ayat->setRuku($refAyat->getRuku());
    $ayat->setSajda($refAyat->getSajda());
    $ayat->setHizbQuarter($refAyat->getHizbQuarter());
    $ayat->setText($ayah);
    $em->persist($ayat);
}
$em->flush();




