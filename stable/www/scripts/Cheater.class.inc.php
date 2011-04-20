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

/* Cheater class to handle cheaters, warning them, warning admins and deleting them
 *
 * Author: Øystein Fladby 07.05.2004
 * ChangeLog:
 *
 * Version: test
 * 
 */

if( !class_exists( "Cheater" ) ) {

class Cheater {
	//CONSTANTS
	var $NORMAL_MODE = "NORMAL"; 	
	var $INCONSPICIOUS_MODE = "INCONSPICIOUS";
	var $QUICK_MODE = "QUICK";
	var $SUSPICIOUS_MODE = "SUSPICIOUS";
	var $PARANOID_MODE = "PARANOID";

	var $PATH_TO_CHEATER_FILES = "cheater/";
	var $SUSPICIOUS_FILE = "suspicious.dat";
	var $IP_FILE = "suspiciousIP.dat";
	var $C_ID_FILE = "suspiciousComputerID.dat";

	//Other
	var $db;	
	var $modes = array( "INCONSPICIOUS"=> 10, "QUICK" => 8, "NORMAL" => 6, "SUSPICIOUS" => 4, "PARANOID" => 2 );
	var $mode = false;				// all computers with this many or more provinces are suspicious	
	var $suspicious = array();
	var $ipList = array();
	var $computerNameList = array();
	var $summary = "";
	var $timer = array( "start" => 0, "stop" => 0);
	var $files = array();
					
	/*
	 * Just set up the class and execute main functions
	 */
	function Cheater( &$db, $mode=false ) {		
		echo "Starting cheater class: ".$this->timer( "START" )."\n<br>";
		$this->db = &$db;
		echo "Database set\n<br>";
		$this->fileHandler( "OPEN", $this->SUSPICIOUS_FILE, "w" );
		$this->fileHandler( "WRITE", $this->SUSPICIOUS_FILE, "Cheater script run: ".$this->timer( "GET_START" )."<br><br>\n\n");			
		$mode ? $this->mode = $this->modes[$mode] : $this->mode = $this->modes["NORMAL"];
		$dummyArray = array_flip($this->modes);
		echo "Mode set to ".$dummyArray[$this->mode]."\n<br>";
		$this->findSuspicious();
		$this->checkSuspicious();
		$this->writeProvinces();
		$this->fileHandler( "WRITE",$this->SUSPICIOUS_FILE, "Cheater script finished: ".$this->timer( "STOP" )." in ".$this->timer( "GET_RUNNING_TIME" )."<br><br>\n\n");
		$this->fileHandler( "CLOSE", $this->SUSPICIOUS_FILE );
		echo "Cheater class finished: ".$this->timer( "GET_STOP" )." (".$this->timer( "GET_RUNNING_TIME" ).")\n<br>";
		echo "Ending cheater class\n<br>";
	}	
	
	/*
	 * Function to open, write to and close files
	 * $command = OPEN, CLOSE, WRITE or GET_HANDLE
	 * $argument1 = filename
	 * $argument2 = access permission (only in OPEN command) or text to write (only in WRITE command)
	 */
	function fileHandler( $command=false, $argument1=false, $argument2=false ) {
		$result = false;
		if( $argument1 && $command ) {
			if( !strcmp( $command, "OPEN" ) ) {
				if( $argument2 ) {
					$result = @fopen( $this->PATH_TO_CHEATER_FILES.$argument1, $argument2 );
				} else {
					$result = @fopen( $this->PATH_TO_CHEATER_FILES.$argument1, 'a+' );
				}
				if( !$result ) {
					die( "Couldn't open or create file for writing<br>\n" );
				} 
				$this->files[$argument1] = $result;
				$this->fileHandler( "WRITE", $argument1, "\n<br><br>\nFile opened: ".date("d.m.Y H:i:s")."\n<br><br>\n" );
				echo "Opened file ($argument2): $argument1\n<br>";
			} else if( !strcmp( $command, "CLOSE" ) ) {
				$this->fileHandler( "WRITE", $argument1, "\n<br><br>\nFile closed: ".date("d.m.Y H:i:s")."\n<br><br>\n" );
				$result = @fclose( $this->files[$argument1] );
				echo "Closed file: $argument1\n<br>";
			} else if( !strcmp( $command, "WRITE" ) ) {
				$result = @fwrite( $this->files[$argument1], $argument2 );
			} else if( !strcmp( $command, "GET_HANDLE" ) ) {
				$result = $this->files[$argument1];
			} 
		} 
		return $result;
	}
	
	/*
	 * Function to time this script
	 * takes START, STOP, GET_START, GET_STOP and GET_RUNNING_TIME as commands
	 */
	function timer( $command=false ) {
		$result = false;
		if( !strcmp( $command, "START" ) ) {
			$this->timer["start"] = time();
			$result = $this->timer( "GET_START" );
		} else if( !strcmp( $command, "STOP" ) ) {
			$this->timer["stop"] = time();
			$result = $this->timer( "GET_STOP" );
		} else if( !strcmp( $command, "GET_START" ) ) {
			$result = date( "d-m-Y H:i:s", $this->timer['start'] );
		} else if( !strcmp( $command, "GET_STOP" ) ) {
			$result = date( "d-m-Y H:i:s", $this->timer['stop'] );
		} else if( !strcmp( $command, "GET_RUNNING_TIME" ) ) {
			$end = ( $this->timer["stop"] ? $this->timer["stop"] : time() );
			$result = date("H:i:s", mktime( 	( date("H", $end)- date("H", $this->timer["start"]) ),
																				( date("i", $end)- date("i", $this->timer["start"]) ),
																				( date("s", $end)- date("s", $this->timer["start"]) ), 
																				date("m", $this->timer["start"]),
																				date("d", $this->timer["start"]),
																				date("Y", $this->timer["start"]) ) );
		} else {
			$result = "";
		}
		return $result;
	}
	
	/*
	 * Ecexute functions to quickly find suspicious provinces / users
	 */
	function findSuspicious() {		
		echo "Finding suspicious users\n<br>";
		$this->sameComputerID();
		$this->sameIP();
		ksort( $this->suspicious );
		echo "Done finding suspicious users\n<br>";		
	}
	
	/*
	 * Execute more thoroughly checking functions for all suspicious provinces
	 */
	function checkSuspicious() {
		echo "Checking suspicious users\n<br>";
		$this->compareUsersOnSameComputerOrIP();
		echo "Done checking suspicious users\n<br>";
	}
	
	
	
		
	/**************************************************/
	/* FUNCTIONS TO CHECK SUSPICIOUS USERS						*/
	/**************************************************/
	
	/*
	 * Adds messages between provinces to the evidence
	 * Adds certain news to evidence
	 */
	function getMoreEvidence( $firstProvince, $secondProvince ) {
		$moreSQL = "SELECT message, toID, fromID 
								FROM Message  
								WHERE ( toID='".$firstProvince["pID"]."' AND fromID='".$secondProvince["pID"]."')
									OR ( toID='".$secondProvince["pID"]."' AND fromID='".$firstProvince["pID"]."')" ;
		if( ($moreResult = $this->db->query( $moreSQL ) ) && $this->db->numRows( $moreResult ) ) {
			while( $more = $this->db->fetchArray( $moreResult ) ) {
				$this->suspicious[$firstProvince["userID"]]->addEvidence( 
					"message", array( "to" => $more['toID'], "from" => $more['fromID'], "message" => strtolower( preg_replace( "/(\n)|(<br>)|(<br \/>)/", " ", trim( $more['message'] ) ) ) ) );
				$this->suspicious[$secondProvince["userID"]]->addEvidence( 
					"message", array( "to" => $more['toID'], "from" => $more['fromID'], "message" => strtolower( preg_replace( "/(\n)|(<br>)|(<br \/>)/", " ", trim( $more['message'] ) ) ) ) );
			}
		}
		$moreSQL = "SELECT info FROM NewsProvince WHERE pID='".$firstProvince["pID"]."'";
		if( ($moreResult = $this->db->query( $moreSQL ) ) && $this->db->numRows( $moreResult ) ) {
			while( $more = $this->db->fetchArray( $moreResult ) ) {
				$info = $more['info'];
				if( strstr( $info, "#".$secondProvince["kiID"] ) || 
						strstr( $info, $secondProvince["provinceName"] ) ||
						strstr( $info, "thieves" ) ) {					
					$this->suspicious[$firstProvince["userID"]]->addEvidence( "news", array( "otherUserID" => $secondProvince["userID"], "news" => strtolower( preg_replace( "/(\n)|(<br>)|(<br \/>)/", " ", trim(  $info ) ) ) ) );
				}
			}
		}
		$moreSQL = "SELECT info FROM NewsProvince WHERE pID='".$secondProvince["pID"]."'";
		if( ($moreResult = $this->db->query( $moreSQL ) ) && $this->db->numRows( $moreResult ) ) {
			while( $more = $this->db->fetchArray( $moreResult ) ) {
				$info = $more['info'];
				if( strstr( $info, "#".$firstProvince["kiID"] ) || 
						strstr( $info, $firstProvince["provinceName"] ) ||
						strstr( $info, "thieves" ) ) {					
					$this->suspicious[$secondProvince["userID"]]->addEvidence( "news", array( "otherUserID" => $firstProvince["userID"], "news" => strtolower( preg_replace( "/(\n)|(<br>)|(<br \/>)/", " ", trim(  $info ) ) ) ) );
				}
			}
		}
	}
	
	/*
	 *	Function to compare two users
	 * 	takes two userID's
	 * 	Must run $this->getUserDetails( $evidenceType, $checkArray) 
	 *  or one or both of sameComputerID() and sameIP() first!
	 */
	function compareTwoUsers( $firstUser, $secondUser ) {
		$firstDetails = $this->suspicious[$firstUser]->getAllDetails();
		$secondDetails = $this->suspicious[$secondUser]->getAllDetails();
		$this->getMoreEvidence( $firstDetails, $secondDetails );		
		while( list( $firstDetailKey, $firstDetailValue ) = each( $firstDetails ) ) {
			if( strcmp( $firstDetailKey, "history" ) ) {
				reset( $secondDetails );
				while( list( $secondDetailKey, $secondDetailValue ) = each( $secondDetails ) ) {
					if( strcmp( $firstDetailValue, $secondDetailValue ) ) {
						if( !is_numeric( $firstDetailValue ) && !is_numeric( $secondDetailValue ) && strcmp( $secondDetailKey, "history" ) ) {						
							$firstLength = strlen( $firstDetailValue );
							$secondLength = strlen( $secondDetailValue );
							if( ( $firstLength < 255 ) && ( $secondLength < 255 ) ) {
								$lev = levenshtein( $firstDetailValue, $secondDetailValue, 1, 2, 1 ) ;										
								if( ( $lev < $firstLength ) && ( $lev < ( $this->modes["INCONSPICIOUS"] - $this->mode + round( $firstLength / $this->mode ) ) ) ) {
									$this->suspicious[$firstUser]->addEvidence( 
										"details", array( $firstDetailKey => $lev, 
										"otherUserID" => $secondUser, 
										"otherField" => $secondDetailKey ) );											
								}
								if( ( $lev < $secondLength ) && ( $lev < ( $this->modes["INCONSPICIOUS"] - $this->mode + round( $secondLength / $this->mode ) ) ) ) {
									$this->suspicious[$secondUser]->addEvidence( 
										"details", array( $secondDetailKey => $lev, 
										"otherUserID" => $firstUser, 
										"otherField" => $firstDetailKey ) );											
								}
								if( strlen($firstDetailValue) && strlen( $secondDetailValue ) && (
										strstr( $firstDetailValue, $secondDetailValue ) || 
										strstr( $secondDetailValue, $firstDetailValue ) ) ) {
									$this->suspicious[$firstUser]->addEvidence( 
										"details", array( $firstDetailKey => "substring", 
										"otherUserID" => $secondUser, 
										"otherField" => $secondDetailKey ) );
									$this->suspicious[$secondUser]->addEvidence( 
										"details", array( $secondDetailKey => "substring", 
										"otherUserID" => $firstUser, 
										"otherField" => $firstDetailKey ) );
								}
							}									
						} 
					} else if( 	strcmp( $firstDetailKey, "provinceCreated" ) &&
											strcmp( $firstDetailKey, "userCreated" ) ){
						$this->suspicious[$firstUser]->addEvidence( 
									"details", array( $firstDetailKey => "same", 
									"otherUserID" => $secondUser, 
									"otherField" => $secondDetailKey ) );
						$this->suspicious[$secondUser]->addEvidence( 
									"details", array( $secondDetailKey => "same", 
									"otherUserID" => $firstUser, 
									"otherField" => $firstDetailKey ) );								
					}
				}
			}
		}
	}
	
	/*
	 *	Function to compare Users which use the same ip/computerName
	 * 	Must run $this->getUserDetails( $evidenceType, $checkArray) 
	 *  or one or both of sameComputerID() and sameIP() first!
	 */
	function compareUsersOnSameComputerOrIP() {
		$userList = array();
		$computerCount = 0;
		echo "Checking computers ";
		while( list( $computerName, $computer ) = each( $this->computerNameList ) ) {
			$computerCount++;
			echo ".";
			while( ( $firstUser = array_shift( $computer ) ) ) {				
				foreach( $computer as $secondUser ) {
					isset( $userList[$firstUser['userID']] ) ? "" : $userList[$firstUser['userID']] = array();
					isset( $userList[$secondUser['userID']] ) ? "" : $userList[$secondUser['userID']] = array();
					if( !in_array( $secondUser['userID'], $userList[$firstUser['userID']] ) ) {						
						array_push( $userList[$secondUser['userID']], $firstUser['userID'] );
						array_push( $userList[$firstUser['userID']], $secondUser['userID'] );
						$this->compareTwoUsers( $firstUser['userID'], $secondUser['userID'] );
					}
				}
			}			
		}
		echo "$computerCount\n<br>";
		$ipCount = 0;
		echo "Checking ip's ";
		while( list( $ip, $computer ) = each( $this->ipList ) ) {
			$ipCount++;
			echo ".";
			while( ( $firstUser = array_shift( $computer ) ) ) {				
				foreach( $computer as $secondUser ) {
					isset( $userList[$firstUser['userID']] ) ? "" : $userList[$firstUser['userID']] = array();
					isset( $userList[$secondUser['userID']] ) ? "" : $userList[$secondUser['userID']] = array();
					if( !in_array( $secondUser['userID'], $userList[$firstUser['userID']] ) ) {						
						array_push( $userList[$secondUser['userID']], $firstUser['userID'] );
						array_push( $userList[$firstUser['userID']], $secondUser['userID'] );
						$this->compareTwoUsers( $firstUser['userID'], $secondUser['userID'] );
					}
				}
			}			
		}
		echo "$ipCount\n<br>";
	}
	
	
	
	
	
	/**************************************************/
	/* FUNCTIONS TO FIND SUSPICIOUS USERS							*/
	/**************************************************/
	
	/*******
	 *	Function to get relevant details for users using the given ip/computerName
	 *******
	 *  takes type of evidence (ip/computerName) 
	 *	and an array with suspicious ID's (actual ip's or computerNames)
	 */
	function getUserDetails( $evidenceType, $checkArray ) {
		$cIDcounter = 0;		
		foreach( $checkArray as $eID ) {
			$selectSQL = "SELECT 	distinct L.userID, 
														U.name as realName, U.password, U.username, 
														U.email, UNIX_TIMESTAMP(U.dob) as dob, U.country, UNIX_TIMESTAMP(U.created) as userCreated, U.history, 
														P.pID, P.provinceName, P.rulerName, UNIX_TIMESTAMP(P.created) as provinceCreated,
														K.kiID, K.name as kingdomName 
										FROM User U, Province P, Kingdom K, Login L 
										WHERE U.pID=P.pID AND P.kiID=K.kiID AND U.userID=L.userID AND L.".$evidenceType."='".$eID."'";
			if( ( $usersResult = $this->db->query( $selectSQL ) ) && ( $users = $this->db->numRows( $usersResult ) ) ) {
				while( $details = $this->db->fetchArray( $usersResult ) ) {
					$userID = $details['userID'];
					$listToUse = $evidenceType."List";
					array_push( $this->{$listToUse}[$eID], array( $evidenceType => $eID, "userID" => $userID, "users" => $users ) );
					if( !isset( $this->suspicious[$userID] ) ) {
						$this->suspicious[$userID] = new UserEvidence( $userID );
						$this->suspicious[$userID]->addDetails( array( 
						"userID" => $userID,
						"realName" => strtolower( trim($details['realName'] ) ),
						"password" => strtolower( trim( $details['password'] ) ),
						"username" => strtolower( trim( $details['username'] ) ),
						"email" => strtolower( trim( $details['email'] ) ),
						"dob" => $details['dob'],
						"country" => strtolower( trim( $details['country'] ) ),
						"userCreated" => $details['userCreated'],
						"history" => strtolower( trim( $details['history'] ) ),
						"pID" => $details['pID'],
						"provinceName" => strtolower( trim( $details['provinceName'] ) ),
						"rulerName" => strtolower( trim( $details['rulerName'] ) ),
						"provinceCreated" => $details['provinceCreated'],
						"kiID" => $details['kiID'],
						"kingdomName" => strtolower( trim( $details['kingdomName'] ) ) ) );												
					}				
					$this->suspicious[$userID]->addEvidence( $evidenceType, array( $evidenceType => $eID, "users" => $users ) );
				}
				echo ".";
				$cIDcounter++;
			} 
		}
		return $cIDcounter;
	}
		
	/*
	 *	Function to find computers used by several users
	 */
	function sameComputerID() {		
		$cIDcounter = 0;
		$this->fileHandler( "OPEN", $this->C_ID_FILE );
		echo "Finding computerNames ";
		$tempTable = "CREATE TEMPORARY TABLE Suspicious AS 
										SELECT computerName, count( distinct userID ) as users 
										FROM Login 
										GROUP BY computerName";
		$dropTemp = "DROP TEMPORARY TABLE Suspicious";
		$selectSQL = "SELECT computerName, users 
									FROM Suspicious 
									WHERE users >= '".$this->mode."' 
									ORDER BY users DESC";			
		$this->db->query( $tempTable );
		if( ( $result = $this->db->query( $selectSQL ) ) && $this->db->numRows( $result ) ) {
			while( $row = $this->db->fetchArray( $result ) ) {
				$computerID = $row['computerName'];
				$this->fileHandler( "WRITE", $this->C_ID_FILE, "ComputerID: ".$computerID." :: Users: ".$row['users']."<br>\n" );				
				$this->computerNameList[$computerID] = array();
				$cIDcounter += $this->getUserDetails( "computerName", array( $computerID ) );			
			}
		}
		echo "$cIDcounter\n<br>";
		$this->db->query( $dropTemp );
		$this->fileHandler( "CLOSE", $this->C_ID_FILE );
	}
	
	/*
	 *	Function to find ip's used by several users
	 */
	function sameIP() {
		$IPcounter = 0;
		$this->fileHandler( "OPEN", $this->IP_FILE );
		echo "Finding ip's ";
		$tempTable = "CREATE TEMPORARY TABLE Suspicious AS 
										SELECT ip, count( distinct userID ) as users 
										FROM Login 
										GROUP BY ip";
		$dropTemp = "DROP TEMPORARY TABLE Suspicious";
		$selectSQL = "SELECT ip, users 
									FROM Suspicious 
									WHERE users >= '".$this->mode."' 
									ORDER BY users DESC";		
		$this->db->query( $tempTable );
		if( ( $result = $this->db->query( $selectSQL ) ) && $this->db->numRows( $result ) ) {
			while( $row = $this->db->fetchArray( $result ) ) {
				$IP = $row['ip'];
				$this->fileHandler( "WRITE", $this->IP_FILE, "ip: ".$IP." :: Users: ".$row['users']."<br>\n" );				
				$this->ipList[$IP] = array();
				$IPcounter += $this->getUserDetails( "ip", array( $IP ) );			
			}
		}
		echo "$IPcounter\n<br>";
		$this->db->query( $dropTemp );
		$this->fileHandler( "CLOSE", $this->IP_FILE );
	}
	
	
	
	
	/********************************************************/
	/* OTHER FUNCTIONS																			*/
	/********************************************************/
	
	/*
	 *	Function to sort the users based on number of evidence found
	 */
	function sortCompare( $user1, $user2 ) {
		$user1Value = $user1->getNoOfEvidence();
		$user2Value = $user2->getNoOfEvidence();
		if( $user1Value == $user2Value ) {
			return 0;
		}
		return ( $user1Value < $user2Value ) ? 1 : -1 ;
	}
	
	/*
	 * Write users with all evidence to file
	 */
	function writeProvinces() {		
		usort( $this->suspicious, array("Cheater", "sortCompare") );
		$userCount = 0;
		echo "Writing Provinces ";
		$html = "";
		foreach( $this->suspicious as $user ) {
			$html .= "<b>User ID: ".$user->getDetail("userID")."\n<br>";
			$html .= "Province ID: ".$user->getDetail("pID")."\n<br>";
			$html .= "Kingdom ID: ".$user->getDetail("kiID")."\n<br>";
			$html .= "Evidence: ".$user->getNoOfEvidence()."</b>\n<br>";
			$evidenceTypes = $user->getEvidenceTypes();
			foreach( $evidenceTypes as $type ) {
				$html .= "<b><i>&nbsp;&nbsp;Evidence type: $type</i></b>\n<br>";
				$count = 0;
				$evidence = $user->getEvidence( $type );
				foreach( $evidence as $evidencePair ) {
					$html .= "&nbsp;&nbsp;&nbsp;&nbsp;<b>".(++$count).":</b> ";
					while( list( $field, $value ) = each( $evidencePair ) ) {
						$html .= $field.": ".$value."  ::-::  ";
					}
					$html .= "\n<br>";
				}
			}
			$html .= "\n<br>";
			echo ".";
			$userCount++;
		}
		echo "$userCount\n<br>";
		$this->fileHandler( "WRITE", $this->SUSPICIOUS_FILE, $html );
	}
} // end class









/****************************************************************************************************/
/****************************************************************************************************/
/*************************** CLASS UserEvidence *****************************************************/
/****************************************************************************************************/
/****************************************************************************************************/
/* Class to store and handle user details and evidence for a single user														*/
/****************************************************************************************************/
class UserEvidence {
	var $userID = false;
	var $noOfEvidence = 0;
	var $userDetails = array();
	var $evidence = array();
	var $evidenceTypes = array( "ip" => "ip", 
															"computerName" => "computerName", 
															"details" => "details", 
															"message" => "message", 
															"news" => "news");

	/*
	 * constructor to set up evidence types
	 */
	function UserEvidence( $userID ) {
		$this->userDetails['userID'] = $userID;
		$this->userID = $userID;
		foreach( $this->evidenceTypes as $type ) {
			$this->evidence[$type] = array();
		}
	}
	
	/*
	 * add details for this user
	 */
	function addDetails( $key, $value=false ) {
		if( $value ) {
			$this->userDetails[$key] = $value;
			return true;
		} else if( is_array( $key ) ) {
			while( list( $myKey, $myValue ) = each( $key ) ) {
				$this->userDetails[$myKey]=$myValue;
			}
			return true;
		} else {
			return false;
		}
	}
	
	/*
	 * add evidence to this user
	 */
	function addEvidence( $type, $inArray ) {
		if( !isset( $this->evidence[$type] ) ) {
			echo "\n<br><br>\nEvidence type $type not found in class UserEvidence\n<br><br>\n";
		}
		$this->noOfEvidence++;
		array_push( $this->evidence[$type], $inArray );
	}
	
	/*
	 * get array with all evidence types
	 */
	function getEvidenceTypes() {
		return $this->evidenceTypes;
	}
	
	/*
	 * get array of all evidences of given type
	 */
	function getEvidence( $type ) {
		if( isset( $this->evidence[$type] ) ) {
			return $this->evidence[$type];
		} else {
			return false;
		}
	}

	/*
	 * get the given user detail
	 */
	function getDetail( $key ) {
		if( isset( $this->userDetails[$key] ) ) {
			return $this->userDetails[$key];
		} else {
			return false;
		}
	}
	
	/*
	 * get array with all user details
	 */
	function getAllDetails() {
		return $this->userDetails;
	}
	
	/*
	 * get the number of evidences for this user
	 */
	function getNoOfEvidence() {
		return $this->noOfEvidence;
	}	
}
} // end if !class exists
?>