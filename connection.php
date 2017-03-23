<?php

$log = new \Monolog\Logger('PHRETS');
$log->pushHandler(new \Monolog\Handler\StreamHandler('php://stdout', \Monolog\Logger::DEBUG));

$config = new \PHRETS\Configuration;
$config->setLoginUrl('http://imls.rets.paragonrels.com/rets/fnisrets.aspx/IMLS/login?rets-version=rets/1.7.2')
    ->setUsername('551942')
    ->setPassword('hrf4p8ky')
    ->setUserAgent('DREALTY/1.0')
    ->setRetsVersion('1.7.2');

$propertyClass = array('RE_1');
$officeClass = array('Office');
$rets = new \PHRETS\Session($config);
$rets->setLogger($log);
$connect = $rets->Login();

$selectFields = 'L_ListingID,L_City,L_Class,L_Type_,L_Area,L_AskingPrice,L_SystemPrice,L_AddressNumber,L_AddressSearchNumber,L_AddressDirection,L_AddressStreet,L_Address2,L_State,L_Zip,L_StatusCatID,L_StatusID,L_SaLeRent,L_Keyword1,L_Keyword2,L_Keyword5,L_Keyword6,L_Keyword7,L_ListAgent1,L_ListOffice1,L_ListAgent2,L_ListOffice2,L_ListingDate,L_ExpirationDate,L_OriginaLPrice,L_Remarks,L_HowSoLd,L_SeLLingAgent1,L_SeLLingOffice1,L_SeLLingAgent2,L_SeLLingOffice2,L_StatusDate,L_InputDate,L_UpdateDate,L_Last_Photo_updt,L_PictureCount,LMD_MP_Latitude,LMD_MP_Longitude,LMD_MP_QuaLity,LMD_MP_AddressLine,LMD_MP_Subdivision,L_Address,L_PricePerSQFT,LM_Char1_1,LM_Char1_2,LM_Char1_3,LM_Char1_5,LM_Char1_8,LM_Char1_19,LM_Char10_1,LM_Char10_2,LM_Char10_3,LM_Char10_11,LM_Char10_17,LM_Char10_18,LM_Char10_19,LM_Char10_20,LM_Char10_21,LM_Char10_22,LM_Char25_6,LM_Char25_14,LM_Char25_15,LM_Char25_16,LM_Char25_17,LM_Char50_1,LM_Int1_20,LM_Int4_1,LM_Dec_3,LM_Dec_8,LM_char5_1,LM_char5_2,LM_char5_3,LM_char5_4,LM_char5_5,LM_char5_6,LM_char5_7,LM_char5_10,LM_char5_11,LM_char5_13,LM_char10_42,LM_char10_43,LM_char10_44,LM_char10_45,LM_char10_70,LM_char100_1,LM_char255_1,LM_char512_1,LM_int4_27,LM_Dec_12,LM_Dec_14,LM_Dec_15,LM_Dec_16,LFD_COOLING_1,LFD_CONSTRUCTION_2,LFD_FIREPLACE_3,LFD_GARAGETYPE_4,LFD_HEATING_5,LFD_INCLUDEDKITCHENFEATURES_6,LFD_LANDUSE_7,LFD_SPRINKLERSYSTEM_8,LFD_LOTFEATURES_11,LFD_POOLSPA_12,LFD_ROOF_13,LFD_SEWER_14,LFD_WATER_17,LFD_WATERHEATER_227,LA1_UserFirstName,LA1_UserLastName';
$officeFields = 'O_OfficeID,O_OrganizationName,O_PhoneNumber1';

function officeImport($officeFields, $rets, $officeClass, $link) {
  /**
   * Truncate Table to perform fresh import.
   */

  $truncateSQL = "TRUNCATE TABLE Office";
  mysqli_query($link, $truncateSQL);

  foreach ($officeClass as $pClass) {

    $maxRows = true;
    $offset = 1;
    $limit = 5000;
    $query = "O_HiddenOtyID=1";

    while ($maxRows) {
      $results = $rets->Search('Office', $pClass, $query, [
        'Limit' => $limit,
        'select' => $officeFields,
        'Offset' => $offset
      ]);


      $count = 0;
      foreach ($results as $r) {
        $fields = $r->getFields();
        foreach ($fields as $field) {
          $dbValues[] = mysqli_real_escape_string($link, $r["$field"]);
        }
        $dataValues = '"' . implode('", "', $dbValues) . '"';
        unset($dbValues);
        $sql = "insert into Offices (" . $officeFields . ") values ($dataValues)";
        if (mysqli_query($link, $sql)) {
          echo "Office Created for (" . $count . ") -> " . $r['O_OrganizationName'] . "\r\n";
        } else {
          echo "ERROR: " . mysqli_error($link) . "\r\n\r\n";
        };


        $count++;
      }
      $offset = ($offset + $count);
      $maxRows = $results->IsMaxrowsReached();
    }
  }

  $now = date('Y-m-d H:i:s');
  $updateTimeSQL = "update infos set last_office_import='$now'";
  mysqli_query($link, $updateTimeSQL);
}

