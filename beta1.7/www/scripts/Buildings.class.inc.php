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

/* Buildings class
 * 
 * This class will handle all functions needed to display, get and manipulate the 
 * information about buildings stored in the database.
 * 
 * Author: Øystein Fladby	25.02.2003
 * 
 * Changelog
 ********
 * V3
 ********
 * Øystein Fladby	28.10.2003 	- made the destroyBuildings function return false or a string
 *										- made a setInProgress function
 ********
 * V2
 ********
 * Øystein Fladby 30.05.2004	- removed this. in front of JavaScript statements to remove a Mozilla bug 
 * Øystein Fladby	19.08.2003	- made a minor fix (but this has been a major bug!) in the checkMaxBuildings function
 * Øystein Fladby	11.08.2003	- made the race and building requirement work.
 *								- added the showIllegalBuilding to show buildings the player has which he/she no longer
 *								  has the requirements to build.
 *								- Destroy buildings now only has options of buildings which the player have
 * Øystein Fladby	15.06.2003	- rewrote dotick functions ( 0.25 of prior timing / 0.01 if db engine handles all SQL)
 *								- all BuildingBase functions uses constants instead of function names
 *								- getEffect instead of lots of functions
 *								- getTotalIncome instead of a part of getTotalProperty
 *								- deletion of buildings now takes buildings in progress first
 *								- getHousing instead of many housing functions
 * 								- this class now takes a province object instead of id
 *
 ********
 * V1
 ********
 * Øystein Fladby	23.04.2003	added subMilitaryBuildTime and science requirements and news-addon
 * Anders Elton     08.04.2003  added getBuildingNetworth
 * Øystein Fladby	21.03.2003	added the XxxHousing-functions
 * Øystein Fladby	18.03.2003	changed the doTick functions because of old DB-engine
 *								added max-buildings-check.
 * Øystein Fladby	04.03.2003	added a where in the select SQL in updateResources()
 * 								changed the showXXX() tables
 * Øystein Fladby	28.02.2003	added JavaScript to calculate total resource needs 
 *
 * Version: 2.0
 * 
 */
