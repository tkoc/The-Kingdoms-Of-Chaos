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
/* CryptBuilding class.
 * 
 * This class will handle all functions of a CryptBuilding requires BuildingBase.class.inc.php
 * For more details about functions, see BuildingBase.class.inc.php
 * 
 * Author: Anders Elton	04.01.2005
 * 
 * Version: 1.0
 * 
 */

if( !class_exists( "DocksBuilding" ) ) {
require("BuildingBase.class.inc.php");

class DocksBuilding extends BuildingBase{
	var $maxBuildings = 20;			//max 20% your acres developed with this building => max 100% extra gold
	var $picture = "temple.jpg";

	// effect
	var $peasantHousing = 25;			// houses # pesants / military
	var $extraHousing = 10;			// allows for this many thieves and wizards
	var $foodIncome = 15;			//a dock produces # units of food each tick
	var $addMetalIncome = 15;
	var $goldIncome = 50;			//get another # gold
	var $addPesantGrowth = -1;		//reduce % pesant growth
	var $addAttack = 0.5;				//add attack strength by %
	var $addedResearchTime=-1;

	function DocksBuilding( $buildingID ) {	
		$this->BuildingBase( $buildingID, 	// id	
							 "Dock", 		// name
							 20, 			// ticks
							 1500, 			// gold
							 1500, 			// metal	
							 "Docks add $this->goldIncome gc to your gold income, produce $this->foodIncome units of food and $this->addMetalIncome metal each day. Houses $this->extraHousing additional thieves and wizards. Moreover people can live in the boats, so there is more room for population. Each Dock houses $this->peasantHousing people. However due to diseases brought to you from afar it will reduce your peasant growth by ".abs($this->addPesantGrowth)."%. Finally due to boats you can have alternative ways of attacking which increases your attack strength by ".abs($this->addAttack)."%. Researching new knowledges will also go faster by ".abs($this->addedResearchTime)."%. These effects apply if you have developed this building per 1% of your land." );
	}
	function scienceRequirements() {
		return array( 'military' =>0, "infrastructure" => 8 /*trade science*/, "magic" => 0, "thievery" => 0 );
	}	

	function wizardHousing() {
		return $this->extraHousing;
	}
	function addPeasantGrowth() {
		return $this->addPesantGrowth;
	}
	
	function thiefHousing() {
		return $this->extraHousing;
	}	
	function foodIncome() {
		return $this->foodIncome;
	}
	
	function maxBuildings() {
		return $this->maxBuildings;
	}
	function pictureFile() {
		return $this->picture;
	}
	function metalIncome() {
		return $this->addMetalIncome;
	}
	function goldIncome() {
		return $this->goldIncome;
	}
	function addResearchTime(){
		return $this->addedResearchTime;
	}
	function addAttack() {
		return $this->addAttack;
	}
	function peasantHousing() {
		return $this->peasantHousing;
	}		
	
}
} // end if( !class_exists() )
?>
