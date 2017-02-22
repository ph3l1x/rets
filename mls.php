<?php
header('Content-type: application/json');
require_once('db.php');


$query = 'SELECT * FROM `Listings` WHERE L_ListingID="' . $_POST['mls'] . '"';
//$query = 'SELECT * FROM `Listings` WHERE L_ListingID="98640789"';
$results = array();
$rows = mysqli_query($link, $query);
    while($data = mysqli_fetch_assoc($rows)) {
      $results = $data;
    }

print json_encode($results);