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
 * HumanRace is the class for the human race, describing their special 
 * abilities
 *
 * Made by Øystein Fladby 31.05.2003
 * 
 * Changelog:
 * 23.03.04 Øystein: Added full description
 * 26.10.03 Øystein	Added allowed science param in RaceBase call and changed some properties
 *
 * Version: test
 *
 *************************************************************************/
if( !class_exists( "HumanRace" ) ) {
require_once( "RaceBase.class.inc.php");
class HumanRace extends RaceBase {

	// NB! Remembr to change the description if you fuck around to much. (like adding/removing bonuses)
	var $addedCaravanLoss = -20;
	var $addedResearchTime = -30;
	var $addedThieveryOff = 25;
	var $addedThieveryDef = 45;
	var $addedBuildingTime = -20;
	var $addedThieveryLoss = -25;
	var $addedGoldExplore = 5;
    var $addInfluence = 10;
	var $addedPeasantHousing = 5;


	function HumanRace ( $rID ) {
		$this->RaceBase ( $rID, 
						"Human", 
						"Cities were created by humans, and that's where they they live. Humans like to put things in order, and they have done so with great success. With their thieves humans can easily find out enemy weaknesses, and use that info to gain advantage on the battlefield.
						<table width=\"100%\" >
						  <tr>
						  <td>
						<br><br>".abs( $this->addedCaravanLoss)."% less resources is lost in trade caravans.<br>
						".abs( $this->addedResearchTime)."% faster science research.<br>
						".abs( $this->addedThieveryOff)."% Stronger thieves.<br>
						".abs( $this->addedThieveryDef)."% extra thievery defense.<br>
						".abs( $this->addedThieveryLoss)."% less thieves lost in failed ops.<br>
						".abs($this->addedBuildingTime)."% faster construction time.<br>
						".abs( $this->addedGoldExplore)."% more expensive exploration cost.<br>
						".abs($this->addInfluence)."% bonus to influence.<br>
						".abs( $this->addedPeasantHousing)."% extra housing.<br><br>
						</td>
						<td align=RIGHT>
						<img src=\"".$GLOBALS['path_domain_img']."/guide/guide_human.jpg\" border=\"0\">
						</td>
						</tr>
						</table>",
						array('military' =>255, "infrastructure" => 255, "magic" => 255, "thievery" => 255) // max science level.
		 );
	}
	function addResearchTime ($province=NULL) {
		return $this->addedResearchTime;
	}
	function addResourceLoss ($province=NULL) {
		return $this->addedCaravanLoss;
	}
	function addThieveryLoss ($province=NULL) {
		return $this->addedThieveryLoss;
	}
	function addThieveryOff($province=NULL) {
		return $this->addedThieveryOff;
	}
	function addBuildingTime($province=NULL) {
		return $this->addedBuildingTime;
	}
	function addThieveryDef($province=NULL) {
		return $this->addedThieveryDef;
	}
	function addExploreGoldCost($province=NULL) {
		return $this->addedGoldExplore;
	}
	function addPeasantHousing($province=NULL) {
		return $this->addedPeasantHousing;
	}


} // end class humanrace
} // end if class exists
?>