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
die("Old script.  please releoad or refresh your cache.  The new script is politics.php");
///////////////////////////////////////////////////////////////////////////////////////////////////////////
// vote.php
// This script allows each province to vote for a King.
// it show all the provinces an lets the user vote for the on to be King. Higlights the users own province.
// it also shows who each province has voted for
// 
// If the user is the current king, he can change the name of the kingdom.
///////////////////////////////////////////////////////////////////////////////////////////////////////////

//error_reporting(E_ALL);
require_once ("all.inc.php");
require_once ("User.class.inc.php");
require_once ("Province.class.inc.php");
require_once ("isLoggedOn.inc.php");
require_once ("News.class.inc.php");


//////////////////////////////////
// Checks if the user presse the vote-link
// and calls the vote-function
//////////////////////////////////
if (isset($_GET["vote"])) {
	$province->vote($_GET['vote']);
	updateKing($province->getKiId());
	$province->getProvinceData();
}



$sql = "SELECT pID, provinceName, rulerName, voteFor, networth FROM Province WHERE 
kiID=".$province->getkiId() ." ORDER BY pID ASC";

/////////////////////////////////
// Build the array with the result
// and creates a new array with the pID's of
// all the provinces in the kingdom.
//////////////////////////////////


$result = $database->query($sql);
if (($row = $database->fetchArray())) {
   do {
      $votes[$row['pID']]['numVotes']=0;   
      $arr[$row['pID']]=$row;
   }   while(($row = $database->fetchArray()));
}

/////////////////////////////////
// counts each province's votes
/////////////////////////////////

reset($arr);
foreach ($arr as $item) {
      $votes[$item['voteFor']]['numVotes']++;   
}

////////////////////////////////
// If the user i the King, he/her gets an additional option:
// The king can change the name and the banner of the kingdom
// Uses the isKing() to check.
////////////////////////////////
$database->query("SELECT * from Kingdom where kiID=".$province->getkiId()."");
$total = $database->fetchArray();

if ($province->isKing()) {

    $message = "Your Majesty, do You wish to change the name of Your kingdom?";

   if (isset($_POST['chname'])){
      if (strlen($_POST['newname'])<= 0) {
         $message = $province->getShortTitle() .", please enter a valid name!";
      } else {
         change($province, $_POST['newname']);
	 $total['name']=$_POST['newname'];
         $message = "You have successfully changed the name of Your kingdom, Your Majesty!";
      }
   }
   if (isset($_POST['chbanner'])) {
      $database->query("UPDATE Kingdom set banner='$_POST[banner]' where kiID='".$province->getKiId()."'");
      $total['banner']=$_POST['banner'];
   }
	require_once ("Kingdom.class.inc.php");
	$kingdom = new Kingdom ($database, $province->getkiId());
	$kingdom->loadKingdom();
	if (isset($_POST['chkdpw'])) {
		$kingdom->setKingdomPassword($_POST['kingdomPassword']);
	}
   $body  .= "<p align='center'><font size='4pt' color='#FFEECC'>Change name of Kingdom:</font></p>";
   $body .= "<div class='bread' align='center'>" .$message ."</div><br>";
   $body .= "<form method='POST' action='vote.php?'>";
   $body .= "   <table border='0' align='center' width='60%'>
      <tr>
         <td align='center' class='rep3' width='100%' colspan='2'><b>&nbsp;".$currentName."</b></td>
      </tr>
      <tr>
         <td class='rep1' width='50%' align='center'>
            <input type='text' size='50' name='newname' class='form' value='$total[name]' title='Type a new name here'>
         </td>
         <td class='rep1' width='50%' align='center'>
            <input type='submit' class='form' name='chname' value='Change Name' title='Click here to change the name'>
         </td>
      </tr>

      <tr>
         <td class='rep1' width='50%' align='center'>
            <input type='text' size='50' name='kingdomPassword' class='form' value='".$kingdom->kingdomPassword."' title='Type a new password here'>
         </td>
         <td class='rep1' width='50%' align='center'>
            <input type='submit' class='form' name='chkdpw' value='Change Kingdom Password' title='Click here to change kingdom password'>
         </td>
      </tr>
      <tr>
         <td class='rep1' width='50%' align='center'>
            <input type='text' size='50' name='banner' class='form' value='$total[banner]' title='Type an url to a banner here'>
         </td>
         <td class='rep1' width='50%' align='center'>
            <input type='submit' class='form' name='chbanner' value='Change banner' title='Click here to add the banner'>
         </td>
      </tr>
   </table></form>";
   $body .= "<center><img src='../img/throne.jpg' border='0'></img></center><br>";

}


