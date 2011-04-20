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

/* BlacksmithBuilding class.
 * 
 * This class will handle all functions of a BlacksmithBuilding requires BuildingBase.class.inc.php
 * For more details about functions, see BuildingBase.class.inc.php
 * 
 * Author: Øystein Fladby	26.10.2003
 * 
 * Version: 2.0
 * 
 */

if( !class_exists( "BlacksmithBuilding" ) ) {
require("BuildingBase.class.inc.php");

class BlacksmithBuilding extends BuildingBase{
	var $addAttack = 2;				//add attack strength by 1%
	var $maxBuildings = 20;			//max 20% your acres developed with this building => max 50% extra gold
	function BlacksmithBuilding( $buildingID ) {	
		$this->BuildingBase( $buildingID, 	
							 "Blacksmith", 
							 24, 
							 1000, 
							 500,	
							 "The blacksmith increases the attack power of your units by
							 ".abs($this->addAttack)."% if one percent of your land is 
							 developed with blacksmiths. You will not get the benefits of 
							 buildings exceeding $this->maxBuildings% of your land." );
	}
	function addAttack() {
		return $this->addAttack;
	}
	function scienceRequirements() {
		return array( 'military' =>0, "infrastructure" => 4, "magic" => 0, "thievery" => 0 );
	}	
	function maxBuildings() {
		return $this->maxBuildings;
	}
}
} // end if( !class_exists() )
?>