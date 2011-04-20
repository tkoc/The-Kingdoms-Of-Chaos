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

/* BugInfestationSpell class is the class of a spell, derived from SpellBase.
 *
 * Author: Øystein Fladby 28.04.2003
 * 
 * Version: 1.0
 * 
 */
 
require_once( "SpellBase.class.inc.php" );
if( !class_exists( "BugInfestationSpell" ) ) {

class BugInfestationSpell extends SpellBase {
	var $minInfestedBuildings = 5; 		// 5 of 100 of the provinces total buildings might be infested
	var $maxInfestedBuildings = 20; 	// 15 of 100 of the provinces total buildings might be infested
	var $minDays = 10;
	var $maxDays = 20;
	var $sciReq = array( 'military' =>0, "infrastructure" => 0, "magic" => 4, "thievery" => 0 );		// the required sciences to cast this spell
	var $mana = 25;

	function BugInfestationSpell( $sID ) {
		$this->SpellBase( 	$sID, 									// spell ID
							"Bug infestation",						// name
							7.5,					// gold cost pr acre
							5,					// metal cost pr acre
							10,					// food cost pr acre
							2,					// needed wizards pr acre
							1,					// cast on enemies
							0,					// type direct
							"The Bug infestation spell will summon a horde of creepy bugs which 
							will infest between $this->minInfestedBuildings and 
							$this->maxInfestedBuildings% of the buildings in the target province 
							( buildings goes back into 'in progress state' ) for $this->minDays - $this->maxDays days.",	 // description
							false);									// picture
	}

	function getNeededMana() {
		return $this->mana;
	}
	
	function spellEffect( &$db, $province, $targetProvince, $wizards, $strength ) {
		mt_srand( $this->makeSeed() );
		$targetpID = $targetProvince->getpID();
		$infestedHtml = "";		
		$result = false;
		require_once( "Buildings.class.inc.php" );
		$dummy = NULL;
		$buildings = new Buildings( $db, $dummy );
		$percentage = $strength * ( mt_rand( ( $this->minInfestedBuildings ), ( $this->maxInfestedBuildings ) ) / 100 );
		$infestResult = $buildings->transferToProgress( $percentage, $targetpID, $this->minDays, $this->maxDays );
		$number = $infestResult['totTransfer'];
		if( $number ) {
			$ownReport = $province->getShortTitle().", we successfully cast the $this->name spell at the 
						province of <i>".$targetProvince->provinceName." (#".$targetProvince->getkiID().")</i> with the result 
						that ".$number." of the buildings in 
						<i>".$targetProvince->provinceName."</i> were infested.";
			$targetReport = $targetProvince->getAdvisorName().", the province of <i>".$province->provinceName."
						 (#".$province->getkiID().")</i> has cast the $this->name spell at our province, having a horrible effect! 
						The buildings which were infested by the bugs will be repaired as soon as possible, 
						and here's an estimation of the affected buildings: ".$infestResult['html']." <br>".$targetProvince->getShortTitle().
						", we must let them pay for this evil action!";
		} else {
			$ownReport = $province->getShortTitle().", we successfully cast the $this->name spell at the 
						province of <i>".$targetProvince->provinceName." (#".$targetProvince->getkiID().")</i>, but unfortunately it didn't affect 
						any buildings at all!";
			$targetReport = $targetProvince->getAdvisorName().", the province of <i>".$province->provinceName."
						 (#".$province->getkiID().")</i> has cast the $this->name spell at our province! Fortunately, no buildings were 
						affected by the spell, but even so, ".$targetProvince->getShortTitle().
						", we must let them pay for this evil action!";
		}
		$province->postNews( $ownReport );
		$targetProvince->postNews( $targetReport );
		return $result;
	}	
	function scienceRequirements() {
		return $this->sciReq;
	}
}
} // end if ! class exists
?>