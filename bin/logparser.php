<?php
ini_set('memory_limit', '2048M');
$today = time();
$yesterday = $today - (1*60*60*24);
$fileNameNoExt = date('Y-m-d', $yesterday);
//$fileNameNoExt = date('Y-m-d', time());

$fileName = $fileNameNoExt . '.log';
$logPath = "/var/www/VHOSTS/alquran.cloud/api/logs/";
$file  =  $logPath . $fileName;
$statsFile = '/var/www/VHOSTS/alquran.cloud/api/stats/statistics.log';

if ($file) {
    $r = logSort($file);

    logSum($r, true);

    $statLine = $fileNameNoExt . ':::' . json_encode($r) . "\n";

    return file_put_contents($statsFile, $statLine, FILE_APPEND);
}


/*************
Functions
**************/
function logSort($file) {
    $lines = file($file);
    $r = [];
    $r['total'] = count($lines);
    foreach ($lines as $l) {
        $parts = explode('::', $l);
        $line = ($parts[count($parts) - 1]);
        // Actual log.
        $la = json_decode(trim(str_replace(' []', '', $line)));
        $types = explode('INFO:', $parts[0]);
        $type = trim($types[1]);
        $ip = '0.0.0.0'; // Unknown
        $r['useragent'] = [];
        $r['referer'] = [];
        $r['origin'] = [];
        if (isset($la->server->ip)) {
            $ip = $la->server->ip;
        }
        if (isset($la->server->url) && $la->server->url != '') {
            $r['url'][$la->server->url][] = 1;
        }
        $r['endpoint'][$type][] = 1;
        $r['ip'][$ip][] = 1;
        
        if (isset($la->server->useragent) && $la->server->useragent != '') {
            $r['useragent'][$la->server->useragent][] = 1;
        }
        
        if (isset($la->server->origin) && $la->server->origin != '') {
            $r['origin'][$la->server->origin][] = 1;
        }
        
        if (isset($la->server->referer) && $la->server->referer != '') {
            $r['referer'][$la->server->referer][] = 1;
        }
        
    }
    
    return $r;
}

function logSum(array &$sortedLog, $truncate = false) {
    foreach ($sortedLog['endpoint'] as $key => $element) {
        $sortedLog['endpoint'][$key]['count'] = count($sortedLog['endpoint'][$key]);
        if ($truncate) {
            truncateArray($sortedLog['endpoint'][$key], 'count');
        }
    }
    foreach ($sortedLog['url'] as $key => $element) {
        $sortedLog['url'][$key]['count'] = count($sortedLog['url'][$key]);
        if ($truncate) {
            truncateArray($sortedLog['url'][$key], 'count');
        }
    }
    foreach ($sortedLog['ip'] as $key => $element) {
        $sortedLog['ip'][$key]['count'] = count($sortedLog['ip'][$key]);
        if ($truncate) {
            truncateArray($sortedLog['ip'][$key], 'count');
        }
    }
    foreach ($sortedLog['useragent'] as $key => $element) {
        $sortedLog['useragent'][$key]['count'] = count($sortedLog['useragent'][$key]);
        if ($truncate) {
            truncateArray($sortedLog['useragent'][$key], 'count');
        }
    }
    foreach ($sortedLog['origin'] as $key => $element) {
        $sortedLog['origin'][$key]['count'] = count($sortedLog['origin'][$key]);
        if ($truncate) {
            truncateArray($sortedLog['origin'][$key], 'count');
        }
    }
    foreach ($sortedLog['referer'] as $key => $element) {
        $sortedLog['referer'][$key]['count'] = count($sortedLog['referer'][$key]);
        if ($truncate) {
            truncateArray($sortedLog['referer'][$key], 'count');
        }
    }
    $sortedLog['endpoint']['count'] = count($sortedLog['endpoint']);
    $sortedLog['url']['count'] = count($sortedLog['url']);
    $sortedLog['ip']['count'] = count($sortedLog['ip']);
    $sortedLog['useragent']['count'] = count($sortedLog['useragent']);
    $sortedLog['origin']['count'] = count($sortedLog['origin']);
    $sortedLog['referer']['count'] = count($sortedLog['referer']);
}

function truncateArray(array &$array, $exception)
{
    foreach ($array as $k => $element) {
        if ($k !== $exception) {
            unset ($array[$k]);
        }
    }
}
