<?php

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/config.php';

global $apiKey;

$client = new \BABA\Collabim\API\Client\Collabim();
$client->authenticate($apiKey);

var_dump($client->oneTimeAnalysesKeywordMeasuring(['prvni pozice do 48h na google','seo']));
