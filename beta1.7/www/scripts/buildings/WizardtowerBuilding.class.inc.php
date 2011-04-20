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

/* WizardtowerBuilding class.
 * 
 * This class will handle all functions of a WizardtowerBuilding requires BuildingBase.class.inc.php
 * For more details about functions, see BuildingBase.class.inc.php
 * 
 * Author: Øystein Fladby	11.03.2003
 * 
 * Version: 2.0
 * 
 */

if( !class_exists( "WizardtowerBuilding" ) ) {
require("BuildingBase.class.inc.php");

class WizardtowerBuilding extends BuildingBase{
	var $addMagicChance = 1;			// better % chance to cast a spell pr % acre 
	var $addMagicProtection = 1;	// better % protection pr % acre 
	var $wizardHousing = 30;		// allows for this many wizards
	var $peasantHousing = 20;		// houses this many peasants
	var $picture = "tower.jpg";
	function WizardtowerBuilding( $buildingID ) {	
		$this->BuildingBase( $buildingID, 		// id
							 "Wizard Tower", 	// name
							 20, 				// ticks
							 750, 				// gold
							 0, 				// metal	
							 "The wizard tower allows you to have $this->wizardHousing wizards in your province 
							 and it has room for $this->peasantHousing people. It also increases your chance to
							 cast spells by $this->addMagicChance% and your magic protection by $this->addMagicProtection% 
							 if you have built wizard towers on 1% of your acres." );
	}
	function wizardHousing() {
		return $this->wizardHousing;
	}
	function peasantHousing() {
		return $this->peasantHousing;
	}
	function addMagicChance() {
		return $this->addMagicChance;
	}
	function addMagicProtection() {
		return $this->addMagicProtection;
	}
	function pictureFile() {
		return $this->picture;
	}
}
} // end if( !class_exists() )
?>