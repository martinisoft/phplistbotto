<?
/* ## ===================================================================== ## */
require('./global.php');

// Set the section
$_SYSTEM['section'] = 'home';

##################
# Top 10 requests
/*
if($top10requests) {

 $adb->open("SELECT songlist.*, requestlist.code as requestcode, count(*) as cnt 
            FROM requestlist, songlist 
			WHERE   (requestlist.songID = songlist.ID) AND
			        (requestlist.code<700) 
			GROUP BY songlist.ID
			ORDER BY cnt DESC, songlist.date_played DESC
			LIMIT 10 ");
			
 $requests = $adb->rows();
 
//if(count($requests)>0) 
  //require("top10requests.php");
}
*/
#===================

//NEW: Code
$history = $adb->Execute("SELECT songlist.*, historylist.listeners as listeners, historylist.requestID as requestID, historylist.date_played as starttime FROM historylist,songlist WHERE (historylist.songID = songlist.ID) AND (songlist.songtype='S') ORDER BY historylist.date_played DESC LIMIT 6");
$queue = $adb->Execute("SELECT songlist.*, queuelist.requestID as requestID FROM queuelist, songlist WHERE (queuelist.songID = songlist.ID)  AND (songlist.songtype='S') AND (songlist.artist <> '') ORDER BY queuelist.sortID ASC LIMIT 5");
$queueWait = $adb->Execute("SELECT songlist.*, queuelist.requestID as requestID FROM queuelist, songlist WHERE (queuelist.songID = songlist.ID)  AND (songlist.songtype='S') AND (songlist.artist <> '') ORDER BY queuelist.sortID ASC");
$requests = $adb->Execute("SELECT songlist.*, requestlist.code as requestcode, count(*) as cnt FROM requestlist, songlist WHERE (requestlist.songID = songlist.ID) AND (requestlist.status='played') GROUP BY songlist.ID ORDER BY cnt DESC, songlist.date_played DESC LIMIT 10");
$currentSong = $history->FetchRow();
// Find out if the song was dedicated
if ($currentSong['requestID'] > 0) {
	$songMessage = $adb->GetRow("SELECT msg, name FROM `requestlist` WHERE `ID` = '".$currentSong['requestID']."'");
}
$numberWaiting = $queueWait->RecordCount();
$playCount = number_format($adb->GetOne("SELECT COUNT(ID) AS playtotal FROM historylist"));
$requestCount = number_format($adb->GetOne("SELECT COUNT(ID) AS requests FROM requestlist"));
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

		<?
		if ($numberWaiting > 0) {
		?>
		<div class="section">
			<h2>Coming Up...</h2>
			<p><?= pluralize($numberWaiting, ' Song', ' Songs') ?> waiting in line</p>
			<table>
				<thead>
					<tr>
						<th>Track</th>
						<th>Album</th>
						<th>Time</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i = 0;
					while ($comingUp = $queue->FetchRow()) {
						$rowColor = ($i % 2) ? '' : ' class="alternate"';
					?>
					<tr<?= $rowColor ?>>
						<td><?= songTitle($comingUp) ?></td>
						<td><?= ($comingUp['album']) ? $comingUp['album'] : "&nbsp;" ?></td>
						<td><?= songTime($comingUp['duration']) ?></td>
					</tr>
					<?
						$i++;
					}
					?>
				</tbody>
			</table>
		</div>
		<?
		}
		?>

		<div class="section">
			<h2>Recently Played:</h2>
			<p><?= $playCount ?> Songs heard so far</p>
			<table>
				<thead>
					<tr>
						<th>Track</th>
						<th>Album</th>
						<th>Time</th>
					</tr>
				</thead>
				<tbody>
					<?
					for ($i = 1; $i <=5; $i++) {
						$songHistory = $history->FetchRow();
						if (empty($songHistory)) {
							break;
						}
						$rowColor = ($i % 2) ? '' : ' class="alternate"';
					?>
					<tr<?= $rowColor ?>>
						<td><?= songTitle($songHistory) ?></td>
						<td><?= ($songHistory['album']) ? $songHistory['album'] : "&nbsp;" ?></td>
						<td><?= songTime($songHistory['duration']) ?></td>
					</tr>
					<?
					}
					?>
				</tbody>
			</table>
		</div>

		<div class="section">
			<h2>Top 10 Requests:</h2>
			<p><?= $requestCount ?> Total requests to date</p>
			<table>
				<thead>
					<tr>
						<th>Track</th>
						<th>Album</th>
						<th>Time</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i = 0;
					while ($topRequests = $requests->FetchRow()) {
						$rowColor = ($i % 2) ? '' : ' class="alternate"';
					?>
					<tr<?= $rowColor ?>>
						<td><?= songTitle($topRequests) ?></td>
						<td><?= ($topRequests['album']) ? $topRequests['album'] : "&nbsp;" ?></td>
						<td><?= songTime($topRequests['duration']) ?></td>
					</tr>
					<?
						$i++;
					}
					?>
				</tbody>
			</table>
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