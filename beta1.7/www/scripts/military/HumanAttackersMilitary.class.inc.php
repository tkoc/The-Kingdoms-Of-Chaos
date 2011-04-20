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

	if( !class_exists("HumanAttackersMilitary")) {
		class HumanAttackersMilitary extends MilitaryBase {
			var $picture = "scripts/military/human/legion.gif";
			var $MilitaryConst = NULL;
			
			function HumanAttackersMilitary( $militaryID ) {
				$this->MilitaryConst = $GLOBALS['MilitaryConst'];
				$info = "This military is specialized in attacking other provinces. They still got their basic defense skill, but they have become much better at attacking. This military unit needs an additional training time of 18 days after finishing recruits training";
				$type = $this->MilitaryConst->OFF_SOLDIERS;
				$ticks = 18;
				$cost = array("gold"=>450, "metal"=>0, "food"=>0);
				$strength = array("attack"=>3, "defense"=>1);
				$milConst = $GLOBALS['MilitaryConst'];

				$this->MilitaryBase($militaryID, "Legions", $info ,$type, $ticks, $cost, $strength);
			}


			function scienceRequirements() {
				return array("military" =>1, "infrastructure" => 0, "magic" => 0, "thievery" => 0);
			}

			function pictureFile() {
				return $this->picture;
			}

			function raceRequirements() {
				return "Human";
			}
			
			function trainFrom() {
				return "Recruits";
			}
			
			function sciReqTxt() {
				$txt = "<ul><b>Knowledge req:</b><br><br><li>Infrastructure: 0<li>Military: 1<li>Magic: 0<li>Thievery: 0<br>&nbsp;";
				return $txt;
			}

		}
		
	}
?>