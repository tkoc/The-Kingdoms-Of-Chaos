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
// 

if( !class_exists( "ConstructionScience" ) ) {
require_once("ScienceBase.class.inc.php");

class ConstructionScience extends ScienceBase {
	var $ADD_BUILDING_TIME = -30;
        function ConstructionScience( $scienceID ) {
                                                                        // id,name,ticks,gold,metal,description
                $this->ScienceBase( $scienceID, "infrastructure" ,"Architecture", 36, 3000000, 200000,"
The discovery of Architecture allows your peasants to build more than just simple huts.  Not only can they make bigger building projects, but the time it takes to complete the project is drastically reduced. <b><i>The time it takes to build a building is reduced by ".abs($this->ADD_BUILDING_TIME)." %</i></b>",
/* requires */
array('military' =>8, "infrastructure" => 4, "magic" => 0, "thievery" => 0),
/* Gives */
array("military" => 0, "infrastructure" => 8, "magic" => 0, "thievery" => 0)

		);    
        }
	function addBuildingTime($province=NULL) {
		return $this->ADD_BUILDING_TIME;
	}	
}

} // end if( !class_exists() )
?>
