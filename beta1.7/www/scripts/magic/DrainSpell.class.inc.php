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

/* DrainSpell class is the class of a spell, derived from SpellBase.
 *
 * Author: Øystein Fladby 25.03.2004
 * 
 * Changelog:
 * 01.01.05: Anders Elton - changed min/max values.
 *
 * Version: 1.0
 * 
 */
 
require_once( "SpellBase.class.inc.php" );
if( !class_exists( "DrainSpell" ) ) {

class DrainSpell extends SpellBase {
	var $minMana = 1;				// minimum mana drained
	var $maxMana = 7;				// maximum mana drained
	var $sciReq = array( 'military' =>0, "infrastructure" => 0, "magic" => 2, "thievery" => 0 );		// the required sciences to cast this spell
	var $raceReq = array( "Undead" ); // this spell is available to
	var $mana = 5;
	var $costPeasants = 0.1;
	
	function DrainSpell( $sID ) {
		$this->SpellBase( 	$sID, 									// spell ID
							"Drain",						// name
							3,					// gold cost pr acre
							3,					// metal cost pr acre
							2,					// food cost pr acre
							0.75,					// needed wizards pr acre
							1,					// cast on enemies
							1,					// type indirect
							"Drain will drain your opponents mana sources and give some of it 
							to you each tick! Only undead may cast this spell.",	// description
							false);									// picture
	}	
	function getNeededMana() {
		return $this->mana;
	}
	
	function spellEffect( &$db, $province, $targetProvince, $wizards, $strength ) {

		mt_srand( $this->makeSeed() );
		$selectSQL = "SELECT mana FROM Province WHERE pID='".$targetProvince."'";
		$result = $db->query( $selectSQL );
		$row = $db->fetchArray( $result );
		$targetMana = $row['mana'];
		$mana = mt_rand( 	round( $targetMana * ( $strength * $this->minMana / 100 ) ), 
							round( $targetMana * ( $strength * $this->maxMana / 100) ) );
		$updateSQL = "UPDATE Province 
					SET mana= GREATEST( 0, (mana-$mana) ) 
					WHERE pID='".$targetProvince."'";
		$db->query( $updateSQL );
		$updateSQL = "UPDATE Province 
					SET mana= LEAST( 100, (mana+$mana) ) 
					WHERE pID='".$province."'";
		$db->query( $updateSQL );
		return true;		
	}
	
	function raceRequirements() {
		return $this->raceReq;
	}
	function scienceRequirements() {
		return $this->sciReq;
	}
	function getCostPeasants() {
		return $this->costPeasants;
	}
}
} // end if ! class exists
?>