<?
//Turn site on/off (yeah I know its lazy)
exit();

/* ## ======================================== ## */
$_SYSTEM = array();

// Your MySQL database login details
$_SYSTEM['dsn'] = "mysqli://samdb:samdb@localhost/samdb";

// The IP address of the machine SAM is running on (DO NOT use a local IP address like 127.0.0.1 or 192.x.x.x)
$_SYSTEM['samhost'] = "theiggsta.mine.nu";

// The port SAM handles HTTP requests on. Usually 1221.
$_SYSTEM['samport'] = 1221;

// Title
$_SYSTEM['title'] = "NSRadio Listbotto 2.6 - Domo Aragato Mister Listbotto!";

// Images Folder
$_SYSTEM['images'] = '/listbot/images';

// Root dir
$_SYSTEM['root'] = 'D:\\webroot\\listbot';

/* ## ======================================== ## */

require_once($_SYSTEM['root'].'/common/functions.php');
require_once($_SYSTEM['root'].'/common/adodb/adodb.inc.php');
$adb = NewADOConnection($_SYSTEM['dsn']);
$adb->SetFetchMode(ADODB_FETCH_ASSOC);
/*
require_once('./common/adodb/session/adodb-session.php');
$GLOBALS['ADODB_SESS_CONN'] = &$db;
ADODB_Session::open(false, false, false);
session_start();

$_SESSION['userid'] = 0;
$adb->Execute("UPDATE sessions SET ip = '".getIP()."' WHERE sesskey = '".session_id()."'");
$sessionCount = $adb->GetOne("SELECT DISTINCT COUNT(ip) AS active_sessions FROM sessions WHERE expiry > ".time()."");
*/

// PEAR Changeover, never completed, just started.
//require_once 'DB.php';
//$db =& DB::connect($_SYSTEM['dsn']);
//if (PEAR::isError($db)) {
//    die($db->getMessage());
//}
//$db->setFetchMode(DB_FETCHMODE_ASSOC);
?>