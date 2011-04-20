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

/******************************************************
 * EffectBase is the base class of all effects available
 * in the game.
 *
 * Made by Øystein Fladby 31.05.2003
 *
 * Changelog:
 *
 * Version: 2.0.test
 * Implemented in: RaceBase, BuildingBase
 ******************************************************/
if( !class_exists( "EffectBase" ) ) {
class EffectBase {

	////////////////////////////////////////////
	// EffectBase::baseFunction
	////////////////////////////////////////////
	// Function to get values based on a function name
	////////////////////////////////////////////
	function baseFunction( $functionName ) {
//		echo "-$functionName-";
		return $this->$functionName();
	}

	////////////////////////////////////////////
	// EffectBase::addXxx
	////////////////////////////////////////////
	// Various functions to get how much extra / less 
	// resources or military / building bonus / time 
	// (in percent) a race/spell/science/building gives.
	// Return negative value if you want to subtract
	// Returns:
	//    0 if not overrided by child
	////////////////////////////////////////////
	function addArmyMaintainanceCost($province=NULL)
	{
		return 0;
	}
	
	// Misc
	function addPeasantHousing ($province=NULL) {
		return 0;
	}
	
	// Knowledge
	function addResearchTime ($province=NULL) {
		return 0;
	}
	// trade
	function addResourceLoss ($province=NULL) {
		return 0;
	}
	// resources
	function addGoldIncome($province=NULL) {
		return 0;
	}
	function addMetalIncome($province=NULL) {
		return 0;
	}
	function addFoodIncome($province=NULL) {
		return 0;
	}
	function addPeasantGrowth($province=NULL) {
		return 0;
	}	
	
	//military
	function addMilitaryGoldCost($province=NULL) {
		return 0;
	}
	function addMilitaryMetalCost($province=NULL) {
		return 0;
	}
	function addMilitaryFoodCost($province=NULL) {
		return 0;
	}
	function addMilitaryTrainTime($province=NULL) {
		return 0;
	}
	function addDefense($province=NULL) {
		return 0;
	}
	function addAttack($province=NULL) {
		return 0;
	}
	function addAttackTime($province=NULL) {
		return 0;
	}
	function addMorale($province=NULL) {
		return 0;
	}
	
	// thievery
	function smartThieves($province=NULL){
		return 0;
	}
	function addSpyBonus($province=NULL) {
		return 0;
	}
	function addThieveryLoss ($province=NULL) {
		return 0;
	}
	function addThieveryOff($province=NULL) {
		return 0;
	}
	function addThieveryDef($province=NULL) {
		return 0;
	}
	function addInfluence($province=NULL) {
		return 0;
	}
	
	// buildings
	function addBuildingTime($province=NULL) {
		return 0;
	}	
	function addBuildingGoldCost($province=NULL) {
		return 0;	
	}
	function addBuildingMetalCost($province=NULL) {
		return 0;	
	}
	
	// explore
	function addExploreTime($province=NULL) {
		return 0;
	}
	function addExploreGoldCost($province=NULL) {
		return 0;
	}
	function addExploreSoldierCost($province=NULL) {
		return 0;
	}
	
	// magic
/*	function addSpellEffect() {
		return 0;
	}
*/	function addWizardUse($province=NULL) {
		return 0;
	}
	function addManaCost($province=NULL) {
		return 0;
	}
	function addMagicGoldCost($province=NULL) {
		return 0;
	}
	function addMagicMetalCost($province=NULL) {
		return 0;
	}
	function addMagicFoodCost($province=NULL) {
		return 0;
	}
	function addMagicPeasantCost($province=NULL) {
		return 0;
	}
	function addMagicChance($province=NULL) {
		return 0;
	}
	function addMagicProtection($province=NULL) {
		return 0;
	}
	function addMagicResistance($province=NULL) {
		return 0;
	}
/*	function addMagicMirror() {
		return 0;
	}
	function addMaxMana() {
		return 0;
	}
*/
} // end class
} // end if class exists
?>