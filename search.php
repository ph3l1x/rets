<?php

require_once('db.php');
require_once('phpThumb/phpThumb.config.php');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: accept, access-control-allow-headers, x-angularjs, x-csrf-token");

$getResults = $_GET;
if($getResults['list'] == 'listingTypes') {
    $query = "select distinct L_Type_ from Listings";
    $rows = mysqli_query($link, $query);
    while($data = mysqli_fetch_assoc($rows)) {
        $results[] = $data['L_Type_'];
    }
    if(is_array($results)) {
        print json_encode($results);
    } else {
        print "";
    }
    die();

} elseif($getResults) {
    foreach($getResults as $k => $v) {
        $searchQueryElements[] = "$k=\"$v\"";
    }
    $query = "select * from Listings where ".implode(" and ",$searchQueryElements)." limit 50";
} else {
    $query = "SELECT * FROM Listings WHERE L_City = 'Boise' ORDER BY L_AskingPrice DESC limit 12";

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
    $listingMatches = glob('images/'.$data['L_ListingID'].'-1.jpg');
    foreach($listingMatches as $afile) {
        if(is_file($afile)) {
            $data['L_IMAGES'] = phpThumbURL("src=/var/www/rets.mindimage.net/{$afile}&w=320&h=170&zc=c", 'phpThumb/phpThumb.php');
        }
    }
    $results[] = $data;
}

if(is_array($results)) {
    print json_encode($results);
} else {
    print "";
}