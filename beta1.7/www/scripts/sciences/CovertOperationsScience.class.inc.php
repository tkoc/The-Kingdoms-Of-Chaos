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

if( !class_exists( "CovertOperationsScience" ) ) {
require_once("ScienceBase.class.inc.php");

class CovertOperationsScience extends ScienceBase {
		var	$ADD_THIEVERY_LOSSES= "-30";
		
        function CovertOperationsScience( $scienceID ) {
                                                                        // id,name,ticks,gold,metal,description
                $this->ScienceBase( $scienceID,"thievery", "Covert Operations", 40, 2500000, 0,
		"Your thieves get trained in the art of covert operations.   While not truly masters yet, your thieves get trained to 
		do more operations, making them far more devastating than normal thieves.  
		<b>Reduces thievery losses by $this->ADD_THIEVERY_LOSSES %.  Access to Poison water and Infiltration.</b>",
/* requires */
array('military' =>0, "infrastructure" => 0, "magic" => 0, "thievery" => 1),
/* Gives */
array("military" => 0, "infrastructure" => 0, "magic" => 0, "thievery" => 2)

		);    
        }
	function addThieveryLoss ($province=NULL) {
		return $this->ADD_THIEVERY_LOSSES;
	}
	

}
} // end if( !class_exists() )
?>