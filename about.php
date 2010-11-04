<?
require('./global.php');

// Set the section
$_SYSTEM['section'] = 'about';

$history = $adb->Execute("SELECT songlist.*, historylist.listeners as listeners, historylist.requestID as requestID, historylist.date_played as starttime FROM historylist,songlist WHERE (historylist.songID = songlist.ID) AND (songlist.songtype='S') ORDER BY historylist.date_played DESC LIMIT 6");
$currentSong = $history->FetchRow();
// Find out if the song was dedicated
if ($currentSong['requestID'] > 0) {
	$songMessage = $adb->GetRow("SELECT msg, name FROM `requestlist` WHERE `ID` = '".$currentSong['requestID']."'");
}
// Statistics!
$playCount = number_format($adb->GetOne("SELECT COUNT(ID) AS playtotal FROM historylist"));
$requestCount = number_format($adb->GetOne("SELECT COUNT(ID) AS requests FROM requestlist"));
$trackCount = number_format($adb->GetOne("SELECT COUNT(ID) AS tracks FROM songlist"));
$mostTrack = $adb->GetRow("SELECT * FROM songlist ORDER BY count_requested DESC LIMIT 1");
$avgTrack = songTime(round($adb->GetOne("SELECT AVG(duration) FROM songlist WHERE duration BETWEEN 15000 AND 900000")), 1);
//SELECT songlist.artist, songlist.count_requested AS requested  FROM songlist, requestlist WHERE songlist.ID = requestlist.songID GROUP BY artist ORDER BY requested DESC LIMIT 1
$mostArtist = $adb->GetRow("SELECT artist FROM songlist, requestlist WHERE songlist.ID = requestlist.songID GROUP BY artist ORDER BY songlist.count_requested DESC LIMIT 1");
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
			<h2>About Listbot</h2>
			<p>A brief history of our beloved listbot.</p>
			<p class="info">Listbot originally started out as a space filler for all the dead air when Dj's weren't broadcasting and the fact it played a list of music and nothing more.  We'll get to the new nickname part later.  The bot was originally hosted by the stations former owner, Prodigy, and played nothing but Trance/Techno style music and earned the bots infamous reputation.  Even "The Show" made fun of the bot, making a fake show segment with listbot taking over the cast.</p>
			<p class="info">At one point, NSRadio suddenly switched owners and we lost listbot forever.  Soon, we had plenty of dead air and Djs going afk with a list of their music with no solution to fill the missing void of listbots absence.  It was around this time that we devised a better solution to the problem by allowing people to control the bot instead of hearing what the Dj selected to play for a few hours.</p>
			<p class="info">The solution is what you are looking at right now, 2 years in the making and still running.  We used a Dj program and this web interface to control what is essentially a Jukebox.  This solution proved to be very popular with our listeners since they could pick out anything they wanted and it would be played automatically.  The new nickname "Listbotto" came from one of our british Djs "Cartman2b", who thought about the Mr. Roboto song and replaced it with listbot, hence the nickname Listbotto.</p>
			<p class="info">The latest version improves upon the old code and makes things a bit quicker and easier to read and see overall.  Listbot is still a work in progress and we are always updating it with new music or code.</p>
		</div>

		<div class="section">
			<h2>Facts and Information</h2>
			<p>Things you may or may not know about listbotto.</p>
			<dl class="facts">
				<dt>Listbotto is a <strong>Jukebox</strong></dt>
				<dd>Believe it or not, you can actually control Listbotto via the <a href="playlist.php">Playlist & Requests</a> section of this web site.</dd>

				<dt>You can dedicate your requests</dt>
				<dd>Want to dedicate a song to someone?  Click on the dedication link in the popup window when you request the song. The dedication will show up in the "Now Playing" section above every page when the song is played.</dd>

				<dt>Listbotto doesn't have any of my favorite songs!</dt>
				<dd>To date, Listbot has over 4,000 tracks to choose from with more being added all the time.  We are sorry you cannot find anything in the list of music, but encourage you to select songs you haven't heard before so you can hear other styles of music.  Who knows, you might find something you like, it happens all the time!</dd>

				<dt>We cut out the long songs</dt>
				<dd>To keep the playlist moving, we have disabled any tracks 15 minutes or over.  Some songs under 30 seconds are also hidden to show more relevant songs as well.</dd>

				<dt>Can you play more FFT skits?</dt>
				<dd>Why not request a skit?  Search by album for <a href="playlist.php?keyword=%22The+Show%22&filter=album&limit=25" title="Search for the FFT Skits">"The Show"</a> in the playlist page, you'll definately find something.</dd>
			</dl>
		</div>

		<div class="section">
			<h2>Fun Statistics</h2>
			<p>Everybody loves stattistics!</p>
			<dl id="stats">
				<dt><?= $requestCount ?></dt>
				<dd>Total requests to date (Keep in mind that all requests don't always go through.)</dd>
				<dt><?= $playCount ?></dt>
				<dd>Songs heard so far.</dd>
				<dt><?= $trackCount ?></dt>
				<dd>Tracks in the jukebox. (Damn thats a lot of music!)</dd>
				<dt><?= $avgTrack ?></dt>
				<dd>Average Track Length.</dd>
				<dt><?= songTitle($mostTrack) ?></dt>
				<dd>The most requested song.</dd>
				<dt><?= $mostArtist['artist'] ?></dt>
				<dd>The most popular artist.</dd>
				<dt>0</dt>
				<dd>Moogles harmed in the making of this bot. (We care about our Moogles!)</dd>
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