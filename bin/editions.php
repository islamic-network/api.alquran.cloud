<?php
require_once realpath(__DIR__) . '/../config/doctrineBootstrap.php';

use Doctrine\ORM\EntityManager;

$entityManager = EntityManager::create($dbParams, $dbConfig);
$apiKey = '9e2f24a5b170a0d9ffd3627af9c2e26e';

echo 'Safety Measure in place';
exit;

$client = new \GuzzleHttp\Client(['base_uri' => 'http://api.globalquran.com/']);
        
$res = $client->request('GET',
                 'quran', 
                 [
                     'query' => [
                         'format' => 'json',
                         'key' => $apiKey
                     ]
                 ]
                );
$r = (string) $res->getBody()->getContents();
$x = json_decode($r);

foreach ($x->quranList as $id => $rec) {
    $e = new \Quran\Entity\Edition();
    $e->setIdentifier($id);
    $e->setLanguage($rec->language_code);
    $e->setEnglishName($rec->english_name);
    $e->setName($rec->name);
    $e->setFormat($rec->format);
    $e->setType($rec->type);    
    $e->setAudio(json_encode($rec->media)); 
    $e->setSource($rec->source);
    $e->setLastUpdated($rec->last_update);
    $entityManager->persist($e);
    $entityManager->flush();
    echo "{$id}. Processed \n";
}
