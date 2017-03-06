<?php
header('Content-type: application/json');
require_once('db.php');
require_once('phpThumb/phpThumb.config.php');

$action = $_GET['action'];

if ($action == 'least_expensive') {
  $query = 'SELECT * FROM `Listings` WHERE L_CITY="Boise" ORDER BY L_SystemPrice DESC LIMIT 10';
  $rows = mysqli_query($link, $query);
  $results = array();
  while($data = mysqli_fetch_assoc($rows)) {
    $data['L_IMAGES'] = phpThumbURL("src=/var/www/rets.mindimage.net/images/{$data['L_ListingID']}-1.jpg&w=100&zc=c", 'phpThumb/phpThumb.php');
    $results[] = $data;
  }

  print json_encode($results);
} else {

  $query = 'SELECT * FROM `Listings` WHERE L_ListingID="' . $_POST['mls'] . '"';
//$query = 'SELECT * FROM `Listings` WHERE L_ListingID="98640789"';
  $results = array();
  $rows = mysqli_query($link, $query);
  while ($data = mysqli_fetch_assoc($rows)) {
    $results = $data;
  }

  print json_encode($results);
}