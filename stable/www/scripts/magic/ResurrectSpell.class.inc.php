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
/* ResurrectSpell class is the class of a spell, derived from SpellBase.
 *
 * Author: Øystein Fladby 04.06.2004
 * 
 * Version: 1.0
 * 
 */
 
require_once( "SpellBase.class.inc.php" );
if( !class_exists( "ResurrectSpell" ) ) {

class ResurrectSpell extends SpellBase {
	var $minResurrect = 2.5;		// resurrect at least % of the dead units
	var $maxResurrect = 20;		//resurrect maximum % of the dead units
	var $mana = 20;
	var $raceReq = array( "Undead" ); // this spell is available to
	var $sciReq = array( 'military' =>0, "infrastructure" => 0, "magic" => 8, "thievery" => 0 );		// the required sciences to cast this spell
	var $costPeasants = 0.5;
	
	function ResurrectSpell( $sID ) {
		$this->SpellBase( 	$sID, 				// spell ID
							"Resurrect",								// name
							30,					// gold cost pr acre
							20,					// metal cost pr acre
							20,					// food cost pr acre
							1.25,				// needed wizards pr acre
							2,					// cast on self / provinces in own kingdom
							1,					// type indirect
							"Resurrect must be cast at your own province and will resurrect a number of your 
							fallen undead each tick. These resurrected units will become a part of your population, 
							so you should keep in mind that they will fill up houseroom and consume resources like 
							regular units.",//description
							false);									// picture
	}
	
	function spellEffect( &$db, $pID, $targetpID, $wizards, $strength ) {
		$province = new Province( $pID, $db );
		$province->getProvinceData();		
		if( $targetpID == $pID ) {		
			$province->getMilitaryData();				
			$deadMilitary = $province->milObject->getDeadCount();
			
			mt_srand( $this->makeSeed() );
			$resurrectResult = "";
			if( is_array( $deadMilitary ) ) {
				foreach( $deadMilitary as $deadUnit ) {
					$resurrect = mt_rand( round( $deadUnit['num'] * ( $strength * $this->minResurrect / 100 ) ), 
						 										round( $deadUnit['num'] * ( $strength * $this->maxResurrect / 100 ) ) );
					if( $resurrect ) {
						$province->milObject->removeFromDeadCount( $deadUnit['type'], $resurrect );
						$province->milObject->create( $deadUnit['type'], $resurrect );
						$militaryUnit = $province->milObject->getMilUnit( $deadUnit['type'] );
						$unitName = $militaryUnit['object']->getName();
						$resurrectResult .= "$resurrect $unitName got resurrected<br>";
					}		
				}
			}
			if( $resurrectResult ) {
				$ownReport = 	$province->getShortTitle().", our $this->name spell cast at our 
											own province has given good results!<br>".$resurrectResult;
			} else {
				$ownReport = 	$province->getShortTitle().", our $this->name spell cast at our own province 
											had no effect at all because we don't have enough fallen undead to resurrect.<br>";											
			}
			$province->postNews( $ownReport );
			return true;
		} else {
			$ownReport = $province->getShortTitle().", our $this->name spell cast at some other province 
									 will have no effect at all because it must be cast at our own province...<br>";											
			$province->postNews( $ownReport );
			return false;
		}		
	}

	function raceRequirements() {
		return $this->raceReq;
	}
	function scienceRequirements() {
		return $this->sciReq;
	}
	function getNeededMana() {
		return $this->mana;
	}
	function getCostPeasants() {
		return $this->costPeasants;
	}
}
} // end if ! class exists
?>
