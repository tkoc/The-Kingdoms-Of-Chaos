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

require_once("all.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "User.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "requireLoggedOn.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Province.class.inc.php");

if (!isset($_GET["action"]))
	$action = 1;
else 
	$action = (int)$_GET["action"];

$html = "";
$html .= showSubNavigation ();
$html .= '<div style="clear:both">'; // It is needed to clear the float of the Sub Navigation Menu

// Get the actual data
switch ($action) {
    case 1:
        $html .= showProvincePrefs ();
        break;
    case 100:
        $html .= showCheaters();
        break;
    default:
        $html .= showProvincePrefs ();
        break;
}

$html .= '</div>';

$province->getProvinceData();

templateDisplay($province,$html,"","");



function showSubNavigation () {
	$html = '
		<div class="game-sub-buttons">
			<ul>
				<li>
					<a href="preferences.php">Province Prefs</a>'; if ($GLOBALS['context']['user']['is_admin'] || $GLOBALS['context']['user']['username'] == "DevaDevil") $html .= '|';
			$html .= '
				</li>';
		if ($GLOBALS['context']['user']['is_admin'] || $GLOBALS['context']['user']['username'] == "DevaDevil") {
			$html .= '
				<li>
					<a href="preferences.php?action=100">Cheaters</a>
				</li>';
		}
		$html .= '
			</ul>
		</div>';
		
		
	return $html;
}


function showProvincePrefs () {
	global $user, $db;
	$html = "";
	if (isset($_POST['submit'])) {
		if (empty($_POST['validation']) || $_POST['validation'] != 4) {
			$feedback = "<b>You need to enter the correct answer on the 2+2 question field.</b><br /><br />";
		}
		else {
			if (isset($_POST['action'])) {
				if (strcmp ($_POST['action'], "delete") == 0) {
					$data = mysql_fetch_array ($GLOBALS['db']->selectField ("*", "User", "username",$GLOBALS['context']['user']['username'])); 
					$db->updateField ("Province", "status", "Deleted", "pID", $data['pID']);
					
					$feedback = "<b>Your Province is deleted. In a few minutes you will be allowed to recreate</b><br /><br />";
				}
				else if(strcmp ($_POST['action'], "vacation") == 0) {
					
					$detailProvince = new Province($user->getpID(), $GLOBALS['database']);
					$detailProvince->getProvinceData();
					$detailProvince->getMilitaryData();
					$milOut = $detailProvince->milObject->getMilitaryOut();
					if (is_array($milOut) || ($detailProvince->influence < 80) || ($detailProvince->mana < 80)) {
						$feedback = "<b>You can not go into vacation mode at this point. You can't go into vacation mode while taking hostile actions.</b><br /><br />";
					}
					else {
						$user->database->query("UPDATE Province set vacationMode='true', vacationTicks=0 WHERE pID='$user->pId'");
						$feedback = "<b>Your province is now on vacation mode. You will be able to return to normal mode after 48 ticks</b><br /><br />";			
					}
				}
			}
			else
				$feedback = "<b>You didn't choose any function.</b><br /><br />";
	
		}
	}

	if (isset ($feedback))
		$html .= $feedback;
	
	$html .= '
		Welcome '.$GLOBALS['context']['user']['username'].'! Choose one of the functions below:<br /><br />
		
		<form action="" method="post">
		<label for="deleteProvince">Delete Province</label> <input name="action" type="radio" id="deleteProvince" value="delete" /><br />
		<label for="putOnVacation">Put on Vacation</label> <input name="action" type="radio" id="putOnVacation" value="vacation" /><br /><br />
		What is the result of 2+2 (value in integer)? <input name="validation" id="validation" type="text" value="" /><br /><br />
		Your action is irreversible. Proceed with caution! <input name="submit" type="submit" value="submit" />
		</form>
		';
		
	return $html;
}


function showCheaters () {
	$html = "";
	
	$result_smf = $GLOBALS['forumdb']->selectField6 ("*", "smf_members", "member_ip", "ASC"); // Take the data from smf
	
	$previous['user'] = "";
	$previous['ip'] = "";
	$found = false;
	$common = array();
	
	while ($data = mysql_fetch_array ($result_smf)) {
		if (strcmp ($previous['ip'], $data['member_ip'])==0) {
			if ($found == false)
				$common[$previous['ip']][] = $previous['user'];
			
			$common[$previous['ip']][] = $data['member_name'];
			
			$found = true;
		}
		else {
			$previous['ip'] = $data['member_ip'];
			$previous['user'] = $data['member_name'];
			$found = false;
		}
		
	}
	
	
	foreach ($common as $ip => $users) {
		//var_dump ($common);
		$html .= "IP: ".$ip." | ";
		$flag = 0;
		foreach ($users as $value) {
			if ($flag != 0)
				$html .=  ' - ';
			
			$result = mysql_fetch_array ($GLOBALS['db']->selectField ("*", "User", "username", $value)); // Take the data from Users
			$html .=  '<a href="'.$GLOBALS['path_domain_root'].'administration/gameUsers.php?userID='.$result["userID"].'">'.$value.'</a>';
			$flag = 1;
		}
		$html .=  "<br />";
	}


	return $html;
}

?>