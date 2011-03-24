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
/* SoulHarvestSpell class is the class of a spell, derived from SpellBase.
 *
 * Author: Øystein Fladby 05.06.2004
 * 
 * Changelog:
 *
 * Version: 1.0
 * 
 */
 
require_once( "SpellBase.class.inc.php" );
if( !class_exists( "SoulHarvestSpell" ) ) {

class SoulHarvestSpell extends SpellBase {
	var $minKilled = 1.5;		// minimum killed units 
	var $maxKilled = 3.0;
	var $failedToTurn = 10; // % of the killed units which are not turned to undeads		
	var $raceReq = array( "Undead" ); // this spell is available to
	var $immuneRace = array( "Undead" ); // this races are immune to this spell
	var $sciReq = array( 'military' =>0, "infrastructure" => 0, "magic" => 8, "thievery" => 0 );		// the required sciences to cast this spell
	var $mana = 40;
	var $costPeasants = 0.5;
	
	function SoulHarvestSpell( $sID ) {
		$this->SpellBase( 	$sID, 									// spell ID
							"Soul harvest",						// name
							70,					// gold cost pr acre
							25,					// metal cost pr acre
							20,					// food cost pr acre
							3,					// needed wizards pr acre
							1,					// cast on enemies
							0,					// type direct
							"Soul harvest turns a number of army units and peasants, adding them 
							to the undead army of the caster. Only undead may cast this spell and 
							other undead are immune to it.",	// description
							false);									// picture
	}
	function spellEffect( &$db, $province, $targetProvince, $wizards, $strength ) {
		if( ! $targetProvince->raceObj->raceReqOk( $this->immuneRace ) ) {

			mt_srand( $this->makeSeed() );
			$killPeasants = mt_rand(round( $targetProvince->peasants * ( $strength * $this->minKilled / 100 ) ), 
					 						round( $targetProvince->peasants * ( $strength * $this->maxKilled / 100 ) ) );
			$turnedPeasants = round( ( 1-( $this->failedToTurn / 100 ) ) * $killPeasants );
			$targetProvince->usePeasants( $killPeasants );
			$updateSQL = 	"UPDATE Province set peasants=(peasants+$turnedPeasants)
											WHERE pID='".$province->getpID()."'";
			$db->query( $updateSQL );
		
			$unitKilledHtml = "";
			$unitsTurned = 0;
			$unitsKilled = 0;
			$homeUnits = $targetProvince->milObject->getMilitaryHome();
			foreach( $homeUnits as $unit ) {
				$numberOfUnits = $unit['num'];
				$unitName = $unit['object']->getName();			
				$killUnits = mt_rand( round( $numberOfUnits * ( $strength * $this->minKilled / 100 ) ), 
															round( $numberOfUnits * ( $strength * $this->maxKilled / 100 ) ) );
				$killUnits = round($killUnits*$province->sizeModifier($targetProvince));															
				if ($unit['object']->getMilType() == $targetProvince->milObject->MilitaryConst->ELITE_SOLDIERS)
				{
					// elites are more resistant to this spell now.
					$killUnits = round(0.5*$killUnits);
				}
				if( $killUnits ) {
					$turnedUnits = round( ( 1-( $this->failedToTurn / 100 ) ) * $killUnits );
					$targetProvince->milObject->killUnits( $unit['object']->getMilType(), $killUnits, false );
				
					$province->milObject->create( $unit['object']->getMilType(), $turnedUnits );
				
					$unitKilledHtml .= "<br>$killUnits of the $unitName were killed and $turnedUnits of them 
														joined the undead army!";
					$unitsTurned += $turnedUnits;
					$unitsKilled += $killUnits;
				}
			}
			$ownReport = 	$province->getShortTitle().", our $this->name spell cast at the 
							province of <i>".$targetProvince->provinceName." (#".$targetProvince->getkiID().")</i> has given
							good results!<br> 
							$killPeasants of their peasants died and $turnedPeasants of them were turned to undead 
							and are now serving our province!<br>
							$unitsKilled of their units were killed and $unitsTurned of them were turned to undead 
							and are now serving our province!.<br>";
			$targetReport = $targetProvince->getAdvisorName().", the $this->name spell cast at us by the province of <i>".$province->provinceName."
							 (#".$province->getkiID().")</i> is having a devastating effect! 
							We think our losses are: 
							<br>$killPeasants of the peasants died and $turnedPeasants of them were turned to undead!.
							$unitKilledHtml. <br>".
							$targetProvince->getShortTitle().", I suggest we attack them back at once!";
		} else {
			$ownReport = $province->getShortTitle().", we successfully cast the $this->name spell at the 
					province of <i>".$targetProvince->provinceName." (#".$targetProvince->getkiID().")
					</i>, but as they were also undead, the spell had no effect!";
			$targetReport = $targetProvince->getAdvisorName().", the province of <i>".$province->provinceName."
					(#".$province->getkiID().")</i> has cast the $this->name spell at our province, but 
					because we're already dead, we can't die again! Anyway, this was 
					a really powerful spell, so perhaps we should retaliate with some of our own stuff?";
		}
		$province->postNews( $ownReport );
		$targetProvince->postNews( $targetReport );
		return true;		
	}
		
	function getNeededMana() {
		return $this->mana;
	}	
	function scienceRequirements() {
		return $this->sciReq;
	}
	function raceRequirements() {
		return $this->raceReq;
	}
	function getCostPeasants() {
		return $this->costPeasants;
	}
}
} // end if ! class exists
?>
