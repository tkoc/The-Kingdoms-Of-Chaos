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

if( !class_exists( "BuildingConstants" ) ) {
require_once( $GLOBALS['path_www_scripts'] . "effect/EffectConstants.class.inc.php" );

class BuildingConstants extends EffectConstants {

	// other
	var $PEASANTS_PR_ACRE = 15;

	//Requirements
	var $BUILDING_REQUIREMENTS	= "buildingRequirements";
	var $SCIENCE_REQUIREMENTS	= "scienceRequirements";
	var $RACE_REQUIREMENTS	= "raceRequirements";
	var $BUILDING_PREVENT	= "preventBuildingBuilding";
	var $SCIENCE_PREVENT	= "preventBuildingScience";

	// Housing
	var $THIEF_HOUSING 		= "thiefHousing";
	var $WIZARD_HOUSING 	= "wizardHousing";
	var $MILITARY_HOUSING 	= "militaryHousing";
	var $PEASANT_HOUSING 	= "peasantHousing";
	
	// employes
	var $EMPLOYES 			= "employes";
	
	// Income
	var $GOLD_INCOME 		= "goldIncome";
	var $METAL_INCOME 		= "metalIncome";
	var $FOOD_INCOME 		= "foodIncome";
	
	// Other
	var $GET_ID 			= "getID";
	var $GET_NAME			= "getName";
	var $GET_GOLD_COST		= "getGoldCost";
	var $GET_METAL_COST		= "getMetalCost";
	var $GET_FOOD_COST		= "";
	var $GET_DESCRIPTION	= "getDescription";
	var $GET_TICKS			= "getTicks";
	var $MAX_BUILDINGS		= "maxBuildings";
	var $START_BUILDINGS 	= "startValue";
	var $GET_PICTURE		= "getPictureFile";
		
} // end class
} // end if class exists
?>