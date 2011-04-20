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

/* ApocalypseSpell class is the class of a spell, derived from SpellBase.
 *
 * Author: �ystein Fladby 03.06.2003
 * 
 * Changelog
 * 23.03.04 �ystein: Added tweaking possibilities for lost resources. Reduced damage a little and 
 *					increased cost a little
 *
 * 19.02.06 �ystein: Fixed a few bugs in effect_1, effect_4 and in the ownKingdom effect
 *
 * Version: 1.1
 * 
 */
 
if( !class_exists( "ApocalypseSpell" ) ) {
require_once( "SpellBase.class.inc.php" );

class ApocalypseSpell extends SpellBase {
	var $db;
	var $province;
	var $targetProvince;
	var $wizards;
	var $casterWizName;
	var $strength;
	
	var $minDestroyedAcres = 7.5; 		// 15 of 100 of the provinces total acres might be destroyed
	var $minKilledPeasants = 7.5;		// 15 of 100 of the peasants might be killed
	var $minKilledUnits = 7.5;			// 15 of 100 of the units might be killed 
	var $maxDestroyedAcres = 15; 		// 25 of 100 of the provinces total acres might be destroyed
	var $maxKilledPeasants = 15;		// 25 of 100 of the peasants might be killed
	var $maxKilledUnits = 15;			// 25 of 100 of the units might be killed
	
	var $morale = 75;					// 75% morale gone
	var $mana = 100;					// 100% mana gone
	var $influence = 100;				// 100% influence gone
	
	var $gold = 90;						// 90% of gold destroyed
	var $metal = 90;					// 90% of metal destroyed
	var $food = 25;						// 25% of food destroyed
	
	var $backFireWizardsDead = 90;		// 90% of the used wizards are killed
	
	var $ownReport = "";
	var $targetReport = "";
	
	var $sciReq = array( 'military' =>128, "infrastructure" => 128, "magic" => 128, "thievery" => 128 );		// the required sciences to cast this spell
		
	function ApocalypseSpell( $sID ) {
		$this->SpellBase( 	$sID, 									// spell ID
							"Apocalypse",							// name
							110,									// gold cost pr acre
							110,									// metal cost pr acre
							75,										// food cost pr acre
							10,										// needed wizards pr acre
							1,										// cast on enemies
							0,										// type direct
							"The effects of the Apocalypse spell is still unknown, but it's rumoured to 
							cause great disasters...",	// description
							false);									// picture
	}

	function getNeededMana() {
		return $this->mana;
	}
	
	function spellEffect( &$db, &$province, &$targetProvince, $wizards, $strength ) {
		$this->db = &$db;
		$this->province = &$province;
		$this->targetProvince = &$targetProvince;
		$this->wizards = $wizards;
		$this->strength = $strength;
		$milArray = $this->province->milObject->getMilUnit($this->province->milObject->MilitaryConst->WIZARDS);
		$this->casterWizName = strtolower( $milArray['object']->getName() );
		
		require_once( "News.class.inc.php" );		

		$allNews = 	"The crazy province of <i>".$this->province->provinceName."
					 (#".$this->province->getkiID().")</i> 
					has just cast the $this->name spell at the province 
					of <i>".$this->targetProvince->provinceName." (#".$this->targetProvince->getkiID().")</i>... 
					Luckily, the world didn't end, but the ".$this->casterWizName." of 
					".$this->province->provinceName." say the effects were not as they had 
					thought, and they'll continue trying to get the spell right!";
		$news = new News( $this->db, 0 );
		$news->postAll( $allNews );	
	
		mt_srand( $this->makeSeed() );
		$this->province->useMana(100);
		if( mt_rand( 1, 5 ) == 1 ) {
			$this->effect_1();
			$this->ownKingdomEffect();
		} else {
			$noOfEffects = mt_rand( 1, 3 );
			$effects = range( 2, 5 );
			srand( $this->makeSeed() );
			shuffle( $effects );
			
			$this->ownReport = $this->province->getShortTitle().", we successfully cast the $this->name spell at the 
						province of <i>".$this->targetProvince->provinceName." (#".$this->targetProvince->getkiID().")</i> with the result of ";
						
			$this->targetReport = $this->targetProvince->getAdvisorName().", the province of <i>".
						$this->province->provinceName." (#".$this->province->getkiID().")
						</i> has cast the $this->name spell at our province, with the result of ";					
			for( $i = 0; $i < $noOfEffects; $i++ ) {
				if( $i != 0 ) {
					$this->ownReport .=		"<br>There's also reports of ";
					$this->targetReport .=	"<br>There's also reports of ";
				}
				$effectNumber = array_pop( $effects );
				$function = "effect_".$effectNumber;
				$this->$function();
			}
			$this->ownReport .= "<br>This was not excactly the effect the ".$this->province->military[5]['name']." 
						had anticipated, though and they'll study hard to do it better the next time.";
						
			$this->targetReport .= "<br>Luckily, the world didn't end... yet, I suggest we attack them 
						back at once so they'll never be able to try this spell again!";
			$this->province->postNews( $this->ownReport );
			$this->targetProvince->postNews( $this->targetReport );
		}		
	}
	
	function effect_1() {			// backFire / p� seg selv
		$this->province->milObject->killUnits( $this->provinceObj->milObject->MilitaryConst->WIZARDS, floor( $this->wizards * $this->backFireWizardsDead / 100 ) );
		$targetReport = $this->targetProvince->getAdvisorName().", the province of <i>".
						$this->province->provinceName." (#".$this->province->getkiID().")
						</i> has tried to cast the ".$this->name." spell at our province, but something went terribly 
						wrong... It seems the spell backfired at them or something. The effect of this spell 
						is still unknown, and it shouldn't be cast at all!
						<br>Luckily, the world didn't end... yet, I suggest we attack them 
						back at once so they'll never be able to try this spell again!";
		$this->targetProvince->postNews( $targetReport );
		$ownReport = "Oh my good God, ".$this->province->getShortTitle().", 
					what have we done! Our ".$this->casterWizName." managed to cast the ".
					$this->name ." spell at the province of <i>".$this->targetProvince->provinceName."
					 (#".$this->targetProvince->getkiID().")</i> 
					and everything seemed to work the way they wanted it to... But then suddenly, something happened, 
					and the spell was thrown back at us with even greater force than we used to cast it! This caused 
					";
					
		// change target to self
		$origTargProv = $this->targetProvince;
		$this->targetProvince = $this->province;
		$this->effect_2();
		$this->targetReport .= "<br>It also caused ";
		$this->effect_3();
		$this->targetReport .= "<br>The last effect we have been able to detect is perhaps the most 
								scary of them all... ";
		$this->effect_4();
		$ownReport .= $this->targetReport;
		$ownReport .= "<br><br>The surviving $wizname say they'll study more and try to do a much better work 
					the next time You choose to cast the spell... If You ever dare to do so... ";
		$this->province->postNews( $ownReport );
		$this->targetProvince = $origTargProv;		
	}
	
	function effect_2() {			// earthquake / acres		
		require_once( "Buildings.class.inc.php" );
		$dummy = NULL;
		$buildings = new Buildings( $this->db, $dummy );
		mt_srand( $this->makeSeed() );
		$destroyAcres = mt_rand( floor( $this->targetProvince->acres * ( $this->strength * $this->minDestroyedAcres / 100 ) ), 
								 floor( $this->targetProvince->acres * ( $this->strength * $this->maxDestroyedAcres / 100 ) ) );
		$updateSQL = 	"UPDATE Province 
						SET acres=GREATEST( 0, (acres-$destroyAcres) )
						WHERE pID LIKE '".$this->targetProvince->getpID()."'";
		$this->db->query( $updateSQL );
		$buildings->destroyBuildingsOnAcres( $this->targetProvince->getpID(), $destroyAcres );
		$this->ownReport .= "a rare earthquake... It only raged and destroyed empty buildings and the acres they were 
						standing on... Anyway the result was this: 
						<br>$destroyAcres acres and the buildings on them were destroyed.";
		$this->targetReport .= "a rare effect! It seemed like an earthquake, but it only raged and destroyed 
						empty buildings and the acres they were standing on... So this is our estimate 
						of our losses:
						<br>$destroyAcres acres and the buildings on them were destroyed.";
	}
	
	function effect_3() {		// darkness / morale
		$updateSQL = 	"UPDATE Province 
						SET morale=GREATEST((morale-".$this->strength * $this->morale."),0),
							mana=GREATEST((mana-".$this->strength * $this->mana."),0), 
							influence=GREATEST((influence-(".$this->strength * $this->influence."),0) 
						WHERE pID LIKE '".$this->targetProvince->getpID()."'";
		$this->db->query( $updateSQL );
		$this->ownReport .= "a mystical magical darkness that laid itself over the province. Seems it emptied the magical 
						resources too! And made their thieves unable to lie. And I'm sure their military won't 
						like to train in the dark...";
		$this->targetReport .= "a mystrious	magical darkness that laid itself over the province. Seems it emptied the magical 
						resources too, and made our thieves unable to lie! And I'm sure our military won't 
						like to train in the dark...";
	}
	
	function effect_4() {			// flames 
		mt_srand( $this->makeSeed() );
		$destroyedHtml = "";
		$killPeasants = mt_rand( floor( $this->targetProvince->peasants * ( $this->strength * $this->minKilledPeasants / 100 ) ), 
								 floor( $this->targetProvince->peasants * ( $this->strength * $this->maxKilledPeasants / 100 ) ) );
		$this->targetProvince->usePeasants($killPeasants);
		$unitKilledHtml = "";
		$noOfUnitsKilled = $killPeasants;
		$homeUnits = $this->targetProvince->milObject->getMilitaryHome();
		foreach( $homeUnits as $unit ) {
			if( isset( $unit['num'] ) && $unit['num'] ) {
				$numberOfUnits = $unit['num'];			 
				$unitName = $unit['object']->getName();			
				$killUnits = mt_rand( 	round( $numberOfUnits * ( $this->strength * $this->minKilledUnits / 100 ) ), 
							round( $numberOfUnits * ( $this->strength * $this->maxKilledUnits / 100 ) ) );
				$this->targetProvince->milObject->killUnits( $unit['object']->getMilType(), $killUnits );
				$unitKilledHtml .= "<br>$killUnits of the $unitName were killed";
				$noOfUnitsKilled += $killUnits;					
			}
		}
		$this->ownReport .= "a great ball of flames that were rolling through the entire province. 
						The strangest thing was that it kept away from the buildings and was speeding up to 
						catch living people who tried to run away... The result is reported to be 
						".$noOfUnitsKilled." corpses.";
		$this->targetReport .= "a great ball of flames that were rolling through the entire province. 
						The strangest thing was that it kept away from the buildings and was speeding up 
						to catch living creatures who tried to run away... We estimated the losses to be:
						<br>".$killPeasants." of the peasants died.
						".$unitKilledHtml.". ";
	}
	
	function effect_5() {			// gull til metall, metall til jord
		$gold = $this->targetProvince->gold;
		$updateSQL = 	"UPDATE Province 
						SET metal = gold + ( metal - ( metal / 100 * ".$this->metal." ) ), 
							gold = gold - ( gold / 100 * ".$this->gold." ),
							food = food - ( food / 100 * ".$this->food." )
						WHERE pID LIKE '".$this->targetProvince->getpID()."'";
		$this->db->query( $updateSQL );
		$this->ownReport .= "a very strange thing indeed. Almost all their gold was transformed into metal while 
						their metal crumbled into rusty dust... If we could only figure out a way to 
						reverse this spell... Some of their food also got destroyed.";
		$this->targetReport .= "a very strange thing indeed. Almost all our gold was transformed into metal while 
						the metal crumbled into rusty dust... I really hope we'll someday be able to learn 
						the reverse of this magic... Some of our food also got destroyed.";
	}
	
	function ownKingdomEffect() {
		$origTargProv = $this->targetProvince;
		$kiID = $this->province->getkiID();
		$selectSQL ="SELECT *
						FROM Province
						WHERE kiID LIKE '$kiID'";
		$queryResult = $this->db->query( $selectSQL );
		
		$this->minDestroyedAcres = 1; 		
		$this->minKilledPeasants = 1;		
		$this->minKilledUnits = 1;			
		$this->maxDestroyedAcres = 5; 		
		$this->maxKilledPeasants = 5;		
		$this->maxKilledUnits = 2;			
		$this->morale = 50;
		$this->mana = 50;
		$this->influence = 30;
		$this->food = 10;
		$this->metal = 25;
		$this->gold = 25;
		$this->ownReport = $this->province->getShortTitle().", we have recieved other reports of our $this->name 
				spell which went so terribly wrong... I... it seems people from all over our Kingdom has 
				suffered from our failure!";
		$this->province->postNews( $this->ownReport );			
		while( $result = $this->db->fetchArray( $queryResult ) ) {
			if( $result['pID'] != $this->province->pID ) {
				$this->targetProvince = new Province( $result['pID'], $this->db );
				$this->targetProvince->getProvinceData();
				$this->targetProvince->getMilitaryData();
				$effects = range( 2, 5 );
				srand( $this->makeSeed() );
				shuffle( $effects );
				$effectNumber = array_pop( $effects );
				$function = "effect_".$effectNumber;
				
				$this->targetReport = $this->targetProvince->getAdvisorName().", the province of <i>".$this->province->provinceName."
						 (#".$this->province->getkiID().")</i> has tried to cast the ".$this->name." spell at <i>".
						 $origTargProv->provinceName." (#".$origTargProv->getkiID().")</i>, but something went 
						terribly wrong, and	our very own province, among others in this Kingdom, has suffered! 
						There are reports of ";
				$this->$function();
				$this->targetReport .= "<br>".$this->targetProvince->getAdvisorName().", do You think we should ask <i>".
						$this->province->provinceName." (#".$this->province->getkiID().")</i> to stop this dangerous spellcasting? Or perhaps they'll have more luck 
						the next time...";
				$this->targetProvince->postNews( $this->targetReport );			
			}
		}
		$this->targetProvince = $origTargProv;
	}
		
	function scienceRequirements() {		
		return $this->sciReq;
	}
}
} // end if ! class exists
?>