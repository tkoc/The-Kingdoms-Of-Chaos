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

/*************************************************************************
 * OrcRace is the class for the orc race, describing their special 
 * abilities
 *
 * Made by Øystein Fladby 31.05.2003
 *
 * Changelog:
 * Øystein 23.03.04: Added full description
 * Anders Elton 25.10.03: Updated for age 4.
 * Version: test
 *
 *************************************************************************/
if( !class_exists( "King" ) ) {
require_once( "../effect/EffectBase.class.inc.php");
class King extends EffectBase {
	function addGoldKingBonus($province=NULL) {
		return 10;
	}
	function addFoodKingBonus($province=NULL) {
		return 10;
	}
	function addMetalKingBonus($province=NULL) {
		return 10;
	}
} // end class orc race
} // end if class exists
?>