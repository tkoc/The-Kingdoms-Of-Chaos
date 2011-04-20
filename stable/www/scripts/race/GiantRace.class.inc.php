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
/*************************************************************************
 * GiantRace is the class for the Giant race, describing their special 
 * abilities
 *
 * Made by Soptep 03.01.2010
 *
 *************************************************************************/
if( !class_exists( "GiantRace" ) ) {
	require_once( "RaceBase.class.inc.php");
	class GiantRace extends RaceBase {
		//numbers are in %
		
		// Negative
		var $addedPeasantHousing = -14; // They have smaller population
		var $addedPeasantGrowth = -20; // They grow slower
		var $addedFoodIncome = -20; // Produce less = eat more
		
		// Positive
		var $addedThieveryDef = 80; // They are resistant to thieves
		var $addedAttackTime = -20;
		var $addedAttack = 15;
		var $addedDefense = 15;
		
		
		
		function GiantRace ( $rID ) {
			$this->RaceBase ( $rID, 
					"Giant", 
					"The mythical creatures of the past have returned. Once thought to be legendary offspring of the gods long since extinct, Giants have come to take what is in their eyes rightfully theirs! Larger than any human, stronger than any Orc they are fearsome opponents. They are monstrous, savage creatures ready to fight for their land. When are fighting the earth is shaking and their enemies are struggling to defend themselves. Their massive size makes them ill suited for covert operations, but they can see well enough to spot enemy thieves with ease. Their army is more expensive and takes longer to train but once at the battlefield they will kill any creature on their way!
							<table width=\"100%\" >
							  <tr>
							  <td>
								<img src=\"".$GLOBALS['path_domain_img']."/guide/guide_giant.jpg\" border=\"0\">
							</td>
							<td align=LEFT>
					<br><br>
					".abs($this->addedThieveryDef)."% thievery defense.<br>
					".abs($this->addedAttackTime)."% shorter attack time.<br>
					".abs($this->addedAttack)."% bonus to attack strength.<br>
					".abs($this->addedDefense)."% bonus to defense strength.<br>
					".abs($this->addedPeasantHousing)."% less population.<br>
					".abs($this->addedPeasantGrowth)."% slower birthrates.<br>
					".abs($this->addedFoodIncome)."% less food produced.<br>
					Can not research thievery knowledge.<br><br>
							</td>
							</tr>
							</table>",
					array("magic"=>255, "infrastructure"=>255, "military"=>255, "thievery"=>0) );
		}
	
	
		function addPeasantHousing($province=NULL) {
			return $this->addedPeasantHousing;
		}
		
		function addPeasantGrowth($province=NULL) {
			return $this->addedPeasantGrowth;
		}
		
		function addFoodIncome($province=NULL) {
			return $this->addedFoodIncome;
		}
		
		function addThieveryDef($province=NULL) {
			return $this->addedThieveryDef;
		}
	
		function addAttackTime($province=NULL) {
			return $this->addedAttackTime;
		}
		
		function addAttack($province=NULL) {
			return $this->addedAttack;
		}
	
		function addDefense($province=NULL) {
			return $this->addedDefense;
		}
	} // end class giant race
} // end if class exists
?>
