<?php

// nowplaying class

class lastfm_nowplaying {

	private $api_root = "http://ws.audioscrobbler.com/2.0/";
	private $user_agent = 'nowplaying widget - http://www.icj.me/';
	
	public function __construct($api_key) {
		$this->api_key = $api_key;
	}

	private function is_too_long($string, $size = "medium") {
		if($size == "medium") $len = 30;
		if($size == "tall") $len = 25;
		
		// marquees if the string is too long
		if(strlen($string) >= $len) {
			return '<marquee direction="left" behavior="scroll" scroll="on" scrollamount="3">' . $string . '</marquee>';
		} else {
			return $string;
		}
	}

	private function retrieveData($url) {
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
		
		return curl_exec($ch);
		curl_close($ch);
	}

	public function info($username) {

		$recent_tracks = $this->retrieveData($this->api_root . "?format=json&method=user.getrecenttracks&user=" . $username . "&api_key=" . $this->api_key . "&limit=5");
		$recent_tracks = json_decode($recent_tracks, true);


		// get the users top track's information from what we can
		$track = $recent_tracks['recenttracks']['track']['0'];

		foreach($track as $key => $item) {
			if(is_array($item)) {
				if(isset($item['#text'])) {
					// put this as the key
					$track[$key] = $item['#text'];
				}
			} else {
				$track[$key] = $item;
			}
		}

		// make 'image' only the large one (that we want) and also add in the no artwork if its the case
		$track['image'] = $track['image'][2]['#text'] ? $track['image'][2]['#text'] : 'no_artwork.png';

		// nowplaying info
		$track['nowplaying'] = isset($track['@attr']['nowplaying']);
		unset($track['@attr']); // cleanup

		// load the information from the track api call
		$track_json = $this->retrieveData($this->api_root . "?format=json&method=track.getInfo&username=" . $username . "&api_key=" . $this->api_key . "&artist=" . urlencode($track['artist']) . "&track=" . urlencode($track['name']) . "&autocorrect=1");

		$track_arr = json_decode($track_json, true);
		$track = $track + $track_arr['track'];
		$track['playcount'] = isset($track['userplaycount']) ? $track['userplaycount'] : 1;
		$track['duration'] = $track['duration'] ? gmdate("i:s", ($track['duration'] / 1000)) : '?';
		$track['userloved'] = $track['userloved'] ? '<strong>&#x2764;</strong>' : null;
		$toolongarr = array('artist', 'name', 'album');

		foreach($track as $key => $value) {
			if(in_array($key, $toolongarr)) {
				// do we know the values?
				if(strlen($value) == 0) {
					$track[$key] = "Unknown $key";
					$value = $track[$key]; // fix for line below
				}

				$track[$key] = $this->is_too_long($value);
			}
		}

		// cleanup
		unset($track['id']);
		unset($track['listeners']);
		unset($track['toptags']);
		unset($track['userplaycount']);
		return $track;
	}
}
