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

/* Magic class to handle all functionality of displaying the magic page, casting spells and showing
 * spells already cast by / on the province
 *
 * Author: Øystein Fladby 28.04.2003
 * ChangeLog:
 * Øystein 	xx.xx.2003	- This clas now uses Effect class to get effects from other classes and may 
 *						use peasants as a resource
 * Øystein 	27.10.2003	- Added the getStrength function, kingdom numbers and added a little on the chance to cast formula
 * Øystein 	03.09.2003	- Rewrote most of the code to implement the EffectBase class and the
 *						MagicConstants class. Implemented buildings, race and magic effects, but 
 *						the science effects are still missing. Also need to check for peasants as a
 * 						resource and kill them if necessary. Implemented a new spelltype - Triggered 
 *						and have to change the dispel spell a little.
 * 
 * Version: 2.0
 * 
 */

if( !class_exists( "Magic" ) ) {
$GLOBALS['pathToSpells'] = "magic/";
require_once( $GLOBALS['path_www_scripts'] . $GLOBALS['pathToSpells']."MagicConstants.class.inc.php" );
require_once( $GLOBALS['path_www_scripts'] . "Province.class.inc.php" );
require_once( $GLOBALS['path_www_scripts'] . "Effect.class.inc.php" );
require_once( $GLOBALS['path_www_scripts'] . "ActionLogger.class.inc.php" );
require_once( $GLOBALS['path_www_scripts'] . "TrigEffect.class.inc.php" );

$GLOBALS['magicConstants'] = new MagicConstants();

class Magic {
	var $db;					
	var $pID = false;				// the casters province ID
	var $kiID = false;				// the choosen target kingdom
	var $targetpID = false;			// the choosen target provinceID
	var $sID = false;				// choosen spell ID
	var $targetSpellID = false;		// used for dispel on own province
	var $faultyInput = false;		// if the user input has errors, this is set to true
	var $wizardName = false;		// the name of the wizard unit in lowercase
	var $wizards = NULL;			// the number of magicians
	var $MANA_MODIFIER = 5;			// the amount of mana gained each tick
	var $MAX_LOST = 10;				// part of the used wizards (1 tenth is max)
	var $TARGET_MANA_LOST = 5;		// 1/5th of the mana used by the caster lost if target managed to stop spell
	var $LEAST_AMOUNT_MANA_USED = 5; // the least amount of mana used to cast any spell with any bonus
	var $messageToUser = "";		// might contain a message to the user
	var $provinceObj;				// Province object of the caster province
	var $targetProvinceObj;			// Province object of the target province
	var $targetWizName;			//the name of the targets magicians
	var $targetWizNum;			//the number of target magicians
	var $effectObj;					// Effect object
	var $MAX_SPELL_EFFECT = 2;	//maximum spell effect multiplier
	var $spells = array();			// Objects of all spells		($spells[sID] = object )
	var $activeSpells = array();	// list of all active spells	($activeSpells[$sID] = array with info)
	var $MAX_SPELL_STACK = 5;       // how many times can a province have a spell on itself with the same target and caster

	var $logdata = NULL;
	
	////////////////////////////////////////////
	// Magic::Magic
	////////////////////////////////////////////
	// Constructor to set up the object with necessary
	// variables. 
	// Takes a db-reference and optionally a province object / NULL / false
	////////////////////////////////////////////
	function Magic( &$db, &$province ) {
		$this->db = &$db;
		$this->setSpells();
		if( $province ) {
			$this->provinceObj = &$province;
			$this->pID = $this->provinceObj->getpID();
			$this->effectObj = new Effect( $this->db );
			$this->update();
			$this->pageHandler();
			$this->update();
		}
	}
	
	////////////////////////////////////////////
	// Magic::update
	////////////////////////////////////////////
	// Function to update this provinces variables
	////////////////////////////////////////////
	function update() {
		$this->provinceObj->getProvinceData();
		$this->provinceObj->getMilitaryData();
		$milArray = $this->provinceObj->milObject->getMilUnit($this->provinceObj->milObject->MilitaryConst->WIZARDS);
		$this->wizardName = strtolower( $milArray['object']->getName() );
		$this->wizards = $milArray['num'];
		$this->setActiveSpells();
	}
	
	////////////////////////////////////////////
	// Magic::pageHandler
	////////////////////////////////////////////
	// Function to set the right values for the province
	// variables and call the right functions 
	// when information has been 'post' or
	// 'get' to this page. 
	////////////////////////////////////////////
	function pageHandler() {
		if( isset( $_GET['delete'] ) &&
			!strcmp( $_GET['delete'], "ok" ) && 
			isset( $_GET['spID'] ) ) {
			$this->deleteSpell( $_GET['spID'] );
			$_SERVER['QUERY_STRING'] = preg_replace( "(spID=[0-9]*&delete=ok)", "", $_SERVER['QUERY_STRING'] );
		}
		if( isset( $_GET['kiID'] ) ) {
			$this->kiID = $_GET['kiID'];
		} else {
			$this->kiID = $this->provinceObj->getkiID();
		}
		if( isset( $_GET['sID'] ) ) {
			$this->sID = $_GET['sID'];
		} else {
			$this->sID = 1;
		}
		if( isset( $_GET['targetpID'] ) ) {
			$this->targetpID = $_GET['targetpID'];
		} else {
			$this->targetpID = $this->getProvince( $this->kiID );
		}
		$this->targetProvinceObj = new Province( $this->targetpID, $this->db );
		$this->targetProvinceObj->getProvinceData();
		$this->targetProvinceObj->getMilitaryData();
		$targetMilArray = $this->targetProvinceObj->milObject->getMilUnit($this->targetProvinceObj->milObject->MilitaryConst->WIZARDS);
		$this->targetWizName = strtolower( $targetMilArray['object']->getName() );
		$this->targetWizNum = $targetMilArray['num'];
		if( isset( $_POST['useMagic'] ) ) {
			$this->useMagic();
		}
	}
	
	////////////////////////////////////////////
	// Magic::setSpells
	////////////////////////////////////////////
	// Function to set the array $spells for
	// this province to contain objects of 
	// all spell types
	////////////////////////////////////////////
	function setSpells() {
		$selectSQL = "SELECT sID, className FROM SpellT ORDER BY className";
		if( $this->db->query( $selectSQL ) && $this->db->numRows() ) {
			require_once($GLOBALS['pathToSpells'] . "SpellBase.class.inc.php");
			while( ($className = $this->db->fetchArray())) {
				require_once( $GLOBALS['pathToSpells'].$className['className'].".class.inc.php");
				$id = $className['sID'];
				$this->spells[$id] = new $className['className']( $id );
			}
		}
	}
	
	////////////////////////////////////////////
	// Magic::setActiveSpells
	////////////////////////////////////////////
	// Function to set the array $activeSpells for
	// this province to contain all active spells 
	// cast by / on the given province (this province
	// if no pID is given)
	////////////////////////////////////////////
	function setActiveSpells( $pID=false ) {
		$this->activeSpells = NULL;
		$this->activeSpells = array();
		$pID = ( $pID ? $pID : $this->pID );
		$selectSQL = 	"SELECT S.spellID, S.casterID, S.targetID, S.sID, S.type, 
								S.ticks, S.strength, S.wizards, P.provinceName, P.kiID as tkiID, PP.provinceName as casterProvince, PP.kiID as ckiID 
					 	FROM Spells S, Province P, Province PP
						WHERE S.targetID=P.pID
						AND S.casterID=PP.pID
						AND ( S.casterID LIKE '$pID'
						OR S.targetID LIKE '$pID' )";
		if( $this->db->query( $selectSQL ) && $this->db->numRows() ) {
			while( $row = $this->db->fetchArray() ) {
				$this->activeSpells[ $row['spellID'] ] = $row;
			}
		}
	}
	
	////////////////////////////////////////////
	// Magic::getJavaScript
	////////////////////////////////////////////
	// Function to get the JavaScripts needed to
	// make the html work
	// Returns:
	//		String with html-java script
	////////////////////////////////////////////
	function getJavaScript() {
		$javaScript = "	<script language=\"JavaScript\" type=\"text/JavaScript\">
						<!--
						function MM_jumpMenu(targ,selObj,restore){
  							eval(targ+\".location='\"+selObj.options[selObj.selectedIndex].value+\"'\");
  							if (restore) selObj.selectedIndex=0;
						}
						-->
						</script>";
		return $javaScript;
	}
	
	////////////////////////////////////////////
	// Magic::getKingdoms
	////////////////////////////////////////////
	// Function to get a mysql result set with
	// all kingdom names and IDs
	// Returns:
	//		Mysql result set with provinces if any
	//		false otherwise
	////////////////////////////////////////////
	function getKingdoms() {
		$selectSQL = "SELECT name, kiID FROM Kingdom ORDER BY name";
		if( ( $kingdoms = $this->db->query( $selectSQL ) ) && $this->db->numRows() ) {
			return $kingdoms;
		}else {
			return false;
		}
	}
	
	////////////////////////////////////////////
	// Magic::getProvince
	////////////////////////////////////////////
	// Function to get (the first) province ID in
	// a kingdom
	// Returns:
	//		integer - pID
	//		$this->pID otherwise
	////////////////////////////////////////////
	function getProvince( $kiID ) {
		$selectSQL = 	"SELECT pID 
						FROM Province
						WHERE kiID LIKE '$kiID' ORDER BY provinceName";
		if( ( $this->db->query( $selectSQL ) ) && $this->db->numRows() ) {
			$row = $this->db->fetchArray();
			return $row['pID'];
		} else {
			return $this->pID;
		}
	}
	
	////////////////////////////////////////////
	// Magic::getProvinces
	////////////////////////////////////////////
	// Function to get a mysql result set with
	// all province names and IDs in a given kingdom
	// Returns:
	//		Mysql result set with provinces if any
	//		false otherwise
	////////////////////////////////////////////
	function getProvinces( $kiID ) {
		$selectSQL = 	"SELECT provinceName, pID 
						FROM Province
						WHERE kiID LIKE '$kiID' ORDER BY provinceName";
		if( ( $provinces = $this->db->query( $selectSQL ) ) && $this->db->numRows() ) {
			return $provinces;
		} else {
			return false;
		}
	}
	
	////////////////////////////////////////////
	// Magic::getDispelSpell
	////////////////////////////////////////////
	// Function to get a default spell to dispel when
	// casting dispel on yourself
	// Returns:
	//		int - SpellID
	//		false if none found
	////////////////////////////////////////////
	function getDispelSpell() {
		foreach( $this->activeSpells as $spell ) {
			if( ( $spell['targetID'] == $this->pID ) && ( $this->spells[ $spell['sID'] ]->baseFunction( $GLOBALS['magicConstants']->GET_CAST_ON ) == 1 ) ) {
				return $spell['spellID'];
			}
		}
		return false;
	}
	
	////////////////////////////////////////////
	// Magic::getUsedWizards
	////////////////////////////////////////////
	// Function to get the number of wizards busy
	// with maintaining spells
	// Returns:
	//		Integer value of wizards
	////////////////////////////////////////////
	function getUsedWizards() {
		$usedWizards = 0;
		if( is_array( $this->activeSpells ) ) {
			foreach( $this->activeSpells as $spell ) {
				if( $spell['casterID'] == $this->pID ) {
					$usedWizards += $spell['wizards'];
				}
			}
		}
		return $usedWizards;
	}
	
	////////////////////////////////////////////
	// Magic::getSpellEffect
	////////////////////////////////////////////
	// Function to get a given spell effect of a province
	// Takes the function name (from EffectBase class )
	// and the province ID if this->pID shouldn't be used.
	// Returns:
	//		A float where 1 is no effect
	////////////////////////////////////////////
	function getSpellEffect( $FUNC_TYPE, $pID=false ) {
		if( $pID ) {
			$this->setActiveSpells( $pID );
		} else {
			$pID = $this->pID;
		}
		
		$effect = 1;										
		foreach( $this->activeSpells as $spell ) {			// go through all active spells
			$sID = $spell['sID'];
			if( $this->spells[$sID]->baseFunction( $GLOBALS['magicConstants']->GET_TYPE ) == 1 ) {
				if( $spell['targetID'] == $pID ) {				// if the target is $pID
					$effect *= ( 1 + ( ( $this->spells[$sID]->$FUNC_TYPE() / 100.00 ) * $spell['strength'] ) );
				}												// add / sub effect
			}
		}
		if( $this->pID ) {
			$this->setActiveSpells( $this->pID );
		}
		$effect = $this->makeNumberBetween( $effect );
		return $effect;
	}
	
	////////////////////////////////////////////
	// Magic::addMagicEffect
	////////////////////////////////////////////
	// Function to get the added effect due to size diff or other effects
	// Returns:
	//		A float between 0.0001 and 2
	////////////////////////////////////////////
	function addMagicEffect() {
		$result = 1;
		$result *= $this->effectObj->getEffect( $GLOBALS['effectConstants']->ADD_SPELL_EFFECT, $this->pID );
		return $result;
	}
	
	////////////////////////////////////////////
	// Magic::getStrength
	////////////////////////////////////////////
	// Function to get the strength of a spell
	// 0.0001-1 = less strength
	// 1 = normal strength
	// Returns:
	//		A float where 1 is no effect
	////////////////////////////////////////////
	function getStrength() {
		$retval = $this->effectObj->getStrength(
					$this->targetProvinceObj->acres, 
					$this->provinceObj->acres, 
					($this->targetWizNum / $this->targetProvinceObj->acres), 
					($this->wizards / $this->provinceObj->acres), 
					($this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_CAST_ON ) == 0 ?
					true : false ) );
		
		//protection add and removed by baal-peor
		//if($this->spells[$this->sID]->getType() == 1) {
		//	$nowMana = $this->provinceObj->mana;
		//	$reduced = 100-$nowMana;
		//	if($reduced < 0) $reduced = 0;
		//	$reduceStr = (($reduced*0.5)/100);
		//	$retval2 = 1 * (1 - $reduceStr);
		//	$retval *= $retval2;
		//}
		//if( ( $this->pID != $this->targetpID ) && ($this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_CAST_ON ) != 0 ) ) { // if this isn't cast at self and isn't friendly
		//	$val1 = $this->effectObj->getEffect( $GLOBALS['effectConstants']->ADD_MAGIC_RESISTANCE, $this->targetpID );
		//	$retval *= $val1;
		//	//echo "adding Resistance - $val1";
		//}
		return $retval;
	}
	
	////////////////////////////////////////////
	// Magic::addManaCost
	////////////////////////////////////////////
	// Function to get additional mana needed to 
	// cast a spell.
	// 0.0001-1 = less mana
	// 1-2 = more mana
	// Returns:
	//		A float where 1 is no effect
	////////////////////////////////////////////
	function addManaCost() {
		$result = 1.00;
		$result *= $this->effectObj->getEffect( $GLOBALS['magicConstants']->ADD_MANA_COST, $this->pID );
		return $result;
	}
	
	////////////////////////////////////////////
	// Magic::addWizardUse
	////////////////////////////////////////////
	// Function to get additional wizards needed to 
	// cast a spell.
	// 0.0001-1 = fewer wizards
	// 1-2 = more wizards
	// Returns:
	//		A float where 1 is no effect
	////////////////////////////////////////////
	function addWizardUse() {
		$result = 1.00;
		$result *= $this->effectObj->getEffect( $GLOBALS['magicConstants']->ADD_WIZARD_USE, $this->pID );
		return $result;
	}
	
	////////////////////////////////////////////
	// Magic::addResourceUse
	////////////////////////////////////////////
	// Function to get additional resources needed to 
	// cast a spell.
	// 0.0001-1 = less resources
	// 1-> = more resources
	// ['gold'] , ['metal'], ['food'], ['peasants']
	// Returns:
	//		Associative array with values where 1 is no effect
	////////////////////////////////////////////
	function addResourceUse() {
		$result = array("gold" 		=> 1,
						"metal" 	=> 1,
						"food" 		=> 1,
						"peasants" 	=> 1 );
		$result['gold'] 		*= $this->effectObj->getEffect( $GLOBALS['magicConstants']->ADD_MAGIC_GOLD_COST, $this->pID );
		$result['metal'] 		*= $this->effectObj->getEffect( $GLOBALS['magicConstants']->ADD_MAGIC_METAL_COST, $this->pID );
		$result['food'] 		*= $this->effectObj->getEffect( $GLOBALS['magicConstants']->ADD_MAGIC_FOOD_COST, $this->pID );
		$result['peasants'] *= $this->effectObj->getEffect( $GLOBALS['magicConstants']->ADD_MAGIC_PEASANT_COST, $this->pID );
		return $result;
	}
	
	////////////////////////////////////////////
	// Magic::addChanceToCast
	////////////////////////////////////////////
	// Function to get additional chance to cast a spell.
	// 0.0001-1 = less chance
	// 1-> = greater chance
	// uses caster=$this->pID, target=$this->targetpID
	// Returns:
	//		A float where 1 is no effect
	////////////////////////////////////////////
	function addChanceToCast() {
		$addChanceToCast = 1;
		$addChanceToCast *= $this->effectObj->getEffect( $GLOBALS['magicConstants']->ADD_MAGIC_CHANCE, $this->pID );		
		return $addChanceToCast;
	}
	
	////////////////////////////////////////////
	// Magic::addMagicProtection
	////////////////////////////////////////////
	// Function to get additional protection against spells.
	// 0.0001-1 = less protection
	// 1-> = greater protection
	// uses caster=$this->pID, target=$this->targetpID
	// Returns:
	//		A float where 1 is no effect
	////////////////////////////////////////////
	function addMagicProtection() {
		$addMagicProtection = 1;
		if( ( $this->pID != $this->targetpID ) && ($this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_CAST_ON ) != 0 ) ) { // if this isn't cast at self and isn't friendly
			$addMagicProtection *= $this->effectObj->getEffect( $GLOBALS['magicConstants']->ADD_MAGIC_PROTECTION, $this->targetpID );
		}
		return $addMagicProtection;
	}
	
	////////////////////////////////////////////
	// Magic::getSubMana
	////////////////////////////////////////////
	// Function to get the amount of mana to use
	// Returns:
	//		int
	////////////////////////////////////////////
	function getSubMana( $wizards ) {
		$minManaUsed = $this->addManaCost() *	$this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_NEEDED_MANA );										
		$subMana = 	$this->addManaCost() *																								// ( added mana cost *
						( $this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_NEEDED_MANA ) * 	// needed mana *
						( $this->targetProvinceObj->acres * 																			// targetacres ) *
						$this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_NEEDED_WIZARDS ) * 	// ( needed wizards *
						$this->addWizardUse() ) / 																							// added needed wizards /
						( $wizards ) );																										// wizards used )																		
						
		$subMana = ( $subMana < $this->LEAST_AMOUNT_MANA_USED ? $this->LEAST_AMOUNT_MANA_USED : $subMana );		// make sure at least the needed overall mana will be used		
		$subMana = ( $subMana < $minManaUsed ? $minManaUsed : $subMana );		// make sure at least the needed spell mana will be used		
		$subMana = round( $subMana );
		return $subMana;
	}
	
	////////////////////////////////////////////
	// Magic::hasMana
	////////////////////////////////////////////
	// Function to check that the province has enough
	// mana to have even the slightest chance to cast 
	// the spell
	// Returns:
	//		true if ok
	//		false otherwise
	////////////////////////////////////////////
	function hasMana( $wizards = false ) {
		if( $wizards ) {
			$result =  ( ( $this->provinceObj->mana >= $this->getSubMana( $wizards ) )? true : false ) ;
		} else {
			$result = false;
		}
		return $result;
	}
	
	////////////////////////////////////////////
	// Magic::hasResources
	////////////////////////////////////////////
	// Function to check that the province has enough
	// resources to cast the spell
	// Returns:
	//		true if ok
	//		false otherwise
	////////////////////////////////////////////
	function hasResources( $acres=1 ) {
		$additionalResourceUse = $this->addResourceUse();
		if( ( $this->provinceObj->gold >= round( $additionalResourceUse['gold'] * $this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_GOLD_COST ) 	* $acres ) ) && 
			( $this->provinceObj->metal >= round( $additionalResourceUse['metal'] * $this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_METAL_COST ) 	* $acres ) ) &&
			( $this->provinceObj->food >= round( $additionalResourceUse['food'] * $this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_FOOD_COST ) 	* $acres ) ) && 
			( $this->provinceObj->peasants >= round( $additionalResourceUse['peasants'] * $this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_PEASANT_COST ) * $acres ) ) ) {
			return true;
		} else {
			return false;
		}
	}
	
	////////////////////////////////////////////
	// Magic::hasRequirements
	////////////////////////////////////////////
	// Function to check that the province has the required
	// science and buildings to cast this spell
	// Returns:
	//		true if ok
	//		false otherwise
	////////////////////////////////////////////
	function hasRequirements( $sID ) {
		$result = true;
		//Science requirements
		$sciReqArr = $this->spells[$sID]->baseFunction( $GLOBALS['magicConstants']->SCIENCE_REQUIREMENTS );
		//building requirements
		$buildReqArr = $this->spells[$sID]->baseFunction( $GLOBALS['magicConstants']->BUILDING_REQUIREMENTS );
		//race requirements
		$raceReqArr = $this->spells[$sID]->baseFunction( $GLOBALS['magicConstants']->RACE_REQUIREMENTS );		
		//buildings that prevents
		$buildingPrevArr = $this->spells[$sID]->baseFunction( $GLOBALS['magicConstants']->BUILDING_PREVENT );
		//sciences that prevents
		//$sciPrevArr = $this->spells[$sID]->baseFunction( $GLOBALS['magicConstants']->SCIENCE_PREVENT );
		if( $this->spells[$sID]->isKingdomSpell() ) {
			if( $this->provinceObj->getkiID() != $this->targetProvinceObj->getkiID() ) {
				$result = false;
			}
		}
		if( $this->spells[$sID]->isSelfOnly() ) {
			if( $this->provinceObj->getpID() != $this->targetProvinceObj->getpID() ) {
				$result = false;
			}
		}
		$result = ( $result && $this->effectObj->reqsOk( $buildReqArr, $sciReqArr, $raceReqArr, $buildingPrevArr, $this->pID ) );		
		return $result;
	}	
	
	////////////////////////////////////////////
	// Magic::insertSpell
	////////////////////////////////////////////
	// Function to insert a spell into the Spells table
	////////////////////////////////////////////
	function insertSpell( $targetID, $sID, $strength, $ticks, $wizards, $type, $pID=false ) {
		$pID = ( $pID ? $pID : $this->pID );
		$insertSQL = 	"INSERT INTO Spells 
						( casterID, targetID, sID, ticks, strength, wizards, type ) VALUES 
						( $pID, $targetID, $sID, $ticks, $strength, $wizards, $type )";
		$this->db->query( $insertSQL );
	}
	
	////////////////////////////////////////////
	// Magic::deleteSpell
	////////////////////////////////////////////
	// Function to delete a spell from the Spells table
	////////////////////////////////////////////
	function deleteSpell( $spellID ) {
		$deleteSQL = "DELETE FROM Spells WHERE spellID='$spellID'";
		$this->db->query( $deleteSQL );
	}
	
	////////////////////////////////////////////
	// Magic::doTick
	////////////////////////////////////////////
	// Function to make all the spell effects for
	// indirect spells each tick.
	// Also decreases ticks and deletes from the 
	// Spells table as needed.
	////////////////////////////////////////////
	function doTick () {
		$selectSQL = "SELECT DISTINCT targetID FROM Spells WHERE ticks > 0";// get all target pID's
		if( ( $result = $this->db->query( $selectSQL ) ) && $this->db->numRows() ) {
			while( $row = $this->db->fetchArray( $result ) ) {
				$this->setActiveSpells( $row['targetID'] );
				$addGold = 1;												// reset all
				$addMetal = 1;
				$addFood = 1;
				$addPeasants = 1;
				foreach( $this->activeSpells as $spell ) {					// all active spells
					if( ( $spell['targetID'] == $row['targetID'] ) && ( $spell['type'] == 1 ) ) {			// if the spell was cast on the province						
						$sID = $spell['sID'];
						$strength = $spell['strength'];
						$addGold 			*= ( ( $this->spells[$sID]->baseFunction( $GLOBALS['magicConstants']->ADD_GOLD_INCOME ) 		/ 100	* $strength ) + 1 ); 	// add income of the spells
						$addMetal 		*= ( ( $this->spells[$sID]->baseFunction( $GLOBALS['magicConstants']->ADD_METAL_INCOME ) 		/ 100	* $strength ) + 1 );
						$addFood 			*= ( ( $this->spells[$sID]->baseFunction( $GLOBALS['magicConstants']->ADD_FOOD_INCOME )  		/ 100 * $strength ) + 1 );
						$addPeasants 	*= ( ( $this->spells[$sID]->baseFunction( $GLOBALS['magicConstants']->ADD_PEASANT_GROWTH ) 	/ 100 * $strength ) + 1 );					
						// Cheating in some functionality for duration spells with a given effect (not resource or other effect)
						$this->spells[$sID]->spellEffect( $this->db, $spell['casterID'], $spell['targetID'], $spell['wizards'], $strength );
					}
				}
				if( ( $addGold != 1 ) || ( $addMetal != 1 ) || ( $addFood != 1 ) || ( $addPeasants != 1 ) ) {	// only if any should be updated				
					$addGold = $this->makeNumberBetween( $addGold );
					$addPeasants = $this->makeNumberBetween( $addPeasants );
					$addFood = $this->makeNumberBetween( $addFood );
					$addMetal = $this->makeNumberBetween( $addMetal );
					$updateSQL 	= 	"UPDATE Province
									SET incomeChange 	= ( incomeChange*$addGold ),
										metalChange		= ( metalChange*$addMetal ),
										foodChange		= ( foodChange*$addFood ),
										peasantChange	= ( peasantChange*$addPeasants )
									WHERE pID LIKE '".$row['targetID']."'";
					$this->db->query( $updateSQL );
				}
			}
		}
		$updateSQL = "UPDATE Province set mana=LEAST( ( mana+$this->MANA_MODIFIER ), 100)";	
		$this->db->query( $updateSQL );
		$updateSQL = "UPDATE Spells set ticks=( ticks-1 ) WHERE type=1";				// count down ticks
		$this->db->query( $updateSQL );
		$deleteSQL = "DELETE FROM Spells WHERE ticks <= 0 AND type=1";				// military spells might get -1
		$this->db->query( $deleteSQL );
		
	}
	////////////////////////////////////////////
	// Magic::makeNumberBetween
	////////////////////////////////////////////
	// Function to make a number between a max and min value
	// Returns:
	//		A float between 0.0001 and MAX_SPELL_EFFECT
	////////////////////////////////////////////
	function makeNumberBetween( $inNumber, $from=0.0001, $to=false ) {
		if( $to === false ) {
			$to = $this->MAX_SPELL_EFFECT;
		}
		return ( ( $inNumber < $from ) ? $from : ( ( $inNumber > $to ) ? $to : $inNumber ) );
	}
	
	////////////////////////////////////////////
	// Magic::inputOk
	////////////////////////////////////////////
	// Function to make sure all input have correct values
	// and types
	// return 
	//		array with 	'error' = false if all is ok
	//					'error' = true if something was wrong
	//					'ticks' and 'wizards' as numbers
	////////////////////////////////////////////	
	function inputOk( $availableWizards, $type ) {
		$result = array("error" => false,
						"ticks" => false,
						"wizards" => false);		
		// input errors:
		if( isset( $_POST['ticks'] ) ) {
			$result['ticks'] = $_POST['ticks'];
			if( !is_numeric( $result['ticks'] )  ) {
				$result['error'] = true;
				$this->messageToUser .= "<br>".$this->provinceObj->getShortTitle().", You have 
										to enter numeric values for days to maintain the spell.";
			}
			if( $result['ticks'] ) {
				if( ( $result['ticks'] < 1 ) || ( $result['ticks'] > 24 ) ) {
					$result['error'] = true;
					$this->messageToUser .= "<br>".$this->provinceObj->getShortTitle().", You have 
											to enter values between 1 and 24 for days to maintain 
											the spell.";
				}
			}
		}
		if( isset( $_POST['wizards'] ) ) {
			$result['wizards'] = $_POST['wizards'];
			if( !is_numeric( $result['wizards'] ) ) {
				$result['error'] = true;
				$this->messageToUser .= "<br>".$this->provinceObj->getShortTitle().", You have 
										to enter numeric values for number of 
										$this->wizardName.";
			}
			if( $result['wizards'] ) {
				if(	( $result['wizards'] < 1 ) || ( $result['wizards'] > $availableWizards ) )   {
					$result['error'] = true;
					if( $availableWizards ) {
						$this->messageToUser .= "<br>".$this->provinceObj->getShortTitle().", You have 
												to enter values between 1 and $availableWizards for $this->wizardName to use.";
					} else {
						$this->messageToUser .= "<br>".$this->provinceObj->getShortTitle().", You have 
												no $this->wizardName free to use.";
					}
				}
			}
		}			
		if( $type == 2 ) {
			if( !isset( $_POST['chooseDispelSpell'] ) && $this->pID == $this->targetpID ) {
				$result['error'] = true;
				$this->messageToUser .= "<br>".$this->provinceObj->getShortTitle().", You have 
												to choose a spell to remove.";
			}
		}
		
		// Other errors:
		if( !$result['error'] ) {
			if( !$this->hasResources( $this->targetProvinceObj->acres ) ) {
				$this->messageToUser .= "<br>".$this->provinceObj->getShortTitle().", we don't have
										enough resources to cast this spell, and I don't think the old 
										grumpy $this->wizardName would settle for less than they asked for.";
				$result['error'] = true;
			} else if( !$this->hasMana( $result['wizards'] ) ) {
				$this->messageToUser .= "<br>".$this->provinceObj->getAdvisorName().", it would do no good to 
									force the $this->wizardName to try to cast this spell now. They're tired, and need to
									rest for a while to regain their strength. You might try to increase their 
									numbers, but pherhaps it's better to ask them again tomorrow?";
				$result['error'] = true;
			} else if( ( $this->pID == $this->targetpID ) && $this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_CAST_ON ) == 1 ) { // if this spell is cast at own province, and is aggressive
				$this->messageToUser .=	"<br>".$this->provinceObj->getShortTitle().", are You mad? You cannot think to 
							cast this spell at Your own glorious Province? That would be madness! 
							Besides, the old $this->wizardName would never agree to do such a thing.";
				$result['error'] = true;
			} else if( 	( $this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_CAST_ON ) == 1 ) && 
						!( $this->pID == $this->targetpID ) &&							// if not own province, and aggressive spell
							$this->targetProvinceObj->isProtected() ) {		 				// and the target is protected
							$this->messageToUser .=	"<br>".$this->provinceObj->getAdvisorName().", this province is so new, it still has 
										it's magic protecting shield intact. I suggest that we try again as soon 
										as the newbie-shield has been removed!";
							$result['error'] = true;
			} else if( 	( $this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_CAST_ON ) == 1 ) && 
						!( $this->pID == $this->targetpID ) &&							// if not own province, and aggressive spell
							$this->provinceObj->isProtected() ) {		 				// and the target is protected
							$this->messageToUser .=	"<br>".$this->provinceObj->getAdvisorName().", we cant cast offensive spells while we are in protection!"; 
							$result['error'] = true;
			} else if( ! $this->hasRequirements( $this->sID ) ) {
				$this->messageToUser .=	"<br><br><b>".$this->provinceObj->getAdvisorName().", you're not allowed to use this magic (".$this->spells[$this->sID]->getName().") on this province.</b><br><br>";								
				$result['error'] = true;
			} 
		}
		return $result;
	}

	////////////////////////////////////////////
	// Magic::managedSpellCasting
	////////////////////////////////////////////
	// Function to calculate the chance for success and 
	// with some random factor decide whether the 
	// casting was successful or not
	// returns
	//		array with 	'success' = true if success
	// 					'success' = false if failure
	//					'subMana - the amount of mana used
	////////////////////////////////////////////
	function managedSpellCasting( $wizards, $wizardsPrAcre, $ticks ) {
		$result = array("success" => false,
						"subMana" => 0);
		
		
		// target info
		$targetAcres = $this->targetProvinceObj->acres;
		$targetWizards = $this->targetWizNum;
		$targetWizardsPrAcre = ( $targetWizards / $targetAcres );
				
		$result['subMana'] = $this->getSubMana( $wizards );
		mt_srand( $this->spells[$this->sID]->makeSeed() );
		$addedChance = $this->addChanceToCast(); // less chance = 0.0001-1
		$targetAddedProtection = $this->addMagicProtection(); // less protection = 0.0001-1
		
		// Users mana / 90 vs random 1-100 mana (small is good)
		$randomMana = round( mt_rand( 1,100 ) * $targetAddedProtection / $addedChance  );
		
		// Choosen days vs random 1-72 days (big is good)
		$randomDays = round( mt_rand( 2, 72 ) * $addedChance / $targetAddedProtection );
		
		// random 1 - target wpa vs random 1 - caster wpa
		if( $this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_CAST_ON ) != 1 ) {
			$randomTargetWizardsPrAcre = $randomWizardsPrAcre = 0;
		} else {
			$randomTargetWizardsPrAcre = mt_rand( 1, round( $targetWizardsPrAcre*1000*$targetAddedProtection ) ); // 1 - twpa*targetAddedProtection
			$randomWizardsPrAcre = mt_rand( 1 , round( $wizardsPrAcre*1000*$addedChance*3 ) ); //1 - wpa*addedChance 
		}
		
		// Used wizards vs random 1 - needed wizards (random small is good)
		$neededWizards = 	round( $this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_NEEDED_WIZARDS ) *
								$targetAcres * $this->addWizardUse());

		$randomNeededWizards = mt_rand( 1 , round( $neededWizards / $addedChance ) ); 
		
		//echo "<br>RWPA: ".$randomWizardsPrAcre." vs. RTWPA: ".$randomTargetWizardsPrAcre;
		//echo "<br>WPA: ".round( $wizardsPrAcre*1000)." vs. TWPA: ".round( $targetWizardsPrAcre*1000 );
		if( 	( $randomMana <= 95 ) && 								// should pass 95/100 times
				( $randomMana <= $this->provinceObj->mana ) &&  // should pass ( province->mana/100 ) times
				( $randomDays > $ticks ) &&							// should pass 47/48 times for 1 tick, 46/48 for 2 etc. -> 24/48 for 24 ticks
				( $randomWizardsPrAcre >= $randomTargetWizardsPrAcre ) && // should pass wpa / Twpa times
				( $randomNeededWizards <= $wizards ) ) {			// should pass $wizards/$neededWizards times	


			if ($this->provinceObj->OnMagicAction() == false)
			{
				if ($this->provinceObj->isOverPopulated() == true)
				{
					$this->messageToUser .=	"<br><br><b>".$this->provinceObj->getAdvisorName().", the peasants are rioting due to overpopulation.
										Our wizards refuse to cast spells while our province is in this shape!";				
				}
				else
				{
					$this->messageToUser .=	"<br><br><b>".$this->provinceObj->getAdvisorName().", The spell failed due to a trigger in our province!";				
				}
				$result['error'] = true;
			}
			else
			{
				$result['success'] = true;
			}
		}
		
		$this->logdata = $result;
		$this->logdata['addedChance'] = $addedChance;
		$this->logdata['magicProt'] = $targetAddedProtection;
		$this->logdata['randomManaCalc'] = "round( mt_rand( 1,100 ) * $targetAddedProtection / $addedChance  )";
		$this->logdata['randomMana'] = $randomMana;
		$this->logdata['randomDaysCalc'] = "round( mt_rand( 2, 72 ) * $addedChance / $targetAddedProtection )";
		$this->logdata['randomDays'] = $randomDays;
		$this->logdata['randomNeededWizzies'] = $randomNeededWizards;
		$this->logdata['neededWizzies'] = $neededWizards;
		$this->logdata['actualWizardsUSED'] = $wizards;
		$this->logdata['duration'] = $ticks;
		
		return $result;			
	}

	////////////////////////////////////////////
	// Magic::spellCastFailure
	////////////////////////////////////////////
	// Function to send messages and do everything
	// that should be done when a spell fails at 
	// casting time.
	////////////////////////////////////////////
	function spellCastFailure( $wizards, $targetWizards, $subMana ) {
		mt_srand( $this->spells[$this->sID]->makeSeed() );
		$randomLoose = mt_rand( 1, 3 );
		$deadWizards = "";
		$targetWizardName = $this->targetWizName;
		if( $randomLoose == 1 ) {
			$lost = mt_rand( 1, round( ( $wizards / $this->MAX_LOST ) ) );
			$this->provinceObj->milObject->killUnits($this->provinceObj->milObject->MilitaryConst->WIZARDS, $lost);
			$deadWizards = " ".$this->provinceObj->getShortTitle().", $lost of the $this->wizardName even died in the attempt!";
			if( $this->targetpID != $this->pID ) {
				if( $targetWizards ) {
					if( $this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_CAST_ON ) == 0 ) {
						$this->targetProvinceObj->postNews( $this->targetProvinceObj->getAdvisorName().
																", the province of <i>".$this->provinceObj->provinceName." (#".
																$this->provinceObj->getkiID().")</i> 
																tried to cast a spell, the $targetWizardName say it was a 
																<i>".$this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_NAME ).
																"</i> spell, on our province! Unfortunately, something seemed to go terribly 
																wrong when they tried to cast it." );
					} else {
						$this->targetProvinceObj->postNews( $this->targetProvinceObj->getAdvisorName().
																", the province of <i>".$this->provinceObj->provinceName." (#".
																$this->provinceObj->getkiID().")</i> 
																tried to cast a spell, the $targetWizardName say it was a 
																<i>".$this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_NAME ).
																"</i> spell, on our province! Fortunately, the $targetWizardName managed to 
																stop it, and they even managed to kill $lost of the other 
																$targetWizardName, but they had to work hard." );
						$targetSubMana = round( $subMana / $this->TARGET_MANA_LOST );
						$targetSubMana = ( $this->targetProvinceObj->mana > $targetSubMana ? 
												$targetSubMana 
												: 
												$this->targetProvinceObj->mana );
						$this->targetProvinceObj->useMana( $targetSubMana );
					}					
				} else {
					$this->targetProvinceObj->postNews( $this->targetProvinceObj->getAdvisorName().
														", someone tried to cast a spell on our province! 
														Fortunately, something seemed to go wrong. If we 
														had our own $targetWizardName, they could probably 
														tell you which province that cast the spell and 
														which spell it was." );
				}
			}
		}
		$this->messageToUser .= "<br>".$this->provinceObj->getAdvisorName().", our $this->wizardName have
								failed in casting <i>".$this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_NAME ).
								"</i>. $deadWizards";
	}

	////////////////////////////////////////////
	// Magic::castSpell
	////////////////////////////////////////////
	// Function to cast the spell. 
	// Makes a message to the user if anything goes wrong.
	////////////////////////////////////////////
	function castSpell( $type, $ticks, $wizards, $strength ) {
		$targetWizName = $this->targetWizName;		
		$targetWizNum = $this->targetWizNum;
		if( $type == 3 ) {				// if it's a triggered spell
			$this->insertSpell( $this->targetpID, $this->sID, $strength, $ticks, $wizards, $type, $pID=false );
			$this->messageToUser .= "<br>".$this->provinceObj->getAdvisorName().", your $this->wizardName have
									succeeded in casting <i>".$this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_NAME ).
									"</i> at ".
									( $this->pID == $this->targetpID? "Your own province"
									: "the province of <i>".$this->targetProvinceObj->provinceName."</i>"). 
									". This spell will remain until You remove it".
									( $this->pID == $this->targetpID? "."
									: " or <i>".$this->targetProvinceObj->provinceName."</i> dispels it.");
			if( $this->targetpID != $this->pID ) {		// don't post news if cast on self
				if( $targetWizNum ) {	//if the opposition has any wizards
					if( $this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_CAST_ON ) == 0 ) {
						$this->targetProvinceObj->postNews( $this->targetProvinceObj->getAdvisorName().
															", someone has cast a friendly triggered spell on our province! The $targetWizName 
															are	working to find out which spell it was. Pherhaps You should 
															go to Your magic advisor and see what they've found?" );			
					}else {
						$this->targetProvinceObj->postNews( $this->targetProvinceObj->getAdvisorName().
															", someone has cast a spell on our province! The $targetWizName 
															tried to stop it, but they couldn't! They didn't see any effect from it, though, 
															but they think it might have been a spell which will be triggered by some event 
															in our province. The only way to find out what kind of spell this was is to try 
															to dispel it." );
					}
				} else {
					if( $this->spells[$this->sID]->castOn == 0 ) {
						$this->targetProvinceObj->postNews( $this->targetProvinceObj->getAdvisorName().
															", someone has cast a friendly spell on our province! May I suggest 
															that You start training $targetWizName to find out what kind of spell 
															it was." );
					
					} else {
						$this->targetProvinceObj->postNews( $this->targetProvinceObj->getAdvisorName().
															", someone has cast a spell on our province! Unfortunately 
															we didn't have any $targetWizName to stop it. May I suggest
															that You start training $targetWizName to find out what kind of spell 
															it was." );
					}
				}
			}
		} else if( $type == 2 ) {		// if it's a dispell spell
			if( $this->pID == $this->targetpID ) {
				$this->messageToUser .= $this->spells[$this->sID]->spellEffect( $this->db, $this->provinceObj, $this->targetProvinceObj, $wizards, $_POST['dispelSpell'] );
			} else {
				$this->messageToUser .= $this->spells[$this->sID]->spellEffect( $this->db, $this->provinceObj, $this->targetProvinceObj, $wizards, $strength );
			}				
			$this->setActiveSpells();			
				
		} else if( $type == 1 ) {	// if it's an indirect spell			
			$this->insertSpell( $this->targetpID, $this->sID, $strength, $ticks, $wizards, $type, $pID=false );
			$this->messageToUser .= "<br>".$this->provinceObj->getAdvisorName().", your $this->wizardName have
									succeeded in casting <i>".$this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_NAME ).
									"</i> at ".
									( $this->pID == $this->targetpID? "Your own province"
									: "the province of <i>".$this->targetProvinceObj->provinceName."</i>"). 
									", and they will maintain the spell for $ticks day".
									( $ticks > 1? "s." : "." );
			if( $this->targetpID != $this->pID ) {		// don't post news if cast on self
				if( $targetWizNum ) {	//if the opposition has any wizards
					if( $this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_CAST_ON ) == 0 ) {
						$this->targetProvinceObj->postNews( $this->targetProvinceObj->getAdvisorName().
															", someone has cast a friendly spell on our province! The $targetWizName 
															are	working to find out which spell it was. Pherhaps You should 
															go to Your magic advisor and see what they've found?" );			
					}else {
						$this->targetProvinceObj->postNews( $this->targetProvinceObj->getAdvisorName().
															", someone has cast a spell on our province! The $targetWizName 
															tried to stop it, but they couldn't, so right now they're 
															working to find out which spell it was. Pherhaps You should 
															go to Your magic advisor and see what they've found?" );
					}
				} else {
					if( $this->spells[$this->sID]->castOn == 0 ) {
						$this->targetProvinceObj->postNews( $this->targetProvinceObj->getAdvisorName().
															", someone has cast a friendly spell on our province! May I suggest 
															that You start training $targetWizName to find out what kind of spell 
															it was." );
					
					} else {
						$this->targetProvinceObj->postNews( $this->targetProvinceObj->getAdvisorName().
															", someone has cast a spell on our province! Unfortunately 
															we didn't have any $targetWizName to stop it. May I suggest
															that You start training $targetWizName to find out what kind of spell 
															it was." );
					}
				}
			}			
		} else if( $type == 0 ) {	// if it's a direct spell
			$this->spells[$this->sID]->spellEffect( $this->db, $this->provinceObj, $this->targetProvinceObj, $wizards, $strength );
			$this->messageToUser .= "<br>".$this->provinceObj->getAdvisorName().", our $this->wizardName have
									succeeded in casting <i>".$this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_NAME ).
									"</i> at ".( $this->pID == $this->targetpID? 
									"our own province.":"the province of <i>".$this->targetProvinceObj->provinceName."</i>.").
									" I made a document with a small description of the results and put it together 
									with all Your other news in Your room, ".$this->provinceObj->getShortTitle();
		} else {	// THIS SHOULD NEVER HAPPEN
			echo 	"There's a type-error in the ".$this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_NAME ).
					" spell. Contact admin.";
		}
				//CREATE DESC!!!

		
	}

	////////////////////////////////////////////
	// Magic::triggerSpellEffect
	////////////////////////////////////////////
	// Function to trigger a spell effect. The trigger TYPEs
	// are defined as constants in the MagicConstants class
	// Returns:
	//		true or false
	// 		ex: true if it's a SPELL_CAST trigger and the spell should
	// 			be allowed to be cast.
	//			false if it shouldn't be allowed to be cast.
	////////////////////////////////////////////
	function triggerSpellEffect( $TRIGGER_TYPE, $pID, $dummyArray=false ) {
		$result = true;
		$selectSQL = 	"SELECT sID, casterID, targetID, wizards, spellID, strength 
						FROM Spells 
						WHERE targetID LIKE '$pID' 
						AND type=3";
		if( ($result = $this->db->query( $selectSQL ) ) && $this->db->numRows( $result ) ) {
			while( $row = $this->db->fetchArray( $result ) ) {
				$sID = $row['sID'];
				if( $this->spells[$sID]->isTriggerType( $TRIGGER_TYPE ) ) {
					$this->spells[$sID]->triggerEffect( $this->db, $sID, $row['casterID'], $row['targetID'], $row['wizards'], $row['spellID'], $row['strength'] );
				}
			}
		}
		return $result;
	}
	
	////////////////////////////////////////////
	// Magic::useMagic
	////////////////////////////////////////////
	// Function to cast the spell. Checks the user input,
	// that the province has all resources / requirements 
	// needed. Then tries to cast the spell with at least 
	// 10% failure rate.
	// Makes a message to the user if anything goes wrong.
	////////////////////////////////////////////
	function useMagic() {
		$this->sID = $_POST['sID'];
		$targetpID = $_POST['useMagicOnID'];
		$updateSpell = false;				// hack, if we cast spell with no effect, update # of days it is active.
		$hackSpellID= -1;
		$isEvil = false;
		$isSucc = false;
		// Bad hack to disable magic on dead provinces
		// anders
		$hack_province = new Province($targetpID,$this->db);
		$hack_province->getProvinceData();
		if ($hack_province->isAlive() == false)
		{
			$this->messageToUser .= "<br>The province is dead.";
			return;
		}
		// hack to only allow $this->MAX_SPELL_STACK on a single province.
		//
		if ( $this->spells[$this->sID]->getMaxStack() > 0)
		{
			$this->db->query("select SpellID from Spells WHERE targetID=$targetpID AND sID=".$this->sID."");
			if ($this->db->numRows() >= $this->spells[$this->sID]->getMaxStack())
			{
				$this->messageToUser .= "<br>Casting this spell on this province will minimal effect at this moment!  We can try to keep the spell in place for a while longer.";
				return;
			}
		} // if it has stack limit

		// spellInfo
		$type = $this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_TYPE );
		
		// caster info
		$totalWizards =  $this->wizards;
		$usedWizards = $this->getUsedWizards();			
		$availableWizards = ( $totalWizards- $usedWizards );
		$wizardsPrAcre = ( $totalWizards / $this->provinceObj->acres );
		
		$variables = $this->inputOK( $availableWizards, $type);
		if( !$variables['error'] ) {						// NO ERRORS, TRY TO CAST SPELL
			$ticks = $variables['ticks'];
			$wizards = $variables['wizards'];		
			
			$variables = $this->managedSpellCasting( $wizards, $wizardsPrAcre, $ticks );
			$this->doStats( $variables['success'] );
			$subMana = $variables['subMana'];
			if( $variables['success'] ) {			// MANAGED TO CAST SPELL
				$strength = $this->getStrength();
				if( $this->triggerSpellEffect( $GLOBALS['magicConstants']->TRIGGER_SPELL_CAST, $this->pID ) ) {
					if ($updateSpell == false)
						$this->castSpell( $type, $ticks, $wizards, $strength );
					else
					{
						$this->db->query("UPDATE Spells set ticks=GREATEST((ticks+1),$ticks+1) WHERE spellID=$hackSpellID");
						$this->messageToUser .="<br>Our wizards succeded in enchanting the spell!";
					}
					$isSucc = true;
					
				} else {
					$this->messageToUser .= "<br>".$this->provinceObj->getAdvisorName().", the 
											$this->wizardName was interrupted by a triggered spell and 
											couldn't cast the spell right! They are working to figure out 
											what happened, and they said they'll put a note about it in 
											Your room.";
				}				
			}else {									// COULDN'T CAST SPELL
				$this->triggerSpellEffect( $GLOBALS['magicConstants']->TRIGGER_SPELL_FAILURE, $this->pID );
				$this->spellCastFailure( $wizards, $this->targetWizNum, $subMana );
			}
			$addResourceCost = $this->addResourceUse();
			$this->provinceObj->useResource(	round( $this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_GOLD_COST ) * $this->targetProvinceObj->acres * $addResourceCost['gold'] ),
														round( $this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_METAL_COST ) * $this->targetProvinceObj->acres * $addResourceCost['metal'] ),
														round( $this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_FOOD_COST ) * $this->targetProvinceObj->acres * $addResourceCost['food'] ) );
			$this->provinceObj->usePeasants( round( $this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_PEASANT_COST ) * $this->targetProvinceObj->acres * $addResourceCost['peasants'] ) );
			// OBS!! TODO!!! ADD PEASANT COST!!!!
			//$peasantCost = round( $this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_PEASANT_COST ) * $this->targetProvinceObj->acres * $addResourceCost['peasant'] );
			$subMana = round( ( ( $this->provinceObj->mana >= $subMana )? $subMana : $this->provinceObj->mana ) );
			$this->provinceObj->useMana( $subMana );
			$this->update();
		}

		//Log spellcasting to actionlog...
		$spellSuccess=0;
		$descTXT = "";
		if($variables['success']) {
			$spellSuccess=1;
			$descTXT .= "<br>success: true";
		}
		else {
			$descTXT .= "<br>success: false";
		}
		
		//$this->logdata['descTXT'] = $descTXT;
		$dedata = NULL;
		while($dedata = next($this->logdata)) {
			$keydata = key($this->logdata);
			$descTXT .= "<br>".$keydata.": ".$dedata;
		}
		
		
		$actionLogger = new ActionLogger($this->db);
		$actionLogger->log($actionLogger->MAGIC,$this->provinceObj->pID,$this->sID, $targetpID, $spellSuccess, $descTXT);

		if( ( $this->pID != $this->targetpID ) && ($this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_CAST_ON ) != 0 ) && ($isSucc == true)) { // if this isn't cast at self and isn't friendly
			$trigEff = new TrigEffect($this->db);
			$trigEff->triggEffect(1, $this->targetpID);
			//echo "EVIL SUCCESS";
		}
	}	



	////////////////////////////////////////////
	// Magic::doStats
	////////////////////////////////////////////
	// Function to create statistics depending on 
	// the success or failure in casting spells
	////////////////////////////////////////////
	function doStats( $castingResult ) {
	  $difficulty=0;
	  $difficulty=round($this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_NEEDED_WIZARDS )*3);
	  if( !$castingResult ) {//failed in casting a spell
	    $difficulty *= -1;
	  }
	  $sql = "update Province set magicRep=GREATEST( 0, ( magicRep+($difficulty) ) ) where pID='".$this->pID."'";
	  $this->db->query( $sql );
	}






