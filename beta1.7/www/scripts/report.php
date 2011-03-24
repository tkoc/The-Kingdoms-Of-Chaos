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
//error_reporting(E_ALL);
/* report.php	 - script for showing all the provinces in the selected kingdom
 * 
 *  Displays Name of the province, acres and networth. (Also highlights the King's and the user's province(not yet))
 */
require_once ("all.inc.php");
require_once ("User.class.inc.php");
require_once ("Province.class.inc.php");
require_once ("all.inc.php");
require_once ("isLoggedOn.inc.php");
require_once("Kingdom.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "requireLoggedOn.inc.php");

////////////////////////////////
// Checks if kingdomId is set, if not sets it to the users kiId
////////////////////////////////
$body = "";
if ((isset($_GET['kingdomId']))) {
	$currentKid  = $_GET['kingdomId'];
} else {
	if (isset($_REQUEST['currentKid']))
		$currentKid = $_REQUEST['currentKid'];
	else $currentKid = $province->getkiID();   
}

if (isset($_POST['Previous'])) {
	$result = $database->query("SELECT kiID from Kingdom where kiID<$currentKid and kiID>0 ORDER BY kiID DESC LIMIT 1");
	if (($array= $database->fetchArray())) {
		$currentKid = $array['kiID'];
	}
	     
} else if (isset($_POST['Next'])) {
	$result = $database->query("SELECT kiID from Kingdom where kiID>$currentKid and kiID>0 ORDER BY kiID ASC LIMIT 1");
	if (($array= $database->fetchArray())) {
		$currentKid = $array['kiID'];
	}
} else if (isset($_POST['OK'])) {
	if (!is_numeric($_POST['kingdomId'])) {
		$currentKid = $province->getkiID();
	} else {
		$currentKid = $_POST['kingdomId'];
	}
}

/*$linkexhange = '
<center>
<A HREF="http://www.gamesites200.com/cgi-bin/bpwork.cgi?advert=NonSSI&page=42" target="_blank">
<IMG SRC="http://www.gamesites200.com/cgi-bin/bpwork.cgi?ID=tkoc&page=42" BORDER=0></a>
<br><font size=1 face=Verdana><A HREF="http://www.gamesites200.com/bp">Game Sites 200 Banner Exchange</a></font>
</center>';
*/
////////////////////////////////////////
// Builds the menu
////////////////////////////////////////
	$body = "";
 /* 	$body ='<CENTER>
	<script type="text/javascript"><!--
google_ad_client = "pub-4842467343941804";
google_ad_width = 728;
google_ad_height = 90;
google_ad_format = "728x90_as";
google_ad_type = "text_image";
google_ad_channel ="";
google_color_border = "93886C";
google_color_bg = "000000";
google_color_link = "FFFFFF";
google_color_url = "999999";
google_color_text = "CCCCCC";
//--></script>
<script type="text/javascript"
  src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</CENTER>	';
*/
$body .= "<br /><center>";
$body .= "   <table frame='box' border='0' width='60%'>";
$body .= "      <tr>";
$body .= "        <td class='rep1' width='33%' align='center'><a href='report.php' class='rep'><b>Search Kingdoms</b></a></td>";
$body .= "        <td class='rep1' width='33%' align='center'><a href='provincesRanking.php' class='rep'><b>Provinces Ranking</b></a></td>";
$body .= "        <td class='rep1' width='33%' align='center'><a href='kingdomsRanking.php' class='rep'><b>Kingdoms Ranking</b></a></td>";
$body .= "      </tr>";
$body .= "   </table>";
$body .= "</center><br />";

////////////////////////////////////////
// Builds the form
////////////////////////////////////////
  
   $body .= "<div align='center'><font size='5pt' color='FFEECC'>Enter a kingdom #:<br></font>";
   $body .= "<form method='POST' name='choose' action='report.php'>";
   $body .= "   <table border='0' width='25%'>	";
   $body .= "	<tr><td align='center' class='rep1' ><input type='text' size='8' name='kingdomId' class='form' title='Enter a kingdom number'></td>";
   $body .= "	<td align='center' class='rep1'><input type='submit' name='OK' value='&nbsp;&nbsp;OK&nbsp;&nbsp;&nbsp;' class='form' 
title='Click to see the selected kingdom'><br></td></tr>";
   $body .= "	<tr><td align='center' class='rep1' ><input type='submit' value='Previous' name='Previous' class='form' title='Click to see the previous 
kingdom'></td>";
   $body .= "	<td align='center' class='rep1'><input type='submit' value='&nbsp;Next&nbsp;' name='Next' class='form' title='Click to see 
the next kingdom'></td></tr>";
   $body .= "   <tr><td><input type='hidden' name='currentKid' value='" .$currentKid ."'></td></tr>";
   $body .= "   </table>";	
   $body .= "</form></div>";
   $body .= "<br><br>";

	$kingdom = new Kingdom ($database, $currentKid);
	$kingdom->loadKingdom();
	
	$body .= $kingdom->showKingdom();
	$body .= "<br>";
templateDisplay($province, $body);

?>
