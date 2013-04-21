<?php

date_default_timezone_set('Europe/London');

$api_key = "APIKEY";

// parse options
$username = isset($_GET["username"]) ? $_GET["username"] : "icj_";
$size = isset($_GET["size"]) ? $_GET["size"] : "medium";
$bgcolor = isset($_GET["bgcolor"]) ? $_GET["bgcolor"] : "ffffff";
$autorefresh = isset($_GET["autorefresh"]) && ($_GET["autorefresh"] == "no") ? false : true;
$color = isset($_GET["color"]) ? $_GET["color"] : "red";
$delimiter = ($size == "tall") ? "<br>" : " &nbsp; "; 

// headers
header('X-Frame-Options: GOFORIT'); 
header('Content-Type: text/html; charset=utf-8');

include __DIR__ . "/class.lastfm-nowplaying.php";

try {
	$np = new lastfm_nowplaying($api_key);
	$track = $np->info($username);
} catch (exception $e) {
	printf("error %s", $e);
}

?>
