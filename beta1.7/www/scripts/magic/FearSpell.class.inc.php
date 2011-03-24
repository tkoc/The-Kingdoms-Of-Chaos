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
/* FearSpell class is the class of a spell, derived from SpellBase.
 *
 * Author: �ystein Fladby 25.03.2004
 * 
 * Version: 1.0
 * 
 */
 
require_once( "SpellBase.class.inc.php" );
if( !class_exists( "FearSpell" ) ) {

class FearSpell extends SpellBase {
	var $minMorale = 8;
	var $maxMorale = 15;
	var $sciReq = array( 'military' =>0, "infrastructure" => 0, "magic" => 2, "thievery" => 0 );	// the required sciences to cast this spell
	var $raceReq = array( "Undead" ); // this spell is available to	
	var $immuneRace = array( "Undead" ); // this races are immune to this spell
	var $mana = 15;	
	var $costPeasants = 0.1;

	function FearSpell( $sID ) {
		$this->SpellBase( 	$sID, 									// spell ID
							"Fear",					// name
							5,						// gold cost pr acre
							5,						// metal cost pr acre
							2.5,					// food cost pr acre
							1,						// needed wizards pr acre
							1,						// cast on enemies
							0,						// type direct
							"The Fear spell will surely scare the target army, and it will also 
							make your own army gain some confidence by seeing the scared opponent. 
							Only undead may cast this spell.",	 // description
							false);									// picture
	}

	function getNeededMana() {
		return $this->mana;
	}
	
	function spellEffect( &$db, $province, $targetProvince, $wizards, $strength ) {
		if( ! $targetProvince->raceObj->raceReqOk( $this->immuneRace ) ) {
			$effect = new Effect( $db );
			mt_srand( $this->makeSeed() );
			$targetpID = $targetProvince->getpID();
			$randomTargetMorale = round( mt_rand( ( $this->minMorale ), ( $this->maxMorale ) ) * $strength );
			$updateSQL = 	"UPDATE Province 
							SET morale = GREATEST(0, (morale - $randomTargetMorale)) 
							WHERE pID LIKE $targetpID";
			$db->query($updateSQL);
			$pID = $province->getpID();
			$randomSelfMorale = round( mt_rand( ( $this->minMorale ), ( $this->maxMorale ) ) * $strength );
			$maxMorale = round( 100 * $effect->getEffect( $GLOBALS['effectConstants']->ADD_MORALE, $pID ) );
			$updateSQL = 	"UPDATE Province 
							SET morale = LEAST($maxMorale, (morale + $randomSelfMorale)) 
							WHERE pID LIKE $pID";
			$db->query($updateSQL);
			$ownReport = $province->getShortTitle().", we successfully cast the $this->name spell at the 
					province of <i>".$targetProvince->provinceName." (#".$targetProvince->getkiID().")
					</i> with a scary result. At least to them, but our own undead army gained $randomSelfMorale% 
					morale from the spell!";
			$targetReport = $targetProvince->getAdvisorName().", the province of <i>".$province->provinceName."
					(#".$province->getkiID().")</i> has cast the $this->name spell at our province 
					causing our army to stay up all night afraid of going to bed! The morale of our military units has been 
					reduced by $randomTargetMorale%.<br>".$targetProvince->getShortTitle().", 
					perhaps we should do something to make them happier again?";
		} else {
			$ownReport = $province->getShortTitle().", we successfully cast the $this->name spell at the 
					province of <i>".$targetProvince->provinceName." (#".$targetProvince->getkiID().")
					</i>, but as they were also undead, they didn't get scared by our illusions!";
			$targetReport = $targetProvince->getAdvisorName().", the province of <i>".$province->provinceName."
					(#".$province->getkiID().")</i> has cast the $this->name spell at our province, but 
					because we're undead, we sure aren't afraid of no ghosts! Bring them on, I say! Anyway, this was 
					not really a nice action, so perhaps we should retaliate with some really scary stuff?";
		}
		$province->postNews( $ownReport );
		$targetProvince->postNews( $targetReport );
		return true;
	}	

	function scienceRequirements() {
		return $this->sciReq;
	}
	function raceRequirements() {
		return $this->raceReq;
	}
	function getCostPeasants() {
		return $this->costPeasants;
	}
}
} // end if ! class exists
?>