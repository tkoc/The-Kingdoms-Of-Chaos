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

if( !class_exists( "AgricultureScience" ) ) {

class AgricultureScience extends ScienceBase {
		var	$ADD_FOOD = "30";
		
        function AgricultureScience( $scienceID ) {
                                                                        // id,name,ticks,gold,metal,description
                $this->ScienceBase( $scienceID,"infrastructure", "Agriculture", 24, 750000, 0,
				"This advance will allow your peasants to use a plow on the farmland.  This allows your peasants 
				to use a three-field crop rotation instead of two-field rotation, <b>increasing food production of 
				your farms by $this->ADD_FOOD%.</b>",
/* requires */
array('military' =>0, "infrastructure" => 1, "magic" => 0, "thievery" => 0),
/* Gives */
array("military" => 0, "infrastructure" => 2, "magic" => 0, "thievery" => 0)

		);    
        }
		
// already applied by effect class???
	function doTick (&$database) {
		$foodMultiplier = 1+ ($this->ADD_FOOD/100);
		$database->query("UPDATE Province RIGHT JOIN Science on (Province.pID=Science.pID AND Science.sccID='$this->scID' AND Science.ticks<1) set Province.foodChange=Province.foodChange*$foodMultiplier");
	}
	function addFoodIncome($province=NULL) {
		return $this->ADD_FOOD;
	}


}
} // end if( !class_exists() )
?>