<?php
require_once realpath(__DIR__) . '/../config/doctrineBootstrap.php';

set_time_limit(0);

use Doctrine\ORM\EntityManager;

$entityManager = EntityManager::create($dbParams, $dbConfig);
$apiKey = '9e2f24a5b170a0d9ffd3627af9c2e26e';

//echo 'Safety Measure in place';
//exit;

$client = new \GuzzleHttp\Client(['base_uri' => 'http://api.globalquran.com/']);

$juzs = $entityManager->getRepository('\Quran\Entity\Juz')->findAll();

foreach ($juzs as $juz) {
    $res = $client->request('GET',
                 'juz/' . $juz->getId() . '/quran-simple', 
                 [
                     'query' => [
                         'format' => 'json',
                         'key' => $apiKey
                     ]
                 ]
                );
    $r = (string) $res->getBody()->getContents();
    $x = json_decode($r);

    $gbJuz = $x->quran->{'quran-simple'};
    
    $firstAyat = reset($gbJuz)->id;
    $lastAyat = end($gbJuz)->id;
    
    $sql = "UPDATE ayat SET juz_id = '{$juz->getId()}' WHERE number >= '{$firstAyat}' AND number <= '{$lastAyat}'" . ";";
    
    echo $sql . "\n";
}
