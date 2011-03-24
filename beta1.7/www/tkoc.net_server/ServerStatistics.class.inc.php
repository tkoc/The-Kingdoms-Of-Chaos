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
if( !class_exists( "ServerStatistics" ) ) {
require_once($GLOBALS['path_www_scripts'] . "all.inc.php");
/*
CREATE TABLE Log (
	ID int(11) NOT NULL auto_increment PRIMARY KEY,
	Age int(11) DEFAULT '-1',
	Provinces int(11) DEFAULT '0',
	UsersActive int(11) DEFAULT '0',
	UsersTotal int(11) DEFAULT '0',
	Logins int(11) DEFAULT '0',
	TickTime datetime,
	Tick int(11)  DEFAULT '0',
	ServerStatus enum('Running','Pause','Ended','Beta')
);

*/

class ServerStatistics
{
	var $Database = NULL;
	var $Config   = array();

	function ServerStatistics ($databaseReference) 
	{
		$this->Database = $databaseReference;
		$this->Config = $GLOBALS['config'];
	}
	
	function DoTick()
	{
		$this->Database->query("SELECT count(pID) as TotalProvinces FROM Province where status='Alive'");
		$a = $this->Database->fetchArray();
		$TotalProvinces = $a['TotalProvinces'];

		$this->Database->query("SELECT count(userID) as TotalUsers FROM User");
		$a = $this->Database->fetchArray();
		$TotalUsers = $a['TotalUsers'];

		$this->Database->query("SELECT count(userID) as ActiveUsers FROM User where status='Active'");
		$a = $this->Database->fetchArray();
		$ActiveUsers = $a['ActiveUsers'];
		
		// number of logins last hour
		$this->Database->query("select count(loginId) as Logins from Login where (UNIX_TIMESTAMP()-UNIX_TIMESTAMP(timestamp))<(3600)");
		$a = $this->Database->fetchArray();
		$Logins = $a['Logins'];
		
		$this->Database->query("INSERT INTO Log (Age,Provinces,UsersActive,UsersTotal,Logins,TickTime,Tick,ServerStatus ) VALUES
								('".$this->Config['age']."','$TotalProvinces','$ActiveUsers','$TotalUsers','$Logins',NOW(),
								 '".$this->Config['ticks']."','".$this->Config['status']."')");
	}
}

}
?>
