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
/* TriggeredSpellBase class is the baseclass of a triggeredspell, derived from SpellBase.
 *
 * Author: Øystein Fladby 04.09.2003
 * 
 * Version: test
 * 
 */
 
require_once( "SpellBase.class.inc.php" );
if( !class_exists( "TriggeredSpellBase" ) ) {

class TriggeredSpellBase extends SpellBase {
	var $triggerType;
	function TriggeredSpellBase( $inSid, $inName, $inCostGold, $inCostMetal, $inCostFood, 
								$inWizardsNeeded, $inCastOn, $inDescription, $inTriggerType, 
								$inPicture=false ) {
		$this->triggerType = $inTriggerType;
		$this->SpellBase( $inSid, $inName, $inCostGold, $inCostMetal, $inCostFood, $inWizardsNeeded, $inCastOn, 3, $inDescription, $inPicture);									// picture
	}
	
	function triggerEffect( &$db, $sID, $casterID, $targetID, $wizards, $spellID, $strength ) {
	
	}
	
	////////////////////////////////////////////
	// TriggeredSpellBase::isTriggerType
	////////////////////////////////////////////
	// Function to check that this is a spell which should 
	// be triggered by the given TRIGGER_TYPE_CONSTANT
	// Returns:
	// 		true - if ok to trigger
	//		false
	////////////////////////////////////////////
	function isTriggerType( $TRIGGER_TYPE_CONSTANT ) {
		$result = false;
			if( !strcmp( $this->triggerType, $TRIGGER_TYPE_CONSTANT ) ) {
				$result = true;
			}
		return $result;
	}
}
} // end if ! class exists
?>