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
/* Effect.class.inc.php - Effect class to calculate the combined effect from all
 * classes which uses the EffectBase.class.inc.php All classes that need an effect 
 * should make one object of this type, then call getEffect( FUNCTION_NAME_FROM_EFFECTBASE, pID )
 * to get the combined effect from all classes which makes use of the EffectBase class.
 * All buildings / sciences / magic / thievery / military etc. which requires something should 
 * call the reqsOk( ... , $pID ) function.
 *
 * This class might also be used to retrieve information / call functions from the empty magic/ science /
 * race / building/ province objects ( empty = without pID )
 *
 * Author: Øystein Fladby 03.09.2003
 * 
 *
 * Version test
 */
if( !class_exists( "Effect" ) ) {
require_once( "globals.inc.php" );
require_once( $GLOBALS['path_www_scripts'] . "Province.class.inc.php" );
require_once( $GLOBALS['path_www_scripts'] . "Race.class.inc.php" );
require_once( $GLOBALS['path_www_scripts'] . "Buildings.class.inc.php" );
require_once( $GLOBALS['path_www_scripts'] . "Magic.class.inc.php" );
require_once( $GLOBALS['path_www_scripts'] . "Science.class.inc.php" );
require_once( $GLOBALS['path_www_scripts'] . "Council.class.inc.php" );
require_once( $GLOBALS['path_www_scripts'] . "TrigEffect.class.inc.php" );
require_once( $GLOBALS['path_www_scripts'] . "seasons/SeasonFactory.class.inc.php");
require_once( $GLOBALS['path_www_scripts'] . "effect/EffectConstants.class.inc.php" );
$GLOBALS['effectConstants'] = new EffectConstants();

class Effect {
	var $db;
	var $sciObj;
	var $magicObj;
	var $buildObj;
	var $provinceObj;
	var $raceObj;
	var $councilObj;
	var $trigEffObj;
	var $beastObj;
	
	function Effect( $db ) {
		$dummy = NULL;
		$this->db = $db;
		$this->sciObj 		= new Science( $this->db, $dummy );
		$this->magicObj 	= new Magic( $this->db, $dummy );
		$this->buildObj		= new Buildings( $this->db, $dummy );
		$this->raceObj		= new Race( $this->db, $dummy );
		$this->councilObj	= new Council ($this->db,$dummy);
		$this->trigEffObj	= new TrigEffect($this->db);
	}
	
	////////////////////////////////////////////
	// Effect::getStrength
	////////////////////////////////////////////
	// Function to get the strength of a spell / thievery op
	// 0.0001-1 = less strength
	// 1 = normal strength
	// Returns:
	//		A float where 1 is no effect
	////////////////////////////////////////////
	function getStrength( $targetAcres, $ownAcres, $targetPA, $ownPA, $other=false ) {
		$result = 1;
		//echo "<br>tA: $targetAcres<br>oA: $ownAcres<br>opa: $ownPA<br>tpa: $targetPA";
		$result *= ( 1 + log10( ( min( $targetAcres, $ownAcres ) ) / ( max( $targetAcres, $ownAcres ) ) ) );
		//echo "<br>res1: $result";
		if( $ownPA && $targetPA ) {										// make sure this expression won't be zero (division and log)
			$tmp = ( 1 + log10( $ownPA / $targetPA ) );
			$result *= $this->makeNumberBetween( $tmp, 0.0001, 1 );
		//echo "<br>res2: $result";
		} else if( !$ownPA && $targetPA ) {							// the target has wizards, the caster don't
			$result *= 0;
		//	echo "<br>res3: $result";
		}																				// else no one has wizards => 1, caster has and target don't => 1
		//echo "<br>res4: $result";
		if( $other ) {														// good spells gets double strength
			$result *= 2;
		}
		$result = $this->makeNumberBetween( $result, 0.0001, 1 );	// make sure no strength above 100%
		//echo "<br>end result: $result";
		return $result;
	}
	
	////////////////////////////////////////////
	// Effect::reducedEffect
	////////////////////////////////////////////
	// Function to get the reduced effect due to size diff
	// Returns:
	//		A float between 0.0001 and 2
	////////////////////////////////////////////
	function reducedEffect( $acres, $networth, $targetAcres, $targetNetworth ) {
		// TODO!!! OBS !!!! Have to fill in the nice little formula here.
		return 1;
	}

	////////////////////////////////////////////
	// Effect::getEffect
	////////////////////////////////////////////
	// Function to get the combined effect from all classes that uses EffectBase
	// Takes a function from EffectConstants and the province ID
	// Returns:
	//		A float between 0.0001 and 2 where 1 is no effect
	////////////////////////////////////////////
	function getEffect( $FUNCTION_FROM_EFFECT_CONSTANTS, $pID ) {
		
		$result = 1;
		//echo "<br>$FUNCTION_FROM_EFFECT_CONSTANTS:";
		$result *= $this->makeNumberBetween( $this->sciObj->getScienceEffect( $FUNCTION_FROM_EFFECT_CONSTANTS, $pID ) );
		//echo "<br>sci: $result";
		$result *= $this->makeNumberBetween( $this->magicObj->getSpellEffect( $FUNCTION_FROM_EFFECT_CONSTANTS, $pID ) );
		//echo "<br>spell: $result";
		$result *= $this->makeNumberBetween( $this->buildObj->getEffect( $FUNCTION_FROM_EFFECT_CONSTANTS, $pID ) );
		//echo "<br>build: $result";
		$result *= $this->makeNumberBetween( $this->raceObj->getEffect( $FUNCTION_FROM_EFFECT_CONSTANTS, $pID ) );
		//echo "<br>race: $result";
		$result *= $this->makeNumberBetween( $this->councilObj->getCouncilEffect( $FUNCTION_FROM_EFFECT_CONSTANTS, $pID ) );

		$result *= $this->makeNumberBetween((float) 1 + ((float) $GLOBALS['CurrentSeason']->$FUNCTION_FROM_EFFECT_CONSTANTS())/100.0);
		
		//$dbgRes = 1; Effect class added and removed by baalPeor...
		//$result *= $this->makeNumberBetween( $this->trigEffObj->getEffect($FUNCTION_FROM_EFFECT_CONSTANTS, $pID) );
		//echo "DEBUG::EFFECT::$dbgRes<br>";
		//echo "<br>end: $result<br>";
		return $result;
	}
	
	////////////////////////////////////////////
	// Effect::makeNumberBetween
	////////////////////////////////////////////
	// Function to make a number between a max and min value
	// Returns:
	//		A float between 0.0001 and 5
	////////////////////////////////////////////
	function makeNumberBetween( $inNumber, $from=0.0001, $to=2 ) {
		return ( ( $inNumber < $from ) ? $from : ( ( $inNumber > $to ) ? $to : $inNumber ) );
	}
	
	////////////////////////////////////////////
	// Effect::reqsOk
	////////////////////////////////////////////
	// * Function to check that all requirements are met.
	// * Takes arrays of building NAMES, race NAMES and science IDS
	// * needXxx are requirements the player MUST have finished / met
	// * permittedXxx are buildings/ sciences the player CAN NOT 
	// have started building / researching or have finished 
	// a value of 0 is always ok
	// Returns:
	//		True if all conditions are met
	// 		False if one contition fails
	////////////////////////////////////////////
	function reqsOk( $needBuildings, $needSciences, $needRaces, $permittedBuildings, $pID ) {
		if( !$this->buildObj->buildingReqOk( $needBuildings, 	$pID ) 	||	// check for at least one finished building of each required type
			$this->buildObj->haveBuilding( $permittedBuildings, $pID ) ) {	// check for at least one finished/in progress building of at least one type
			return false;
		} 
		if( !$this->raceObj->raceReqOk( $needRaces, $pID ) ) {			// only one because a player may have only one race, and can't change it
			return false;
		} 
		if( !$this->sciObj->scienceReqOk( $needSciences, $pID ) ) {		//
			return false;
		}		
		return true;
	}
}
} // end if ! class exists
?>
