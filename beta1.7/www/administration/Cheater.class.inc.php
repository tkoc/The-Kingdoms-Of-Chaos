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
 * Version: 1.0
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

	var $sharedComputerThreadName = "Shared computer";

	//Other
	var $db;	
	var $modes = array( "INCONSPICIOUS"=> 15, "QUICK" => 10, "NORMAL" => 6, "SUSPICIOUS" => 4, "PARANOID" => 2 );
	var $mode = false;				// all computers with this many or more provinces are suspicious	
	var $timer = array( "start" => 0, "stop" => 0);
	
	//HTML
	var $html = "";
	var $topHtml = "";
	var $tableColors = array( "0" => "#DDDDDD", "1" => "#BBBBBB" );
	var $currentTableColor = 0;
					
	/*
	 * Just set up the class and execute main functions
	 */
	function Cheater( &$db ) {		
		$this->topHtml .=  "Starting cheater class: ".$this->timer( "START" )."<br>";
		$this->db = &$db;
		$this->topHtml .=  "Database set<br>";
		$this->init();
		$this->topHtml .=  "Cheater class finished: ".$this->timer( "STOP" )." (".$this->timer( "GET_RUNNING_TIME" ).")<br>";
		$this->topHtml .=  "Ending cheater class<br>";
	}	
	
	/*
	 * get the output formatted as html
	 */
	function getHtmlDisplay() {
		$this->topHtml = "<table>
										<tr>
											<td width='10%' valign='top'>".
												$this->getMenu()."
											</td>
										</tr>
										<tr>										
											<td>".
												$this->topHtml."
											</td>
										</tr>
										<tr>
											<td>".
												$this->html."
										</td>
									</tr>
								</table>";
		return $this->topHtml;
	}
	
	/*
	 * Function to get the menu for the cheater script
	 */
	function getMenu() {
		$html = "<table><tr>";
		$html .= "<td><a href='".$_SERVER['PHP_SELF']."?findSuspicious=yes'>Find</a></td>";
		$html .= "<td><a href='".$_SERVER['PHP_SELF']."?viewSuspicious=yes'>View</a></td>";
		$html .= "<td><a href='".$_SERVER['PHP_SELF']."?searchSuspicious=yes'>Search</a></td>";
		$html .= "<td>".$this->getChooseMode()."</td>";
		$html .= "<td><a href='".$_SERVER['PHP_SELF']."?howToUse=yes'>How to use this script</a></td>";
		$html .= "</tr></table>";
		return $html;
	}
	
	/*
	 * Function to init the Cheater class depending on the option selected by the admin through the 
	 * html interface
	 */
	function init() {
		$page = "";
		if( isset( $_POST['ChangeMode'] ) ) {
			$this->mode = $this->modes[ $_POST['mode'] ];
			$_SESSION['CheaterMode'] = $this->mode;
		} else {
			if( isset( $_SESSION['CheaterMode'] ) ) {
				$this->mode = $_SESSION['CheaterMode'];
			} else {
				$this->mode = $this->modes["NORMAL"];
				$_SESSION['CheaterMode'] = $this->mode;
			}
		}		
		$dummyArray = array_flip($this->modes);
		$this->topHtml .=  "Mode set to ".$dummyArray[$this->mode]."<br>";
		if( isset( $_POST['checkedComputerName'] ) ) {
			$this->setCheckedComputerName( $_POST['checkedComputerName'] );
		}
		if( isset( $_POST['checkedIP'] ) ) {
			$this->setCheckedIP( $_POST['checkedIP'] );
		}			
		if( isset( $_GET['findSuspicious'] ) ) {
			$this->topHtml .= $this->findSuspicious();	
			$page = "find";
		}
		if( isset( $_GET['viewSuspicious'] ) ) {
			$this->html .= $this->listSuspicious();
			$page = "view";
		}		
		if( isset( $_GET['searchSuspicious'] ) ) {
			$this->searchSuspicious();
			$page = "search";
		}
		if( isset( $_POST['SaveEvidence'] ) ) {
			if( isset( $_SESSION['userEvidence'][ $_POST['saveUserID'] ] ) ) {
				$this->saveEvidence( $_SESSION['userEvidence'][ $_POST['saveUserID'] ] );
			}
		}
		if( isset( $_POST['DeleteUser'] ) && isset( $_POST['reason'] ) ) {			
			$this->saveEvidence( $_SESSION['userEvidence'][ $_POST['deleteUserID'] ] );
			$this->deleteUser( $_POST['deleteUserID'], $_POST['reason'] );
		}
		
		if( !$page ) {
			$this->html .= $this->getHowToUse();
		}		
	}
	
	
	/********************************************/
	/********************************************/
	/***************** SEARCH *******************/
	/********************************************/
	/********************************************/
	
	/*
	 * Search for specific users, provinces or kingdoms
	 */
	function searchSuspicious() {
		$userIDs = array();
		$this->topHtml .= "Search for suspicious entries<br>";
		$this->topHtml .= "To search for more than one, use comma (,) to seperate each number<br>";		
		$this->topHtml .= "<table><tr><td nowrap>
											<form action='".$_SERVER['PHP_SELF']."' method='GET'>
												Province ID:<input type='hidden' name='searchSuspicious' value='yes'>
												</td><td><input type='text' name='pID' size='50'>
												</td><td><input type='Submit' value='Search'>
											</form>
										</td></tr>
										<tr><td nowrap>
											<form action='".$_SERVER['PHP_SELF']."' method='GET'>
												Kingdom ID:<input type='hidden' name='searchSuspicious' value='yes'>
												</td><td><input type='text' name='kiID' size='50'>
												</td><td><input type='Submit' value='Search'>
											</form>
										</td></tr>
										<tr><td nowrap>
											<form action='".$_SERVER['PHP_SELF']."' method='GET'>
												UserID:<input type='hidden' name='searchSuspicious' value='yes'>
												</td><td><input type='text' name='userID' size='50'>
												</td><td><input type='Submit' value='Search'>
											</form>
										</td></tr></table>";
		if( isset( $_GET['pID'] ) && ( $allpID = trim( $_GET['pID'] ) ) ) {
			$selectSQL = "SELECT distinct U.userID FROM User U WHERE U.pID IN (".$allpID.")";
			if( ( $pIDresult = $this->db->query( $selectSQL ) ) && $this->db->numRows( $pIDresult ) ) {
				while( $row = $this->db->fetchArray( $pIDresult ) ) {
					array_push( $userIDs, $row['userID'] );
				}
			}
		}
		if( isset( $_GET['kiID'] ) && ( $allkiID = trim( $_GET['kiID'] ) ) ) {
			$selectSQL = "SELECT distinct U.userID 
										FROM User U, Province P 
										WHERE P.kiID IN (".$allkiID.") 
											AND P.pID=U.pID";
			if( ( $kiIDresult = $this->db->query( $selectSQL ) ) && $this->db->numRows( $kiIDresult ) ) {
				while( $row = $this->db->fetchArray( $kiIDresult ) ) {
					array_push( $userIDs, $row['userID'] );
				}
			}
		}
		if( isset( $_GET['userID'] ) && ( $alluserID = trim( $_GET['userID'] ) ) ) {
			$selectSQL = "SELECT distinct U.userID FROM User U WHERE U.userID IN (".$alluserID.")"; 
			if( ( $userIDresult = $this->db->query( $selectSQL ) ) && $this->db->numRows( $userIDresult ) ) {
				while( $row = $this->db->fetchArray( $userIDresult ) ) {
					array_push( $userIDs, $row['userID'] );
				}
			}
		}
		if( isset( $_GET['computerName'] ) && ( $computerName = trim( $_GET['computerName'] ) ) ) {
			$this->topHtml .= "<form action='".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."' method='POST'>
										Mark computerName <b>".$computerName."</b> as checked?
										<input type='hidden' name='checkedComputerName' value='".$computerName."'>
										<input type='Submit' value='Yes'>
										</form>";
			$selectSQL = "SELECT distinct L.userID FROM Login L WHERE L.computerName IN ('".$computerName."')"; 
			if( ( $userIDresult = $this->db->query( $selectSQL ) ) && $this->db->numRows( $userIDresult ) ) {
				while( $row = $this->db->fetchArray( $userIDresult ) ) {
					array_push( $userIDs, $row['userID'] );
				}
			}
		}
		if( isset( $_GET['ip'] ) && ( $IP = trim( $_GET['ip'] ) ) ) {
			$this->topHtml .= "<form action='".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."' method='POST'>
										Mark IP <b>".$IP."</b> as checked?
										<input type='hidden' name='checkedIP' value='".$IP."'>
										<input type='Submit' value='Yes'>
										</form>";
			$selectSQL = "SELECT distinct L.userID FROM Login L WHERE L.ip IN ('".$IP."')"; 
			if( ( $userIDresult = $this->db->query( $selectSQL ) ) && $this->db->numRows( $userIDresult ) ) {
				while( $row = $this->db->fetchArray( $userIDresult ) ) {
					array_push( $userIDs, $row['userID'] );
				}
			}
		}
		if( count( $userIDs ) ) {
			$this->topHtml .= "Checking users...<br>";
			$_SESSION['userEvidence'] = $this->findUserDetails( $userIDs );
			$this->html .= $this->getUserEvidenceDisplay( $_SESSION['userEvidence'] );
		}		
	}
	
	
	
	/*
	 *	Function to find relevant details for users
	 * 	returns array of UserEvidence objects
	 */
	function findUserDetails( $userIDArray ) {
		$this->topHtml .= "Getting details...<br>";
		$userList = array();
		$checkString = "";
		if( count( $userIDArray ) ) {
			foreach( $userIDArray as $userID ) {
				if( is_numeric( $userID ) ) {
					$checkString .= ( $checkString ? ", " : "" ).$userID;
				}
			}
		}
		$selectSQL = "SELECT 	distinct U.userID, 
													U.name as realName, U.password, U.username, U.deletedReason,  
													U.email, UNIX_TIMESTAMP(U.dob) as dob, U.country, UNIX_TIMESTAMP(U.created) as userCreated, U.history, 
													P.pID, P.status, P.provinceName, P.rulerName, UNIX_TIMESTAMP(P.created) as provinceCreated,
													K.kiID, K.name as kingdomName 
									FROM User AS U LEFT JOIN Province AS P ON U.pID=P.pID LEFT JOIN Kingdom AS K ON P.kiID=K.kiID
									WHERE U.userID IN (".$checkString.")";
		if( ( $usersResult = $this->db->query( $selectSQL ) ) && ( $users = $this->db->numRows( $usersResult ) ) ) {
			while( $details = $this->db->fetchArray( $usersResult ) ) {
				$userID = $details['userID'];			
				$pID = ( isset( $details['pID'] ) ? $details['pID'] : "N/A" );
				$userList[$userID] = new UserEvidence( $userID );
				$userList[$userID]->addDetails( "userID", $userID, "U.userID", "User U",	"number",	false );
				$userList[$userID]->addDetails( "deletedReason", strtolower( trim( ( isset( $details['deletedReason'] ) ? $details['deletedReason'] : "N/A" ) ) ), "U.deletedReason", "User U", "string", false);
				
				$isInForum = "No";
				$selectForumThreadSQL = "SELECT ForumID FROM ForumMain WHERE ForumName='".$this->sharedComputerThreadName."'";
				if( ( $threadResult = $this->db->query( $selectForumThreadSQL ) ) && $this->db->numRows( $threadResult ) ) {
					$threadRow = $this->db->fetchArray( $threadResult );
					$selectUserInThreadSQL = "SELECT PostThreadID  
																FROM ForumPost 
																WHERE PostUserID='".$userID."' 
																	AND PostForumID='".$threadRow['ForumID']."'";
					if( ( $selectUserResult = $this->db->query( $selectUserInThreadSQL ) ) && $this->db->numRows( $selectUserResult ) ) {
						$isInForum = "";
						while( $userRow = $this->db->fetchArray( $selectUserResult ) ) {
							$isInForum .= ( $isInForum ? ", " : "" ).$userRow['PostThreadID'];
						}
						$isInForum = "<b>Yes</b> ( Check the ".$this->sharedComputerThreadName." forum thread: ".
														$isInForum." before deleting this user )";
					}
				}				
				$userList[$userID]->addDetails( "isInForum",  $isInForum , "userID", "ForumPost FP", "string", false);
				
				$userList[$userID]->addDetails( "realName", strtolower( trim( ( isset( $details['realName'] ) ? $details['realName'] : "N/A" ) ) ), "U.name", "User U", "string");
				$userList[$userID]->addDetails( "password", strtolower( trim( ( isset( $details['password'] ) ? $details['password'] : "N/A" ) ) ), "U.password", "User U", "string");
				$userList[$userID]->addDetails( "username", strtolower( trim( ( isset( $details['username'] ) ? $details['username'] : "N/A" ) ) ), "U.userName", "User U", "string");
				$userList[$userID]->addDetails( "email", strtolower( trim( ( isset( $details['email'] ) ? $details['email'] : "N/A" ) ) ), "U.email", "User U", "string");
				$userList[$userID]->addDetails( "pID", $pID, "P.pID", "Province P", "number", false);
				$userList[$userID]->addDetails( "provinceStatus", strtolower( trim( ( isset( $details['status'] ) ? $details['status'] : "N/A" ) ) ), "P.status", "Province P", "string", false);
				$userList[$userID]->addDetails( "provinceName", strtolower( trim( ( isset( $details['provinceName'] ) ? $details['provinceName'] : "N/A" ) ) ), "P.provinceName", "Province P", "string");
				$userList[$userID]->addDetails( "rulerName", strtolower( trim( ( isset( $details['rulerName'] ) ? $details['rulerName'] : "N/A" ) ) ), "P.rulerName", "Province P", "string");					
				$userList[$userID]->addDetails( "kiID", ( isset( $details['kiID'] ) ? $details['kiID'] : "N/A" ), "K.name", "Kingdom K", "number", false);
				$userList[$userID]->addDetails( "kingdomName", strtolower( trim( ( isset( $details['kingdomName'] ) ? $details['kingdomName'] : "N/A" ) ) ), "U.name", "User U", "string");
				$userList[$userID]->addDetails( "history", strtolower( trim( ( isset( $details['history'] ) ? $details['history'] : "N/A" ) ) ), "U.history", "User U", "textblock");
				$userList[$userID]->addDetails( "dob", ( isset( $details['dob'] ) ? $details['dob'] : "N/A" ), "U.dob", "User U", "date");
				$userList[$userID]->addDetails( "provinceCreated", ( isset( $details['provinceCreated'] ) ? $details['provinceCreated'] : "N/A" ), "P.created", "Province P", "date");
				$userList[$userID]->addDetails( "userCreated", ( isset( $details['userCreated'] ) ? $details['userCreated'] : "N/A" ), "U.created", "User U", "date");
				$userList[$userID]->addDetails( "country", strtolower( trim( ( isset( $details['country'] ) ? $details['country'] : "N/A" ) ) ), "U.country", "User U", "string", false);				
							
				$selectNewsSQL = "SELECT info FROM NewsProvince WHERE pID='".$pID."'";
				$news = "";
				if( ( $newsResult = $this->db->query( $selectNewsSQL ) ) && $this->db->numRows( $newsResult ) ) {
					while( $newsRow = $this->db->fetchArray( $newsResult ) ) {
						$news .= "<br>*****<br>".strtolower( trim( $newsRow['info'] ) );
					}
				}
				$userList[$userID]->addDetails( "news",  $news , "NP.info", "NewsProvince NP", "textBlock", false);
				
				$selectMessageSQL = "SELECT message, fromID, toID FROM Message WHERE toID='".$pID."' OR fromID='".$pID."'";
				$messages = "";
				if( ( $messageResult = $this->db->query( $selectMessageSQL ) ) && $this->db->numRows( $messageResult ) ) {
					while( $messageRow = $this->db->fetchArray( $messageResult ) ) {
						$messages .= "<br>*****<br>From: ".$messageRow['fromID']."<br>To: ".$messageRow['toID']."<br>Message: ".strtolower( trim( $messageRow['message'] ) );
					}
				}
				$userList[$userID]->addDetails( "message",  $messages , "M.text", "Message M", "textBlock", false);
				
				$selectComputerNamesSQL = 	"SELECT distinct computerName
																		FROM Login 
																		WHERE userID='".$userID."'
																		ORDER BY timestamp DESC LIMIT 25";
			
				$compNames = "";
				$login = "";
				if( ( $computerNamesResult = $this->db->query( $selectComputerNamesSQL ) ) && $this->db->numRows( $computerNamesResult ) ) {
					while( $loginRow = $this->db->fetchArray( $computerNamesResult ) ) {
						$compNames .= ( $compNames ? "," : "" )."'".$loginRow['computerName']."'";
					}
					$selectLoginSQL = 	"SELECT ip, computerName, UNIX_TIMESTAMP(timestamp) as timestamp, userID, pID  
															FROM Login 
															WHERE computerName IN ( ".$compNames." ) 
															ORDER BY timestamp DESC 
															LIMIT 25";					
					if( ( $loginResult = $this->db->query( $selectLoginSQL ) ) && $this->db->numRows( $loginResult ) ) {
						while( $loginRow = $this->db->fetchArray( $loginResult ) ) {
							$login .= "<br>*****<br>Date: ".date( "d-m-Y H:i", $loginRow['timestamp'] ).
												"<br>computerName: ".$loginRow['computerName'].
												"<br>IP: ".$loginRow['ip'].( ( $loginRow['userID'] == $userID ) ? 
												"<br>userID: ".$loginRow['userID'] :
												"<br>userID: <b>".$loginRow['userID']."</b>" );
						}
					}
				}
				$userList[$userID]->addDetails( "login",  $login , "L.timestamp", "Login L", "textBlock", false);
			}		
		}
			
		return $userList;
	}
	
	
	
	
	
	
	/********************************************/
	/********************************************/
	/****************** LIST ********************/
	/********************************************/
	/********************************************/
	
	/*
	 * List suspicious computers or ips
	 */
	function listSuspicious() {
		$html = "";
		$qs = preg_replace( "/([&]?evidenceType=[\w]+)/", "", $_SERVER['QUERY_STRING'] );
		$html .= "<a href='".$_SERVER['PHP_SELF']."?".$qs."&evidenceType=computerName'><b>Computer names:</b></a><br>";
		$html .= "<a href='".$_SERVER['PHP_SELF']."?".$qs."&evidenceType=IP'><b>IP addresses:</b></a><br>";
		if( isset( $_GET['evidenceType'] ) && !strcmp( "computerName", $_GET['evidenceType'] ) ) {
			$computerNameList = $this->getSuspiciousComputerNames();
			if( ( $noOfComputerNames = count( $computerNameList ) ) ) {
				$html .= $noOfComputerNames." computerNames were found<br>";
				$html .= "<table><tr><td>Computer name</td><td>Users</td><td>Found</td><td>Checked</td></tr>";					
				foreach( $computerNameList as $computer ) {
					$html .= "<tr bgcolor='".$this->getTableColor()."'>
												<td nowrap><a href='".$_SERVER['PHP_SELF']."?searchSuspicious=yes&computerName=".$computer['computerName']."'>".( $computer['checked'] ? "* " : "" ).$computer['computerName']."</a></td>
												<td nowrap align='center'>".$computer['noOfUsers']."</td>
												<td nowrap align='center'>".$computer['dateFound']."</td>
												<td nowrap align='center'>".( $computer['checked'] ? $computer['dateChecked'] : "no" )."</td>
												<td>";
					$userString = "";
					foreach( $computer['userIDs'] as $userID ) {
						$userString .= ( $userString ? ", " : "" )."<a href='".$_SERVER['PHP_SELF']."?searchSuspicious=yes&userID=".$userID."'>".$userID."</a>";
					}
					$html .= $userString."</td></tr>";
				}
				$html .= "</table>";
			} else {
				$html .= "No computerName found<br>";
			}
		}		
		if( isset( $_GET['evidenceType'] ) && !strcmp( "IP", $_GET['evidenceType'] ) ) {
			$ipList = $this->getSuspiciousIP();
			if( ( $noOfIPAddresses = count( $ipList ) ) ) {
				$html .= $noOfIPAddresses." IP addresses were found<br>";
				$html .= "<table><tr><td>IP</td><td>Users</td><td>Found</td><td>Checked</td><td>User IDs</td></tr>";					
				foreach( $ipList as $ip ) {
					$html .= "<tr bgcolor='".$this->getTableColor()."'>
												<td nowrap><a href='".$_SERVER['PHP_SELF']."?searchSuspicious=yes&ip=".$ip['IP']."'>".( $ip['checked'] ? "* " : "" ).$ip['IP']."</a></td>
												<td nowrap align='center'>".$ip['noOfUsers']."</td>
												<td nowrap align='center'>".$ip['dateFound']."</td>
												<td nowrap align='center'>".( $ip['checked'] ? $ip['dateChecked'] : "no" )."</td>
												<td>";
					$userString = "";
					foreach( $ip['userIDs'] as $userID ) {
						$userString .= ( $userString ? ", " : "" )."<a href='".$_SERVER['PHP_SELF']."?searchSuspicious=yes&userID=".$userID."'>".$userID."</a>";
					}
					$html .= $userString."</td></tr>";
				}
				$html .= "</table>";
			} else {
				$html .= "No ip found<br>";
			}
		} 	
		return $html;
	}
	
	/*
	 * Get suspicious computerNames
	 */
	function getSuspiciousComputerNames() {
		$computerNameList = array();
		$selectSQL = "SELECT computerName, userString, noOfUsers, UNIX_TIMESTAMP(dateFound) as dateFound, checked, UNIX_TIMESTAMP(dateChecked) as dateChecked 
									FROM CheaterComputer 
									WHERE noOfUsers >= '".$this->mode."'
									ORDER BY checked, noOfUsers DESC";		
		if( ( $result = $this->db->query( $selectSQL ) ) && $this->db->numRows( $result ) ) {
			while( $row = $this->db->fetchArray( $result ) ) {
				$computerNameList[ $row['computerName'] ] = array( 	"computerName" => $row['computerName'],																														
																														"noOfUsers" => $row['noOfUsers'],
																														"dateFound" => date( "d-m-Y", $row['dateFound'] ),
																														"checked" => $row['checked'],
																														"dateChecked" => date( "d-m-Y", $row['dateChecked'] ),
																														"userString" => $row['userString'], 
																														"userIDs" => @split( ",", $row['userString'] ) );
			}
		}
		return $computerNameList;
	}
	
	/*
	 * Get suspicious IPs
	 */
	function getSuspiciousIP() {
		$ipList = array();
		$selectSQL = "SELECT IP, noOfUsers, userString, UNIX_TIMESTAMP(dateFound) as dateFound, checked, UNIX_TIMESTAMP(dateChecked) as dateChecked 
									FROM CheaterIP 
									WHERE noOfUsers >= '".$this->mode."'
									ORDER BY checked, noOfUsers DESC";		
		if( ( $result = $this->db->query( $selectSQL ) ) && $this->db->numRows( $result ) ) {
			while( $row = $this->db->fetchArray( $result ) ) {
				$ipList[ $row['IP'] ] = array( 	"IP" => $row['IP'],																				
																				"noOfUsers" => $row['noOfUsers'],
																				"dateFound" => date( "d-m-Y", $row['dateFound'] ),
																				"checked" => $row['checked'],
																				"dateChecked" => date( "d-m-Y", $row['dateChecked'] ),
																				"userString" => $row['userString'],
																				"userIDs" => @split( ",", $row['userString'] ) );
			}
		}
		return $ipList;
	}
	
	/********************************************/
	/********************************************/
	/*************** FIND/SAVE ******************/
	/********************************************/
	/********************************************/
	
	/*
	 * Ecexute functions to quickly find suspicious computers / ips
	 */
	function findSuspicious() {	
		$this->topHtml .=  "Finding suspicious users<br>";
		$this->sameComputerName( $this->mode );		
		$this->sameIP( $this->mode );
		$this->topHtml .=  "Done finding suspicious users<br>";
	}
		
	/*
	 *	Function to find computers used by several users
	 */
	function sameComputerName( $noOfUsers="2", $computerName="%" ) {		
		$this->topHtml .=  "Finding computerNames...<br>";
		$tempTable = "CREATE TEMPORARY TABLE Suspicious AS 
										SELECT computerName, count( distinct userID ) as users 
										FROM Login 
										GROUP BY computerName";
		$dropTemp = "DROP TEMPORARY TABLE Suspicious";
		$selectSQL = "SELECT computerName, users 
									FROM Suspicious 
									WHERE users >= '".$noOfUsers."' 
										AND computerName LIKE '".$computerName."'
									ORDER BY users DESC";			
		$this->db->query( $tempTable );
		if( ( $result = $this->db->query( $selectSQL ) ) && $this->db->numRows( $result ) ) {
			while( $row = $this->db->fetchArray( $result ) ) {
				$computerID = $row['computerName'];
				$users = $row['users'];
				$selectUsers = "SELECT userID FROM Login WHERE computerName='".$computerID."' ORDER BY userID";
				$userIDs = array();
				if( ( $selectUserResult = $this->db->query( $selectUsers ) ) && $this->db->numRows( $selectUserResult ) ) {
					while( $selectUserRow = $this->db->fetchArray( $selectUserResult ) ) {
						$userIDs[ $selectUserRow['userID'] ] = $selectUserRow['userID'];
					}
				}
				$userString = "";
				foreach( $userIDs as $userID ) {
					$userString .= ( $userString ? "," : "" ).$userID;
				}
				$checkSQL = "SELECT noOfUsers FROM CheaterComputer WHERE computerName='".$computerID."'";
				if( ( $checkResult = $this->db->query( $checkSQL ) ) && $this->db->numRows( $checkResult ) ) {
					$checked = $this->db->fetchArray( $checkResult );
					if( $checked['noOfUsers'] < $users ) {
						$updateSQL = "UPDATE TABLE CheaterComputer 
												SET noOfUsers='".$users."', dateFound='".date("Y-m-d")."', checked='0', 
														dateChecked='', userString='".$userString."' 
												WHERE computerName='".$computerID."'";
						$this->db->query( $updateSQL );	
					}
				} else {
					$insertSQL = "INSERT INTO CheaterComputer (computerName, noOfUsers, dateFound, userString) 
												VALUES ('".$computerID."','".$users."','".date("Y-m-d")."', '".$userString."')";
					$this->db->query( $insertSQL );					
				}
			}
		}
		$this->db->query( $dropTemp );		
	}
	
	/*
	 *	Function to find ip's used by several users
	 */
	function sameIP( $noOfUsers="2", $ip="%" ) {
		$this->topHtml .=  "Finding ip's...<br>";
		$tempTable = "CREATE TEMPORARY TABLE Suspicious AS 
										SELECT ip, count( distinct userID ) as users 
										FROM Login 
										GROUP BY ip";
		$dropTemp = "DROP TEMPORARY TABLE Suspicious";
		$selectSQL = "SELECT ip, users 
									FROM Suspicious 
									WHERE users >= '".$noOfUsers."' 
										AND ip LIKE '".$ip."'
									ORDER BY users DESC";		
		$this->db->query( $tempTable );
		if( ( $result = $this->db->query( $selectSQL ) ) && $this->db->numRows( $result ) ) {
			while( $row = $this->db->fetchArray( $result ) ) {
				$IP = $row['ip'];
				$users = $row['users'];
				$selectUsers = "SELECT userID FROM Login WHERE ip='".$IP."' ORDER BY userID";
				$userIDs = array();
				if( ( $selectUserResult = $this->db->query( $selectUsers ) ) && $this->db->numRows( $selectUserResult ) ) {
					while( $selectUserRow = $this->db->fetchArray( $selectUserResult ) ) {
						$userIDs[ $selectUserRow['userID'] ] = $selectUserRow['userID'];
					}
				}
				$userString = "";
				foreach( $userIDs as $userID ) {
					$userString .= ( $userString ? "," : "" ).$userID;
				}
				$checkSQL = "SELECT noOfUsers FROM CheaterIP WHERE IP='".$IP."'";
				if( ( $checkResult = $this->db->query( $checkSQL ) ) && $this->db->numRows( $checkResult ) ) {
					$checked = $this->db->fetchArray( $checkResult );
					if( $checked['noOfUsers'] < $users ) {
						$updateSQL = "UPDATE TABLE CheaterIP 
												SET noOfUsers='".$users."', checked='0', dateFound='".date("Y-m-d")."', 
														dateChecked='', userString='".$userString."' 
												WHERE IP='".$IP."'";
						$this->db->query( $updateSQL );	
					}
				} else {
					$insertSQL = "INSERT INTO CheaterIP (IP, noOfUsers, dateFound, userString) 
												VALUES ('".$IP."','".$users."', '".date("Y-m-d")."', '".$userString."')";
					$this->db->query( $insertSQL );					
				}
			}
		}	
		$this->db->query( $dropTemp );
	}	
	
	/********************************************/
	/********************************************/
	/************** UPDATE/SAVE *****************/
	/********************************************/
	/********************************************/
	
	/*
	 * Set checked status of a computerName or an IP
	 */
	function setCheckedComputerName( $computerName ) {
		$this->sameComputerName( 2, $computerName );
		$updateSQL = "UPDATE CheaterComputer 
									SET checked='1', dateChecked='".date("Y-m-d")."' 
									WHERE computerName='".$computerName."'";
		$this->db->query( $updateSQL );
		$this->topHtml .= "<b>computerName: ".$computerName." has been set to checked</b><br>";
	}	
	function setCheckedIP( $IP ) {
		$this->sameIP( 2, $IP );
		$updateSQL = "UPDATE CheaterIP 
									SET checked='1', dateChecked='".date("Y-m-d")."' 
									WHERE IP='".$IP."'";
		$this->db->query( $updateSQL );
		$this->topHtml .= "<b>IP: ".$IP." has been set to checked</b><br>";
	}
	
	/*
	 * Add to user history, set province as deleted cheater and set CheaterEvidence entries to deleted
	 */
	function deleteUser( $userID, $reason="" ) {
		$selectCheaterEvidence = "SELECT ceID FROM CheaterEvidence WHERE userID='".$userID."' AND deleted='0'";
		if( ( $cheaterEvidenceResult = $this->db->query( $selectCheaterEvidence ) ) && $this->db->numRows() ) {
			$updateCheaterEvidence = "UPDATE CheaterEvidence 
																SET deleted='1', deletedReason='".date("d-m-Y").": ".$reason."' 
																WHERE userID='".$userID."' 
																	AND deleted='0'";
			$this->db->query( $updateCheaterEvidence );
			$updateUser = "UPDATE User 
										SET history=CONCAT(history, '\n<br>Deleted cheater'), deletedReason=CONCAT(deletedReason, '\n<br>".date("d-m-Y").": ".$reason."') 
										WHERE userID='".$userID."'";
			$this->db->query( $updateUser );
			$updateProvince = "UPDATE Province P, User U SET P.status='DeletedCheater' WHERE U.userID='".$userID."' AND P.pID=U.pID";
			$this->db->query( $updateProvince );
			$this->topHtml .= "<b>Cheater with userID ".$userID." deleted because of: ".$reason."</b><br>";
		} else {
			$this->topHtml .= "<b>You have to save the evidence before you can delete a cheater!</b><br>";
		}
	}
	
	/*
	 * Save all associated evidence in the CheaterEvidence table
	 */
	function saveEvidence( $userEvidenceObject ) {
		$userID = $userEvidenceObject->getDetail("userID");
		$insertSQL = "INSERT INTO CheaterEvidence 
									( userID, pID, kiID, realName, userName, password, provinceName, rulerName, kingdomName, 
									email, country, dob, history, news, messages, login, associatedUserIDs, isInForum, evidenceDate )
									VALUES
									( '".$userID."', 
									'".htmlspecialchars(addslashes($userEvidenceObject->getDetail("pID")				))."', 
									'".htmlspecialchars(addslashes($userEvidenceObject->getDetail("kiID")				))."', 
									'".htmlspecialchars(addslashes($userEvidenceObject->getDetail("realName")		))."', 
									'".htmlspecialchars(addslashes($userEvidenceObject->getDetail("userName")		))."', 
									'".htmlspecialchars(addslashes($userEvidenceObject->getDetail("password")		))."', 
									'".htmlspecialchars(addslashes($userEvidenceObject->getDetail("provinceName")))."', 
									'".htmlspecialchars(addslashes($userEvidenceObject->getDetail("rulerName")	))."', 
									'".htmlspecialchars(addslashes($userEvidenceObject->getDetail("kingdomName")))."',
									'".htmlspecialchars(addslashes($userEvidenceObject->getDetail("email")			))."', 
									'".htmlspecialchars(addslashes($userEvidenceObject->getDetail("country")		))."', 
									'".htmlspecialchars(addslashes($userEvidenceObject->getOriginalDetail("dob")))."', 
									'".htmlspecialchars(addslashes($userEvidenceObject->getDetail("history")		))."', 
									'".htmlspecialchars(addslashes($userEvidenceObject->getDetail("news")				))."', 
									'".htmlspecialchars(addslashes($userEvidenceObject->getDetail("messages")		))."', 
									'".htmlspecialchars(addslashes($userEvidenceObject->getDetail("login")			))."',
									'".htmlspecialchars(addslashes($userEvidenceObject->getAssociatedUserIDs()	))."',
									'".htmlspecialchars(addslashes($userEvidenceObject->getDetail("isInForum")	))."',
									'".date( "Y-m-d" )."' )";
		if( $this->db->query( $insertSQL ) ) {
			$ceID = $this->db->lastInsertId();
			$selectLoginComputerSQL = "SELECT distinct computerName FROM Login WHERE userID='".$userID."'";
			if( ( $loginComputerResult = $this->db->query( $selectLoginComputerSQL ) ) && $this->db->numRows( $loginComputerResult ) ) {
				while( $computerRow = $this->db->fetchArray( $loginComputerResult ) ) {
					$insertComputerSQL = "INSERT INTO CheaterEvidenceType (ceID,computerName) VALUES ( '".$ceID."','".$computerRow['computerName']."')";
					$this->db->query( $insertComputerSQL );				
				}
			}
			$selectLoginIPSQL = "SELECT distinct ip FROM Login WHERE userID='".$userID."'";
			if( ( $loginIPResult = $this->db->query( $selectLoginIPSQL ) ) && $this->db->numRows( $loginIPResult ) ) {
				while( $IPRow = $this->db->fetchArray( $loginIPResult ) ) {
					$insertIPSQL = "INSERT INTO CheaterEvidenceType (ceID,IP) VALUES ( '".$ceID."','".$IPRow['ip']."')";
					$this->db->query( $insertIPSQL );				
				}
			}			
			$this->topHtml .= "<b>User evidence for user ".$userID." has been saved</b><br>";
		} else {
			$this->topHtml .= "Couldn't save user evidence to table for user ".$userID."<br>";
			echo $this->db->error();
		}
	}
	
	/********************************************/
	/********************************************/
	/**************** DISPLAY *******************/
	/********************************************/
	/********************************************/
	
	/*
	 * Display information about users
	 */
	function getUserEvidenceDisplay( &$userEvidenceObjectArray ) {
		$html = "";
		$more = ( isset( $_GET['more'] ) ? $_GET['more'] : "" );
		$moreArray = split( ",", $more );
		foreach( $userEvidenceObjectArray as $userEvidenceObject ) {
			$html .= "<table bgcolor='".$this->getTableColor()."' width='100%'>";
			$userDetails = $userEvidenceObject->getAllDetails();			
			$thisUserID = $userEvidenceObject->getDetail("userID");
			while( list( $key, $detail ) = each( $userDetails ) ) {
				if( in_array( $thisUserID, $moreArray ) || ( strcmp( "login", $key ) && strcmp( "news", $key ) && strcmp( "message", $key ) ) ) {
					$otherUsers = "";
					if( !$detail->doCheck() || !count( $otherUsersArray = $this->compareDetails( $userEvidenceObject, $detail->getOriginalValue() ) ) ) {
						$html .= "<tr><td>$key</td><td>".$detail->getValue()."</td><td>&nbsp;</td></tr>";
					} else {
						foreach( $otherUsersArray as $otherUserID ) {
								$otherUsers .= ( $otherUsers ? ", " : "" ).$otherUserID;
						}
						$html .= "<tr><td><b>$key</b></td><td><b>".$detail->getValue()."</b></td><td>
												<a href='".$_SERVER['PHP_SELF']."?searchSuspicious=yes&userID=".$thisUserID.",".$otherUsers."'>".$otherUsers."</a>
											</td></tr>";
						$_SESSION['userEvidence'][$thisUserID]->addAssociatedUserID( $otherUsers );
					}
				}
			}			
			if( in_array( $thisUserID, $moreArray ) ) {
				$thisMore = preg_replace( "/,?".$thisUserID."/", "", $more ); 
				$qs = preg_replace( "/&?more=[0-9,]+&?/","", $_SERVER['QUERY_STRING'] );
				$html .= "<tr><td colspan='3'>
										<a href='".$_SERVER['PHP_SELF']."?".$qs."&more=".$thisMore."'>-- less --</a>
									</td></tr>";
			} else {
				$thisMore = preg_replace( "/,?".$thisUserID."/", "", $more ); 
				$qs = preg_replace( "/&?more=[0-9,]+&?/","", $_SERVER['QUERY_STRING'] );
				$html .= "<tr><td colspan='3'>
										<a href='".$_SERVER['PHP_SELF']."?".$qs."&more=".$thisMore.( $thisMore ? "," : "").$thisUserID."'>-- more --</a>
									</td></tr>";
			}
			$html .= "<tr><td colspan='3'>
									<form action='".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."' method='POST'>
										<input type='hidden' value='".$thisUserID."' name='saveUserID'>
										<input type='Submit' value='Save this evidence' name='SaveEvidence'>
									</form>
								</td></tr>";
			$html .= "<tr><td colspan='3'>
									<form action='".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."' method='POST'>
										<input type='hidden' value='".$thisUserID."' name='deleteUserID'>
										Reason: <input type='text' name='reason' maxlength='200'><input type='Submit' value='Delete this users province' name='DeleteUser'>
									</form>
								</td></tr>";
			$html .= "</table>";
		}		
		return $html;
	}
	
	/*
	 * To select how many users one computer/ip should be allowed to have before it's recorded
	 */
	function getChooseMode() {
		$html = "";
		$html .= "<form action='".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."' method='POST'>
								<select name='mode'>";
		$modeNames = array_flip( $this->modes );
		foreach( $modeNames as $modeName ) {
			if( isset( $_SESSION['CheaterMode'] ) ) {
				if( $this->modes[$modeName] == $_SESSION['CheaterMode'] ) {
					$html .= "<option value='".$modeName."' selected>".$modeName."</option>";
				} else {
					$html .= "<option value='".$modeName."'>".$modeName."</option>";
				}
			} else {
				if( $this->modes[$modeName] == $this->modes['NORMAL'] ) {
					$html .= "<option value='".$modeName."' selected>".$modeName."</option>";
				} else {	
					$html .= "<option value='".$modeName."'>".$modeName."</option>";
				}
			}
		}						
		$html .=	 "</select><input type='Submit' value='Change mode' name='ChangeMode'>
							</form>";
		return $html;
	}
	
	/*
	 * Describe how to use this script
	 */
	function getHowToUse() {
		$html = "";
		$html .= "<table bgcolor='".$this->getTableColor()."' width='100%'>
							<tr><td><b>Menu</b></td></tr>
							<tr><td>Find: search for and save computerNames and IPs with several users</td></tr>
							<tr><td>View: view the saved computerNames or IPs</td></tr>
							<tr><td>Search: get detailed information about users</td></tr>
							<tr><td>Choose mode: Set the sensitivity of the find and view options. Do not use suspisious/paranoid 
							until you've tried the normal setting as this might take a lot of time.</td></tr>
						</table>";
		$html .= "<table bgcolor='".$this->getTableColor()."' width='100%'>
							<tr><td><b>Find</b></td></tr>
							<tr><td>This page will just run a script to find and save all computerNames and 
							ip's which have more than a given number of userIDs associated to it. The number 
							of userIDs required to save the computerName/ip is set by choosing a mode on the menu.
							Here's the different settings:<br>";
		$modeNames = array_flip( $this->modes );
		foreach( $modeNames as $modeName ) {
			$html .= $modeName." requires ".$this->modes[$modeName]." users to be saved.<br>";
		}					
		$html .= "</td></tr>
						</table>";
		$html .= "<table bgcolor='".$this->getTableColor()."' width='100%'>
							<tr><td><b>View</b></td></tr>
							<tr><td>Here you may choose to view saved IPs or saved computerNames. This page will also 
							look at the mode setting and only show saved computers/ip's with enough users. When you 
							view computerNames/ip's you'll see the computerName/ip then the number of users using this 
							computerName/ip, when the computer/ip was found, wether it's been checked or not (see search) 
							and each of the userIDs using the computerName/ip.<br>
							By clicking the computerName/ip you'll get to the search page which will show all users 
							associated to this computerName/ip.<br>
							By clicking one of the userIDs you'll get to the search page which will show only that user.<br>
							If the computerName/ip has a * in front, it's been checked.</td></tr>
						</table>";
			$html .= "<table bgcolor='".$this->getTableColor()."' width='100%'>
							<tr><td><b>Search</b></td></tr>
							<tr><td>Here you may search for specific kingdoms, users or provinces. To search for more than 
							one kingdom/province/user separate them with commas(,)<br>
							If you came to this page through the view page, you'll have the option of marking this computerName/ip 
							as checked. Do this when you've finished checking/deleting the users displayed.<br>
							All selected users will be displayed on this page, so it might take a while to load. You'll get basic 
							details for each user and if the user detail exists within another user's details, it will become bold 
							and the other user's userID will be displayed to the right. If you want to compare the showing user with 
							one of the users with the same detail, click on that other user to the right, and only two users 
							will be displayed. If you want to compare all users with the same details, copy the userIDs to the 
							right and paste them into the search field and search for them.<br>
							To see news/messages/login for a user, click the 'more' link (lots of extra info)<br>
							To save all evidence for this user, click the 'Save evidence' button<br>
							To mark this user as a cheater, first save the evidence then write a reason and click the 'Delete this 
							users province' button to update all tables.<br></td></tr>
						</table>";
		return $html;
	}
	
	
	/********************************************/
	/********************************************/
	/***************** CHECK ********************/
	/********************************************/
	/********************************************/
	
	/*
	 * Check one user evidence detail against other users details
	 */
	function compareDetails( $currentUserObject, $detail ) {
		$result = array();
		$listOfCheckedFields = array( "U.name", 
																	"U.userName", 
																	"U.password",
																	"U.email",
																	"U.dob",
																	"U.created",
																	"U.history",																												
																	
																	"P.rulerName",
																	"P.provinceName",																												
																	
																	"K.name" );
		$checkFields = "";
		foreach( $listOfCheckedFields as $field ) {
			$checkFields .= ( $checkFields ? " OR " : "" ).$field." LIKE '%$detail%'";
		}
		$currentUserID = $currentUserObject->getDetail( "userID" );
		$selectSQL = "SELECT U.userID 
									FROM User AS U LEFT JOIN Province AS P ON U.pID=P.pID, Kingdom AS K
									WHERE U.userID != '".$currentUserID."' 
										AND P.pID=U.pID 
										AND P.kiID=K.kiID 
										AND	( ".$checkFields.")";		
		if( ( $detailResult = $this->db->query( $selectSQL ) ) && $this->db->numRows( $detailResult ) ) {
			while( $row = $this->db->fetchArray( $detailResult ) ) {
				$result[ $row['userID'] ] = $row['userID'] ;
			}
		}
		return $result;
	}
	

	
	/********************************************************/
	/* OTHER FUNCTIONS																			*/
	/********************************************************/
	
	/*
	 * Function to alternate betveen two colors of the table / rows in a table
	 */
	function getTableColor() {
		$this->currentTableColor = ( ( $this->currentTableColor + 1 ) % 2 );
		return $this->tableColors[ $this->currentTableColor ];
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
	var $userDetails = array();
	var $evidence = array();
	var $associatedUserIDs = array();
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
	}	
	
	/*
	 * Get the userIDs which are connected to this one through IP or computerName
	 */
	function getAssociatedUserIDs() {
		$userIDs = "";
		foreach( $this->associatedUserIDs as $userID ) {
			$userIDs .= ( $userIDs ? "," : "" ).$userID;
		}
		return $userIDs;
	}

	/*
	 * Add userIDs which are connected to this one through IP or computerName
	 * Takes a string of userIDs seperated by ','
	 */	
	function addAssociatedUserID( $userIDs ) {
		$userIDArray = split( ",", $userIDs );		
		foreach( $userIDArray as $userID ) {
			$this->associatedUserIDs[trim($userID)] = trim($userID);
		}
	}
	
	/*
	 * add details for this user
	 */
	function addDetails( $name, $value, $field, $table, $type, $check=true ) {
		$this->userDetails[$name] = new UserDetail( $name, $value, $field, $table, $type, $check );
	}
	
	/*
	 * get the given user detail
	 */
	function getDetail( $name ) {
		if( isset( $this->userDetails[$name] ) ) {
			return $this->userDetails[$name]->getValue();
		} else {
			return false;
		}
	}
	
	/*
	 * get the given original user detail
	 */
	function getOriginalDetail( $name ) {
		if( isset( $this->userDetails[$name] ) ) {
			return $this->userDetails[$name]->getOriginalValue();
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
}


/************************************************************/
/************************************************************/
/********************* UserDetail ***************************/
/************************************************************/
/************************************************************/
class UserDetail {
	var $detailTypes = array( "date" => "date",
														"number" => "number",
														"varchar" => "varchar",
														"textBlock" => "textBlock" );
	var $name = "";
	var $value = "";
	var $type = "";
	var $field = "";
	var $table = "";
	var $check = true;
	
	function UserDetail( $name, $value, $field, $table, $type, $check ) {
		$this->name = $name;
		$this->value = $value;
		$this->field = $field;
		$this->table = $table;
		$this->type = $type;
		$this->check = $check;
	}
	
	/*
	 * Get a detail for screen output
	 */
	function getValue() {
		if( !strcmp( $this->detailTypes['date'], $this->type ) ) {
			return date("d-m-Y", $this->value );
		} else {
			return $this->value;
		}
	}
	
	/*
	 * To get the date as mysql want it
	 */
	function getOriginalValue() {
		if( !strcmp( $this->detailTypes['date'], $this->type ) ) {
			return date("Y-m-d", $this->value );
		} else {
			return $this->value;
		}
	}
	
	/*
	 * Should this detail be checked against other users details?
	 */
	function doCheck() {
		return $this->check;
	}
}
} // end if !class exists
?>