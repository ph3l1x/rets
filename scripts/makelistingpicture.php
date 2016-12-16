<?php

//$fileList = glob('../images/*-1.jpg');
$fileList[] = '../images/98632198-1.jpg';
$i=0;
foreach($fileList as $file) {
	$outfile = explode('-1', $file);
	$outfile = "../images/".explode('/', $outfile[0])[2] .'-listing.jpg';
	$image = new imagick($file);

	$imageProps = $image->getImageGeometry();
	$width = $imageProps['width'];
	$height = $imageProps['height'];

	if($width > $height) {
		$newHeight = 170;
		$newWidth = (170 / $height) * $width;
	} else {
		$newWidth = 316;
		$newHeight = (316 / $width) * $height;
	}

//	$image->resizeImage($newWidth,$newHeight, imagick::FILTER_LANCZOS, 0.9, true);
//	$image->cropImage(316,170,0,0);
	$image->scaleImage(316,170, true);
	$image->writeImage($outfile);
	print "({$i})Writting {$outfile} to images folder\r\n";
	$i++;
}
