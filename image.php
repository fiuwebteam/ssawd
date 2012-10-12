<?php
/*
 * SSAWD image.php v 1.0
 * Author: Andre Oliveira
 * Date: 10/11/12
 * Link: https://github.com/fiuwebteam/ssawd
 * 
 * This is a file to detect the device currently being used, 
 * read the passed image, and, depending on the device,
 * resize and cache local copies of the image.
 * 
 */
require('./lib/config.php');
require('./lib/functions.php');
// Read the passed image, exit if none is given.
$imageLocation = isset($_GET["img"]) ? $_GET["img"] : null;
if ($imageLocation == null) { 
	die("You need to specify the location of the image."); 
}
// Makes sure that the images are from the same host only.
if ($localImagesOnly) {
	$imgLocParse = parse_url($imageLocation);
	if (isset($imgLocParse["host"]) && $_SERVER["HTTP_HOST"] != $imgLocParse["host"]) {
		die("This script is currently only able to use images on the same host.<br/>
		This can be changed in the configuration.");
	}
}
/*
 *  Read the device type cookie if available, else, parse the request,
 *  identify the device, and save the cookie for future use.
*/
if ( isset($_COOKIE["device_type"]) ) {
	$type = $_COOKIE["device_type"];	
} else {
	require('./lib/ua-parser/UAParser.php');
	$type = "desktop";
	$ua = UA::parse();	
	if ($ua->isTablet) {
		$type = "tablet";
	} else if($ua->isMobile || $ua->isMobileDevice ) {
		$type = "mobile";
	}
	setcookie("device_type", $type, strtotime('+30 days') );
}

$imageName = "./cache/images/" . sha1($imageLocation . $type);

require('./lib/SimpleImage.php');
$image = new SimpleImage();

// Load the local copy is present, else, resize if appropriate, and save a local copy.
if (file_exists($imageName)) {
	$image->load($imageName);	
} else {
	mkCacheDir("image");
	$image->load($imageLocation);
	$width = 0;
	switch($type) {
		case "tablet":
			if ($image->getWidth() > $tabletWidth ) { $width = $tabletWidth; }
		break;
		case "mobile":
			if ($image->getWidth() > $mobileWidth ) { $width = $mobileWidth; }
		break;
	}
	if ($width) { 
		$image->resizeToWidth($width); 
	}
	if (!$image->save($imageName, $image->image_type)) {
		die("Could not save local cache.");
	}	
}
// Identify the proper file type for the header.
$contentType = "image/jpeg";
if( $image->image_type == IMAGETYPE_JPEG ) {
	$contentType = "image/jpeg";
} elseif( $image->image_type == IMAGETYPE_GIF ) {
	$contentType = "image/gif";
} elseif( $image->image_type == IMAGETYPE_PNG ) {
	$contentType = "image/png";
}
header("Content-Type: $contentType");
$image->output($image->image_type);
?>