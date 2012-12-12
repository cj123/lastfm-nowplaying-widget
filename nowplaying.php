<?php
/*
 * Last.fm Now Playing Widget
 * Copyright (c) 2012, Callum Jones
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/* Note: this requires a HTML5 doctype (<!doctype html>) to work correctly if you are including the php file. */

date_default_timezone_set('Europe/London');

// allow iFraming
header('X-Frame-Options: GOFORIT'); 

// your API key here. Sign up at http://www.last.fm/api/account
$api_key = "";

// default last.fm username
$username = "";

// if you want to embed the php file, change this to true
$embedded = false;

// the size of the plugin (defaults to medium)

$size = "medium";

/* ----------------- change below here with caution! ----------------- */

function retrieveData($url) {
	$ch = curl_init();
	$timeout = 5;
	$user_agent = "Now Playing Widget by Callum Jones";
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
	
	return curl_exec($ch);
	curl_close($ch);
}

if($_GET["username"]) {
	$username = $_GET["username"];
}

if($_GET["size"]) {
	$size = $_GET["size"];
} else {
	$size = "medium";
}


if($_GET["autorefresh"] == "no") {
	$autorefresh = false;
} else {
	$autorefresh = true;
}

if($_GET["color"] == "black") {
	$color = "black";
} else {
	$color = "red";
}

$api_root = "http://ws.audioscrobbler.com/2.0/";

// recent tracks
$recent_tracks = retrieveData($api_root . "?method=user.getrecenttracks&user=" . $username . "&api_key=" . $api_key);
$recent_tracks = simplexml_load_string($recent_tracks);

// most recent track information

$trackname = $recent_tracks->recenttracks->track[0]->name;
$artist = $recent_tracks->recenttracks->track[0]->artist;
$album = $recent_tracks->recenttracks->track[0]->album;
$trackurl = $recent_tracks->recenttracks->track[0]->url;
$listendate = '<div id="lastplayed">' . date("d/m/y", strtotime($recent_tracks->recenttracks->track[0]->date)) . '</div>';
$albumart = $recent_tracks->recenttracks->track[0]->image[3]; // large sized album art

if ($recent_tracks->recenttracks->track->attributes()->nowplaying == "true") $playing = true;

if ($albumart == "") $albumart = "no_artwork.png";

function is_too_long($string, $size) {
	if($size == "medium") $len = 30;
	if($size == "tall") $len = 25;
	
	// marquees if the string is too long
	if(strlen($string) >= $len) {
		return '<marquee direction="left" behavior="scroll" scroll="on" scrollamount="3">' . $string . '</marquee>';
	} else {
		return $string;
	}
}

$trackname = is_too_long($trackname, $size);
$artist = is_too_long($artist, $size);
$album = is_too_long($album, $size);

//track information
$track_info = retrieveData($api_root . "?method=track.getInfo&username=" . $username . "&api_key=" . $api_key . "&artist=" . str_replace(' ', '%20', $artist) . "&track=" . str_replace(' ', '%20', $trackname) . "&autocorrect=1");
$track_info = simplexml_load_string($track_info);

$playcount = $track_info->track->userplaycount;
if(!$playcount) $playcount = 1;
$album_link = $track_info->track->album[0]->url;


$duration = gmdate("i:s", ($track_info->track->duration / 1000));

// empties
if($duration == "00:00") $duration = "?";
// for some reason, with the responses, it doesnt unset it, its something... :S
if($album == "") $album = "Unknown album";
if($artist == "") $artist = "Unknown artist";

// get current status of lovin'
if ($track_info->track->userloved == 1) $user_loved = "<strong>&#x2764;</strong> &nbsp; ";

if(!$embedded) {
	$last_fm_plugin = '<!doctype html>
<html>
<head>
	<title>' . $username . ' on last.fm</title>
	<link rel="stylesheet" type="text/css" href="styles/last.fm.css">';
	if ($autorefresh) $last_fm_plugin .= '<meta http-equiv="refresh" content="30">';
$last_fm_plugin .= '</head>
<body>';
}

// create the widget
$last_fm_plugin .= '<div id="lastfm" class="' . $size . ' center">
	<div id="topbar" class="' . $color . '">';
	if($playing) {
		$last_fm_plugin .= 'now playing &middot; last.fm';
	} else {
		$last_fm_plugin .= 'last played &middot; last.fm';
	}
	$last_fm_plugin .= '</div>';
	if($albumart != "") {
		if($album_link != "") {
			$last_fm_plugin .= '<a target="_blank" href="' . $album_link . '"><img id="artwork" src="' . $albumart . '"></a>';
		} else {
			$last_fm_plugin .= '<img id="artwork" src="' . $albumart . '">';
		}
	}
	$last_fm_plugin .= '<div id="songinfo">
		<artist>' . $artist . '</artist>
		<song><a target="_blank" href="' . $trackurl . '">' . $trackname . '</a></song>
		<album>' . $album .'</album>
	</div>
	<div id="userinfo">
		' . $user_loved . '<strong>&#9835;:</strong> ' . $playcount . ' &nbsp; <strong>t:</strong> ' . $duration . ' &nbsp; <strong>u:</strong> <a target="_blank" href="http://www.last.fm/user/' . $username .'">' . $username . '</a>
	</div>';
	
	if (!$playing) {
		// add last listened to date overlayed on artwork
		$last_fm_plugin .= $listendate;
	}
	$last_fm_plugin .= '</div>' . "\n";

if(!$embedded) {
	$last_fm_plugin .= '</body></html>';
}
if($size == "tall") $last_fm_plugin = str_replace(" &nbsp; ", "<br>", $last_fm_plugin);

print $last_fm_plugin;


?>