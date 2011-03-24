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

/* GrowthSpell class is the class of a spell, derived from SpellBase.

 *

 * Author: �ystein Fladby 12.12.2005

 * 

 * Version: 1.0

 * 

 */

require_once( "SpellBase.class.inc.php" );

if( !class_exists( "GrowthSpell" ) ) {



class GrowthSpell extends SpellBase {

	var $minAcres = 5; 		// gain at least 5 acres instantly
	var $maxAcres = 10; 		// gain max 10 acres instantly
	var $raceReq = array( "Human", "Elf", "Orc", "Dwarf", "Undead", "Giant" ); // this spell is available to
	var $sciReq = array( 'military' =>0, "infrastructure" => 0, "magic" => 0, "thievery" => 0 );		// the required sciences to cast this spell
	var $mana = 25;
		
	function GrowthSpell( $sID ) {
		$this->SpellBase( 	$sID, 									// spell ID
							"Growth",							// name
							5,				// gold cost pr acre
							2.5,				// metal cost pr acre
							1,				// food cost pr acre
							1,				// needed wizards pr acre
							2,				// cast on self
							0,				// type direct
							"The Growth spell will instantly create acres from somewhere unknown.",	// description
							false);									// picture
	}

	function getNeededMana() {
		return $this->mana;
	}
	
	function spellEffect( &$db, $province, $targetProvince, $wizards, $strength ) {
		$dummy = NULL;
		mt_srand( $this->makeSeed() );
		$gainedAcres = mt_rand( $this->minAcres, $this->maxAcres );
		$updateSQL = 	"UPDATE Province set acres=(acres+$gainedAcres) 
						WHERE pID LIKE '".$province->getpID()."'";
		$db->query( $updateSQL );
		$ownReport = 	$province->getShortTitle().", we successfully cast the $this->name spell at our own province
						with the result that $gainedAcres acres were added to our great province!";
		$province->postNews( $ownReport );
		return true;
	}

	function scienceRequirements() {		
		return $this->sciReq;
	}
	function raceRequirements() {
		return $this->raceReq;
	}
}
} // end if ! class exists
?>