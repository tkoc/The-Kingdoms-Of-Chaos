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
/* StableBuilding class.
 * 
 * This class will handle all functions of a StableBuilding requires BuildingBase.class.inc.php
 * For more details about functions, see BuildingBase.class.inc.php
 * 
 * Author: Øystein Fladby	11.03.2003
 * 
 * Version: 1.0
 * 
 */

if( !class_exists( "StableBuilding" ) ) {
require("BuildingBase.class.inc.php");

class StableBuilding extends BuildingBase{
	var $addAttackTime = -2;			// better attack time by giving a negative number
	var $maxBuildings = 15;			// max 15% of acres developed with this building 
	var $raceReq = array("Human", "Elf", "Dwarf", "Undead", "Giant");
	var $scienceReq = array( "military" =>4, "infrastructure" => 4, "magic" => 0, "thievery" => 0 );
	function StableBuilding( $buildingID ) {	
		$this->BuildingBase( $buildingID, 	 	// id	
							 "Stable",	 	// name	
							 20, 					// ticks
							 2000, 				// gold
							 150, 				// metal	
							 "The stable reduces your attack time by ".abs($this->addAttackTime)."% if you 
							 got 1% of your acres built over with them. You will not get the  benefits from 
							 buildings exceeding $this->maxBuildings% of your land." );
	}
	function addAttackTime() {
		return $this->addAttackTime;
	}
	function maxBuildings() {
		return $this->maxBuildings;
	}
	function raceRequirements() {
		return $this->raceReq;
	}
	function scienceRequirements() {
		return $this->scienceReq;
	}
}
} // end if( !class_exists() )
?>
