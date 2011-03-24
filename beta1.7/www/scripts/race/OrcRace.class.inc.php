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
if( !class_exists( "OrcRace" ) ) {
require_once( "RaceBase.class.inc.php");
class OrcRace extends RaceBase {
	var $addedResearchTime = 10;
	var $addAttack = 9;
    var $addMorale = 15;
	var $addDefense = 6;
    var $addAttackTime = -15;

	function OrcRace ( $rID ) {
		$this->RaceBase ( $rID, "Orc", 
								"In the wasteland the orcs have found their home.  The orc way is simple but effective.  If it speak and does not look orc... Kill it!  If it still speaks, kill it again.  With this attitude only the strongest of the orcs survive.  When it comes down to raw strength, the orc is unchallanged.
						<table width=\"100%\" >
						  <tr>
						  <td>
							<img src=\"".$GLOBALS['path_domain_img']."/guide/guide_orc.jpg\" border=\"0\">
						</td>
						<td align=LEFT>

								<br><br>".abs($this->addedResearchTime)."% longer time to research a knowledge<br>
								".abs($this->addMorale)."% bonus to morale.<br>
								".abs($this->addAttackTime)."% shorter attack time.<br>
								".abs($this->addAttack)."% bonus to attack strength.<br>
								".abs($this->addDefense)."% bonus to defense strength.<br><br>
						</td>
						</tr>
						</table>",
								array('military' =>255, "infrastructure" => 255, "magic" => 255, "thievery" => 255) // max science level.						
						);
	}
	function addResearchTime ($province=NULL) {
		return $this->addedResearchTime;
	}
	
	function addAttack($province=NULL) {
		return $this->addAttack;
	}
	function addMorale($province=NULL) {
		return $this->addMorale;
	}
	function addDefense($province=NULL) {
		return $this->addDefense;
	}
	function addAttackTime($province=NULL) {
		return $this->addAttackTime;
	}

} // end class orc race
} // end if class exists
?>
