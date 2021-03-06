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

	if( !class_exists("HumanElitesMilitary")) {
		class HumanElitesMilitary extends MilitaryBase {
			var $picture = "scripts/military/human/paladins.gif";
			var $MilitaryConst = NULL;
				
			function HumanElitesMilitary( $militaryID ) {
				$this->MilitaryConst = $GLOBALS['MilitaryConst'];
				$info = "The paladin is of the elite branch in the human military, and can be used both for attacking and defending. This military is better than specialists at both. This unit needs an additional trainingtime of 24 days after finishing recruits training. To train this unit you need gold, food, and metal";
				$type = $this->MilitaryConst->ELITE_SOLDIERS;
                                //echo "<br><br><br>******************<br>";
                                //echo "* MILTYPE: $type";
				$ticks = 24;
				$cost = array("gold"=>1500, "metal"=>500, "food"=>300);
				$strength = array("attack"=>5, "defense"=>5);
				$this->MilitaryBase($militaryID, "Paladins", $info ,$type, $ticks, $cost, $strength);
			}

			function scienceRequirements() {
				return array('military' =>4, "infrastructure" => 0, "magic" => 0, "thievery" => 0);
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
				$txt = "<ul><b>Knowledge req:</b><br><br><li>Infrastructure: 0<li>Military: 4<li>Magic: 0<li>Thievery: 0<br>&nbsp;";
				return $txt;
			}

		}
		
	}
?>