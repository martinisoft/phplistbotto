<?php
require("../global.php");

$key = 'abc321';

if (isset($_GET['key']) && isset($_GET['songid'])) {
	if ($_GET['key'] == $key) {
		echo "Resetting song...<br/>\n";
		//$sql = "UPDATE songlist SET weight = ".$weight.", count_played = 0, count_requested = 0 WHERE ID = ".$_GET['songid']."";
		$sql = "DELETE FROM requestlist WHERE songID = ".$_GET['songid']."";
		$db->Execute($sql);
		echo "Song reset...<br/>\n";
	}
}
?>