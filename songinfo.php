<?php
require('./global.php');

$song_id = intval($_GET['song_id']);
$song = $adb->GetRow("SELECT * FROM songlist WHERE ID = ".addslashes($song_id)."");

// Song History
$history = $adb->Execute("SELECT songlist.*, historylist.listeners as listeners, historylist.requestID as requestID, historylist.date_played as starttime FROM historylist,songlist WHERE (historylist.songID = songlist.ID) AND (songlist.songtype='S') ORDER BY historylist.date_played DESC LIMIT 1");
// Current Song
$currentSong = $history->FetchRow();
// Find out if the song was dedicated
if ($currentSong['requestID'] > 0) {
	$songMessage = $adb->GetRow("SELECT msg, name FROM `requestlist` WHERE `ID` = '".$currentSong['requestID']."'");
}

//require("req/request.java.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">

<head profile="http://gmpg.org/xfn/11">

	<!-- title naming convention: Name - Section Title - Detail -->
	<title><?= $_SYSTEM['title'] ?></title>

	<!-- Meta tags -->
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="author" content="Aaron Kalin" />
	<meta name="copyright" content="Copyright &copy; 2004-2005 Aaron Kalin" />
	<meta name="description" content="NSRadio's List Bot" />
	<meta name="keywords" content="listbot, natural selection, nsradio, request" />
	<meta name="dc.title" content="NSRadio List Bot" />
	<meta name="robots" content="noindex, follow, noarchive" />

	<!-- To correct the unsightly Flash of Unstyled Content. http://www.bluerobot.com/web/css/fouc.asp -->
	<script type="text/javascript"></script>

	<script type="text/javascript">
	function request(song_id) {
		reqwin = window.open("req.php?song_id="+song_id, "_AR_request", "location=no,status=no,menubar=no,scrollbars=no,resizeable=yes,height=420,width=668");
	}
	</script>

	<!-- Favorite Icon Link -->
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />

	<!-- Style Baby Yeah! -->
	<style type="text/css" media="screen">@import "stylesheets/screen.css";</style>

</head>

<body>

<div id="wrap">
	<!-- The Masthead -->
	<div id="header"></div>

	<? require_once('./common/partials/_nav.php'); ?>

	<div id="content">
		<? require_once('./common/partials/_nowplaying.php'); ?>

		<div class="section">
			<h2>Song Information:</h2>
			<dl id="songInfo">
				<dt>Album Cover</dt>
				<dd><?= albumPicture($song, 1) ?></dd>
				<dt>Song Title</dt>
				<dd><?= $song['title'] ?></dd>
				<dt>Artist</dt>
				<dd><?= $song['artist'] ?></dd>
				<dt>Album</dt>
				<dd><?= ($song['album']) ? $song['album'] : 'n/a' ?></dd>
				<dt>Year</dt>
				<dd><?= $song['albumyear'] ?></dd>
				<dt>Genre</dt>
				<dd><?= $song['genre'] ?></dd>
				<dt>Length</dt>
				<dd><?= songTime($song['duration']) ?></dd>
				<dt>Weight</dt>
				<dd><?= $song['weight'] ?>/100 [Lower means less rotation]</dd>
				<dt>Date Added</dt>
				<dd><?= date("F jS, Y", $adb->UnixDate($song['date_added'])) ?></dd>
				<?php if ($song['count_requested']>0) { ?>
				<dt>Last Requested</dt>
				<dd><?= date("F jS, Y", $adb->UnixDate($song['last_requested'])) ?></dd>
				<?php } ?>
				<dt>Played</dt>
				<dd><?= pluralize($song['count_played'], ' Time', ' Times') ?></dd>
				<dt>Requested</dt>
				<dd><?= pluralize($song['count_requested'], ' Time', ' Times') ?></dd>
				<dt>Request Song</dt>
				<dd><a href="javascript:request(<?= $song['ID'] ?>)" class="request">Request</a></dd>
			</dl>
		</div>

		<div id="copyright">
			<a href="http://www.nsradio.net/" title="Natural Selection Radio">NSRadio</a> is the official <a href="http://www.naturalselection.com/" title="Natural-Selection - A Half-Life Mod">Natural-Selection</a> radio station. <br />
			Natural Selection is trademark&#8482; <a href="http://www.unknownworlds.com/" title="Unknown Worlds Entertainment">Unknown Worlds Entertainment</a>.<br />
			Copyright &copy; 2004-2005 Aaron Kalin - Some Rights Reserved. <br />
			Powered by Gorges and 1337ies.
		</div>
	</div>
</div>

</body>
</html>
