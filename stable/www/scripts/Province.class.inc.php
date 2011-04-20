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
//* file: Province.class.inc.php
//*
//* Province managment class.	
//* 
//* Author: Anders Elton
//*
//* History:
//*     01.01.2005: Anders Elton. Added new function: sizeModifier($targetProvince)
//*		12.08.2004: Anders Elton.  Fixed userResource, its no longer possible to update to negative,
//*									and it *will* return false if such an attempt is made.
//*									(why didnt we do this ages ago, it was an easy fix:)
//*		01.08.2004: Anders Elton.  added debug output / class statistics.
//*		01.08.2004: Anders Elton.  Rewrote to match new coding style
//*     31.05.2004: J�rgen: lag til attackNum for � pr�ve � lage en gangbang protection
//*						   ogs� lag til en funksjon for � oppdatere denne :P - incAttackNum();
//*	    13.09.03 - Anders: oppdatert for neste age.. ? :D
//* 	11.08.03 	- �ystein: 	- la til getrID som skal skaffe id'en til rasen
//*	  						- la til raceObj som et Race objekt for spilleren. 
//*     28.04.03 - �ystein:la til useMana
//*     24.04.03 - Anders: la til gainResource..
//*     18.04.03 - Anders: fixing select statement.
//*     09.04.03 - Anders: added variable kingdom name.
//*     08.04.03 - Anders:  Changed:  province dont take $user as parameter.  only pID!!!!!
//*     07.04.03 - added getkiId()
//*     14.03.03 - adding military object into the class.
//*	
//**********************************************************************

