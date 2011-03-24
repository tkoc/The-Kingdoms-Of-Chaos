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
/* Spell class to be extended by all spells. Contains all functionality
 * available to a spell.
 * 
 * O B S : Remember to update Magic.class.inc.php if functions are 
 * added / removed / return value type changed
 *
 * Author: Øystein Fladby 28.04.2003
 * 
 * Version: test
 * 
 */
 
if( !class_exists( "SpellBase" ) ) {
require_once( WWW_SCRIPT_PATH . "effect/EffectBase.class.inc.php" );
require_once( WWW_SCRIPT_PATH . "magic/MagicConstants.class.inc.php" );

class SpellBase extends EffectBase {
	var $sID;							// the spell ID
	var $name="NN";						// the spell name
	var $costGold=0;					// the cost of this spell in gold
	var $costMetal=0;					// the cost of this spell in metal
	var $costFood=0;					// the cost of this spell in food
	var $costPeasants=0;				// the cost of this spell in peasants
	var $wizardsNeeded=0;				// the number of wizards recommended to cast this spell on one acre
	var $neededMana=2.5;				// the mana needed to cast the spell
	var $description="";				// the description of this spell
	var $pictureFile = "magic.gif";		// the picture file of this spell
	var $castOn = false;				// = 0 if friendly, 
										// = 1 if unfriendly
										// = 2 if self only
	var $type = false;					// = 0 if direct
										// = 1 if indirect
										// = 2 is dispel
										// = 3 if triggered by other event
	
	function SpellBase( $inSid, $inName, $inCostGold, $inCostMetal, $inCostFood, 
				$inWizardsNeeded, $inCastOn, $inType, $inDescription, $inPicture=false ) {
		$this->sID = $inSid;
		$this->name = $inName;
		$this->costGold = $inCostGold;
		$this->costMetal = $inCostMetal;
		$this->costFood = $inCostFood;
		$this->wizardsNeeded = $inWizardsNeeded;
		$this->castOn = $inCastOn;
		$this->type = $inType;
		$this->description = $inDescription;
		$this->pictureFile = ( $inPicture ? $inPicture : $this->pictureFile );
	}
	
	function getMaxStack()
	{
		return -1;
	}

	////////////////////////////////////////////
	// SpellBase::makeSeed
	////////////////////////////////////////////
	// Function to make a random seed for a random function
	// Returns:
	// 		float number
	////////////////////////////////////////////
	function makeSeed() {
    		list($usec, $sec) = explode(' ', microtime());
    		return (float) $sec + ((float) $usec * 100000);
	}
	
	////////////////////////////////////////////
	// SpellBase::spellEffect
	////////////////////////////////////////////
	// Function which executes the spell. Must be 
	// overrided by child.
	// Takes a casterProvince object and a targetProvince
	// object.
	// OBS!!! IF this is an indirect spell with a specific spellEffect to run each tick, then
	// $casterProvince and targetProvince will only getpID's instead of objects!!!
	// Returns:
	// 		String with info of what happened
	////////////////////////////////////////////
	function spellEffect (&$db, $casterProvince, $targetProvince, $wizards, $other=false ) {
		return true;
	}
		
	////////////////////////////////////////////
	// SpellBase::xxxRequirements
	////////////////////////////////////////////
	// Various functions to get the requirements
	// needed to be allowed to cast a spell
	// Returns:
	//    	false if not overrided by child
	//		else array of building names, science ids, race names etc
	// 	 	which have to be built / researched before
	// 	 	this spell is allowed to be cast
	////////////////////////////////////////////
	function scienceRequirements() {
		$sciReq = array( 'military' =>0, "infrastructure" => 0, "magic" => 0, "thievery" => 0 );		// the required sciences to cast this spell
		return $sciReq;
	}	
	function buildingRequirements() {
		return false;
	}
	function raceRequirements() {
		return false;
	}
	function isKingdomSpell() {
		return false;
	}
	function isSelfOnly() {
		return ( ( $this->castOn == 2 ) ? true : false );
	}
	
	////////////////////////////////////////////
	// SpellBase::preventSpellXxx
	////////////////////////////////////////////
	// Various functions to get buildings/science which 
	// prevents the casting of this spell
	// Returns:
	//    	false if not overrided by child
	//		else array of building NAMEs or science ids
	// 	 	which must not be built / researched  to
	// 	 	allow this spell to be cast 
	////////////////////////////////////////////
	function preventSpellScience() {
		return false;
	}
	
	function preventSpellBuilding() {
		return false;
	}
	
	////////////////////////////////////////////
    // SpellBase::getPictureFile
    ////////////////////////////////////////////
    // Function to get the path and name of the 
	// picture of this spell
    // Returns:
    //    string with path and picture name
    ////////////////////////////////////////////
	function getPictureFile() {
		$picturePath = "magic/img/";	// the path to the spell pictures
		$file = $picturePath.$this->pictureFile;
		if( file_exists( $file ) ) {
			return $file;
		} else {
			return "no picture found";
		}
	}
	
	////////////////////////////////////////////
	// SpellBase::isTriggerType
	////////////////////////////////////////////
	// Function to inherit
	////////////////////////////////////////////
	function isTriggerType( $TRIGGER_TYPE_CONSTANT ) {
		return false;
	}
	
	////////////////////////////////////////////
	// SpellBase::getXxx
	////////////////////////////////////////////
	// Various functions to get the info recieved in the constructor
	// Returns:
	//    value recieved by constructor
	////////////////////////////////////////////
	function getID() {
		return $this->sID;
	}
	function getName() {
		return $this->name;
	}
	function getNeededWizards() {
		return $this->wizardsNeeded;
	}
	function getCastOn() {
		return $this->castOn;
	}
	function getCostGold() {
		return $this->costGold;
	}
	function getCostMetal() {
		return $this->costMetal;
	}
	function getCostFood() {
		return $this->costFood;
	}
	// TODO!!! OBS!!! ADD PEASANT COST IN ALL SPELLS? ///
	function getCostPeasants() {
		return $this->costPeasants;
	}
	function getType() {
		return $this->type;
	}
	function getDescription() {
		return $this->description;
	}
	function getNeededMana() {
		return $this->neededMana;
	}
}
} // end if ! class exists
?>
