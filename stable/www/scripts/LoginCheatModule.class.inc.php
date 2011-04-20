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

//**********************************************************************
//* User.class.inc.php
//*
//* Definitions for one user.
//*
//* Still has ugly hacks in the logon process to make shit work.
//* Author: Anders Elton
//*
//* History:
//* 	01.08.04
//*		31.07.2004: Anders Elton. Rewrote to fit new code style
//**********************************************************************


if( !class_exists( "User" ) ) {
require_once ("globals.inc.php");

$GLOBALS['user_objects_created'] = 0;
class User {

	// private

	var $cId 		= "";
	var $pId		= -1;
	var $userID		= "";
	var $username	= "";
	var $password	= "";
	var $access 	= 0;
	var $nick 		= "";
	var $chatName	= "";
	var $name 		= "";
	var $dob 		= "";
	var $status		= "";
	var $allowUserUpdateNick = false;
	var $allowUserUpdateImage = false;
	var $allowUserUpdateSignature = false;
	var $signature 	= "";
	var $image 		= "";
	var $email 		= "";
	var $database	= false;
	var $ip			= "";
	var $cookieDomain = "";  // this will be set in the constructor!
	var $enableSessionProtection = true;
	var $maxSessions = 5;  // allow 9 "open" sessions at the same time.
	// pub

	////////////////////////////////////////////
	// User::User
	////////////////////////////////////////////
	// Constructor
	// takes a coockie and a reference to a database object
	////////////////////////////////////////////

	function User ($cookie, &$databaseReference) {
	   $this->cId = $cookie;							
	   $this->database = &$databaseReference;			//don't take a copy of the database object, use the existing one.
	   $this->cookieDomain = $GLOBALS['domain_name'];
	   $GLOBALS['user_objects_created']++;
	}


	////////////////////////////////////////////
	// User::checkNick
	////////////////////////////////////////////
	// 
	// Checks if the nick in the user should be updated or not.
	////////////////////////////////////////////

	function checkNick()
	{
		if ($this->allowUserUpdateNick == false) {
			if ($this->pId > 0) {
				if ($this->database->query("SELECT rulerName, provinceName from Province where pID='$this->pId'") && $this->database->numRows()){
					$res = $this->database->fetchArray();
					// update nick
					$this->database->query("UPDATE User set nick='".$res['rulerName']." in<br>".$res['provinceName']."' WHERE pID='$this->pId'");
				} else {
					$this->database->query("UPDATE User set nick=username WHERE pID='$this->pId'");
				}
			} else {
				$this->database->query("UPDATE User set nick=username WHERE pID='$this->pId'");
			}
		}
	}

	////////////////////////////////////////////
	// Buildings::logon
	////////////////////////////////////////////
	// Function to log an user on to the system
	// Takes the username and password given by the user in a form
	////////////////////////////////////////////

	function logon ($user,$pass) {
	   $this->username = $user;
	   $this->password = $pass;
	   $this->database->query("SELECT * FROM User WHERE username='$this->username' AND password='$this->password'");
	   $this->ip=getenv("REMOTE_ADDR");  					// nb. kan være kjørt gjennom proxy..
	   if (!isset($_COOKIE['computerName'])) {
	   		// expire in 70 days.
			$value = "tkoc:";
			$value .= md5(microtime());
	   		setcookie ("computerName",$value,time()+60*60*24*70,"/",$this->cookieDomain);
			$_COOKIE['computerName'] = $value;
	   }
	   // $ip=getenv(HTTP_X_FORWARDED_FOR); blir da ip adressa.
		if ($result = $this->database->fetchArray()) {
			$this->setUserData($result);
			$this->pId = $result['pID'];
			$this->userID = $result['userID'];
		  	$this->cId = "".md5(microtime());
			$this->database->query("INSERT INTO Login (ip, loggedon, userID, pID, cID, timestamp, computerName) VALUES ('$this->ip', 'Y', '$this->userID', '$this->pId', '$this->cId',NOW(),'$_COOKIE[computerName]')");
			if ( !$this->database->queryOk() ) {			//fail
				return false;
			}
		} else {  										// fail
			return false;
		}
		$this->checkNick();
	  	setcookie ("cId",$this->cId,time()+3600*24,"/",$this->cookieDomain);  	        // expire in 1 hour
		return true;										//the user is logged on
	}
	////////////////////////////////////////////
	// Buildings::isLoggedOn
	////////////////////////////////////////////
	// Function to check whether an user is logged on or not.
	// Returns:
	// 		true if the user is logged on, false if not.
	////////////////////////////////////////////

	function isLoggedOn() {
		$this->ip=getenv("REMOTE_ADDR");
		// 7200 seconds max. hardcoded in sql.
		$this->database->query("SELECT User.allowUserUpdateSignature,User.allowUserUpdateImage,User.image,User.allowUserUpdateNick,User.email,
			User.name,User.userID,User.username,User.password,User.nick,User.signature,User.access, User.pID, Login.timestamp,Login.ip,User.activeSessions,User.chatName 
				FROM Login 
				LEFT JOIN User on User.userID=Login.userID 
					WHERE Login.cID LIKE '$this->cId' 
					AND Login.loggedon='Y' 
					AND ((UNIX_TIMESTAMP(Login.timestamp)+7200)>UNIX_TIMESTAMP()) 
						ORDER BY Login.LoginId DESC");

		if ( $this->database->numRows()>0 ) {	
			$result = $this->database->fetchArray();
//			$this->userID = $result['userID'];
//			$this->pId = $result['pID'];
			$this->setUserData($result);
			if (($result['activeSessions']>$this->maxSessions) && $this->enableSessionProtection) {  // dont allow user to log in..
				$this->logoff();
				$this->database->query("Update User set activeSessions=0 where userID='".$this->userID."'");
				echo ("Session count to large, please log in again!\n");
			} else {
				$this->database->query("Update User set activeSessions=activeSessions+1 where userID='".$this->userID."'");
				register_shutdown_function(array(&$this, 'closeSession'));
			}
			return true;	         	   					// the user is already logged on
	   } //else echo "SELECT userID, pID, timestamp,ip FROM Login WHERE cID LIKE '$this->cId' AND loggedon='Y' AND ip='$this->ip' ORDER BY LoginId DESC";
//	else echo "NO RESULT????" . $this->cId;
	   if (isset($_COOKIE['cId']))
		   setcookie ("cId", "" ,time()-3600,"/",$this->cookieDomain);
// tmp!!!
		if ($this->database->query("SELECT * from Login where cID LIKE '$this->cId'") && $this->database->numRows()) {
//			echo "Found these sessions for the cookie. ($this->cId) (your IP: " . getenv(REMOTE_ADDR) . ") This is what is registered in the database:<br>";
			while ($res = $this->database->fetchArray()) {
//				print_r($res);
			}
		} else {
//			echo "Could not keep you logged on because your cookie no longer excist. ($this->cId)";
		}
	   
	   return false;
	}

	function closeSession ()
	{
//		$this->database->query("Update User set activeSessions=GREATEST((activeSessions-1),0) where userID='".$this->userID."'");
		// because if they double click only one will "close", so we close them all. still serves its purpose.
		$this->database->query("Update User set activeSessions=0 where userID='".$this->userID."'");
	}
	////////////////////////////////////////////
	// Buildings::logoff
	////////////////////////////////////////////
	// Function to log an user off the system.
	////////////////////////////////////////////

	function logoff () {
	   $this->database->query("UPDATE Login SET loggedon='N' WHERE cID LIKE '$this->cId'");
	   setcookie ("cId", "" ,time()-3600,"/",$this->cookieDomain);
//	   echo "Loggin off...UPDATE TABLE Login SET loggedon='N' WHERE cID LIKE '$this->cId'";
	}
	////////////////////////////////////////////
	// Buildings::getpID
	////////////////////////////////////////////
	// Function to return the users province ID.
	// Returns:
	//		the integer value of the users province id
	////////////////////////////////////////////
	function getpID() {
		return $this->pId;
	}
	
	function setUserData ($arr)
	{
		$this->nick = $arr['nick'];
		$this->username = $arr['username'];
		$this->password = $arr['password'];
		$this->userID = $arr['userID'];
		$this->pId = $arr['pID'];
		$this->signature = $arr['signature'];
		$this->access = $arr['access'];
		$this->name = $arr['name'];
		$this->chatName = $arr['chatName'];
		if (isset($arr['allowUserUpdateNick']))
			$this->allowUserUpdateNick = ($arr['allowUserUpdateNick']=='true') ? true:false;

		$this->allowUserUpdateImage = ($arr['allowUserUpdateImage']=='true') ? true:false;
		$this->allowUserUpdateSignature = ($arr['allowUserUpdateSignature']=='true') ? true:false;
		$this->email = $arr['email'];
		$this->image = $arr['image'];
		if (isset($arr['dob']))
			$this->dob = $arr['dob'];
		if (isset($arr['country']))
			$this->country = $arr['country'];
		if (isset($arr['status']))
			$this->status = $arr['status'];
		if (isset($arr['created']))
			$this->created = $arr['created'];
	}
	// update Functions
	function updateSignature($sig)
	{
		$this->signature=htmlspecialchars($sig);
		$this->signature = str_replace("\n","<br>", $this->signature);
		$this->database->query("UPDATE User set signature='$this->signature' where userID='$this->userID' AND allowUserUpdateSignature='true'");
	}
	function updateName ($newName) 
	{
		$this->name=htmlspecialchars($newName);
		$this->database->query("UPDATE User set name='$this->name' where userID='$this->userID'");
	}

	function updatePassword ($newPassword) 
	{
		$this->password=$newPassword;
		$this->database->query("UPDATE User set password='$this->password' where userID='$this->userID'");
	}
	
	function updateEmail ($newEmail) 
	{
		$this->email=$newEmail;
		$this->database->query("UPDATE User set email='$newEmail' where userID='$this->userID'");
	}
	function updateImage ($str)
	{
		$this->image = $str;
		$this->database->query("UPDATE User set image='$str' WHERE userID='".$this->userID."' AND allowUserUpdateImage='true'");
	}
	function updateNick ($str)
	{
		$this->nick = htmlspecialchars($str);
		$this->database->query("UPDATE User set nick='$this->nick' WHERE userID='".$this->userID."' AND allowUserUpdateNick='true'");
	}

	function updateChatName($str) {
		$toset = substr($str, 0,8);
		$this->chatName = $toset;
		$this->database->query("UPDATE User set chatName='$this->chatName' WHERE userID='".$this->userID."'");
	}
	
	function showDebugData()
	{
		echo "<br>Debug Data for User class:<br>";
		echo "-----------------------------------------------------<br>";
		echo "Number of objects created: " . $GLOBALS['user_objects_created'] . "<br>";
		echo "-----------------------------------------------------<br>";
	}
	
}
}
?>