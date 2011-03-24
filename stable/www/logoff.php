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
/* Login.php
 *
 * Logs user off the system. 
 * 
 * Created by: Anders Elton
 * Last modified: 12.03.03
 */
require_once ("scripts/Database.class.inc.php");
require_once ("scripts/User.class.inc.php");
require_once ("scripts/all.inc.php");

$database = new Database($DBLOGIN,$DBPASSW,$DBHOST,$DBDATABASE);
$database->connect();

global $cId;
$cId = $_COOKIE['cId'];
if (isset($cId)==false) {$cId="";}

$user = new User ($cId,$database);  // no cookie

if ($user->isLoggedOn()==false) {
}
$user->logoff();
setcookie("showmenu","",time()-3600);
$database->shutdown();   // do we need this?
require("header.php");
?>

<table BGCOLOR="#86846D" width=800 HEIGHT=200 ALIGN="CENTER" CELLSPACING=1 CELLPADDING=1>
<tr>
<td BGCOLOR=BLACK ALIGN=CENTRE style="background:url(/img/snake.jpg) black ;background-repeat:no-repeat;;background-position: center">
<br>&nbsp
You are now logged off!
</td>
</tr>
</table>
<?php require("footer.php"); ?>
