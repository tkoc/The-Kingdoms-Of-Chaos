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

class myDatabase {
	var $errors = array ();
	var $dbarray= array ();
	var $link_id;
	
	
	function __construct ($dbhost,$dbusername,$dbpassword, $dbname) {
		$this->connectDB ($dbhost,$dbusername,$dbpassword, $dbname);
	}
	
	
	function connectDB ($dbhost,$dbusername,$dbpassword, $dbname) {
		/*@include_once ("./data/data.php");
		if (empty($dbhost)) {
			@include_once ("../data/data.php");
			if (empty($dbhost))
				@include_once ("../../data/data.php");
		}*/
		
		// Linking to the database
		$this->link_id = mysql_connect($dbhost,$dbusername,$dbpassword, true);
		if(!$this->link_id)
			die ('Could not connect this database:'. mysql_error());

		/*** Select the specific database ***/
		mysql_select_db($dbname,$this->link_id);
		/************************************/
	}
	
	function insertUser ($a1, $a2, $a3, $a4, $a5, $a6) {
		$data = array($a1, $a2, $a3, $a4, $a5, $a6);
		$data = $this->protectSQL ($data);
		/* Insert User */
		$query = "INSERT INTO User (userID, username, pID, history, access, lastPlayedAge) VALUES (";
		foreach ($data as $value)
			$query .= "$value,";

		$query[strlen ($query)-1] = ")";
				
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query '.$query.mysql_error().' </p>');
	}
	
	function insertPost ($a1, $a2, $a3, $a4, $a5, $a6, $a7) {
		$data = array($a1, $a2, $a3, $a4, $a5, $a6, $a7);
		$data = $this->protectSQL ($data);
		/* Insert User */
		$query = "INSERT INTO Forum (kiID, pID, poster, parent, title, message, dateSubmitted) VALUES (";
		foreach ($data as $value)
			$query .= "$value,";

		$query[strlen ($query)-1] = ")";
				
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query '.$query.mysql_error().' </p>');
	}
	
	
	function updateField ($table, $field, $value, $wherefield, $wherevalue) {
		$data=array($value, $wherevalue);
		$data = $this->protectSQL ($data);
		
		$query = "UPDATE $table SET $field=$data[0] WHERE $wherefield=$data[1]";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
	}
	
	function updateField2 ($table, $field, $value, $wherefield, $wherevalue, $wherefield2, $wherevalue2) {
		$data=array($value,$wherevalue,$wherevalue2);
		$data = $this->protectSQL ($data);
		
		$query = "UPDATE $table SET $field=$data[0] WHERE $wherefield=$data[1] AND $wherefield2=$data[2]";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
	}
	
	function selectField ($field, $table, $wherefield, $wherevalue) {
		$data=array($wherevalue);
		$data = $this->protectSQL ($data);
		
		$query = "SELECT $field FROM $table WHERE $wherefield=$data[0]";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
			
		return $result;
	}

	function selectField2 ($field, $table, $wherefield1, $wherevalue1, $wherefield2, $wherevalue2) {
		$data=array($wherevalue1, $wherevalue2);
		$data = $this->protectSQL ($data);
		
		$query = "SELECT $field FROM $table WHERE $wherefield1=$data[0] AND $wherefield2=$data[1]";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
			
		return $result;
	}
	
	function selectField3 ($field, $table, $wherefield1, $wherevalue1, $wherefield2, $wherevalue2, $orderfield, $ordertype) {
		$data=array($wherevalue1, $wherevalue2);
		$data = $this->protectSQL ($data);
		
		$query = "SELECT $field FROM $table WHERE $wherefield1=$data[0] AND $wherefield2=$data[1] ORDER BY $orderfield $ordertype";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
			
		return $result;
	}
	
	function selectField4 ($field, $table, $wherefield1, $wherevalue1, $wherefield2, $wherevalue2, $wherefield3, $wherevalue3) {
		$data=array($wherevalue1, $wherevalue2, $wherevalue3);
		$data = $this->protectSQL ($data);
		
		$query = "SELECT $field FROM $table WHERE $wherefield1=$data[0] AND $wherefield2=$data[1] AND $wherefield3=$data[2]";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
			
		return $result;
	}
	
	function selectField5 ($field, $table, $wherefield1, $wherevalue1, $orderfield, $ordertype) {
		$data=array($wherevalue1);
		$data = $this->protectSQL ($data);
		
		$query = "SELECT $field FROM $table WHERE $wherefield1=$data[0] ORDER BY $orderfield $ordertype";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
			
		return $result;
	}
	
	function selectField6 ($field, $table, $orderfield, $ordertype) {
		
		$query = "SELECT $field FROM $table ORDER BY $orderfield $ordertype";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
			
		return $result;
	}
	
	function deleteRow ($table, $wherefield1, $wherevalue1, $wherefield2, $wherevalue2) {
		$data=array($wherevalue1, $wherevalue2);
		$data = $this->protectSQL ($data);
		
		$query = "DELETE FROM $table WHERE $wherefield1=$data[0] AND $wherefield2=$data[1]";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
			
		$query = "OPTIMIZE TABLE $table";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
	}
	
	function deleteRow2 ($table, $wherefield1, $wherevalue1) {
		$data=array($wherevalue1);
		$data = $this->protectSQL ($data);
		
		$query = "DELETE FROM $table WHERE $wherefield1=$data[0]";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
			
		$query = "OPTIMIZE TABLE $table";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
	}
	
	function deleteRow3 ($table, $wherefield1, $wherevalue1) {
		$data=array($wherevalue1);
		$data = $this->protectSQL ($data);
		
		$query = "DELETE FROM $table WHERE $wherefield1 < $data[0]";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
			
		$query = "OPTIMIZE TABLE $table";
		
		$result = mysql_query($query,$this->link_id);
		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
	}
	
	function emptyTable ($table) {
		$query = "TRUNCATE TABLE $table";
		$result = mysql_query($query,$this->link_id);

		if (!$result)
			die('<p id="error">Could not perform query'.$query.mysql_error().'</p>');
	}
	
	function protectSQL ($data) {
		if(get_magic_quotes_gpc()) {
			foreach ($data as &$value)
				$value = stripslashes ($value);
		}
		foreach ($data as &$value) {
			$new_value = mysql_real_escape_string($value ,$this->link_id);
			if (empty($new_value) && !empty($value)) {
            	die("mysql_real_escape_string failed.");
        	}
        	$value = $new_value;
			if (!is_numeric($value)) {
				$value = "'".$value."'";
			}
		}
		
		return $data;
	}
	
	function verifyValue($result) {
		$rows = mysql_num_rows($result);
		if ( $rows == 0)
			return true;
		
		return false;
	}
	
	
	function equalValues ($value1, $value2) {
		if ( strcmp($value1,$value2) == 0)
			return true;
		
		return false;
	} 
	
	
}

?>