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
/*@include ("./data/data.php");
if (empty($server)) {
	@include ("../data/data.php");
	if (empty($server)) {
		@include ("../../data/data.php");
		if (empty($server)) {
			@include ("../../../data/data.php");
		}
	}
}*/

$path = "../data/data.php";
@include ($path);
while (empty($currentPath)) {
	$path = "../".$path;
	@include ($path);
}

/*
$path = "data/data.php";
echo $path;	
while ((@include $path) != "OK") {
	$path = "../".$path;
	echo $path;	
}	*/
	

if( !class_exists( "Server" ) ) {
	require_once ($currentPath."scripts/all.inc.php");
	require_once ($GLOBALS['scriptsPath'] . "Military.class.inc.php");
	require_once ($GLOBALS['scriptsPath'] . "Buildings.class.inc.php");
	require_once ($GLOBALS['scriptsPath'] . "Science.class.inc.php");
	require_once ($GLOBALS['scriptsPath'] . "Magic.class.inc.php");
	require_once ($GLOBALS['scriptsPath'] . "News.class.inc.php");
	require_once ($GLOBALS['scriptsPath'] . "Thievery.class.inc.php");
	require_once ($GLOBALS['scriptsPath'] . "Explore.class.inc.php");
	require_once ($GLOBALS['scriptsPath'] . "Attack.class.inc.php");
	require_once ($GLOBALS['scriptsPath'] . "Race.class.inc.php");
	require_once ($GLOBALS['scriptsPath'] . "Kingdom.class.inc.php");
	require_once ($GLOBALS['scriptsPath'] . "TrigEffect.class.inc.php");
	require_once ($GLOBALS['scriptsPath'] . "gameEffects.class.inc.php");
	require_once ($GLOBALS['scriptsPath'] . "seasons/SeasonFactory.class.inc.php");
	$GLOBALS['script_mode'] = 'server'; // override the web mode.
	
	class Server {
		var $database;
		var $forumdb;
		var $errorLogFile	="";		// set in constructor!
		var $logFile	 	="";		// set in constructor!
		var $config;
		var $PEASANT_BIRTH = "0.025";
		var $PEASANT_EATS  = "0.35";
		var $PEASANT_EARNS = "3.0";
		var $INACTIVE_DAYS = 5;
		var $REMOVE_DAYS   = 10;
		var $BASE_PAY_MILITARY = 0.6;
		var $BADRESOURCE_KILL_UNITS = 0.004;
		var $OVERPOPULATED_KILL_UNITS = 0.006;
		//var $VACATION_MODE_MIN = 72;  // in hours
		//var $VACATION_MODE_MAX = 336; // 14 days.
	
	
		function Server () 
		{
			$this->database = $GLOBALS['database'];
			$this->forumdb = $GLOBALS['forumdb'];
			$this->config = $GLOBALS['config'];
			$this->errorLogFile = $GLOBALS['errorFileLog'];
			$this->logFile = $GLOBALS['gameLog'];
			//$this->VACATION_MODE_MIN = $this->config['vacationMin'];
			//$this->VACATION_MODE_MAX = $this->config['vacationMax'];
		}
		
		
		function doTick () {
			$this->startFixing();
			$this->runStatus();
			
			if ($this->config['status'] == "Running")
				$this->gameProgressTick();
			else
				$this->fixKingdoms();
				
			$this->database->query("UPDATE Config set ticks=(ticks+1), lastTickTime=NOW()")
				or $this->error("Fatal error in server.class.inc.php Update set ticks");
		}
		
	
		function startFixing() {
			$this->removeResetProvince();
			$this->fixBrokenUsers();
			$this->fixBrokenProvinces();
		}
		
		
		//* RUNNING => END => PAUSE => RUNNING => ...
		//* Length:
		//* random 	=> 24*3 => 24*4 => random  => ...
		function runStatus() {
			echo "Game Status: ".$this->config['status']." - Remaining Ticks: ".($this->config['statusLength']-$this->config['ticks'])." - Age: ".$this->config['age']."<br /><br />";
			$this->mywriteLog ($GLOBALS['gameLog'],"\r\nGame Status: ".$this->config['status']." - Remaining Ticks: ".($this->config['statusLength']-$this->config['ticks'])." - Age: ".$this->config['age']);
			
			if ($this->config['status'] == 'Running') {
				// From Running to Ended
				if ($this->config['ticks'] == $this->config['statusLength']) {
					$this->database->query("UPDATE Config set status='Ended', statusLength=4, ticks=1");
					// Set last played age
					//$database->query("UPDATE User, Login SET User.lastPlayedAge='$age' WHERE User.userID=Login.userID");
					$this->getAgeEndScores($database);
				}
				
				// Afto prepei na figei
				if ($this->config['ticks']+$this->config['ApocalypseLength'] == $this->config['statusLength'])
				{
					$this->mywriteLog ($GLOBALS['gameLog'],"\r\n--------------------\r\nAPOCALYPSE HITS THE WORLD (".$this->config["age"].")\r\n--------------------\r\nGAME ENDING IN " .($this->config['statusLength']-$this->config['ticks'])." ticks.");
				}
			}
			else if ($this->config['status']=='Pause') {
				// From Pause to Running
				if ($this->config['ticks']+1 == $this->config['statusLength']) 
				{
					$length = mt_rand (1300, 1600);
					$this->database->query("UPDATE Config set status='Running', statusLength=$length, ticks=1");
					$this->mywriteLog ($GLOBALS['gameLog'],"\r\nGAME AUTOMATICALLY STARTED!!!!.");
					$this->mywriteLog ($GLOBALS['gameLog'],"\r\nTotal age length: ".$length." ticks");
				}
			}
			else if ($this->config['status']=='Ended') 
			{
				// From Ended to Pause
				if ($this->config['ticks']+1 == $this->config['statusLength']) 
				{
					$this->mywriteLog ($GLOBALS['gameLog'], "\r\n\r\nGame is going from Ended TO Pause\r\n");
					$this->database->query("UPDATE Config set status='Pause', statusLength=4, ticks=1, Age=Age+1");	
					
					$this->database->query("SELECT count(pID) as totalUsers from Province");
					$totalUsers = $this->database->fetchArray();
			
					$this->mywriteLog ($GLOBALS['gameLog'],
					'
					Remaining ticks: '. ($this->config['statusLength']-$this->config['ticks']) . '
					   STATISTICS
						   Number of queries   : ' . $GLOBALS['database_queries_count'] . '
						   Number of fetches   : ' . $GLOBALS['database_queries_fetch_count'] . '
						   Provinces           : ' . $totalUsers['totalUsers'] .'
					   END');
					
					//sendMassMail($this->database);
					$this->resetGameData();
					$this->mywriteLog ($GLOBALS['gameLog'],"\r\n--------------------\r\nNEW AGE ONLINE (".$this->config["age"].")\r\n--------------------\r\nGAME AUTOMATICALLY SET TO ALLOW SIGNUPS!!!!.");
				}
			}//* END OF STATUS-End
		}
		
		
		function gameProgressTick () {
			$this->prepareTick();
			
			/*echo "Emails<br />";
			$email = new Email($this->database);
			$email->doTick();*/
		
			echo "Attack<br />";
			//This HAS to go before Military!!!!!!!!!!!!!
			$attack = new Attack($this->database);
			$attack->doTick();
			
			echo "Military<br />";
			$military = new Military($this->database);
			$military->doTick();
			
			echo "Triggered effects<br />";
			$triggerEffects = new TrigEffect($this->database);
			$triggerEffects->doTick();
			
			echo "Exploring<br />";
			$explore = new Explore($this->database, NULL);
			$explore->doTick();
			
			echo "Buildings<br />";
			$buildings = new Buildings( $this->database, NULL);
			$buildings->doTick();

			echo "Science<br />";
			$science = new Science ($this->database,NULL);
			$science->doTick();
			
			echo "Magic<br />";
			$magic = new Magic($this->database,NULL);
			$magic->doTick();
			
			echo "Thievery<br />";
			$th = new Thievery($this->database,false);
			$th->doTick();
			
			echo "Race<br />";
			$race = new Race($this->database,NULL);
			$race->doTick();
			
			echo "News<br />";
			$newsDel = new News($this->database);
			$newsDel->doTick();
			
			echo "Kingdom<br />";
			$kingdom = new Kingdom($this->database);
			$kingdom->doTick();
			
			echo "Game Effects<br />";
			$gameEffects = new gameEffects ($this->database, $this->config);
			$overpopModifier = $gameEffects->doTick();
			$this->OVERPOPULATED_KILL_UNITS *= $overpopModifier;
			
			echo "Server<br />";
			$this->doServerTick();
			
			//echo "RecruitBonus\n";
			//require_once($GLOBALS['path_www_administration'] . "RecruitPlayers.class.inc.php");
			//$recruit = new RecruitPlayers($this->database);
			//$recruit->doTick();
			
			echo "Seasonstuff<br />";
			$GLOBALS['CurrentSeason']->DoTick();
			
			$this->database->shutdown();
			
			echo "End tick\n";
		}
		
		
		/* Reset fields, calculate base values */
		function prepareTick () {
			$this->database->query("UPDATE Province SET foodExpenses = 0") or $this->error("Fatal error in server.class.inc.php (prepareTick)1");
			$this->database->query("UPDATE Province SET metalExpenses = 0")or $this->error("Fatal error in server.class.inc.php (prepareTick)2");
			$this->database->query("UPDATE Province SET goldExpenses = 0") or $this->error("Fatal error in server.class.inc.php (prepareTick)3");
			$this->database->query("UPDATE Province SET foodChange = 0") or $this->error("Fatal error in server.class.inc.php (prepareTick)1");
			$this->database->query("UPDATE Province SET metalChange = 0")or $this->error("Fatal error in server.class.inc.php (prepareTick)2");
			$this->database->query("UPDATE Province SET peasantChange = 0") or $this->error("Fatal error in server.class.inc.php (prepareTick)3");
			$this->database->query("UPDATE Province SET incomeChange = 0") or $this->error("Fatal error in server.class.inc.php (prepareTick)4");
			$this->database->query("UPDATE Province SET aliveTicks = aliveTicks+1 WHERE aliveTicks>-1") or $this->error("Fatal error in server.class.inc.php (prepareTick)5");
			$this->database->query("UPDATE Province SET protection = protection-1 WHERE aliveTicks>-1 AND protection>0 AND vacationmode='false'") or $this->error("Fatal error in server.class.inc.php (prepareTick)5");
			$this->database->query("UPDATE Province SET vacationTicks = vacationTicks+1 WHERE vacationmode='true'") or $this->error("Fatal error in server.class.inc.php (prepareTick)5");
			$this->growPeasants();
			$this->growFood();
			$this->growMetal();
			$this->growGold();
			
			$this->baseExpensesFood();
			$this->baseExpensesMetal();
			$this->baseExpensesGold();
		}
		
		
		function growPeasants() {
			if ($this->config['ticks']<50) {
				$this->database->query("UPDATE Province SET peasantChange = peasantChange + (peasants*$this->PEASANT_BIRTH*1.3) WHERE vacationmode='false'") or $this->error("Fatal error in server.class.inc.php (growPeasant)1");
			} 
			else {
				$this->database->query("UPDATE Province SET peasantChange = peasantChange + (peasants*$this->PEASANT_BIRTH) where vacationmode='false'") or $this->error("Fatal error in server.class.inc.php (growPeasant)2");
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
			$this->database->query("UPDATE Province SET incomeChange = incomeChange + (peasants*$this->PEASANT_EARNS) WHERE vacationmode='false'") or $this->error("Fatal error in server.class.inc.php (growGold)1");
			$this->database->query("UPDATE Province SET incomeChange = (incomeChange*1.1) WHERE aliveTicks<24 AND aliveTicks>0") or $this->error("Fatal error in server.class.inc.php (growGold)2");
		}
		
		
		function baseExpensesFood()
		{
			// peasants + military eat.
			$this->database->query("UPDATE Province set foodExpenses=((peasants+militaryPopulation)*$this->PEASANT_EATS) where vacationmode='false'");	
		}
		
		function baseExpensesMetal()
		{
		}
		
		function baseExpensesGold()
		{
			// military cost...
			$this->database->query("UPDATE Province set goldExpenses=((militaryPopulation)*$this->BASE_PAY_MILITARY) where vacationmode='false'");
		
		}
		
		
		function doServerTick () {
			$this->applyKingBonus();
			$this->updateTotals();
			$this->updateProvinceResources();
			$this->handleBadResources();  
			$this->handleProvincesStatus();
		}
		
		
		function applyKingBonus () {
			// King Bonus Algorithm - Soptep: Around January :P
			$maxKingBonus = 115; //15% later
			$minKingBonus = 102;
			
			$this->database->query("Select * From Config");
			$this->config = $this->database->fetchArray();
			$maxProvinceInKD = $this->config["maxProvinceInKD"];
			
			$bonusStep = (int) (($maxKingBonus - $minKingBonus) / $maxProvinceInKD);
			
			for ($i=1; $i<=$maxProvinceInKD; $i++) {
				if ($i==1)
					$bonus = $minKingBonus / 100;
				else if ($i==$maxProvinceInKD)
					$bonus = $maxKingBonus / 100;
				else 
					$bonus = ($minKingBonus + ($bonusStep*($i-1))) / 100;
				
				// HACK: fix king/queen bonus
				$this->database->query("UPDATE Province RIGHT JOIN Kingdom on Kingdom.king=Province.pID set incomeChange = incomeChange*$bonus Where Kingdom.numProvinces=$i")
					or $this->error("Fatal error in server.class.inc.php (doTick)1");
				$this->database->query("UPDATE Province RIGHT JOIN Kingdom on Kingdom.king=Province.pID set foodChange = foodChange*$bonus Where Kingdom.numProvinces=$i")
					or $this->error("Fatal error in server.class.inc.php (doTick)2");
				$this->database->query("UPDATE Province RIGHT JOIN Kingdom on Kingdom.king=Province.pID set metalChange = metalChange*$bonus Where Kingdom.numProvinces=$i")
					or $this->error("Fatal error in server.class.inc.php (doTick)3");
			}
		}
		
		
		function updateTotals () {
			if ($this->config['ticks']%24 == 0) {
				$this->database->query("UPDATE Province SET metalTotal = 0");
				$this->database->query("UPDATE Province SET foodTotal = 0");
				$this->database->query("UPDATE Province SET incomeTotal = 0");
				$this->database->query("UPDATE Province SET peasantTotal = 0");
			}
		}
		
		
		function updateProvinceResources () {
			// metal
			$this->database->query("UPDATE Province SET metalTotal = metalTotal+metalChange-metalExpenses WHERE vacationmode='false'")
				or $this->error("Fatal error in server.class.inc.php (doTick)4");
			$this->database->query("UPDATE Province SET metal = metal+metalChange-metalExpenses WHERE vacationmode='false'")
				or $this->error("Fatal error in server.class.inc.php (doTick)5");
			
			// food
			$this->database->query("UPDATE Province SET foodTotal = foodTotal+foodChange-foodExpenses WHERE vacationmode='false'")
				or $this->error("Fatal error in server.class.inc.php (doTick)6");
			$this->database->query("UPDATE Province SET food = food + foodChange - foodExpenses WHERE vacationmode='false'")
				or $this->error("Fatal error in server.class.inc.php (doTick)7");
	
			// gold
			$this->database->query("UPDATE Province SET incomeTotal = incomeTotal+incomeChange-goldExpenses WHERE vacationmode='false'")
				or $this->error("Fatal error in server.class.inc.php (doTick)8");
			$this->database->query("UPDATE Province SET gold = incomeChange + gold - goldExpenses WHERE vacationmode='false'")
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
		}
		
		
		function handleBadResources() 
		{
			// starving. dont grow peasants
			$this->database->query("UPDATE Province set peasants = peasants-ABS(peasantChange*2) WHERE food<0")
				or $this->error("Fatal error in server.class.inc.php (handleBadResrouces)1");
			// kill them!
			$this->database->query("UPDATE Province set peasants = GREATEST(peasants,0)")
				or $this->error("Fatal error in server.class.inc.php (handleBadResrouces)2" . $this->database->error());
				
			// reduce income also. (50%)
			$this->database->query("UPDATE Province set gold= GREATEST((gold-(ABS(incomeChange)*0.5)),0) WHERE food<0")
				or $this->error("Fatal error in server.class.inc.php (handleBadResrouces)3");
			// overpopulation
			// overpopulated if (max pop - total pop) < -acres
			// loose acres/10 units of each kind if you are overpopulated
			$this->database->query("SELECT pID,acres,buildingPeasantPopulation,militaryPopulation, food, gold, metal 
										FROM Province 
											WHERE ((buildingPeasantPopulation-militaryPopulation-peasants)<(-acres))
											OR (
												((buildingPeasantPopulation-militaryPopulation-peasants)<(buildingPeasantPopulation*0.05))
												AND ((buildingPeasantPopulation*0.9)<militaryPopulation)
												)
											OR (food<0)
											OR (metal<0)
											OR (gold<0)");
			if ($this->database->numRows() > 0)
			{
				while ($a =$this->database->fetchArray())
				{
					if ((($a['buildingPeasantPopulation']*0.9) < $a['militaryPopulation']) || ($a['food']<0 || $a['metal']<0 ||$a['gold']<0 ))
						$overPopulatedProvinces[] = $a;
				}
				if (!isset($overPopulatedProvinces))
					return;
				reset($overPopulatedProvinces);
				$c = 0;
				$d = 0;
				foreach ($overPopulatedProvinces as $i)
				{
					$c++;
					$victimProvince = new Province($i['pID'],$this->database);
					$victimProvince->getProvinceData();
					if ($victimProvince->isProtected())
					{
						continue;
					}
					$victimProvince->getMilitaryData();
					$GLOBALS['province'] = $victimProvince;
					// WARNING cutn paste from assasinate military
					$enemyThieves  = $victimProvince->milObject->getMilUnit($victimProvince->milObject->MilitaryConst->THIEVES);
					$enemyWizards  = $victimProvince->milObject->getMilUnit($victimProvince->milObject->MilitaryConst->WIZARDS);
					$enemySoldiers = $victimProvince->milObject->getMilUnit($victimProvince->milObject->MilitaryConst->SOLDIERS);
					$enemyDef = $victimProvince->milObject->getMilUnit($victimProvince->milObject->MilitaryConst->DEF_SOLDIERS);
					$enemyOff = $victimProvince->milObject->getMilUnit($victimProvince->milObject->MilitaryConst->OFF_SOLDIERS);
					$enemyElite = $victimProvince->milObject->getMilUnit($victimProvince->milObject->MilitaryConst->ELITE_SOLDIERS);
					if ($i['food']<0 || $i['gold']<0 || $i['metal']<0)
						$kill = $this->BADRESOURCE_KILL_UNITS;
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
							$victimProvince->milObject->killUnits($GLOBALS['MilitaryConst']->THIEVES,$killThieves, false );
						if ($killWizards>0)
							$victimProvince->milObject->killUnits($GLOBALS['MilitaryConst']->WIZARDS,$killWizards, false );
						if ($killSoldiers>0)
							$victimProvince->milObject->killUnits($GLOBALS['MilitaryConst']->SOLDIERS,$killSoldiers, false );
						if ($killDef>0)
							$victimProvince->milObject->killUnits($GLOBALS['MilitaryConst']->DEF_SOLDIERS,$killDef, false );
						if ($killOff>0)
							$victimProvince->milObject->killUnits($GLOBALS['MilitaryConst']->OFF_SOLDIERS,$killOff, false );
						if ($killElite>0)
							$victimProvince->milObject->killUnits($GLOBALS['MilitaryConst']->ELITE_SOLDIERS,$killElite, false );
							
						if ($kill == $this->BADRESOURCE_KILL_UNITS)
						{
							if ($i['food']<0)
								$victimProvince->postNews($victimProvince->getAdvisorName()." about $sum units died of starvation this morning!");
							else
								$victimProvince->postNews($victimProvince->getAdvisorName()." about $sum left us this morning because we could not pay there wages!");
						}
						else
							$victimProvince->postNews($victimProvince->getAdvisorName()." about $sum units left our Province this morning because they had no place to live");
	//					$this->mywriteLog($this->logFile,"\n$c Provinces treated for overpopulation ($d real)\n");						
					}
				}
				$this->mywriteLog($this->logFile,"\r\n$c Provinces treated for overpopulation ($d real)\n");
			}
				
			// reset resources to 0.
			$this->database->query("UPDATE Province set food=0 WHERE food<0")
				or $this->error("Fatal error in server.class.inc.php (handleBadResrouces)4");
			$this->database->query("UPDATE Province set metal=0 WHERE metal<0")
				or $this->error("Fatal error in server.class.inc.php (handleBadResrouces)4");
			$this->database->query("UPDATE Province set gold=0 WHERE gold<0")
				or $this->error("Fatal error in server.class.inc.php (handleBadResrouces)4");
			
		}
		
		
		function handleProvincesStatus() {
			// This does not belong exactly in this function but anyway it is more closer here
			$this->sendEmail("Protected");
		
			// Set provinces with zero acres as killed
			$this->database->query("UPDATE Province set status='Killed' WHERE acres=0 AND vacationmode='false'");
			$this->sendEmail ("Killed");
			
			$now = time();
			// Set provinces inactive
			$inactiveDays = $now - ($this->INACTIVE_DAYS*24*60*60);
			$this->database->query("UPDATE Province set status='Inactive' WHERE lastAccess<$inactiveDays AND vacationmode='false'");
			$this->sendEmail ("Inactive");
			$protectionDays = 24*($this->REMOVE_DAYS - $this->INACTIVE_DAYS)*60; // A value big enough to protect the user even with 1 minute ticks
			$this->database->query("UPDATE Province set protection=$protectionDays WHERE status='Inactive'");
			
			// Set provinces deleted
			$removeDays = $now - ($this->REMOVE_DAYS*24*60*60);
			$this->database->query("UPDATE Province set status='Deleted' WHERE lastAccess<$removeDays AND status='Inactive'");
			$this->sendEmail ("Deleted");
			
			// Remove provinces
			$this->removeResetProvince();
			
			// Clear kd
			$this->fixKingdoms();
		}
	
	
		function removeResetProvince()
		{
			$this->database->query("SELECT pID, kiID FROM Province where status='Deleted' OR status='Killed'");
			if ($this->database->numRows())	 {
				while ($provlist[] = $this->database->fetchArray());
			}
			if (isset($provlist) && is_array ($provlist) )
			{
				reset($provlist);
				foreach ($provlist as $prov) {
					$this->removeProvince($prov['pID'], $prov['kiID']);
				}
			}
	
		}
	
		function removeProvince ($prov, $kiID) {
			$d = getdate();
			$deleted = $d['year']."-".$d['mon']."-".$d['mday'];
			$this->database->query( "DELETE FROM Message where toID=$prov");
			$this->database->query( "DELETE FROM Message where fromID=$prov"); //???
			$this->database->query( "DELETE FROM Science where pID=$prov");
			$this->database->query( "DELETE FROM Spells where targetID=$prov");
			$this->database->query( "DELETE FROM ProgressBuild where pID=$prov");
			$this->database->query( "DELETE FROM ProgressExplore where pID=$prov");
			$this->database->query( "DELETE FROM ProgressMil where pID=$prov");
			$this->database->query( "DELETE FROM Military where pID=$prov");
			$this->database->query( "DELETE FROM TmpInCommandMilitary where pID=$prov");
			$this->database->query( "DELETE FROM MagicMilitary where pID=$prov");
			$this->database->query( "DELETE FROM Explore where pID=$prov");
			$this->database->query( "DELETE FROM Buildings where pID=$prov");
			$this->database->query( "DELETE FROM Army where pID=$prov");
			$this->database->query( "DELETE FROM Attack where pID=$prov");
			$this->database->query( "DELETE FROM targetID where pID=$prov");
			$this->database->query( "DELETE FROM Province where pID=$prov");
			
			//$this->database->query( "UPDATE Kingdom set numProvinces=numProvinces-1 where kiID=$kiID");
			$this->database->query( "UPDATE User set pID=-1,history=CONCAT(history,'<BR>Automatically DELETED by SERVER the $deleted') WHERE pID=$prov");		
			return true;
		}
	
	
		function fixBrokenUsers()
		{
			$this->database->query("select UserID,User.pID as province from User LEFT join Province on User.pID=Province.PID where User.pID> 0 and Province.pID is null");
			if ($this->database->numRows() > 0)
			{
				while ($provincelist[] = $this->database->fetchArray())
				{
					echo "grab";
				}
				reset($provincelist);
				foreach ($provincelist as $a)
				{
					$this->database->query( "UPDATE User set pID=-1,history=CONCAT(history,'<BR>Fixed errornous province relation') WHERE pID='".$a['province']."'");						
				}
			}
		}
	
		function fixBrokenProvinces()
		{
			$this->database->query("select UserID,Province.pID as province from Province LEFT JOIN User on Province.pID=User.pID where Province.pID> 0 and User.pID is null");
			if ($this->database->numRows() > 0)
			{
				while ($provincelist[] = $this->database->fetchArray())
				{
					echo "grab";
				}
				reset($provincelist);
				foreach ($provincelist as $a)
				{
					$this->removeProvince($a['province']);
				}
			$this->fixKingdoms();
			}
	
		}
	
		function fixKingdoms ()
		{
			$kingdomList = array();
			
			if ($this->database->query("select count(pID) as ant,kiID from Province group by kiID")  && $this->database->numRows()) {
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
	//				$this->mywriteLog($this->logFile,"Missmatch in kd #$kingdom[kiID] : REAL => $kingdom[real] : GUESS =>$kingdom[guess] ... fixing\n ");
					$this->database->query("UPDATE Kingdom set numProvinces=$kingdom[real] where kiID=$kingdom[kiID]");
				}
			}
			// password protected kingdoms with 0 users... remove password!
			//$this->database->query("UPDATE Kingdom set password='', name='Unexplored Kingdom' where numProvinces=0");
			$this->database->query( "DELETE FROM Kingdom where numProvinces=0");
		}
		
		
		function error ($errorMsg) {
			$this->mywriteLog($this->errorLogFile,$errorMsg);
			die($errorMsg);
		}
		
		
		function sendEmail ($type) {
			switch ($type) {
				case "Protected":
					$now = time();
					$inactiveTicks = $now - ($this->config["totalTickTime"]*30); // Hasn't logged in for 30 ticks
					$this->database->query("SELECT username FROM Province, User WHERE protection=24 AND lastAccess<$inactiveTicks AND Province.pID=User.pID");
					$subject ="Protected";
					$message ="Protected";
					break;
				case "Killed":
					$this->database->query("SELECT username FROM Province, User WHERE status='Killed' AND Province.pID=User.pID");
					$subject ="Killed";
					$message ="Killed";
					break;
				case "Inactive":
					$this->database->query("SELECT username FROM Province, User WHERE status='Inactive' AND protection=0 AND Province.pID=User.pID");
					$subject ="Inactive";
					$message = "You have not logged into your province for ".$this->INACTIVE_DAYS." days\n";
					$message .= "Your account will automatically be deleted in a few days if you do not log in\n\n";
					$message .= "\n";
					$message .= "\n\n\tTo log in please go to http://www.tkoc.net";
					break;
				case "Deleted":
					$this->database->query("SELECT username FROM Province, User WHERE status='Deleted' AND Province.pID=User.pID");
					$subject ="Deleted";
					$message ="Deleted";
					break;
			}
			
			$from = 'From: The Kingdoms of Chaos <admin@tkoc.net>';
			//$mailheaders = "From: Chaos Admin <admin@tkoc.net> \n";
			//$mailheaders .= "Reply-To: admin@tkoc.net\n\n";
			
			while ($data=$this->database->fetchArray()) {
				$result = mysql_fetch_array ($this->forumdb->selectField ("email_address", "smf_members", "member_name", $data['username']));
				$message =  "Dear ".$data['username']."\n\n".$message;
				echo $message;
				echo $result['email_address'];
				@mail($result['email_address'], $subject, $message, $from);
			}
		}
		
		
		function resetGameData () {
			$this->mywriteLog ($GLOBALS['gameLog'],"Resetting game data\r\n");
			$this->database->query("DELETE FROM Army") or die($this->database->error());
			$this->database->query("ALTER TABLE Army AUTO_INCREMENT = 1") or die($this->database->error());
			$this->database->query("DELETE FROM Attack") or die($this->database->error());
			$this->database->query("ALTER TABLE Attack AUTO_INCREMENT = 1") or die($this->database->error());
			$this->database->query("DELETE FROM Buildings") or die($this->database->error());
			$this->database->query("ALTER TABLE Buildings AUTO_INCREMENT = 1") or die($this->database->error());
			$this->database->query("DELETE FROM Explore") or die($this->database->error());
			$this->database->query("ALTER TABLE Explore AUTO_INCREMENT = 1") or die($this->database->error());
			$this->database->query("DELETE FROM Kingdom") or die($this->database->error());
			$this->database->query("ALTER TABLE Kingdom AUTO_INCREMENT = 1") or die($this->database->error());
			$this->database->query("DELETE FROM MagicMilitary") or die($this->database->error());
			$this->database->query("ALTER TABLE MagicMilitary AUTO_INCREMENT = 1") or die($this->database->error());
			$this->database->query("DELETE FROM Message") or die($this->database->error());
			$this->database->query("ALTER TABLE Message AUTO_INCREMENT = 1") or die($this->database->error());
			$this->database->query("DELETE FROM Military") or die($this->database->error());
			$this->database->query("ALTER TABLE Military AUTO_INCREMENT = 1") or die($this->database->error());
			$this->database->query("DELETE FROM News") or die($this->database->error());
			$this->database->query("ALTER TABLE News AUTO_INCREMENT = 1") or die($this->database->error());
			$this->database->query("DELETE FROM NewsProvince") or die($this->database->error());
			$this->database->query("ALTER TABLE NewsProvince AUTO_INCREMENT = 1") or die($this->database->error());
			$this->database->query("DELETE FROM ProgressBuild") or die($this->database->error());
			$this->database->query("ALTER TABLE ProgressBuild AUTO_INCREMENT = 1") or die($this->database->error());
			$this->database->query("DELETE FROM ProgressExpl") or die($this->database->error());
			$this->database->query("ALTER TABLE ProgressExpl AUTO_INCREMENT = 1") or die($this->database->error());
			$this->database->query("DELETE FROM ProgressMil") or die($this->database->error());
			$this->database->query("ALTER TABLE ProgressMil AUTO_INCREMENT = 1") or die($this->database->error());
			$this->database->query("DELETE FROM Province") or die($this->database->error());
			$this->database->query("ALTER TABLE Province AUTO_INCREMENT = 1") or die($this->database->error());
			$this->database->query("DELETE FROM Science") or die($this->database->error());
			$this->database->query("ALTER TABLE Science AUTO_INCREMENT = 1") or die($this->database->error());
			$this->database->query("DELETE FROM Spells") or die($this->database->error());
			$this->database->query("ALTER TABLE Spells AUTO_INCREMENT = 1") or die($this->database->error());
			$this->database->query("DELETE FROM TmpInCommandMilitary") or die($this->database->error());
			$this->database->query("ALTER TABLE TmpInCommandMilitary AUTO_INCREMENT = 1") or die($this->database->error());
			$this->database->query("DELETE FROM AdminLogin") or die($this->database->error());
			$this->database->query("ALTER TABLE AdminLogin AUTO_INCREMENT = 1") or die($this->database->error());
			$this->database->query("DELETE FROM TrigEffect") or die($this->database->error());
			$this->database->query("ALTER TABLE TrigEffect AUTO_INCREMENT = 1") or die($this->database->error());
			$this->database->query("DELETE FROM DeadMilitary") or die($this->database->error());
			$this->database->query("ALTER TABLE DeadMilitary AUTO_INCREMENT = 1") or die($this->database->error());
			$this->database->query("DELETE FROM ActionLog") or die($this->database->error());
			$this->database->query("ALTER TABLE ActionLog AUTO_INCREMENT = 1") or die($this->database->error());
			
			$this->database->query("DELETE FROM Forum") or die($this->database->error());
			$this->database->query("ALTER TABLE Forum AUTO_INCREMENT = 1") or die($this->database->error());
			$this->database->query("DELETE FROM ForumMain WHERE ForumID > 13") or die($this->database->error());
			$this->database->query("ALTER TABLE ForumMain AUTO_INCREMENT = 14") or die($this->database->error());
			$this->database->query("DELETE FROM ForumPost WHERE PostForumID > 13") or die($this->database->error());
			// We don't know where it is, temporarily don't initiate it as forum should be replaced at the future. Soptep: 11/02/2010
			//$this->database->query("ALTER TABLE forumpost AUTO_INCREMENT = 1") or die($this->database->error());
			$this->database->query("DELETE FROM ForumThread WHERE ThreadForumID > 13") or die($this->database->error());
			//$this->database->query("ALTER TABLE forumthread AUTO_INCREMENT = 1") or die($this->database->error());
		
			$this->database->query("UPDATE User set pID=-1") or die($this->database->error());
			
			$this->mywriteLog ($GLOBALS['gameLog'],"Game Data has been reset\r\n");
		}
		
		
		function getAgeEndScores ($database)
		{
			require_once($GLOBALS['scriptsPath']."ResultsBuilder.class.inc.php" );
			$rb = new ResultsBuilder( $database );
			$rb->saveResults();                         
		}
		
		
		function mywriteLog ($filename, $txt) {
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
				echo "Error writing to $filename, file not writeable";
			}
		}
	
	} /* Class Server */
}
?>