<?php

// Prepared SQL Queries
// Used for PEAR::DB to easily modify all queries in one spot
// TODO: Figure out a more efficient query set

/*
$history = $adb->Execute("SELECT songlist.*, historylist.listeners as listeners, historylist.requestID as requestID, historylist.date_played as starttime FROM historylist,songlist WHERE (historylist.songID = songlist.ID) AND (songlist.songtype='S') ORDER BY historylist.date_played DESC LIMIT 6");
$queue = $adb->Execute("SELECT songlist.*, queuelist.requestID as requestID FROM queuelist, songlist WHERE (queuelist.songID = songlist.ID)  AND (songlist.songtype='S') AND (songlist.artist <> '') ORDER BY queuelist.sortID ASC LIMIT 5");
$queueWait = $adb->Execute("SELECT songlist.*, queuelist.requestID as requestID FROM queuelist, songlist WHERE (queuelist.songID = songlist.ID)  AND (songlist.songtype='S') AND (songlist.artist <> '') ORDER BY queuelist.sortID ASC");
$requests = $adb->Execute("SELECT songlist.*, requestlist.code as requestcode, count(*) as cnt FROM requestlist, songlist WHERE (requestlist.songID = songlist.ID) AND (requestlist.status='played') GROUP BY songlist.ID ORDER BY cnt DESC, songlist.date_played DESC LIMIT 10");
*/


?>