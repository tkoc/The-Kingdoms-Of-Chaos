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

/*
 * Execute all queries found in .sql files
 *
 * @author Petros Karipidis
 * @since 25/02/2011
 */
class createDatabaseTables {
	private $dbhost;
	private $dbusername;
	private $dbpassword;
	private $dbnameGame;
	private $dbnameForum;
	private $listSQLFiles = array();
	private $link;
	
	public function __construct() {
		$this->getAndInitializeData();
		$this->dbConnect();
		$this->startCreatingTables();
	}

	private function getAndInitializeData () {
		$path = "./data/data.php";
        @include ($path);
        while (empty($currentPath)) {
            $path = "../".$path;
            @include ($path);
        }
		$this->dbhost = $dbhost;
		$this->dbusername = $dbusername;
		$this->dbpassword = $dbpassword;
		$this->dbnameGame = $dbname;
		$this->dbnameForum = $dbForum;
		
		$this->listSQLFiles = glob("*.sql");
	}

	private function dbConnect() {
		$this->link = mysql_connect($this->dbhost,$this->dbusername,$this->dbpassword, true);
		if(!$this->link)
			die ('Could not connect to database:'. mysql_error());
	}

	private function dbSelect($dbname) {
		mysql_select_db($dbname,$this->link);
	}

	private function startCreatingTables() {
		foreach ($this->listSQLFiles as $SQLFile) {
			if (strstr ($SQLFile, "forum"))
				$this->dbSelect ($this->dbnameForum);
			else
				$this->dbSelect ($this->dbnameGame);

			$sql = file_get_contents($SQLFile);
			$this->createTables($sql);
		}
	}

	private function createTables($sql) {
		$queries = explode ("\n\n", $sql);

		foreach ($queries as $query) {
			$splitspace = explode (" ", $query);
			$table = str_replace("`", "", $splitspace['2']);

			$result = mysql_query($query,$this->link);
			if (!$result)
				die('<p id="error">Could not perform query '.$query.mysql_error().' </p>');
			else
				echo "$table created";
			//echo "$query";
			echo "<br /><br />";
		}
	}
}

new createDatabaseTables();
?>