<?php

require_once('db.php');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: accept, access-control-allow-headers, x-angularjs, x-csrf-token");

$getResults = $_GET;

if($getResults) {
    foreach($getResults as $k => $v) {
        $searchQueryElements[] = "$k=$v";
    }
    $query = "select * from Listings where ".implode(" and ",$searchQueryElements)." limit 12";
} else {
    $query = "SELECT * FROM Listings ORDER BY L_AskingPrice DESC limit 12"; 
}




$rows = mysqli_query($link, $query);
while($data = mysqli_fetch_assoc($rows)) {

    $matches = glob('images/'.$data['L_ListingID'].'*');
    foreach($matches as $file) {
        if(is_file($file)) {
            $data['IMAGES'][] = $file;
            sort($data['IMAGES'], SORT_NATURAL);
        }
    }
    $results[] = $data;
}

if(is_array($results)) {
    print json_encode($results);
} else {
    print "";
}