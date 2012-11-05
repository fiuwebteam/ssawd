<?php 
/*
 * SSAWD js.php v 1.0
 * Author: Andre Oliveira
 * Date: 10/12/12
 * Link: https://github.com/fiuwebteam/ssawd
 * 
 * This is a file to detect the device type, fetch 
 * the approiate js files associated for that device 
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

$jsFolder = "./js/$type/";
$jsFile = md5(
	md5_of_dir($jsFolder) . 
	md5_of_dir("./js/shared/") . 
	$cascade . $tabletIsDesktop . 
	$mobileIsTablet
);
$jsLocation = "./cache/js/$jsFile";

if (file_exists($jsLocation) && false) {
	header("Content-Type: text/javascript");
	readfile($jsLocation);
	exit();
} else {
	mkCacheDir("js");
	flushCache("js");
	if ($cascade) {
		$output = cascadeHandler($type, "js", $tabletIsDesktop, $mobileIsTablet);		
	} else {
		$output = breakHandler($type, "js", $tabletIsDesktop, $mobileIsTablet);
	}
	$output = readFolder("./js/shared/") . $output;
	require('./lib/jsmin.php');
	$output = JSMin::minify($output);
	$output = utf8_encode($output);
	file_put_contents($jsLocation, $output);
	header("Content-Type: text/javascript");
	echo $output;
	exit();
}
?>