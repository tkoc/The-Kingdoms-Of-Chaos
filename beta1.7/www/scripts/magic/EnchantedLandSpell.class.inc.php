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

/* EnchantedLandSpell class is the class of a spell, derived from SpellBase.
 *
 * Author: Øystein Fladby 28.04.2003
 * 
 * Changelog:
 * 24.03.2004 Øystein: Added race requirements
 *
 * Version: 1.0
 * 
 */
 
require_once( "SpellBase.class.inc.php" );
if( !class_exists( "EnchantedLandSpell" ) ) {

class EnchantedLandSpell extends SpellBase {
	var $addMetalIncome = 9;		// give  more metal income each tick
	var $addFoodIncome = 7;		// give  more food income each tick
	var $addGoldIncome = 5;		// give  more gold each tick
	var $addPeasantGrowth = 6;		// give  more peasants each tick
	var $addMilitaryStrength = +0;  // add to miliitary strength
	var $raceReq = array( "Human", "Elf", "Dwarf", "Orc", "Giant" ); // this spell is available to
	var $mana = 8;
	
	function EnchantedLandSpell( $sID ) {
		require($GLOBALS["path_language"]."english/scripts/magic.txt");
		$this->SpellBase( 	$sID, 									// spell ID
							$myLanguage["enchanted-land-name"],						// name
							1.1,					// gold cost pr acre
							0.55,					// metal cost pr acre
							0,					// food cost pr acre
							0.44,					// needed wizards pr acre
							0,					// cast on self / provinces in own kingdom
							1,					// type indirect
							$myLanguage["enchanted-land-desc"],	// description
							false);									// picture
	}	
	function getNeededMana() {
		return $this->mana;
	}
	function addGoldIncome () {
		return $this->addGoldIncome;
	}
	function addMetalIncome () {
		return $this->addMetalIncome;
	}
	function addFoodIncome () {
		return $this->addFoodIncome;
	}
	function addPeasantGrowth () {
		return $this->addPeasantGrowth;
	}
        function addDefense($province=NULL) {
                return $this->addMilitaryStrength;
        }
        function addAttack($province=NULL) {
                return $this->addMilitaryStrength;
        }

	function raceRequirements() {
		return $this->raceReq;
	}
	function isKingdomSpell() {
		return true;
	}
}
} // end if ! class exists
?>