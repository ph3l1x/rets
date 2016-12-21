<?php

require_once('db.php');
require_once('phpThumb/phpThumb.config.php');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, accept, access-control-allow-headers, x-angularjs, x-csrf-token");

$getResults = $_GET;

$postData = json_decode(file_get_contents('php://input'), true);


/*
 * Returning Listing Type Fields
 */
if($getResults['list'] == 'listingTypes') {
    $query = "select distinct L_Type_ from Listings";
    $rows = mysqli_query($link, $query);
    while($data = mysqli_fetch_assoc($rows)) {
        $results[] = array('name' => $data['L_Type_'], 'selected' => false, 'filter' => 'L_Type_');

    }
    if(is_array($results)) {
        print json_encode($results);
    } else {
        print "";
    }
    die();

}
elseif($getResults['list'] == 'citiesList') {
    $query = "select distinct L_City from Listings";
    $rows = mysqli_query($link, $query);
    while($data = mysqli_fetch_assoc($rows)) {
        $results[] = array('name' => $data['L_City'], 'selected' => false, 'filter' => 'L_City');

    }
    if(is_array($results)) {
        print json_encode($results);
    } else {
        print "";
    }
    die();

}

elseif($getResults['list'] == 'search') {
	$search = $getResults['search'];

    $query = "select distinct L_City from Listings where L_City like '%".$search."%' || L_Zip like '%".$search."%' || LMD_MP_Subdivision like '%".$search."%' || L_ListingID like '%".$search."%'"; 

    $rows = mysqli_query($link, $query);
    while($data = mysqli_fetch_assoc($rows)) {
        $results[] = array('name' => $data['L_City'], 'selected' => false, 'filter' => 'L_City');

    }
    if(is_array($results)) {
        print json_encode($results);
    } else {
        print "";
    }
    die();

}


 elseif($postData) {
    foreach($postData as $v=>$k) {
		//print_r($k);
        if($k['filter'] == "L_Type_" ) {
            $field = $k['filter'];
            $queryParts[] = "{$k['filter']}=\"{$k['name']}\"";
        }
		if($k['filter'] == "L_Keyword2" ) {
            $field = $k['filter'];
            $queryParts[] = "{$k['filter']}=\"{$k['name']}\"";
        }
		if($k['filter'] == "LM_Dec_3" ) {
            $field = $k['filter'];
            $queryParts[] = "{$k['filter']}=\"{$k['name']}\"";
        }
		if($k['filter'] == "L_City" ) {
            $field = $k['filter'];
            $queryParts[] = "{$k['filter']}=\"{$k['name']}\"";
        }
		if($k['filter'] == "L_SystemPrice"){
            $field = $k['filter'];
			$price = explode("-",$k['name']);
            $queryParts[] = "{$k['filter']} BETWEEN {$price[0]} AND {$price[1]}";			
		}
    }

    $query = "select * from Listings where ".implode(" && ", $queryParts)." limit 20";

} elseif($getResults) {

    foreach($getResults as $k => $v) {
        $searchQueryElements[] = "$k=\"$v\"";



    }
    $query = "select * from Listings where ".implode(" && ",$searchQueryElements)." DESC limit 20";
} else {
    $query = "SELECT * FROM Listings WHERE L_City = 'Boise' and L_Type_ = 'Single Family' and L_AskingPrice between 200000 and 500000 ORDER BY L_AskingPrice DESC limit 20";

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
    print $query;
}