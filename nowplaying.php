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

// your API key here. Sign up at http://www.last.fm/api/account
$api_key = "";

// the username you want to get the info from
$username = "";

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

$api_root = "http://ws.audioscrobbler.com/2.0/";

// recent tracks
$recent_tracks = retrieveData($api_root . "?method=user.getrecenttracks&user=" . $username . "&api_key=" . $api_key);
$recent_tracks = simplexml_load_string($recent_tracks);

// most recent track information
$trackname = $recent_tracks->recenttracks->track[0]->name;
$artist = $recent_tracks->recenttracks->track[0]->artist;
$album = $recent_tracks->recenttracks->track[0]->album;
$trackurl = $recent_tracks->recenttracks->track[0]->url;
$albumart = $recent_tracks->recenttracks->track[0]->image[2];

// base64 images, the easiest way to avoid path issues :P
if ($albumart == "") $albumart = "data:image/png;base64,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";

// marquees if string too long (probably will be changed)
if(strlen($trackname) >= 30) $trackname = '<marquee direction="left" behavior="scroll" scroll="on" scrollamount="3">' . $trackname . '</marquee>';
if(strlen($artist) >= 30) $artist = '<marquee direction="left" behavior="scroll" scroll="on" scrollamount="3">' . $artist . '</marquee>';
if(strlen($album) >= 30) $album = '<marquee direction="left" behavior="scroll" scroll="on" scrollamount="3">' . $album . '</marquee>';

//track information
$track_info = retrieveData($api_root . "?method=track.getInfo&username=" . $username . "&api_key=" . $api_key . "&artist=" . str_replace(' ', '%20', $artist) . "&track=" . str_replace(' ', '%20', $trackname) . "&autocorrect=1");
$track_info = simplexml_load_string($track_info);

$playcount = $track_info->track->userplaycount;
$duration = gmdate("i:s", ($track_info->track->duration / 1000));
if ($track_info->track->userloved == 1) $user_loved = "<strong>&#x2764;</strong> &nbsp; ";

// create the widget
print('<div id="lastfm">
	<div id="topbar">
		now playing &middot; last.fm
	</div>
	<img id="artwork" src="' . $albumart . '">
	<div id="songinfo">
		<artist>' . $artist . '</artist>
		<song><a target="_blank" href="' . $trackurl . '">' . $trackname . '</a></song>
		<album>' . $album .'</album>
	</div>
	<div id="userinfo">
		' . $user_loved . '<strong>&#9835;:</strong> ' . $playcount . ' &nbsp; <strong>T:</strong> ' . $duration . ' &nbsp; <strong>U:</strong> <a target="_blank" href="http://www.last.fm/user/' . $username .'">' . $username . '</a>
	</div>
</div>' . "\n");
?>