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
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
$imageLocation = isset($_GET["img"]) ? $_GET["img"] : null;
if ($imageLocation == null) { exit(); }

require('./lib/ua-parser/UAParser.php');
require('./lib/SimpleImage.php');

$type = "desktop";
$ua = UA::parse();

if ($ua->isTablet) {
	$type = "tablet";
} else if($ua->isMobile || $ua->isMobileDevice ) {
	$type = "mobile";
}

$imageName = "./cache/images/" . sha1($imageLocation . $type);

$image = new SimpleImage();

if (file_exists($imageName)) {
	$image->load($imageName);	
} else {
	$image->load($imageLocation);
	$width = 0;
	switch($type) {
		case "tablet":
			if ($image->getWidth() > 1024 ) { $width = 1024; }
		break;
		case "mobile":
			if ($image->getWidth() > 480 ) { $width = 480; }
		break;
	}
	if ($width) { 
		$image->resizeToWidth($width); 
	}
	$image->save($imageName);	
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