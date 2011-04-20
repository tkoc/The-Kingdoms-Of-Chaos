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
/*   Show the top 50 biggest provinces, based on acres.	
*    Includes  a link to the report.php script wich 	
*    displays the current kingdoms provinces
*
*	FYFYFYFYFYF!!!!!!!!!
*
* Author: Knut Elvesæter.
*/
require_once ("Database.class.inc.php");
require_once ("User.class.inc.php");
require_once ("Province.class.inc.php");
require_once ("all.inc.php");
//require_once ("isLoggedOn.inc.php");
require_once ("requireLoggedOn.inc.php");
if (isset($_GET['otype']))
	$ordertype = $_GET['otype'];
else
   $ordertype = "Networth";

// Quick fix
// by Anders Elton
// Deletes & log hack attempts.
$cheat = false;
switch ($ordertype)
	{
		case 'Acres': $orderby = "acres desc, networth desc"; break;
		case 'Networth': $orderby = "networth desc, acres desc"; break;
		default:
			$cheat = true;
	}

if (isset($_GET['order']))
	if ($_GET['order'] != $orderby)
		$cheat = true;
		
if ($cheat == true)
{
			require_once($GLOBALS['path_www_scripts'] . "ActionLogger.class.inc.php");
			$pid = $GLOBALS['province']->getpID();

// anderse Removed auto deletion from here...
// since the effect is gone anyway
//			$GLOBALS['database']->query("UPDATE Province set status='DeletedCheater' where pID ='$pid'");


			$actionlogger = new ActionLogger($GLOBALS['database']);
			$actionlogger->log($actionlogger->CHEAT,$pid, $pid,1,true);
			templateDisplay( $GLOBALS['province'], "Cheaters are not allowed in this game.\n");
			die();
}

/*$orderby = $_GET['order'];
if (!isset($orderby)) {
   $orderby = "acres desc, networth desc";
}*/


//if( (stristr($orderby, "gold") === FALSE) && (stristr($orderby, "metal") === FALSE) && (stristr($orderby, "food") === FALSE) && (stristr($orderby, "morale" === FALSE)) && (stristr($orderby, "mana") === FALSE) || (stristr($orderby, "influence") === FALSE) && (stristr($orderby, "reputation") === FALSE) && (stristr($orderby, "magicRep") === FALSE) ) {
if( (stristr($orderby, "gold") != FALSE ) || (stristr($orderby, "metal") != FALSE) || (stristr($orderby, "food") != FALSE) || (stristr($orderby, "morale") != FALSE) || (stristr($orderby, "mana") != FALSE) || (stristr($orderby, "influence") != FALSE) || (stristr($orderby, "reputation") != FALSE) || (stristr($orderby, "magicRep") != FALSE) ) {
	$usrCheat = $GLOBALS['user'];
//	if($usrCheat != false) {
//		$cheatSQL = "update User set topCheat=topCheat+1 where userID={$usrCheat->userID}";
//		$database->query($cheatSQL);
//	}
	$body = "<center>Please read the rules... only way to get this page is if you tried to cheat, this is reason enough for us admins to delete you</center>";
}
else {


$dummy=null;
$raceObj = new Race ($database,$dummy);

$race =$raceObj->getAllRaces();
foreach ($race as $r) {
        $id = $r->getID();
        $name = $r->getName();
        $races[$id] = $name;
}
	
////////////////////////////////////
// Builds the query
///////////////////////////////////

   $sql = "SELECT Province.pID, Province.provinceName, Province.acres, Province.networth, Province.kiID, Kingdom.name, Province.spID FROM Province, Kingdom WHERE 
Kingdom.kiID=Province.kiID and Province.status='Alive' ORDER BY " .$orderby;
   $database->query($sql);

while ($item = $database->fetchArray()) {
        $add = $item;
        $add['race'] = $races[$item['spID']];
        $add['kingdom'] = $item['kiID'];
        $list[] = $add;
}

//reset($list);



////////////////////////////////
// creates an array of the sql-result
////////////////////////////////
/*
if (($row = $database->fetchArray())) {
   do {
        $arr[]=$row;
//        $kingdomName = $row['name'];
//        $kingID = $row['king'];
   } while(($row = $database->fetchArray()));
}
*/
/////////////////////////////////
// Builds the menu
/////////////////////////////////
	$body ='';
   $body .= "    <br><center>";
   $body .= GetStatLinks();
   $body .= "   <br></center>";
   

   $body .= "<div align='center'><br><br><font size='6pt' color='FFEECC'>Top Provinces by " .$ordertype ."</font></div>";
   $body .= "<br><br>";
   $body .= "	<table border='0' align='center' width='60%' nowrap cellspacing='2'>";
   $body .= "	   <tr><td align='left' width='40%' nowrap class='rep3'><b>Province: </b></td><td class='rep3'>Race:</td><td align='left' 
width='20' nowrap class='rep3'><b>Kingdom</b></td><td align='left' nowrap class='rep3'><b>Kingdom #: </b></td><td 
alig='left' class='rep3'><a href='top50.php?order=acres%20desc,%20networth%20desc&otype=Acres' class='rep'><b><span style='text-decoration:underline'>Acres:</span></b></a></td><td 
align='left' 
class='rep3'><a href='top50.php?order=networth%20desc,%20acres%20desc&otype=Networth' class='rep'><b><span style='text-decoration:underline'>Networth:</span></b></a></td></tr>";
		
			
//////////////////////////////
// Displays the result
//////////////////////////////

$count = 0;
		if ($list) {
		   reset($list);
                   foreach ($list as $row) {
//			$province = new Province ($row[pID],&$database);
//			$province->getProvinceData();
			$count++;
			$body .= "<tr><td align='left' class='rep1'>$count.&nbsp;" . "<a href=\"provinceAction.php?victim=$row[pID]\" class='rep'>". 
				$row["provinceName"]."</a></td><td class='rep1'>" .$row['race'] ."</td><td align='left' class='rep1'>
				<a href='report.php?kingdomId=" .$row['kiID'] ."' class='rep'>" .$row['name'] ."</a><td align='right' class='rep1'>
				<a href='report.php?kingdomId=" .$row['kiID'] ."' class='rep'>" 
				.$row['kiID'] ."</a></td><td align='right' class='rep1'>" .$row['acres'] . "</td><td align='right' class='rep1'>" 
				.$row['networth'] ."</td></tr>";
  		
		   }		   
		} else {
			echo "No provinces found!";
		}

		
	$body .= "</table>";
	$body .= "<br><br><center><img src='../img/hor_ruler.gif' align='center' border='0'></img></center>";
}
	templateDisplay($province, $body);

$database->shutdown();
?>
