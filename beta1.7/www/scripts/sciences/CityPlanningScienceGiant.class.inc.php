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

//
require_once("ScienceBase.class.inc.php");
if( !class_exists( "CityPlanningScienceGiant" ) ) {
class CityPlanningScienceGiant extends ScienceBase {
		var $addedPeasantHousing = 10;
		var $raceReq = array( "Giant" );

		function CityPlanningScienceGiant ( $scienceID ) {
        	// id,name,ticks,gold,metal,description
			$this->ScienceBase( 
			$scienceID,"infrastructure", "Giant's City Planning", 48, 5000000, 1500000,
			"Allows the construction of larger metropolises. <b>Increase population by $this->addedPeasantHousing %</b>. Available only to Giants.",
	
			/* requires */
			array('military' =>8, "infrastructure" => 8, "magic" => 0, "thievery" => 0),
		
			/* Gives */
			array('military' =>8, "infrastructure" => 8, "magic" => 0, "thievery" => 0)
			);    
		}
		function addPeasantHousing($province=NULL) 
		{
			return $this->addedPeasantHousing;
		}
	// already applied by effect stuff?
		function doTick (&$database) 
		{
			$multiplier = 1+ ($this->addedPeasantHousing/100);
			$database->query("UPDATE Province RIGHT JOIN Science on (Province.pID=Science.pID AND Science.sccID='$this->scID' AND Science.ticks<1) set Province.buildingPeasantPopulation=Province.buildingPeasantPopulation*$multiplier");
		}
		
		function raceRequirements() {
			return $this->raceReq;
		}
			
	}
} // end if( !class_exists() )
?>