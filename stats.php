<?php
// Quick PHP Stats Script for Steamcast
// by Aaron "Theiggsta" Kalin

// Username & Pass
$user = urlencode("nsrstaff");
$pass = urlencode("passwordhere");
// Connect
$h = fopen("http://".$user.":".$pass."@67.18.169.2:8000/admin/status.xml", "r");
// Load content
while(!feof($h)) {
	$content .= fread($h,4096);
}
// Drop Handle
fclose($h);
// Load the content
$xml = simplexml_load_string($content);
// I often wonder if people read comments
//print_r($xml);

// Pull up the sources
foreach ($xml->sources->source as $source) {
	// Find the one we want (live)
	if ($source->mount == "/live.mp3") {
		// We've got the one we want, clone it!
		$info = clone $source;
	}
}
// $info is now the stream xml tree, use print_r to inspect it

// HACK: reverse song order (steamcast doesn't show the songs in descending order)
$_songs = array();
foreach ($info->played->song_event as $song) {
	$_songs[] = $song;
}
array_pop($_songs);
$_songs = array_reverse($_songs);
?>
<html>
<head>
	<title>Status</title>
</head>
<body>
<h1>Stats</h1>
<?php if ($info->status != 1) { ?>
<h1><font color="red">OFFLINE</font></h1>
<?php exit(); ?>
<?php } else { ?>
<h1><font color="green">ONLINE</font></h1>
<?php } ?>
<!-- Pick what you want, here's a few usual choices -->
<!-- The Name -->
<p><strong>Name:</strong> <?=$info->name;?></p>
<!-- Description (if they use Icecast2 protocol) -->
<p><strong>Description:</strong> <?=$info->description;?></p>
<!-- URL -->
<p><strong>URL:</strong> <a href="<?=$info->url;?>"><?=$info->url;?></a></p>
<!-- Bitrate (rounded of course) -->
<p><strong>Bitrate:</strong> <?=floor($info->bitrate);?></p>
<!-- Listener Count / Listner Limit (I go by unique count) -->
<p><strong>Listeners:</strong> <?=$info->unique_nodes;?>/<?=$info->max_nodes;?></p>
<!-- How about we show the last 10 songs? -->
<p><h3>Last 10 Songs:</h3>
<?php
$i;
foreach ($_songs as $number => $song) {
	$i++;
	?><strong><?=$i;?>. <?=$song->song_title;?></strong><br/><?php
}
?>
</p>
</body>
</html>