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
/* MarketplaceBuilding class.
 * 
 * This class will handle all functions of a MarketplaceBuilding requires BuildingBase.class.inc.php
 * For more details about functions, see BuildingBase.class.inc.php
 * 
 * Author: Øystein Fladby	11.03.2003
 * 
 * Version: 2.0
 * 
 */

if( !class_exists( "MarketplaceBuilding" ) ) {
require("BuildingBase.class.inc.php");

class MarketplaceBuilding extends BuildingBase{
	var $addGoldIncome = 3;			// give 2% more gold
	var $addMetalIncome = 3;
	var $addFoodIncome = 3;
	var $addResourceLoss = -2;	// reduce resource loss by giving a negative value
	var $maxBuildings = 40;			// max 50% of acres developed with this building => max 100% extra gold
	var $picture = "marketplace.jpg";
	function MarketplaceBuilding( $buildingID ) {	
		$this->BuildingBase( $buildingID, 	 	// id	
							 "Marketplace", 	// name	
							 20, 					// ticks
							 2200, 				// gold
							 250, 				// metal	
							 "The marketplace adds $this->addGoldIncome% extra 
							 gold, $this->addMetalIncome% extra metal and $this->addFoodIncome% extra food
							 each day if you have 1% of your acres developed with them. It also reduces your 
							 trading loss by ".abs($this->addResourceLoss)."% if you got 1% of your acres built over with 
							 marketplaces. You will not get the  benefits from buildings exceeding 
							 $this->maxBuildings% of your land." );
	}
	function addGoldIncome() {
		return $this->addGoldIncome;
	}
	function addFoodIncome() {
		return $this->addFoodIncome;
	}
	function addMetalIncome() {
		return $this->addMetalIncome;
	}
	function addResourceLoss() {
		return $this->addResourceLoss;
	}
	function maxBuildings() {
		return $this->maxBuildings;
	}
	function pictureFile() {
		return $this->picture;
	}
}
} // end if( !class_exists() )
?>
