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
/* Cleansing class is the class of a spell, derived from SpellBase. It's a duration spell which
 * will try to remove one bad spell each tick.
 *
 * Author: Øystein Fladby 22.09.2006
 * 
 * Version: 1.0
 * 
 */
 
require_once( "SpellBase.class.inc.php" );
if( !class_exists( "CleansingSpell" ) ) {

class CleansingSpell extends SpellBase {		

	var $raceReq = array( "Elf" );
	var $mana = 10;
	var $sciReq = array( 'military' =>0, "infrastructure" => 0, "magic" => 8, "thievery" => 0 );		// the required sciences to cast this spell

	function CleansingSpell( $sID ) {
		$this->SpellBase( 	$sID, 									// spell ID
							"Cleansing",								// name
							15,					// gold cost pr acre
							10,					// metal cost pr acre
							5,					// food cost pr acre
							2,					// needed wizards pr acre
							0,					// cast on friends
							1,					// type indirect
							"The Cleansing spell tries to remove one bad spell cast on a province each tick. There is 
							always a small chance a spell will not be removed at the tick. Only Elves will be able to 
							cast this spell, and they must have the science to cast earthquake first too.",	// description
							false);										// picture
	}
	
	function spellEffect( &$db, $province, $targetProvince, $wizards, $other=false ) {
		$dummy = NULL;
		$magic = new Magic( $db, $dummy );
		mt_srand( $this->makeSeed() );
		$spellsRemovedHtml = "";
		$html = "";
		$html .= "<br>".$province->getShortTitle().",";
		$milArray = $province->milObject->getMilUnit( $province->milObject->MilitaryConst->WIZARDS );
		$ourWizardsName = strtolower( $milArray['object']->getName() );
		$selectSQL = 	"SELECT S.spellID, S.strength, S.wizards, S.sID
									FROM Spells S 
									WHERE S.targetID='".$targetProvince->getpID()."'
									ORDER BY S.strength DESC";
		if( ( $spells = $db->query( $selectSQL ) ) && $db->numRows() ) {			
			$remove = random( 1, $db->numRows() );
			while( $remove-- ) {
				$row = $db->fetchArray( $spells );
			}
			if( $magic->spells[ $row['sID'] ]->baseFunction( $GLOBALS['magicConstants']->GET_CAST_ON ) == 1 ) { 	// if a bad spell
				$randomChance = mt_rand( 1, 100 );
				if( $randomChance > 5 ) {
					$type = $magic->spells[ $row['sID'] ]->baseFunction( $GLOBALS['magicConstants']->GET_TYPE );
					$spellsRemovedHtml .= "<br>".($type == 3 ? "Triggered spell: " : "").
												$magic->spells[ $row['sID'] ]->baseFunction( $GLOBALS['magicConstants']->GET_NAME );
					$deleteSQL = "DELETE FROM Spells WHERE spellID='".$row['spellID']."'";
					$db->query( $deleteSQL );
				}
			}
			
			if( strlen( $spellsRemovedHtml ) ) {
					$ownReport = $province->getShortTitle().", our $ourWizardsName have successfully removed a bad spell which 
								was cast at the province of <i>".$targetProvince->provinceName." (#".$targetProvince->getkiID().")</i>. 
								The cleansed spell was: ".$spellsRemovedHtml."<br><br>Your ".$ourWizardsName.".";
					$targetReport = $targetProvince->getAdvisorName().", the cleansing spell cast at us by the province of <i>".
								$province->provinceName." (#".$province->getkiID().")</i> has removed a bad spell from us! The removed spell 
								was: ".$spellsRemovedHtml;
					$province->postNews( $ownReport );
					$targetProvince->postNews( $targetReport );
			} else {
				$ownReport = $province->getShortTitle().", our $ourWizardsName could not cleanse any bad spells from the province of 
								<i>".$targetProvince->provinceName." (#".$targetProvince->getkiID().")</i>.<br><br>Your ".$ourWizardsName.".";
				$province->postNews( $ownReport );
			}
		}
		return true;
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
}
} // end if ! class exists
?>
