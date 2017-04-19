<?php

require_once('db.php');
require_once('phpThumb/phpThumb.config.php');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, accept, access-control-allow-headers, x-angularjs, x-csrf-token");

//print("hi"); die();

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
    $intval = intval($search);
    $num_length = strlen((string) $intval);
    

    if ($intval == 0) {
        //Normal search, return cities.
        $query = "select distinct L_City from Listings where L_City like '".$search."%' limit 40"; 
        $rows = mysqli_query($link, $query);
        
        while($data = mysqli_fetch_assoc($rows)) {
            $results[] = array('name' => $data['L_City'], 'selected' => false, 'filter' => 'L_City');
        }
    } else {
        
        
        if ($num_length > 5 ) {
            $query = "select distinct L_ListingID from Listings where L_ListingID like '".$search."%' limit 40"; 
            $rows = mysqli_query($link, $query);
            while($data = mysqli_fetch_assoc($rows)) {
                $results[] = array('name' => $data['L_ListingID'], 'selected' => false, 'filter' => 'L_ListingID');
            }
	    } else {
	        $query = "select distinct L_Zip from Listings where L_Zip like '".$search."%' limit 40"; 
            $rows = mysqli_query($link, $query);
            while($data = mysqli_fetch_assoc($rows)) {
                $results[] = array('name' => $data['L_Zip'], 'selected' => false, 'filter' => 'L_Zip');
            }
	    }
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
		if($k['filter'] == "L_ListingID" ) {
            $field = $k['filter'];
            $queryParts[] = "{$k['filter']}=\"{$k['name']}\"";
        }
        if($k['filter'] == "L_Type_" ) {
            $field = $k['filter'];
            $temp = explode(",", $k['name']);
            
            $query = "(";
            for ($i = 0; $i < count($temp); $i++) {
                if ($i == count($temp)-1) {
                    $query .= "\"" . $temp[$i] . "\"";
                } else {
                    $query .= "\"" . $temp[$i] . "\",";
                }
            }
            $query .= ")";
            
            $queryParts[] = "{$k['filter']} IN " . $query;
        }
		if($k['filter'] == "L_Keyword2" ) {
            $field = $k['filter'];
            $queryParts[] = "{$k['filter']} >= \"{$k['name']}\"";
        }
        if($k['filter'] == "L_Remarks" ) {
            $field = $k['filter'];
            $queryParts[] = "{$k['filter']} LIKE \"%{$k['name']}%\"";
        }
		if($k['filter'] == "LM_Dec_3" ) {
            $field = $k['filter'];
            $queryParts[] = "{$k['filter']} >= \"{$k['name']}\"";
        }
		if($k['filter'] == "L_City" ) {
            $field = $k['filter'];
            $queryParts[] = "{$k['filter']}=\"{$k['name']}\"";
        }
        if($k['filter'] == "L_Zip" ) {
            $field = $k['filter'];
            $queryParts[] = "{$k['filter']}=\"{$k['name']}\"";
        }
		if($k['filter'] == "L_SystemPrice"){
            $field = $k['filter'];
			$price = explode("-",$k['name']);
			
			if ($price[1] > 999999) {
			    $price[1] = 99999999;
			}
			
            $queryParts[] = "{$k['filter']} BETWEEN {$price[0]} AND {$price[1]}";			
		}
		if($k['filter'] == "LM_int4_27"){
            $field = $k['filter'];
			$sqft = explode("-",$k['name']);
            $queryParts[] = "{$k['filter']} BETWEEN {$sqft[0]} AND {$sqft[1]}";			
		}
		if($k['filter'] == "LM_Int4_1"){
            $field = $k['filter'];
			$year = explode("-",$k['name']);
            $queryParts[] = "{$k['filter']} BETWEEN {$year[0]} AND {$year[1]}";			
		}
		
		if($k['bound'] == "Bounds" || $k['filter'] == "Bounds") {

            $coords = explode(",", $k['name']);
        
            
            $queryParts[] = "LMD_MP_Latitude BETWEEN {$coords[2]} AND {$coords[0]}";	
            $queryParts[] = "LMD_MP_Longitude BETWEEN {$coords[3]} AND {$coords[1]}";	
            
		}
    }

    $query = "select * from Listings where ".implode(" && ", $queryParts)." limit 40";
    //print $query; die();

} elseif($getResults) {

    foreach($getResults as $k => $v) {
        $searchQueryElements[] = "$k=\"$v\"";



    }
    $query = "select * from Listings where ".implode(" && ",$searchQueryElements)." DESC limit 40";
    //print $query; die();
} else {
    $query = "SELECT * FROM Listings WHERE L_City = 'Boise' and L_Type_ = 'Single Family' and L_AskingPrice between 200000 and 500000 ORDER BY L_AskingPrice DESC limit 40";
    //print $query; die();

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
