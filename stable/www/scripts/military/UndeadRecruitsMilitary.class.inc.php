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



	if( !class_exists("UndeadRecruitsMilitary")) {

		class UndeadRecruitsMilitary extends MilitaryBase {
			var $picture = "recruite";
			var $MilitaryConst = NULL;
		

			function UndeadRecruitsMilitary( $militaryID ) {
				$this->MilitaryConst = $GLOBALS['MilitaryConst'];
				$info = "The slaves are the newly educated 'military' of the undead. There are always at least two vampires for each thousand slave in training watching the slaves and making sure they don't run away. You have to demoralise the peasants for at least 12 days to make them obay your orders.";
				$type = $this->MilitaryConst->SOLDIERS;
				$ticks = 12;
				$cost = array("gold"=>250, "metal"=>0, "food"=>0);
				$strength = array("attack"=>1, "defense"=>1);
				$this->MilitaryBase($militaryID, "Slaves", $info ,$type, $ticks, $cost, $strength);
//				$this->MilitaryBase($militaryID, "Recruits", 12, 300, 0, 50, 1, 1, 0, );
			}



			function pictureFile() {
				return "erecruits.gif";
			}



			function raceRequirements() {
				return "Undead";
			}
			
			function sciReqTxt() {
				$txt = "<ul><b>Knowledge req:</b><br><br><li>Infrastructure: 0<li>Military: 0<li>Magic: 0<li>Thievery: 0<br>&nbsp;";
				return $txt;
			}



		}

		

	}

?>