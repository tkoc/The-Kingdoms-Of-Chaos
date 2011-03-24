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
 * Author: Øystein Fladby	23.03.2004
 * 
 * Version: 1.0
 * 
 */

if( !class_exists( "CryptBuilding" ) ) {
require("BuildingBase.class.inc.php");

class CryptBuilding extends BuildingBase{
	var $goldIncome = 100;			//get another 10 gold
	var $addPesantGrowth = 5;		//add 5% extra pesant growth
	var $maxBuildings = 20;			//max 20% your acres developed with this building => max 100% extra gold
	var $raceReq = array( "Undead" ); // races that have this building
	var $picture = "temple.jpg";
	function CryptBuilding( $buildingID ) {	
		$this->BuildingBase( $buildingID, 	// id	
							 "Crypt", 		// name
							 20, 			// ticks
							 1500, 			// gold
							 0, 			// metal	
							 "The crypt adds $this->goldIncome to your gold income, 
							 and $this->addPesantGrowth% to your peasant growth if 
							 you have developed this building on 1% of your land.
							 You will not get the benefits of buildings exceeding 
							 $this->maxBuildings% of your land." );
	}
	
	function goldIncome() {
		return $this->goldIncome;
	}
	function addPeasantGrowth() {
		return $this->addPesantGrowth;
	}
	function maxBuildings() {
		return $this->maxBuildings;
	}
	function pictureFile() {
		return $this->picture;
	}
	function raceRequirements() {
		return $this->raceReq;
	}
}
} // end if( !class_exists() )
?>
