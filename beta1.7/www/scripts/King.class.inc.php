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
/*************************************************************************
 * Race.class.inc.php - contains the Race class that the Province class should 
 * make one of. Contains functionality to get effects for each race.
 *
 * Made by Øystein Fladby 01.06.2003
 * Changelog:
 * Øystein	04.09.2003	- Added parameter pID to the getEffect and reqOk functions to enable the use
 *						of the Effect class
 * Oystein	11.09.2003	- Added doTick function
 *
 * Version: test
 *
 *************************************************************************/
if( !class_exists( "King" ) ) {
$GLOBALS['PATH_TO_KING'] = "king/";
require_once ("globals.inc.php");
require_once( $GLOBALS['path_www_scripts'] . $GLOBALS['PATH_TO_KING'] . "KingConstants.class.inc.php" );
$GLOBALS['KingConstants'] = new KingConstants();
class King {
	var $db;
	var $king = false;										// king object for this province
	var $province = false;									// object of this province
	
	function King( $db, $province ) {
		$this->db = $db;
		if( $province ) {
			$this->province = $province;
			if ( $this->province->isKing() );
				$this->setKing();
		}
	}

	function getAllRaces() {
		return $this->races;
	}
	
	function doTick() {
		foreach( $this->races as $race ) {
			$spID = $race->baseFunction( $GLOBALS['KingConstants']->GET_ID);
			$addGold = 1+ ( $race->baseFunction( $GLOBALS['KingConstants']->$ADD_GOLDKING_BONUS) / 100.0 );
			$addMetal = 1+ ( $race->baseFunction( $GLOBALS['KingConstants']->$ADD_FOODKING_BONUS ) / 100.0 );
			$addFood = 1+ ( $race->baseFunction( $GLOBALS['KingConstants']->$ADD_METALKING_BONUS ) / 100.0 );
			$updateSQL = 	"UPDATE Province 
					SET 	incomeChange  = (incomeChange*$addGold) ,
						metalChange = (metalChange*$addMetal) ,
						foodChange  = (foodChange*$addFood)
					WHERE spID LIKE '$spID'";
			$this->db->query( $updateSQL ) or die("Error in King.class.inc.php");
		}
		return "\rRace doTick OK!";
	}
	
	function setKing() {
		require_once( $GLOBALS['PATH_TO_KING']."KingBase.class.inc.php" );
		$this->king = new KingBase();
		return true;
	}
	
	////////////////////////////////////////////
	// Race::getEffect
	////////////////////////////////////////////
	// Function to get a given race effect of a province
	// Takes the function name (from RaceBase / EffectBase class )
	// and an optional race ID
	// Returns:
	//		A float between 0.00001 and 2 where 1 is no effect
	////////////////////////////////////////////
	function getEffect( $function, $pID=false ) {
		if( $pID ) {
			$selectSQL = "SELECT spID FROM Province WHERE pID LIKE '$pID'";
			if( ( $result = $this->db->query( $selectSQL ) ) && $this->db->numRows( $result ) ) {
				$row = $this->db->fetchArray( $result );
				$race = $this->races[ $row['spID'] ];
			}
		} else {
			$race = $this->race;
		}		
		$effect = 1.0;
		$effect += ( $race->$function() / 100.00 ); // always 0.0001 to 2 if race gain/reduction not above 100%
		return $effect;
	}
	
	////////////////////////////////////////////
	// Race::raceReqOk
	////////////////////////////////////////////
	// Function to find out if a player has the correct race
	// compared to the requirements. Takes an array of races 
	// or just one race as either ints or strings.
	// Returns:
	//		true or false
	////////////////////////////////////////////
	function raceReqOk( $race, $pID=false ) {
		$result = true;
		if( $race ) {
			if( $pID ) {
				$selectSQL = "SELECT spID FROM Province WHERE pID LIKE '$pID'";
				if( ( $result = $this->db->query( $selectSQL ) ) && $this->db->numRows( $result ) ) {
					$row = $this->db->fetchArray( $result );
					$this->rID = $row['spID'];
				}
			}
			$result = $this->races[ $this->rID ]->isRace( $race ); // checks arrays with strings / id's
		}
		return $result;
	}
} // end class Race
} // end if class exists
?>
