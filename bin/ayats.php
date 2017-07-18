<?php
require_once realpath(__DIR__) . '/../config/doctrineBootstrap.php';

set_time_limit(0);
ini_set('memory_limit','1048M');

use Doctrine\ORM\EntityManager;

$entityManager = EntityManager::create($dbParams, $dbConfig);
$apiKey = '9e2f24a5b170a0d9ffd3627af9c2e26e';

echo 'Safety Measure in place';
//exit;

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

$row=0;
foreach ($x->quranList as $id => $rec) {
    if ($rec->format == 'text') {
        $row++;
        $res2 = $client->request('GET',
                     'complete/' . $id, 
                     [
                         'query' => [
                             'format' => 'json',
                             'key' => $apiKey
                         ]
                     ]
                    );
        $r = (string) $res2->getBody()->getContents();
        $x = json_decode($r);
        $edition = $entityManager->getRepository('\Quran\Entity\Edition')->findOneBy(['identifier' => $id]);
        echo $row . ". {$id}. Starting \n";
        foreach($x->quran->$id as $r) {
            $a = new \Quran\Entity\Ayat();
            $a->setNumber($r->id);
            $a->setNumberInSurat($r->ayah);
            $a->setSurat($entityManager->getRepository('\Quran\Entity\Surat')->findOneBy(['id' => $r->surah]));
            $a->setText($r->verse);
            $a->setEdition($edition);
            $entityManager->persist($a);            
        }
        echo "{$id}. Processed \n";
        $entityManager->flush();
        $entityManager->clear();
    }

}
