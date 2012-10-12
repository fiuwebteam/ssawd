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
		case "image":
			chkMkFolder("./cache/images/");
			break;		
	}
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


?>
