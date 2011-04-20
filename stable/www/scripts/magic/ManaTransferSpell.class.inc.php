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
/* ManaTransferSpell class is the class of a spell, derived from SpellBase.
 *
 * Author: Øystein Fladby 26.03.2004
 * 
 * Changelog:
 *
 * Version: 1.0
 * 
 */
 
require_once( "SpellBase.class.inc.php" );
if( !class_exists( "ManaTransferSpell" ) ) {

class ManaTransferSpell extends SpellBase {
	var $transfer = 50;			// the mana to be transfered
	var $minTransfer = 70;		// min mana % transfered
	var $maxTransfer = 99;		//max mana % transfered
	var $raceReq = array( "Elf" ); // this spell is available to
	var $raceMana = array( "Elf" ); //Race with more morale than 100 (elf)
	var $sciReq = array( 'military' =>0, "infrastructure" => 0, "magic" => 2, "thievery" => 0 );		// the required sciences to cast this spell
	
	function ManaTransferSpell( $sID ) {
		$this->SpellBase( 	$sID, 				// spell ID
							"Mana transfer",		// name
							3,					// gold cost pr acre
							3,				// metal cost pr acre
							3,					// food cost pr acre
							2,					// needed wizards pr acre
							0,					// cast on self / provinces in own kingdom
							0,					// type direct
							"Mana transfer will try to transfer $this->transfer of your mana 
							to the target province, but you should expect some of it to return 
							to mother nature on the way. Only elves may cast this spell.",			// description
							false);				// picture
	}	
	function spellEffect( &$db, $province, $targetProvince, $wizards, $strength ) {
		mt_srand( $this->makeSeed() );
		if( $province->mana < $this->transfer ) {
			$this->transfer = $province->mana;
		}
		$province->useMana( $this->transfer );
		$transfer = round( $this->transfer * ( mt_rand( ( $this->minTransfer ), ( $this->maxTransfer ) ) / 100 ) );
		if( $targetProvince->raceObj->raceReqOk( $this->raceMana ) ) {
			$updateSQL = "UPDATE Province SET mana = LEAST( 110, (mana+$transfer) ) WHERE pID='".$targetProvince->getpID()."'";
		}
		else{		
			$updateSQL = "UPDATE Province SET mana = LEAST( 100, (mana+$transfer) ) WHERE pID='".$targetProvince->getpID()."'";
		}
		$db->query( $updateSQL );
		$ownReport = $province->getShortTitle().", we successfully cast the $this->name spell at the 
					province of <i>".$targetProvince->provinceName." (#".$targetProvince->getkiID().")</i> with the result 
					that ".$transfer." of our mana came through to their province.";
		if( ( $targetProvince->mana + $transfer ) > 100 ) {
			$targetReport = $targetProvince->getAdvisorName().", the province of <i>".$province->provinceName."
							(#".$province->getkiID().")</i> has cast the $this->name spell at our province, giving us 
							$transfer more mana! Unfortunately, we couldn't hold it all, but now we're 
							holding as much mana as we possibly can.";
		} else {
			$targetReport = $targetProvince->getAdvisorName().", the province of <i>".$province->provinceName."
							(#".$province->getkiID().")</i> has cast the $this->name spell at our province, giving us 
							$transfer more mana!";
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
	function isKingdomSpell() {
		return true;
	}
}
} // end if ! class exists
?>