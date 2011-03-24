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
/* FireworksSpell class is the class of a spell, derived from SpellBase.
 *
 * Author: Øystein Fladby 28.04.2003
 * 
 * Changelog:
 * 24.03.2004 Øystein: Added race requirements
 *
 * Version: 1.0
 * 
 */
 
if( !class_exists( "FireworksSpell" ) ) {
require_once( "SpellBase.class.inc.php" );

class FireworksSpell extends SpellBase {
	var $minMorale = 5;
	var $maxMorale = 15;
	var $sciReq = array( 'military' =>0, "infrastructure" => 0, "magic" => 2, "thievery" => 0 );		// the required sciences to cast this spell
	var $raceReq = array( "Human", "Elf", "Orc","Giant" ); // this spell is available to
	var $mana = 10;
	
	function FireworksSpell( $sID ) {
		$this->SpellBase( 	$sID, 									// spell ID
							"Fireworks",						// name
							4,					// gold cost pr acre
							2,					// metal cost pr acre
							10,				// food cost pr acre
							1,					// needed wizards pr acre
							0,					// cast on friends
							0,										// type direct
							"The Fireworks spell will be used in combination with 
							the making of a great party for the military units in the target 
							province. This will increase their morale by $this->minMorale 
							- $this->maxMorale %. Only humans, elves, orcs and giants may cast this spell.", // description
							false);									// picture
	}
	
	function getNeededMana() {
		return $this->mana;
	}

	function spellEffect( &$db, $province, $targetProvince, $wizards, $strength ) {
		$effect = new Effect( $db );
		mt_srand( $this->makeSeed() );
		$targetpID = $targetProvince->getpID();
		$randomMorale = round( mt_rand( ( $this->minMorale ), ( $this->maxMorale ) ) * $strength );
		$maxMorale = round( 100 * $effect->getEffect( $GLOBALS['effectConstants']->ADD_MORALE, $targetpID ) );
		$updateSQL = 	"UPDATE Province 
							SET morale = LEAST($maxMorale, (morale + $randomMorale)) 
							WHERE pID LIKE $targetpID";
		$db->query($updateSQL);
		if($targetpID != $province->getpID() ) {
			$ownReport = $province->getShortTitle().", we successfully cast the $this->name spell at the 
				province of <i>".$targetProvince->provinceName." (#".$targetProvince->getkiID().")</i> while a great party was 
				held. The troops enjoyed the party, and particularly the fireworks!";
			$targetReport = $targetProvince->getAdvisorName().", the province of <i>".$province->provinceName."
				 (#".$province->getkiID().")</i> has cast the $this->name spell at our province and arranged a great party for 
				our military! Our military enjoyed the party and specially the fireworks so much that 
				their morale increased by $randomMorale %!"; 
			$province->postNews( $ownReport );
			$targetProvince->postNews( $targetReport );
		} else {
			$ownReport = $province->getShortTitle().", we have arranged a great party for our troops with  
				a great firework show at the end. The troops enjoyed the party, and particularly the 
				fireworks so much, their morale increased by $randomMorale %!";
			$province->postNews( $ownReport );
		}
		return true;
	}	

	function scienceRequirements() {
		return $this->sciReq;
	}
	function raceRequirements() {
		return $this->raceReq;
	}
	function isKingdomSpell() {
		return true;
	}
}
} // end if ! class exists
?>