function fullListingImport($selectFields, $rets, $propertyClass, $link)
{
    /**
     * Truncate Table to perform fresh import.
     */
    $truncateSQL = "TRUNCATE TABLE Listings";
    mysqli_query($link, $truncateSQL);

    foreach ($propertyClass as $pClass) {

        $maxRows = true;
        $offset = 1;
        $limit = 10000;
        $query = "L_StatusCatID=1";

        while ($maxRows) {
            $results = $rets->Search('Property', $pClass, $query, [
                'Limit' => $limit,
                'select' => $selectFields,
                'Offset' => $offset
            ]);


            $count = 0;
            foreach ($results as $r) {
                $fields = $r->getFields();
                foreach ($fields as $field) {
                    $dbValues[] = mysqli_real_escape_string($link, $r["$field"]);
                }
                $dataValues = '"' . implode('", "', $dbValues) . '"';
                unset($dbValues);
                $sql = "insert into Listings (" . $selectFields . ") values ($dataValues)";
                if (mysqli_query($link, $sql)) {
                    echo "Record Created for MLS(" . $count . ") -> " . $r['L_ListingID'] . "\r\n";
                } else {
                    echo "ERROR: " . mysqli_error($link) . "\r\n\r\n";
                };


                $count++;
            }
            $offset = ($offset + $count);
            $maxRows = $results->IsMaxrowsReached();
        }
    }

    $now = date('Y-m-d H:i:s');
    $updateTimeSQL = "update infos set last_full_listing_import='$now', last_listing_update='$now'";
    mysqli_query($link, $updateTimeSQL);


}
function fullImageImport($selectFields, $rets, $propertyClass, $link) {

    $files = glob('images/*');
    foreach($files as $file) {
        if(is_file($file)) {
            unlink($file);
        }
    }
    $currentIdQuery = "select L_ListingID from Listings";
    $rows = mysqli_query($link, $currentIdQuery);
    while($currentIDs = mysqli_fetch_array($rows)) {
        $objects = $rets->GetObject('Property', 'Photo',  $currentIDs['L_ListingID']);
        foreach($objects as $object) {
            file_put_contents("images/{$object->getContentId()}-{$object->getObjectId()}.jpg", $object->getContent());
        }

    }
    $now = date('Y-m-d H:i:s');
    $updateTimeSQL = "update infos set last_full_image_update='$now', last_image_update='$now'";
    mysqli_query($link, $updateTimeSQL);

}
function updateImages($selectFields, $rets, $propertyClass, $link) {

    $dateQuery = "select last_image_update from infos";
    $row = mysqli_query($link, $dateQuery);
    $updatedDate = mysqli_fetch_assoc($row)['last_image_update'];
    $updatedDate = explode(" ", $updatedDate);
    $updatedDate = $updatedDate[0]."T".$updatedDate[1]."+";

    $query = "L_Last_Photo_updt={$updatedDate}";
    $results = $rets->Search('Property', 'RE_1', $query, [
                'Limit' => '9999',
                'select' => 'L_ListingID'
            ]);
    $count = 1;
    foreach ($results as $r) {
        $mlsNewPhotos =  $r['L_ListingID'];


        $objects = $rets->GetObject('Property', 'Photo',  $mlsNewPhotos);
        foreach($objects as $object) {
            file_put_contents("/var/www/rets.mindimage.net/images/{$object->getContentId()}-{$object->getObjectId()}.jpg", $object->getContent());
        }

        print "Added Photos for (".$count.") -> ".$mlsNewPhotos."\r";
        $count++;
    }
    $now = date('Y-m-d H:i:s');
    $updateTimeSQL = "update infos set last_full_image_update='$now', last_image_update='$now'";
    mysqli_query($link, $updateTimeSQL);

}
function purgeImages($selectFields, $rets, $propertyClass, $link) {
    $files = glob('images/*');
    foreach($files as $file) {
        $firstPart = explode("/", $file);
        $goodPart = explode("-", $firstPart[1])[0];
        $mlsFiles[] = $goodPart;
    }
        $mlsFiles = array_unique($mlsFiles);

    $currentIdQuery = "select L_ListingID from Listings";
    $rows = mysqli_query($link, $currentIdQuery);
    while($currentIDs = mysqli_fetch_array($rows)) {
        $listingIDs[] = $currentIDs['L_ListingID'];
    }
    sort($listingIDs);
    sort($mlsFiles);

    $difference = array_merge(array_diff($listingIDs, $mlsFiles), array_diff($mlsFiles, $listingIDs));

    foreach($difference as $diff) {
        foreach(glob("images/{$diff}*") as $filename) {
            unlink($filename);
        }
    }
}
function updateListings($selectFields, $rets, $propertyClass, $link) {
//    $currentIdQuery = "select L_ListingID from Listings";
//    $rows = mysqli_query($link, $currentIdQuery);
//   // $currentIDs = mysqli_fetch_array($rows);
//    $currentIDs = mysqli_fetch_all($rows, MYSQLI_ASSOC);

//    $dateQuery = "select last_listing_update from infos";
//    $row = mysqli_query($link, $dateQuery);
//    $updatedDate = mysqli_fetch_assoc($row)['last_listing_update'];
//
//    foreach ($propertyClass as $pClass) {
//        $maxRows = true;
//        $offset = 1;
//        $limit = 1000;
//        $query = "L_InputDate=$updatedDate";
//
//        while($maxRows) {
//            $results = $rets->Search('Property', $pClass, $query, [
//                'Limit' => $limit,
//                'select' => $selectFields,
//                'Offset' => $offset
//            ]);
//
//            $count = 0;
//            foreach ($results as $r) {
//                $fields = $r->getFields();
//                foreach ($fields as $field) {
//                    $dbValues[] = mysqli_real_escape_string($link, $r["$field"]);
//                }
//                $dataValues = '"' . implode('", "', $dbValues) . '"';
//                unset($dbValues);
//
//                print_r($r);

//                $sql = "insert into Listings (" . $selectFields . ") values ($dataValues)";
//                if (mysqli_query($link, $sql)) {
//                    echo "Record Created for MLS(" . $count . ") -> " . $r['L_ListingID'] . "\r\n";
//                } else {
//                    echo "ERROR: " . mysqli_error($link) . "\r\n\r\n";
//                };
//
//
//                $count++;
//            }
//            $offset = ($offset + $count);
//            $maxRows = $results->IsMaxrowsReached();
//        }
//    }


}
