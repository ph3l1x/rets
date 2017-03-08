<?php
/**
 * Arguments from command line:
 * -m full-listing-import
 * -m full-image-import
 * -m update-listings
 * -m update-images
 * -m purge-images
 *
 */
require_once('db.php');

$arguments = getopt("m:");

if($arguments) {
    date_default_timezone_set('America/Boise');

    if ($arguments['m'] == 'full-listing-import') {
        require_once("vendor/autoload.php");
        require_once("connection.php");
        fullListingImport($selectFields, $rets, $propertyClass, $link);

    } elseif ($arguments['m'] == 'update-listings') {

        require_once("vendor/autoload.php");
        require_once("connection.php");
        updateListings($selectFields, $rets, $propertyClass, $link);

    } elseif ($arguments['m'] == 'full-image-import') {

        require_once("vendor/autoload.php");
        require_once("connection.php");
        fullImageImport($selectFields, $rets, $propertyClass, $link);

    } elseif ($arguments['m'] == 'update-images') {

        require_once("vendor/autoload.php");
        require_once("connection.php");
        updateImages($selectFields, $rets, $propertyClass, $link);

    } elseif ($arguments['m'] == 'purge-images') {
        require_once("vendor/autoload.php");
        require_once("connection.php");
        purgeImages($selectFields, $rets, $propertyClass, $link);

    } elseif ($arguments['m'] == 'office-import') {
      require_once("vendor/autoload.php");
      require_once("connection.php");
      officeImport($officeFields, $rets, $officeClass, $link);

    } else {
        print "Argument Specified does not exist. Available options are -m full-listing-import, full-image-import, update-listings, update-images, purge-images, office-import\r\n";
    }
} else {
    print "Arguments required -m full-listing-import, full-image-import, update-listings, update-images, purge-images, office-import\r\n";

}
