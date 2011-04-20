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

if(!class_exists("MainAttack")) {
//	require_once("all.inc.php");
    $path = $GLOBALS['path_www_scripts'];
//echo $path;
	require_once("AttackBase.class.inc.php");
	require_once($path."military/MilitaryConstants.class.inc.php");
	require_once($path."effect/EffectConstants.class.inc.php");
	require_once($path."Buildings.class.inc.php");
	require_once($path."Science.class.inc.php");
	require_once($path."Province.class.inc.php");
	require_once($path."Military.class.inc.php");
    require_once($path."News.class.inc.php");
	require_once($path."Effect.class.inc.php");

	class MainAttack extends AttackBase {
		// LEAVE THEESE ALONE... 
		var $attackerMilObj = NULL;
		var $defenderMilObj = NULL;
		var $attackerProvince = NULL;
		var $defenderProvince = NULL;
		var $attackerSciFi = NULL;
		var $defenderSciFi = NULL;
		var $buildings = NULL;
		var $landRatio = 0;
		var $sID = 0;
		var $tID = 0;
		var $attID = 0;
		var $attack=0;
		var $defense=0;
		var $attackerRatio = 1;
		var $defenderRatio = 1;
		var $land = 0;
		var $dbRef = NULL;
		var $attackerDIE = NULL;
		var $defenderDIE = NULL;
	    var $milConst = NULL;
		var $effect = NULL;
        var $effectConst = NULL;
		var $destroyedBuildings = NULL;

		//PARAM
		var $landGrabPR = 8;  // = 10%, is this OK? a bit less to make other attack better? lose less military, gain less land.... ?
		var $minLandGrab = 0; // = 10 acres..
		var $maxLandGrab = 0;

		function MainAttack($attackID) {
		    $this->milConst = $GLOBALS["MilitaryConst"];
			$name = "Ordinary Attack";
			$description = "This is the main attack, the one wich is straight foreward, and all the ordinary military units know how to do. This attack do not require any knowledge at all";
//                        echo "<br>aID: $attackID<br>";
			$ticks = array("to"=> 0, "back"=> 7, "attack"=> 0);
			$cost = array("gold"=> 0, "metal"=>0, "food"=>0);

			$canUseMil[$this->milConst->SOLDIERS] = true;
			$canUseMil[$this->milConst->DEF_SOLDIERS] = true;
			$canUseMil[$this->milConst->OFF_SOLDIERS] = true;
			$canUseMil[$this->milConst->ELITE_SOLDIERS] = true;
			$canUseMil[$this->milConst->WIZARDS] = false;
			$canUseMil[$this->milConst->THIEVES] = false;
			$canUseMil[$this->milConst->SPECIAL] = false;

			$this->AttackBase($attackID, $name, $description, $ticks, $cost, NULL, $canUseMil);
			
			$attackerWINloseRATIO = array("low" => 4, "high" => 9);
			$attackerLOSloseRATIO = array("low" => 5, "high" => 10);
			$this->attackerDIE = array("win" => $attackerWINloseRATIO, "lose" => $attackerLOSloseRATIO);

			$defenderWINloseRATIO = array("low" => 2, "high" => 8);
			$defenderLOSloseRATIO = array("low" => 3, "high" => 9);
			$this->defenderDIE = array("win" => $defenderWINloseRATIO, "lose" => $defenderLOSloseRATIO);
			//$this->effect = new Effect($this);
		}

		function update() {
			//update an attack
			return 0;
		}

		function attack( $provinceMilitaryObject, $enemyMilitaryObject ) {
			//start an attack!!!!!!!
			return 0;
		}

	    function initiateAttack($milObj, $tID, $army, $attID, $dbRef, $effect) {
		    $this->dbRef = $dbRef;
		    $this->sID = $milObj->pID;
		    $this->tID = $tID;
//		    echo "<br>ID PROVINS: $this->sID, $this->tID<br>";
		    $this->attackerMilObj = $milObj;
		    $this->defenderMilObj = new Military($this->dbRef, $tID);
		    $this->defenderMilObj->initializeObject();
		    $this->attackerMilObj->setArmy($army);
		    $this->defenderMilObj->setArmy();
		    $this->attackerProvince = new Province($this->sID, $dbRef);
		    $this->defenderProvince = new Province($tID, $dbRef);
		    $this->attackerProvince->getProvinceData();
		    $this->defenderProvince->getProvinceData();
		    $this->attackerSciFi = new Science($this->dbRef, $this->sID);
		    $this->defenderSciFi = new Science($this->dbRef, $tID);
			$pref = NULL;
		    $this->buildings = new Buildings($this->dbRef, $pref);
		    $this->attID = $attID;
			$this->effect = $effect;
		    $this->effectConst = new EffectConstants();
		}
	    
		function calculate($typ) {
			$retVal = 0;
			if($typ == "Attack") {
			    $retVal =  $this->calculateAttack();
			    $effectiveMorale = $this->attackerProvince->morale * 1; //$this->effect->getEffect($GLOBALS['MilitaryConst']->ADD_MORALE, $this->sID);
			    $retVal['attack'] *= ($effectiveMorale/100); 
			    $retVal['defense'] *= ($effectiveMorale/100);
			}
			else if($typ == "Defense") {
				$retVal = $this->calculateDefense();
		        	$effectiveMorale = ($this->defenderProvince->morale / 100);
			        if($effectiveMorale > 1) {
					$retVal['attack'] *= $effectiveMorale;
					$retVal['defense'] *= $effectiveMorale;
				}
			}
			else {
				$retVal = false;
			}
			return $retVal;
		}

		function calculateAttack() {
			return $this->attackerMilObj->getArmyPoints();
		}

		function calculateDefense() {
			return $this->defenderMilObj->getArmyPoints();
		}

		function addBonus($typ) {
			if($typ == "Attack") {
			    $this->attack['attack'] *= round($this->effect->getEffect($this->effectConst->ADD_ATTACK, $this->sID));
			    $this->attack['defense'] *= round($this->effect->getEffect($this->effectConst->ADD_DEFENSE, $this->sID));
			}
			else if($typ == "Defense") {
			    $this->defense['attack'] *= round($this->effect->getEffect($this->effectConst->ADD_ATTACK, $this->tID));
			    $this->defense['defense'] *= round($this->effect->getEffect($this->effectConst->ADD_DEFENSE, $this->tID));
			}
			else {
				//asumption ERR
			//	echo "<br>Error adding bonuses<br>";
			}
		}

		function calcRatio($typ) {
			if($typ == "land") {
				$this->attackerProvince->getProvinceData();
				$this->defenderProvince->getProvinceData();
				$this->landRatio = ($this->defenderProvince->acres/($this->attackerProvince->acres + 1));
				$this->maxLandGrab = (int)($this->attackerProvince->acres / 2);
			} 
			else if ($typ == "armypoints") {
				$this->attackerRatio = ($this->attack['attack'] + 1) / ($this->defense['defense'] + 1); //how much bigger is Attacker
				$this->defenderRatio =   (pow(10, (($this->attack['attack'] + 1)/(($this->defense['defense']+1)/2)))) / 100;    
			    $this->attackerRatioTo = max(( ($this->attack['defense'] + 1) / ($this->defense['attack'] + 1) ), 1);
				if($this->attackerRatio >= 1) $this->attackerRatio = 1;
				else $this->attackerRatio = 1 + (1 - $this->attackerRatio); 
				if($this->defenderRatio > 1) $this->defenderRatio = 1;
			}
			else {
				//ERRROR
			}
		}

		function killUnits() {
			$attArmy = $this->attackerMilObj->getArmy();
			$defArmy = $this->defenderMilObj->getArmy();
			
			$attackerLOSSES = NULL;
			$defenderLOSSES = NULL;
			$defLoseRatio = NULL;
			$attLoseRatio = NULL;
			if($this->attack['attack'] > $this->defense['defense']) {
				$defLosePr = $this->defenderDIE['lose'];
				$attLosePr = $this->attackerDIE['win'];
			}
			else {
				$defLosePr = $this->defenderDIE['win'];
				$attLosePr = $this->attackerDIE['lose'];
			}

		    
		        foreach($attArmy as $milUnit) {
				$num=$milUnit['num'];
				$milObj = $milUnit['object'];
			        $type = $milObj->getMilType();
			    
			        //echo "<br><br> type: $type,  $num, ".$attLosePr['high'].", ".$attLosePr['low'].", $this->attackerRatio";
				$maxNumDie = ceil(($num/100) * ($attLosePr['high'] * $this->attackerRatio));
				$minNumDie = ceil(($num/100) * ($attLosePr['low'] * $this->attackerRatio));
				$dead = @mt_rand($minNumDie, $maxNumDie);
			  //      echo "<br>Dead before: $dead";
			        $lessDeadpr = $this->attackerRatioTo * $this->milConst->MIL_LOSSES[$type];
					$lessDead = ceil(($dead/100)*$lessDeadpr);
					$dead -= $lessDead;
			    //    echo " Dead after: $dead";
			        $dead = max($dead, 0);
			        //echo " Dead after eliminating -dead: $dead";
//				$sql = "update Military set num=num-$dead where pID=".$this->sID." and mID=".$milObj->getID();
				$this->attackerMilObj->killUnits($type, $dead);
				$sql2 = "update Army set num=num-$dead where pID=".$this->sID." and mID=".$milObj->getID()." and AttackID=".$this->attID;
	//			$this->dbRef->query($sql);
				$this->dbRef->query($sql2);
				$attackerLOSSES[] = array("dead" => $dead, "milName" => $milObj->getName());
			}

			//DEFENDERS
			foreach($defArmy as $milUnit) {
		        	$milObj = $milUnit['object'];

				if( ($milObj->getMilType() != $this->milConst->WIZARDS) && ($milObj->getMilType() != $this->milConst->THIEVES)) {
					$num=$milUnit['num'];
	       		        	$unitRatio=(100 - $this->milConst->MIL_LOSSES[$milObj->getMilType()]) / 100;
			      //  echo "<br>Defender num: $num, defLose: ".$defLosePr['high']." defLoseLOW: ".$defLosePr['low']." ratio $this->defenderRatio, typeRatio: $unitRatio";
					$maxNumDie = ceil(($num/100) * ($defLosePr['high'] * $this->defenderRatio));
					$minNumDie = ceil(($num/100) * ($defLosePr['low'] * $this->defenderRatio));
					$dead = @mt_rand($minNumDie, $maxNumDie);
			      //  echo "<br>Dead before: $dead";
			        	$dead = ceil($dead * ($unitRatio));
			      //  echo " Dead AFTER: $dead";
			        	$dead = max($dead, 0);
				//echo "<br>DEAD! : $dead<br>"; // = 2;
	//			$sql = "update Military set num=num-$dead where pID=".$this->tID." and mID=".$milObj->getID();
					$type = $milObj->getMilType();
					$this->defenderMilObj->killUnits($type, $dead);
					$sql2 = "update Army set num=num-$dead where pID=".$this->tID." and mID=".$milObj->getID()." and AttackID=".$this->attID;
	//				$this->dbRef->query($sql);
					$this->dbRef->query($sql2);
					$defenderLOSSES[] = array("dead" => $dead, "milName" => $milObj->getName());
				}
			}
			$this->defenderLOSSES = $defenderLOSSES;
			$this->attackerLOSSES = $attackerLOSSES;
		}

		function grabLand() {
		    if($this->attack['attack'] > $this->defense['defense']) {
			$land = (int) (( ($this->defenderProvince->acres/100) * $this->landGrabPR ) * $this->landRatio);
			if($land < $this->minLandGrab) $land=$this->minLandGrab;
			else if($land > $this->maxLandGrab) $land=$this->maxLandGrab;
			if($land > $this->defenderProvince->acres) $land=$this->defenderProvince->acres;
			$sql = "update Province set acres=acres-$land where pID=".$this->tID;
			$sql2 = "update Attack set acres=acres+$land where attackID=".$this->attID;
			$this->destroyedBuildings = $this->buildings->destroyBuildingsOnAcres($this->tID, $land);
			$this->land = $land;
			$this->dbRef->query($sql);
			$this->dbRef->query($sql2);
		    }
		}

		function writeNews() {
			$news = new News($this->dbRef, 1);
		        $news2 = new News($this->dbRef, 0);
			$newsText1 = "";
			$newsText2 = "";
		        $kingdom1 = "";
		        $kingdom2 = "";

			if($this->attack['attack'] > $this->defense['defense']) {
				//ATTACKER NEWS TEXT!!
				$newsText1 .= "\n\t\t".$this->attackerProvince->getShortTitle().", you attacked ".$this->defenderProvince->provinceName."(#".$this->defenderProvince->kiId.") and we can say you won the war<br>";
				$newsText1 .= "\n\tYour losses are:<br>";
			        $kingdom1 .= "\n\t\t".$this->attackerProvince->provinceName." attacked the province of ".$this->defenderProvince->provinceName."(#".$this->defenderProvince->kiId."). ".$this->attackerProvince->provinceName." won and stole $this->land acres";

				//DEFENDER NEWS TEXT!
				$newsText2 .= "\n\t\t<br>".$this->defenderProvince->getShortTitle()." you where attacked by ".$this->attackerProvince->provinceName."(#".$this->attackerProvince->kiId.") and I'm sorry to say you lost the war<br>";
				$newsText2 .= "\n\t\And your losses are:<br>";
			        $kingdom2 .= "\n\t\t".$this->defenderProvince->provinceName." was attacked by the province of ".$this->attackerProvince->provinceName."(#".$this->attackerProvince->kiId."). ".$this->defenderProvince->provinceName." lost  and $this->land acres was lost in the combat";
			}
			else {
				$newsText1 .= "\n\t\t".$this->attackerProvince->getShortTitle().", you attacked ".$this->defenderProvince->provinceName."(#".$this->defenderProvince->kiId.") and we I'm sorry to say you lost the war<br>";
				$newsText1 .= "\n\t\tYour losses are:<br>";
			        $kingdom1 .= "\n\t\t".$this->attackerProvince->provinceName." attacked the province of ".$this->defenderProvince->provinceName."(#".$this->defenderProvince->kiId."). ".$this->attackerProvince->provinceName." lost";
				$newsText2 .= "\n\t\t<br>".$this->defenderProvince->getShortTitle()." you where attacked by ".$this->attackerProvince->provinceName."(#".$this->attackerProvince->kiId.") but you defeated your enemy<br>";
				$newsText2 .= "\n\t\Still you have some losses and they are:<br>";
			        $kingdom2 .= "\n\t\t".$this->defenderProvince->provinceName." was attacked by the province of ".$this->attackerProvince->provinceName."(#".$this->attackerProvince->kiId."). ".$this->defenderProvince->provinceName." won";
			}
			
			foreach($this->attackerLOSSES as $loss) {
				$newsText1 .= "\n\t".$loss['milName'].": ".$loss['dead']."<br>";
			}
			
			foreach($this->defenderLOSSES as $loss) {
				$newsText2 .= "\n\t".$loss['milName'].": ".$loss['dead']."<br>";
			}

			if($this->land > 0) {
				$newsText1 .= "\n\tYou also stole ".$this->land." acres.";
				$newsText2 .= "\n\tYou also lost ".$this->land." acres. Because of this you also lost: ".$this->destroyedBuildings['html']."<br>Wich make up a total of ".$this->destroyedBuildings['totDestroyed']." buildings";
			}
		        //echo "<br><br>NEWS: ".$newsText1."<br><br>";
			$news->postNews($newsText1, $this->sID);
			$news->postNews($newsText2, $this->tID);
		        $news2->postNews($kingdom1, $this->attackerProvince->kiId);
		        $news2->postNews($kingdom2, $this->defenderProvince->kiId);
		}

		function lowerMorale() {
			if($this->attack['attack'] > $this->defense['defense'])
				$moraleLoss = floor(($this->attackerProvince->morale/100)*15);
			else $moraleLoss = floor(($this->attackerProvince->morale/100)*20);
			$sql = "update Province set morale=morale-$moraleLoss where pID=$this->sID";
			$this->dbRef->query($sql);
		}

		function win() {
			if($this->attack['attack'] > $this->defense['defense']) return true;
			else return false;
		}
		
		function handleAttack($milObj, $tID, $army, $attID, $dbRef, $effect) {
		    //print_r($dbRef);
		    $this->initiateAttack($milObj, $tID, $army, $attID, $dbRef, $effect);
		    
			$this->attack = $this->calculate("Attack");
			$this->defense = $this->calculate("Defense");

			$this->addBonus("Attack");
			$this->addBonus("Defense");

			$this->calcRatio("land");
			$this->calcRatio("armypoints");

			$this->killUnits();
			//$this->killUnits("Attackers");
			//$this->killUnits("Defenders");
			
			$this->lowerMorale();

			$this->grabLand();

			$this->writeNews();

		}

	}
}
?>