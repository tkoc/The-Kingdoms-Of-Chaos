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

if( !class_exists( "ResultsBuilder" ) ) {
require_once( $GLOBALS['path_www_scripts'] . "all.inc.php" );
require_once( $GLOBALS['path_www_scripts'] . "Province.class.inc.php" );

class ResultsBuilder {
	var $age = false;
	var $db;
	var $tmp = false;
	var $startAge = 1;
	var $chosenAge = 1;
	var $chosenSort = "networth";
	var $chosenType = "Province";
	
	function ResultsBuilder ( $db ) {		
		$this->db = $db;
		$this->setVariables();
	}
	
	/*
	 * Get the chosen results page
	 */
	function getResultPage() {			
		$page = "get".$this->chosenType."Contents";
		return $this->$page( $this->chosenSort, $this->chosenAge );
	}
	
	/*
	 * Save all results from the last age. 
	 * Backing up the database before doing anything with it.
	 * Then removing provinces and kingdoms.
	 * Then transfer results to the Results table
	 */
	function saveResults() {
		if( $this->age ) {
			$this->backup();
			$this->cleanUpProvinces();			
			$this->transferResults();
		}
	}	

	
	/*
	 * "private"
	 * Sets the current age, chosen age, chosen sorting and chosen type
	 */
	function setVariables() {
		$result = true;		
		if( isset( $GLOBALS['config'] ) ) {			
			$this->age = $GLOBALS['config']['age'];
		} else {
			$result = false;
		}
		if( isset( $_GET['type'] ) && isset( $_GET['sort'] ) && isset( $_GET['age'] ) ) {			
			if( ( $_GET['age'] >= $this->startAge ) &&  ( $_GET['age'] <= $this->age ) ) {
				$this->chosenAge = $_GET['age'];
			} else {
				die( "wrong age" );
			}
			if( !strcasecmp( $_GET['type'], "Province" ) ) {
				$this->chosenType="Province";
			} else if( !strcasecmp( $_GET['type'], "Kingdom" ) ) {
				$this->chosenType="Kingdom";
			} else {
				die( "wrong type" );
			}			
			if( !strcasecmp( $_GET['sort'], "networth" ) ) {
				$this->chosenSort = "networth";
			} else if( !strcasecmp( $_GET['sort'], "acres" ) ) {
				$this->chosenSort = "acres";
			} else if( !strcasecmp( $_GET['sort'], "thievery" ) ) {
				$this->chosenSort = "thievery";
			} else if( !strcasecmp( $_GET['sort'], "magic" ) ) {
				$this->chosenSort = "magic";
			} else {
				die( "wrong sort" );
			}
		}
		return $result;
	}
	
	/*
	 * "private"
	 * Making a backup of the database.
	 */
	function backup() {
		$backupFile = $GLOBALS['path_www_scripts'] . "results/chaos.resultBackup.".date("d.m.Y").".sql";
		echo "Backing up to :".$backupFile."\n";
		$dummy = array();
		$result = false;
		exec( "mysqldump --opt chaos > $backupFile", $dummy, $result);
		return $result;
	}
	
	/*
	 * "private"
	 * Deleting not-alive/non-login provinces and empty kingdoms from the database
	 */
	function cleanUpProvinces() {
		echo "Cleaning up by deleting not-alive/non-login provinces and empty kingdoms\n";
		$selectSQL = "SELECT pID, kiID 
									FROM  Province 
									WHERE (networth = '') 
										OR (networth <= 0) 
										OR (acres <= 0) 
										OR (status != 'Alive')";
		if( ( $result = $this->db->query( $selectSQL ) ) && $this->db->numRows( $result ) ) {
			while( $row = $this->db->fetchArray( $result ) ) {
				$deleteSQL = "DELETE FROM Province WHERE pID='".$row['pID']."'";
				$this->db->query( $deleteSQL );
				$updateSQL = "UPDATE User SET pID='-1', history=CONCAT(history, '\n<br>Province deleted by ResultsBuilder class ".date("d.m.Y").".') WHERE pID='".$row['pID']."'";
				$this->db->query( $updateSQL );
				$updateSQL = "UPDATE Kingdom SET numProvinces=numProvinces-1 WHERE kiID='".$row['kiID']."'";
				$this->db->query( $updateSQL );
			}
		}
		$deleteSQL = "DELETE FROM Kingdom WHERE numProvinces <= 0";
		$this->db->query( $deleteSQL );
	}
	
	/*
	 * "private"
	 * Transferring all results to the Results table
	 */
	function transferResults() {
		echo "Saving the results: \n";
		$counter = 0;
		$selectSQL = "SELECT 	U.userID, U.userName,
													P.pID 
									FROM User U, Province P 
									WHERE U.pID=P.pID";
		if( ( $result = $this->db->query( $selectSQL ) ) && $this->db->numRows( $result ) ) {
			while( $row = $this->db->fetchArray( $result ) ) {							
				$tempProvince = new Province( $row['pID'], $this->db );
				$tempProvince->getProvinceData();
				$tempProvince->setNetworth();
				$insertSQL = "INSERT INTO Results 
												( age, pID, userID, userName, 
													provinceName, rulerName, acres, networth, 
														thieveryRank, thieveryPoints,
														magicRank, magicPoints,
													kiID, kingdomName ) 
											VALUES 
												( '".$this->age."', '".$tempProvince->getpID()."', '".$row['userID']."', '".addslashes($row['userName'])."', 
													'".addslashes($tempProvince->provinceName)."', '".addslashes($tempProvince->rulerName)."', '".$tempProvince->acres."', '".round($tempProvince->networth)."', 
														'".$tempProvince->getThieveryRank()."', '".$tempProvince->reputation."',
														'".$tempProvince->getMagicRank()."', '".$tempProvince->magicReputation."',														
													'".$tempProvince->getkiID()."', '".addslashes($tempProvince->kingdomName)."')";
				$this->db->query( $insertSQL );
				echo "\r".$counter++;
			}
		}
		echo "\n";
	}	
	
	/*
	 * "private"
	 * returns the mysql result of provinces ordered in the given order
	 */
	function getProvinceResults( $sort, $age ) {
		$tmpSort = array( "acres" 		=> "acres DESC, networth DESC",
											"networth"	=> "networth DESC, acres DESC",
											"thievery" 	=> "thieveryPoints DESC",
											"magic"			=> "magicPoints DESC" );
		$selectSQL = 	"SELECT provinceName, acres, networth, kiID, thieveryRank, magicRank 
									FROM Results 
									WHERE age='".$age."' 
									ORDER BY ".$tmpSort[$sort];
		return $this->db->query( $selectSQL );
	}
	
	/*
	 * "private"
	 * returns the mysql result of kingdoms ordered in the given order
	 */
	function getKingdomResults( $sort, $age ) {
		$tmpSort = array( "acres" 		=> "acres DESC, networth DESC",
											"networth"	=> "networth DESC, acres DESC",
											"thievery"	=> "thieveryPoints DESC",
											"magic"			=> "magicPoints DESC");
		$selectSQL = 	"SELECT kingdomName, kiID, SUM(acres) as acres, SUM(networth) as networth, 
										SUM(thieveryPoints) as thieveryPoints, SUM(magicPoints) as magicPoints,
										COUNT(pID) as provinces 
									FROM Results
									WHERE age='".$age."' 
									GROUP BY kiID 
									ORDER BY ".$tmpSort[$sort];
		return $this->db->query( $selectSQL );
	}
	
	/*
	 * "private"
	 * puts the province results into the template html pages
	 */
	function getProvinceContents( $sort, $age ) {
		$data = $this->getProvinceResults( $sort, $age );
		
		$counter = 1;
		while( $row = $this->db->fetchArray( $data ) ) {
			if( $counter == 1 ) {
				
			}
				
		}
		return 1;
	}
	
	/*
	 * "private"
	 * puts the kingdom results into the template html pages
	 */
	function getKingdomContents( $sort, $age ) {
		$data = $this->getKingdomResults( $sort, $age );
		
		$counter = 1;
		while( $row = $this->db->fetchArray( $data ) ) {
			if( $counter == 1 ) {
				//$this->tmp->addVariable( "KINGDOM_RESULT", array( 	"winnerName" 	=> $row['kingdomName'] ) );
			}
			//$this->tmp->addVariable( "KINGDOM_RESULT", array( "number" => $counter++,
				
		}
		//return $this->tmp->getHTML( "KINGDOM_RESULT" );
	}
	
	
}

if( isset($_GET['doSave']) ) {
$rb = new ResultsBuilder( $database );
$rb->saveResults();
}

}
?>
