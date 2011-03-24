<?php
/*******************************************************************************
    The Kingdoms of Chaos - An online browser text game - <http://www.tkoc.net>
    Copyright (C) 2011 - Administrators of The Kingdoms of Chaos

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

    Contact Information:
	Petros Karipidis  - petros@rufunka.com - <http://www.rufunka.com/>
	Anastasios Nistas - tasosos@gmail.com  - <http://tasos.pavta.com/>
	
	Other Information
	=================
	The exact Author of each source file should be specified after this license
	notice. If not specified then the "Current Administrators" found at
	<http://www.tkoc.net/about.php> are considered the Authors of the source
	file.

	As stated at the License Section 5.d: "If the work has interactive user
	interfaces, each must display Appropriate Legal Notices; however, if the
	Program has interactive interfaces that do not display Appropriate Legal
	Notices, your work need not make them do so.", we require you give
	credits at the appropriate section of your interface.
********************************************************************************/
?>
<?php
//**********************************************************************
//* all.inc.php
//*
//* This should be included in every script.
//*
//* - This setup a database object and put it in global
//* - Will load the game config and put it in global
//* - includes FU (frequently used) functions
//**********************************************************************
// setup error control
//ini_set('error_reporting', 0 );
//ini_set('log_errors', 0 );
//ini_set("display_errors", "1");
//set_magic_quotes_runtime(0);
// tata!
#header("Expires: Mon, 03 Mar 2003 00:00:00 GMT");
#header("Last Modifies:" . gmdate("D, d M Y H:i:s") . "GMT");
#header("Cache-Control: no-cahce, must-revalidate");
#header("Pragma: no-cache");
$fcid_post = $showmenu = $fcid = $num = "";
// first, set up the global variables.
require_once ('globals.inc.php');
require_once ($GLOBALS['maindomain']."worldforum/SSI.php");

// setup db object
$DBLOGIN = $dbusername;
$DBPASSW = $dbpassword;
$DBHOST  = $dbhost;
$DBDATABASE = $dbOldGame;
require_once ($GLOBALS['path_www_scripts'] . 'Database.class.inc.php');
$database = new Database($DBLOGIN,$DBPASSW,$DBHOST,$DBDATABASE);
$GLOBALS['database'] = $database;
$GLOBALS['database']->connect();

require_once ($GLOBALS['path_includes']."database.php");

$GLOBALS["forumdb"] = new myDatabase ($dbhost,$dbusername,$dbpassword, $dbForum);
$GLOBALS["db"] = new myDatabase ($dbhost,$dbusername,$dbpassword, $dbOldGame);


// load game-config
$database->query("SELECT * from Config");
$config = $database->fetchArray();
$GLOBALS['config'] = $config;


function GetStatLinks()
{
$body = "";                      
   $body .= "   <table frame='box' border='0' width='60%'>";
   $body .= "      <tr>";
   $body .=  "        <td class='rep1' width='33%' align='center'><a href='report.php' class='rep'><b>Search Kingdoms</b></a></td>";
   $body .=  "        <td class='rep1' width='33%' align='center'><a href='top50.php' class='rep'><b>Provinces Ranking</b></a></td>";
   $body .=  "        <td class='rep1' width='33%' align='center'><a href='topKing.php' class='rep'><b>Kingdoms Ranking</b></a></td>";

   $body .= "      </tr>";
   $body .= "   </table>";
return $body;
}


// Networth definitions..

define("NW_GOLD",0.001);
define("NW_FOOD",0.0005);
define("NW_METAL",0.001);
define("NW_PEASANTS",1.0);
// army
define("NW_SOLDIER",2.0);
define("NW_OFF",4.0);
define("NW_DEF",4.0);
define("NW_ELITE",8.0);
define("NW_THIEVES",5.0);
define("NW_MAGES",5.0);
//buildings
define("NW_ACRE",15.0);
define("NW_BUILDING",15.0);

// sciences
define("NW_SCIENCE",1000.0);

//Startvalues for era

$STARTYEAR = 2003;
$STARTMONTH = 4;
$STARTDAY = 9;
$STARTHOUR = 0;
$STARTMIN = 0;


function writeChaosNumber ($num)
{
	if (is_numeric($num))
		return 	number_format($num,0,' ',',');
	else return $num;
}

function DebugOut($text)
{
	if ((isset($GLOBALS['game_debug']) && $GLOBALS['game_debug'] == true))
	{
		echo "<b>Debug:</b>".$text;
	}
}
function Debug($text)
{
	return DebugOut($text);
}


