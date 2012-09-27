# Last.fm Now Playing Widget

A simple widget that displays a user's currently played song on [last.fm](http://last.fm).

## Usage

Either:
* load the last.fm widget in an iframe (recommended). e.g. for html5 doctype 
`<iframe src="http://www.icj.me/plugins/nowplaying.php?username=<user>&color=<color>" seamless></iframe>` 
replacing `<user>` with your username and `<color>` with either **red** or **black**. If you wish to prevent autorefresh, add `&autorefresh=no`
* load the php code directly into your site. First import the stylesheet
e.g. `<link rel="stylesheet" type="text/css" href="last.fm.css">`
and then modify the nowplaying.php to contain your API key and username, and load it into the PHP of your site 
e.g. `include('nowplaying.php');`
