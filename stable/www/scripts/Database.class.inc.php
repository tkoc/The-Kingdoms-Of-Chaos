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
//**********************************************************************
//* Database management class.  Maintains a connection to a mysql database.
//* The shutdown function needs to be called at the end of script, or the
//* connection to the database will not die.
//*	
//* 
//* Author: Anders Elton
//*
//* Changelog
//* Anders Elton	12.08.04 Added affectedRows() (alias for mysql_affected_rows)
//* Anders ELton	01.08.04 Anders Elton.  Rewrote to match new coding style
//* Anders ELton	01.08.04 added debug options.
//* Anders Elton 	17.11.03 added pconnect to reduce load
//* Anders Elton 	13.11.03 removed pconnect in hope of avoiding database crash
//* Anders Elton 	22.08.03 added pconnect in hope of avoiding bug
//* Øystein Fladby	18.03.03 added optional parameter to fetchArray
//* Anders Elton    	18.03.03 error now returns the error insted of display it.
//* Anders Elton    	05.03.03  (added @ to make silent errors.) added true/false check in fetcharray 
//* Jorgen Belsaas  	17.02.03  (added numRows) 
//*	
//**********************************************************************

if( !class_exists( "Database" ) ) {

// statistics
$GLOBALS['database_queries_count'] 		 = 0;
$GLOBALS['database_queries_fetch_count'] = 0;
$GLOBALS['database_total_bytes_fetched'] = 0;

// debug
$GLOBALS['database_last_query']			 = "";

class Database {
	// private vars 
 		var $host 		= "";
		var $username 	= "";
		var $password 	= "";
		var $database 	= "";
		var $link		= false;
		var $result		= false;	
	// Constructor
	
		function Database ($u, $p, $h,$db) 
		{
			$this->username= $u;
			$this->password =$p;
			$this->host = $h;
			$this->database = $db;
		}

	////////////////////////////////////////////
	// Database::Connect
	////////////////////////////////////////////
	// Connects to the database
	// Returns:
	//    true  - success
	//    false	- fail
	////////////////////////////////////////////
		function connect () {
//			$this->link = @mysql_pconnect($this->host,$this->username,$this->password);
			$this->link = @mysql_connect($this->host,$this->username,$this->password, true);
			if (!$this->link) return false;
			mysql_select_db($this->database, $this->link);
			return true;
		}
	////////////////////////////////////////////
	// Database::Shutdown
	////////////////////////////////////////////
	// Terminates database connection.  This
	// function *must* be called, or the connection
	// will never close..
	////////////////////////////////////////////
		
		function shutdown () {
		// disabeled to fix register_shutdown
//			@mysql_close($this->link);
		}
	////////////////////////////////////////////
	// Database::safeQuery
	////////////////////////////////////////////
	// Preforms a safe query.
	// Returns:
	//    the result.
	// @ See also mysq_query function
	////////////////////////////////////////////

 		function safeQuery ($q) 
		{
			$GLOBALS['database_queries_count']++;
			$GLOBALS['database_last_query']	  = $q;
			$this->result = @mysql_query(mysql_escape_string($q),$this->link);
			return $this->result;
		}

	////////////////////////////////////////////
	// Database::query
	////////////////////////////////////////////
	// Preforms a query.
	// Returns:
	//    the result.
	// @ See also mysq_query function
	////////////////////////////////////////////

 		function query ($q) {
			$GLOBALS['database_queries_count']++;
			$GLOBALS['database_last_query']	  = $q;
			$this->result = @mysql_query($q,$this->link);
			return $this->result;
		}

	////////////////////////////////////////////
	// Database::fetchArray
	////////////////////////////////////////////
	// Grabs array from query result
	// No parameter -> last result
	// Returns:
	//    The assosiative array
	// @ See aslo mysql_fetch_array function
	////////////////////////////////////////////
		function fetchArray ( $result=false ) {
			$GLOBALS['database_queries_fetch_count']++;
			if( !$result ) {
				$result = $this->result;
			}
			$arr = @mysql_fetch_array($result, MYSQL_ASSOC);
			return $arr;
		}

	////////////////////////////////////////////
	// Database::queryOk
	////////////////////////////////////////////
	// Returns result of last query
	// Returns:
	//    the result.
	////////////////////////////////////////////
		function queryOk() {
			return $this->result;
		}

	////////////////////////////////////////////
	// Database::lastInsertId
	////////////////////////////////////////////
	// Returns last inserted id.
	// Returns:
	//    Id
	// @ See also mysql_insert_id function
	////////////////////////////////////////////
		function lastInsertId(){
			return @mysql_insert_id($this->link);
		}
	// comment.
		function numRows() {
			return @mysql_num_rows($this->result);
		}
		function affectedRows()
		{
			return @mysql_affected_rows($this->link);
		}
	
	////////////////////////////////////////////
	// Database::setRecordOffset
	////////////////////////////////////////////
	// Sets the record pointer to the given record number
	////////////////////////////////////////////
		function setRecordOffset( $offset, $result=false ) {
			$GLOBALS['database_queries_fetch_count']++;
			if( $result ) {
				return @mysql_data_seek( $result, $offset );
			} else {
				return @mysql_data_seek( $this->result, $offset );
			}
		}		
		
	////////////////////////////////////////////
	// Database::error
	////////////////////////////////////////////
	// returns mysql error msg.
	////////////////////////////////////////////
		function error(){
			return @mysql_errno() . ": " . @mysql_error() . "\n";
		}

	function showDebugData()
	{
		echo "<br>Debug Data for Game database:<br>";
		echo "-----------------------------------------------------<br>";
		echo "Number of queries:       " . $GLOBALS['database_queries_count'] . "<br>";
		echo "Number of fetches:       " . $GLOBALS['database_queries_fetch_count'] . "<br>";
		echo "Number of bytes recived: " . $GLOBALS['database_total_bytes_fetched'] . "<br>";
		echo "Last query:              " . wordwrap ($GLOBALS['database_last_query'],80,"\n") . "<br>";
		echo "-----------------------------------------------------<br>";
	}
}
}
?>
