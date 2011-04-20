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

require_once("MilitaryBase.class.inc.php");
	require_once("MilitaryConstants.class.inc.php");

	if( !class_exists("GiantElitesMilitary")) {
		class GiantElitesMilitary extends MilitaryBase {
			var $picture = "recruite";
			var $MilitaryConst = NULL;

			function GiantElitesMilitary( $militaryID ) {
				$this->MilitaryConst = $GLOBALS['MilitaryConst'];
				$ticks = 24;
				$info = "The elite Earth Giant is strong. Both in defense and offense, this unit will slay all before him. They are expensive and do need additional $ticks days of training, but a force of Earth elementals is frightning indeed.";
				$type = $this->MilitaryConst->ELITE_SOLDIERS;
				$cost = array("gold"=>1800, "metal"=>600, "food"=>350);
				$strength = array("attack"=>6, "defense"=>6);
				$this->MilitaryBase($militaryID, "Earth Giants", $info ,$type, $ticks, $cost, $strength);
			}
			
			function scienceRequirements() {
				return array('military' =>4, "infrastructure" => 0, "magic" => 0, "thievery" => 0);
			}
			
			function pictureFile() {
				return $this->picture;
			}

			function raceRequirements() {
				return "Giant";
			}
			
			function sciReqTxt() {
				$txt = "<ul><b>Knowledge req:</b><br><br><li>Infrastructure: 0<li>Military: 4<li>Magic: 0<li>Thievery: 0<br>&nbsp;";
				return $txt;
			}
			
			function trainFrom() {
				return "Recruits";
			}
		}
		
	}
?>