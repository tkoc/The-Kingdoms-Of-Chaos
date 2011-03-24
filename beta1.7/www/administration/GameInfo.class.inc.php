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
if( !class_exists( "GameInfo" ) ) {
error_reporting(E_ALL);
class GameInfo {
	var $database;
	var $provinceTable 			= "Province";
	var $kingdomTable			= "Kingdom";
	var $userTable				= "User";
	var $loginTable				= "Login";
	var $configTable			= "Config";
	

	// user / province info
	var $totalUsers				= -1;				// number of users in db
	var $totalProvinces			= -1;				// total number of province
	var $pendelingProvinces		= -1;				// provinces awaiting deletion for inactivity
	var $deadProvinces			= -1;				// provinces with status dead
	var $killedProvinces		= -1;				// provinces with status killed
	var $deletedProvinces		= -1;				// provinces with status deletedCheater
	var $activityToday			= -1;				// Logins today (24 hours)
	var $uniqeActivityToday		= -1;				// uniqe logins today
	var $highestGoldCollected	= -1;				// highest gold count on a province
	var $highestMetalCollected	= -1;				// highest metal count on a province
	var $highestFoodCollected	= -1;				// highest foood count on a province
	var $highestIncome			= -1;				// province with best income
	var $highestReputation		= -1;				// highest reputation count for a province
	
	// Kingdom info
	
	var $totalKingdoms			= -1;				// Total number of kingdoms
	var $emptyKingdoms			= -1;				// number of empty Kingdoms
	var $avgKingdomSize			= -1;				// average number of provinces in kingdom
	var $numberOfLeaders		= -1;				// total number of king/queens
	
	// game / info

	var $gameStatus				= "";				// status of game: Running, Pause or Ended
	var $uptime					= -1;				// how many ticks has this age lasted?
	var $endTime				= -1;				// how long until the game ends?
	var $startTime				= -1;				// when will game start (if gameStatus=='Pause')
	var $lastTick				= "";				// date with last tick
	var $nextTick				= "";				// date with next tick.
	
	/* GameInfo (&$database)
	 *
	 * sets up the object.  collects data from server.
     */
	
	function GameInfo (&$databaseRef)
	{
		$this->database = $databaseRef;
		
		$this->getProvinceInfo();
		$this->getKingdomData();
	}
	
	
	function getKingdomData ()
	{
		$this->database->query("SELECT count(*) as totalKingdoms FROM Kingdom");
		$n = $this->database->fetchArray();
		$this->totalKingdoms = $n['totalKingdoms'];
		$this->database->query("SELECT count(*) as emptyKingdoms FROM Kingdom WHERE numProvinces=0");
		$n = $this->database->fetchArray();
		$this->emptyKingdoms = $n['emptyKingdoms'];
		$this->database->query("SELECT AVG(numProvinces) as avgKingdomSize FROM Kingdom");
		$n = $this->database->fetchArray();
		$this->avgKingdomSize = $n['avgKingdomSize'];
		$this->database->query("SELECT count(*) as numberOfLeaders FROM Kingdom where king>0");
		$n = $this->database->fetchArray();
		$this->numberOfLeaders = $n['numberOfLeaders'];
		
	}
	
	/* getProvinceInfo
	 *
	 * Fyller inn data i provinsinfo sakene,
	 */
	function getProvinceInfo ()
	{
		$this->database->query("SELECT count(*) as totalUsers FROM $this->userTable");
		$n = $this->database->fetchArray();
		$this->totalUsers = $n['totalUsers'];
		$this->database->query("SELECT count(*) as pendelingProvinces FROM $this->provinceTable WHERE kiID<0");
		$n = $this->database->fetchArray();
		$this->pendelingProvinces = $n['pendelingProvinces'];
		$this->database->query("SELECT count(*) as totalProvinces FROM $this->provinceTable");
		$n = $this->database->fetchArray();
		$this->totalProvinces = $n['totalProvinces'];
		$this->database->query("SELECT count(*) as deadProvinces FROM $this->provinceTable WHERE status='Deleted'");
		$n = $this->database->fetchArray();
		$this->deadProvinces = $n['deadProvinces'];
		$this->database->query("SELECT count(*) as killedProvinces FROM $this->provinceTable WHERE status='Killed'");
		$n = $this->database->fetchArray();
		$this->killedProvinces = $n['killedProvinces'];
		$this->database->query("SELECT count(*) as deletedProvinces FROM $this->provinceTable WHERE status='DeletedCheater'");
		$n = $this->database->fetchArray();
		$this->deletedProvinces = $n['deletedProvinces'];
		$this->database->query("select count(loginId) as activityToday from $this->loginTable where (UNIX_TIMESTAMP()-UNIX_TIMESTAMP(timestamp))<(3600*24)");
		$n = $this->database->fetchArray();
		$this->activityToday = $n['activityToday'];
		$this->database->query("select count(distinct pID) as uniqeActivityToday from $this->loginTable where (UNIX_TIMESTAMP()-UNIX_TIMESTAMP(timestamp))<(3600*24);");
		$n = $this->database->fetchArray();
		$this->uniqeActivityToday = $n['uniqeActivityToday'];
		$this->database->query("SELECT MAX(gold) as gold, MAX(food) as food, MAX(metal) as metal, MAX(reputation) as reputation FROM $this->provinceTable");
		$n = $this->database->fetchArray();
		$this->highestGoldCollected = $n['gold'];
		$this->highestFoodCollected = $n['food'];
		$this->highestMetalCollected = $n['metal'];
		$this->highestReputation = $n['reputation'];
	}
	
	function showKingdomInfo()
	{
		$html = '<TABLE bgcolor=#CCCCCC cellpadding=5 cellspacing=0 border=0>
					<TR>
						<TD>Kingdoms:</TD>
						<TD>'.$this->totalKingdoms.' ('.$this->emptyKingdoms.' empty kingdoms)</TD>
					</TR>
					<TR>
						<TD>Avg Kingdom Size:</TD>
						<TD>'.$this->avgKingdomSize.'</TD>
					</TR>
					<TR>
						<TD>Kings/Queens:</TD>
						<TD>there are '.$this->numberOfLeaders.' kings/queens</TD>
					</TR>
				</TABLE>';
		return $html;
	
	}
	
	function showProvinceInfo ()
	{
		$html = '<TABLE bgcolor=#CCCCCC cellpadding=5 cellspacing=0 border=0>
					<TR>
						<TD>Users:</TD>
						<TD>'.$this->totalUsers.'</TD>
					</TR>
					<TR>
						<TD>Logins today:</TD>
						<TD>'.$this->activityToday.' ('.$this->uniqeActivityToday.' uniqe)</TD>
					</TR>
					<TR>
						<TD>Provinces:</TD>
						<TD>'.$this->totalProvinces.' ('.$this->pendelingProvinces.' inactive)</TD>
					</TR>
					<TR>
						<TD>Killed provinces:</TD>
						<TD>'.$this->killedProvinces.'</TD>
					</TR>
					<TR>
						<TD>Highest resource in province(gold/food/metal):</TD>
						<TD>'.$this->highestGoldCollected.'/'.$this->highestFoodCollected.'/'.$this->highestMetalCollected.'</TD>
					</TR>
					<TR>
						<TD>Best reputation:</TD>
						<TD>'.$this->highestReputation.'</TD>
					</TR>
				</TABLE>';
			return $html;
	}
}

}
?>