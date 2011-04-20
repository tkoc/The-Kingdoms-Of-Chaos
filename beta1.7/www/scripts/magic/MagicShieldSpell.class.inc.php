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

/* MagicShieldSpell class is the class of a spell, derived from SpellBase.
 *
 * Author: Øystein Fladby 28.04.2003
 * 
 * Version: 1.0
 * 
 */
 
require_once( "SpellBase.class.inc.php" );
if( !class_exists( "MagicShieldSpell" ) ) {

class MagicShieldSpell extends SpellBase {
	var $addMagicProtection = 25;		// give 15% better protection against spells from other provinces
	var $mana = 5;
	
	function MagicShieldSpell( $sID ) {
		$this->SpellBase( 	$sID, 									// spell ID
							"Magic shield",							// name
							2,					// gold cost pr acre
							0,					// metal cost pr acre
							5,					// food cost pr acre
							1.5,					// needed wizards pr acre
							0,					// cast on self / provinces in own kingdom
							1,					// type indirect
							"Magic shield gives the target $this->addMagicProtection% better protection 
							against spells from other provinces. To make the shield so strong, the wizards 
							can't differentiate between good and evil spells, so people who want to help the target 
							by casting nice spells at him/her will also have more problems. This spell will 
							not affect the targets own spellcasting at his/her own province.",//description
							false);									// picture
	}
	function addMagicProtection() {
		return $this->addMagicProtection;
	}
	function getNeededMana() {
		return $this->mana;
	}
	function isKingdomSpell() {
		return true;
	}
}
} // end if ! class exists
?>