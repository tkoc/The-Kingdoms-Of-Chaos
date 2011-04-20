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

/*************************************************************************
 * ElfRace is the class for the elf race, describing their special 
 * abilities
 *
 * Made by Øystein Fladby 31.05.2003
 * 
 * Changelog:
 * 23.03.04 Øystein Added full description
 * 26.10.03 Øystein	Added allowed science param in RaceBase call and changed some properties
 *
 * Version: test
 *
 *************************************************************************/
if( !class_exists( "ElfRace" ) ) {
require_once( "RaceBase.class.inc.php");
class ElfRace extends RaceBase {
	var $addedMagicChance = 20;
	var $addedMagicProtection = 20;
	var $addedWizardUse = -10;
	var $addedManaCost = -20;
	var $addedBuildingTime = -20;
	var $addedPeasantGrowth = -10;	//Elves multiplies slower
	var $addedFoodIncome = 10;	//Elves eat less
	
	function ElfRace ( $rID ) {
		$this->RaceBase ( $rID, 
								"Elf", 
								"In the woods of Chaos you will find the beautiful elves. With their care for beauty - arts and the like - elves started to use magic to enchant their lands. Other races consider elves to have the best magicians in the world. Elves, however, consider their swords to be just as good.

						<table width=\"100%\" >
						  <tr>
						  <td>
								<br><br>".abs($this->addedManaCost)."% less mana used.<br>
								".abs($this->addedMagicProtection)."% magic protection.<br>
								".abs($this->addedMagicChance)."% better chance of success in spellcasting.<br>
								".abs($this->addedWizardUse)."% less wizards recommended to cast spells.<br>
								".abs($this->addedFoodIncome)."% more food produced.<br>
								".abs($this->addedBuildingTime)."% faster construction time.<br>
								".abs($this->addedPeasantGrowth)."% slower birthrates.<br><br>
						</td>
						<td align=RIGHT>
						<img src=\"".$GLOBALS['path_domain_img']."/guide/guide_elf.jpg\" border=\"0\">
						</td>
						</tr>
						</table>",
								array('military' =>255, "infrastructure" => 255, "magic" => 255, "thievery" => 255) // max science level.
		 );
	}
	function addMagicChance($province=NULL) {
		return $this->addedMagicChance;
	}
	function addMagicProtection($province=NULL) {
		return $this->addedMagicProtection;
	}
	function addWizardUse($province=NULL) {
		return $this->addedWizardUse;
	}
	function addManaCost($province=NULL) {
		return $this->addedManaCost;
	}
	function addBuildingTime($province=NULL) {
		return $this->addedBuildingTime;
	}
	function addFoodIncome($province=NULL) {
		return $this->addedFoodIncome;
	}
	function addPeasantGrowth($province=NULL) {
		return $this->addedPeasantGrowth;
	}
} // end class elf race
} // end if class exists
?>