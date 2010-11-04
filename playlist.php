<?

//
// Retired Code Section (Mainly for reference, keeps things clean)
//

//SELECT * FROM songlist  WHERE (songtype='S') AND (status=0)  ORDER BY artist ASC, title ASC LIMIT 0,25

// Scrub the search (temp)
//$where = " AND (title LIKE '%".addslashes($search)."%') OR (artist LIKE '%".addslashes($search)."%') OR (album LIKE '%".addslashes($search)."%') ";

/* -- OLD
switch ($filter) {
	case 'all':
		$where .= "AND (title LIKE '%".addslashes($search)."%') OR (artist LIKE '%".addslashes($search)."%') OR (album LIKE '%".addslashes($search)."%')";
		break;
	case 'artist':
		$where .= "AND (artist LIKE '%".addslashes($search)."%')";
		break;
	case 'song':
		$where .= "AND (title LIKE '%".addslashes($search)."%')";
		break;
	case 'album':
		$where .= "AND (album LIKE '%".addslashes($search)."%')";
		break;
	default:
		$filter = 'all';
		$where = "";
}

// -- OLD (for FULLTEXT searches)
switch ($filter) {
	case 'artist':
		$scol = 'artist';
		break;
	case 'song':
		$scol = 'title';
		break;
	case 'album':
		$scol = 'album';
		break;
	case 'all':
		$scol = 'artist, title, album';
		break;
	default:
		$filter = 'all';
		$scol = 'artist, title, album';
}
*/

//$searchResult = $pdb->query("SELECT * FROM songlist WHERE (songtype='S') AND (status=0) $where ORDER BY artist ASC, title ASC LIMIT ".$pager->getRange1().",$limit");
// Quick fix - disallow long songs (Change to BETWEEN() SQL later)
//$searchResult = $pdb->query("SELECT * FROM songlist WHERE (songtype='S') AND (status=0) $where AND duration < 900000 ORDER BY artist ASC, title ASC LIMIT ".$pager->getRange1().",$limit");

// Includes
require_once('./global.php');
require_once("./common/paginator.php");
require_once("./common/paginator_html.php");

// Set the section
$_SYSTEM['section'] = 'playlist';

// Song History
$history = $adb->Execute("SELECT songlist.*, historylist.listeners as listeners, historylist.requestID as requestID, historylist.date_played as starttime FROM historylist,songlist WHERE (historylist.songID = songlist.ID) AND (songlist.songtype='S') ORDER BY historylist.date_played DESC LIMIT 1");
// Current Song
$currentSong = $history->FetchRow();
// Find out if the song was dedicated
if ($currentSong['requestID'] > 0) {
	$songMessage = $adb->GetRow("SELECT msg, name FROM `requestlist` WHERE `ID` = '".$currentSong['requestID']."'");
}

// Search Subsystem

// Search Term
if (isset($_GET['keyword'])) {
	$search = $_GET['keyword'];
} else {
	$search = $_GET['search'];
}
// Filter (all, artist, song, album)
$filter = $_GET['filter'];
// Limit (5, 10, 25, 50, 100) (Max 100)
$limit = $_GET['limit'];
// Start (record start)
$start = $_GET['start'];

// Scrub the limit
$limit = intval($limit);
if (empty($limit)) {
	$limit = 25;
} elseif ($limit >= 100) {
	$limit = 100;
}

// Scrub the record start
$start = intval($start);
if (empty($start)) {
	$start = 0;
} elseif ($start >= 100) {
	$start = 100;
}

// Scrub the search filter
$where = "";

// Search
switch ($filter) {
	case 'artist':
		$scol = 'artist';
		$skeys = array('artist');
		break;
	case 'song':
		$scol = 'title';
		$skeys = array('artist');
		break;
	case 'album':
		$scol = 'album';
		$skeys = array('artist');
		break;
	case 'all':
		$scol = 'artist, title, album';
		$skeys = array('artist', 'title', 'album');
		break;
	default:
		$filter = 'all';
		$scol = 'artist, title, album';
		$skeys = array('artist', 'title', 'album');
}

if (!empty($search)) {
	//$where = "AND MATCH($scol) AGAINST('".addslashes($search)."*' IN BOOLEAN MODE) ";
	//$where = "AND $scol LIKE '%".addslashes($search)."%' ";
	foreach ($skeys as $skey) {
		$where .= "AND ($skey LIKE '%".addslashes($search)."%') ";
	}
} else {
	$where = '';
}

