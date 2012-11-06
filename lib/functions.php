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
 * Handles file generation if the script is set to cascade
 */
function cascadeHandler($device, $type) {
	$readFolders = array();
	$output = "";
	switch ($device) {
		case "desktop":
			$output = readFolder("./$type/desktop/") . $output;
			$output = readFolder("./$type/desktop_tablet/") . $output;
			$output = readFolder("./$type/desktop_mobile/") . $output;
			$readFolders[] = "./$type/desktop/";
			$readFolders[] = "./$type/desktop_tablet/";
			$readFolders[] = "./$type/desktop_mobile/";
		case "tablet":
			$output = readFolder("./$type/tablet/"). $output;
			if (!in_array("./$type/desktop_tablet/", $readFolders)) {
				$output = readFolder("./$type/desktop_tablet/"). $output;
				$readFolders[] = "./$type/desktop_tablet/";
			}
			$output = readFolder("./$type/mobile_tablet/"). $output;
			$readFolders[] = "./$type/mobile_tablet/";
		case "mobile":
			if (!in_array("./$type/desktop_mobile/", $readFolders)) {
				$output = readFolder("./$type/desktop_mobile/"). $output;
				$readFolders[] = "./$type/desktop_mobile/";
			}
			if (!in_array("./$type/mobile_tablet/", $readFolders)) {
				$output = readFolder("./$type/mobile_tablet/"). $output;
				$readFolders[] = "./$type/mobile_tablet/";
			}
			$output = readFolder("./$type/mobile/"). $output;
		default:
			$output = readFolder("./$type/shared/"). $output;			
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
 * Deletes the contents of a specified folder older than a specified time.
 */
function emptyFolder($folder, $time) {
	$dircontent = scandir($folder);
	$ignoreFiles = array(".", "..");
	$today = time();
	foreach($dircontent as $value) {
		if (
			!in_array($value, $ignoreFiles) &&
			(($today - $time) >= filemtime($folder.$value))
		) {
			unlink($folder.$value);
		}
	}
}
/*
 * Flushes the specified cashe folder older than a specified time.
 * Default is 30 days.
 */
function flushCache($cache = null, $time = 2592000) {
	switch($cache) {
		case "css":
			emptyFolder("./cache/css/", $time);
			break;
		case "js":
			emptyFolder("./cache/js/", $time);
			break;
		case "images":
			emptyFolder("./cache/images/", $time);
			break;		
	}
}
function getFolders($device, $type) {
	$output = array("./$type/shared/");
	foreach (scandir("./$type/") as $value) {
		if (strpos($value, $device) !== false) {
		    $output[] = "./$type/$value/";
		}
	}
	return $output;
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

function makeFileName($folders, $cascade) {
	$fileName = $cascade;
	foreach($folders as $value) {
		$fileName .= md5_of_dir($value);
	}
	$fileName = md5( $fileName );
	return $fileName;
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
