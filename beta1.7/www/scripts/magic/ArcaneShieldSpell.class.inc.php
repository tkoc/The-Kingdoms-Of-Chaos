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
/* Arcane Shield class is the class of a spell, derived from SpellBase.
 *
 * Author: Tasos Nistas 11.10.2009
 * 
 * Changelog:
 *
 * Version: 1.0
 * 
 */

require_once( "SpellBase.class.inc.php" );
if( !class_exists( "ArcaneShieldSpell" ) ) {

class ArcaneShieldSpell extends SpellBase {
	var $addDefense = 8;		// give % better protection against attacks from other provinces
	var $addMagicProtection = 15;
	var $addThieveryDef = 15;
	var $mana = 15;
	var $raceReq = array( "Elf" ); // this spell is available to
	var $sciReq = array( 'military' =>0, "infrastructure" => 0, "magic" => 2, "thievery" => 0 );		// the required sciences to cast this spell
	
	function ArcaneShieldSpell( $sID ) {
		$this->SpellBase( 	$sID, 									// spell ID
							"Arcane Shield",								// name
							9,					// gold cost pr acre
							4,					// metal cost pr acre
							6,					// food cost pr acre
							2.5,					// needed wizards pr acre
							0,					// cast on self / provinces in own kingdom
							1,					// type indirect
							"Arcane shield gives the target $this->addDefense% better defense, $this->addMagicProtection%
							Magic Defence and $this->addThieveryDef% Thievery Defence 
							for the duration of the spell. Only Elves can cast Arcane Shield.",//description
							false);									// picture
	}
	function addDefense() {
		return $this->addDefense;
	}
	function addMagicProtection() {
		return $this->addMagicProtection;
	}
	function addThieveryDef() {
		return $this->addThieveryDef;
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
	function isKingdomSpell() {
		return true;
	}
	function getMaxStack()
	{
		return 10;
	}
}
} // end if ! class exists
?>
