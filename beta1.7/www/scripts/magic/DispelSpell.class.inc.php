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
/* Dispel class is the class of a spell, derived from SpellBase. It's a special spell since it
 * might be cast at yourself to remove a aggressive spell, and at another province to remove 
 * good spells.
 *
 * Author: Øystein Fladby 28.04.2003
 * 
 * Version: 1.0
 * 
 */
 
require_once( "SpellBase.class.inc.php" );
if( !class_exists( "DispelSpell" ) ) {

class DispelSpell extends SpellBase {		
	function DispelSpell( $sID ) {
		$this->SpellBase( 	$sID, 									// spell ID
							"Dispel",								// name
							3,					// gold cost pr acre
							2,					// metal cost pr acre
							0,					// food cost pr acre
							1,					// needed wizards pr acre
							0,					// cast on all
							2,					// type dispel
							"The Dispel spell removes a spell cast on a province. If cast at 
							your own province, you may choose which evil spell to remove. If cast 
							at another province, it will remove a number of spells which benefits 
							the target province. The number of spells removed depends on the number 
							of wizards you use (as the only spell which gets better the more wizards 
							you use even above the recommended amount), the number of wizards which 
							are maintaining the spell and the strength of the spell, but there's 
							always at least a tiny chance of removing at least one spell.",	// description
							false);										// picture
	}
	
	function spellEffect( &$db, $province, $targetProvince, $wizards, $other=false ) {
		$dummy = NULL;
		$magic = new Magic( $db, $dummy );
		mt_srand( $this->makeSeed() );
		$html = "";
		$html .= "<br>".$province->getShortTitle().",";
		$milArray = $province->milObject->getMilUnit( $province->milObject->MilitaryConst->WIZARDS );
		$ourWizardsName = strtolower( $milArray['object']->getName() );
		if( ( $province->getpID() == $targetProvince->getpID() ) && $other ) {			// get spell info
			$selectSQL = 	"SELECT S.sID, S.strength, S.wizards, P.pID
							FROM Spells S, Province P 
							WHERE S.spellID='$other'
							AND S.casterID = P.pID";
			$db->query( $selectSQL );
			if( $db->numRows() ) {
				$row = $db->fetchArray();
				$randomWizards = mt_rand( 1, round( $row['wizards']*strength) );
				if( $wizards > $randomWizards ) {
					$deleteSQL = "DELETE FROM Spells WHERE spellID='$other'";	// remove spell
					$db->query( $deleteSQL );			
					$casterProvince = new Province( $row['pID'], $db );
					$casterProvince->getProvinceData();
					$spellName = $magic->spells[ $row['sID'] ]->baseFunction( $GLOBALS['magicConstants']->GET_NAME );
					$type = $magic->spells[ $row['sID'] ]->baseFunction( $GLOBALS['magicConstants']->GET_TYPE );
					$html .= 	" our $ourWizardsName have successfully removed a 
								spell which cast on our province! I think the $ourWizardsName 
								even found out ".
								($type == 3 ? "which triggered spell it was and " : "")
								."which province it came from, but they said 
								they would write a report and put it in Your room with the other news, ".
								$province->getAdvisorName();
					$ownReport = $province->getShortTitle().", we have removed the ".
								($type == 3 ? "triggered " : "")."<i>$spellName</i> spell which 
								was cast at our province! It was an evil spell, indeed, and it was hard to 
								understand it, but we managed to cut it off. It seemed to be cast from the 
								province of <i>".$casterProvince->provinceName." (#".$casterProvince->getkiID().")</i>, by the way. 
								<br> Your $ourWizardsName";	
					$casterReport = $casterProvince->getShortTitle().", our ".
									($type == 3 ? "triggered " : "")."<i>$spellName</i> 
									spell which was cast at the province of <i>".
									$province->provinceName." (#".$province->getkiID().")</i> has been blocked!";						
					$province->postNews( $ownReport );
					$casterProvince->postNews( $casterReport );
				} else {
					$html .= 	" our $ourWizardsName couldn't remove the 
								spell which cast on our province! Perhaps you should try with more wizards, ".
								$province->getAdvisorName();					
					$casterReport = $casterProvince->getShortTitle().", the province of 
									<i>".$province->provinceName." (#".$province->getkiID().")</i>
									has tried to counter the ".($type == 3 ? "triggered " : "")."<i>$spellName</i> 
									spell which we have cast at them, but they failed!";
					$casterProvince->postNews( $casterReport );
				}
			}
		} else {
			$selectSQL = 	"SELECT S.spellID, S.strength, S.wizards, S.sID
							FROM Spells S 
							WHERE S.targetID='".$targetProvince->getpID()."'
							ORDER BY S.strength DESC";
			if( ( $spells = $db->query( $selectSQL ) ) && $db->numRows() ) {
				$spellsRemovedHtml = "";
				$spellsRemoved = 0;
				while( ( $row = $db->fetchArray( $spells ) ) ) {
					if( $magic->spells[ $row['sID'] ]->baseFunction( $GLOBALS['magicConstants']->GET_CAST_ON ) == 0 ) { 			// if a defensive spell
						//echo "w: $wizards s: $other";
						//echo "<br>w: ".$row['wizards']." s: ".$row['strength'];
						$randomChance = mt_rand( 1, round( $wizards * $other ) );
						if( $randomChance > ( $row['wizards'] * $row['strength'] ) ) {
							$type = $magic->spells[ $row['sID'] ]->baseFunction( $GLOBALS['magicConstants']->GET_TYPE );
							$spellsRemovedHtml .= "<br>".($type == 3 ? "Triggered spell: " : "").
														$magic->spells[ $row['sID'] ]->baseFunction( $GLOBALS['magicConstants']->GET_NAME );
							$deleteSQL = "DELETE FROM Spells WHERE spellID='".$row['spellID']."'";
							$db->query( $deleteSQL );
							$wizards -= ( ( $row['strength'] * $row['wizards'] ) / 2 );
							$spellsRemoved++;
							//echo "<br>wa: $wizards";
						}
					}
				}
				if( strlen( $spellsRemovedHtml ) ) {
					$html .=	" our $ourWizardsName have successfully removed some defensive or 
								beneficial spells which were cast at the province of <i>".
								$targetProvince->provinceName." (#".$targetProvince->getkiID().")</i>. ";
					$ownReport = $province->getShortTitle().", we have succeeded in casting dispel at the 
								province of <i>".$targetProvince->provinceName." (#".$targetProvince->getkiID().")</i>. Here's a list of 
								the spell".( $spellsRemoved > 1 ? "s" : "" )." which were removed: ".
								$spellsRemovedHtml."<br><br>Your ".$ourWizardsName.".";
					$targetReport = $targetProvince->getAdvisorName().", the province of <i>".
								$province->provinceName." (#".$province->getkiID().")</i> has cast dispel at our province! Here's a list 
								of the spell".( $spellsRemoved > 1 ? "s" : "" )." they managed to remove: ".
								$spellsRemovedHtml;
					$province->postNews( $ownReport );
					$targetProvince->postNews( $targetReport );
				} else {
					$html .= " our $ourWizardsName couldn't find any spells to remove from the province of <i>".
							$targetProvince->provinceName." (#".$targetProvince->getkiID().")</i>. Perhaps we should try to make use of more 
							$ourWizardsName?";
				}
			} else {
				$html .= " our $ourWizardsName couldn't find any spells to remove from the province of <i>".
							$targetProvince->provinceName." (#".$targetProvince->getkiID().")</i>. Perhaps we should try to make use of more 
							$ourWizardsName?";
			}
		}
		return $html;
	}
	
}
} // end if ! class exists
?>
