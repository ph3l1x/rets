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
var_dump($system);

$resources = $system->getResources();
$classes = $resources->first()->getClasses();
var_dump($classes);

$classes = $rets->GetClassesMetadata('Property');
var_dump($classes->first());

$objects = $rets->GetObject('Property', 'Photo', '00-1669', '*', 1);
var_dump($objects);

$fields = $rets->GetTableMetadata('Property', 'A');
var_dump($fields[0]);

$results = $rets->Search('Property', 'A', '*', ['Limit' => 3, 'Select' => 'RE_1']);
foreach ($results as $r) {
    var_dump($r);
}