/////////////////////////////////////////////////////////////////////////////////////////
//								DISPLAY FUNCTIONS																//
/////////////////////////////////////////////////////////////////////////////////////////


	////////////////////////////////////////////
	// Magic::displaySpell
	////////////////////////////////////////////
	// Function to get the html to display specific
	// info for casting this spell at the choosen 
	// province
	// Returns:
	//		String with html
	////////////////////////////////////////////
	function displaySpell() {
		$targetAcres = $this->targetProvinceObj->acres;
		$addResourceCost = $this->addResourceUse();
		//echo "<br>awu: ".$this->addWizardUse();
		$goldCost 	= round( $this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_GOLD_COST ) * $targetAcres * $addResourceCost['gold'] );
		$metalCost 	= round( $this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_METAL_COST ) * $targetAcres * $addResourceCost['metal'] );
		$foodCost = round( $this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_FOOD_COST ) * $targetAcres * $addResourceCost['food'] );
		$peasantCost = round( $this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_PEASANT_COST ) * $targetAcres * $addResourceCost['peasants'] );
		$recWiz 	= round( $this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_NEEDED_WIZARDS ) * $targetAcres * $this->addWizardUse() );		
		$strength = round( $this->getStrength() * 100 );
		$html = "";
		$html .= $this->provinceObj->getAdvisorName().", the $this->wizardName say they'll need ";
		$html .= ( $goldCost ? "$goldCost units of gold " : "" );
		$html .= ( $goldCost && $metalCost ? "and " : "" );
		$html .= ( $metalCost ? "$metalCost units of metal " : "" );
		$html .= ( ( $goldCost || $metalCost ) && $foodCost ? "and " : "" );
		$html .= ( $foodCost ? "$foodCost units of food " : "" );
		$html .= ( ( $goldCost || $metalCost || $foodCost ) && $peasantCost ? "and " : "" );
		$html .= ( $peasantCost ? "$peasantCost peasants " : "" );
		$html .= "to cast <i>".$this->spells[ $this->sID ]->getName()."</i> at ";
		$html .= ( $this->targetpID == $this->pID ? 
					"<i>yourself" : "the province of <i>".$this->targetProvinceObj->provinceName );
		$html .= "</i>. They also say they recommend using no less than $recWiz $this->wizardName for this task and that
					you will be able to cast the spell with $strength% strength.";
		return $html;
	}
	
	////////////////////////////////////////////
	// Magic::displayUseMagic
	////////////////////////////////////////////
	// Function to get the html code for displaying 
	// the form for letting the user cast magic
	// Returns:
	//		String with html form
	////////////////////////////////////////////
	function displayUseMagic() {
		$usedWizards = $this->getUsedWizards();
		$type = $this->spells[$this->sID]->baseFunction( $GLOBALS['magicConstants']->GET_TYPE );
		$availableWizards = ( $this->wizards - $usedWizards );
		if ($availableWizards < 0)
			$availableWizards = 0;
		$ticks = 12;
		if( $this->faultyInput ) {			
			$availableWizards = ( isset( $_POST['wizards'] ) ? $_POST['wizards'] : 0 );
			$ticks = ( isset( $_POST['ticks'] ) ? $_POST['ticks'] : 0 );
		}
		$html = "";
		$html .= "<form action='".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."' method='POST' name='useMagic'>";
		$html .= $GLOBALS['fcid_post'];
		$html .= 	"Use <input type='textfield' name='wizards' class='form' value='$availableWizards' maxlength='7'
						 size='6' title='Enter the number of $this->wizardName you want to send here'>
					of $availableWizards available $this->wizardName";
		if( $type == 2 ) {								// dispell spell
			$html .= $this->chooseDispelSpell().".&nbsp;";
		} else if( $type == 1 ) {						// indirect spella
			$html .= " for <input type='textfield' name='ticks' class='form' value='$ticks' size='2' maxlength='2'
					title='Enter the number of days you want the $this->wizardName to maintain the 
					spell (max 24)'> days. &nbsp;" ;
					
		} else if( ( $type == 0 ) || ( $type == 3 ) ) {	// direct or triggered spells
			$html .= ".&nbsp;";
		}

		$html .= "<input type='hidden' name='sID' value='$this->sID'>";
		$html .= "<input type='hidden' name='useMagicOnID' value='$this->targetpID'>";
		$html .= "<input type='submit' name='useMagic' class='form' value='Cast spell' title='Cast the selected spell'>";
		$html .= "</form>";
		return $html;
	}
	
	////////////////////////////////////////////
	// Magic::displayActiveSpells
	////////////////////////////////////////////
	// Function to get the html code for displaying 
	// the active spells cast by / on the province
	// Returns:
	//		String with html
	////////////////////////////////////////////
	function displayActiveSpells() {
		$ownSpellsHtml = "";
		$goodSpellsHtml = "";
		$othersSpellsHtml = "";
		foreach( $this->activeSpells as $spell ) {
			$sID = $spell['sID'];
			$strength = round( $spell['strength'] * 100 );
			$type = $this->spells[$sID]->baseFunction( $GLOBALS['magicConstants']->GET_TYPE );
			if( $spell['casterID'] == $this->pID ) {		// if this province cast the spell
				if( $type == 3 ) {
					$ownSpellsHtml .= 	"<tr><td>Triggered spell: ".	
										$this->spells[$sID]->baseFunction( $GLOBALS['magicConstants']->GET_NAME ).
										" which was cast ";
				}else {
					$ownSpellsHtml .= 	"<tr><td>".	
										$this->spells[$sID]->baseFunction( $GLOBALS['magicConstants']->GET_NAME ).
										" (".$spell['ticks']." day".($spell['ticks']>1?"s":"")." left) 
										which was cast ";
				}
				if( $spell['targetID'] == $this->pID ) {
					$ownSpellsHtml .= "at Your own province ";
				}else {
					$ownSpellsHtml .= 	"at the province of <i>".$spell['provinceName']."(#".$spell['tkiID'].")</i> ";
				}
				$ownSpellsHtml .= "by ".$spell['wizards']." $this->wizardName ($strength%).";
				$ownSpellsHtml .=	"</td><td><a href='".$_SERVER['PHP_SELF']."?spID=".$spell['spellID']."
									&delete=ok' title='Click to stop maintaining this spell' target='_self'>
									<font size='-2'>STOP this spell</font>
									</a></td></tr>";
			} else if( $this->spells[$sID]->baseFunction( $GLOBALS['magicConstants']->GET_CAST_ON ) == 0 ) {	// other province cast nice spell at this province
				if( $type != 3 ) {
					$goodSpellsHtml .= 	"<br>".$this->spells[$sID]->baseFunction( $GLOBALS['magicConstants']->GET_NAME ).
										" (".$spell['ticks']." day".($spell['ticks']>1?"s":"")." left) cast by the province of <i>".
										$spell['casterProvince']."(#".$spell['ckiID'].")</i>";	
				} else {
					$goodSpellsHtml .= 	"<br>Triggered spell: ".$this->spells[$sID]->baseFunction( $GLOBALS['magicConstants']->GET_NAME ).
										" cast by the province of <i>".$spell['casterProvince']."(#".$spell['ckiID'].")</i>";	
				}
			} else {										// other province cast aggressive spell at this province
				if( $type != 3 ) {
					$othersSpellsHtml .=	"<br>".$this->spells[$sID]->baseFunction( $GLOBALS['magicConstants']->GET_NAME ).
											" (".$spell['ticks']." day".($spell['ticks']>1?"s":"")." left)";
				} else {
					$othersSpellsHtml .=	"<br>Triggered spell ( UNKNOWN )";
				}
			}
		}
		if( strlen( $ownSpellsHtml ) ) {
			$ownSpellsHtml = 	"<table width='100%' align=center border='0' cellpadding='0' cellspacing='0'><tr><td colspan='2'>".
								$this->provinceObj->getAdvisorName().
								", Your $this->wizardName are currently maintaining the following spells: </td></tr>".
								$ownSpellsHtml."</table>";
		} else {
			$ownSpellsHtml =	$this->provinceObj->getAdvisorName().
								", our $this->wizardName are currently not maintaining any spell at all. May
								I suggest that we at least cast something at our own grand province to
								gain more food or some protection against our evil enemies? Of course, 
								".$this->provinceObj->getAdvisorName().", I think the best would be to 
								attack our enemies right away, but pherhaps You have some other plans?";
		}
		if( strlen( $goodSpellsHtml ) ) {
			$goodSpellsHtml = 	"<br>".$this->provinceObj->getShortTitle().", there's been cast some 
								friendly spells at our province! I've made a list of them here for You: ".
								$goodSpellsHtml;
		}
		if( strlen( $othersSpellsHtml ) ) {
			if( $this->wizards ) {
				$othersSpellsHtml = "<br>".$this->provinceObj->getShortTitle().", for some reason it seems as 
								someone has cast some spells at us! Our $this->wizardName felt their magic, 
								but it was too strong to be stopped. At least, the old $this->wizardName have
								found out which spells were cast: ".
								$othersSpellsHtml;
			} else {
				$othersSpellsHtml = "<br>".$this->provinceObj->getShortTitle().", for some reason it seems as 
								someone has cast some spells at us! Unfortunately, we don't have any 
								$this->wizardName, so we can't find out what kind of spell it is.";
			}
		} 		
		$html = "";
		$html .= "<table width='75%' border='0'><tr><td>$ownSpellsHtml</td></tr></table>";
		$html .= "<table width='75%' border='0'><tr><td>$goodSpellsHtml</td></tr></table>";
		$html .= "<table width='75%' border='0'><tr><td>$othersSpellsHtml</td></tr></table>";
		return $html;
	}
		
	////////////////////////////////////////////
	// Magic::chooseKingdom
	////////////////////////////////////////////
	// Function to get the html code for displaying 
	// a form to let the user choose a kingdom from a list
	// Returns:
	//		String with html from
	////////////////////////////////////////////
	function chooseKingdom() { 
		$html =	"Enter kingdom #</td><td nowrap>
				<form name='chooseKingdom#' method='GET' action='".$_SERVER['PHP_SELF']."'>";
		$html .= $GLOBALS['fcid_post'];
		$html .= "
				<input type='textfield' size='4' class='form' value='$this->kiID' name='kiID' title='Enter a kingdom number'>
				<input type='hidden' name='sID' value='".$this->sID."'>
				<input type='Submit' value='ok' class='form' title='Click to choose kingdom'>
				</form>
				</td></tr><tr><td nowrap>or choose from list &nbsp;</td><td nowrap>
				<form name='chooseKingdom' method='GET' action='".$_SERVER['PHP_SELF']."'>";
		$html .= $GLOBALS['fcid_post'];
		$html .= "
					<select name='kiID' class='form'>";
		if( $kingdoms = $this->getKingdoms() ) {
			while( $row = $this->db->fetchArray( $kingdoms ) ) {
				$html .= 	"<option 
							".( ( $this->kiID == $row['kiID'] )? "selected='true'" : "" )."
							value='".$row['kiID']."'>".$row['name']."
							</option>\n";
			}
		}
		$html .= 	"</select>
					<input type='hidden' name='sID' value='".$this->sID."'>
					<input type='Submit' value='ok' class='form' title='Click to choose kingdom'>
					</form>";
		return $html;
	}
	
	////////////////////////////////////////////
	// Magic::chooseProvinceSpell
	////////////////////////////////////////////
	// Function to get the html code for displaying 
	// a form to let the user choose a province and a spell
	// Returns:
	//		String with html from
	////////////////////////////////////////////
	function chooseProvinceSpell() {	
		$html = 	"Choose province </td><td nowrap>
					<form name=chooseProvinceSpell method='GET' action='".$_SERVER['PHP_SELF']."'>";
		$html .= $GLOBALS['fcid_post'];
		$html .= "
					<select name='targetpID' class='form'>";
		if( $provinces = $this->getProvinces( $this->kiID ) ) {
			while( $row = $this->db->fetchArray( $provinces ) ) {
				$html .= 	"<option 
							".( ( $this->targetpID == $row['pID'] )? "selected='true'" : "" )."
							value='".$row['pID']."'>"
							.$row['provinceName']."</option>\n";
			}
		}
		$html .= 	"</select></td></tr>";
		
		$html .= 	"<tr><td nowrap>and choose spell &nbsp;</td><td nowrap>
					<select name='sID' class='form'>";
		foreach( $this->spells as $spell ) {
			$sID = $spell->baseFunction( $GLOBALS['magicConstants']->GET_ID );
			if( $this->hasRequirements( $sID ) ) {
				$html .= 	"<option 
							".( ( $this->sID == $sID )? "selected='true'" : "" )."
							value='".$sID."'>"
							.$spell->baseFunction( $GLOBALS['magicConstants']->GET_NAME )."</option>\n";
			}
		}
		$html .= 	"</select>
					<input type='hidden' name='kiID' value='".$this->kiID."'>
					<input type='Submit' value='ok' class='form' title='click to choose province and spell'>
					</form>";
		return $html;
	}
	
	////////////////////////////////////////////
	// Magic::chooseDispelSpell
	////////////////////////////////////////////
	// Function to get the html code for displaying 
	// a form to let the user choose a spell to remove
	// Returns:
	//		String with html from
	////////////////////////////////////////////
	function chooseDispelSpell() {
		$qs = preg_replace( "(spellID=[0-9]*&)", "", $_SERVER['QUERY_STRING'] );
		$spellID = ( isset( $_GET['spellID'] ) ? $_GET['spellID'] : $this->getDispelSpell() );
		$html = "";
		if( $this->pID == $this->targetpID ) { 
			$html	=	"<input type='hidden' name='dispelSpell' value='$spellID'> 
						to remove <select name=chooseDispelSpell onChange=\"MM_jumpMenu('self',this,0)\">";
			foreach( $this->activeSpells as $spell ) {
				$sID = $spell['sID'];
				if( ( $spell['targetID'] == $this->pID ) && ( $this->spells[$sID]->baseFunction( $GLOBALS['magicConstants']->GET_CAST_ON ) == 1 ) ) {
					if( $this->spells[$sID]->baseFunction( $GLOBALS['magicConstants']->GET_TYPE ) != 3 ) {
						$html .= 	"<option 
									".( ( $spell['spellID'] == $spellID )? "selected='true'" : "" ). 
									" value='".$_SERVER['PHP_SELF']."?spellID=".$spell['spellID']."&$qs'>"
									.$this->spells[ $spell['sID'] ]->getName().
									"( ".$spell['ticks']." day".( $spell['ticks'] > 1 ? "s" : "" )." left )
									</option>";
					} else {
						$html .= 	"<option 
									".( ( $spell['spellID'] == $spellID )? "selected='true'" : "" ). 
									" value='".$_SERVER['PHP_SELF']."?spellID=".$spell['spellID']."&$qs'>
									Triggered spell ( UNKNOWN )
									</option>";
					}
				}
			}
			$html .= 	"</select>";
		}		
		return $html;
	}
	
	////////////////////////////////////////////
	// Magic::getDisplay
	////////////////////////////////////////////
	// Function to get the htmlpage as a string
	// Returns:
	//    String with html
	////////////////////////////////////////////
	function getDisplay() {
		$result = "";
		$result .= $this->getJavaScript();
		$result .= "<br><table width='75%' cols='2' align='center' border='0' cellpadding='2' cellspacing=1'>";
		$result .= "<tr><td nowrap>
						<table width='100%' cols='2' align='center' border='0' cellpadding='2' cellspacing='0'>
							<tr><td nowrap>".$this->chooseKingdom()."</td></tr>
							<tr><td nowrap>".$this->chooseProvinceSpell()."</td></tr>
						</table>
					</td>";
		$result .= "<td align='center'>
						<table width='100%' cols='2' align='center' border='0' cellpadding='2' cellspacing='0'>
							<tr><td align='center'>Your mana percentage: ".$this->provinceObj->mana."%</td></tr>
							<td align='center'>".$this->displaySpell()."</td></tr>
						</table>";
		$result .= "<tr><td colspan='2' align='center'><br>".$this->messageToUser."</td></tr>";
		$result .= "<tr><td colspan='2' align='center'>".$this->displayUseMagic()."</td></tr>";
		$result .= "</table>";
				
		$result .= "<br><table width='75%' align='center' border='0' cellpadding='0' cellspacing=1'>";
		$result .= "<tr><td align='center'>Spell description:</td></tr>";
		$result .= "<tr><td>".$this->spells[ $this->sID ]->baseFunction( $GLOBALS['magicConstants']->GET_DESCRIPTION )."</td>";
		$result .= "<td>&nbsp; &nbsp;</td>";
		$result .= "<td><img src='".$this->spells[ $this->sID ]->baseFunction( $GLOBALS['magicConstants']->GET_PICTURE )."'></td></tr>";
		$result .= "</table>";
		
		$result .= "<br><center>".$this->displayActiveSpells();		
		
		return $result;
	}
}
} // end if !class exists
?>