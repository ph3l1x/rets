<?php
header('Content-type: application/json');
require_once('db.php');
require_once('phpThumb/phpThumb.config.php');

$action = $_GET['action'];

function getOfficeName($officeID, $link) {
  $query = "select * from Offices where `O_OfficeID`= {$officeID}";
  $rows = mysqli_query($link, $query);
  while($data = mysqli_fetch_assoc($rows)) {
    $results = $data;
  }
  return $results;
}
if ($action == 'least_expensive') {
  $query = 'SELECT * FROM `Listings` WHERE L_CITY="Boise" ORDER BY CONVERT(`L_SystemPrice`, DECIMAL) ASC LIMIT 30';
  $rows = mysqli_query($link, $query);
  $results = array();
  while($data = mysqli_fetch_assoc($rows)) {
    if(file_exists('images/' . $data['L_ListingID'] . '-1.jpg')) {
      $data['L_IMAGES'] = phpThumbURL("src=/var/www/rets.mindimage.net/images/{$data['L_ListingID']}-1.jpg&w=100&zc=c", 'phpThumb/phpThumb.php');
      $results[] = $data;
    }
  }

  print json_encode($results);
} elseif ($action == 'most_expensive') {
  $query = 'SELECT * FROM `Listings` WHERE L_CITY="Boise" ORDER BY CONVERT(`L_SystemPrice`, DECIMAL) DESC LIMIT 30';
  $rows = mysqli_query($link, $query);
  $results = array();
  while ($data = mysqli_fetch_assoc($rows)) {
    if (file_exists('images/' . $data['L_ListingID'] . '-1.jpg')) {
      $data['L_IMAGES'] = phpThumbURL("src=/var/www/rets.mindimage.net/images/{$data['L_ListingID']}-1.jpg&w=100&zc=c", 'phpThumb/phpThumb.php');
      $results[] = $data;
    }
  }
  print json_encode($results);
} elseif ($action == 'newest') {
  $query = 'SELECT * FROM `Listings` WHERE L_CITY="Boise" ORDER BY `L_ListingDate` DESC LIMIT 30';
  $rows = mysqli_query($link, $query);
  $results = array();
  while ($data = mysqli_fetch_assoc($rows)) {
    if (file_exists('images/' . $data['L_ListingID'] . '-1.jpg')) {
      $data['L_IMAGES'] = phpThumbURL("src=/var/www/rets.mindimage.net/images/{$data['L_ListingID']}-1.jpg&w=100&zc=c", 'phpThumb/phpThumb.php');
      $results[] = $data;
    }
  }
  print json_encode($results);
} elseif ($action == 'updated') {
  $query = 'SELECT * FROM `Listings` WHERE L_CITY="Boise" ORDER BY `L_UpdateDate` DESC LIMIT 30';
  $rows = mysqli_query($link, $query);
  $results = array();
  while ($data = mysqli_fetch_assoc($rows)) {
    if (file_exists('images/' . $data['L_ListingID'] . '-1.jpg')) {
      $data['L_IMAGES'] = phpThumbURL("src=/var/www/rets.mindimage.net/images/{$data['L_ListingID']}-1.jpg&w=100&zc=c", 'phpThumb/phpThumb.php');
      $results[] = $data;
    }
  }
  print json_encode($results);
} else {

  $query = 'SELECT * FROM `Listings` WHERE L_ListingID="' . $_POST['mls'] . '"';
//  $query = 'SELECT * FROM `Listings` WHERE L_ListingID="98635521"';
  $results = array();
  $officeData = array();
  $rows = mysqli_query($link, $query);
  while ($data = mysqli_fetch_assoc($rows)) {
    $results = $data;
    $officeData = getOfficeName($data['L_ListOffice1'], $link);
  }
  if($results['L_ListingID']) {
    foreach (glob("images/" . $results['L_ListingID'] . '*.jpg') as $image) {
      $results['ALL_IMAGES'][] = $image;
    }
  }

  print json_encode(array_merge($results, $officeData));
}