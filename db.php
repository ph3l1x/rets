<?php
$dbHost = 'localhost';
$dbUserName = 'rets';
$dbPassword = 'Obkml1ZptVoZjHQd';
$dbName = 'rets';

$link = mysqli_connect($dbHost, $dbUserName, $dbPassword, $dbName);
if($link === false) {
    die("Database Connection Failed: " . mysqli_connect_error());
}