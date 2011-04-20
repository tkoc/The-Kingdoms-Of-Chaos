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
	require_once ("all.inc.php");
	$GLOBALS['user_objects_created'] = 0;
	class User {
		var $pId		= -1;
		var $userID		= "";
		var $username	= "";
		var $access 	= 0;
		var $database	= false;
	
		////////////////////////////////////////////
	
		// User::User
	
		////////////////////////////////////////////
	
		// Constructor
	
		// takes a coockie and a reference to a database object
	
		////////////////////////////////////////////
	
		function User ($databaseReference) {
		   $this->database = $databaseReference;
	
		   $GLOBALS['user_objects_created']++;
		}
	
	
		function isLoggedOn() {				
			$result_smf = mysql_fetch_array ($GLOBALS['forumdb']->selectField ("*", "smf_members", "member_name", $GLOBALS['context']['user']['username'])); // Take the data from smf
			$result = mysql_fetch_array ($GLOBALS['db']->selectField ("*", "User", "username", $GLOBALS['context']['user']['username'])); // Find him at the game users table
			
			if (!isset($result['username']) || $result['username'] == "") { // Add him to the game database if he doesn't exist
				//$created = date ("Y-m-d", $result_smf['date_registered']);
				$GLOBALS['db']->insertUser ($result_smf['id_member'], $result_smf['member_name'], -1, "", 3, -1);
				$result = mysql_fetch_array ($GLOBALS['db']->selectField ("*", "User", "username", $GLOBALS['context']['user']['username']));
			}
			
			// Update the user's last access to his province
			if ($result["pID"] != -1) {
				$GLOBALS['db']->updateField ("Province", "lastAccess", time(), "pID", $result["pID"]);
			}
			
			// Set inactive users back to active
			//$GLOBALS['db']->updateField2 ("Province", "protection", 0, "pID", $result["pID"], "status", "Inactive");
			//$GLOBALS['db']->updateField2 ("Province", "status", "Alive", "pID", $result["pID"], "status", "Inactive");
			$GLOBALS['database']->query ("UPDATE Province set status='Alive', protection=0 WHERE pID=".$result["pID"]." AND status='Inactive'");
			
			$this->setUserData($result);
			/*// Forum Nick Hack
			if ($this->pId != -1) {
				$result = mysql_fetch_array ($GLOBALS['db']->selectField ("*", "Province", "pID", $this->pId)); // Find his province
				$data = $result['rulerName']." in <br />".$result['provinceName'];
				$GLOBALS['db']->updateField ("User", "nick", $data, "pID", $this->pId); // Find him at the game users table
			}*/
				
			return true;
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
			$this->username = $arr['username'];
			$this->userID = $arr['userID'];
			$this->pId = $arr['pID'];
			$this->access = $arr['access'];
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