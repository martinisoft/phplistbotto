<?
 require("config.php"); 
 
 //$db->open("SELECT songlist.*, historylist.listeners as listeners, historylist.requestID as requestID, historylist.date_played as starttime FROM historylist,songlist WHERE (historylist.songID = songlist.ID) AND (songlist.songtype='S') ORDER BY historylist.date_played DESC LIMIT 6");
 //$history = $db->rows();
 //reset($history);
 
 //$db->open("SELECT songlist.*, queuelist.requestID as requestID FROM queuelist, songlist WHERE (queuelist.songID = songlist.ID)  AND (songlist.songtype='S') AND (songlist.artist <> '') ORDER BY queuelist.sortID ASC LIMIT 5");
 //$queue = $db->rows();
 //reset($queue);
 
 //$db->open("SELECT songlist.*, queuelist.requestID as requestID FROM queuelist, songlist WHERE (queuelist.songID = songlist.ID)  AND (songlist.songtype='S') AND (songlist.artist <> '') ORDER BY queuelist.sortID ASC");
 //$numberWaiting = $db->recordcount();
 
 /*
 list($key, $song) = each($history);
 $listeners = $song["listeners"];

 $starttime = strtotime($song["date_played"]);
 $curtime = time(); 
 $timeleft = $starttime+round($song["duration"]/1000)-$curtime;

  //Set refesh interval
 if($timeleft>0) # 30 second minimum wait
   { $timeout = $timeleft;}		# if timeleft is valid, refresh on timeleft (should be end of song)
 else
   { $timeout = 90; }			# otherwise, fall back on 90 second refresh
   
 if($timeout>180) $timeout = 180;
 if($timeout<30) $timeout = 30;
 */
   
 //$refreshURL = "playing.php?buster=".date('dhis').rand(1,1000);
 $refreshURL = "playing.php";
/* ## ===================================================================== ## */

##################
# Top 10 requests
/*
if($top10requests) {

 $db->open("SELECT songlist.*, requestlist.code as requestcode, count(*) as cnt 
            FROM requestlist, songlist 
			WHERE   (requestlist.songID = songlist.ID) AND
			        (requestlist.code<700) 
			GROUP BY songlist.ID
			ORDER BY cnt DESC, songlist.date_played DESC
			LIMIT 10 ");
			
 $requests = $db->rows();
 
//if(count($requests)>0) 
  //require("top10requests.php");
}
*/
#===================

//NEW: Code
$history = $pdb->query("SELECT songlist.*, historylist.listeners as listeners, historylist.requestID as requestID, historylist.date_played as starttime FROM historylist,songlist WHERE (historylist.songID = songlist.ID) AND (songlist.songtype='S') ORDER BY historylist.date_played DESC LIMIT 6");
$queue = $pdb->query("SELECT songlist.*, queuelist.requestID as requestID FROM queuelist, songlist WHERE (queuelist.songID = songlist.ID)  AND (songlist.songtype='S') AND (songlist.artist <> '') ORDER BY queuelist.sortID ASC LIMIT 5");
$queueWait = $pdb->query("SELECT songlist.*, queuelist.requestID as requestID FROM queuelist, songlist WHERE (queuelist.songID = songlist.ID)  AND (songlist.songtype='S') AND (songlist.artist <> '') ORDER BY queuelist.sortID ASC");
$requests = $pdb->query("SELECT songlist.*, requestlist.code as requestcode, count(*) as cnt FROM requestlist, songlist WHERE (requestlist.songID = songlist.ID) AND (requestlist.code<700) GROUP BY songlist.ID ORDER BY cnt DESC, songlist.date_played DESC LIMIT 10");
$songCount = number_format($pdb->queryOne("SELECT COUNT(id) AS songtotal FROM songlist"));
$requestCount = number_format($pdb->queryOne("SELECT COUNT(id) AS requests FROM requestlist"));
$currentSong = $pdb->fetchInto($history, MDB_FETCHMODE_ASSOC, 0);
$numberWaiting = $pdb->numRows($queueWait);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta name="author" content="Aaron Kalin" />
	<meta name="keywords" content="nsradio, natural selection, listbot" />
	<meta name="description" content="An Automated Jukebox for NSRadio" />
	<meta name="robots" content="all" />
	<title>NSRadio - Listbot</title>

	<!-- to correct the unsightly Flash of Unstyled Content. http://www.bluerobot.com/web/css/fouc.asp -->
	<script type="text/javascript"></script>
	
	<style type="text/css" title="currentStyle">
		@import "styles/default.css";
	</style>
	<script language="JavaScript">
		<!---
			var refreshID = "";
			refreshID = setTimeout("DoRefresh()", 180000);
			
			function DoRefresh()
			{
				document.location.href = '<? echo $refreshURL; ?>';
			}
		//--->
	</script>
	<link rel="Shortcut Icon" type="image/x-icon" href="favicon.ico" />
</head>

<body id="nsradio-listbot">