// Note -- Make this more efficient later, is this really needed?
if (strlen($search) == 1) {
	$nextletter = chr(ord($search)+1);
	if ($search == '0') {
		$where .= "AND NOT((artist>='A') AND (artist<'ZZZZZZZZZZZ')) ";
	} else {
		$where .= "AND ((artist>='".addslashes($search)."') AND (artist<'$nextletter')) ";
	}
}

// Get Count (NEW)
//$count =& $db->getOne("SELECT COUNT(*) AS number FROM songlist WHERE (songtype='S') AND (status=0) $where AND duration BETWEEN 15000 AND 900000");

// Get Count (OLD)
$count = $adb->GetOne("SELECT COUNT(*) AS number FROM songlist WHERE (songtype='S') AND (status=0) $where ");

// Screw up the search keyword(s) for URL use
$search = urlencode($search);

// Paginator
$currentPage = intval($_GET['page']);
if (empty($currentPage)) {
	$currentPage = 1;
}
$pager =& new Paginator_html($currentPage, $count);
$pager->set_Limit($limit);
$pager->set_Links(3);
$pager->initVars();
$pager->setVar("filter", $filter);
$pager->setVar("search", $search);

// Run Query
$searchResult = $adb->Execute("SELECT * FROM songlist WHERE (songtype='S') AND (status=0) $where AND duration BETWEEN 15000 AND 900000 ORDER BY artist ASC, title ASC LIMIT ".$pager->getRange1().",$limit");

$resultCount = number_format($adb->GetOne("SELECT COUNT(ID) FROM songlist WHERE (songtype='S') AND (status=0) $where AND duration BETWEEN 60000 AND 900000"));
$songCount = number_format($adb->GetOne("SELECT COUNT(ID) AS songtotal FROM songlist"));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">

<head profile="http://gmpg.org/xfn/11">

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
		reqwin = window.open("req.php?song_id="+song_id, "_AR_request", "location=no,status=no,menubar=no,scrollbars=no,resizeable=yes,height=280,width=668");
	}
	</script>

	<!-- Favorite Icon Link -->
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />

	<!-- Swingin' Style Baby, Yeah! -->
	<style type="text/css" media="screen">@import "stylesheets/screen.css";</style>

</head>

<body>

