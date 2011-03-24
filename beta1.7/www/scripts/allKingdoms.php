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
require_once ("all.inc.php");
require_once ("User.class.inc.php");
require_once ("Province.class.inc.php");
require_once ("isLoggedOn.inc.php");

$orderby = $_GET['order'];
if (!isset ($orderby)) {
   $orderby = "nw desc";
}
if ($orderby == "sumAcres desc") {
   $header = "All Kingdoms orderd by acres";

}
if ($orderby == "nw desc") {
   $header = "All Kingdoms ordered by networth";

}
/*
echo "orderby:";
echo $orderby;
echo "order:";
echo $_GET['order'];
*/
////////////////////////////////////
// Builds the query
///////////////////////////////////

   $sql ="select SUM(Province.networth) as nw, SUM(Province.acres) as sumAcres,Province.kiID,Kingdom.numProvinces, Kingdom.Name as kingdomName from Province left join 
Kingdom on Kingdom.kiID=Province.kiID where Kingdom.kiID>0 group by Province.kiID order by " .$orderby;
//echo $sql;
   $result = $database->query($sql);

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
   

   $body .= "<div align='center'><br><br><font size='6pt' color='FFEECC'>" .$header ."</font></div>";
   $body .= "<br><br>";
   $body .= "	<table border='0' align='center' width='60%' nowrap cellspacing='2'>";
   $body .= "	   <tr><td align='left' width='40%' nowrap class='rep3' align='left'><b>Kingdom: </b></a></td><td align='left'width='20' 
nowrap class='rep3'><b>#:</b></td><td align='left' class='rep3'>Provinces:</td><td align='left' nowrap class='rep3'><a href='allKingdoms.php?order=sumAcres%20desc'class='rep'><b>Total Acres: </b></a></td><td 
alig='left' class='rep3'><a href='allKingdoms.php?order=nw%20desc' class='rep'><b>Total Networth:</b></a>	</td></tr>";
		
			
//////////////////////////////
// Displays the result
//////////////////////////////

$count =0;
if (($row = $database->fetchArray())) {
   do {
	$count++;
	$body .= "<tr><td align='left' class='rep1'>$count .<a href='report.php?kingdomId=" .$row['kiID']. "' class='rep'>". 
$row['kingdomName']."</a></td><td align='right' class='rep1'><a href='report.php?kingdomId=" .$row['kiID']. "' class='rep'>" .$row['kiID'] 
."</a></td><td align='right' class='rep1'>" .$row['numProvinces'] ."</td><td align='right' class='rep1'>" .number_format($row['sumAcres'], 0,' ',',') ."</td><td align='right' class='rep1'>" 
.number_format($row['nw'], 0,' ',',') ."</td></tr>";
  		

   }   while(($row = $database->fetchArray()));

}

		
	$body .= "</table>";
	$body .= "<br><br><center><img src='../img/hor_ruler.gif' align='center' border='0'></img></center>";
	templateDisplay($province, $body);

$database->shutdown();
?>
