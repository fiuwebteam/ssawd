<?php
/*
 * SSAWD image.php v 1.0
 * Author: Andre Oliveira
 * Date: 10/11/12
 * Link: https://github.com/fiuwebteam/ssawd
 * 
 * This is a file to automatically resize and cache images depending on the 
 * device viewing the site.
 * 
 */

// Set the width of the devices (in pixels) here.
$mobileWidth = 480;
$tabletWidth = 1024;

$imageLocation = isset($_GET["img"]) ? $_GET["img"] : null;
if ($imageLocation == null) { 
	die("You need to specify the location of the image."); 
}

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

require('./lib/SimpleImage.php');

$imageName = "./cache/images/" . sha1($imageLocation . $type);

$image = new SimpleImage();

if (file_exists($imageName)) {
	$image->load($imageName);	
} else {
	if (!file_exists("./cache/images/")) {
		if (!mkdir("./cache/images/")) {
			die("Could not create image cache folder.");
		}
	}
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
	if (!$image->save($imageName)) {
		die("Could not save local cache.");
	}	
}
$contentType = "image/jpeg";
if( $image->image_type == IMAGETYPE_JPEG ) {
	$contentType = "image/jpeg";
} elseif( $image->image_type == IMAGETYPE_GIF ) {
	$contentType = "image/gif";
} elseif( $image->image_type == IMAGETYPE_PNG ) {
	$contentType = "image/png";
}
header("Content-Type: $contentType");
$image->output();
?>