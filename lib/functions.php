<?php
/*
 * SSAWD functions.php v 1.0
 * Author: Andre Oliveira
 * Date: 10/12/12
 * Link: https://github.com/fiuwebteam/ssawd
 * 
 * This is where functions shared between the applications are stored.
 * 
 */

/*
 * Handles normal file generation
 */

function breakHandler($device, $folder, $tabletIsDesktop, $mobileIsTablet) {
	$output = "";
	switch ($device) {
		case "mobile":
			if (!$mobileIsTablet) {
				$output .= readFolder("./$folder/mobile/");
				break;
			}
		case "tablet":
			if (!$tabletIsDesktop) { 
				$output .= readFolder("./$folder/tablet/");
				break;
			}
		case "desktop":
			$output .= readFolder("./$folder/desktop/");
			break;
	}
	return $output;
}

/*
 * Handles file generation if the script is set to cascade
 */
function cascadeHandler($device, $type, $tabletIsDesktop, $mobileIsTablet) {
	$output = "";
	switch ($device) {
		case "desktop":
			$output = readFolder("./$type/desktop/") . $output;
		case "tablet":
			if (!$tabletIsDesktop) { $output = readFolder("./$type/tablet/") . $output; }
			else { $output = readFolder("./$type/desktop/") . $output; }
		case "mobile":
			if (!$mobileIsTablet) { $output = readFolder("./$type/mobile/") . $output; }
			else if (!$tabletIsDesktop) { $output = readFolder("./$type/tablet/") . $output; } 
			else { $output = readFolder("./$type/desktop/") . $output; }
	}
	return $output;
}

/*
 * Checks if the required folder exists, if it does not, make one.
 */
function chkMkFolder($path) {
	if (!file_exists($path)) {
		if (!mkdir($path)) {
			die("Could not create folder in $path.");
		}
	}
}
/*
 *  Read the device type cookie if available, else, parse the request,
 *  identify the device, and save the cookie for future use.
*/
function deviceType() {
	if ( isset($_COOKIE["device_type"]) ) {
		switch($_COOKIE["device_type"]) {
			case "desktop":
			case "tablet":
			case "mobile":
				$type = $_COOKIE["device_type"];
				break;
			default:
				die("Invalid cookie parameter.");
		}
			
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
	return $type;
}
/*
 * Deletes the contents of a specified folder.
 */
function emptyFolder($folder) {
	$dircontent = scandir($folder);
	$ignoreFiles = array(".", "..");
	foreach($dircontent as $value) {
		if (!in_array($value, $ignoreFiles)) {
			unlink($folder.$value);
		}
	}
}
/*
 * Flushes the specified cashe folder.
 */
function flushCache($cache = null) {
	switch($cache) {
		case "css":
			emptyFolder("./cache/css/");
			break;
		case "js":
			emptyFolder("./cache/js/");
			break;
		case "images":
			emptyFolder("./cache/images/");
			break;		
	}
}
/*
 * Read the contents of a directory and return all the files in one string.
 */
function readFolder($folder) {
	$ignoreFiles = array(".", "..", "README");
	$output = "";
	$dirContent = scandir($folder);
	foreach ($dirContent as $value) {
		if (!in_array($value, $ignoreFiles)) {
			$output .= file_get_contents($folder.$value);
		}
	}
	return $output;
}
/*
 * Make unique identifier for a directory based off of the touch timestamp.
 */
function md5_of_dir($folder) {
	$dircontent = scandir($folder);
	$ret='';
	foreach($dircontent as $filename) {
		if ($filename != '.' && $filename != '..') {
			if (filemtime($folder.$filename) === false) return false;
			$ret.=date("YmdHis", filemtime($folder.$filename)).$filename;
		}
	}
	return md5($ret);
}
/*
 * Make the desired cache folder.
 */
function mkCacheDir($cache = null) {
	chkMkFolder("./cache/");	
	switch($cache) {
		case "css":
			chkMkFolder("./cache/css/");
			break;
		case "js":
			chkMkFolder("./cache/js/");
			break;
		case "images":
			chkMkFolder("./cache/images/");
			break;		
	}
}

?>
