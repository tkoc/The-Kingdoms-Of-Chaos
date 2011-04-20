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

/* Cleanse class is the class of a spell, derived from SpellBase. It's a special spell since it
 * might be cast at provinces in your Kingdom to remove all spells.
 *
 * Author: Tasos Nistas 10.10.2009
 * 
 * Changelog:
 *
 * Version: 1.0
 * 
 */
/*$path = $GLOBALS['path_www_scripts'];
require_once($path."News.class.inc.php");*/
require_once( "SpellBase.class.inc.php" );
if( !class_exists( "CleanseSpell" ) ) {

class CleanseSpell extends SpellBase {	
	
	var $raceReq = array( "Elf" ); // this spell is available to
	var $sciReq = array( 'military' =>0, "infrastructure" => 0, "magic" => 2, "thievery" => 0 );
	var $mana = 13;
	var $sID = 0;

	function CleanseSpell( $sID ) {
		$this->SpellBase( 	$sID, 									// spell ID
							"Cleanse",								// name
							3,					// gold cost pr acre
							2,					// metal cost pr acre
							0,					// food cost pr acre
							1,					// needed wizards pr acre
							0,					// cast on all
							0,					// type direct
							"The Cleanse spell removes a spell cast on a province. It will remove a 
							number of friendly or unfriendly spells which are on 
							the target province. The number of spells removed depends on the number 
							of wizards you use (as the only spell which gets better the more wizards 
							you use even above the recommended amount), the number of wizards which 
							are maintaining the spell and the strength of the spell, but there's 
							always at least a tiny chance of removing at least one spell. Only Elves can cast Cleanse",	// description
							false);										// picture
	}
	
	function spellEffect( &$db, $province, $targetProvince, $wizards, $other=false ) 
	{
		$dummy = NULL;
		$magic = new Magic( $db, $dummy );
		mt_srand( $this->makeSeed() );
		$html = "";
		$html .= "<br>".$province->getShortTitle().",";
		$milArray = $province->milObject->getMilUnit( $province->milObject->MilitaryConst->WIZARDS );
		$ourWizardsName = strtolower( $milArray['object']->getName() );
		
			$selectSQL = 	"SELECT S.spellID, S.strength, S.wizards, S.sID
							FROM Spells S 
							WHERE S.targetID='".$targetProvince->getpID()."'
							ORDER BY S.strength DESC";
			if( ( $spells = $db->query( $selectSQL ) ) && $db->numRows() ) {
				$totalspells = $db->numRows();
				//echo "totalspells: $totalspells <br />";
				$spellsRemovedHtml = "";
				$spellsRemoved = 0;
				while( ( $row = $db->fetchArray( $spells ) ) ) 
				{
						// Old algorithm
						/*echo "w: $wizards s: $other";
						echo "<br>w: ".$row['wizards']." s: ".$row['strength'];
						echo "<br />wizards*other= ". $wizards * $other;
						$randomChance = mt_rand( 1, round( $wizards * $other ) );
						
						echo "<br />randomchance: $randomChance";
						echo "<br />ta row*row= ".$row['wizards'] * $row['strength'];
						if( $randomChance > ( $row['wizards'] * $row['strength'] ) ) 
						{*/
						
						// Soptep algorithm - 14 /12/ 2009
						if ($wizards >= $row['wizards']*2)
							$chance = 1;
						if ($wizards <= $row['wizards']/2)
							$chance = 100;
						else
							if ($wizards >= $row['wizards'])
								$chance = 100 - (($wizards / (2*$row['wizards'])) * 100);
							else
								$chance = (0 - (($wizards*50)/(0.5*$row['wizards']))) + 150;
						
						//echo $chance."<br />";
						$randomChance = mt_rand(1, 100);
						//echo $randomChance."<br />";
						
						
						if( $randomChance > $chance ) 
						{
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
				if( strlen( $spellsRemovedHtml ) ) {
					$html .=	" our $ourWizardsName have successfully removed some spells which were cast at the province of <i>".
								$targetProvince->provinceName." (#".$targetProvince->getkiID().")</i>. ";
					$ownReport = $province->getShortTitle().", we have succeeded in casting cleanse at the 
								province of <i>".$targetProvince->provinceName." (#".$targetProvince->getkiID().")</i>. Here's a list of 
								the spell".( $spellsRemoved > 1 ? "s" : "" )." which were removed: ".
								$spellsRemovedHtml."<br><br>Your ".$ourWizardsName.".";
					$targetReport = $targetProvince->getAdvisorName().", the province of <i>".
								$province->provinceName." (#".$province->getkiID().")</i> has cast cleanse at our province! Here's a list 
								of the spell".( $spellsRemoved > 1 ? "s" : "" )." they managed to remove: ".
								$spellsRemovedHtml;
					$casterReport = "Some spells which were cast at the province of <i>".$targetProvince->provinceName." (#".$targetProvince->getkiID().")</i>
								have been dispelled.Maybe a province has cast cleanse at the enemy's province! Here's a list 
								of the spell".( $spellsRemoved > 1 ? "s" : "" )." they managed to remove: ".
								$spellsRemovedHtml;
					$province->postNews( $ownReport );				
					$targetProvince->postNews( $targetReport );
					//$news->postNews($casterReport, $this->sID);
				}
				else {
					$html .= " our $ourWizardsName couldn't find any spells to remove from the province of <i>".
							$targetProvince->provinceName." (#".$targetProvince->getkiID().")</i>. Perhaps we should try to make use of more 
							$ourWizardsName?";
					
					$ownReport = $province->getShortTitle().", our $ourWizardsName couldn't find any spells to remove from the province of 
								<i>".$targetProvince->provinceName." (#".$targetProvince->getkiID().")</i>.<br><br>Your ".$ourWizardsName.". Perhaps we should try to make use of more 
							$ourWizardsName?";
					$province->postNews( $ownReport );
				}
			} 
			else {
				$html .= " our $ourWizardsName couldn't find any spells to remove from the province of <i>".
							$targetProvince->provinceName." (#".$targetProvince->getkiID().")</i>. Pherhaps we should try to make use of more 
							$ourWizardsName?";
				$ownReport = $province->getShortTitle().", our $ourWizardsName couldn't find any spells to remove from the province of 
								<i>".$targetProvince->provinceName." (#".$targetProvince->getkiID().")</i>.<br><br>Your ".$ourWizardsName.". Perhaps we should try to make use of more 
							$ourWizardsName?";
				$province->postNews( $ownReport );
			}
		
		return $html;
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
}
} // end if ! class exists
?>