<?

function albumPicture($song, $nolink = 0) {
	global $_SYSTEM;
	if (empty($song['picture'])) {
		$pic = $_SYSTEM['images']."/na.png";
	} else {
		$pic = $_SYSTEM['images']."/albums/".$song['picture'];
	}
	if ($nolink==0) {
		$newtitle = "<a href=\"songinfo.php?song_id=".$song['ID']."\" title=\"Click me for more information on this song\"><img src=\"".$pic."\" alt=\"Album Picture\" /></a>";
	} else {
		$newtitle = "<img src=\"".$pic."\" title=\"".$song['artist']." - ".$song['album']."\"/>";
	}
	return $newtitle;
}

function songTime($duration, $verbose = 0) {
	$ss = round($duration / 1000);
	$mm = (int)($ss / 60);
	$ss = ($ss % 60);
	if ($ss<10) {
		$ss="0$ss";
	}
	if ($verbose == 0) {
		$mmss = "$mm:$ss";
	} else {
		$mmss = "$mm minutes $ss seconds";
	}
	return $mmss;
}

function songTitle($song, $nolink = 0) {
	if (empty($song['artist'])) {
		$newtitle = $song['title'];
	} else {
		$newtitle = $song['artist']." - ".$song['title'];
	}
	if ($nolink==0) {
		$newtitle = "<a href=\"songinfo.php?song_id=".$song['ID']."\" title=\"View Song Information\">".$newtitle."</a>";
	}
	if ($song['requestID']!=0) {
		$newtitle .= " <span>[Requested]</span>";
	}
	return $newtitle;
}

function pluralize($input, $singular, $plural) {
	if ($input==1) {
		return $input.$singular;
	} else {
		return $input.$plural;
	}
}

function section($current, $name) {
	$newtext = "";
	if ($name==$current) {
		$newtext = " class=\"active\"";
	}
	return $newtext;
}

function sendRequest($song_id) {
	global $_SYSTEM;
	$request = "GET /req/?songID=$song_id&host=".urlencode($_SERVER["REMOTE_ADDR"])." HTTP\1.0\r\n\r\n";
	$xmldata = "";
	$fd = @fsockopen($_SYSTEM['samhost'],$_SYSTEM['samport'], $errno, $errstr, 30);
	//$fd = fopen("http://".$_SYSTEM['samhost'].":".$_SYSTEM['samport']."/req/?songID=".$song_id."&host=".urlencode($_SERVER["REMOTE_ADDR"]),"r");
	//echo "fd=$fd";
	if (!empty($fd)) {
		fputs ($fd, $request);
		$line="";
		while(!($line=="\r\n")) {
			$line=fgets($fd,128);
		}
		// strip out the header
		while ($buffer = fgets($fd, 4096)) {
			$xmldata .= $buffer;
		}
		fclose($fd);
	} else {
		return array('code' => 803, 'message' => getError(803, $errstr, $errno));
	}

	if (empty($xmldata)) {
		return array('code' => 804, 'message' => getError(804));
	} else {
		$info = array();
		$xml = simplexml_load_string($xmldata);
		$info['code'] = (int) $xml->status->code;
		$info['message'] = (string) $xml->status->message;
		$info['requestid'] = (int) $xml->status->requestID;
		if (empty($info['code'])) {
			return array('code' => 804, 'message' => getError(804));
		}
		return $info;
	}
}

function getError($code, $errstr = '', $errno = 0) {
	switch ($code) {
		// Level 1 errors
		case 600 : $message = "Requested song is offline and can not be played"; break;
		case 601 : $message = "Song recently played."; break;
		case 602 : $message = "Artist recently played."; break;
		case 603 : $message = "Song already in queue to be played."; break;
		case 604 : $message = "Artist already in queue to be played."; break;
		case 605 : $message = "Song already in request list."; break;
		case 606 : $message = "Artist already in request list."; break;
		// Level 2 Errors
		case 700 : $message = "Invalid Request. (Unknown Error)"; break;
		case 701 : $message = "Banned"; break;
		case 702 : $message = "Banned until mm:ss";  break;
		case 703 : $message = "Requested song ID invalid.";  break;
		case 704 : $message = "Request limit reached.  4 songs per half-hour.";  break;
		case 705 : $message = "Request limit reached.  You can only request 15 songs per day."; break;
		case 706 : $message = "Requests are disabled -- Special Program -- Try again a little later."; break;
		case 707 : $message = "Authorization failed.  IP not in allowed list.";  break;
		case 708 : $message = "You have already requested this song and it is waiting in the request queue to be played.";  break;
		case 709 : $message = "Invalid data returned!";  break;
		case 800 : $message = "Host must be specified"; break;
		case 801 : $message = "Host can not be 127.0.0.1 or localhost"; break;
		case 802 : $message = "Song ID must be valid";  break;
		case 803 : $message = "Unable to connect to $samhost:$samport. Station might be offline.<br />The error returned was $errstr ($errno).";  break;
		case 804 : $message = "Invalid data returned!";  break;
	}
	return $message;
}

function authUser($user, $pass) {
	global $db;
	//$db->query();
	return false;
}

function getIP() {
	if (getenv(HTTP_X_FORWARDED_FOR)) {
		$ip = getenv(HTTP_X_FORWARDED_FOR);
	} else {
		$ip = getenv(REMOTE_ADDR);
	}
	return $ip;
}

function getStats() {
	// Username & Pass
	$user = urlencode("admin");
	$pass = urlencode("nsradioadminP@$$");
	// Connect
	$h = fopen("http://".$user.":".$pass."@67.18.169.2:8080/admin/status.xml", "r");
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
}

?>