<div id="container">
	<div id="intro">
		<div id="pageHeader">
			<span class="pageTitle">NSRadio Listbot</span>
			<span class="currentSong"><span class="currentlyPlaying">Currently Playing:</span><span class="songTitle"><? echo $currentSong['artist']." - ".$currentSong['title']; ?></span></span>
		</div>
	</div>

	<div id="linkList">
		<div id="linkList2">
			<div id="lselect">
				<h3 class="select"><span>Navigation:</span></h3>
				<ul id="nav">
					<li><a href="playing.php" title="Main Page">Home</a></li>
					<li><a href="http://chaos.net-addicts.net:8000/listen.pls" title="Tune In!">Tune In!</a></li>
					<li><a href="playlist.php?limit=25" title="See our playlist and make requests!">Playlist &amp; Requests</a></li>
				</ul>
			</div>
			
			<div id="lstats">
				<h3 class="stats"><span>Statistics:</span></h3>
				<ul>
					<li><span class="number"><? echo $currentSong['listeners']; ?> / 200</span><span class="stat">Listeners Tuned In</span></li>
					<li><span class="number"><? echo $online->onlineCount; ?></span><span class="stat"><? echo ($online->onlineCount==1) ? "Person" : "People"; ?> Browsing</span></li>
					<li><span class="number"><? echo $songCount; ?></span><span class="stat">Total Tracks</span></li>
					<li><span class="number"><? echo $requestCount; ?></span><span class="stat">Total Requests</span></li>
				</ul>
			</div>
		</div>
	</div>

	<div id="supportingText">
		<?
		if ($numberWaiting > 0) {
		?>
		<div id="comingUp">
			<table class="comingUp" summary="Coming Up">
				<tr>
					<td colspan="3" class="tableTitle">Coming Up (<? echo $numberWaiting; ?> Songs Waiting):</td>
				</tr>
				<tr>
					<td class="tableHeader">Track</td>
					<td class="tableHeader">Album</td>
					<td class="tableHeader">Time</td>
				</tr>
				<?
					$i = 0;
					while ($comingUp = $pdb->fetchInto($queue, MDB_FETCHMODE_ASSOC)) {
						$rowColor = ($i % 2) ? "firstRow" : "secondRow";
				?>
				<tr class="<? echo $rowColor; ?>">
					<td><? echo (empty($comingUp['artist'])) ? $comingUp['title'] : $comingUp['artist']." - ".$comingUp['title']; ?><? if($comingUp['requestID']!=0) { echo " ~requested~"; }?></td>
					<td><? echo ($comingUp['album']) ? $comingUp['album'] : "&nbsp;" ?></td>
					<td><? echo songTime($comingUp['duration']); ?></td>
				</tr>
				<?
						$i++;
					}
				?>
			</table>
		</div>
		<?
		}
		?>

		<div id="songHistory">
			<table class="songHistory" summary="Last Played">
				<tr>
					<td colspan="4" class="tableTitle">Last Played:</td>
				</tr>
				<tr>
					<td class="tableHeader">Song#</td>
					<td class="tableHeader">Track</td>
					<td class="tableHeader">Album</td>
					<td class="tableHeader">Time</td>
				</tr>
				<?
					for ($i = 1; $i <=5; $i++) {
						$songHistory = $pdb->fetchInto($history, MDB_FETCHMODE_ASSOC, $i);
						$rowColor = ($i % 2) ? "secondRow" : "firstRow";
				?>
				<tr class="<? echo $rowColor; ?>">
					<td><? echo $songHistory['ID']; ?></td>
					<td><? echo (empty($songHistory["artist"])) ? $songHistory['title'] : $songHistory['artist']." - ".$songHistory['title'] ?></td>
					<td><? echo ($songHistory['album']) ? $songHistory['album'] : "&nbsp;" ?></td>
					<td><? echo songTime($songHistory['duration']); ?></td>
				</tr>
				<?
					}	
				?>
			</table>
		</div>

		<div id="topRequests">
			<table class="topRequests" summary="Last Played">
				<tr>
					<td colspan="4" class="tableTitle">Top 10 Requests:</td>
				</tr>
				<tr>
					<td class="tableHeader">#</td>
					<td class="tableHeader">Track</td>
					<td class="tableHeader">Album</td>
					<td class="tableHeader">Time</td>
				</tr>
				<?
					$i = 0;
					while ($topRequests = $pdb->fetchInto($requests, MDB_FETCHMODE_ASSOC)) {
						$rowColor = ($i % 2) ? "firstRow" : "secondRow";
				?>
				<tr class="<? echo $rowColor; ?>">
					<td><? echo $topRequests['cnt']; ?></td>
					<td><? echo (empty($topRequests["artist"])) ? $topRequests['title'] : $topRequests['artist']." - ".$topRequests['title'] ?></td>
					<td><? echo ($topRequests['album']) ? $topRequests['album'] : "&nbsp;" ?></td>
					<td><? echo songTime($topRequests['duration']); ?></td>
				</tr>
				<?
						$i++;
					}
				?>
			</table>
		</div>

		<div id="footer">
			<a href="http://www.nsradio.net/" title="Natural Selection Radio">NSRadio</a> is the official <a href="http://www.natural-selection.org/" title="Natural-Selection - A Half-Life Mod">Natural-Selection</a> radio station. <br />
			Natural Selection is trademark&#8482; <a href="http://www.unknownworlds.com/" title="Unknown Worlds Entertainment">Unknown Worlds Entertainment</a>.<br />
			Copyright &copy; 2004 Aaron L. Kalin - All Rights Reserved. <br />
			This page and its code was built by Theiggsta, Fam and Mr. Headcrab.
		</div>
	</div>
</div>

<!-- These extra divs/spans may be used as catch-alls to add extra imagery. -->
<div id="extraDiv1"><span></span></div><div id="extraDiv2"><span></span></div><div id="extraDiv3"><span></span></div>
<div id="extraDiv4"><span></span></div><div id="extraDiv5"><span></span></div><div id="extraDiv6"><span></span></div>

</body>
</html>
<?
// Free up the memory
$pdb->freeResult($queue);
$pdb->freeResult($history);
$pdb->freeResult($requests);
?>