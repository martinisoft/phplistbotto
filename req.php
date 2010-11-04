<?
require('./global.php');

$song_id = intval($_GET['song_id']);

$info = sendRequest($song_id);

if ($info['requestid'] > 0) {
	$song = $adb->GetRow("SELECT songlist.*, songlist.ID as songID FROM requestlist, songlist WHERE (songlist.ID = requestlist.songID) AND (requestlist.ID = ".$info['requestid'].") LIMIT 1");
	if (!isset($song["songID"])) {
		$song["songID"]=0;
	}
	$song["requestid"] = $info['requestid'];
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">

<head profile="http://gmpg.org/xfn/11">

	<!-- title naming convention: Name - Section Title - Detail -->
	<title>Request Received!</title>

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

	<!-- Favorite Icon Link -->
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />

	<!-- Style Baby Yeah! -->
	<style type="text/css" media="screen">@import "stylesheets/miniscreen.css";</style>

</head>

<body>

<div id="wrap">
	<!-- The Masthead -->
	<!--<div id="header"><p>Your request has been successfully received</p></div>-->

	<div id="content">
		<? if ($info['code']==200) { ?>
		<div id="songinfo">
			<h2>You Requested</h2>
			<p><?= songTitle($song, 1) ?></p>
		</div>
		<p id="thanks">Your request has been queued and will play soon.  Thank you.</p>
		<p id="dedicate">Want to dedicate this song? <a href="dedicate.php?song_id=<?= $song['songID'] ?>&#038;request_id=<?= $song['requestid'] ?>" title="Dedicate this song">Click here</a></p>
		<? } else { ?>
		<div id="alert">
			<h2>Error Playing Request</h2>
			<p><?= $info['message'] ?></p>
		</div>
		<? } ?>
	</div>
</div>
</body>
</html>