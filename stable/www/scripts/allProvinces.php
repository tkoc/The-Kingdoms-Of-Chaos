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
*
*
*
*/
require_once ("Database.class.inc.php");
require_once ("User.class.inc.php");
require_once ("Province.class.inc.php");
require_once ("all.inc.php");
require_once ("isLoggedOn.inc.php");

$ordertype = $_GET['otype'];
if (!isset($ordertype)) {
   $ordertype = "Acres";

}



$orderby = $_GET['order'];
if (!isset($orderby)) {
   $orderby = "acres desc, networth desc";

}




////////////////////////////////////
// Builds the query
///////////////////////////////////

   $sql = "SELECT Province.pID, Province.provinceName, Province.acres, Province.networth, Province.kiID, Kingdom.name FROM Province, Kingdom WHERE 
Kingdom.kiID=Province.kiID and Province.status='Alive' ORDER BY " .$orderby;
   $result = $database->query($sql);

////////////////////////////////
// creates an array of the sql-result
////////////////////////////////

if (($row = $database->fetchArray())) {
   do {
        $arr[]=$row;
//        $kingdomName = $row['name'];
//        $kingID = $row['king'];
   } while(($row = $database->fetchArray()));
}

/////////////////////////////////
// Builds the menu
/////////////////////////////////

   $body = "    <br><center>";
   $body .= "   <table frame='box' border='0' width='60%'>";
   $body .= "      <tr>";
   $body .=  "        <td class='rep1' width='33%' align='center'><a href='report.php' class='rep'><b>Show Kingdoms</b></a></td>";
   $body .=  "        <td class='rep1' width='33%' align='center'><a href='top50.php' class='rep'><b>Top 50</b></a></td>";
   $body .=  "        <td class='rep1' width='33%' align='center'><a href='topKing.php' class='rep'><b>Top 10</b></a></td>";

   $body .= "      </tr>";
   $body .= "   </table>";
   $body .= "   <br></center>";
   

   $body .= "<div align='center'><br><br><font size='6pt' color='FFEECC'>All provinces by " .$ordertype ."</font></div>";
   $body .= "<br><br>";
   $body .= "	<table border='0' align='center' width='60%' nowrap cellspacing='2'>";
   $body .= "	   <tr><td align='left' width='40%' nowrap class='rep3'><b>Province: </b></td><td class='rep3'>Race:</td><td align='left' 
width='20' nowrap class='rep3'><b>Kingdom</b></td><td align='left' nowrap class='rep3'><b>Kingdom #: </b></td><td 
alig='left' class='rep3'><a href='top50.php?order=acres%20desc,%20networth%20desc&otype=Acres' class='rep'><b>Acres:</b></a></td><td 
align='left' 
class='rep3'><a href='top50.php?order=networth%20desc,%20acres%20desc&otype=Networth' class='rep'><b>Networth:</b></a></td></tr>";
		
			
//////////////////////////////
// Displays the result
//////////////////////////////

$count = 0;
		if ($arr) {
		   reset($arr);
                   foreach ($arr as $row) {
			$province = new Province ($row[pID],&$database);
			$province->getProvinceData();
			$count++;
			$body .= "<tr><td align='left' class='rep1'>$count.&nbsp;" . "<a href=\"provinceAction.php?victim=$row[pID]\" 
class='rep'>". 
$row["provinceName"]."</a></td><td class='rep1'>$province->race</td><td align='left' class='rep1'><a href='report.php?kingdomId=" .$row['kiID'] ."' class='rep'>" .$row['name'] ."</a><td align='right' class='rep1'>
<a href='report.php?kingdomId=" .$row['kiID'] ."' class='rep'>" 
.$row['kiID'] ."</a></td><td align='right' class='rep1'>" .$row['acres'] . "</td><td align='right' class='rep1'>" .$row['networth'] 
."</td></tr>";
  		
		   }		   
		} else {
			echo "No provinces found!";
		}

		
	$body .= "</table>";
	$body .= "<br><br><center><img src='../img/hor_ruler.gif' align='center' border='0'></img></center>";
	templateDisplay($province, $body);

$database->shutdown();
?>
