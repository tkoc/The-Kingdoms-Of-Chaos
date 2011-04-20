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

/* Message class
 * 
 * This class will handle all functions needed to display, send, delete and recieve
 * messages in the game.
 * 
 * Author: Øystein Fladby	25.02.2003
 * 
 * Changelog
 * 11.08.03 - Added ingame time, choose kiID when sending and removed a bug when deleting the last message on a page
 *
 * Version: test
 * 
 */
 
require_once( $GLOBALS['path_www_scripts'] . "../forum/Template.class.inc.php" );
require_once( $GLOBALS['path_www_scripts'] . "ChaosTime.class.inc.php" );
$GLOBALS['PATH_TO_MESSAGES'] = "messages/";
class Message {
	
	var $pID;
	var $kiID;
	var $db;
	var $page;
	var $messageNo;
	var $messagesOnPage = 10;
	var $totalMessages;
	var $tmp;
	var $pageState;
	var $toOrFrom = array( 	1 => "From",
							2 => "To" );
	
	////////////////////////////////////////////
	// Message::Message
	////////////////////////////////////////////
	// Constructor to set up the class with a database
	// and an optional province ID
	////////////////////////////////////////////
	function Message( &$db, $pID=false ) {
		$this->pID = $pID;
		$this->db = $db;
		$this->kiID = $this->getKingdomID( $pID );
		$this->page = ( isset( $_GET['page'] ) ? $_GET['page'] : 1 );
		$this->messageNo = ( isset( $_GET['messageNo'] ) ? $_GET['messageNo'] : 1 );
		$this->tmp = new Template();
		$this->tmp->def( "MENU", $GLOBALS['PATH_TO_MESSAGES']."messageMenu.html" );
		$this->pageHandler();
	}
	
