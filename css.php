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

$device = deviceType();
/* 
 * Cascade will read the folder up to the current device type
 * Ex: If the device is tablet and cascade is on, it will read the mobile 
 * folder and append it to the output before reading the tablet folder
 * and appending that.   
 */
$cascade = isset($_GET["cascade"]) ? true : false;

$cssFolders = getFolders($device, "css");
$cssFile = makeFileName($cssFolders, $cascade);
$cssLocation = "./cache/css/$cssFile";

if (file_exists($cssLocation) && false) {
	header("Content-Type: text/css");
	readfile($cssLocation);
	exit();
} else {
	mkCacheDir("css");
	flushCache("css");
	if ($cascade) {
		$output = cascadeHandler($device, "css");		
	} else {
		$output = "";
		foreach($cssFolders as $value) {
			$output .= readFolder($value);
		}
	}			
	require('./lib/cssmin.php');
	$output = CssMin::minify($output);
	file_put_contents($cssLocation, $output);	
	header("Content-Type: text/css");
	echo $output;
	exit();
}
?>