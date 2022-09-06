<?php
require ('../config/doctrineBootstrap.php');

use Symfony\Component\Yaml\Yaml;
use Doctrine\ORM\EntityManager;
use Quran\Entity\Edition;
use Quran\Entity\Ayat;

global $dbParams, $dbConfig;

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

$ayahs = file('ayahs.txt');

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




