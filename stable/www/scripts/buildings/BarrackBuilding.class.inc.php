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
/* BarrackBuilding class.
 * 
 * This class will handle all functions of a BarracksBuilding requires BuildingBase.class.inc.php
 * For more details about functions, see BuildingBase.class.inc.php
 * 
 * Author: Øystein Fladby	11.03.2003
 * 
 * Version: test
 * 
 */

if( !class_exists( "BarrackBuilding" ) ) {
require("BuildingBase.class.inc.php");

class BarrackBuilding extends BuildingBase{
	var $addMilitaryGoldCost = -2;	//reduce military unit gold cost by 2%
	var $picture = "barracks.jpg";
	var $maxBuildings=25;
	function BarrackBuilding( $buildingID ) {	
		$this->BuildingBase( $buildingID, 	
							 "Barrack", 
							 10, 
							 750, 
							 50,	
							 "The barrack reduces the gold cost of all military units by
							 ".abs($this->addMilitaryGoldCost)."% if one percent of your land is 
							 developed with barracks. You will not get the benefits of buildings 
							 exceeding $this->maxBuildings% of your land." );
	}
	function addMilitaryGoldCost() {
		return $this->addMilitaryGoldCost;
	}	
	function scienceRequirements() {
		return array( 'military' =>0, "infrastructure" => 0, "magic" => 0, "thievery" => 0 );
	}	
	function pictureFile() {
		return $this->picture;
	}
 	function maxBuildings() {
                return $this->maxBuildings;
        }
}
} // end if( !class_exists() )
?>
