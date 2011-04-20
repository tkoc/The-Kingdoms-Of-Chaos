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

/* HolySpell class is the class of a spell, derived from SpellBase.
 *
 * Author: Øystein Fladby 24.03.2004
 * 
 * Changelog:
 *
 * Version: 1.0
 * 
 */
 
require_once( "SpellBase.class.inc.php" );
if( !class_exists( "HolySpell" ) ) {

class HolySpell extends SpellBase {
	var $minKilled = 0.5;	//min % of undead killed
	var $maxKilled = 1; 	//max % of undead killed
	var $minBuildingsDestroyed = 1; // crypts
	var $maxBuildingsDestroyed = 5; // crypts
	var $raceReq = array( "Human", "Elf" ); // this spell is available to
	var $targetRace = array( "Undead" );
	var $targetBuildings = array( "Crypt" );
	var $sciReq = array( 'military' =>0, "infrastructure" => 0, "magic" => 2, "thievery" => 0 );
	var $mana = 20;
	
	function HolySpell( $sID ) {
		$this->SpellBase( 	$sID, 									// spell ID
							"Holy",						// name
							15,					// gold cost pr acre
							2,					// metal cost pr acre
							0,					// food cost pr acre
							0.75,				// needed wizards pr acre
							1,					// cast on enemies
							0,					// type direct
							"Holy will do damage to undead creatures and crypts. 
							It may only be cast by elves and humans.",	// description
							false);									// picture
	}	
	function getNeededMana() {
		return $this->mana;
	}
	function spellEffect( &$db, $province, $targetProvince, $wizards, $strength ) {
		$result = false;
		if( $targetProvince->raceObj->raceReqOk( $this->targetRace ) ) {
			$result = true;
			$dummy = NULL;
			require_once( "Buildings.class.inc.php" );
			$buildings = new Buildings( $db, $dummy );
			mt_srand( $this->makeSeed() );
			$destroyedHtml = "";
			$destroyBuildings = mt_rand( round( $targetProvince->acres * ( $strength * $this->minBuildingsDestroyed / 100 ) ), 
									 round( $targetProvince->acres * ( $strength * $this->maxBuildingsDestroyed / 100 ) ) );
			$killPeasants = mt_rand( round( $targetProvince->peasants * ( $strength * $this->minKilled / 100 ) ), 
									 round( $targetProvince->peasants * ( $strength * $this->maxKilled / 100 ) ) );
			$targetProvince->usePeasants( $killPeasants );
			$buildingsResult = $buildings->destroySpecificBuildings( $targetProvince->getpID(), $destroyBuildings, $this->targetBuildings );
			$unitKilledHtml = "";	
			$unitsKilled = $killPeasants;
			$homeUnits = $targetProvince->milObject->getMilitaryHome();
			foreach( $homeUnits as $unit ) {
				$numberOfUnits = $unit['num'];
				$unitName = $unit['object']->getName();			
				$killUnits = mt_rand( 	round( $numberOfUnits * ( $strength * $this->minKilled / 100 ) ), 
										round( $numberOfUnits * ( $strength * $this->maxKilled / 100 ) ) );
				$killUnits = round($killUnits*$province->sizeModifier($targetProvince));								 
				$targetProvince->milObject->killUnits( $unit['object']->getMilType(), $killUnits );
				$unitKilledHtml .= "<br>$killUnits of the $unitName were disintegrated";
				$unitsKilled += $killUnits;
			}
			$ownReport = 	$province->getShortTitle().", we successfully cast the $this->name spell at the 
							province of <i>".$targetProvince->provinceName." (#".$targetProvince->getkiID().")</i> with the result that 
							".$buildingsResult['totDestroyed']." undead buildings were destroyed and 
							$unitsKilled of the undead monsters were disintegrated!";
			$targetReport = $targetProvince->getAdvisorName().", the province of <i>".$province->provinceName."
							 (#".$province->getkiID().")</i> has cast the $this->name spell at our province, having a devastating effect! 
							We think our losses are: 
							<br>$killPeasants of the peasants found their final rest.
							$unitKilledHtml. <br>
							Also we lost some buildings:".$buildingsResult['html'].".<br>".$targetProvince->getShortTitle().
							", I suggest we attack them back at once!";
		} else {
			$ownReport = $province->getShortTitle().", we successfully cast the $this->name spell at the 
						province of <i>".$targetProvince->provinceName." (#".$targetProvince->getkiID().")</i>, but because there 
						were no undead in that province that we know about, it didn't have any effect at all!";
			$targetReport = $targetProvince->getAdvisorName().", the province of <i>".$province->provinceName."
						 (#".$province->getkiID().")</i> has cast the $this->name spell at our province, but because there 
						were no undead in our province that we know about, it didn't have any effect at all!";
		}
		$province->postNews( $ownReport );
		$targetProvince->postNews( $targetReport );
		return $result;
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