if( !class_exists("Buildings") ) {
$GLOBALS['PATH_TO_BUILDINGS'] = $GLOBALS['path_www_scripts'] . "buildings/";
require_once($GLOBALS['path_www_scripts']."Effect.class.inc.php");
require_once( $GLOBALS['PATH_TO_BUILDINGS']."BuildingConstants.class.inc.php");
$GLOBALS['BuildingConst'] = new BuildingConstants();

class Buildings {	
	var $db;
	var $pID;
	var $provinceObj;	// object of the users province
	var $buildings;		// array with objects of all building types ['bID'] (bID UNIQUE)
	var $progress;		// array with all buildings in progress [key]['bID']['noToBuild']['ticks']
	var $totProgress;	// array with total number of a building in progress ['bID'] (bID UNIQUE)
	var $built;			// array with already built buildings in province ['bID'] (bID UNIQUE)
	var $totBuild;		// integer value with the total amount of buildings already built and in progress.
	var $effectObj;	

	////////////////////////////////////////////
	// Buildings::Buildings
	////////////////////////////////////////////
	// Constructor to set which database to use and 
	// make a new instance / object of each buildingtype, 
	// also includes/requires the files for all building-types.
	// Initialises all variables by calling updateBuildings()
	// A province (not 0) should be given by all normal pages.
	//
	// The class should only recieve NULL or 0 instead of a province 
	// when the server script wants to call the doTick function at each tick. 
	////////////////////////////////////////////
	function Buildings( $db, $provinceObj ) {
		$this->db = $db;
		$this->provinceObj = $provinceObj;
		if( $provinceObj ) {
			$this->pID = $this->provinceObj->getpID();
			$this->effectObj = new Effect( $this->db );
			$this->updateBuildings();
		} else {
			$this->setBuildings();		
		}
	}
	
	////////////////////////////////////////////
	// Buildings::updateBuildings
	////////////////////////////////////////////
	// Reset and update all the variables
	////////////////////////////////////////////
	function updateBuildings() {	
		if( $this->pID ) {
			unset($this->buildings);
			unset($this->progress);		
			unset($this->totProgress);	
			unset($this->built);
			$this->setBuildings();
			$this->totBuild = 0;				
			$this->setBuilt();					
			$this->setProgress();	
		}
	}
	
	////////////////////////////////////////////
	// Buildings::setBuildings
	////////////////////////////////////////////
	// Get alll buildingtypes and put them in the buildings array
	////////////////////////////////////////////
	function setBuildings() {
		$typeSQL = "SELECT bID, className FROM BuildingT";
		if( ( $types = $this->db->query( $typeSQL ) ) && $this->db->numRows() ) { // if the query is ok, and it found something in the DB
			if( !class_exists( "BuildingBase" ) ){
				require_once( $GLOBALS['PATH_TO_BUILDINGS']."BuildingBase.class.inc.php");
			}
			while( $row = $this->db->fetchArray( $types ) ) {				// while there are more buildingtypes in the DB
				if( !class_exists( $row['className'] ) ){
					require_once( $GLOBALS['PATH_TO_BUILDINGS'].$row['className'].".class.inc.php" );		// require the file for the class
				}
				$bID = $row['bID'];
				$this->buildings[$bID] = new $row['className']($bID);	// make a new instance (object) of the class and put it in the buildings array
				$this->totProgress[$bID] = 0;							//initialising totProgress array because of += in setProgress
			}
		} else {
			echo "There are no buildings in the DB!!!";
		}
	}

	////////////////////////////////////////////
	// Buildings::setBuilt
	////////////////////////////////////////////
	// Get already built buildings in the province 
	// and put them in the built array
	////////////////////////////////////////////
	function setBuilt() {
		$buildingsSQL = "SELECT bID, num FROM Buildings WHERE pID LIKE '$this->pID'";
		if( $this->db->query( $buildingsSQL ) && $this->db->numRows() ) {
			while( $dummy = $this->db->fetchArray() ) {
				// there can be only one of each building type per province
				$num = $dummy['num'];
				$bID = $dummy['bID'];
				$this->built[$bID] = $num;
				$this->totBuild += $num;
			}
		}
	}

	////////////////////////////////////////////
	// Buildings::setProgress
	////////////////////////////////////////////
	// Get all buildings in progress info for this 
	// player and put them in the progress array. Also
	// set the total number of buildings in progress 
	// for each buildingtype
	////////////////////////////////////////////	
	function setProgress() {
		$progressSQL = 	"SELECT bID, noToBuild, ticks 
						FROM ProgressBuild 
						WHERE pID LIKE '$this->pID' 
						ORDER BY bID, ticks"; 
		if( $this->db->query( $progressSQL ) && $this->db->numRows() ) {
			$count = 0;
			while( $dummy = $this->db->fetchArray() ) {
				$this->progress[$count] = $dummy;
				$this->totProgress[ $this->progress[$count]['bID'] ] += $this->progress[$count]['noToBuild'];
				$this->totBuild += $this->progress[$count]['noToBuild'];
				$count++;
			}			
		}
	}
	
	////////////////////////////////////////////
	// Buildings::setBuildings
	////////////////////////////////////////////
	// Function to call for each new player / at reset
	// Inserts all buildingtypes and starting number 
	// of buildings for a new province.
	////////////////////////////////////////////
	function setStartBuildings( $pID ) {
		foreach( $this->buildings as $building ) {
			$bID = $building->baseFunction( $GLOBALS['BuildingConst']->GET_ID );
			$startNum = $building->baseFunction( $GLOBALS['BuildingConst']->START_BUILDINGS );
			$insertSQL = "INSERT INTO Buildings (bID, num, pID) VALUES ('$bID','$startNum', '$pID' )";
			$this->db->query( $insertSQL );
		}
	}
	
	////////////////////////////////////////////
	// Buildings::doTick
	////////////////////////////////////////////
	// Function to be called by server script at each tick.
	// Updates all tables in the database which have a 
	// connection to buildings and their properties.
	// Calculate normal pesant growth first and put in
	// changePesants in Province table
	////////////////////////////////////////////
	function doTick() {
		$this->progressBuildCountDown();		// let building produce the same tick as it's finished?
		$this->moveFinished();
		$this->updateResources();
	}
	
	////////////////////////////////////////////
	// Buildings::progressBuildCountDown
	////////////////////////////////////////////
	// Function to count down the ticks in progressBuild
	// by one. Should be called from doTick() each tick.
	////////////////////////////////////////////
	function progressBuildCountDown(){
		$updateSQL = 	"UPDATE ProgressBuild RIGHT JOIN Province on (ProgressBuild.pID=Province.pID)
						SET ticks=(ticks-1) WHERE Province.vacationmode='false'";
		$this->db->query( $updateSQL );
	}
	
	////////////////////////////////////////////
	// Buildings::moveFinished
	////////////////////////////////////////////
	// Function to move all buildings with 0 ticks in 
	// ProgressBuild table to Buildings table and then 
	// delete them from ProgressBuild. 
	// Should be called from doTick() each tick.
	////////////////////////////////////////////
	function moveFinished(){
	// new in 5.
	// Can be buggy?
		$updateSQL = 	"UPDATE ProgressBuild P RIGHT JOIN Buildings B on B.pID=P.pID AND B.bID=P.bID 
						SET B.num=(B.num+P.noToBuild) 
						WHERE P.ticks <= 0";
	
	
	// dont work in 5
/*		$updateSQL = 	"UPDATE ProgressBuild P, Buildings B  
						SET B.num=(B.num+P.noToBuild) 
						WHERE P.ticks <= 0 
						AND B.pID=P.pID 
						AND B.bID=P.bID";*/
		$this->db->query( $updateSQL );
		$deleteSQL = 	"DELETE FROM ProgressBuild
						WHERE ticks <= 0";
		$this->db->query( $deleteSQL );		
	}
	
	////////////////////////////////////////////
	// Buildings::updateResources
	////////////////////////////////////////////
	// Function to update gold, metal and food in
	// each province based on which and how many
	// buildings the province got.
	// Also adds additional pesant growth to the
	// number already in changePesants in the Province
	// table. (call after changePesants is calculated)
	// Should be called from doTick() each tick.
	////////////////////////////////////////////
	function updateResources() {
		// calculating gold, metal and food income and put it in changeXXX in the Province table
		foreach( $this->buildings as $building ) {
		
		// new in mysql 5
			$updateSQL = 	"UPDATE Province P RIGHT JOIN Buildings B ON B.bID LIKE ".$building->baseFunction( $GLOBALS['BuildingConst']->GET_ID )." AND P.pID=B.pID
							SET P.incomeChange	=P.incomeChange	+(B.num *".$building->baseFunction( $GLOBALS['BuildingConst']->GOLD_INCOME)."),
								P.metalChange	=P.metalChange	+(B.num *".$building->baseFunction( $GLOBALS['BuildingConst']->METAL_INCOME)."),
								P.foodChange	=P.foodChange	+(B.num *".$building->baseFunction( $GLOBALS['BuildingConst']->FOOD_INCOME).") where P.vacationmode='false'";
		
		
		// didnt work in mysql 5 for some reason
/*			$updateSQL = 	"UPDATE Province P, Buildings B
							SET P.incomeChange	=P.incomeChange	+(B.num *".$building->baseFunction( $GLOBALS['BuildingConst']->GOLD_INCOME)."),
								P.metalChange	=P.metalChange	+(B.num *".$building->baseFunction( $GLOBALS['BuildingConst']->METAL_INCOME)."),
								P.foodChange	=P.foodChange	+(B.num *".$building->baseFunction( $GLOBALS['BuildingConst']->FOOD_INCOME).")
							WHERE B.bID LIKE ".$building->baseFunction( $GLOBALS['BuildingConst']->GET_ID )."
							AND P.pID=B.pID";*/
			$this->db->query( $updateSQL );
		}
		//calculating peasants housing for all acres
		$updateSQL = "Update Province set buildingPeasantPopulation = ( acres * ".$GLOBALS['BuildingConst']->PEASANTS_PR_ACRE." )";
		$this->db->query( $updateSQL );
		// calculating additional gold, metal and food income and updata changeXXX in province table
		foreach( $this->buildings as $building ) {
												// income + ( least of ( (# of buildings / acres) and ( buildings max % ) ) * added income * income )
												
// New in mysql 5												
			$updateSQL = "UPDATE Province P RIGHT JOIN Buildings B ON B.bID LIKE ".$building->baseFunction( $GLOBALS['BuildingConst']->GET_ID )." AND P.pID=B.pID
							SET P.incomeChange 	= P.incomeChange	+ ( least( ( B.num / P.acres ), ( ".$building->baseFunction( $GLOBALS['BuildingConst']->MAX_BUILDINGS )." / 100 ) ) * ".$building->baseFunction($GLOBALS['BuildingConst']->ADD_GOLD_INCOME)." 	 * P.incomeChange 	),
								P.metalChange  	= P.metalChange		+ ( least( ( B.num / P.acres ), ( ".$building->baseFunction( $GLOBALS['BuildingConst']->MAX_BUILDINGS )." / 100 ) )	* ".$building->baseFunction($GLOBALS['BuildingConst']->ADD_METAL_INCOME)." 	 * P.metalChange 	),
								P.foodChange   	= P.foodChange		+ ( least( ( B.num / P.acres ), ( ".$building->baseFunction( $GLOBALS['BuildingConst']->MAX_BUILDINGS )." / 100 ) )	* ".$building->baseFunction($GLOBALS['BuildingConst']->ADD_FOOD_INCOME)." 	 * P.foodChange 	),
								P.peasantChange = P.peasantChange	+ ( least( ( B.num / P.acres ), ( ".$building->baseFunction( $GLOBALS['BuildingConst']->MAX_BUILDINGS )." / 100 ) ) * ".$building->baseFunction($GLOBALS['BuildingConst']->ADD_PEASANT_GROWTH)." * P.peasantChange 	) where P.vacationmode='false'";
//							    P.buildingPeasantPopulation = P.buildingPeasantPopulation + ( B.num * ( ".$building->baseFunction( $GLOBALS['BuildingConst']->PEASANT_HOUSING )." - ".$GLOBALS['BuildingConst']->PEASANTS_PR_ACRE." ) ) where P.vacationmode='false'";
//							
// Dont work in mysql 5 for some reason.
/*			$updateSQL = "UPDATE Province P, Buildings B
							SET P.incomeChange 	= P.incomeChange	+ ( least( ( B.num / P.acres ), ( ".$building->baseFunction( $GLOBALS['BuildingConst']->MAX_BUILDINGS )." / 100 ) ) * ".$building->baseFunction($GLOBALS['BuildingConst']->ADD_GOLD_INCOME)." 	 * P.incomeChange 	),
								P.metalChange  	= P.metalChange		+ ( least( ( B.num / P.acres ), ( ".$building->baseFunction( $GLOBALS['BuildingConst']->MAX_BUILDINGS )." / 100 ) )	* ".$building->baseFunction($GLOBALS['BuildingConst']->ADD_METAL_INCOME)." 	 * P.metalChange 	),
								P.foodChange   	= P.foodChange		+ ( least( ( B.num / P.acres ), ( ".$building->baseFunction( $GLOBALS['BuildingConst']->MAX_BUILDINGS )." / 100 ) )	* ".$building->baseFunction($GLOBALS['BuildingConst']->ADD_FOOD_INCOME)." 	 * P.foodChange 	),
								P.peasantChange = P.peasantChange	+ ( least( ( B.num / P.acres ), ( ".$building->baseFunction( $GLOBALS['BuildingConst']->MAX_BUILDINGS )." / 100 ) ) * ".$building->baseFunction($GLOBALS['BuildingConst']->ADD_PEASANT_GROWTH)." * P.peasantChange 	),
							    P.buildingPeasantPopulation = P.buildingPeasantPopulation + ( B.num * ( ".$building->baseFunction( $GLOBALS['BuildingConst']->PEASANT_HOUSING )." - ".$GLOBALS['BuildingConst']->PEASANTS_PR_ACRE." ) )
							WHERE B.bID LIKE ".$building->baseFunction( $GLOBALS['BuildingConst']->GET_ID )."
							AND P.pID=B.pID";
							*/
			$this->db->query( $updateSQL );

			$updateSQLMaxPop = "UPDATE Province P RIGHT JOIN Buildings B ON B.bID LIKE ".$building->baseFunction( $GLOBALS['BuildingConst']->GET_ID )." AND P.pID=B.pID
							SET P.buildingPeasantPopulation = P.buildingPeasantPopulation + ( B.num * ( ".$building->baseFunction( $GLOBALS['BuildingConst']->PEASANT_HOUSING )." - ".$GLOBALS['BuildingConst']->PEASANTS_PR_ACRE." ) )";
			$this->db->query( $updateSQLMaxPop );
		     
		}		
	}

	////////////////////////////////////////////
	// Buildings::getIncome
	////////////////////////////////////////////
	// Function to get the income from a number of buildings
	// Param FUNC_TYPE is a function name found in BuildingConstants - Income
	// Returns:
	//		float which is ready to be multiplied (between 0.0001 and 2.0)
	////////////////////////////////////////////
	function getIncome( $FUNC_TYPE, $bID, $noOfBuildings ) {
		return ( $this->buildings[$bID]->baseFunction( $FUNC_TYPE ) * $noOfBuildings );
	}
	
	
	////////////////////////////////////////////
	// Buildings::getEffect
	////////////////////////////////////////////
	// Function to get the given effect.
	// Param TYPE is a function name found in BuildingConstants / EffectConstants
	// Returns:
	//		float which is ready to be multiplied where 1 is no effect
	////////////////////////////////////////////
	function getEffect( $FUNC_TYPE, $pID = false, $acres=false ) {
		if( $pID && !$acres ) {
			$selectSQL = "SELECT acres FROM Province WHERE pID LIKE '$pID'";
			$this->db->query( $selectSQL );
			if( $this->db->numRows() ) {
				$row = $this->db->fetchArray();
				$acres = $row['acres'];
			}
		} else if( !$pID ) {
			$pID = $this->pID;
			$acres = $this->provinceObj->acres;
		}
		$effect = 1;
		$selectSQL = 	"SELECT bID, num
						FROM Buildings
						WHERE pID LIKE '$pID'
						AND num > 0";
		if( $this->db->query( $selectSQL ) && $this->db->numRows() ) {
			while( $row = $this->db->fetchArray() ) {				 
				 $effect += $this->getTotalProperty( $row['bID'], $FUNC_TYPE, $row['num'], $acres );
			}
		}
		return $effect;
	}	
	
	////////////////////////////////////////////
	// Buildings::getTotalProperty
	////////////////////////////////////////////
	// Function to get the effect of a number of buildings 
	// in a province with a given size
	// Returns:
	//		the value to be added to the property
	//		e.g. 0,34 (percentage added defense)		
	////////////////////////////////////////////
	function getTotalProperty($bID, $function, $num, $acres) {		
		$buildingBonus 	= $this->buildings[$bID]->baseFunction( $function );		
		$effectivePercentage = $this->checkMaxBuildings( $bID, $acres, $num );
		return ( $effectivePercentage * $buildingBonus );		
	}
	
	////////////////////////////////////////////////////
	// Buildings::checkMaxBuildings 
	////////////////////////////////////////////////////
	// Function to compare the users buildings with
	// the max value of buildings he/she is allowed to
	// get profit from.
	////////////////////////////////////////////////////
	function checkMaxBuildings( $bID, $acres, $num ) {
		if( $acres > 0 ) {									// making sure acres > 0 to avoid errors
			$userPercentage = ( $num / $acres );
			$max = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->MAX_BUILDINGS );
                        if( $max ) {
                                $maxPercentage  = ( $max / 100 );
                                return ( $maxPercentage < $userPercentage ? $maxPercentage : $userPercentage );
                        } else {
                                return $userPercentage;
                        }
		} else {
			return 0;
		}
	} 

	////////////////////////////////////////////
	// Buildings::addBuildingCost
	////////////////////////////////////////////
	// Function to get the reduced building 
	// cost, a value between 0.0001 and 2, ready to 
	// be multiplied with the building cost.
	// Returns:
	//		array of floats which should be multiplied with the
	//			  building cost
	////////////////////////////////////////////
	function addBuildingCost() {
		$buildingCost['gold'] = $this->effectObj->getEffect($GLOBALS['BuildingConst']->ADD_BUILDING_GOLD_COST, $this->pID );
		$buildingCost['metal'] = $this->effectObj->getEffect($GLOBALS['BuildingConst']->ADD_BUILDING_METAL_COST, $this->pID );
		return $buildingCost;
	}

	////////////////////////////////////////////
	// Buildings::addBuildingTime
	////////////////////////////////////////////
	// Function to get the reduced building 
	// time value, a value where 1 is no effect, ready to 
	// be multiplied with the building time.
	// Returns:
	//		float which should be multiplied with the
	//			  building time
	////////////////////////////////////////////
	function addBuildingTime() {
		$buildingTime = $this->effectObj->getEffect( $GLOBALS['BuildingConst']->ADD_BUILDING_TIME, $this->pID );
		return $buildingTime;
	}

	////////////////////////////////////////////
	// Buildings::getHousing
	////////////////////////////////////////////
	// Function to get the max number of a given unit type / peasants
	// the province has a home for
	// param. FUNC_TYPE = constant from BuildingConstants.class
	// param. pID
	// Returns:
	//		integer value
	////////////////////////////////////////////
	function getHousing( $FUNC_TYPE, $pID = false ) {	
		$max = 0;
		if( $pID ) {
			$selectSQL = 	"SELECT bID, num
							FROM Buildings
							WHERE pID LIKE '$pID'
							AND num > 0";
			if( $this->db->query( $selectSQL ) && $this->db->numRows() ) {
				while( $row = $this->db->fetchArray() ) {
					$max += ( $this->buildings[ $row['bID'] ]->baseFunction( $FUNC_TYPE ) * $row['num'] );
				}
			}
		} else {		
			foreach( $this->built as $building ) {
				$max += ( $this->buildings[ $building['bID'] ]->baseFunction( $FUNC_TYPE ) * $building['num'] );
			}
		}
		return $max;
	}	
	
	////////////////////////////////////////////
	// Buildings::destroySpecificBuildings
	////////////////////////////////////////////
	// Function to destroy spesified buildings on a number of
	// acres. $buildings should be an array of building names.
	// Returns:
	//		array with 	[html] 	= string with <br>1 of your finished Home buildings <br>3 of your ....
	//						[totDestroyed]	= total number of destroyed buildings
	//		false - if error
	////////////////////////////////////////////
	function destroySpecificBuildings( $pID, $acres, $buildings ) {
		$result = false;
		$totDestroyed = 0;
		if( $pID && $acres ) {
			$selectSQL = "SELECT acres FROM Province WHERE pID LIKE '$pID'";
			$this->db->query( $selectSQL );
			if( $this->db->numRows() ) {
				$row = $this->db->fetchArray();
				$totalAcres = $row['acres'];
			}
		} else if( !$pID ) {
			$pID = $this->pID;
			$totalAcres = $this->provinceObj->acres;
		}
		$selectSQL = 	"SELECT bID, num
						FROM Buildings
						WHERE pID LIKE '$pID'
						AND num > 0";
		
		if( ( $selectResult = $this->db->query( $selectSQL ) ) && ( $numRows = $this->db->numRows() ) ) {
			if( !$result ) {
				$result = "";
			}
			while( $row = $this->db->fetchArray( $selectResult ) ) {
				$bID = $row['bID'];
				$name = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->GET_NAME );
				if( in_array( $name, $buildings ) ) {
					$destroy = ceil( $acres * $row['num'] / $totalAcres );
					$name = strtolower( $name );
					$numDestroyed = ( ( $row['num'] - $destroy ) > 0 ) ? $destroy : $row['num'];	// making result
					$result .= "\n<br>$numDestroyed of your finished $name buildings";
					$totDestroyed += $numDestroyed;
				
					$updateSQL ="UPDATE Buildings
									SET num = GREATEST( (num - $destroy), 0 )
									WHERE bID LIKE '$bID' 
									AND pID LIKE '$pID'";
					$this->db->query( $updateSQL );
				}
			}
		}
		$selectSQL =    "SELECT bID, noToBuild, ticks
                        FROM ProgressBuild
                        WHERE pID LIKE '$pID'
                        AND noToBuild > 0";
		$progArray = false;
		if( ( $selectResult = $this->db->query( $selectSQL ) ) && ( $numRows = $this->db->numRows() ) ) {
			$progArray = array();
			while( $row = $this->db->fetchArray( $selectResult ) ) {				
				$bID = $row['bID'];
				$name = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->GET_NAME );
				if( in_array( $name, $buildings ) ) {
					$destroy = ceil( $acres * $row['noToBuild'] / $totalAcres );
				
					if( !array_key_exists( $bID, $progArray ) ) {
						$progArray[$bID] = 0;
					}
					$numDestroyed = ( ( $row['noToBuild'] - $destroy ) > 0 ) ? $destroy : $row['noToBuild'];	// making result
					$progArray[$bID] += $numDestroyed;
					$totDestroyed += $numDestroyed;
				
					$updateSQL ="UPDATE ProgressBuild
									SET noToBuild = GREATEST( (noToBuild - $destroy), 0 )
									WHERE bID LIKE '$bID'
									AND pID LIKE '$pID'
									AND ticks LIKE '".$row['ticks']."'";
					$this->db->query( $updateSQL );
				}
			}
		}
		if( $progArray ) {
			$html = "";
			while( list( $key, $num ) = each( $progArray ) ) {
				$name = strtolower( $this->buildings[$key]->baseFunction( $GLOBALS['BuildingConst']->GET_NAME ) );
				$html .= "\n<br>$num of your $name buildings in progress";
			}
			if( $result ) {
				$result .= $html;
			} else {
				$result = $html;
			}
		}
		$result = array( "html" => $result, "totDestroyed" => $totDestroyed );
		return $result;
	}
	////////////////////////////////////////////
	// Buildings::destroyBuildingsOnAcres
	////////////////////////////////////////////
	// Function to destroy buildings on a number of
	// acres
	// Returns:
	//		array with 	[html] 	= string with <br>1 of your finished Home buildings <br>3 of your ....
	//						[totDestroyed]	= total number of destroyed buildings
	//		false - if error
	////////////////////////////////////////////
	function destroyBuildingsOnAcres( $pID, $acres ) {
		$result = false;
		$totDestroyed = 0;
		$totalAcres = 0;
		if( $pID && $acres ) {
			$selectSQL = "SELECT acres FROM Province WHERE pID LIKE '$pID'";
			$this->db->query( $selectSQL );
			if( $this->db->numRows() ) {
				$row = $this->db->fetchArray();
				$totalAcres = $row['acres'];
			}
		} else if( !$pID ) {
			$pID = $this->pID;
			$totalAcres = $this->provinceObj->acres;
		}
		$totalAcres = ( $totalAcres > 0 ? $totalAcres : 1 );
		$selectSQL = 	"SELECT bID, num
						FROM Buildings
						WHERE pID LIKE '$pID'
						AND num > 0";
		
		if( ( $selectResult = $this->db->query( $selectSQL ) ) && ( $numRows = $this->db->numRows() ) ) {
			if( !$result ) {
				$result = "";
			}
			while( $row = $this->db->fetchArray( $selectResult ) ) {
				$bID = $row['bID'];
				$name = strtolower( $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->GET_NAME ) );
				$destroy = ceil( $acres * $row['num'] / $totalAcres );
				
				$numDestroyed = ( ( $row['num'] - $destroy ) > 0 ) ? $destroy : $row['num'];	// making result
				$result .= "\n<br>$numDestroyed of your finished $name buildings";
				$totDestroyed += $numDestroyed;
				
				$updateSQL ="UPDATE Buildings
								SET num = GREATEST( (num - $destroy), 0 )
								WHERE bID LIKE '$bID' 
								AND pID LIKE '$pID'";
				$this->db->query( $updateSQL );
			}
		}
		$selectSQL =    "SELECT bID, noToBuild, ticks
                        FROM ProgressBuild
                        WHERE pID LIKE '$pID'
                        AND noToBuild > 0";
		$progArray = false;
		if( ( $selectResult = $this->db->query( $selectSQL ) ) && ( $numRows = $this->db->numRows() ) ) {
			$progArray = array();
			while( $row = $this->db->fetchArray( $selectResult ) ) {				
				$bID = $row['bID'];
				$destroy = ceil( $acres * $row['noToBuild'] / $totalAcres );
				
				if( !array_key_exists( $bID, $progArray ) ) {
					$progArray[$bID] = 0;
				}
				$numDestroyed = ( ( $row['noToBuild'] - $destroy ) > 0 ) ? $destroy : $row['noToBuild'];	// making result
				$progArray[$bID] += $numDestroyed;
				$totDestroyed += $numDestroyed;
				
				$updateSQL ="UPDATE ProgressBuild
								SET noToBuild = GREATEST( (noToBuild - $destroy), 0 )
								WHERE bID LIKE '$bID'
								AND pID LIKE '$pID'
								AND ticks LIKE '".$row['ticks']."'";
				$this->db->query( $updateSQL );
			}
		}
		if( $progArray ) {
			$html = "";
			while( list( $key, $num ) = each( $progArray ) ) {
				$name = strtolower( $this->buildings[$key]->baseFunction( $GLOBALS['BuildingConst']->GET_NAME ) );
				$html .= "\n<br>$num of your $name buildings in progress";
			}
			if( $result ) {
				$result .= $html;
			} else {
				$result = $html;
			}
		}
		$result = array( "html" => $result, "totDestroyed" => $totDestroyed );
		return $result;
	}

	////////////////////////////////////////////
	// Buildings::transferToProgress
	////////////////////////////////////////////
	// Function to transfer a percentage of the buildings 
	// from "finished" to "in progress"
	// Takes the percentage of buildings (float like 0.56) that should be transfered, the pID and
	// optionally the number of minimum ticks it should be transfered to and the maximum 
	// Optionally it also takes a specific building to transfer.
	// Returns:
	//		array with 	[html] 	= string with <br>1 of your Home buildings <br>3 of your ....
	//						[totTransfer]	= total number of transfered buildings
	//		false - if error
	////////////////////////////////////////////
	function transferToProgress( $percentage, $pID, $minTicks=1, $maxTicks=24, $bID=false ) {
		$result = false;
		$percentage = ( abs( $percentage) > 1 ) ? abs( $percentage/100 ) : abs( $percentage );
		mt_srand( $this->makeSeed() );
		
		if( $bID ) {
			$selectSQL = "SELECT bID, num
							FROM Buildings
							WHERE pID LIKE '$pID'
							AND num > 0 
							AND bID LIKE '$bID'";
		} else {
			$selectSQL = "SELECT bID, num
							FROM Buildings
							WHERE pID LIKE '$pID'
							AND num > 0";
		}
		
		if( ( $selectResult = $this->db->query( $selectSQL ) ) && ( $numRows = $this->db->numRows() ) ) {
			$result = array( "html" => "", "totTransfer" => 0);
			while( $row = $this->db->fetchArray( $selectResult ) ) {
				$bID = $row['bID'];
				$num = $row['num'];
				$name = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->GET_NAME );
				$transfer = round( $percentage * $num );
				$result['html'] .= "\n<br>$transfer of your $name buildings";
				$result['totTransfer'] += $transfer;
					
				$updateSQL ="UPDATE Buildings
								SET num = (num - $transfer)
								WHERE bID LIKE '$bID' 
								AND pID LIKE '$pID'";
				$this->db->query( $updateSQL );
					
				while( $transfer >= 1 ) {
					$ticks = mt_rand( $minTicks, $maxTicks );
					$tmpTransfer = mt_rand( max( 1, round( $transfer / 5 ) ), $transfer );
					$transfer -= $tmpTransfer;
					//echo "<br>hei: $transfer :: $tmpTransfer :: $ticks";
										
					$selectSQL = "SELECT bID 
									FROM ProgressBuild
									WHERE bID LIKE '$bID'
									AND pID LIKE '$pID'
									AND ticks LIKE '$ticks'";
					if( $this->db->query( $selectSQL ) && $this->db->numRows() ) {
						//echo " :: U";
						$updateSQL ="UPDATE ProgressBuild
										SET noToBuild = (noToBuild + $tmpTransfer)
										WHERE pID LIKE '$pID'
										AND bID LIKE '$bID'
										AND ticks LIKE '$ticks'";
						$this->db->query( $updateSQL );
					}else {
						//echo " :: I";
						$insertSQL = "INSERT INTO ProgressBuild ( bID, pID, ticks, noToBuild )
										VALUES ( $bID, $pID, $ticks, $tmpTransfer )";
						$this->db->query( $insertSQL );
					}
				}
			}
		}		
		return $result;
	}
	
	////////////////////////////////////////////
	// Buildings::makeSeed
	////////////////////////////////////////////
	// Function to make a random seed for a random function
	// Returns:
	// 		float number
	////////////////////////////////////////////
	function makeSeed() {
    		list($usec, $sec) = explode(' ', microtime());
    		return (float) $sec + ((float) $usec * 100000);
	}
	
	////////////////////////////////////////////
	// Buildings::getNumBuilding
	////////////////////////////////////////////
	// Function to get the number of buildings already
	// built for a given building id
	// Returns:
	//		integer number of built buildings
	////////////////////////////////////////////

	function getNumBuilding( $bID ) {
		if ( isset( $this->built[$bID] ) ) {
			return $this->built[$bID];
		} else {
			return 0;
		}
	}
	
	////////////////////////////////////////////
	// Buildings::getBuilding
	////////////////////////////////////////////
	// Function to get an object of the given buildingID
	// Returns:
	//		BuildingBase object 
	////////////////////////////////////////////
	function getBuilding( $bID ) {
		return $this->buildings[ $bID ];
	}
	
	////////////////////////////////////////////
	// Buildings::getTotProgress
	////////////////////////////////////////////
	// Function to get the sum of buildings in 
	// progress for a given building id
	// Returns:
	//		integer sum of buildings in progress
	////////////////////////////////////////////
	function getTotProgress( $bID ) {
		if( isset( $this->totProgress[$bID] ) ) {
			return $this->totProgress[$bID];
		}else {
			return 0;
		}
	}
	
	////////////////////////////////////////////
	// Buildings::useResources
	////////////////////////////////////////////
	// Function to update the Province tables gold
	// and metal values when using resources
	// Returns:
	//		an empty string for true, an error message for false
	////////////////////////////////////////////
	function useResources( $number, $bID ) {
		$returnString = $this->enoughResources( $number, $bID );
		if( !strlen($returnString)  ) {
			// user got enough resources
			$reduced = $this->addBuildingCost();
			$gold = round( $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->GET_GOLD_COST ) * $number * $reduced['gold'] );
			$metal = round( $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->GET_METAL_COST ) * $number * $reduced['metal'] );
			
			$ret = $this->provinceObj->useResource( $gold, $metal, 0 );	
			if ($ret == false)
				$returnstring ="Not enough resources.";
			else
				$returnString = "";
		} 
		return $returnString;
	}
	
	////////////////////////////////////////////
	// Buildings::getBuildingCost
	////////////////////////////////////////////
	// Function to calculate the cost of building this building(s)
	// Returns:
	//		associative array with the cost of the buildings
	////////////////////////////////////////////
	function getBuildingCost( $number, $bID ) {
		$reduced = $this->addBuildingCost();
		$BuildingCost['gold'] = round( $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->GET_GOLD_COST ) * $number * $reduced['gold'] );
		$BuildingCost['metal'] = round( $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->GET_METAL_COST ) * $number * $reduced['metal'] );
		return $BuildingCost;
	}
	////////////////////////////////////////////
	// Buildings::enoughResources
	////////////////////////////////////////////
	// Function to make sure player has enough 
	// resources to build this building(s)
	// Returns:
	//		an empty string for true, an error message for false
	////////////////////////////////////////////
	function enoughResources( $number, $bID ) {
		$returnString = "";
		$BuildingCost = $this->getBuildingCost( $number, $bID );
		if( $BuildingCost['gold'] > $this->provinceObj->gold ) {
			$name = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->GET_NAME );
			$returnString = "<br>You don't have enough gold to build $number $name building".( $number == 1? "":"s");
		}
		if( $BuildingCost['metal'] > $this->provinceObj->metal ) {
			$name = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->GET_NAME );
			$returnString .= "<br>You don't have enough metal to build $number $name building".( $number == 1? "":"s");
		}
		if( $number > ($this->provinceObj->acres - $this->totBuild) ) {
			$buildingName = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->GET_NAME );
			$returnString .= "<br>You don't have enough acres left to build $number $buildingName building".( $number == 1? "":"s");
		}
		return $returnString;		
	}
	
	////////////////////////////////////////////
	// Buildings::haveBuilding
	////////////////////////////////////////////
	// Function to find out if a player has the given
	// building either finished or in progress.
	// Takes either an array with building names/ ids or
	// just one single name / id.
	// Returns:
	//		true or false
	////////////////////////////////////////////
	function haveBuilding( $bID, $pID=false ) {
		if( $bID ) {
			if( $pID ) {
				$savedpID = $this->pID;
				$this->pID = $pID;
				$this->updateBuildings();
			}
			if( is_numeric( $bID ) ) {
				if( $this->built[$bID]['num'] > 0 ) {
					return true;
				} else if( $this->totProgress[$bID] > 0 ) {
					return true;
				}
			} else if( is_string( $bID ) ) {
				foreach( $this->buildings as $building ) {
					$buildingID = $building->baseFunction( $GLOBALS['BuildingConst']->GET_ID);
					if( ( ( $this->built[$buildingID]['num'] > 0 ) || ( $this->totProgress[$buildingID] > 0 ) ) 
						&& (!strcmp( $building->baseFunction( $GLOBALS['BuildingConst']->GET_NAME ), $bID ) ) ) {
						return true;
					}
				}
			} else if( is_array( $bID ) ) {
				foreach( $bID as $buildingID ) {
					if( $this->haveBuilding( $buildingID ) ) {
						return true;
					}
				}
			}
			if( $pID ) {
				$this->pID = $savedpID;
				$this->updateBuildings();
			}
		} /*else {
			return true;
		}*/
		return false;
	}
	
	////////////////////////////////////////////
	// Buildings::buildingReqOk
	////////////////////////////////////////////
	// Function to find out if a player has the required finished
	// buildings. Takes an array of building names / ids or just 
	// one building name/ id.
	// Returns:
	//		true or false
	////////////////////////////////////////////
	function buildingReqOk( $bID, $pID=false ) {
		if( $bID ) {
			if( $pID ) {
				$savedpID = $this->pID;
				$this->pID = $pID;
				$this->updateBuildings();
			}
			if( is_numeric( $bID ) ) {
				if( $this->built[$bID]['num'] > 0 ) {
					return true;
				}				
			} else if( is_string( $bID ) ) {
				foreach( $this->buildings as $building ) {
					$buildingID = $building->baseFunction( $GLOBALS['BuildingConst']->GET_ID );
					if( ( $this->built[$buildingID]['num'] > 0 ) && (!strcmp( $building->baseFunction( $GLOBALS['BuildingConst']->GET_NAME ), $bID ) ) ) {
						return true;
					}
				}
			} else if( is_array( $bID ) ) {
				foreach( $bID as $building ) {
					if( !$this->buildingReqOk( $building ) ) {
						return false;
					}
				}
				return true;
			}
			if( $pID ) {
				$this->pID = $savedpID;
				$this->updateBuildings();
			}
		} else {
			return true;
		}
		return false;
	}
	
	////////////////////////////////////////////
	// Buildings::haveRequirements
	////////////////////////////////////////////
	// Function to make sure the player has the required
	// science / buildings etc. to build the given building 
	// Returns:
	//		true if requirements ok, else false
	////////////////////////////////////////////
	function haveRequirements( $bID ) {

		$sciReqArr = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->SCIENCE_REQUIREMENTS );
		//building req
		$buildingReqArr = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->BUILDING_REQUIREMENTS );
		//race req
		$raceReqArr = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->RACE_REQUIREMENTS );
		//buildings that prevents
		$buildingPrevArr = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->BUILDING_PREVENT );
		//sciences that prevents
		//$sciPrevArr = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->SCIENCE_PREVENT );
		return $this->effectObj->reqsOk( $buildingReqArr, $sciReqArr, $raceReqArr, $buildingPrevArr, $this->pID );
