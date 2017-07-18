<?php

require_once realpath(__DIR__) . '/../config/doctrineBootstrap.php';

use Doctrine\ORM\EntityManager;
set_time_limit(0);
ini_set('memory_limit','1048M');
$apiKey = '9e2f24a5b170a0d9ffd3627af9c2e26e';

$entityManager = EntityManager::create($dbParams, $dbConfig);

$audioBasePath = '/var/www/VHOSTS/alquran.cloud/cdn/quran/audio/';

function downloadFile($url, $path)
{
    $newfname = $path;
    $file = fopen ($url, 'rb');
    if ($file) {
        $newf = fopen ($newfname, 'wb');
        if ($newf) {
            while(!feof($file)) {
                fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
            }
        }
    } else {
        return false;
    }
    if ($file) {
        fclose($file);
    }
    if (isset($newf) && $newf) {
        fclose($newf);
    }
    
    return true;
}


$filter = [
    'format' => 'audio'
];


$editions = $entityManager->getRepository('\Quran\Entity\Edition')->findBy($filter);

$edCount = 0;
foreach ($editions as $edition) {
    $edCount++;
    $medias = json_decode(json_decode(($edition->getMedia())));
    echo 'Processing ' . $edition->getIdentifier(); echo "\n";
    foreach ($medias as $media) {
        echo $media->type . "\n";
        if ($media->type == 'mp3' && $edition->getIdentifier() == 'ar.saoodshuraym') {
            $dir = $audioBasePath . $media->kbs . '/' . $edition->getIdentifier();
            if (!file_exists($dir) && !is_dir($dir)) {
                // Create Dir
                mkdir($dir);
            }
            for ($i=1; $i<=6236; $i++) {
                $writePath = $dir . '/'. $i . '.mp3';
                $url = 'http:' . $media->path . $i . '.mp3';
                if (!file_exists($writePath)) {
                    if (downloadFile($url, $writePath)) {
                        echo "$edCount - Downloaded file $i at $url for " . $edition->getIdentifier() . " ->  $writePath" . "\n";
                    } else {
                        echo "$edCount - Failed to download file $i at $url for " . $edition->getIdentifier() . "\n";
                    }
                } else {
                    echo "$edCount - File $writePath exists. Skipped file $i for " . $edition->getIdentifier() . "\n";
                }
            }
        }
    }
}
echo 'Completed OGG';



//http://stackoverflow.com/questions/3938534/download-file-to-server-from-url

//http://audio.globalquran.com/ar.alafasy/mp3/64kbs/8.mp3
