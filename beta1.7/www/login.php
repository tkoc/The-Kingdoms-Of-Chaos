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
// ToDo: Elegxo gia sfalmata
include_once ("./headernew.php");
?>
<div class="content" style="background-image:url('./img/loginback-dark.jpg'); padding:10px 0px;">
	<center>
	<?php 
	if ($GLOBALS['context']['user']['is_logged']) {
		header ("Location: ./scripts/showProvince.php");
		exit;
	}
	else {
		ssi_login($domain."scripts/showProvince.php");
	}
	$config = $GLOBALS['config'];
	
	echo "<br />";
	if (isset($config['status'])) {
		$remainingTicks = $config['statusLength']-$config['ticks'];
		$totalSeconds = $config['totalTickTime']*$remainingTicks;
		$lastTick = strtotime($config['lastTickTime']);
		$estimatedDate = date ("l j F, G:i",$lastTick+$totalSeconds);
		
		$message = "Age #".$config['age'].": <b>".$config['status'].".</b>";

		if ($config['status']=='Pause') 
			$message .= " The Age will start in ".$remainingTicks." tick".($remainingTicks == 1 ? "" : "s")." - $estimatedDate.";
		else if ($config['status']=='Ended') 
			$message .=  " Next Age will open for signups in ".$remainingTicks." tick".($remainingTicks == 1 ? "" : "s")." - $estimatedDate.";
		
		echo "$message<br />";
		echo ssi_howManyOnline()." online right now.";
	} 
	else {
		echo "Game status: <b><font color=red>GAME IS DOWN (Database failure)</font></b>";
	}
	include_once("./time.php");
	?>
	</center>
<?php include_once ("./footernew.php"); ?>
