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

$device = deviceType();
/* 
 * Cascade will read the folder up to the current device type
 * Ex: If the device is tablet and cascade is on, it will read the mobile 
 * folder and append it to the output before reading the tablet folder
 * and appending that.   
 */
$cascade = isset($_GET["cascade"]) ? true : false;

$jsFolders = getFolders($device, "js");
$jsFile = makeFileName($jsFolders, $cascade);
$jsLocation = "./cache/js/$jsFile";

if (file_exists($jsLocation)) {
	header("Content-Type: text/javascript");
	readfile($jsLocation);
	exit();
} else {
	mkCacheDir("js");
	flushCache("js");
	if ($cascade) {
		$output = cascadeHandler($device, "js");		
	} else {
		$output = "";
		foreach($jsFolders as $value) {
			$output .= readFolder($value);
		}
	}
	require('./lib/jsmin.php');
	$output = JSMin::minify($output);
	$output = utf8_encode($output);
	file_put_contents($jsLocation, $output);
	header("Content-Type: text/javascript");
	echo $output;
	exit();
}
?>