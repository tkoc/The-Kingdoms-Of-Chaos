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
/* DoomSpell class is the class of a spell, derived from SpellBase.
 *
 * Author: Øystein Fladby 25.03.2004
 * 
 * Changelog:
 *
 * Version: 2.0
 * 
 */
 
require_once( "SpellBase.class.inc.php" );
if( !class_exists( "DoomSpell" ) ) {

class DoomSpell extends SpellBase {
	var $minKilled = 0.5;		// minimum killed units 
	var $maxKilled = 1.5;		// 
	var $raceReq = array( "Undead" ); // this spell is available to
	var $immuneRace = array( "Undead" ); // this races are immune to this spell
	var $sciReq = array( 'military' =>0, "infrastructure" => 0, "magic" => 8, "thievery" => 0 );		// the required sciences to cast this spell
	var $mana = 30;
	var $costPeasants = 0.5;
	
	function DoomSpell( $sID ) {
		$this->SpellBase( 	$sID, 									// spell ID
							"Doom",						// name
							50,					// gold cost pr acre
							20,					// metal cost pr acre
							10,					// food cost pr acre
							3,					// needed wizards pr acre
							1,					// cast on enemies
							1,					// type indirect
							"Doom kills a number of army units and peasants each day. Only 
							undead may cast this spell and other undead are immune to it.",	// description
							false);									// picture
	}
	function spellEffect( &$db, $pID, $targetpID, $wizards, $strength ) {
		$targetProvince = new Province( $targetpID, $db );
		$targetProvince->getProvinceData();
		$province = new Province( $pID, $db );
		$province->getProvinceData();
		
		require_once($GLOBALS['path_www_scripts']."News.class.inc.php" );
		$newsKingdom = new News($db, 0);		
		
		if( ! $targetProvince->raceObj->raceReqOk( $this->immuneRace ) ) {			
			$province->getMilitaryData();	
			$targetProvince->getMilitaryData();
			
			mt_srand( $this->makeSeed() );
			$killPeasants = mt_rand(round( $targetProvince->peasants * ( $strength * $this->minKilled / 100 ) ), 
					 				round( $targetProvince->peasants * ( $strength * $this->maxKilled / 100 ) ) );
			$targetProvince->usePeasants( $killPeasants );
			$updateSQL = 	"UPDATE Province set peasants=(peasants+$killPeasants) )
							WHERE pID LIKE '".$province->getpID()."'";
			$db->query( $updateSQL );
		
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
				$unitKilledHtml .= "<br>$killUnits of the $unitName were killed!";
				$unitsKilled += $killUnits;
			}
			$ownReport = 	$province->getShortTitle().", our $this->name spell cast at the 
							province of <i>".$targetProvince->provinceName." (#".$targetProvince->getkiID().")</i> has given
							good results!<br> 
							$killPeasants of their peasants died.<br>
							$unitsKilled military units died.<br>";
			$targetReport = $targetProvince->getAdvisorName().", the $this->name spell cast at us is having a devastating effect! 
							We think our losses are: 
							<br>$killPeasants of the peasants died.
							$unitKilledHtml. <br>".$targetProvince->getShortTitle().
							", I suggest we attack them back at once!";
			$targetKingdomReport = "\n\t\tOur magical leaders felt an evil power used against our kingdom. They suppose it is the $this->name spell, however they can't tell what the effect is, and on which province it was casted.";
			
			$province->postNews( $ownReport );
			$targetProvince->postNews( $targetReport );
			$newsKingdom->postNews($targetKingdomReport, $targetProvince->getkiID(), $newsKingdom->SYMBOL_NONE);
			
			return true;
		}	else {
			$ownReport = $province->getShortTitle().", our $this->name spell cast at the 
							province of <i>".$targetProvince->provinceName." (#".$targetProvince->getkiID().")</i> 
							will not give any results at all because their race is immune to this spell!";
			$targetReport = $targetProvince->getAdvisorName().", the $this->name spell cast at us is not having any effects at all because we are 
							 of a race immune to this spell!";			
			$province->postNews( $ownReport );
			$targetProvince->postNews( $targetReport );
			return false;
		}					
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