<div id="wrap">
	<!-- The Masthead -->
	<div id="header"></div>

	<? require_once('./common/partials/_nav.php'); ?>

	<div id="content">
		<? require_once('./common/partials/_nowplaying.php'); ?>

		<form method="get" action="playlist.php">
		<div class="section">
			<h2>Search Playlist:</h2>
			<p><?= $resultCount ?><?= ($resultCount==1) ? ' Result' : ' Results' ?> Returned</p>
			<fieldset>
				<label for="keyword">Keyword:</label>
				<input type="text" size="25" id="keyword" name="keyword" value="<?= htmlentities(urldecode($search)) ?>" />
				<label for="filter">Search By:</label>
				<select id="filter" name="filter">
					<option value="all"<?= ($filter=='all') ? ' selected="selected"' : ''; ?>>All</option>
					<option value="artist"<?= ($filter=='artist') ? ' selected="selected"' : ''; ?>>Artist</option>
					<option value="song"<?= ($filter=='song') ? ' selected="selected"' : ''; ?>>Song</option>
					<option value="album"<?= ($filter=='album') ? ' selected="selected"' : ''; ?>>Album</option>
				</select>
				<label for="limit">Limit:</label>
				<select id="limit" name="limit">
					<option value="5"<?= ($limit==5) ? ' selected="selected"' : ''; ?>>5</option>
					<option value="10"<?= ($limit==10) ? ' selected="selected"' : ''; ?>>10</option>
					<option value="25"<?= ($limit==25) ? ' selected="selected"' : ''; ?>>25</option>
					<option value="50"<?= ($limit==50) ? ' selected="selected"' : ''; ?>>50</option>
					<option value="100"<?= ($limit==100) ? ' selected="selected"' : ''; ?>>100</option>
				</select>
				<label for="submit">&nbsp;</label>
				<input id="submit" name="submit" type="submit" value="search" />
			</fieldset>
		</div>
		</form>

		<div class="section">
			<h2>Quick Search By Artist:</h2>
			<div id="quickSearch">
				<ul>
					<li><a href="playlist.php?limit=25" title="All">All</a></li>
					<li><a href="playlist.php?search=A&#038;filter=artist&#038;page=1&#038;limit=25" title="A">A</a></li>
					<li><a href="playlist.php?search=B&#038;filter=artist&#038;page=1&#038;limit=25" title="B">B</a></li>
					<li><a href="playlist.php?search=C&#038;filter=artist&#038;page=1&#038;limit=25" title="C">C</a></li>
					<li><a href="playlist.php?search=D&#038;filter=artist&#038;page=1&#038;limit=25" title="D">D</a></li>
					<li><a href="playlist.php?search=E&#038;filter=artist&#038;page=1&#038;limit=25" title="E">E</a></li>
					<li><a href="playlist.php?search=F&#038;filter=artist&#038;page=1&#038;limit=25" title="F">F</a></li>
					<li><a href="playlist.php?search=G&#038;filter=artist&#038;page=1&#038;limit=25" title="G">G</a></li>
					<li><a href="playlist.php?search=H&#038;filter=artist&#038;page=1&#038;limit=25" title="H">H</a></li>
					<li><a href="playlist.php?search=I&#038;filter=artist&#038;page=1&#038;limit=25" title="I">I</a></li>
					<li><a href="playlist.php?search=J&#038;filter=artist&#038;page=1&#038;limit=25" title="J">J</a></li>
					<li><a href="playlist.php?search=K&#038;filter=artist&#038;page=1&#038;limit=25" title="K">K</a></li>
					<li><a href="playlist.php?search=L&#038;filter=artist&#038;page=1&#038;limit=25" title="L">L</a></li>
					<li><a href="playlist.php?search=M&#038;filter=artist&#038;page=1&#038;limit=25" title="M">M</a></li>
					<li><a href="playlist.php?search=N&#038;filter=artist&#038;page=1&#038;limit=25" title="N">N</a></li>
					<li><a href="playlist.php?search=O&#038;filter=artist&#038;page=1&#038;limit=25" title="O">O</a></li>
					<li><a href="playlist.php?search=P&#038;filter=artist&#038;page=1&#038;limit=25" title="P">P</a></li>
					<li><a href="playlist.php?search=Q&#038;filter=artist&#038;page=1&#038;limit=25" title="Q">Q</a></li>
					<li><a href="playlist.php?search=R&#038;filter=artist&#038;page=1&#038;limit=25" title="R">R</a></li>
					<li><a href="playlist.php?search=S&#038;filter=artist&#038;page=1&#038;limit=25" title="S">S</a></li>
					<li><a href="playlist.php?search=T&#038;filter=artist&#038;page=1&#038;limit=25" title="T">T</a></li>
					<li><a href="playlist.php?search=U&#038;filter=artist&#038;page=1&#038;limit=25" title="U">U</a></li>
					<li><a href="playlist.php?search=V&#038;filter=artist&#038;page=1&#038;limit=25" title="V">V</a></li>
					<li><a href="playlist.php?search=W&#038;filter=artist&#038;page=1&#038;limit=25" title="W">W</a></li>
					<li><a href="playlist.php?search=X&#038;filter=artist&#038;page=1&#038;limit=25" title="X">X</a></li>
					<li><a href="playlist.php?search=Y&#038;filter=artist&#038;page=1&#038;limit=25" title="Y">Y</a></li>
					<li><a href="playlist.php?search=Z&#038;filter=artist&#038;page=1&#038;limit=25" title="Z">Z</a></li>
				</ul>
			</div>
		</div>

		<div class="section">
			<h2>Playlist:</h2>
			<p><?= $songCount ?> Total Songs</p>
			<table>
				<thead>
					<tr>
						<th>Song#</th>
						<th>Track</th>
						<th>Request</th>
						<th>Album</th>
						<th>Time</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i = 0;
					while ($song = $searchResult->FetchRow()) {
						$rowColor = ($i % 2) ? '' : ' class="alternate"';
					?>
					<tr<?= $rowColor ?>>
						<td><?= $song['ID'] ?></td>
						<td><?= songTitle($song) ?></td>
						<td><a href="javascript:request(<?= $song['ID'] ?>)" class="request">Request</a></td>
						<td><?= ($song['album']) ? $song['album'] : "&nbsp;" ?></td>
						<td><?= songTime($song['duration']) ?></td>
					</tr>
					<?
						$i++;
					}
					?>
				</tbody>
			</table>
			<div class="playListNav">
				<?= $pager->pageLinks() ?>
			</div>
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