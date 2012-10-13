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
$jsFolder = "./js/$type/";
$jsFile = md5(md5_of_dir($jsFolder) . md5_of_dir("./js/shared/"));
$jsLocation = "./cache/js/$jsFile";

if (file_exists($jsLocation)) {
	header("Content-Type: text/javascript");
	readfile($jsLocation);
	exit();
} else {
	mkCacheDir("js");
	flushCache("js");
	$output = readFolder("./js/shared/");
	$output .= readFolder($jsFolder);	
	require('./lib/jsmin.php');
	$output = JSMin::minify($output);
	$output = utf8_encode($output);
	file_put_contents($jsLocation, $output);
	header("Content-Type: text/javascript");
	echo $output;
	exit();
}
?>