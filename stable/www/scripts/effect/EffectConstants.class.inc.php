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

if( !class_exists( "EffectConstants" ) ) {
require_once( $GLOBALS['WWW_SCRIPT_PATH'] . "baseclass/ConstantsBase.class.inc.php" );

class EffectConstants extends ConstantsBase {
	//misc
	var $ADD_PEASANT_HOUSING		= "addPeasantHousing";
	// knowledge
	var $ADD_RESEARCH_TIME			= "addResearchTime";
	// trade
	var $ADD_RESOURCE_LOSS			= "addResourceLoss";
	// resources
	var $ADD_GOLD_INCOME			= "addGoldIncome"; 
	var $ADD_METAL_INCOME 			= "addMetalIncome"; 
	var $ADD_FOOD_INCOME 			= "addFoodIncome"; 
	var $ADD_PEASANT_GROWTH			= "addPeasantGrowth"; 
	var $ADD_ARMY_MAINTAINANCE_COST		= "addArmyMaintainanceCost"; 
	
	//military
	var $ADD_MILITARY_GOLD_COST 	= "addMilitaryGoldCost"; 
	var $ADD_MILITARY_METAL_COST 	= "addMilitaryMetalCost"; 
	var $ADD_MILITARY_FOOD_COST 	= "addMilitaryFoodCost"; 
	var $ADD_MILITARY_TRAIN_TIME 	= "addMilitaryTrainTime"; 
	var $ADD_DEFENSE 				= "addDefense"; 
	var $ADD_ATTACK 				= "addAttack"; 
	var $ADD_ATTACK_TIME 			= "addAttackTime";
	var $ADD_MORALE					= "addMorale";
	
	// thievery	
	var $SMART_THIEVES				="smartThieves";
	var $ADD_INFLUENCE				= "addInfluence";
	var $ADD_SPY_BONUS 				= "addSpyBonus"; 
	var $ADD_THIEVERY_OFF			= "addThieveryOff";	
	var $ADD_THIEVERY_DEF			= "addThieveryDef";	
	var $ADD_THIEVERY_LOSS			= "addThieveryLoss";
	
	// buildings
	var $ADD_BUILDING_TIME 			= "addBuildingTime"; 
	var $ADD_BUILDING_GOLD_COST 	= "addBuildingGoldCost"; 
	var $ADD_BUILDING_METAL_COST 	= "addBuildingMetalCost";
	
	// explore
	var $ADD_EXPLORE_TIME 			= "addExploreTime"; 
	var $ADD_EXPLORE_GOLD_COST 		= "addExploreGoldCost"; 
	var $ADD_EXPLORE_SOLDIER_COST 	= "addExploreSoldierCost"; 
	
	// magic
//	var $ADD_SPELL_EFFECT			= "addSpellEffect";			// adds on the effect of direct spells / triggered cast at self
	var $ADD_WIZARD_USE 			= "addWizardUse"; 			// adds on the recommended wizards
	var $ADD_MANA_COST 				= "addManaCost"; 			// adds on mana used to cast a spell
	var $ADD_MAGIC_GOLD_COST 		= "addMagicGoldCost"; 		// adds on gold cost to cast a spell
	var $ADD_MAGIC_METAL_COST 		= "addMagicMetalCost";
	var $ADD_MAGIC_FOOD_COST 		= "addMagicFoodCost";
	var $ADD_MAGIC_PEASANT_COST		= "addMagicPeasantCost"; 	// adds on peasants which has to die to cast a spell
	var $ADD_MAGIC_CHANCE 			= "addMagicChance";			// adds on the chance to cast a spell
	var $ADD_MAGIC_PROTECTION		= "addMagicProtection";		// adds on the chance that an unfriendly spell cast at you fails
	var $ADD_MAGIC_RESISTANCE		= "addMagicResistance";		// adds magic resistance against unfriendly spells cast at you
//	var $ADD_MAGIC_MIRROR			= "addMagicMirror";			// 
//	var $ADD_MAX_MANA				= "addMaxMana";

} // end class
} // end if class exists
?>