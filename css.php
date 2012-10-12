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
require("./css/shared/external.php");
require("./css/$type/external.php");

$cssFolder = "./css/$type/";
// file name changes if the files in the directory have been changed.
$cssFile = md5_of_dir($cssFolder);
$cssLocation = "./cache/css/$cssFile";

if (file_exists($cssLocation)) {
	header("Content-Type: text/css");
	readfile($cssLocation);
	exit();
} else {
	mkCacheDir("css");
	$dircontent = scandir($cssFolder);
	$ignoreFiles = array(".", "..", "README", "external.php");
	$output = "";
	foreach ($dircontent as $value) {
		if (!in_array($value, $ignoreFiles)) {
			$output .= file_get_contents($cssFolder.$value);
		}
	}
	echo $output;
	
}

?>