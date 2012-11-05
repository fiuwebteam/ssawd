<?php 
/*
 * SSAWD css.php v 1.0
 * Author: Andre Oliveira
 * Date: 10/12/12
 * Link: https://github.com/fiuwebteam/ssawd
 * 
 * This is a file to detect the device type, fetch 
 * the approiate css files associated for that device 
 * and minimize them all into one sheet.
 * 
 */

require('./lib/functions.php');

$type = deviceType();
/* 
 * Cascade will read the folder up to the current device type
 * Ex: If the device is tablet and cascade is on, it will read the mobile 
 * folder and append it to the output before reading the tablet folder
 * and appending that.   
 */
$cascade = isset($_GET["cascade"]) ? true : false;
/*
 * If we want the output to be the same for a tablet as a desktop 
 * we mark this flag on and we only have to add files to the desktop.
 */
$tabletIsDesktop = isset($_GET["tabletIsDesktop"]) ? true : false;
/*
 * If we want the output to be the same for a mobile as a tablet 
 * we mark this flag on and we only have to add files to the tablet.
 */
$mobileIsTablet = isset($_GET["mobileIsTablet"]) ? true : false;

$cssFolder = "./css/$type/";
$cssFile = md5(
	md5_of_dir($cssFolder) . 
	md5_of_dir("./css/shared/") . 
	$cascade . $tabletIsDesktop . 
	$mobileIsTablet
);
$cssLocation = "./cache/css/$cssFile";

if (file_exists($cssLocation)) {
	header("Content-Type: text/css");
	readfile($cssLocation);
	exit();
} else {
	mkCacheDir("css");
	flushCache("css");
	if ($cascade) {
		$output = cascadeHandler($type, "css", $tabletIsDesktop, $mobileIsTablet);		
	} else {
		$output = breakHandler($type, "css", $tabletIsDesktop, $mobileIsTablet);
	}
	$output = readFolder("./css/shared/") . $output;		
	require('./lib/cssmin.php');
	$output = CssMin::minify($output);
	file_put_contents($cssLocation, $output);	
	header("Content-Type: text/css");
	echo $output;
	exit();
}
?>