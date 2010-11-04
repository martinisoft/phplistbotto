<?
require('./global.php');

if (isset($_GET['song_id'])) {
	$song_id = intval($_GET['song_id']);
	$request_id = intval($_GET['request_id']);
	$dedicated = false;
} else {
	$song_id = intval($_POST['songid']);
	$request_id = intval($_POST['requestid']);
	$name = $_POST['rname'];
	$msg = $_POST['rmessage'];
	if (empty($name)) {
		$name = 'Anonymous';
	}
	$sql = "UPDATE requestlist SET msg = '".addslashes($msg)."', name = '".addslashes($name)."' WHERE ID = '".addslashes($request_id)."'";
	if ($adb->Execute($sql) === false) {
		$error = "Invalid Information Given";
	} else {
		$dedicated = true;
	}
}

//$data["msg"] = "$rmessage";
//$data["name"] = "$rname";
//$adb->update("requestlist",$data,"(ID = $requestid)");
//$adb->open("SELECT * FROM songlist WHERE (ID = $songid)");
//$song = $adb->row();
//$song["requestid"] = $requestid;
//$dedicated = true;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">

<head profile="http://gmpg.org/xfn/11">

	<!-- title naming convention: Name - Section Title - Detail -->
	<title>Dedicate Your Request</title>

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
	<!--<div id="header"><p>Send a dedication message for your selection!</p></div>-->

	<div id="content">
		<? if ($dedicated==false && !isset($error)) { ?>
		<div id="alert">
			<h2>Dedicate Song:</h2>
		</div>
		<form method="post" action="dedicate.php">
			<fieldset>
				<input type="hidden" name="requestid" value="<?= $request_id ?>" />
				<input type="hidden" name="songid" value="<?= $song_id ?>" />
				<label for="rname">Your Name:</label>
				<input type="text" name="rname" size="30" />
				<label for="rname">Your Message:</label>
				<textarea rows="4" name="rmessage" cols="24"></textarea>
				<input type="submit" value="Dedicate it!" name="B1" />
			</fieldset>
		</form>
		<? } else { ?>
		<div id="message">
			<h2>Sucessfully Dedicated Song!</h2>
			<p><?= $msg ?> by <span><?= $name ?></span></p>
		</div>
		<p id="thanks">Dedication Received. Thank you.</p>
		<? } ?>
		<? if (isset($error)) { ?>
		<div id="alert">
			<h2>Error</h2>
			<p><?= $error ?></p>
		</div>
		<? } ?>
	</div>
</div>
</body>
</html>