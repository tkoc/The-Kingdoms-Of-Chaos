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
 * DwarfRace is the class for the dwarf race, describing their special 
 * abilities
 *
 * Made by Jørgen Belsaas 25.10.2003
 *
 * Changelog:
 * Øystein 23.03.04: Added full description
 *
 * Version: test
 *
 *************************************************************************/
if( !class_exists( "DwarfRace" ) ) {
	require_once( "RaceBase.class.inc.php");
	class DwarfRace extends RaceBase {
		//numbers are in %
		var $addedMagicProtection = 70;
		var $addedIncomeGold = 5;
		var $addedIncomeMetal = 10;
		var $addedAttackTime = 20;
		var $addedPeasantHousing = 20;
		var $addedDefense = 5;
		
		function DwarfRace ( $rID ) {
			$this->RaceBase ( $rID, 
					"Dwarf", 
					"Deep in the mountains, a race long forgotten has surfaced to the earth.  Dwarves generally don't believe in magic.  They are more concerned about their mines and gold revenue.  The famous dwarf Thorgrim Ironbeard once said: 'You all might think we are small and weak.  But when we surface at dawn, we throw shadows just as big as the mountain.'
							<table width=\"100%\" >
							  <tr>
							  <td>
								<img src=\"".$GLOBALS['path_domain_img']."/guide/guide_dwarf.jpg\" border=\"0\">
							</td>
							<td align=LEFT>
					<br><br>Starts with mining science.<br>
					".abs($this->addedIncomeGold)."% additional income to gold.<br>
					".abs($this->addedIncomeMetal)."% additional income to metal.<br>
					".abs($this->addedMagicProtection)."% magic defense.<br>
					".abs($this->addedAttackTime)."% longer attack time.<br>
					".abs($this->addedDefense)."% bonus to defense strength.<br>
					".abs($this->addedPeasantHousing)."% extra housing.<br>
					Can not research magic knowledge.<br><br>
							</td>
							</tr>
							</table>",
					array("magic"=>0, "infrastructure"=>255, "military"=>255, "thievery"=>255) );
		}
		
		function getStartScience() {
			return array("magic"=>0, "infrastructure"=>1, "military"=>0, "thievery"=>0);
		}
	
		function addMagicProtection($province=NULL) {
			return $this->addedMagicProtection;
		}
	
		function addGoldIncome($province=NULL) {
			return $this->addedIncomeGold;
		}
	
		function addMetalIncome($province=NULL) {
			return $this->addedIncomeMetal;
		}
	
		function addAttackTime($province=NULL) {
			return $this->addedAttackTime;
		}
		
		function addPeasantHousing($province=NULL) {
			return $this->addedPeasantHousing;
		}
	
		function addDefense($province=NULL) {
			return $this->addedDefense;
		}
	
	} // end class elf race
} // end if class exists
?>
