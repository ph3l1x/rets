<?php
$dbHost = 'localhost';
$dbUserName = 'rets';
$dbPassword = 'giznad0';
$dbName = 'rets';

$link = mysqli_connect($dbHost, $dbUserName, $dbPassword, $dbName);
if($link === false) {
    die("Database Connection Failed: " . mysqli_connect_error());
}