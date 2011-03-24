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
include("headernew.php");
//require_once ("scripts/all.inc.php");
$GLOBALS["create_prov"] = 1;
require_once( $GLOBALS['WWW_SCRIPT_PATH']."Register.class.inc.php" );
require_once ($GLOBALS['WWW_SCRIPT_PATH']."Database.class.inc.php");
require_once ($GLOBALS['WWW_SCRIPT_PATH']."User.class.inc.php");
require_once ($GLOBALS['WWW_SCRIPT_PATH']."Province.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "requireLoggedOn.inc.php");

$database = new Database($DBLOGIN,$DBPASSW,$DBHOST,$DBDATABASE);
$database->connect();
$maintenance = false;
?>

<div class="content" style="background:url(img/stranger2.jpg); background-repeat:no-repeat; background-position:center;">
   <center>
	<?php
	if ($maintenance)
		echo "<br /><br /><b>The Game is under maintenance, Province Registrations will open soon!</b><br /><br />";
	else {
		if (!$GLOBALS['context']['user']['is_logged']) {
		   echo "
		Your session has expired! Please <a href='./login.php' target='_top'>login</a> again!
		<br /><br />
		<a href='./login.php' target='_top'>Click this link to go to login-page</a>
		";
		} 
		else if ($GLOBALS['user']->pId > 0) {
			header ("Location: ./scripts/showProvince.php");
			exit;
		}
		else {
			echo "<br /><b>Read <a href='worldforum'>forum</a> for any help!</b><br /><br />";
			
			$register = new Register( $database );
			echo $register->showNewProvince( $user->userID );
		}
	}
	?>
	</center>
	<br />
</div>
<?php include("footernew.php");?>