///////////////////////////////////
// Builds table header
///////////////////////////////////
$votesRequired = round($total['numProvinces']*0.5);

   $body .= "<br><center><font size='6pt' color='FFEECC'>Vote for a King</font></center><br>"; 
   $body .= "<div class='bread' align='center'>" .$province->getShortTitle() .", are we going to vote for a new ruler today?</div><br> ";
   $body .= "<div class='bread' align='center'> $votesRequired  votes is required to be king. ";
   $body .= "	<table border='0' align='center' width='60%' nowrap cellspacing='2'>";
   $body .= "	   <tr><td width='50%' nowrap class='rep4' align='left'><b>Name: 
</b></td><td alig='right' nowrap class='rep4'><b>Ruler:</b></td><td nowrap align='left' 
class='rep4'><b>Networth:</b></td><td nowrap align='left' class='rep4'><b>Votes:</b></td><td nowrap align='left' 
width='20%'
class='rep4'><b>Voted:</b></td><td nowrap align='left' 
class='rep4'><b>Vote for:</b></td></tr>";


/////////////////////////////////////////
// Builds the table body
/////////////////////////////////////////

reset ($arr);
foreach ($arr as $row) {
      if ($row["pID"]){
      

      $body .= "<tr><td align='left' class='rep1'>";

      if ($row["pID"] == ($province->getpID())) {$body .="<b>";}
      $body .= $row["provinceName"]; 
      if ($row["pID"] == $province->getpID()) {$body .="</b>";}

      $body .= "</td><td align='right'class='rep1'>" .$row["rulerName"]; 
      $body .= "</td><td align='right' class='rep1'>" .$row["networth"] ."</td><td align='right' class='rep1'>" 
.$votes[$row['pID']]['numVotes']; 
      $body .= "</td><td align='right' width='20%' class='rep1'>" .$arr[$row["voteFor"]]["rulerName"] ;
      $body .= "</td><td align='right' class='rep1'><a href='vote.php?vote=".$row["pID"]."' 
class='rep'>Vote</a></td></tr>";
}


}
   $body .= "</table><br><br><br>";


templateDisplay($province, $body);
$database->shutdown();



////////////////////////////////////////
// function change($province, $name)
//
// changes the name of the kingdom.
////////////////////////////////////////
function change($province, $newName) {
   global $database;
   $newName = htmlspecialchars($newName);
   $sql =  "UPDATE Kingdom SET name='$newName' WHERE kiId='" .$province->getkiId(). "'";
   if ($database->query($sql)) {
// og  her vil jeg reloade scriptet pga endringer
   $province->getProvinceData();
   }
}


/////////////////////////////////////
// function newsKing(&kidd, $kingdomid)
// 
// sends out news that there is a new king
///////////////////////////////////

function newsKing($kidd, $kingdomid) {
  global $database;
  $s = "SELECT rulerName, provinceName FROM Province WHERE pID='" .$kidd ."'";
  $resultat = $database->query($s);
  $row = $database->fetchArray();
  $prov = $row['provinceName'];
  $nam =  $row['rulerName']; 

  $text = "Our kingdom has got a new ruler: " .$nam ." of the province " .$prov;
//  echo $text;
  $news = new News(&$database);
    

  $news->postNews($text, $kingdomid);    // sender med tekst om hvem som er ny konge, og dens kiID
//  echo "posted news";
  

}

////////////////////////////////////
// function updateKing($kID)
//
// Updates the database with the new king.
////////////////////////////////////

function updateKing ($kID) {
   global $database;

   $database->query("SELECT king,numProvinces from Kingdom where kiID=$kID ");
   $oldKing=$database->fetchArray();
   $database->query("SELECT count(voteFor) as votes, voteFor as pID from Province where kiID=$kID GROUP by voteFor order by votes DESC");
   $newKing = $database->fetchArray(); 
   
   if (round($oldKing['numProvinces']*0.5)>$newKing['votes']) {
   	$database->query("UPDATE Kingdom set king=0 where kiID=$kID");
  
   }

   if ($oldKing['king'] == $newKing['pID']) {
   } else {
   	$database->query("UPDATE Kingdom set king=$newKing[pID] where kiID=$kID");
   }
}

?>
