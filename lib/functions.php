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
		$type = $_COOKIE["device_type"];	
	} else {
		require('./ua-parser/UAParser.php');
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
