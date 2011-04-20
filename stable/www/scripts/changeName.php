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

//error_reporting(E_ALL);
/* report.php    - script for showing all the provinces in the selected kingdom
 *
 *  Displays Name of the province, acres and networth. (Also highlights the King's and the user's province(not yet))
 */
require ("/usr/local/apache/thurmann.net/htdocs/chaos/scripts/Database.class.inc.php");
require ("/usr/local/apache/thurmann.net/htdocs/chaos/scripts/User.class.inc.php");
require ("/usr/local/apache/thurmann.net/htdocs/chaos/scripts/Province.class.inc.php");
require ("/usr/local/apache/thurmann.net/htdocs/chaos/scripts/all.inc.php");
require ("/usr/local/apache/thurmann.net/htdocs/chaos/scripts/isLoggedOn.inc.php");

if (isset($_GET["changetbn"])) {
   change($_GET["name"]);
}
$sql = "SELECT name FROM Kingdom WHERE kiId=" .$province->getkiId();
$result = $database->query($sql);
do {
  $currentName = $row["name"];
} while($row = $database->fetchArray());

if (($province->getpID())== $kingID) {

   $body = "    <br><center>";
   $body .= "   <table frame='box' border='0' width='60%'>";
   $body .= "      <tr>";
   $body .=  "        <td class='rep1' width='33%'><a href='report.php'>Show Province</a></td>";
   $body .=  "        <td class='rep1' width='33%'><a href='top50.php'>Top 50</a></td>";
   $body .=  "        <td class='rep1' width='33%'><a href='changeName.php'>Top 50</a></td>";
   $body .= "      </tr>";
   $body .= "   </table>";
   $body .= "   <br></center>";


} else {
   $body = "    <br><center>";
   $body .= "   <table frame='box' border='0' width='60%'>";
   $body .= "      <tr>";
   $body .=  "        <td class='rep1' width='50%'><a href='report.php'>Show Province</a></td>";
   $body .=  "        <td class='rep1' width='50%'><a href='top50.php'>Top 50</a></td>";
   $body .= "      </tr>";
   $body .= "   </table>";
   $body .= "   <br></center>";
}

   $body .= "   <table border='0' align='center'><tr><td align='center' class='rep3'>" .$currentName ."</td></tr>";
   $body .= "   <tr><td class='rep1'><form method='get' action='changeName.php'>";
   $body .= "	   <input type='text' size='25' name='name'>";
   $body .= "	   <input type='submit' class='form' name='changebtn' value='Change Name'>";
   $body .= "	</form></td></tr></table>";  






function change($name) {
   global $database;
   $sql =  "UPDATE Kingdom SET name=$name WHERE kiId=" .$province->gekiId();
 }




templateDisplay($province, $body);
$database->shutdown();

?>