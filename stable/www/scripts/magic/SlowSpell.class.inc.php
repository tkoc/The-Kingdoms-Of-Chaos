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

/* SlowSpell class is the class of a spell, derived from SpellBase.
 *
 * Author: Øystein Fladby 26.03.2004
 * 
 * Changelog:
 *
 * Version: 1.0
 * 
 */
 
require_once( "SpellBase.class.inc.php" );
if( !class_exists( "SlowSpell" ) ) {

class SlowSpell extends SpellBase {
	var $addAttackTime = 10;		// give % longer attack time
	var $sciReq = array( 'military' =>0, "infrastructure" => 0, "magic" => 2, "thievery" => 0 );		// the required sciences to cast this spell
	var $mana = 20;
	
	function SlowSpell( $sID ) {
		$this->SpellBase( 	$sID, 									// spell ID
							"Slow",						// name
							5,					// gold cost pr acre
							5,					// metal cost pr acre
							5,					// food cost pr acre
							2,					// needed wizards pr acre
							1,					// cast on enemies
							1,					// type indirect
							"Slow makes the military at home in the targeted province 
							$this->addAttackTime% slower when attacking. This doesn't affect 
							military which is already at war.",	// description
							false);									// picture
	}	
	function getNeededMana() {
		return $this->mana;
	}
	function addAttackTime() {
		return $this->addAttackTime;
	}
	function scienceRequirements() {
		return $this->sciReq;
	}
}
} // end if ! class exists
?>