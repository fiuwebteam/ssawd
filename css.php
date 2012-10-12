<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');
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

require('./lib/config.php');
require('./lib/functions.php');

$type = deviceType();

$cssFolder = "./css/$type/";
// file name changes if the files in the directory have been changed.
$cssFile = md5(md5_of_dir($cssFolder) . md5_of_dir("./css/shared/"));
$cssLocation = "./cache/css/$cssFile";
if (file_exists($cssLocation)) {
	header("Content-Type: text/css");
	readfile($cssLocation);
	exit();
} else {
	mkCacheDir("css");
	flushCache("css");
	$output = readFolder("./css/shared/");
	$output .= readFolder($cssFolder);	
	require('./lib/cssmin.php');
	$output = CssMin::minify($output);
	file_put_contents($cssLocation, $output);	
	header("Content-Type: text/css");
	echo $output;
	exit();
}
?>