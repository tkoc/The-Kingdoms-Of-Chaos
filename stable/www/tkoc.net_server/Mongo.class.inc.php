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
//************************************************
//* file: Server.class.inc.php
//*
//* This is the server engine.
//* 
//* Histry:
//*
//*	31.07.04: Anders Elton.  Send out warning email to inactive provinces.
//*
//*	17.02.04: Anders Elton.  Overpopulation kills less peasants. There will always be at least
//*							one peasant increase in population.
//*
//************************************************

if( !class_exists( "Server" ) ) {
require_once($GLOBALS['path_www_scripts'] . "all.inc.php");
class Server {
	var $database;
	var $errorLogFile	="";		// set in constructor!
	var $logFile	 	="";		// set in constructor!
	var $config;
	var $PEASANT_BIRTH = "0.025";
	var $PEASANT_EATS  = "0.4";
	var $PEASANT_EARNS = "3.0";
	var $INACTIVE_DAYS = 4;
	var $REMOVE_DAYS   = 10;
	var $STARVE_KILL_UNITS = 0.004;
	var $OVERPOPULATED_KILL_UNITS = 0.006;
	var $VACATION_MODE_MIN = 72;  // in hours
	var $VACATION_MODE_MAX = 336; // 14 days.

	
	function Server (&$databaseReference) 
	{
		$this->database = &$databaseReference;
		$this->config = $GLOBALS['config'];
		$this->errorLogFile = $GLOBALS['FILE_ERROR_LOG'];
		$this->logFile = $GLOBALS['FILE_LOG'];
		$this->VACATION_MODE_MIN = $this->config['vacationMin'];
		$this->VACATION_MODE_MAX = $this->config['vacationMax'];
	}
	/* Reset fields, calculate base values */
	function prepareTick () {
		$this->database->query("UPDATE Province SET foodChange = 0") or $this->error("Fatal error in server.class.inc.php (prepareTick)1");
		$this->database->query("UPDATE Province SET metalChange = 0")or $this->error("Fatal error in server.class.inc.php (prepareTick)2");
		$this->database->query("UPDATE Province SET peasantChange = 0") or $this->error("Fatal error in server.class.inc.php (prepareTick)3");
		$this->database->query("UPDATE Province SET incomeChange = 0") or $this->error("Fatal error in server.class.inc.php (prepareTick)4");
		$this->database->query("UPDATE Province SET aliveTicks = aliveTicks+1 WHERE aliveTicks>-1") or $this->error("Fatal error in server.class.inc.php (prepareTick)5");
		$this->database->query("UPDATE Province SET protection = protection-1 WHERE aliveTicks>-1 AND protection>0") or $this->error("Fatal error in server.class.inc.php (prepareTick)5");
		$this->database->query("UPDATE Province SET vactionTicks = vactionTicks+1 WHERE vacationmode='true'") or $this->error("Fatal error in server.class.inc.php (prepareTick)5");
		$this->growPeasants();
		$this->growFood();
		$this->growMetal();
		$this->growGold();
	}
	function doTick () {

		// HACK: fix sessions
		$this->database->query("UPDATE User set activeSessions=0")
			or $this->error("Fatal error in server.class.inc.php (doTick)0");
		
		// HACK: fix king/queen bonus
		$this->database->query("UPDATE Province,Kingdom set incomeChange = incomeChange*1.1 WHERE Kingdom.king=Province.pID")
			or $this->error("Fatal error in server.class.inc.php (doTick)1");
		$this->database->query("UPDATE Province,Kingdom set foodChange = foodChange*1.1 WHERE Kingdom.king=Province.pID")
			or $this->error("Fatal error in server.class.inc.php (doTick)2");
		$this->database->query("UPDATE Province,Kingdom set metalChange = metalChange*1.1 WHERE Kingdom.king=Province.pID")
			or $this->error("Fatal error in server.class.inc.php (doTick)3");

		// Update Totals.
		if ($this->config['ticks']%24 == 0) {
			$this->database->query("UPDATE Province SET metalTotal = 0");
			$this->database->query("UPDATE Province SET foodTotal = 0");
			$this->database->query("UPDATE Province SET incomeTotal = 0");
			$this->database->query("UPDATE Province SET peasantTotal = 0");
		}

		// Update resources in Province
		// metal
		$this->database->query("UPDATE Province SET metalTotal = metalTotal+metalChange")
			or $this->error("Fatal error in server.class.inc.php (doTick)4");
		$this->database->query("UPDATE Province SET metal = metal+metalChange WHERE vacationmode='false'")
			or $this->error("Fatal error in server.class.inc.php (doTick)5");
		// food
		$this->database->query("UPDATE Province set foodChange=foodChange-((peasants+militaryPopulation)*$this->PEASANT_EATS)");
		$this->database->query("UPDATE Province SET foodTotal = foodTotal+foodChange")
			or $this->error("Fatal error in server.class.inc.php (doTick)6");
		$this->database->query("UPDATE Province SET food = food + foodChange WHERE vacationmode='false'")
			or $this->error("Fatal error in server.class.inc.php (doTick)7");
		// gold
		$this->database->query("UPDATE Province SET incomeTotal = incomeTotal+incomeChange")
			or $this->error("Fatal error in server.class.inc.php (doTick)8");
		$this->database->query("UPDATE Province SET gold = incomeChange + gold WHERE vacationmode='false'")
			or $this->error("Fatal error in server.class.inc.php (doTick)9");
		
		// peasants
		$this->database->query("UPDATE Province SET peasantChange=-(buildingPeasantPopulation*$this->PEASANT_BIRTH) WHERE (buildingPeasantPopulation-militaryPopulation-peasants)<0")
			or $this->error("Fatal error in server.class.inc.php (doTick)9.5");
		$this->database->query("UPDATE Province SET peasantChange= LEAST((buildingPeasantPopulation-militaryPopulation-peasants),(peasantChange)) WHERE peasantChange>0")
			or $this->error("Fatal error in server.class.inc.php (doTick)10");
		// HACK: always "grow" one peasant.
		$this->database->query("UPDATE Province SET peasantChange=peasantChange+1 WHERE (peasantChange+militaryPopulation+peasants)<buildingPeasantPopulation")
			or $this->error("Fatal error in server.class.inc.php (doTick)10");

		// starve effect on peasants.
		$this->database->query("UPDATE Province set peasantChange = -ABS(peasantChange) WHERE food<0")
			or $this->error("Fatal error in server.class.inc.php");

		$this->database->query("UPDATE Province SET peasants= peasants + peasantChange WHERE vacationmode='false'")
			or $this->error("Fatal error in server.class.inc.php (doTick)11");
		$this->database->query("UPDATE Province SET peasantTotal = peasantTotal+peasantChange")
			or $this->error("Fatal error in server.class.inc.php (doTick)12");

		$this->handleBadResources();  // includes starving, etc.

		$this->database->query("UPDATE Config set ticks=(ticks+1), lastTickTime=NOW()")
			or $this->error("Fatal error in server.class.inc.php (doTick)13");
		
		$this->handleDead();
			
	}
	
	function handleDead() {
		$this->database->query("UPDATE Province set status='Killed', kiID=-ABS(kiID) WHERE acres=0 AND vacationmode='false'");
		$this->database->query("SELECT MAX(timestamp) as lastLogin,User.pID,User.userID,Province.kiID as kiID ,Province.status as status,(TO_DAYS(curdate())-TO_DAYS(User.created)) as userCreated ,Province.provinceName,User.name,(TO_DAYS(curdate())-TO_DAYS(MAX(timestamp))) as days from User LEFT JOIN Login on User.userID=Login.userID LEFT JOIN Province ON Province.pID=User.pID WHERE User.pID>0 AND Province.vacationmode='false' GROUP by User.userID ORDER BY lastLogin ASC");
		$cleanup = false;
		if ($this->database->numRows())	 {
			while ($provlist[] = $this->database->fetchArray());
			reset($provlist);

			foreach ($provlist as $prov) {
				if (intval($prov['kiID']) < 0) {
					if ($prov['status']!='Alive') {
						$cleanup=true;
						$this->database->query("UPDATE User set pID=-1 WHERE userID=$prov[userID]");
						$this->removeProvince($prov['pID']);
					}
					if (is_null($prov['userCreated'])) {
						$cleanup=true;
						$this->database->query("UPDATE User set pID=-1 WHERE userID=$prov[userID]");
						$this->removeProvince($prov['pID']);
					}
					if (intval($prov['days']) > $this->REMOVE_DAYS)	{
						$cleanup=true;
						$this->database->query("UPDATE User set pID=-1 WHERE userID=$prov[userID]");
						$this->removeProvince($prov['pID']);
					}
				
				}
				if ($prov['status']!='Alive' && $prov['kiID']>'0' && $prov['days']>$this->INACTIVE_DAYS) {
					$this->database->query("UPDATE Province set kiID=-ABS(kiID) where pID='$prov[pID]'");
				}
				if (is_null($prov['days'])) {
					if (is_null($prov['userCreated'])) {
						$cleanup=true;
						$this->database->query("UPDATE Province set kiID=-ABS(kiID) where pID='$prov[pID]'");
						// move to trash kingdom (-1)
					} 
				} else if ($prov['days']>$this->INACTIVE_DAYS) {
					$cleanup=true;
					if ($prov['kiID'] > 0)
					{
						$this->sendInactiveWarning($prov['userID']);
					}
					$this->database->query("UPDATE Province set kiID=-ABS(kiID) where pID='$prov[pID]'");
				} else if ($prov['days']<=$this->INACTIVE_DAYS && $prov['status']=='Alive' && $prov['kiID']<0) {
					//find kingdom.
					//$this->database->query("SELECT * from Kingdom");
					$this->database->query("UPDATE Province set kiID=ABS(kiID) where pID='$prov[pID]'");
					$this->writeLog ($this->logFile,"$prov[pID] is no longer inactive!\n" );
				} 
			}
		}
		$this->fixKingdoms();
		$this->removeResetProvince();
//		if ($cleanup) {
//			$this->writeLog ($this->logFile,"\nCleanup on kingdoms.\n" );
//			require("/home/prosjekt/scripts/fixKingdoms.php");
//		}
	
	}
	
	function handleBadResources() 
	{

		// starving. dont grow peasants
		$this->database->query("UPDATE Province set peasants = peasants-ABS(peasantChange*2) WHERE food<0")
			or $this->error("Fatal error in server.class.inc.php (handleBadResrouces)1");
		// kill them!
		$this->database->query("UPDATE Province set peasants = GREATEST(peasants,0)")
			or $this->error("Fatal error in server.class.inc.php (handleBadResrouces)2" . $this->database->error());
//		$this->database->query("UPDATE Province set peasants = GREATEST((peasants-(foodChange*$this->PEASANT_EATS)),10) WHERE food<0")
//			or $this->error("Fatal error in server.class.inc.php (handleBadResrouces)2" . $this->database->error());
		// reduce income also. (50%)
		$this->database->query("UPDATE Province set gold= GREATEST((gold-(ABS(incomeChange)*0.5)),0) WHERE food<0")
			or $this->error("Fatal error in server.class.inc.php (handleBadResrouces)3");
		// reset food to 0.
		$this->database->query("UPDATE Province set food=0 WHERE food<0")
			or $this->error("Fatal error in server.class.inc.php (handleBadResrouces)4");



		// overpopulation
		// overpopulated if (max pop - total pop) < -acres
		// loose acres/10 units of each kind if you are overpopulated
		$this->database->query("SELECT pID,acres,buildingPeasantPopulation,militaryPopulation, food 
									FROM Province 
										WHERE ((buildingPeasantPopulation-militaryPopulation-peasants)<(-acres))
										OR (
											((buildingPeasantPopulation-militaryPopulation-peasants)<(buildingPeasantPopulation*0.05))
											AND ((buildingPeasantPopulation*0.9)<militaryPopulation)
											)
										OR (food<0)");
		if ($this->database->numRows() > 0)
		{
			while ($a =$this->database->fetchArray())
			{
				if ((($a['buildingPeasantPopulation']*0.9) < $a['militaryPopulation']) || ($a['food']<0))
					$overPopulatedProvinces[] = $a;
			}
			reset($overPopulatedProvinces);
			$c = 0;
			$d = 0;
			foreach ($overPopulatedProvinces as $i)
			{
				$c++;
				$victimProvince = new Province($i['pID'],$this->database);
				$victimProvince->getProvinceData();
				$victimProvince->getMilitaryData();
				$GLOBALS['province'] = $victimProvince;
				// WARNING cutn paste from assasinate military
				$enemyThieves  = $victimProvince->milObject->getMilUnit($victimProvince->milObject->MilitaryConst->THIEVES);
				$enemyWizards  = $victimProvince->milObject->getMilUnit($victimProvince->milObject->MilitaryConst->WIZARDS);
				$enemySoldiers = $victimProvince->milObject->getMilUnit($victimProvince->milObject->MilitaryConst->SOLDIERS);
				$enemyDef = $victimProvince->milObject->getMilUnit($victimProvince->milObject->MilitaryConst->DEF_SOLDIERS);
				$enemyOff = $victimProvince->milObject->getMilUnit($victimProvince->milObject->MilitaryConst->OFF_SOLDIERS);
				$enemyElite = $victimProvince->milObject->getMilUnit($victimProvince->milObject->MilitaryConst->ELITE_SOLDIERS);
				if ($i['food']<0)
					$kill = $this->STARVE_KILL_UNITS;
				else
					$kill = $this->OVERPOPULATED_KILL_UNITS;
				// what to kill?
				$killSoldiers = intval ($enemySoldiers['num'] * $kill);
				$killOff = intval ($enemyOff['num'] * $kill);
				$killDef = intval ($enemyDef['num'] * $kill);
				$killElite = intval ($enemyElite['num'] * $kill);
				$killThieves = intval ($enemyThieves['num'] * $kill);
				$killWizards = intval ($enemyWizards['num'] * $kill);
				$sum = $killSoldiers + $killOff + $killDef + $killElite + $killThieves + $killWizards;
				
				// the killin'
				if ($sum > 0)
				{
					$d++;
					if ($killThieves>0)
						$victimProvince->milObject->killUnits($GLOBALS['MilitaryConst']->THIEVES,$killThieves );
					if ($killWizards>0)
						$victimProvince->milObject->killUnits($GLOBALS['MilitaryConst']->WIZARDS,$killWizards );
					if ($killSoldiers>0)
						$victimProvince->milObject->killUnits($GLOBALS['MilitaryConst']->SOLDIERS,$killSoldiers );
					if ($killDef>0)
						$victimProvince->milObject->killUnits($GLOBALS['MilitaryConst']->DEF_SOLDIERS,$killDef );
					if ($killOff>0)
						$victimProvince->milObject->killUnits($GLOBALS['MilitaryConst']->OFF_SOLDIERS,$killOff );
					if ($killElite>0)
						$victimProvince->milObject->killUnits($GLOBALS['MilitaryConst']->ELITE_SOLDIERS,$killElite );
					if ($i['food']<0)
						$victimProvince->postNews($victimProvince->getAdvisorName()." about $sum units died of starvation this morning!");
					else
						$victimProvince->postNews($victimProvince->getAdvisorName()." about $sum units left our Province this morning because they had no place to live");
//					$this->writeLog($this->logFile,"\n$c Provinces treated for overpopulation ($d real)\n");						
				}
			}
			$this->writeLog($this->logFile,"\n$c Provinces treated for overpopulation ($d real)\n");
		}
		// they need at least 100 peasants to grow new peasants.
/*		$this->database->query("SELECT peasants from Province where peasants<0")
			or $this->error("Fatal error in server.class.inc.php (handleBadResrouces)4");
		if ($this->database->numRows()>0) {
			$this->writeLog($this->errorLogFile,"NEGATIVE PEASANTS!  fixing peasants to 0.\n");
			$this->database->query("UPDATE Province set peasants = 0 WHERE peasants<0");

		}
			*/		
		
	}
	function growPeasants() {
		if ($this->config['ticks']<50) {
			$this->database->query("UPDATE Province SET peasantChange = peasantChange + (peasants*$this->PEASANT_BIRTH*1.3)") or $this->error("Fatal error in server.class.inc.php (growPeasant)1");
		} else {
			$this->database->query("UPDATE Province SET peasantChange = peasantChange + (peasants*$this->PEASANT_BIRTH)") or $this->error("Fatal error in server.class.inc.php (growPeasant)2");
		}
	}
	
	function growFood() {
		return 0;		
	}
	// give boost to new players
	function growMetal() {
		$this->database->query("UPDATE Province set metalChange = metalChange + (acres*5) WHERE aliveTicks<24 AND aliveTicks>0");
		return 0;		
	}
	// bost in gold to new players.
	function growGold() {
		$this->database->query("UPDATE Province SET incomeChange = incomeChange + (peasants*$this->PEASANT_EARNS)") or $this->error("Fatal error in server.class.inc.php (growGold)1");
		$this->database->query("UPDATE Province SET incomeChange = (incomeChange*1.1) WHERE aliveTicks<24 AND aliveTicks>0") or $this->error("Fatal error in server.class.inc.php (growGold)2");
	}




	function removeResetProvince()
	{
		$this->database->query("SELECT pID FROM Province where status='Deleted'");
		if ($this->database->numRows())	 {
			while ($provlist[] = $this->database->fetchArray());
		}
		if (isset($provlist) && is_array ($provlist) )
		{
			reset($provlist);
			foreach ($provlist as $prov) {
				$this->removeProvince($prov['pID']);
			}
		}

	}




	function removeProvince ($prov) {
		$d = getdate();
		$deleted = $d['year']."-".$d['mon']."-".$d['mday'];
		$this->database->query( "DELETE FROM Message where toID=$prov");
		$this->database->query( "DELETE FROM Message where fromID=$prov"); //???
		$this->database->query( "DELETE FROM Province where pID=$prov");
		$this->database->query( "DELETE FROM Science where pID=$prov");
		$this->database->query( "DELETE FROM Spells where targetID=$prov");
		$this->database->query( "DELETE FROM ProgressBuild where pID=$prov");
		$this->database->query( "DELETE FROM ProgressExplore where pID=$prov");
		$this->database->query( "DELETE FROM ProgressMil where pID=$prov");
		$this->database->query( "DELETE FROM Military where pID=$prov");
		$this->database->query( "DELETE FROM TmpInCommandMilitary where pID=$prov");
		$this->database->query( "DELETE FROM MagicMilitary where pID=$prov");
//		$this->database->query( "DELETE FROM Login where pID=$prov");
		$this->database->query( "DELETE FROM Explore where pID=$prov");
		$this->database->query( "DELETE FROM Buildings where pID=$prov");
		$this->database->query( "DELETE FROM Army where pID=$prov");
		$this->database->query( "DELETE FROM Attack where pID=$prov");
		$this->database->query( "DELETE FROM targetID where pID=$prov");
		$this->database->query( "UPDATE User set pID=-1,history=CONCAT(history,'<BR>Automatically DELETED by SERVER the $deleted') WHERE pID=$prov");		
		return true;
	}

	function fixKingdoms ()
	{
		$kingdomList = array();
		
		if ($this->database->query("select count(pID) as ant, kiID from Province group by kiID")  && $this->database->numRows()) {
			while ($item=$this->database->fetchArray()) {
				$kingdomList[$item['kiID']]['guess']='0';
				$kingdomList[$item['kiID']]['real']=$item['ant'];
				$kingdomList[$item['kiID']]['kiID']=$item['kiID'];
			}
		}

		// get what kingdom table thinks..
		if ($this->database->query("select numProvinces,kiID from Kingdom")  && $this->database->numRows()) {
			while ($item=$this->database->fetchArray()) {
				$kingdomList[$item['kiID']]['guess']=$item['numProvinces'];
				$kingdomList[$item['kiID']]['kiID']=$item['kiID'];
		
				$kingdomList[$item['kiID']]['real']=(isset ($kingdomList[$item['kiID']]['real']) && ($kingdomList[$item['kiID']]['real'])>0) ? 
				$kingdomList[$item['kiID']]['real'] : 0;
			}
		}
		reset($kingdomList);

// traverse and fix missmatch

		foreach ($kingdomList as $kingdom) {
			if ($kingdom['real'] != $kingdom['guess'] && $kingdom['kiID'] > 0) {
				$this->writeLog($this->logFile,"Missmatch in kd #$kingdom[kiID] : REAL => $kingdom[real] : GUESS =>$kingdom[guess] ... fixing\n ");
				$this->database->query("UPDATE Kingdom set numProvinces=$kingdom[real] where kiID=$kingdom[kiID]");
			}
		}
		// password protected kingdoms with 0 users... remove password!
		$this->database->query("UPDATE Kingdom set password='', name='Unexplored Kingdom' where numProvinces=0");

	}
	function sendInactiveWarning ($userID)
	{
		$this->database->query("SELECT * FROM User where userID='$userID'");
		$usr = $this->database->fetchArray();
		if ($usr['status'] != 'Active')
			return;
		$this->database->query("UPDATE User set status='Inactive' where userID='$userID'");
		$subject = "The Kingdoms of Chaos";
		$mailheaders = "From: Chaos Admin <admin@tkoc.net> \n";
	
		$mailheaders .= "Reply-To: admin@tkoc.net\n\n";
        $message =  "Dear $usr[name]\n\n";
        $message .= "You have not logged into your province for ".$this->INACTIVE_DAYS." days\n";
        $message .= "Your account will automatically be deleted in a few days if you do not log in\n\n";
        $message .= "\n";
        $message .= "\tHere is your username in case it slipped your mind:\t\t\t$usr[username]\n";
        $message .= "\n";
        $message .= "\n\n\tTo log in please go to http://www.tkoc.net";
      mail($usr['email'], $subject, $message, $mailheaders);	
	}
	function error ($errorMsg) {
		$this->writeLog($this->errorLogFile,$errorMsg);
		die($errorMsg);
	}
	function writeLog ($filename, $txt) {
		if (is_writable($filename)) {

			if (!$handle = fopen($filename, 'a')) {
				print "Cannot open file ($filename)";
				exit;
			}

			if (!fwrite($handle, $txt)) {
				print "Cannot write to file ($filename)";
				exit;
			}
			fclose($handle);
		} else {
			echo "Error writing to $filname, file not writeable";
		}
	}

} /* Class Server */
}



?>