//**********************************************************************
//*   templateDisplay($province, $htmlBody)
//*   
//*   takes the province object for the info at the top of the page
//*   and the string htmlBody that contains the body of the page
//* 	 added $forum as a little hack to display form correct.
//**********************************************************************
//  $left="<img src='../img/space.gif'</img>"
function templateDisplay($province, $htmlBody , $cornerpic="../img/space.gif", $left="",$forum=false ) {
	// At the beginning of each page call these two functions
	$province->setNetworth();
	//ob_start("ob_gzhandler");
	//ob_implicit_flush(0);
	// Include this function on your pages
	function print_gzipped_page() {
		global $HTTP_ACCEPT_ENCODING;
		if( headers_sent() ){
			$encoding = false;
		}elseif( strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') !== false ){
			$encoding = 'x-gzip';
		}elseif( strpos($HTTP_ACCEPT_ENCODING,'gzip') !== false ){
			$encoding = 'gzip';
		}else{
			$encoding = false;
		}
	
		if( $encoding ){
			$contents = ob_get_contents();
			ob_end_clean();
			header('Content-Encoding: '.$encoding);
			print("\x1f\x8b\x08\x00\x00\x00\x00\x00");
			$size = strlen($contents);
			$contents = gzcompress($contents, 9);
			$contents = substr($contents, 0, $size);
			print($contents);
			exit();
		}else{
			ob_end_flush();
			exit();
		}
	}
	
	function showDebugInfo () {
		echo '<pre><font style="font-family: monospace; font-size: 12px;" color="#FFFFFF">';
	
		echo 'Game-Debug directive turned on: (Only admins will see this)<br>';
		// general game stuff.
		echo '<br>General Game debug data:<br>';
		echo "-----------------------------------------------------<br>";
		echo "Script execution time: " . (myclock() - $GLOBALS['game_start_clock']) . "<br>";
		if (isset($GLOBALS['game_queries_required']))
			echo "Required queries:      " . $GLOBALS['game_queries_required'] . "<br>";
		else
			echo "Required queries:      " ."No breakpoint set" . "<br>";
		if (isset($GLOBALS['game_fetches_required']))
			echo "Required fetches:      " . $GLOBALS['game_fetches_required'] . "<br>";
		else
			echo "Required fetches:      " ."No breakpoint set" . "<br>";
	
		$GLOBALS['database']->showDebugData();
		$GLOBALS['user']->showDebugData();
		$GLOBALS['province']->showDebugData();
		// end
		echo '<br>End of debug.  Dumping extra debug buffer:<br>';
		echo $GLOBALS['game_debug_data'];
		
		echo '</font></pre>';
	}
	
	
	error_reporting (E_ALL);
	// so that networth be refreshed on every page
	$province->setNetworth();
	
	// hack by anderse!
	if ($province->vacationmode==true )
	{
		// allow some pages
		switch ($_SERVER['PHP_SELF'])
		{
			// allow these pages
			case '/login.html':
			case '/scripts/showProvince.php':
			case '/scripts/report.php':
			case '/scripts/kingdomForum.php':
			case '/scripts/message.php':
			case '/scripts/showKingdomNews.php':
			
			// TODO: evolve.
			case 'http://www.tkoc.net/scripts/showProvince.php':
			break;
		
		
			default:
			{
				//echo "page=" . $_SERVER['PHP_SELF'] . "<br>";
				$info = "";  
				if ( ($province->vacationmode==true) && ($province->vacationTicks > 48))
				{
						$info .= "You can cancel vacation mode by going to main page and pressing the cancel button. Your province will then resume as normal.<br>";
		//		        $info .= "<form action=".$_SERVER['PHP_SELF']." method=POST>";
		//		        $info .= "<input type=submit name='Cancel' value='Cancel'>";
		//		        $info .= "</form>";
				}
		
				 $htmlBody = '	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;	You cannot do anything here while in vacation mode.<br>'. $info;
			} // default
		} // switch
	} // if

	global $config;
	
	require_once($GLOBALS['path_www_scripts']."ChaosTime.class.inc.php");
	$thisTime = new ChaosTime($config['ticks']);
	$era = $thisTime->getEra();
	$year = $thisTime->getYear();
	$month = $thisTime->getMonth();
	$day = $thisTime->getDay();
	
	$showmenu=true;
	
	if ( ($GLOBALS['config']['status'] == 'Ended') )
	{
		if (@intval($GLOBALS['user']->access) & $GLOBALS['constants']->USER_ADMIN)
		{
			$GLOBALS['game_debug_data'] .= "<br>Special Endage-privilege granted due to admin access";
		} 
		else // not allowed to be logged in after age end.
		{
			die ('
				<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>	
					<title>The Kingdoms of Chaos</title>	
				<meta name="description" content="A massively played mutliplayer online strategy game.  Meet and make new friends and enemies in this warbased roleplaying game.">
				<meta name="keywords" content="game, mutliplayer, friends, war, roleplay, strategy">		
					<link rel=stylesheet href="http://www.tkoc.net/css/chaos.css" type="text/css">
				</head>
				<body bgcolor="#000000">
				<table width=100%>
				<tr>
				<td>
				<CENTER>
				The next age is beeing updated!  please be patient.  Check out the forum for more information.<br><br>
				<a href="http://www.tkoc.net"><img src="http://www.tkoc.net/img/logo/chaos_logo_main.jpg" border="0"></a>
				</CENTER>
				</td>
				</tr>
				</table>
				</body>
				</html>
			');
  		}
	}

	$url = $_SERVER['PHP_SELF'];
	$url = basename($url);
	switch ($url) {
		case "showProvince.php":
			$page = "Home";
			break;
		case "council.php":
			$page = "Council";
			break;
		case "science.php":
			$page = "Knowledge";
			break;
		case "Military.php":
			$page = "Military";
			break;
		case "buildings.php":
			$page = "Buildings";
			break;
		case "kingdomForum.php":
			$page = "Kingdom Forum";
			break;
		case "message.php":
			$page = "Messages";
			break;
		case "report.php":
			$page = "Kingdom";
			break;
		case "aid.php":
			$page = "Trade";
			break;
		case "showKingdomNews.php":
			$page = "Kingdom News";
			break;
		case "politics.php":
			$page = "Politics";
			break;
		case "Attack.php":
			$page = "Attack";
			break;
		case "thievery.php":
			$page = "Thievery";
			break;
		case "magic.php":
			$page = "Magic";
			break;
		case "Explore.php":
			$page = "Explore";
			break;
		case "top50.php":
			$page = "Top Provinces";
			break;
		case "topKing.php":
			$page = "Top Kingdoms";
			break;
		case "provinceNews.php":
			$page = "Province News";
			break;
		case "preferences.php":
			$page = "Preferences";
			break;
		case "provinceAction.php":
			$page = "Province Action";
			break;
		default:
			$page = "No Page";
			break;
	}

//include_once '../facebook/facebook.php';
//$fb=new Facebook();
//$fb_user=$fb->get_loggedin_user();

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

$pagepath = str_replace ($GLOBALS['path_domain_root'],"", $_SERVER['REQUEST_URL']);
$count = substr_count($pagepath, '.php');
if ($count == 0)
	$pagepath .= "index.php";

$translation = "english";
//require_once ($GLOBALS['path_root']."language/".$translation."/".$pagepath."-".$translation.".php");
require_once ($GLOBALS['maindomain']."language/english.php");
require_once ($GLOBALS['path_root']."includes/security.php");
$security = new Security;

	echo '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" >
	<meta http-equiv="pragma" content="no-cache">
	<title>'.$page.' | '.$GLOBALS['site_header'].'</title>
	';	
// hack
// load another css if this is the forum
if ($forum) {
?>
	<link rel=stylesheet href="../css/chaosnew-old.css" type="text/css">
	<link rel=stylesheet href="css/chaos.css" type="text/css">
	
<?php 

} else {
?>
	<link rel=stylesheet href="../css/chaosnew-old.css" type="text/css">
	<link rel=stylesheet href="../css/chaos.css" type="text/css">
<?php
}
	if ($page == "Buildings") {
		require_once( $GLOBALS['PATH_TO_BUILDINGS']."Buildings.js.inc.php" );
	}
 ?>
	<title><?php echo $page.' | '.$mylanguage['head_title']; ?></title>
	<meta name="description" content="<?php echo $mylanguage['head_description']; ?>" />
	<meta name="keywords" content="<?php echo $mylanguage['head_keywords']; ?>" />
	<meta name="Generator" content="<?php echo $mylanguage['head_generator']; ?>" />
	<meta name="robots" content="index, follow" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="text/javascript" language="javascript" src="<?php echo $GLOBALS['path_domain_root']."javascript/shortcut.js"; ?>"></script>
</head>

<body class="body">
<div class="page-container">
		<div class="game-header">
			<div class="cornerpic">
				<img src="<?php echo $cornerpic;?>" />
			</div>
			<div class="header-data">
				<?php if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') === false)) {?>
							<div class="donation-notify">
								<img src="<?php echo $GLOBALS['path_domain_root']; ?>images/arrow.png" alt="Arrow">
								<span>&nbsp;TKOC is a free game and it will remain free. If you like it, please donate and help us cover the expenses and keep us motivated :). Visit: <a href="<?php echo $GLOBALS['path_domain_root']; ?>worldforum/index.php?action=treasury">I want to donate and contribute.</a></span>
							</div>
				<?php } ?> 
				<div class="province-info-header">
					<div class="networth">
						<div class="text">Networth<br /><?php echo number_format($province->networth,0,' ',',');?></div>
					</div>
					<div class="peasants">
						<div class="text">Peasants<br /><?php echo number_format($province->peasants,0,' ',',');?></div>
					</div>
					<div class="food">
						<div class="text">Food<br /><?php echo number_format($province->food,0,' ',',');?> kg</div>
					</div>
					<div class="metal">
						<div class="text">Metal<br /><?php echo number_format($province->metal,0,' ',',');?> kg</div>
					</div>
					<div class="gold">
						<div class="text">Gold<br /><?php echo number_format($province->gold,0,' ',',');?> gc</div>
					</div>
			   </div>
			   <div class="time-tick">
					<?php
					$GLOBALS['database']->query("select SEC_TO_TIME((totalTickTime)-(UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(lastTickTime))) as nextTick from Config");
					$nextTick = $GLOBALS['database']->fetchArray();
					echo "
						<span class='left-time'>$day day of the $month Month of the $year year in the $era era</span>
						<span class='right-tick'>Next tick in: <b>$nextTick[nextTick]</b> hours</span>
					";
					?>
			   </div>
			</div>
		</div>
		<?php require_once ("game-navigation.php"); ?>
		
		
		
		<div class="content">
			<?php 
			$maintenance = false;
			if (!$maintenance || (intval($GLOBALS['user']->access) & $GLOBALS['constants']->USER_ADMIN)) {
				if (strlen($left) > 0) {	
					echo "<div style='width:128px; height:400px; float:left;'>$left</div>";
					
					if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)) {
						echo "<div style='float:left'>$htmlBody";
						
						if ((isset($GLOBALS['game_debug']) && $GLOBALS['game_debug'] == true) && (intval($GLOBALS['user']->access) & $GLOBALS['constants']->USER_ADMIN))
						{
							showDebugInfo ();
						}
						echo "</div>";
					}
					else {
						echo $htmlBody;
					
						if ((isset($GLOBALS['game_debug']) && $GLOBALS['game_debug'] == true) && (intval($GLOBALS['user']->access) & $GLOBALS['constants']->USER_ADMIN))
						{
							showDebugInfo ();
						}
					}
				}
				else {
					echo $htmlBody;
				
					if ((isset($GLOBALS['game_debug']) && $GLOBALS['game_debug'] == true) && (intval($GLOBALS['user']->access) & $GLOBALS['constants']->USER_ADMIN))
					{
						showDebugInfo ();
					}
				
				}
			}
			else
				echo "The game is under maintenance: Database is being updated to support utf-8 and a security hole is being fixed. It will be back in less than 3-5 minutes.";
			?>
		</div> <!--content-->
		<div style="clear:both;">&nbsp;</div>
	</div> <!--page-container-->
	<div>&nbsp;</div>
	<?php
	/*if($fb_user) {
		echo '
		<!-- Note: Include this div markup as a workaround for a known bug in this release on IE where you may get a "operation aborted" error --> 
		<div id="FB_HiddenIFrameContainer" style="display:none; position:absolute; left:-100px; top:-100px; width:0px; height: 0px;"></div> 
		<script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php" type="text/javascript"></script>
		<script type="text/javascript"> 
			FB_RequireFeatures(["CanvasUtil"], function(){ 
				FB.XdComm.Server.init("xd_receiver.htm"); 
				FB.CanvasClient.startTimerToSizeToContent ();
			}); 
		</script>';
	}*/
	?>
</body>
</html>
<?php
// Call this function to output everything as gzipped content.
print_gzipped_page();


}
?>
