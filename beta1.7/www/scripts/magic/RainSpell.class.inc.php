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
/* RainSpell class is the class of a spell, derived from SpellBase.
 *
 * Author: �ystein Fladby 28.04.2003
 * 
 * Version: 1.0
 * 
 */
 
require_once( "SpellBase.class.inc.php" );
if( !class_exists( "RainSpell" ) ) {

class RainSpell extends SpellBase {
	var $minMorale = 5;
	var $maxMorale = 15;
	var $sciReq = array( 'military' =>0, "infrastructure" => 0, "magic" => 2, "thievery" => 0 );		// the required sciences to cast this spell
	var $raceReq = array( "Human", "Elf", "Orc", "Giant" ); // this spell is available to
	var $mana = 15;	

	function RainSpell( $sID ) {
		$this->SpellBase( 	$sID, 									// spell ID
							"Rain",					// name
							2.5,					// gold cost pr acre
							7.5,					// metal cost pr acre
							5,					// food cost pr acre
							1.75,					// needed wizards pr acre
							1,					// cast on enemies
							0,					// type direct
							"The Rain spell will make it rain all day and night in the 
							target province. The military units in the target province 
							might not like this... having as an effect to lower their morale. Only humans, elves, orcs and giants 
							may cast this spell",	 // description
							false);									// picture
	}

	function getNeededMana() {
		return $this->mana;
	}
	function raceRequirements() {
		return $this->raceReq;
	}
	function spellEffect( &$db, $province, $targetProvince, $wizards, $strength ) {
		mt_srand( $this->makeSeed() );
		$targetpID = $targetProvince->getpID();
		$randomMorale = round( mt_rand( ( $this->minMorale ), ( $this->maxMorale ) ) * $strength );
		$updateSQL = 	"UPDATE Province 
				SET morale = GREATEST(0, (morale - $randomMorale)) 
				WHERE pID LIKE $targetpID";
		$db->query($updateSQL);
		$ownReport = $province->getShortTitle().", we successfully cast the $this->name spell at the 
				province of <i>".$targetProvince->provinceName." (#".$targetProvince->getkiID().")
				</i> with the result that it's raining over there all the time! This will surely 
				reduce their morale.";
		$targetReport = $targetProvince->getAdvisorName().", the province of <i>".$province->provinceName."
				(#".$province->getkiID().")</i> has cast the $this->name spell at our province 
				causing it to rain all day and all night! The morale of our military units has been 
				reduced by $randomMorale %.<br>".$targetProvince->getShortTitle().", 
				perhaps we should do something to make them happier again?";
		$province->postNews( $ownReport );
		$targetProvince->postNews( $targetReport );
		return true;
	}	

	function scienceRequirements() {
		return $this->sciReq;
	}
}
} // end if ! class exists
?>