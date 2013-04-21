<?php include __DIR__ . "/include/model.php"; ?>
<!doctype html>
<html>
<head>
	<title><?php echo $username; ?> on last.fm</title>
	<link rel="stylesheet" type="text/css" href="styles/last.fm.css">
	<style type="text/css">body { background: <?php echo $bgcolor; ?> }</style>
	<?php if($autorefresh) { ?><meta http-equiv="refresh" content="100"><?php } ?>
</head>
<body>
	<div id="lastfm" class="<?php echo $size; ?> center">
		<div id="topbar" class="<?php echo $color; ?>">
			<?php if($track['nowplaying']) { echo "now playing"; } else { echo "last played"; } ?> &middot; last.fm
		</div>
		<?php if(!empty($track['url'])) { ?><a target="_blank" href="<?php echo $track['url']; ?>"><?php } ?>

			<img id="artwork" src="<?php echo $track['image']; ?>">
		<?php if(!empty($track['url'])) { ?></a><?php } ?>

		<div id="songinfo">
			<artist><?php echo $track['artist']; ?></artist>
			<song><?php echo $track['name']; ?></song>
			<album><?php echo $track['album']; ?></album>
		</div>
		<div id="userinfo">
			<?php echo $track['userloved']; if($track['userloved']) echo $delimiter; ?><strong>&#9835;:</strong> <?php echo $track['playcount'] . $delimiter; ?><strong>t:</strong> <?php echo $track['duration']. $delimiter; ?><strong>u:</strong> <a target="_blank" href="http://www.last.fm/user/<?php echo $username; ?>"><?php echo $username; ?></a>
		</div>
	</div>
</body>
</html>