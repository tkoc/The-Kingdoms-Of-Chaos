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
/* Building class to be extended by all buildings. Contains all functionality
 * available to a building.
 * 
 * O B S : Remember to update Buildings.class.inc.php and buildingDescription.php when making modifications.
 *
 ******
 * V2
 ******
 * Øystein 	01.06.03	- Added EffectBase extension
 *
 ******
 * V1
 ******
 * Øystein	23.04.03	- Added building images function
 * Øystein	09.03.03 	- Added lots of functions.
 * Author: Øystein Fladby 21.02.2003
 * 
 *  
 * Version: 2.0.test
 * 
 */

if( !class_exists("BuildingBase") ) {
require_once( WWW_SCRIPT_PATH . "effect/EffectBase.class.inc.php" );
require_once( WWW_SCRIPT_PATH . "buildings/BuildingConstants.class.inc.php" );

class BuildingBase extends EffectBase {
	var $bID;			// the building ID
	var $name="NN";			// the building name
	var $ticks=24;			// number of ticks to build this building
	var $goldCost=0;		// the cost of this building in gold
	var $metalCost=0;		// the cost of this building in metal
	var $description="";	// the description of this building
	var $picturePath = "img/";	// the path to the building pictures
	
	function BuildingBase( $inBid, $inName, $inTicks, $inGoldCost, $inMetalCost, $inDescription ) {
		$this->bID = $inBid;
		$this->name = $inName;
		$this->ticks = $inTicks;
		$this->goldCost = $inGoldCost;
		$this->metalCost = $inMetalCost;
		$this->description = $inDescription;
	}
	
	////////////////////////////////////////////
	// BuildingBase::xxxIncome
	////////////////////////////////////////////
	// Various functions to get how much resources a
	// building produces each tick
	// Returns:
	//    0 if not overrided by child
	//////////////////////////////////////////// 
	function goldIncome() {
		return 0;
	}	
	function metalIncome() {
		return 0;
	}
	function foodIncome() {
		return 0;
	}	
	
	////////////////////////////////////////////
	// BuildingBase::xxxHousing
	////////////////////////////////////////////
	// Various functions to get how many people a
	// building has room for or employes
	// Returns:
	//    0 if not overrided by child
	//	 20 for pesantHousing
	////////////////////////////////////////////
	function wizardHousing() {
		return 0;
	}
	function thiefHousing() {
		return 0;
	}
	function militaryHousing() {
		return 0;
	}
	function peasantHousing() {
		return 20;
	}
	
	function employes() {
		return 0;
	}
	
	
	////////////////////////////////////////////
	// BuildingBase::xxxRequirements
	////////////////////////////////////////////
	// Various functions to get the requirements
	// needed to be allowed to build a building
	// Returns:
	//    	false if not overrided by child
	//		else array of building NAMEs or science ids, race NAMEs etc.
	// 	 	which have to be built / researched / fulfilled before
	// 	 	this building is allowed to be built 
	////////////////////////////////////////////
	function scienceRequirements() {
		$sciReq = array( 'military' =>0, "infrastructure" => 0, "magic" => 0, "thievery" => 0 );		// the required sciences to build this building
		return $sciReq;
	}
	
	function buildingRequirements() {
		return false;
	}
	
	function raceRequirements() {
		return false;
	}
	
	////////////////////////////////////////////
	// BuildingBase::preventBuildingXxx
	////////////////////////////////////////////
	// Various functions to get buildings/science which 
	// prevents the building of this building
	// Returns:
	//    	false if not overrided by child
	//		else array of building NAMEs or science ids
	// 	 	which must not be built / researched to
	// 	 	allow this building to be built 
	////////////////////////////////////////////
	function preventBuildingScience() {
		return false;
	}
	
	function preventBuildingBuilding() {
		return false;
	}	
	
	////////////////////////////////////////////
	// BuildingBase::maxBuildings
	////////////////////////////////////////////
	// The max percentage of acres a building might 
	// fill of a province. 0 = unlimited
	// Returns:
	//    0 if not overrided by child
	////////////////////////////////////////////
	function maxBuildings() {
		return 0;
	}
	
	////////////////////////////////////////////
	// BuildingBase::startValue
	////////////////////////////////////////////
	// Get the number of buildings of this building
	// type which the player should start a game with
	// Returns:
	//    0 if not overrided by child
	////////////////////////////////////////////
	function startValue() {
		return 0;
	}
	
	////////////////////////////////////////////
    // BuildingBase::getPictureFile
    ////////////////////////////////////////////
    // Function to get the path and name of the 
	// picture of this building
    // Returns:
    //    string with path and picture name
    ////////////////////////////////////////////
	function getPictureFile() {
		$file = $this->picturePath.$this->pictureFile();
		if( file_exists( $file ) ) {
			return $file;
		} else {
			return "no picture found";
		}
	}
	
	////////////////////////////////////////////
    // BuildingBase::pictureFile
    ////////////////////////////////////////////
    // Function to get the name of the
    // picture of this building. Should be overrided
	// by child class
    // Returns:
    //    string with picture name
    ////////////////////////////////////////////
	function pictureFile() {
		return "buildings.gif";
	}

	////////////////////////////////////////////
	// BuildingBase::getXxx
	////////////////////////////////////////////
	// Various functions to get the info recieved in the constructor
	// Returns:
	//    value recieved by constructor
	////////////////////////////////////////////
	function getID() {
		return $this->bID;
	}
	function getName() {
		return $this->name;
	}
	function getGoldCost() {
		return $this->goldCost;
	}
	function getMetalCost() {
		return $this->metalCost;
	}
	function getTicks() {
		return $this->ticks;
	}
	function getDescription() {
		return $this->description;
	}
}
} // end if( !class_exists() )
?>
