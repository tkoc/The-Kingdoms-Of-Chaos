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

require_once("ScienceBase.class.inc.php");

if( !class_exists( "BugInfestationMagicScience" ) ) {

class BugInfestationMagicScience extends ScienceBase {
		
        function BugInfestationMagicScience( $scienceID ) {
                                                                        // id,name,ticks,gold,metal,description
                $this->ScienceBase( $scienceID,"magic", "Bug infestation", 40, 5000000, 750000,
		"With this spell, you will be able to summon hordes of bugs which will infest the targets buildings, 
		making them useless until the target are able to get rid of the small vermin. 
		<b>Access to Bug infestation</b>",
/* requires */
array('military' =>0, "infrastructure" => 0, "magic" => 2, "thievery" => 0),
/* Gives */
array("military" => 0, "infrastructure" => 0, "magic" => 4, "thievery" => 0)

		);    
        }

}
} // end if( !class_exists() )
?>
