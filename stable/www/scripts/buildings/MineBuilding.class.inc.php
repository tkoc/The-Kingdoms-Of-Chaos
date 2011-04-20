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
/* MineBuilding class.
 * 
 * This class will handle all functions of a MineBuilding requires BuildingBase.class.inc.php
 * For more details about functions, see BuildingBase.class.inc.php
 * 
 * Author: Øystein Fladby	23.04.2003
 * 
 * Version: test
 * 
 */

if( !class_exists( "MineBuilding" ) ) {
require_once("BuildingBase.class.inc.php");

class MineBuilding extends BuildingBase{
	var $addGoldIncome = 10;
	var $addMetalIncome = 100;
	var $picture = "mine.jpg";
	var $scienceRequirements = array( 'military' =>0, "infrastructure" => 1, "magic" => 0, "thievery" => 0 );
	function MineBuilding( $buildingID ) {	
		$this->BuildingBase( $buildingID, 			// id
							 "Mine", 				// name
							 15, 					// ticks
							 1500, 					// gold
							 0,						// metal	
							 "The mines adds $this->addGoldIncome gold and 
							 $this->addMetalIncome metal for each mine you've got." );
	}
	function goldIncome() {
		return $this->addGoldIncome;
	}
	function metalIncome() {
		return $this->addMetalIncome;
	}
	function scienceRequirements() {
		return $this->scienceRequirements;
	}
	function pictureFile() {
		return $this->picture;
	}
}
} //  end if( !class_exists() )
?>
