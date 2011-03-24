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
/* EarthQuakeSpell class is the class of a spell, derived from SpellBase.
 *
 * Author: Øystein Fladby 28.04.2003
 * 
 * Version: 1.0
 * 
 */
require_once( "SpellBase.class.inc.php" );
if( !class_exists( "EarthQuakeSpell" ) ) {

class EarthQuakeSpell extends SpellBase {
	var $minDestroyedAcres = 5; 		// 2 of 100 of the provinces total acres might be destroyed
	var $minKilledPeasants = 3;			// 3 of 100 of the peasants might be killed
	var $minKilledUnits = 1;			// 1 of 100 of the units might be killed 
	var $maxDestroyedAcres = 8; 		// 5 of 100 of the provinces total acres might be destroyed
	var $maxKilledPeasants = 10;		// 10 of 100 of the peasants might be killed
	var $maxKilledUnits = 3;			// 3 of 100 of the units might be killed 
	var $raceReq = array( "Human", "Elf", "Orc", "Giant" ); // this spell is available to
	var $sciReq = array( 'military' =>0, "infrastructure" => 0, "magic" => 8, "thievery" => 0 );		// the required sciences to cast this spell
	var $mana = 40;
		
	function EarthQuakeSpell( $sID ) {
		$this->SpellBase( 	$sID, 									// spell ID
							"Earthquake",							// name
							50,				// gold cost pr acre
							15,				// metal cost pr acre
							0,				// food cost pr acre
							3,				// needed wizards pr acre
							1,				// cast on enemies
							0,				// type direct
							"The Earthquake spell will shake the very earth itself, destroying 
							a number of acres of an enemy province and the buildings on them. 
							It will also swallow and kill both soldiers and pesants. Guess there's
							no need to mention that this spell 
							shouldn't be used at your friends... Only humans, orcs, elves and giants may 
							cast this spell.",	// description
							false);									// picture
	}

	function getNeededMana() {
		return $this->mana;
	}
	
	function spellEffect( &$db, $province, $targetProvince, $wizards, $strength ) {
		$dummy = NULL;
		require_once( "Buildings.class.inc.php" );
		$buildings = new Buildings( $db, $dummy );
		require_once($GLOBALS['path_www_scripts']."News.class.inc.php" );
		$newsKingdom = new News($db, 0);
		
		mt_srand( $this->makeSeed() );
		$destroyedHtml = "";
		$destroyAcres = mt_rand( round( $targetProvince->acres * ( $strength * $this->minDestroyedAcres / 100 ) ), 
								 round( $targetProvince->acres * ( $strength * $this->maxDestroyedAcres / 100 ) ) );
		$destroyAcres = round($destroyAcres*$province->sizeModifier($targetProvince));								 
								 
		$killPeasants = mt_rand( round( $targetProvince->peasants * ( $strength * $this->minKilledPeasants / 100 ) ), 
					 round( $targetProvince->peasants * ( $strength * $this->maxKilledPeasants / 100 ) ) );
		$targetProvince->usePeasants( $killPeasants );
		$updateSQL = 	"UPDATE Province set acres=GREATEST(0, (acres-$destroyAcres) )
						WHERE pID LIKE '".$targetProvince->getpID()."'";
		$db->query( $updateSQL );
		$buildings->destroyBuildingsOnAcres( $targetProvince->getpID(), $destroyAcres );
		
		$unitKilledHtml = "";
		$unitsKilled = $killPeasants;
		$homeUnits = $targetProvince->milObject->getMilitaryHome();
		foreach( $homeUnits as $unit ) {
			$numberOfUnits = $unit['num'];
			$unitName = $unit['object']->getName();			
			$killUnits = mt_rand( 	round( $numberOfUnits * ( $strength * $this->minKilledUnits / 100 ) ), 
						round( $numberOfUnits * ( $strength * $this->maxKilledUnits / 100 ) ) );
			$killUnits = round($killUnits*$province->sizeModifier($targetProvince));								 
			$targetProvince->milObject->killUnits( $unit['object']->getMilType(), $killUnits );
			$unitKilledHtml .= "<br>$killUnits of the $unitName were killed";
			$unitsKilled += $killUnits;
		}
		$ownReport = 	$province->getShortTitle().", we successfully cast the $this->name spell at the 
						province of <i>".$targetProvince->provinceName." (#".$targetProvince->getkiID().")</i> with the result that 
						$destroyAcres acres and the buildings on them were destroyed and 
						$unitsKilled of the people died.";
		$targetReport = $targetProvince->getAdvisorName().", the province of <i>".$province->provinceName."
						 (#".$province->getkiID().")</i> has cast the $this->name spell at our province, having a devastating effect! 
						We think our losses are: 
						<br>$destroyAcres acres and the buildings on them were destroyed. 
						<br>$killPeasants of the peasants died.
						$unitKilledHtml. <br>".$targetProvince->getShortTitle().
						", I suggest we attack them back at once!";
		$ownKingdomReport = "\n\t\t<i>".$province->provinceName."</i> has cast the $this->name spell at the province of <i>".$targetProvince->provinceName." (#".$targetProvince->getkiID().")</i>, having a devastating effect! It destroyed $destroyAcres acres and killed many units.";
		$targetKingdomReport = "\n\t\tThe province of <i>".$province->provinceName." (#".$province->getkiID().")</i> has cast the $this->name spell at the province of <i>".$targetProvince->provinceName."</i>, having a devastating effect! It destroyed $destroyAcres acres and killed many units.";
		
		$province->postNews( $ownReport );
		$targetProvince->postNews( $targetReport );
		$newsKingdom->postNews($ownKingdomReport, $province->getkiID(), $newsKingdom->SYMBOL_NONE);
		$newsKingdom->postNews($targetKingdomReport, $targetProvince->getkiID(), $newsKingdom->SYMBOL_NONE);
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
