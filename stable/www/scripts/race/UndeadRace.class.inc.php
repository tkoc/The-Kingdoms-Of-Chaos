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
 * UndeadRace is the class for the undead race, describing their special 
 * abilities
 *
 * Made by Øystein Fladby 23.03.2004
 * 
 * Changelog:
 *
 * Version: 1.0
 *
 *************************************************************************/
if( !class_exists( "UndeadRace" ) ) {
require_once( "RaceBase.class.inc.php");
class UndeadRace extends RaceBase {
	var $addedMagicChance = 20;
	var $addedWizardUse = -10;
	var $addedManaCost = -20;
	var $addedBuildingTime = -20;
	var $addedResearchTime = 30;
	var $addedResourceLoss = -20;
	var $addedFoodIncome = 50;
	
	function UndeadRace ( $rID ) {
		$this->RaceBase ( $rID, 
								"Undead", 
								"The deceased usually make a gently but definite journey to the afterlife. The art of necromancy, practised by evil wizards disrupted that passing. And when their creations ran out of hand, the wizards were the first to be sacrificed to the Undead gods. Undead are neither living nor dead and their magical origins give them an edge in magic, even though they need to make sacrifices to use it... 
								<table width=\"100%\" >
								<tr>
								<td>
								<br><br>".abs($this->addedManaCost)."% less mana used.<br>
								".abs($this->addedMagicChance)."% better chance of success in spellcasting.<br>
								".abs($this->addedWizardUse)."% less wizards recommended to cast spells.<br>
								".abs($this->addedBuildingTime)."% faster construction time.<br>
								".abs($this->addedResearchTime)."% more time to research a knowledge.<br>
								".abs($this->addedResourceLoss)."% less resources lost when trading.<br>
								".abs($this->addedFoodIncome)."% more food produced.<br><br>",
								array('military' =>255, "infrastructure" => 255, "magic" => 255, "thievery" => 255) // max science level.
		 );
	}
	function addResourceLoss ($province=NULL) {
		return $this->addedResourceLoss;
	}
	function addMagicChance($province=NULL) {
		return $this->addedMagicChance;
	}
	function addResearchTime($province=NULL) {
		return $this->addedResearchTime;
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
} // end class undead race
} // end if class exists
?>