/*		
$sciReqArr = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->SCIENCE_REQUIREMENTS );
		if( !$this->provinceObj->sciObj->scienceReqOk( $sciReqArr ) ) {
			return false;
		}
		//building req
		$buildingReqArr = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->BUILDING_REQUIREMENTS );
		if( !$this->buildingReqOk( $buildingReqArr ) ) {
			return false;
		}
		//race req
		$raceReqArr = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->RACE_REQUIREMENTS );
		if( !$this->provinceObj->raceObj->raceReqOk( $raceReqArr ) ) {
			return false;
		}
		//buildings that prevents
		$buildingPrevArr = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->BUILDING_PREVENT );
		if( $buildingPrevArr ) {
			if( $this->haveBuilding( $buildingPrevArr ) ) {
				return false;
			}
		}
		//sciences that prevents
		$sciPrevArr = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->SCIENCE_PREVENT );
		if( $sciPrevArr ) {
			if( $this->provinceObj->sciObj->scienceReqOk( $sciPrevArr ) ) {
				return false;
			}
		}
		//races that prevents
		$racePrevArr = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->RACE_PREVENT );
		if( $racePrevArr ) {
			if( $this->provinceObj->raceObj->raceReqOk( $racePrevArr ) ) {
				return false;
			}
		}
		return true;
	*/		
	}

	////////////////////////////////////////////
	// Buildings::buildBuilding
	////////////////////////////////////////////
	// Function to build the given number of buildings of 
	// the given type for the current province. It will
	// add to an existing record if a similar record is 
	// already in the table, else it inserts a new record
	// Returns:
	//		an empty string for true, an error message for false
	////////////////////////////////////////////
	function buildBuilding( $numToBuild, $bID ) {
		$returnString = "";
		$returnString = $this->useResources( $numToBuild, $bID );
		if( !strlen( $returnString ) ) {
			$ticks = round( $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->GET_TICKS ) * $this->addBuildingTime() );
			$selectSQL = 	"SELECT proID, noToBuild 
							FROM ProgressBuild 
							WHERE pID LIKE '$this->pID' 
							AND bID LIKE '$bID' 
							AND ticks LIKE '$ticks'";
			$insertSQL =	"INSERT INTO ProgressBuild (bID, pID, ticks, noToBuild) 
							VALUES ('$bID', '$this->pID', '$ticks', '$numToBuild')"; 
			
			if( $result = $this->db->query( $selectSQL ) && $this->db->numRows() ) { // if the query is ok, and we got a result
					$row = $this->db->fetchArray();			
					$numToBuild += $row['noToBuild'];		// add buildings to build to those already in the DB
					$proID = $row['proID'];
					
					$updateSQL = 	"UPDATE ProgressBuild 
									SET noToBuild=$numToBuild 
									WHERE proID LIKE '$proID'";
					$this->db->query( $updateSQL );					
					$this->updateBuildings();				// update this instance with the new information
					$returnString = "";						// not needed
			} else if( $this->db->query( $insertSQL ) ) {	// if no building with the same bID and pID and ticks exists, we have to insert
				$this->updateBuildings();					// update this instance with the new information		
				$returnString = "";							// not needed
			} else {										// should never come this far if the db isn't corrupted	
				$returnString = "\n<br><br>There's a really serious problem with the DB. Contact site admin!";
			}
		}
		return $returnString;
	}
	
	////////////////////////////////////////////
	// Buildings::destroyBuildingInProgress
	////////////////////////////////////////////
	// Function to destroy the given number of buildings of 
	// the given type which are in progress for the current province. 
	// Returns:
	//		int - the number of buildings destroyed
	////////////////////////////////////////////
	function destroyBuildingInProgress( $numToDestroy, $bID ) {
		$progress = $this->getTotProgress( $bID );
		$name = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->GET_NAME );
		if( $progress ) {
			$selectSQL = 	"SELECT noToBuild, ticks
							FROM ProgressBuild
							WHERE pID LIKE '$this->pID'
							AND bID LIKE '$bID'
							ORDER BY ticks DESC";
			$destroy = $this->db->query( $selectSQL );
			if( $this->db->numRows( $destroy ) ) {
				while( $numToDestroy && ( $row = $this->db->fetchArray( $destroy ) ) ) {
					$num = $row['noToBuild'];
					if( ( $num - $numToDestroy ) > 1 ) {	// if the user has more in progress this tick than he want to destroy
						$updateSQL = 	"UPDATE ProgressBuild
										SET noToBuild = GREATEST( (noToBuild - $numToDestroy ), 0) 
										WHERE pID LIKE '$this->pID'
										AND bID LIKE '$bID'
										AND ticks LIKE '".$row['ticks']."'";
						$this->db->query( $updateSQL );
					} else {			// if the user has less in progress this tick than he want to destroy
						$deleteSQL = 	"DELETE FROM ProgressBuild
										WHERE pID LIKE '$this->pID'
										AND bID LIKE '$bID'
										AND ticks LIKE '".$row['ticks']."'";
						$this->db->query( $deleteSQL );
					}
					$numToDestroy -= $num;
					$numToDestroy = ( $numToDestroy < 0 ? 0 : $numToDestroy );
				}
			}	
		}		
		return $numToDestroy;
	}
	////////////////////////////////////////////
	// Buildings::destroyBuilding
	////////////////////////////////////////////
	// Function to destroy the given number of buildings of 
	// the given type for the current province. 

	// Returns:
	//		a string with an explaining text
	////////////////////////////////////////////
	function destroyBuilding( $numToDestroy, $bID ) {
		$built = $this->getNumBuilding( $bID );
		$progress = $this->getTotProgress( $bID );
		$name = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->GET_NAME );
		$leftToDestroy = $numToDestroy;
		$returnString = "";
		if( $numToDestroy == 0 ) {
			$returnString .= "";
		} else if( !$built && !$progress ) {
			$returnString .= "<br>You don't have any $name building".( $numToDestroy == 1 ? "" : "s" )." to destroy";
		} else {
			if( $progress ) { // if the user has buildings in progress
				if( ( $leftToDestroy = $this->destroyBuildingInProgress( $numToDestroy, $bID ) ) ) {// destroy buildings in progress couldn't destroy enough
					$returnString .= "<br>You have destroyed all your $name buildings which were in progress.";				
				} else {
					$returnString .= "<br>You have destroyed $numToDestroy of your $name buildings which were in progress.";
				}
			}								
			if( $leftToDestroy ) {	// if the user wanted to destroy more buildings
				//echo $leftToDestroy;			
				if( $built - $leftToDestroy > 0 ) {	// user also has finished buildings of this type
					$updateSQL = 	"UPDATE Buildings 
									SET num= GREATEST( ( num-$leftToDestroy), 0 ) 
									WHERE bID LIKE '$bID' 
									AND pID LIKE '$this->pID'";
					$this->db->query( $updateSQL );
					$returnString .= "<br>You have destroyed $leftToDestroy of your finished $name buildings.";					
				} else if( $built ) {			// user also destroys all finished buildings if any
					$updateSQL = 	"UPDATE Buildings 
									SET num='0' 
									WHERE bID LIKE '$bID' 
									AND pID LIKE '$this->pID'";
					$this->db->query( $updateSQL );
					$returnString .= "<br>You have destroyed all your finished $name buildings.";
				}
			}
		}
		$this->updateBuildings();
		return $returnString;
	}

	////////////////////////////////////////////
	// Buildings::build

	////////////////////////////////////////////
	// Function to build all buildings posted by the
	// form from showBuildings()
	// Returns:
	//		a string with an explaining text
	////////////////////////////////////////////
	function build() {
		
		$returnString = "";
		foreach( $this->buildings as $building ) {	
												// for each buildingtype
			$javaName = str_replace(" ", "_", $building->baseFunction( $GLOBALS['BuildingConst']->GET_NAME ) );							// javaScript tar ikke mellomrom, derfor er feltnavn gjort om
	
			if( isset( $_POST[$javaName] ) && strlen( $_POST[$javaName] ) ) {	// if the player wanted to build a building
				$numberToBuild = $_POST[$javaName];
				$numberToBuild = round( $numberToBuild, 0 );
				if( !is_numeric( $numberToBuild ) || ( $numberToBuild < 0 ) ) {					// if entered value isn't a number or a number below 0 
					$returnString .= "\n<br>You have to enter a numeric value of at least '0' for the ".$building->baseFunction( $GLOBALS['BuildingConst']->GET_NAME )." building(s).";
				} else if( $numberToBuild > 0 ) {
					$this->effectObj->magicObj->triggerSpellEffect( $GLOBALS['magicConstants']->TRIGGER_BUILDING_BUILDING, $this->pID );
					$dummy = $this->buildBuilding( $numberToBuild, $building->baseFunction( $GLOBALS['BuildingConst']->GET_ID ) );
					if( !strlen( $dummy ) ) {
						$returnString .= "<br>Another ".$numberToBuild." ".$building->baseFunction( $GLOBALS['BuildingConst']->GET_NAME )."".($numberToBuild == 1 ? "" : "s")." in progress.";
					} else {
						$returnString .= "<br>".$dummy;
					}
				}
			}			
		}
		
		$returnString = "\n<br>
			<table align='center' border='1' bordercolor='#FFEECC' cellspacing='1' cellpadding='2'>
				<tr>
					<td class='buildings'>".$returnString."
					</td>
				</tr>
			</table>";
		
		return $returnString;
	}
	
	////////////////////////////////////////////
	// Buildings::destroy
	////////////////////////////////////////////
	// Function to destroy all buildings posted by the
	// form from showDestroy()
	// Returns:
	//		a string with an explaining text
	////////////////////////////////////////////
	function destroy() {
		$returnString = "";
		if( isset( $_POST['noToDestroy'] ) && strlen( $_POST['noToDestroy'] ) && isset( $_POST['buildingType'] ) ) {	// if the player wanted to destroy a building
			$numberToDestroy = $_POST['noToDestroy'];
			$numberToDestroy = round( $numberToDestroy, 0 );
			$bID = $_POST['buildingType'];
			if( !is_numeric( $numberToDestroy ) || ( $numberToDestroy < 0 ) ) {			// if entered value isn't a number or a number below 0 
				$returnString .= "\n<br>You have to enter a numeric value of at least '0' to destroy the buildings.";
			} else if( $numberToDestroy > 0 ) {
				$this->effectObj->magicObj->triggerSpellEffect( $GLOBALS['magicConstants']->TRIGGER_DESTROYING_BUILDING, $this->pID );
				$returnString = $this->destroyBuilding( $numberToDestroy, $bID );
			}
		}
		$returnString = "\n<br>
			<table align='center' border='1' bordercolor='#FFEECC' cellspacing='1' cellpadding='2'>
				<tr>
					<td class='buildings'>".$returnString."
					</td>
				</tr>
			</table>";
		return $returnString;
	}		
	
	////////////////////////////////////////////
	// Buildings::showBuilding
	////////////////////////////////////////////
	// Function to get a table with information 
	// about a given building. It does not print to 
	// screen.
	// Returns:
	//		a string with the html code to display the table
	////////////////////////////////////////////
	function showBuilding( $bID ) {
		$reduced = $this->addBuildingCost();
		$costGold = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->GET_GOLD_COST ) * $reduced['gold'];
		$costMetal = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->GET_METAL_COST ) * $reduced['metal'];
		$acres = $this->provinceObj->acres;
		$name = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->GET_NAME );
		$javaName = str_replace( " ", "_", $name);			// javaScript takler ikke mellomrom
		$progress = $this->getTotProgress( $bID );
		$alreadyBuilt = $this->getNumBuilding( $bID );
		
		$resultString = "			
		<table nowrap class='buildingsTable' align='center' width='100%' cols='4' border='0' cellspacing='1' cellpadding='2'>
			<tr>
				<td nowrap class='buildings'>
					<a href='#' onClick=\"return showDescription('$bID','$acres','$alreadyBuilt');\">$name</a>
				</td>
				<td nowrap class='buildings'>Built: $alreadyBuilt</td> 
				<td nowrap class='buildings'>In progress: $progress</td> 
				<td nowrap class='buildings' align='center'>
					<input type='text' name='$javaName' class='form' size='5' title='Choose number of $name buildings to build'
					onFocus='lastGoldValue=Math.round(document.Build.$javaName.value*$costGold);
							lastMetalValue=Math.round(document.Build.$javaName.value*$costMetal);
							lastAcresValue=Math.round(document.Build.$javaName.value)' 
					onChange='document.Build.$javaName.value=Math.round(document.Build.$javaName.value);
							document.Build.calcGold$javaName.value=Math.round(document.Build.$javaName.value*$costGold);
							document.Build.calcMetal$javaName.value=Math.round(document.Build.$javaName.value*$costMetal);
							calcFields(Math.round(document.Build.$javaName.value*$costGold),Math.round(document.Build.$javaName.value*$costMetal),Math.round(document.Build.$javaName.value*1))'>
				</td> 
			</tr>			
			<tr>
				<td nowrap class='buildings'>Total gold cost:</td>
				<td nowrap class='buildings' align='center'> 
					<input class='readOnly' type='text' maxlength='6' size='6' name='calcGold$javaName' readonly='1' value='0'>
				</td>
				<td nowrap class='buildings'>Total metal cost:</td>
				<td nowrap class='buildings' align='center'>
					<input class='readOnly' type='text' maxlength='5' size='5' name='calcMetal$javaName' readonly='1' value='0'>
				</td>
			</tr>									
		</table><br>\n";
		return $resultString;		
	}

	////////////////////////////////////////////
	// Buildings::showBuildingNobuild
	////////////////////////////////////////////
	// Function to get a table with information 
	// about a given building. It does not print to 
	// screen.
	// Returns:
	//		a string with the html code to display the table
	////////////////////////////////////////////
	function showBuildingNoBuild( $bID ) {
		$reduced = $this->addBuildingCost();
		$costGold = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->GET_GOLD_COST ) * $reduced['gold'];
		$costMetal = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->GET_METAL_COST ) * $reduced['metal'];
		$acres = $this->provinceObj->acres;
		$name = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->GET_NAME );
		$javaName = ereg_replace( " ", "_", $name );			// javaScript takler ikke mellomrom
		$progress = $this->getTotProgress( $bID );
		$alreadyBuilt = $this->getNumBuilding( $bID );
		
		$resultString = "			
		<table nowrap class='buildingsTable' align='center' width='100%' cols='4' border='0' cellspacing='1' cellpadding='2'>
			<tr>
				<td nowrap class='buildings'>
					<a href='#' onClick=\"return showDescription('$bID','$acres','$alreadyBuilt');\">$name</a>
				</td>
				<td nowrap class='buildings'>Built: $alreadyBuilt</td> 
				<td nowrap class='buildings'>In progress: $progress</td> 
			</tr>			
		</table><br>\n";
		return $resultString;		
	}
	
	////////////////////////////////////////////
	// Buildings::showIllegalBuilding
	////////////////////////////////////////////
	// Function to get a table with information 
	// about a given building. It does not print to 
	// screen.
	// Returns:
	//		a string with the html code to display the table
	////////////////////////////////////////////
	function showIllegalBuilding( $bID ) {
		$reduced = $this->addBuildingCost();
		$costGold = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->GET_GOLD_COST ) * $reduced['gold'];
		$costMetal = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->GET_METAL_COST ) * $reduced['metal'];
		$acres = $this->provinceObj->acres;
		$name = $this->buildings[$bID]->baseFunction( $GLOBALS['BuildingConst']->GET_NAME );
		$javaName = ereg_replace( " ", "_", $name );			// javaScript takler ikke mellomrom
		$progress = $this->getTotProgress( $bID );
		$alreadyBuilt = $this->getNumBuilding( $bID );
		
		$resultString = "			
		<table nowrap class='buildingsTable' align='center' width='100%' cols='4' border='0' cellspacing='1' cellpadding='2'>
			<tr>
				<td nowrap class='buildings'>
					<a href='#' onClick=\"return showDescription('$bID','$acres','$alreadyBuilt');\">$name</a>
				</td>
				<td nowrap class='buildings'>Built: $alreadyBuilt</td> 
				<td nowrap class='buildings'>In progress: $progress</td> 
				<td nowrap class='buildings' align='center'>
					<input type='text' name='$javaName' class='readOnly' size='6' maxlength='4' readonly='1' title='There are requirements that you must meet to build more of this building' 
					onFocus='lastGoldValue=Math.round(document.Build.$javaName.value*$costGold);
							lastMetalValue=Math.round(document.Build.$javaName.value*$costMetal);
							lastAcresValue=Math.round(document.Build.$javaName.value)' 
					onChange='document.Build.$javaName.value=Math.round(document.Build.$javaName.value);
							document.Build.calcGold$javaName.value=Math.round(document.Build.$javaName.value*$costGold);
							document.Build.calcMetal$javaName.value=Math.round(document.Build.$javaName.value*$costMetal);
							calcFields(Math.round(document.Build.$javaName.value*$costGold),Math.round(document.Build.$javaName.value*$costMetal),Math.round(document.Build.$javaName.value*1))'>
				</td> 
			</tr>			
			<tr>
				<td nowrap class='buildings'>Total gold cost:</td>
				<td nowrap class='buildings' align='center'> 
					<input class='readOnly' type='text' maxlength='6' size='6' name='calcGold$javaName' readonly='1' value='0'>
				</td>
				<td nowrap class='buildings'>Total metal cost:</td>
				<td nowrap class='buildings' align='center'>
					<input class='readOnly' type='text' maxlength='6' size='6' name='calcMetal$javaName' readonly='1' value='0'>
				</td>
			</tr>									
		</table><br>\n";
		return $resultString;		
	}
	
	////////////////////////////////////////////
	// Buildings::showAllBuildings
	////////////////////////////////////////////
	// Function to display a table with information 
	// about all building types. Also shows calculations
	// and buttons to build buildings.
	// Returns:
	//		a string with the html code
	////////////////////////////////////////////
	function showAllBuildings() {
		$currentFreeAcres = ($this->provinceObj->acres-$this->totBuild);
		$bIDs = array_keys( $this->buildings );
		$self = $_SERVER['PHP_SELF'];
		$count = 0;
		$resultString = "\n<br><br>";
		$resultString .= "<form class='form' action='$self' method='post' target='_self' name='Build' onSubmit='return checkResources()'> ";
		$resultString .= $GLOBALS['fcid_post'];		
		$resultString .="<table cols='3' align='center' border='0' cellspacing='0' cellpadding='0' width='*'>";
		$dummyString = "";
		foreach( $bIDs as $buildingID ) {
			if( $this->haveRequirements( $buildingID ) ) {		// don't show buildings the player can't build or has reached maximum number of
				$dummyString .= "<td>".$this->showBuilding( $buildingID )."</td>";
				if( $count%2 == 0 ) {
					$dummyString = "<tr>".$dummyString."<td>&nbsp;&nbsp;&nbsp;</td>";
				} else {
					$dummyString .= "</tr>";
				}
				$count++;
			} else if( $this->haveBuilding( $buildingID ) ) {
				$dummyString .= "<td>".$this->showIllegalBuilding( $buildingID )."</td>";
				if( $count%2 == 0 ) {
					$dummyString = "<tr>".$dummyString."<td>&nbsp;&nbsp;&nbsp;</td>";
				} else {
					$dummyString .= "</tr>";
				}
				$count++;
			}
		}
		if( $count%2 ) {
			$dummyString .= "</tr>";
		}
		$resultString .= $dummyString;
		$resultString .= "</table>";
		
		$resultString .= "\n
			<table align='center' border='1' bordercolor='#FFEECC' cellspacing='1' cellpadding='2'>
				<tr>
					<td class='buildings'>Gold cost: </td>
					<td class='buildings'>
						<input class='readOnly' type='text' size='8' width='8' name='totalGoldCost' readOnly='1' value='0'>
					</td>
					<td class='buildings'>Current gold:</td>
					<td class='buildings'>
						<input class='readOnly' type='text' size='8' width='8' name='totalGold' readOnly='1' value='".$this->provinceObj->gold."'>
					</td>
					<td class='buildings'>Gold after building:</td>
					<td class='buildings'>
						<input class='readOnly' type='text' size='8' width='8' name='leftGold' readOnly='1' value='".$this->provinceObj->gold."'>
					</td>
				</tr>
				<tr>
					<td class='buildings'>Metal cost:</td>
					<td class='buildings'>
						<input class='readOnly' type='text' size='8' width='8' name='totalMetalCost' readOnly='1' value='0'>
					</td>
					<td class='buildings'>Current metal:</td>
					<td class='buildings'>
						<input class='readOnly' type='text' size='8' width='8' name='totalMetal' readOnly='1' value='".$this->provinceObj->metal."'>
					</td>
					<td class='buildings'>Metal after building:</td>
					<td class='buildings'>
						<input class='readOnly' type='text' size='8' width='8' name='leftMetal' readOnly='1' value='".$this->provinceObj->metal."'>
					</td>
				</tr>
				<tr>
					<td class='buildings'>Buildings to build:</td>
					<td class='buildings'>
						<input class='readOnly' type='text' size='8' width='8' name='totalAcresCost' readOnly='1' value='0'>
					</td>
					<td class='buildings'>Current free acres:</td>
					<td class='buildings'>
						<input class='readOnly' type='text' size='8' width='8' name='totalAcres' readOnly='1' value='$currentFreeAcres'>
					</td>
					<td class='buildings'>Free acres after building:</td>
					<td class='buildings'>
						<input class='readOnly' type='text' size='8' width='8' name='leftAcres' readOnly='1' value='$currentFreeAcres'>
					</td>
				</tr>
			</table>
			
			<table align='center'>
				<tr>
					<td>
						\n\n<br><input type='submit' class='form' name='buildSubmit' value='Start building' title='Click to build the selected buildings'>
					</td>
					<td>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					</td>
					<td>
						\n<br><input type='reset' class='form' title='Click to reset the form' value='Reset'>
					</td>
				</tr>
			</table>";
		$resultString .= "\n</form>";
		$resultString .= "<br><center><img src='../img/castle.jpg'></img></center>";
		return $resultString;
	}	

	////////////////////////////////////////////
	// Buildings::showAllBuildingsNobuild
	////////////////////////////////////////////
	// Function to display a table with information 
	// about all building types. Also shows calculations
	// and buttons to build buildings.
	// Returns:
	//		a string with the html code
	////////////////////////////////////////////
	function showAllBuildingsNoBuild() {
		$currentFreeAcres = ($this->provinceObj->acres-$this->totBuild);
		$bIDs = array_keys( $this->buildings );
		$self = $_SERVER['PHP_SELF'];
		$count = 0;
		$resultString = "\n<br><br>";
		$resultString .= "";
		
		$resultString .="<table cols='3' align='center' border='0' cellspacing='0' cellpadding='0'>";
		$dummyString = "";
		foreach( $bIDs as $buildingID ) {
			if( $this->haveRequirements( $buildingID ) ) {		// don't show buildings the player can't build or has reached maximum number of
				$dummyString .= "<td>".$this->showBuildingNoBuild( $buildingID )."</td>";
				if( $count%2 == 0 ) {
					$dummyString = "<tr>".$dummyString."<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
				} else {
					$dummyString .= "</tr>";
				}
				$count++;
			} else if( $this->haveBuilding( $buildingID ) ) {
				$dummyString .= "<td>".$this->showIllegalBuilding( $buildingID )."</td>";
				if( $count%2 == 0 ) {
					$dummyString = "<tr>".$dummyString."<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
				} else {
					$dummyString .= "</tr>";
				}
				$count++;
			}
		}
		if( $count%2 ) {
			$dummyString .= "</tr>";
		}
		$resultString .= $dummyString;
		$resultString .= "</table>";
		
		$resultString .= "\n";
		return $resultString;
	}	

	
	////////////////////////////////////////////
	// Buildings::showDestroy
	////////////////////////////////////////////
	// Function to display a table with input fields
	// and buttons to let the user destroy buildings.
	// Returns:
	//		a string with the html code
	////////////////////////////////////////////
	function showDestroy() {
		$bIDs = array_keys( $this->buildings );
		$self = $_SERVER['PHP_SELF'];
		$resultString = "\n<br><form class='form' action='$self' method='post' target='_self' name='Destroy'> ";
		$resultString .= $GLOBALS['fcid_post'];		
		$resultString .= "\n
			<table align='center'>
				<tr>
					<td>Destroy &nbsp;</td>
					<td>
						<input type='text' name='noToDestroy' class='form' size='6' maxlength='4' title='Choose number of buildings to destroy'>
					</td>
					<td>&nbsp; of &nbsp;</td>
					<td>
						<select name='buildingType' class='form'>";
						foreach( $this->buildings as $building ) {
							$bID = $building->baseFunction( $GLOBALS['BuildingConst']->GET_ID );
							if( $this->haveBuilding( $bID ) ) {
								$name = $building->baseFunction( $GLOBALS['BuildingConst']->GET_NAME );
								$resultString .= "<option value='$bID'>$name</option>";
							}
						}
		$resultString .= "
						</select>
					</td>
					<td>building(s)</td>
					<td>
						<input type='submit' name='destroySubmit' class='form' class='form' value='Destroy building(s)' title='Click to destroy the selected buildings'>
					</td>
			</tr>
		</table>";
		
		$resultString .= "\n</form>";
		$resultString .= "<br><center><img src='../img/hor_ruler.gif'></img></center>";
		return $resultString;

	}
	////////////////////////////////////////////
	// Buildings::showInProgress
	////////////////////////////////////////////
	// Function to display a table with information about 
	// all buildings which the current province have in 
	// progress.
	// Returns:
	//		a string with the html code
	////////////////////////////////////////////
	function showInProgress() {
		$acres = $this->provinceObj->acres;
		$maxProgressTime = 24;
		$cols = $maxProgressTime+2;
		$td = "<td  class='buildings' width='30'>";
		// go through all buildings in progress
		$resultString = "\n<br><div align='center'><h1>Buildings in progress:</h1></div>\n
						<br><table class='buildingsTable' align='center' cols='$cols' width='90%' border='0' cellspacing='1' cellpadding='2'>";
			$resultString .= "\n\t<tr>\n\t\t<td class='buildings'>Building&nbsp;</td>";
			for( $count = 0; $count <= $maxProgressTime; $count ++ ) {
				$resultString .= "\n\t\t<td class='buildings'>$count</td>";
			}
			$resultString .= "\n\t\t<td class='buildings'>Total</td>";
			$resultString .= "\n\t</tr>\n\t<tr>";
		$lastTick = 0;
		$bID = 0;
	
		if( isset( $this->progress ) ) {
			foreach( $this->progress as $buildProg ) {
				//if this is the first building, and the first time
				if( $bID == 0 ) {
					$bID = $buildProg['bID'];
					// add the first buildingtype
					$name = $this->buildings[ $bID ]->baseFunction( $GLOBALS['BuildingConst']->GET_NAME );
					$resultString .= "\n\t\t<td class='buildings'><a href='#' onClick=\"return showDescription('$bID','$acres','".$this->getNumBuilding( $bID )."');\">$name</a></td>";
			
				} else if( $buildProg['bID'] != $bID ){			// if it's a new buildingtype			
					// add the rest of the <td></td> tags to print total buildings in progress
					// from the last position to position $maxProgressTime
					for( $lastTick; $lastTick <= $maxProgressTime; $lastTick++ ){
						$resultString .= "\n\t\t$td &nbsp;</td>";
					}
					$lastTick = 0;			
					// print the total buildings in progress of this building type
					$totProBuild = $this->getTotProgress( $bID );
					$resultString .= "\n\t\t<td class='buildings'>$totProBuild</td>\n\t</tr>";
				
					$bID = $buildProg['bID'];
					// add a new buildingtype
					$name = $this->buildings[ $bID ]->baseFunction ( $GLOBALS['BuildingConst']->GET_NAME );
					$resultString .= "\n\t<tr>\n\t\t<td class='buildings'><a href='#' onClick=\"return showDescription('$bID','$acres','".$this->getNumBuilding( $bID )."');\">$name</a></td>";
				}
				// for a little while, while the serverscript is running, there might be buildings with 0 ticks.
				// If we only print when ticks != 0, there might be buildings not showing in neither progress or
				// already built info, some 'missing buildings' and if the player don't know this, he/she might
				// build new buildings without knowing they're there, and thet's a bigger problem.
				for( ; $lastTick < $buildProg['ticks']; $lastTick++ ){
					$resultString .= "\n\t\t$td &nbsp;</td>";
				}
				$noToBuild = $buildProg['noToBuild'];			
				$resultString .= "\n\t\t$td $noToBuild</td>";
				$lastTick = $buildProg['ticks']+1;
			} // end foreach
		
			//print the last total number of buildings in progress
			for( ; $lastTick <= $maxProgressTime; $lastTick++ ){
				$resultString .= "\n\t\t$td &nbsp;</td>";
			}
			$totProBuild = $this->getTotProgress( $bID );
			$resultString .= "\n\t\t<td class='buildings'>$totProBuild</td>\n\t</tr>";
			$resultString .= "\n\t</table><br>";
		}
		return $resultString;		
	}	
	////////////////////////////////////////////
	// Buildings::getBuildingNetworth
	////////////////////////////////////////////
	// Returns:
	//		networth value of all the buildings
	// NOTE! networth definitions can be found in all.inc
	////////////////////////////////////////////
	function getBuildingNetworth() {
		$buildingNetworth =	($this->totBuild*NW_BUILDING);
		return $buildingNetworth;
	}
} // end class Buildings
}// end if( !class_exists() )
?>