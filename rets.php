<?php

date_default_timezone_set('America/New_York');

require_once("vendor/autoload.php");

$log = new \Monolog\Logger('PHRETS');
$log->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout', \Monolog\Logger::DEBUG));

$config = new \PHRETS\Configuration;
$config->setLoginUrl('http://imls.rets.paragonrels.com/rets/fnisrets.aspx/IMLS/login?rets-version=rets/1.5')
    ->setUsername('551942')
    ->setPassword('hrf4p8ky')
    ->setRetsVersion('1.7.2')
    ->setUserAgent('DREALTY/1.0');

$rets = new \PHRETS\Session($config);
$rets->setLogger($log);
$connect = $rets->Login();
$system = $rets->GetSystemMetadata();
$resources = $system->getResources();
$classes = $resources->first()->getClasses();
$classes = $rets->GetClassesMetadata('Property');
$objects = $rets->GetObject('Property', 'Photo', '00-1669', '*', 1);
$fields = $rets->GetTableMetadata('Property', 'A');

$results = $rets->Search('Property', 'A', '*', [
    'QueryType' => 'DMQL2',
    'Limit' => 1,
    'Select' => 'RE_1'
]);
$results->last();
$results->first();
$results->getMetadata();
$results->getHeaders();
$results->getTotalResultsCount();
$all_ids = $results->lists('L_ListingID');
$results->toJSON();
$results->toCSV();
$results->toArray();

foreach ($results as $r) {
    var_dump($r);
}