if( !class_exists( "Province" ) ) {

require_once ("globals.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "all.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Database.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "User.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Race.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "ProvinceConstants.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Effect.class.inc.php" );

$GLOBALS['province_objects_created'] = 0;
$GLOBALS['province_function_count_getdata'] = 0;

class Province {

	// private data...
	var $database	= false;  //ref
	var $pID 		= 0;
	var $kiId		= 0;
	
	// const

	// gamestuff
	var $kingdomName 	= "";
	var $provinceName	= "";
	var $rulerName 		= "";
	var $gender			= "";
	var $race			= "";
	var $spID			=   0;
	var $acres			=   0;
	var $peasants		=   0;
	var $mana 			= 100;
	var $influence 		= 100;
	var $king           = "";
    var $created		= "";
    var $PROT_TIME 		= 172800;	// seconds OLD, not in use?

	var $attackNum      = 0;
	var $attackWins     = 0;
	var $attackMade     = 0;
	var $attackSuffered = 0;
	var $attacksSufferedLost = 0;
	
	var $protectionTime = 50; 		// ticks - default 50, (needs to be changed in db to alter protection time)
	var $aliveTicks 	= -1;
	var $status			= 'Deleted';	
	var $morale			= 100;
	var $militaryPopulation     	=0;
	var $buildingPeasantPopulation  =0;
	var $population 	= 0;
	var $incomeChange   = "0";
	var $metalChange 	= "0";
	var $foodChange 	= "0";
	var $peasantChange  = "0";
	var $incomeTotal 	= "0";
	var $metalTotal 	= "0";
	var $foodTotal 		= "0";
	var $peasantTotal 	= "0";
	var $council		= 0;
	var $reputation 	= 0;
	var $voteFor		= 0;
	var $vacationmode       = false;
	var $vacationTicks	=0;
	var $foodExpenses	= 0;
	var $metalExpenses 	=0;
	var $goldExpenses	=0;
	// resources.
	var	$gold=0;
	var $food=0; 
	var $metal=0;
	var $networth=0;

	var $councilObj = NULL;
	var $newsObject = NULL;
	var $raceObj = NULL;
	// military data..
    
	var $military=NULL;
	var $milObject=NULL;
	var $sciObj=NULL;
	var $effectObj = NULL;
	// public
	var $callbackMessage = "todo: change callback message!";
	function Province ($pID,&$databaseReference)
	{
	   $this->database = &$databaseReference;
	   $this->pID = $pID;
	   $GLOBALS['province_objects_created'] ++;
	}
	
	/////////////////////////////////////////////////////	
	// void getProvinceData ()
	//
	// Gets global data for a province.
	// ALL data in table Province.
	/////////////////////////////////////////////////////	

	function getProvinceData ()
	{
		$GLOBALS['province_function_count_getdata']++;
	    $this->database->query("select Province.vacationmode as vacationmode, Province.vacationTicks as vacationTicks, Province.provinceName as provinceName, Province.rulerName as rulerName, Province.reputation as reputation, Province.magicRep as magicRep,
Province.spID as SpeciesID, Province.gender as gender, Province.acres as acres, Province.peasants as peasants, 
Province.gold as gold, Province.food as food, Province.metal as metal, Province.kiID as kiID, Province.militaryPopulation as militaryPopulation,
Province.buildingPeasantPopulation as buildingPeasantPopulation, Province.attacksMade as attacksMade, Province.attacksSuffered as attacksSuffered,
Province.attackWins as attackWins, Province.attackNum as attackNum, Province.attacksSufferedLost as attacksSufferedLost ,Province.aliveTicks as aliveTicks, Province.incomeChange as incomeChange, Province.incomeTotal as incomeTotal,
Province.metalChange as metalChange, Province.metalTotal as metalTotal, Province.foodChange as foodChange, Province.foodTotal as foodTotal,
Province.peasantChange as peasantChange, Province.peasantTotal as peasantTotal, Province.council as council, Province.protection, Province.militaryRep as militaryRep,
Kingdom.king as king, Kingdom.name as kingdomName, Province.mana as mana, Province.influence as influence, Province.voteFor as voteFor,
Province.goldExpenses as goldExpenses, Province.metalExpenses as metalExpenses, Province.foodExpenses as foodExpenses,
Province.networth as networth, Province.status as status , UNIX_TIMESTAMP(Province.created) 
as created,Province.morale as morale from Province LEFT JOIN Kingdom on Kingdom.kiID=Province.kiID where pID='$this->pID'");
//echo $this->database->error() ."pid: $this->pID";
	   if ( ($data = $this->database->fetchArray()) ) {
			$this->setProvinceData($data);
	   } else {
	      // no province!!! shoudl  not happen though...
	   }
	}

	function setProvinceData($data) {
//		$GLOBALS['province'] = $this;
		$this->effectObj = new Effect( $this->database );
		$this->provinceName =  isset($data['provinceName']) ? htmlspecialchars($data['provinceName']):"ProvinceName";
		$this->rulerName    =  isset($data['rulerName'])    ? $data['rulerName']   :"rulerName";
		$this->gender       =  isset($data['gender'])       ? $data['gender']      :"gender";
		$this->acres        =  isset($data['acres'])        ? $data['acres']       :"0";
		$this->peasants     =  isset($data['peasants'])     ? $data['peasants']    :"0";
		$this->buildingPeasantPopulation = isset($data['buildingPeasantPopulation']) ? $data['buildingPeasantPopulation']:"0";
		$this->militaryPopulation = isset($data['militaryPopulation']) ? $data['militaryPopulation']: "";
		$this->attackNum =     isset($data['attackNum'])    ? $data['attackNum']   :"0";
		$this->attackWins =    isset($data['attackWins'])   ? $data['attackWins']  :"0";
		$this->attackMade =    isset($data['attacksMade'])  ? $data['attacksMade'] :"0";
		$this->attackSuffered= isset($data['attacksSuffered'])? $data['attacksSuffered'] :"0";
		$this->attacksSufferedLost= isset($data['attacksSufferedLost'])? $data['attacksSufferedLost'] :"0";
		$this->gold =          isset($data['gold'])         ? $data['gold']:"0";
		$this->food =          isset($data['food'])         ? $data['food']:"0";
		$this->metal =         isset($data['metal'])        ? $data['metal']:"0";
		$this->kiId =          isset($data['kiID'])         ? $data['kiID']:"0";
		$this->kingdomName =   isset($data['kingdomName'])  ? $data['kingdomName']:"KingdomName";
		$this->king =          isset($data['king'])         ? $data['king'] :"0";
		$this->mana=           isset($data['mana'])         ? $data['mana'] :"0";
		$this->influence=      isset($data['influence'])    ? $data['influence']:"0";
		$this->networth=       isset($data['networth'])     ? $data['networth']:"0";
		$this->created =       isset($data['created'])      ? $data['created']:"0";
		$this->status=         isset($data['status'])       ? $data['status'] :"dead";
		$this->morale=         isset($data['morale'])       ? $data['morale'] : 0;
		$this->morale*= $this->effectObj->getEffect($GLOBALS['effectConstants']->ADD_MORALE, $this->pID); // NB!! NOT MULTIPLIED!
		$this->aliveTicks =    isset($data['aliveTicks'])   ? $data['aliveTicks']  : "0";

		$this->goldExpenses =    isset($data['goldExpenses'])   ? $data['goldExpenses']  : "0";
		$this->metalExpenses =    isset($data['metalExpenses'])   ? $data['metalExpenses']  : "0";
		$this->foodExpenses =    isset($data['foodExpenses'])   ? $data['foodExpenses']  : "0";

		$this->incomeChange =  isset($data['incomeChange']) ? $data['incomeChange']: "0";
		$this->metalChange =   isset($data['metalChange'])  ? $data['metalChange'] : "0";
		$this->foodChange =    isset($data['foodChange'])   ? $data['foodChange']:-1;
		$this->peasantChange = isset($data['peasantChange'])? $data['peasantChange']:-1;
		$this->incomeTotal =   isset($data['incomeTotal'])  ? $data['incomeTotal']: -1;
		$this->metalTotal =    isset($data['metalTotal'])   ? $data['metalTotal']:-1;
		$this->foodTotal =     isset($data['foodTotal'])    ? $data['foodTotal']:-1;
		$this->peasantTotal =  isset($data['peasantTotal']) ? $data['peasantTotal']:-1;
		$this->voteFor =       isset($data['voteFor'])      ? $data['voteFor']: 0;
		$this->council =       isset($data['council'])      ? $data['council']:-1;
		$this->councilObj = new Council($this->database,$this );
		$this->spID =          isset($data['SpeciesID'])    ? $data['SpeciesID']:-1;
		$this->reputation =    isset($data['reputation']) ? $data['reputation']:-1;
		$this->magicReputation = isset($data['magicRep']) ? $data['magicRep']:0;
		$this->militaryRep =	isset($data['militaryRep']) ? $data['militaryRep']:0;
		
		if (!isset($data['vacationmode']))
		{
			die("Update this script.  report in forum.\n");
		}
		if ($data['vacationmode'] == 'true')
		{
			$this->vacationmode = true;
		}
		else
		{
			$this->vacationmode = false;
		}
		$this->vacationTicks = isset($data['vacationTicks']) ? $data['vacationTicks'] : 0;
		$this->population = $this->peasants+$this->militaryPopulation;
		$tmp = $this->database->result;
		$this->raceObj = new Race ($this->database,$this);
		$this->database->result = $tmp;
		$this->race = $this->raceObj->race->name;
		$this->protectionTime = isset($data['protection']) ? $data['protection']: 666;
		if ($this->aliveTicks == '-1') {
		  	$this->setAlive();
		}
		if (($this->kiId<0) && $this->status=='Alive') {
			if ($GLOBALS['script_mode'] != 'server')
			{
				$this->kiId *=-1;
				$this->database->query("UPDATE Province set kiID=ABS(kiID) WHERE pID='$this->pID'");
			}
		}
	}


 	function setAlive() {
		$this->database->query("UPDATE Province set aliveTicks=0 where pID='$this->pID'");
	}
	function isOverPopulated()
	{
		if ( (($this->peasants + $this->militaryPopulation) > ($this->buildingPeasantPopulation*1.1)) || ($this->peasants < 1))
			return true;
		else
			return false;
	}
	
	
	// callback.  Return true if action should continue.  false means cancel
	function OnMagicAction()
	{
		if ($this->isOverPopulated())
			return false;
		return true;
	}
	
	// called just before a magic action on us happens.
	// if false is returned here the spell will fail
	// TODO: add callback hoook
	function OnMagiced()
	{
		return true;
	}
	
	function OnAttackAction()
	{
		if ($this->isOverPopulated())
			return false;
		return true;
	}
	
	// this callback is called just before an attack.
	// if false is returned here, the attack on us will fail!
	// TODO: add callback hoook
	function OnAttacked()
	{		
		return true;
	}
	
	
	// callback.  Return true if action should continue.  false means cancel
	function OnThieveryAction($result, $province)
	{
		if ($this->isOverPopulated())
		{
			$this->callbackMessage = "<br><br><b>".$this->getAdvisorName().", our peasants are rioting due to overpopulation.
										Our thieves refuse to operate while our province is in this shape!";;
			return false;
		}
		
		if ($this->councilObj->hasCouncil())
		{
			$advisor = $this->councilObj->GetAdvisor();
			$newres = $advisor->OnThieveryAction($result,$province);
			if ($newres != $result)
			{
				$this->callbackMessage = $advisor->callbackMessage;
			}
		}
		
		return $result;
	}

	// this callback is called just before someone is thieving from you
	// if false is returned here the thievery action on us will fail
	function OnThieved($result, $province)
	{
		if ($this->councilObj->hasCouncil())
		{
			$advisor = $this->councilObj->GetAdvisor();
			$newres = $advisor->OnThieved($result,$province);
			if ($newres != $result)
			{
				$this->callbackMessage = $advisor->callbackMessage;
			}
		}
		return $result;
	}
	
	/////////////////////////////////////////////////////	
	// float setNetworth()
	//
	// Updates the networth.  Calculates networth for 
	// science, buildigns, miltary.  It creates a lot of objects, with lots of
	// db calls..
	// It writes the networth to the database.
	//
	// returns:
	//	- the networth
	/////////////////////////////////////////////////////	

	function setNetworth($debug = false) {
   	    require_once("Military.class.inc.php");
        $this->getMilitaryData();
		require_once("Buildings.class.inc.php");
		$buildings = new Buildings( $this->database, $this );
		require_once("Science.class.inc.php");
		$science = new Science($this->database,$this->pID); 
		$scienceNw = $science->getScienceNetworth();
		if ($debug) echo "ScienceNW: " . $scienceNw . "<br>";
		$buildingNw = $buildings->getBuildingNetworth();
		if ($debug) echo "buildingNW: " . $buildingNw . "<br>";
		$militaryNw=$this->milObject->getArmyNetworth();
		if ($debug) echo "militaryNW: " . $militaryNw . "<br>";
		if ($debug) echo "Gold: ". $this->gold*NW_GOLD . "<br>";
		if ($debug) echo "food: ". $this->food*NW_FOOD . "<br>";
		if ($debug) echo "metal: ". $this->metal*NW_METAL . "<br>";
		if ($debug) echo "peasants: ". $this->peasants*NW_PEASANTS . "<br>";
		if ($debug) echo "acres: ". $this->acres*NW_ACRE . "<br>";

		$this->networth= 	($this->gold*NW_GOLD)+
							($this->food*NW_FOOD)+
							($this->metal*NW_METAL)+
							($this->peasants*NW_PEASANTS)+
							($this->acres*NW_ACRE)+
							($buildingNw) +
							($militaryNw) +
							($scienceNw);
		if ($debug) echo "sum: ". $this->networth . "<br>";
		$this->database->query("UPDATE Province set networth='$this->networth' where pID='$this->pID'");
		return $this->networth;
	}
	
	function getTpa ($function=false)
	{
		$this->getMilitaryData();
		$myThieves = $this->milObject->getMilUnit($this->milObject->MilitaryConst->THIEVES);
		$myTpa = $myThieves['num'] / $this->acres;
		if ($function)
			$myTpa *= $this->effectObj->getEffect($function,$this->pID);
		
		return $myTpa;	
		
	}
	
	function getThieveryRank()
	{
		reset ($GLOBALS['constants']->THIEVERY_RANKS);
		$last = 0;
		$ret = "Decent";
		while (list($rank, $limit) = each($GLOBALS['constants']->THIEVERY_RANKS)) 
		{
			if (($this->reputation < $limit) && ($this->reputation>=$last))
				$ret = $rank;
			$last = $limit;
		}
		return $ret;
	}
	function getMagicRank()
	{
		reset ($GLOBALS['constants']->MAGIC_RANKS);
		$last = 0;
		$ret = "Decent";
		while (list($rank, $limit) = each($GLOBALS['constants']->MAGIC_RANKS)) 
		{
			if (($this->magicReputation < $limit) && ($this->magicReputation>=$last))
				$ret = $rank;
			$last = $limit;
		}
		return $ret;
	}

	function getMilitaryExperience() {
		return $this->militaryRep;
	}

	function updateMilitaryExperience($experience) {
		$sql = "update Province set militaryRep=militaryRep+$experience where pID={$this->pID}";
		$this->database->query($sql);
	}
	
	/////////////////////////////////////////////////////	
	// void usePeasants ()
	//
	// Uses peasants for a province.
	/////////////////////////////////////////////////////	
	function usePeasants($toUse) {
                $this->database->query("UPDATE Province set peasants=GREATEST((peasants-$toUse),0) where pID='$this->pID'");
        }
	
	/////////////////////////////////////////////////////	
	// void useMana ()
	//
	// Uses mana for a province.
	/////////////////////////////////////////////////////	

	function useMana($toUse) {
                $this->database->query("UPDATE Province set mana=GREATEST((mana-$toUse),0) where pID='$this->pID'");
        }
	/////////////////////////////////////////////////////	
	// void useInfluence ()
	//
	// Uses influence for a province.
	/////////////////////////////////////////////////////	

	function useInfluence($toUse) {
		$this->database->query("UPDATE Province set influence=GREATEST((influence-$toUse),40-$toUse) where pID='$this->pID'");
	}
	/////////////////////////////////////////////////////	
	// bool vote ($pID)
	//
	// Changes your vote to $pID, if he is in your kingdom
	// Returns
	//
	// true success
	// false not able to.
	/////////////////////////////////////////////////////	
	
	function vote ($pID) {
		$voteFor = new Province($pID,$this->database);
		$voteFor->getProvinceData();
		if ($voteFor->getKiId() == $this->kiId) {
			$this->database->query("UPDATE Province set voteFor='$pID' WHERE pID='$this->pID'");
			return true;
		}
		return false;
	}

	/////////////////////////////////////////////////////	
	// void postNews ($txt)
	//
	// post news for this province.  $txt can be html code.
	// 
	// if no news object excists postNews will create one for the object.
	// 
	/////////////////////////////////////////////////////	
	function postNews ($txt) {
		require_once($GLOBALS['path_www_scripts'] . "News.class.inc.php");
		if ($this->newsObject==NULL) $this->newsObject = new News($this->database,1,$this->pID);
		$this->newsObject->postNews($txt);
	}

	/////////////////////////////////////////////////////	
	// string getAdvisorName ()
	//
	// returns:
	// 	- a string with a title infront of the ruler name
	/////////////////////////////////////////////////////	

	function getAdvisorName () {
		if ($this->king==$this->pID) $title= ($this->gender == 'M')? 'King':'Queen';
		else $title = $this->gender=='M' ? 'Sir':'Lady';
		return $title . " " . $this->rulerName;
	}
	/////////////////////////////////////////////////////	
	// string getShortTitle ()
	//
	// returns:
	//	- a short title (milady, sire, queen, king)
	/////////////////////////////////////////////////////	

	function getShortTitle () {
		if ($this->king==$this->pID) return ($this->gender == 'M')? 'King':'Queen';
		return $this->gender=='M' ? 'Sire':'Milady';	
	}

	/////////////////////////////////////////////////////	
	// void getMilitaryData()
	//
	// updates the $province internal miliatary array. 
	/////////////////////////////////////////////////////	
	function getMilitaryData () {
		if (!$this->milObject) {
		    require_once("Military.class.inc.php");
    	    $this->milObject = new Military($this->database, $this); //,&$this->database);
		}
//	   $this->military = $this->milObject->military;
	}
	
	/////////////////////////////////////////////////////	
	// int getpID ()
	//
	// returns:
	// 	- province ID (mysql)
	/////////////////////////////////////////////////////	
	function getpID() {
		return $this->pID;
	}

	/////////////////////////////////////////////////////	
	// int getkiId ()
	//
	// returns:
	//	- kingdom ID (mysql)
	/////////////////////////////////////////////////////	
	function getkiID() {
		return $this->kiId;
	}
	
	/////////////////////////////////////////////////////	
	// int getrID ()
	//
	// returns:
	//	- race ID (mysql)
	/////////////////////////////////////////////////////
	function getrID() {
		return $this->spID;
	}

	/////////////////////////////////////////////////////	
	// bool useResource ($gc,$metal,$food)
	//
	// This function subtracts the resources from the province
	//
	// parameters:
	//	- $gc : gold coins to use
	//	- $metal: metal to use
	//	- $food: food to use
	//
	// returns:
	//	- false: not enough gold or errorin sql
	//	- other: the resource set returned from mysql
	/////////////////////////////////////////////////////	
	function useResource($gc,$metal,$food, $peasants=0) {
		if (($this->gold<$gc) || ($this->food<$food) || ($this->metal<$metal)) return false;
		if (($gc+$metal+$food+$peasants)==0) return true;
		if ($peasants>0 && $this->peasants<$peasants) return false;
		$this->database->query("UPDATE Province SET 
										gold=gold-$gc,
										metal=metal-$metal,
										food=food-$food,
										peasants=peasants-$peasants
											WHERE
												((gold-$gc)>=0) AND
												((metal-$metal)>=0) AND
												((food-$food)>=0) AND
												((peasants-$peasants)>=0) AND
											 	pID='$this->pID'");
		if ($this->database->affectedRows()>0)		
		{
			$this->gold-=$gc; $this->food-=$food; $this->metal-=$metal; $this->peasants-=$peasants;
			return true;
		}
		return false;
	}

	/////////////////////////////////////////////////////	
	// bool gainResource ($gc,$metal,$food)
	//
	// This function adds the resources to the province
	//
	// parameters:
	//	- $gc : gold coins to get
	//	- $metal: metal to get
	//	- $food: food to get
	//
	// returns:
	//	- false: error
	//	- other: the resource set returned from mysql
	/////////////////////////////////////////////////////	
	function gainResource($gc,$metal,$food) {
		$this->gold+=$gc; $this->food+=$food; $this->metal+=$metal;
		return $this->database->query("UPDATE Province set gold=gold+$gc, metal=metal+$metal, food=food+$food where pID='$this->pID'");		
	}
        
        /////////////////////////////////////////////////////
        // void update()
        // update the class variables in the database	
	/////////////////////////////////////////////////////	
        function update() {
           $sqlUpdate = "update Province ";
           $sqlUpdate .= "set acres=$this->acres";
	   $sqlUpdate .= ", morale=$this->morale";
	   $sqlUpdate .= ", peasants=$this->peasants";
           $sqlUpdate .= " where pID=$this->pID";
           $this->database->query($sqlUpdate);
        }

	/////////////////////////////////////////////////////	
	// bool isKing()
	//
	// returns:
	//	-true: if province is king
	//	-false: if not king
	/////////////////////////////////////////////////////	
	function isKing () {
	   return ($this->pID==$this->king) ? true:false;
	}

	/////////////////////////////////////////////////////	
	// bool isProtected()
	//
	// returns:
	//	-true: if province is under protection
	//	-false: if not under protection
	/////////////////////////////////////////////////////	
	function isProtected () {
		if ( $this->protectionTime > 0 || $this->vacationmode == true)
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	/////////////////////////////////////////////////////	
	// bool isAlive()
	//
	// returns:
	//	-true: if province is 'Alive'
	//	-false: if status is not 'Alive'
	/////////////////////////////////////////////////////	

	function isAlive () {
		return ($this->status=='Alive') ? true:false;
	}

	/////////////////////////////////////////////////////	
	// bool incAttackNum()
	//
	// increments attack counter to help GB protection!
	// 
	/////////////////////////////////////////////////////	
	function incAttackNum () {
		$this->database->query("UPDATE Province set attackNum=attackNum+1 where pID=$this->pID");
	}

	function updateAttackStats($attacksMade=0, $attacksSuffered=0, $attackWins=0, $attacksSL=0) {
		$this->database->query("UPDATE Province set attacksMade=attacksMade+$attacksMade, attacksSuffered=attacksSuffered+$attacksSuffered, attackWins=attackWins+$attackWins, attacksSufferedLost=attacksSufferedLost+$attacksSL where pID=$this->pID");
	}
	
	function getReputation ()
	{
		return $this->reputation;
	}
	
	function getReputationRank ()
	{
		$this->database->query("SELECT COUNT(*) AS total, tb2.reputation,tb2.provinceName FROM Province,Province AS tb2 WHERE (Province.status='Alive' AND tb2.status='Alive' AND Province.kiID>0 AND tb2.kiID>0) AND Province.reputation >= tb2.reputation AND tb2.pID='$this->pID' group by tb2.provinceName order by reputation DESC");
		$arr = $this->database->fetchArray();
		return $arr['total'];
	}
	function getMagicRepRank ()
	{
		$this->database->query("SELECT COUNT(*) AS total, tb2.magicRep,tb2.provinceName FROM Province,Province AS tb2 WHERE Province.magicRep >= tb2.magicRep AND tb2.pID='$this->pID' group by tb2.provinceName order by magicRep DESC");
		$arr = $this->database->fetchArray();
		return $arr['total'];
	}
	function getExperienceRepRank ()
	{
		$this->database->query("SELECT COUNT(*) AS total, tb2.militaryRep,tb2.provinceName FROM Province,Province AS tb2 WHERE Province.militaryRep >= tb2.militaryRep AND tb2.pID='$this->pID' group by tb2.provinceName order by militaryRep DESC");
		$arr = $this->database->fetchArray();
		return $arr['total'];
	}

	function getAcreRank ()
	{
		$this->database->query("SELECT COUNT(*) AS total, tb2.acres,tb2.provinceName FROM Province,Province AS tb2 WHERE Province.acres >= tb2.acres AND tb2.pID='$this->pID' group by tb2.provinceName order by acres DESC");
		$arr = $this->database->fetchArray();
		return $arr['total'];
	}
	function getNetworthRank ()
	{
		$this->database->query("SELECT COUNT(*) AS total, tb2.networth,tb2.provinceName FROM Province,Province AS tb2 WHERE Province.networth >= tb2.networth AND tb2.pID='$this->pID' group by tb2.provinceName order by networth DESC");
		$arr = $this->database->fetchArray();
		return $arr['total'];
	}
	///////////////////////////////////////////////////////////////////////////////////
	//////Made by tasosos at 26/09/2009 so as to show an average rank for a province
	//////It only takes under consideration the acres, networth, magic reputation and
	//////the thievery reputation
	///////////////////////////////////////////////////////////////////////////////////
	function getAllAvgRank ()
	{
		//$this->database->query("SELECT COUNT(*) AS total, tb2.acres*0.1+tb2.networth*0.7+tb2.magicRep*0.1+tb2.reputation*0.1 as allAvgRank,tb2.provinceName FROM Province,Province AS tb2 WHERE Province.networth >= tb2.networth AND Province.acres >= tb2.acres AND Province.magicRep >= tb2.magicRep AND Province.reputation >= tb2.reputation AND tb2.pID='$this->pID' group by tb2.provinceName order by allAvgRank DESC");
		//$this->database->query("SELECT pID,networth+acres+magicRep+reputation AS allAvgRank FROM Province ORDER BY allAvgRank DESC");
		//$this->database->query("SELECT COUNT(*) AS total, 2*tb2.acres/3+tb2.networth/600+tb2.magicRep+tb2.reputation as allAvgRank,tb2.provinceName FROM Province,Province AS tb2 WHERE Province.networth/600+2*Province.acres/3+Province.magicRep+Province.reputation >= tb2.networth/600+2*tb2.acres/3+tb2.magicRep+tb2.reputation AND tb2.pID='$this->pID' group by tb2.provinceName order by allAvgRank DESC");
		
		$this->database->query("SELECT MAX(acres) FROM Province");
		$AcresRow = $this->database->fetchArray();
		$maxAcres =(int)$AcresRow['MAX(acres)'];
		
		$this->database->query("SELECT MAX(networth) FROM Province");
		$NetworthRow = $this->database->fetchArray();
		$maxNetworth = (int)$NetworthRow['MAX(networth)'];
		
		$this->database->query("SELECT MAX(magicRep) FROM Province");
		$MagicRow = $this->database->fetchArray();
		$maxMagic = (int)$MagicRow['MAX(magicRep)'];
		if ($maxMagic ==0)
			$maxMagic =1;

		$this->database->query("SELECT MAX(reputation) FROM Province");
		$ThieveryRow = $this->database->fetchArray();
		$maxThief = (int)$ThieveryRow['MAX(reputation)'];
		
		$this->database->query("SELECT COUNT(*) AS total, tb2.acres/'$maxAcres'+tb2.networth/'$maxNetworth'+tb2.magicRep/'$maxMagic'+tb2.reputation/'$maxThief' as allAvgRank,tb2.provinceName FROM Province,Province AS tb2 WHERE Province.networth/'$maxNetworth'+Province.acres/'$maxAcres'+Province.magicRep/'$maxMagic'+Province.reputation/'$maxThief' >= tb2.networth/'$maxNetworth'+tb2.acres/'$maxAcres'+tb2.magicRep/'$maxMagic'+tb2.reputation/'$maxThief' AND tb2.pID='$this->pID' group by tb2.provinceName order by allAvgRank DESC");

		$arr = $this->database->fetchArray();
		return $arr['total'];
		//$rows = array();
		//while($arr = $this->database->fetchArray()){
		//	$rows[]=$arr;
		//}
		//foreach($rows as $rank => $row){
		//	echo "#$rank : {$row['pID']}";
		//}
		
	}
	/////////////////////////////////////////////////////////////////////////////////////
	
	function updateReputation ($value)
	{
		$this->database->query("UPDATE Province set reputation=GREATEST((reputation+$value),0) WHERE pID='$this->pID'");
	}

	/////////////////////////////////////////////////////	
	// string displayProtection()
	//
	// If a province is in protection it make a text telling
	// that the province is in protection and for how much
	// longer it will stay protected.
	// returns:
	//	a text telling how long is left of protection
	//	else empty text
	/////////////////////////////////////////////////////	

	function displayProtection() {
		$retVal="";
		if ($this->isProtected())
		{
			$retVal = "\n\t<center><h3>".$this->getAdvisorName().", your province is under magical protection for the next ".($this->protectionTime)." days.\n\t\t<br> During this time others will 
			not be able to attack you</h3></center>";
		
		}
		return $retVal;
	}


	/////////////////////////////////////////////////////	
	// string displayProvince()
	//
	// This will display basic information about the province.  
	// How much military, pesants, knowledge a province has.
	// it will create a new science object to get the percentage 
	// of science a province has. 
	//
	// returns:
	//	a string containing the data.
	/////////////////////////////////////////////////////	

	function displayProvince ($show=255/*$GLOBALS['ProvinceConst']->SHOW_ALL*/) {
		require_once("Science.class.inc.php");
		$science = new Science($this->database,$this->pID);
		$scienceLevel =  $science->getScienceAge();
		if (!$this->isAlive()) {
			$info = "<center>You have been killed</center>";
		} else {
			if ($this->buildingPeasantPopulation<1) {
				$this->buildingPeasantPopulation = $this->acres*15;
			}
			$max =round ( (($this->peasants+$this->militaryPopulation)/$this->buildingPeasantPopulation)*100 );

			$info = '
<table align="center">
	<tr>
		<td><br><br><center><font color="#BDB585" size=6><b>' .$this->provinceName .' (#' .$this->kiId.')</b></font></center>
		</td>
	</tr>
	<tr bgcolor="#000000">
		<td valign="top">
			<table cellspacing="0" cellpadding="0" align="center" width="700" border="0">
				<tr>
					<td colspan="3" width="700" align="left">
						<img src="../img/msg_top.gif" width="700" height="46" border="0" alt="">
					</td>
				</tr>
				<tr>
					<td align="left" background="../img/msg_left.gif" width="52">&nbsp; 
					</td>
					<td width="606" align="center">
						<table border="0">
							<tr>
								<td>
									<table border="0">
										<tr><th align=left>Ruler:</th><td>'. $this->rulerName .' </td></tr>
										<tr><th align=left>Race:</th><td>' .$this->race. '</td></tr>
										<tr><th align=left>Gender:</th><td>' . ($this->gender=='M' ? 'male':'female') .'</td></tr>
										<tr><th align=left>Knowledge:</th><td>'.$scienceLevel.'</td></tr>
										<tr><th align=left>Gold:</th><td>' .number_format($this->gold,0,' ',',') .'gc</td></tr>
										<tr><th align=left>Metal:</th><td>' . number_format($this->metal,0,' ',',').'kg</td></tr>
										<tr><th align=left>Food:</th><td>' . number_format($this->food,0,' ',',').'kg</td></tr>
										<tr><th align=left>Acres:</th><td>' .$this->acres . '</td><td width=40></td></tr>
									</table>
								</td>
								<td width=40>
								&nbsp;
								</td>
								<td VALIGN=TOP>
									<table border="0">
									<tr><th align=left>Peasants:</th><td>' . number_format($this->peasants,0,' ',',') . '('.$max.'%)</td></tr>
									<tr><th align=left>Morale:</th><td>' .$this->morale .'%</td></tr>
		';
	$this->getMilitaryData();
	$milUnits = $this->milObject->getMilitaryNotTr();
	foreach ($milUnits as $unit) {
		if (($unit['object']->getMilType()==$GLOBALS['MilitaryConst']->WIZARDS && (!($show&$GLOBALS['ProvinceConst']->SHOW_MIL_WIZARDS))) ||
			($unit['object']->getMilType()==$GLOBALS['MilitaryConst']->THIEVES && (!($show&$GLOBALS['ProvinceConst']->SHOW_MIL_THIEVES)))
		) {
			$info .= "<tr><th align=left>Trained unit:</th><td>unknown</td></tr>";
		} else {
			$info .= "<tr><th align=left>" . $unit['object']->getName(). ":</th><td>" .number_format($unit['num'],0,' ',',') . "</td></tr>";
		}
	}
$extra = "";
if ($this->isOverPopulated())
{
  $extra = "Peasants are rioting in the streets due to overpopulation!";
}
$info .=		'
									</table>
								</td>
							</tr>
						</table>
					</td>
						<td align="left" background="../img/msg_right.gif" width="42">&nbsp; 
					</td>
				</tr>
				<tr>
					<td colspan="3" width="700"><img src="../img/msg_bottom.gif" width="700" height="45" border="0" alt="">
					</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr>
		<td><br><br><center>'.$extra.'</center>
		</td>
	</tr>

</table>
';

		}
	return $info;
	}

	function showDebugData()
	{
		echo "<br>Debug Data for Province class:<br>";
		echo "-----------------------------------------------------<br>";
		echo "Number of objects created:    " . $GLOBALS['province_objects_created'] . "<br>";
		echo "Calls to getData function:    " . $GLOBALS['province_function_count_getdata']. "<br>";
		echo "-----------------------------------------------------<br>";
	}
	
	// returns a modifier to the size.
	function sizeModifier($targetProvince, $bigisbad = true)
	{
		$bigisbad = true;
		$mod = 1.0;
		if ($targetProvince->acres > $this->acres)
			$mod = $this->acres / $targetProvince->acres;
		else
			$mod = $targetProvince->acres / $this->acres;
		
		if ($mod > 0.75)  // no sizeprotection if 75% of size
		{
			return 1.0;
		}
		
		return $mod + 0.15;
	}
	

} 
}
?>