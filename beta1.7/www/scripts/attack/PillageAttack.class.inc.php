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

if(!class_exists("PillageAttack")) {
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

	class PillageAttack extends AttackBase {
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
		var $winMorale;
		var $loseMorale;
		var $gold;
		var $metal;
		var $food;
		var $resourceRatioGrab = 0;

		var $numGenCoin = 0;

		//PARAM
		var $landGrabPR = 13;  // = 10%, is this OK? a bit less to make other attack better? lose less military, gain less land.... ?
		var $minLandGrab = 0; // = 10 acres..
		var $maxLandGrab = 0;

		function PillageAttack($attackID) {
		    $this->milConst = $GLOBALS["MilitaryConst"];
			$name = "Pillage Attack";
			$description = "Aren't your thieves good enough at stealing? Send out your soldiers to pillage another province, you will most probably manage to rob some of the supplies";
//                        echo "<br>aID: $attackID<br>";
			$ticks = array("to"=> 0, "back"=> 6, "attack"=> 0);
			$cost = array("gold"=> 0, "metal"=>0, "food"=>0);

			$canUseMil[$this->milConst->SOLDIERS] = true;
			$canUseMil[$this->milConst->DEF_SOLDIERS] = true;
			$canUseMil[$this->milConst->OFF_SOLDIERS] = true;
			$canUseMil[$this->milConst->ELITE_SOLDIERS] = true;
			$canUseMil[$this->milConst->WIZARDS] = false;
			$canUseMil[$this->milConst->THIEVES] = false;
			$canUseMil[$this->milConst->SPECIAL] = false;

			$this->AttackBase($attackID, $name, $description, $ticks, $cost, NULL, $canUseMil);
			
			$attackerWINloseRATIO = array("low" => 0, "high" => 3);
			$attackerLOSloseRATIO = array("low" => 1, "high" => 5);
//			$attackerWINloseRATIO = array("low" => 4, "high" => 9);
//			$attackerLOSloseRATIO = array("low" => 5, "high" => 10);
			$this->attackerDIE = array("win" => $attackerWINloseRATIO, "lose" => $attackerLOSloseRATIO);

			$defenderWINloseRATIO = array("low" => 0, "high" => 2);
			$defenderLOSloseRATIO = array("low" => 1, "high" => 2);
//			$defenderWINloseRATIO = array("low" => 2, "high" => 8);
//			$defenderLOSloseRATIO = array("low" => 3, "high" => 9);
			$this->defenderDIE = array("win" => $defenderWINloseRATIO, "lose" => $defenderLOSloseRATIO);
			
			$this->winMorale['MIN'] = 5;
			$this->winMorale['MAX'] = 25;
			
			$this->loseMorale['MIN'] = 7;
			$this->loseMorale['MAX'] = 30;
			
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
		    $this->sID = $milObj->province->pID;
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
			    $this->attack['attack'] *= ($this->effect->getEffect($this->effectConst->ADD_ATTACK, $this->sID));
			    $this->attack['defense'] *= ($this->effect->getEffect($this->effectConst->ADD_DEFENSE, $this->sID));
			}
			else if($typ == "Defense") {
			    $this->defense['attack'] *= ($this->effect->getEffect($this->effectConst->ADD_ATTACK, $this->tID));
			    $this->defense['defense'] *= ($this->effect->getEffect($this->effectConst->ADD_DEFENSE, $this->tID));
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
				$this->landRatio = (min($this->defenderProvince->acres, $this->attackerProvince->acres)/(max($this->attackerProvince->acres, $this->defenderProvince->acres)+1));
				$this->maxLandGrab = (int)($this->attackerProvince->acres / 2);
			} 
			else if ($typ == "armypoints") {
				$this->attackerRatio = ($this->attack['attack'] + 1) / ($this->defense['defense'] + 1); //how much bigger is Attacker
				$this->resourceRatioGrab = min(($this->attack['attack'] + 1), ($this->defense['defense'] + 1)) / max(($this->attack['attack'] + 1), ($this->defense['defense'] + 1));
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
			 
				$this->numGenCoin += $num;
   
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
				
				$this->numGenCoin -= $dead;
				
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
			$gold = (int) ((($this->defenderProvince->gold/100) * $this->landGrabPR) * $this->landRatio * $this->resourceRatioGrab * 1.3);
			$metal = (int) ((($this->defenderProvince->metal/100) * $this->landGrabPR) * $this->landRatio * $this->resourceRatioGrab * 1.3);
			$food = (int) ((($this->defenderProvince->food/100) * $this->landGrabPR) * $this->landRatio * $this->resourceRatioGrab * 1.3);
			
			//echo "calculating to: $gold, $metal, $food, max is ".$this->maxLandGrab."<br>";
//			$land = (int) (( ($this->defenderProvince->acres/100) * $this->landGrabPR ) * $this->landRatio);

			if($gold < $this->minLandGrab) $gold=$this->minLandGrab;
			//else if($gold > $this->maxLandGrab) $gold=$this->maxLandGrab;
			if($gold > $this->defenderProvince->gold) $gold=$this->defenderProvince->gold;

			if($metal < $this->minLandGrab) $metal=$this->minLandGrab;
			//else if($metal > $this->maxLandGrab) $metal=$this->maxLandGrab;
			if($metal > $this->defenderProvince->metal) $metal=$this->defenderProvince->metal;

			if($food < $this->minLandGrab) $food=$this->minLandGrab;
			//else if($food > $this->maxLandGrab) $food=$this->maxLandGrab;
			if($food > $this->defenderProvince->food) $food=$this->defenderProvince->food;

			$this->defenderProvince->useResource($gold,$metal,$food);
			//$this->attackerProvince->gainResource($gold,$metal,$food);
			//$sql = "update Province set acres=acres-$land where pID=".$this->tID;
			//$sql2 = "update Attack set acres=acres+$land where attackID=".$this->attID;


			$sql2 = "update Attack set gold=gold+$gold, food=food+$food, metal=metal+$metal where attackID=".$this->attID;
			$this->dbRef->query($sql2);

			$this->gold = $gold;
			$this->metal = $metal;
			$this->food = $food;

//			$this->destroyedBuildings = $this->buildings->destroyBuildingsOnAcres($this->tID, $land);
//			$this->land = $land;
//			$this->dbRef->query($sql);
//			$this->dbRef->query($sql2);
		}

/*		function grabLand() {
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
		}*/

		function writeNews() {
			$news = new News($this->dbRef, 1); //provincenews
		    $news2 = new News($this->dbRef, 0); //kingdomnews
			
			$newsText1 = $this->attackerProvince->getShortTitle().", you attacked ".$this->defenderProvince->provinceName."(#".$this->defenderProvince->kiId."), or maybe pillaging and plundering is a better description. The military losses during this operation was: <br>"; //newsText attackerProvince

			foreach($this->attackerLOSSES as $loss) {
				$newsText1 .= "\n\t<b>".$loss['milName'].": ".$loss['dead']."</b><br>";
			}

			$newsText1 .= "<br>On the bright side, you managed to plunder: <b>$this->gold goldcoins, $this->metal kg metal and $this->food units of food<b>";

			$newsText2 = $this->defenderProvince->getShortTitle().", you where attacked by ".$this->attackerProvince->provinceName."(#".$this->attackerProvince->kiId."), or maybe pillaging and plundering is a better description. Your military losses was: <br>"; //newsText defender

			foreach($this->defenderLOSSES as $loss) {
				$newsText2 .= "\n\t<b>".$loss['milName'].": ".$loss['dead']."</b><br>";
			}

			$newsText2 .= "<br>That thieving bastard(".$this->attackerProvince->provinceName.") also managed to run away with: <b>$this->gold goldcoins, $this->metal kg metal and $this->food units of food<b>";

		    $kingdom1 = ""; //newsText attacking Kingdom
		    $kingdom2 = ""; //newsText attacked Kingdom

			$kingdom1 .= "\n\t\t".$this->attackerProvince->provinceName." pillaged the province of ".$this->defenderProvince->provinceName."(#".$this->defenderProvince->kiId.").";

			$kingdom2 .= "\n\t\t".$this->defenderProvince->provinceName." was pillaged by the province of ".$this->attackerProvince->provinceName."(#".$this->attackerProvince->kiId.").";

			$news->postNews($newsText1, $this->sID);
			$news->postNews($newsText2, $this->tID);
	        $news2->postNews($kingdom1, $this->attackerProvince->kiId);
	        $news2->postNews($kingdom2, $this->defenderProvince->kiId);
		
		}

		function lowerMorale() {
			if($this->attack['attack'] > $this->defense['defense']) {
				$moraleLossPST = max( $this->winMorale['MIN'], ( (1 - ($this->landRatio*$this->landRatio)) * $this->winMorale['MAX']) );			
			}
				
			else {
				$moraleLossPST = max( $this->loseMorale['MIN'], ( (1 - ($this->landRatio*$this->landRatio)) * $this->loseMorale['MAX']) );
			}
			$moraleLoss = floor(($this->attackerProvince->morale/100)*$moraleLossPST);
			$sql = "update Province set morale=morale-$moraleLoss where pID=$this->sID";
			$this->dbRef->query($sql);
		}
		
		
		// Soptep: 22/01/2010 - Function to give extra income as bfFactor has been deprecated
		function setExtraIncome() {
			$peakAcres = $this->attackerProvince->acres * 1.1;
			
			// The greater the difference is the less extra income will be
			if ($peakAcres >= $this->defenderProvince->acres)
				$modifier = $this->defenderProvince->acres / $peakAcres;
			else
				$modifier = $peakAcres / $this->defenderProvince->acres;
		
			// if win then he will take the 1/8 of the attack points at extra income, otherwise 1/14%
			if ($this->attack['attack'] > $this->defense['defense'])
				$ratio = $this->attack['attack'] / 8;
			else 
				$ratio = $this->attack['attack'] / 14;
			
			$ratio *= $modifier;
			$ratio *= mt_rand (95, 105) / 100;
			
			$sql = "update Attack set extraIncome=$ratio where attackID={$this->attID}";
			$this->dbRef->query($sql);
        }
		

		function win() {
			if($this->attack['attack'] > $this->defense['defense']) return true;
			else return false;
		}

		function addExperience() {
                        $addExp = 0;
                        $rat = 100;
                        //Hardcoded bf prot:
                        if($rat < 50) {
                                $addExp = 1;
                        }
                        else if($rat < 70) {
                                $addExp = 4;
                        }
                        else if($rat < 80) {
                                $addExp = 16;
                        }
                        else if($rat < 90) {
                                $addExp = 64;
                        }
                        else if($rat < 110) {
                                $addExp = 256;
                        }
                        else {
                                $addExp = 300;
                        }

                        $newExp = ($this->defenderProvince->acres / 1000) * $addExp;
                        $randomshit = mt_rand(-30,10);
                        $randpr = 1 + ($randomshit/100);
                        $newExp = $newExp*$randpr;

                        $atExp=0;
                        $dfExp=0;
                        //$rat = min($this->attacker['attack'], $this->defense['defense'])/max($this->attacker['attack'], $this->defense['defense']);
                        //$rat = $this->attacker['attack'] / $this->defense['defense'];
                        $rat = 0.8;
                        $rat = min($rat,0.95);
                        $rat = max(0.75,$rat);
                        $atExp = floor($newExp*$rat);
                        $dfExp = floor($newExp-$atExp);
                        $this->attackerProvince->updateMilitaryExperience($atExp);
                        $this->defenderProvince->updateMilitaryExperience($atExp);
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

			$this->setExtraIncome();
			
			$this->addExperience();
				
			$this->grabLand();

			$this->writeNews();

		}

		function scienceRequirements() {
			return array('military' => 1, 'infrastructure' => 0, 'magic' => 0, 'thievery' => 1);
		}

		function sciReqTxt() {
			$txt = "<br><ul>Knowledge req:<br><br><li>Infrastructure: 0<li>Military: 1<li>Magic: 0<li>Thievery: 1<br>&nbsp;";
			return $txt;
		}
		
		function explain() {
			$txt = "Description<br><br>To get this attack you got to have the first knowledge in both military and thievery. You don't win or lose the same way as in the main attack, but steal resources such as gold, metal and food from a province depending on your size (in acres) compared to the size of the other province. You steal the most from those provinces the same size as you (base modifier is 13%). If provinces get bigger or smaller than you you will grab less resources. The more attackforce you send, the more resources will be grabbed<br><br>In this attack the attacker loses 5%-30% morale. If the attackforce is bigger than the defenseforce, the attacker loses 5%-25% morale in the same way as a 'win' in the main attack. If the attackforce is the same, or lower than the defenseforce the attacker will lose 7%-30% morale.<br><br>Attackforce and defenseforce are calculated as in basic description. This attack kills less units than the other attacks, and does not kill any peasants, thieves or magicians";
			return $txt;
		}

	}
}
?>