	////////////////////////////////////////////
	// Message::pageHandler
	////////////////////////////////////////////
	// Function to choose which page to be viewed
	// and assign values to those pages.
	////////////////////////////////////////////
	function pageHandler() {
		if( isset( $_GET['mID'] ) ) {
			$this->pageState = $_GET['ps'];
			$this->tmp->def( "TEXT", $GLOBALS['PATH_TO_MESSAGES']."viewOneMessage.html" );
			$this->showOneMessage();
			$this->tmp->parse( "TEXT" );
			//echo $this->tmp->debug("TEXT");
		} else if( isset( $_GET['sendMessage'] ) ) {
			$this->pageState = 10;
			$this->tmp->def( "TEXT", $GLOBALS['PATH_TO_MESSAGES']."sendMessage.html" );
			$this->showSendMessage();
			$this->tmp->parse( "TEXT" );
			//echo $this->tmp->debug("TEXT");
		} else {
			if( isset( $_GET['sentMessages'] ) ) {
				$this->pageState = 2;
			} else {
				$this->pageState = 1;
			}
			$this->deleteMessages();
			$this->tmp->def( "TEXT", $GLOBALS['PATH_TO_MESSAGES']."viewMessages.html" );
			$this->tmp->assign( "delete action", $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'] , "TEXT" );
			$this->tmp->assign( "from or to", $this->toOrFrom[$this->pageState] );
			$this->showMessages();
			$this->pageMenu();
			$this->tmp->parse( "TEXT" );			
		}			
		$this->tmp->assign( "incoming link", $_SERVER['PHP_SELF'], "MENU" );
		$this->tmp->assign( "outgoing link", $_SERVER['PHP_SELF']."?sentMessages=true" );
		$this->tmp->assign( "send link", $_SERVER['PHP_SELF']."?sendMessage=true" );
		$this->tmp->assign( "unread", $this->unreadMessages() );
		$this->tmp->assign( "text", $this->tmp->display( "TEXT" ), "MENU" );
		$this->tmp->parse("MENU");
		//echo $this->tmp->display("MENU");
		//echo $this->tmp->debug("MENU");		
	}
	
	////////////////////////////////////////////
	// Message::pageMenu
	////////////////////////////////////////////
	// Function to make the page menu when viewing 
	// messages
	////////////////////////////////////////////
	function pageMenu() {
		$qs = preg_replace( "(&page=[0-9]*)", "", $_SERVER['QUERY_STRING'] );
		$totalPages = ceil( $this->totalMessages / $this->messagesOnPage );
		$this->tmp->assign( "first link", $_SERVER['PHP_SELF']."?$qs&page=1" , "TEXT" );
		$this->tmp->assign( "prev link", $_SERVER['PHP_SELF']."?$qs&page=".( ( $this->page > 1 ) ? ($this->page-1) : 1) );
		for( $pageCount = 1; $pageCount <= $totalPages; $pageCount++ ) {
			$this->tmp->dynamicAssign( "choose page", "page link", $_SERVER['PHP_SELF']."?$qs&page=$pageCount" );
			$this->tmp->dynamicAssign( "choose page", "page number", "$pageCount" );
			if( $this->page == $pageCount ) {
				$this->tmp->dynamicAssign( "choose page", "no link", "$pageCount" );
				$this->tmp->dynamicAssign( "choose page", "page", "" );
			} else {
				$this->tmp->dynamicAssign( "choose page", "page", "$pageCount" );
				$this->tmp->dynamicAssign( "choose page", "no link", "" );
			}
		}
		$this->tmp->assign( "next link", $_SERVER['PHP_SELF']."?$qs&page=".( ( $this->page < $totalPages ) ? ($this->page+1) : $totalPages) );
		$this->tmp->assign( "last link", $_SERVER['PHP_SELF']."?$qs&page=$totalPages" );
	}
	
	////////////////////////////////////////////
	// Message::getTime
	////////////////////////////////////////////
	// Function to format the time string to display in the forums
	// returns
	// 		The string containing the resulting HTML code
	////////////////////////////////////////////
	function getTime( $ticks ) {
		$time = new ChaosTime($ticks);
		$result = 	$time->getDay()." day of the ".$time->getMonth()." month 
					<br>".$time->getYear()." year in the ".$time->getEra()." era";
		return $result;
	}
	
	////////////////////////////////////////////
	// Message::sendMessage
	////////////////////////////////////////////
	// Function to insert a message into the Message
	// table in the DB.
	// takes the province ID to send to, the message 
	// to send and optionally the province ID to send 
	// from
	// returns true always
	////////////////////////////////////////////
	function sendMessage( $topID, $msg, $fromID=false ) {
		if( !$fromID ) {
			$fromID = $this->pID;
		}
		$date = date( "Y-m-d H:i:s" );
		$selectSQL = "SELECT ticks FROM Config";
		$this->db->query( $selectSQL );
		$row = $this->db->fetchArray();
		$msg = nl2br( htmlspecialchars( $msg , ENT_QUOTES ) );
		$insertSQL = 	"INSERT INTO Message (toID, fromID, message, ticks, sent )
						VALUES ($topID, $fromID, '$msg', '".$row['ticks']."', '$date' )";
		$this->db->query( $insertSQL );
		return true;
	}
	
	////////////////////////////////////////////
	// Message::deleteMessage
	////////////////////////////////////////////
	// Function to delete a message from the DB table.
	// Takes the message ID and always returns true.
	////////////////////////////////////////////
	function deleteMessage( $mID ) {
		$toOrFrom = strtolower( $this->toOrFrom[$this->pageState] );
		$opposite = ( strcmp( $toOrFrom, "to" ) ? "to" : "from" );
		if( !strcmp( $opposite, "to" ) ) {
			$opposite = "isRead=1, ".$opposite;
		}
		$updateSQL = 	"UPDATE Message
						SET ".$opposite."Deleted=1
						WHERE mID LIKE '$mID'";
		//echo $updateSQL;
		$this->db->query( $updateSQL );
		$deleteSQL = 	"DELETE FROM Message
						WHERE fromDeleted=1
						AND toDeleted=1
						AND mID LIKE '$mID'";
		$this->db->query( $deleteSQL );
		return true;
	}
	
	////////////////////////////////////////////
	// Message::unreadMessages
	////////////////////////////////////////////
	// Function to get the number of messages to this
	// province which have not yet been read.
	////////////////////////////////////////////
	function unreadMessages() {
		//$toOrFrom = strtolower( $this->toOrFrom[$this->pageState] );
		//$opposite = ( strcmp( $toOrFrom, "to" ) ? "to" : "from" );
		$opposite = "to";
		$selectSQL = 	"SELECT count( mID ) as unreadMessages
						FROM Message
						WHERE ".$opposite."ID LIKE '$this->pID'
						AND isRead=0";
		$this->db->query( $selectSQL );
		$result = $this->db->fetchArray();
		return $result['unreadMessages'];
	}
	
	////////////////////////////////////////////
	// Message::setRead
	////////////////////////////////////////////
	// Function to set a flag when the message has
	// been read.
	////////////////////////////////////////////
	function setRead( $mID ) {
		$updateSQL = 	"UPDATE Message
						SET isRead=1
						WHERE mID LIKE '$mID'";
		$this->db->query( $updateSQL );
	}
	
	////////////////////////////////////////////
	// Message::getKingdomID
	////////////////////////////////////////////
	// Function to get the kingdom ID of a province.
	////////////////////////////////////////////
	function getKingdomID( $pID ) {
		$selectSQL = 	"SELECT kiID
						FROM Province
						WHERE pID LIKE '$pID'";
		$this->db->query( $selectSQL );
		if( $this->db->numRows() ) {
			$kingdom = $this->db->fetchArray();
			return $kingdom['kiID'];
		} else {
			return false;
		}
	}	
	
	////////////////////////////////////////////
	// Message::getKingdoms
	////////////////////////////////////////////
	// Function to get all the kingdom names and the
	// kingdom ids from the Kingdom table.
	////////////////////////////////////////////
	function getKingdoms() {
		$selectSQL = 	"SELECT kiID, name
						FROM Kingdom ORDER BY name ASC";
		$result = $this->db->query( $selectSQL );
		if( $this->db->numRows() ) {
			return $result;
		} else {
			return false;
		}
	}
	
	////////////////////////////////////////////
	// Message::getProvinces
	////////////////////////////////////////////
	// Function to get all the province names and the
	// province idsfor one given Kingdom from the 
	// Province table.
	////////////////////////////////////////////
	function getProvinces( $kiID ) {
		$selectSQL = 	"SELECT pID, provinceName
						FROM Province
						WHERE kiID LIKE '$kiID' 
						ORDER BY provinceName ASC";
		$result = $this->db->query( $selectSQL );
		if( $this->db->numRows() ) {
			return $result;
		} else {
			return false;
		}
	}

	////////////////////////////////////////////
  // Message::getProvinceInfo
  ////////////////////////////////////////////
  // Function to get all province and kingdom names
	// from the Province table.
  ////////////////////////////////////////////
  function getProvinceInfo( $pID ) {
  	$selectSQL =    "SELECT P.provinceName, P.rulerName, K.name as kingdomName, K.kiID, P.pID
                     FROM Province P, Kingdom K
                     WHERE P.pID LIKE '$pID'
									 		 AND P.kiID=K.kiID";
		$result = $this->db->query( $selectSQL );
    if( $this->db->numRows() ) {
			$result = $this->db->fetchArray();
			$result['rulerName'] = "<a href='provinceAction.php?victim=".$result['pID']."'>".$result['rulerName']."</a>";
    } else {
      $result = array("provinceName" 	=> "Deleted province",
											"rulerName"	=> "Deleted user",
											"kingdomName"	=> "Unknown kingdom",
											"kiID" => "");
    }
		return $result;
  }
	
	////////////////////////////////////////////
	// Message::getMessages
	////////////////////////////////////////////
	// Function to get all messages to this province
	// from the DB.
	// returns
	// 		false if no messages
	// 		associative array if messages found
	////////////////////////////////////////////
	function getMessages( $mID=false ) {
		$toOrFrom = strtolower( $this->toOrFrom[$this->pageState] );
		$opposite = ( strcmp( $toOrFrom, "to" ) ? "to" : "from" );
		if( $mID ) {
			$oneMessage = " AND mID LIKE '$mID' ";
		} else {
			$oneMessage = "";
		}
		$selectSQL =	"SELECT mID, UNIX_TIMESTAMP(sent) as time, message, isRead, ticks,
						toID, fromID
						FROM Message 
						WHERE ".$opposite."ID LIKE '$this->pID'
						AND ".$opposite."Deleted=0".
						$oneMessage."
						ORDER BY sent DESC, ticks DESC ";
		$messages = $this->db->query( $selectSQL );
		if( $this->totalMessages = $this->db->numRows() ) {
			if( ( $this->page*$this->messagesOnPage ) > $this->totalMessages ) {
				$this->page = ceil( $this->totalMessages / $this->messagesOnPage );
			}
			return $messages;
		} else {
			return false;
		}
	}
	
	////////////////////////////////////////////
	// Message::showMessages
	////////////////////////////////////////////
	// Function to show all messages sent to this
	// province of sent from this province
	// Takes string "from" as parameter if all messages
	// from this province is to be shown, or "to" if
	// all messages to this province should be shown.
	////////////////////////////////////////////
	function showMessages() {
		if( $messages = $this->getMessages() ) {
			$toOrFrom = strtolower( $this->toOrFrom[$this->pageState] );
			$messageCount = 0;
			//$this->db->setRecordOffset( ( ( $this->page - 1 )*$this->messagesOnPage ), $messages );
			while( ( ( $this->page - 1 )*$this->messagesOnPage ) > $messageCount++ ) {
				$message = $this->db->fetchArray( $messages );
			}
			$messageCount = 0;
			while( ( $message = $this->db->fetchArray( $messages ) ) && ( $this->messagesOnPage > $messageCount++ ) ) {
				if( $provInfo = $this->getProvinceInfo( $message[$toOrFrom."ID"] ) ) {
					$this->tmp->dynamicAssign( "message", "message link", $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."&ps=$this->pageState&mID=".$message['mID'],"TEXT" );
					$this->tmp->dynamicAssign( "message", "province name", "$toOrFrom&nbsp;".$provInfo['rulerName'] );
					$this->tmp->dynamicAssign( "message", "province", $provInfo['provinceName'] );
					$this->tmp->dynamicAssign( "message", "kingdom", $provInfo['kingdomName']." (#".$provInfo['kiID'].")" );
					$this->tmp->dynamicAssign( "message", "date", $this->getTime( $message['ticks'] ) );
					$this->tmp->dynamicAssign( "message", "isRead", ( $message['isRead'] ? "checked" : "" ) );
					$this->tmp->dynamicAssign( "message", "delete value", $message['mID'] );				
				}
			}
		}
	}
	
	////////////////////////////////////////////
	// Message::showOneMessage
	////////////////////////////////////////////
	// Function to show one spesific message.
	////////////////////////////////////////////
	function showOneMessage() {
		if( $messages = $this->getMessages( $_GET['mID'] ) ) {
			$toOrFrom = strtolower( $this->toOrFrom[$this->pageState] );
			if( $toOrFrom == "from" ) {
				$this->setRead( $_GET['mID'] );
			}
			$message = $this->db->fetchArray( $messages );
			if( $provInfo = $this->getProvinceInfo( $message[$toOrFrom."ID"] ) ) {
				$qs = preg_replace( "(&mID=[0-9]*)", "", $_SERVER['QUERY_STRING'] );
				$qs = preg_replace( "(&ps=[0-9]*)", "", $qs );
				$this->tmp->assign( "to or from", $this->toOrFrom[$this->pageState], "TEXT" );
				$this->tmp->assign( "ruler name", $provInfo['rulerName'] );
				$this->tmp->assign( "province name", $provInfo['provinceName'] );
				$this->tmp->assign( "kingdom name", $provInfo['kingdomName'] );
				$this->tmp->assign( "date", $this->getTime( $message['ticks'] ) );
				$this->tmp->assign( "message", $message['message'] );
				$this->tmp->assign( "back link", $_SERVER['PHP_SELF']."?$qs" );
				$this->tmp->assign( "reply action", $_SERVER['PHP_SELF'] );
				$this->tmp->assign( "reply pID", $provInfo['pID'] );
				$this->tmp->assign( "reply kiID", $provInfo['kiID'] );
			}
		}
	}
	
	////////////////////////////////////////////
	// Message::showSendMessage
	////////////////////////////////////////////
	// Function to show a form which allows the 
	// user to send messages to other users.
	////////////////////////////////////////////
	function showSendMessage() {
		if( isset( $_GET['kiID'] ) ) {
			$kiID = $_GET['kiID'];
			if( !is_numeric( $kiID ) ) {
				$kiID = $this->kiID;
			}
		} else {
			$kiID = $this->kiID;
		}
		if( isset( $_GET['pID'] ) ) {
			$pID = $_GET['pID'];
		} else {
			$pID = $this->pID;
		}
		if( isset( $_POST['sendMessage'] ) ) {
			// echo $_POST['province'].":".$_POST['message'];
			$this->sendMessage( $_POST['province'], $_POST['message'] );
		}
		$this->tmp->assign( "send action ", $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'], "TEXT" );
		$this->tmp->assign( "kiID action ", $_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING'], "TEXT" );
		$this->tmp->assign( "kiID value ", $kiID, "TEXT" );
		if( $kingdoms = $this->getKingdoms() ) {
			$qs = preg_replace( "(&kiID=[0-9]*)", "", $_SERVER['QUERY_STRING'] );
			$qs = preg_replace( "(&pID=[0-9]*)", "", $qs );
			while( $kingdom = $this->db->fetchArray( $kingdoms ) ) {
				$this->tmp->dynamicAssign( "kingdoms","kingdom url", $_SERVER['PHP_SELF']."?$qs&kiID=".$kingdom['kiID'] );
				$this->tmp->dynamicAssign( "kingdoms", "kingdom", $kingdom['name'] );
				if( $kiID == $kingdom['kiID'] ) {
					$this->tmp->dynamicAssign( "kingdoms", "selected", "selected" );
				} else {
					$this->tmp->dynamicAssign( "kingdoms", "selected", "" );
				}
			}
			if( $provinces = $this->getProvinces( $kiID ) ) {
				while( $province = $this->db->fetchArray( $provinces ) ) {
					$this->tmp->dynamicAssign( "provinces", "province", $province['provinceName'] );
					$this->tmp->dynamicAssign( "provinces", "province id", $province['pID'] );
					if( $pID == $province['pID'] ) {
						$this->tmp->dynamicAssign( "provinces", "selected province", "selected" );
					} else {
						$this->tmp->dynamicAssign( "provinces", "selected province", "" );
					}
				}
			}
		}				
	}
	
	////////////////////////////////////////////
	// Message::deleteMessages
	////////////////////////////////////////////
	// Function to delete messages when the user 
	// wants to delete them.
	////////////////////////////////////////////
	function deleteMessages() {
		if( isset( $_POST['deleteMessage'] ) ) {
			$id = $_POST['id'];
			if( is_array( $id ) ) {
				foreach( $id as $mID ) {
					$this->deleteMessage( $mID );
				}
			}
		}
	}
	
	function getMessagePage() {
		return $this->tmp->display("MENU");
	}
	function showMessagePage() {
		echo $this->tmp->display("MENU");
	}
}

?>