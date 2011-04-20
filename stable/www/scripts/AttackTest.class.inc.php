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
	require_once("all.inc.php");
	require_once($GLOBALS['path_www_scripts']."Military.class.inc.php");
	require_once($GLOBALS['path_www_scripts']."Effect.class.inc.php");
	class Attack {
		//VARS
		var $dbRef = NULL;
		var $pID = 0;
		var $attacks = NULL;
	        var $AttackConst = NULL;
	        var $MilitaryConst = NULL;
		var $attackObjects = NULL;
	        var $milObject = NULL;
	        var $province = NULL;
	        var $enemyProvince = NULL;
		var $effectiveMorale=0;
		var $MORALE=50;

		//FUNCTIONS
		function Attack(&$dbRef, $pID=0) {
			$this->dbRef = $dbRef;
			$this->pID=$pID;
		        require_once("attack/AttackConstants.class.inc.php");
	                $this->MilitaryConst = $GLOBALS['MilitaryConst'];
			$this->AttackConst = $GLOBALS['AttackConst'];
			if($pID > 0) {
				require_once("Province.class.inc.php");
				$this->province = new Province($this->pID, $this->dbRef);
			        $this->province->getProvinceData();
				require_once("attack/AttackConstants.class.inc.php");
	                        $this->MilitaryConst = $GLOBALS['MilitaryConst'];
				$this->AttackConst = $GLOBALS['AttackConst'];
				$this->initialize();
				$this->effect = new Effect($this->dbRef);
				$moraleBon = $this->effect->getEffect($GLOBALS[MilitaryConst]->ADD_MORALE, $this->pID);
				$this->effectiveMorale=$this->province->morale*$moraleBon;
			}
		}

		function initialize() {
			$this->attackObjects = $this->getAttackObjects();
			foreach($this->attackObjects as $attackObject) {
				if($this->requirementsOK($attackObject)) {
					$ID = $attackObject->getID();
					$this->attacks[$ID] = $attackObject;
				}
			}
		}

		function getAttacks($mode) {
			$retVal = NULL;
		        $sqlAttacks = "select * from Attack where pID=$this->pID";
		        //echo "<br>$sqlAttacks";
		        $this->dbRef->query($sqlAttacks);
		        if($this->dbRef->numRows() > 0) {
        		    while($data = $this->dbRef->fetchArray()) {
				$retVal[] = $data;
			    }
			}
			return $retVal;
		}

		function getAttackObjects() {
			$attackObjects = NULL;
			$path = $this->AttackConst->PATH_TO_ATTACK;
			$sqlGetAttackTypes = "select * from AttackT order by attackType asc";

			$this->dbRef->query($sqlGetAttackTypes);

			while($data = $this->dbRef->fetchArray()) {
				if(!class_exists($data['className'])) {
					require_once($path.$data['className'].".class.inc.php");
				}
			  /*      echo "<pre>";
			           print_r($data);
			        echo "</pre>";*/
				$attackID = $data['attackType'];
				$attackObjects[$attackID] = new $data['className']($attackID);
			}

			$this->dbRef->setRecordOffset(0);

			$data = $this->dbRef->fetchArray();

			$this->firstAttack = $data['attackID'];

			return $attackObjects;
		}

		function requirementsOK($attackObject) {
			require_once("Science.class.inc.php");
			require_once("Military.class.inc.php");

		    
		        //echo "DEBUG: OK<br>";
			$milObj = new Military($this->dbRef, $this->pID);
		        //echo "DEBUG: OK<br>";
			$science = new Science( $this->dbRef, $this->pID );
			$result = true;
			$sciReqArr = $attackObject->scienceRequirements();
			$milReqArr = $attackObject->militaryRequirements();
		/*echo "<pre>";
		print_r( $sciReqArr );
		echo "</pre><br>";*/
				
			if( !$science->scienceReqOk( $sciReqArr ) || !$milObj->militaryReqOk( $milReqArr ) ) {
				$result = false;
			}
		        //echo "DEBUG: OK<br>";
			//building req
			return $result;
		}
		
		function run() {
        		$attackType = $this->AttackConst->ATTACK_ID1;
			if(isset($_GET['attackType']) || isset($_POST['attackType']) ) {
				if(isset($_GET['attackType'])) $attackType = $_GET['attackType'];
				if(isset($_POST['attackType'])) $attackType = $_POST['attackType'];
				if(!isset($this->attacks[$attackType])) $attackType = $this->AttackConst->ATTACK_ID1;
			}
		    
		    
		   

			$attackObject = $this->attackObjects[$attackType];

			

			$retVal = "<center><table border='0' width='90%'><tr><td>";
		    
		    
		    
		        $retVal .= $this->handleAttack($attackObject);


			$retVal .= "<center>".$this->province->getAdvisorName().", in my mind attacking other provinces is the best way to become a prospering province.
								 By attacking, you will gain more land. This will make room for more pesants and military.
								 When attacking a province, you might get lucky and rob some of its resources, wich can come in handy when training more military.
								 Depending on your knowledge and military units, you'll be able to attack in different ways.<br><br>
								 Just keep in mind, you cant attack when your morale is lower than $this->MORALE%. At the moment your morale is ".($this->effectiveMorale)."% Your army will probably get about 10-15% less morale from one attack</center>";

			$retVal .= $this->showAttack($attackObject);
			if(count($this->attacks) > 1) {
				$retVal .= $this->showLinks($this->attacks);
			}
		    
		        $retVal .= "</td></tr><tr><td><br><h1>Current Attacks:</h1><br>";
		        if($attacks = $this->getAttacks(0)) {
			   $retVal .= "<table class='buildingsTable' width='100%'>";
			   $retVal .= "\n\t\t\t<tr><td class='buildings'>&nbsp</td><td class='buildings'>Estimated arrival/attack/return in days</td><td class='buildings' colspan='2'>&nbsp</td></tr>";
			   foreach($attacks as $attack) {
			       $retVal .= $this->showCurrentAttack($attack);
			   }
			   $retVal .= "</table>";
			}
			else {
			    $retVal .= $this->province->getAdvisorName().", you are currently not attacking anyone";
			}

			if( (isset($_GET['details'])) and (is_numeric($_GET['details'])) ) {
				$sql="select * from Attack where pID=$this->pID and attackID=".$_GET['details'];
				$this->dbRef->query($sql);
				//echo "<br>TEST".$this->dbRef->error();
				if($data = $this->dbRef->fetchArray()) {
					$targetID = $data['targetID'];
					$toTick = $data['totick'];
					$stayTick = $data['staytick'];
					$backTick = $data['backtick'];
					$retTick = $backTick+$toTick+$stayTick;
					$acres = $data['acres'];
					$gold = $data['gold'];
					$metal = $data['metal'];
					$food = $data['food'];
					$prov = new Province($targetID, $this->dbRef);
					$prov->getProvinceData();
					$retVal .= "<br><br><center><h2>Attackdetails</h2>
						<br><br>This is an attack launched upon the province of <b>$prov->provinceName(#$prov->kiId)</b>
						<br><br>Days until combat: <b>$toTick</b>
						<br>Days left to fight: <b>$stayTick</b>
						<br>Days until return: <b>$retTick</b>
						";
					if( ($toTick <= 0) && ($stayTick<=0)) $retVal .= "<br><br>This is an attack you cannot stop, because the battle is allready fought";
					else $retVal .= "<br><br>This is an attack you can stop, because the battle isn't over yet";

					if( ($acres>0) || ($gold) || ($metal) || ($food) ) {
						$retVal .= "<br>\n\t";
						if($acres>0) $retVal .= "<br>Stolen acres: <b>$acres</b>";
						if($gold>0) $retVal .= "<br>Stolen gold: <b>$gold</b>";
						if($metal>0) $retVal .= "<br>Stolen Metal: <b>$metal</b>";
						if($food>0) $retVal .= "<br>Stolen food: $food</b>";
					}

					$retVal .= "<br><br>The Army out fighting consists of:";
					$sql = "select * from Army where attackID=".$_GET['details'];
					$this->dbRef->query($sql);
					while($data = $this->dbRef->fetchArray()) {
						$mID = $data['mID'];
						$num = $data['num'];
						$this->milObject = new Military($this->dbRef, $this->pID);
						$milUnit = $this->milObject->getMilTypeObj($mID);
						$retVal .= "<br>".$milUnit->getName().": <b>$num</b>";
					}
					$retVal.= "</center>";
					
				}
			}
			$retVal .= "</tr></td>";
			
			$retVal .= $this->addSpaceBottom();

			return $retVal."</td></tr></table></center>";
		}

		function handleAttack($attackObject) {
			//start an attack
			if(isset($_POST['btnAttack'])) {
			    $this->milObject = new Military($this->dbRef, $this->pID);
	   		    $milArry = $this->milObject->getMilitaryHome(); //($this->MilitaryConst->OWN_MILITARY);
			    $tmpArr = array_reverse($_POST);

			    end($tmpArr);
			    //echo "<br>".key($tmpArr)."<br>";
		    
			    $army = NULL;
	                    
			    
			    while( substr( key($tmpArr), 0, 2) == 'id' ) {
				//echo "id: ".pos($tmpArr)."<br>";
				$milType =substr(key($tmpArr), 2, 1);
				$milNum = pos($tmpArr);
				if( is_numeric($milNum) && $milNum > 0 ) {
				    if( $milArry[$milType]['num'] >= $milNum ) {
					$milObj = $milArry[$milType]['object'];
					$name = $milObj->getName();
					$mID = $milObj->getID();
					$army[] = array("type" => $milType, "num" => $milNum, "mID" => $mID, "name" => $name, "object" => $milObj);
				    }
				    else {
					$milObj = $milArry[$milType]['object'];
					return "<br><center>Not enough ".$milObj->getName()." to send $milNum ".$milObj->getName()." to war</center><br>";
				    }
  
				}
				array_pop($tmpArr);
				end($tmpArr);
			    }
			    

			    if(!is_null($army)) {
				 $targetID = $_POST['selectedProvince'];
				 $attackTicks = $_POST['attackTicks'];
				 $this->enemyProvince = new Province($targetID, $this->dbRef);
		         $this->enemyProvince->getProvinceData();
				 
				if( ($this->pID != $targetID) && !($this->enemyProvince->isProtected()) && ($this->enemyProvince->isAlive()) && ($this->effectiveMorale >= $this->MORALE)) {
			        
					$sqlInsertAttack = "insert into Attack (pID, targetID, attackType, totick, staytick, backtick) values ($this->pID, $targetID, ".$attackObject->getID().",".$attackObject->getToTicks()." ,$attackTicks ,".$attackObject->getBackTicks().")";
					$sqlGetAttackID = "select attackID from Attack where pID=$this->pID order by attackID desc";
				
		//		echo "<br>$sqlInsertAttack<br>";

				
					$ticks = $attackObject->getToTicks() + $attackTicks + $attackObject->getBackTicks();
				
					$this->dbRef->query($sqlInsertAttack);
					$this->dbRef->query($sqlGetAttackID);
					$attackIDdata = $this->dbRef->fetchArray();
					$attackID = $attackIDdata['attackID'];
				    
					$sqlInsertArmyStart = "insert into Army (pID, attackID, mID, num, ticks) values ($this->pID, $attackID, ";
				
					$retVal = $this->province->getAdvisorName().", You have gone to war againts the Kingdom of";
					
				
				  	$txtArmy = "";	    
					foreach($army as $military) {
					    $mID = $military['mID'];
						//$mID = $this->milObject->getMilID($mType);
					    $num = $military['num'];
					    $sqlInsertA = $sqlInsertArmyStart."$mID, $num, $ticks)";
			//		    echo "<br>$sqlInsertA";
					    $this->dbRef->query($sqlInsertA);
					    //$removeMilitarty = "update Military set num=GREATEST(0, num-$num) where pID=$this->pID and mID=$mID";
						//echo "<br>$removeMilitarty";
					    //$this->dbRef->query($removeMilitarty);
					    $txtArmy .= "<br>".$num." ".$military['name'];
					}
				
					
					$attackObject->handleAttack($this->milObject, $targetID, $army, $attackID, $this->dbRef, $this->effect);
					$retVal = "<br><center><table class='buildingsTable' width='40%'><tr><td class='buildings'>".$this->province->getAdvisorName().", You have just sent some of your soldiers out to attack the province of ".$this->enemyProvince->provinceName;
					$retVal .= "(#".$this->enemyProvince->kiId.").\n&nbsp;Your soldiers will enter ".$this->enemyProvince->provinceName."(#".$this->enemyProvince->kiId.") in ".$attackObject->getToTicks()." days. The attack will last for ".$attackTicks." days, and your soldiers will most likely return ";
					$retVal .= $attackObject->getBackTicks()." days after the end of the attack.<br><br>The army you sent consists of: ".$txtArmy;
					return $retVal."</td></tr></table></center><br><br>";
			//		echo "<br>";
				 }
				 else {
					 if($this->pID == $targetID) return "<br><br>I wouldn't be wise to attack yourself... would it?<br><br>";
					 if($this->enemyProvince->isProtected()) return "<br><br>A magical force prevents you from attacking ".$this->enemyProvince->provinceName." for the time being<br><br>";
					 if(!$this->enemyProvince->isAlive()) return "<br><br>Don't you think it's a real waste of time attacking an allready dead province?<br><br>"; 
				 }
			    }
			    else {
				return "<br><br>What about actually sending some soldiers when you launch an attack?<br><br>";
			    }
			}
		}

		function showAttack($attackObject) {
			$retVal = "";
			$milClObj = new Military($this->dbRef, $this->pID);
		        $milClObj->initializeObject();
                        $milClObj->setArmy();
			$army = $milClObj->getMilitaryHome();

		        $getkiID = "select kiID from Province where pID=$this->pID";
			$this->dbRef->query($getkiID);
			$data = $this->dbRef->fetchArray();

		        if(isset($_POST['dropDownKingdom'])) {
			    $selectedKingdom = $_POST['dropDownKingdom'];
			} 
		        if((isset($_POST['selectedKingdom'])) && ($_POST['selectedKingdom'] != "") && (is_numeric($_POST['selectedKingdom']))) {
			    $selectedKingdom = $_POST['selectedKingdom'];
			    $sqlKingdomExists = "select * from Kingdom where kiID=$selectedKingdom";
			    $this->dbRef->query($sqlKingdomExists);
			    if($this->dbRef->numRows() < 1) {
				 $selectedKingdom = $data['kiID'];
			    }
			    
			}
		        else if(!isset($_POST['dropDownKingdom'])){
			    $selectedKingdom = $data['kiID'];
			}

		    
		        if(isset($_POST['selectedProvince'])) $selectedProvince = $_POST['selectedProvince'];
		        else $selectedProvince = $this->pID;
		    
		        
		        //SURROUNDING TABLE
			$retVal .= "\n<table border='0' width='90%'><tr><td colspan='2'>";
		    
		        //SELECT KINGDOM

		       $retVal .= "<table class='buildingsTable' width='100%'><form name='selKingdom' action='".$this->self()."' method='post'>
			 <tr>
		             <br><center><h1>".$attackObject->getName()."</h1></center><br>
			 </tr>
		         <tr>
		            <td class='buildings'>Select Kingdom #:</td>
		            <td class='buildings'><input type='text' name='selectedKingdom' size='5' class='form'> or <select name='dropDownKingdom' class='form'>";
		    
		       $dropDownKingdoms = "select * from Kingdom";
		       $this->dbRef->query($dropDownKingdoms);
		    
		       while($data = $this->dbRef->fetchArray()){
			   $retVal .= "\n\t\t\t\t<option value='".$data['kiID']."'";
			   if($selectedKingdom == $data['kiID']) $retVal .= " selected";
			   $retVal .= ">";
			   $retVal .= $data['name']."</option>";
		       }
		       
		       $retVal .= "</select>
			    </td>
		            <td class='buildings'><input type='submit' name='kingdomOK' value='OK' class='form'></td>
		         </tr></form>
		       </table>";
		    
		    
		    
		        $retVal .= "</td></tr>";
		    
		    
			$retVal .= "<tr><td width='60%'>";
		    
		    

			$retVal .= "<br><center>\n\t".$attackObject->getDescription()."<br><br>";
			$retVal .= "\n\t<table class='buildingsTable' width='100%'><form name='frmAttack' method='post' action='".$this->self()."'>";
			$retVal .= "\n\t\t<tr>\n\t\t\t<td class='buildings'>Name</td>";
			$retVal .= "\n\t\t\t<td class='buildings'>Number</td>";
			$retVal .= "\n\t\t\t<td class='buildings'>Send to war</td>\n\t\t</tr>";
			foreach($army as $military) {
				$milObj = $military['object'];
				$num = $military['num'];
			     //   echo "DEBUG: WRITING INPUTBOXES - ".$milObj->getName()." - ";
				if( ($attackObject->canUse($milObj->getMilType())) && $milClObj->requirementsOK($milObj)) {
			//	        echo "OK<br>";
					$retVal .= $this->writeMilitary($milObj, $num);
				}
			  //      else echo "ERR: cannot use<br>";
			        
			}
		    
		   
		        
		        $retVal .= "\n\t\t<tr>\n\t\t\t<td class='buildings' colspan='2' valign='center'>Province to attack</td>";
		        $retVal .= "\n\t\t\t<td class='buildings'><select name='selectedProvince' class='form'>";
		    
		        $provinces = "select pID, provinceName from Province where kiID=$selectedKingdom";
		        $this->dbRef->query($provinces);
		        while($data = $this->dbRef->fetchArray()) {
			   $retVal .="\n\t\t\t\t\t\t<option value='".$data['pID']."' size='4'";
			   if($selectedProvince = $data['pID']) $retVal .= " selected";
			   $retVal .= ">".$data['provinceName']."</option>";
      			}
		        $retVal .= "</select>";
		    
		        $retVal .= "\n\t\t<tr>\n\t\t\t<td class='buildings' colspan='2' valign='center'>Days to attack</td>";
		        $retVal .= "\n\t\t\t<td class='buildings'>";
		        if($attackObject->getAttackTicks() > 0) {
				   $retVal .= "<select name='attackTicks' class='form'>";
		           for($i = 1; $i <= $attackObject->getAttackTicks(); $i++) {
			          $retVal .="\n\t\t\t\t\t\t<option value='$i' size='4'>-- $i --</option>";
			       }
				   $retVal .= "</select>";
			    }
		        else {
			       $retVal .="\n\t\t\t\t\t\t<input type='hidden' name='attackTicks' value='0' size='4'><!-- 0 --</option> -->";
			    }
		        
		    
		        $retVal .= "\n\t\t\t\t</td>\n\t\t</tr>";
		    
		    $retVal .= "\n\t\t<tr>\n\t\t\t<td class='buildings' colspan='2' valign='center'>&nbsp;</td>";
		        $retVal .= "\n\t\t\t<td class='buildings'><input type='submit' name='btnAttack' value='Attack' class='form'></td>\n\t\t</tr>";
		        
			$retVal .= "</form></table>";
		    
		        $retVal .= "</td><td><center><img src='/img/war.jpg'></img></center></td></tr>";
		    
		    
		    
		        $retVal .= "\n</td><tr></table>";

			return $retVal;

			//show info, inpuboxes for an attack
		}

		function showLinks($array) {
			//if more attacks avilable show links to them
		}

		function addSpaceBottom() {
			//add space at bottom //templateDisplay Should do this!!!!!!
		}
	    
	        function writeMilitary($milObj, $num) {
		    $retVal = "";
		    $retVal .= "\n\t\t<tr>\n\t\t\t<td class='buildings'>".$milObj->getName()."</td>";
		    $retVal .= "\n\t\t\t<td class='buildings'>$num</td>";
		    $retVal .= "\n\t\t\t<td class='buildings'><input type='text' size='7' title='Enter number of ".$milObj->getName()." to send to war' name='id".$milObj->getMilType()."' class='form'></td>\n\t\t</tr>";
		    return $retVal;
		}
	    
	        function self() {
		    return $_SERVER['PHP_SELF'];
		}
		    
		function showCurrentAttack($attack) {
		    $attackID = $attack['attackType'];
		    $attackIID = $attack['attackID'];
		    $attackObj = $this->attackObjects[$attackID];
		    $enemyProv = $attack['targetID'];
		    $this->enemyProvince = new Province($enemyProv, $this->dbRef);
		    $this->enemyProvince->getProvinceData();
		    $totick = $attack['totick'];
		    $staytick = $attack['staytick'];
		    $backtick = $attack['backtick'];
		    
		    $retVal =  "\n\t\t\t<tr>\n\t\t\t\t<td class='buildings'>You are currently using ".$attackObj->getName()." to attack ".$this->enemyProvince->provinceName."(#".$this->enemyProvince->kiId.")";
		    $retVal .= "</td>\n\t\t\t\t<td class='buildings'>$totick/$staytick/".($totick+$staytick+$backtick)."</td>";
		    $retVal .= "<td class='buildings'><center><a href='".$this->self()."?details=$attackIID'>details</a></center></td><td class='buildings'><center>stop attack</center></td></tr>";
		    return $retVal;
		}

		function doTick() {
		    require_once("Military.class.inc.php");
		    require_once("Province.class.inc.php");
		    require_once("News.class.inc.php");
		    require_once("Science.class.inc.php");
		    require_once("Buildings.class.inc.php");
			$effect = new Effect($this->dbRef);
		    
		    $sqlGetAttackTypes = "select * from AttackT";
		    $sqlCountDownToTicks = "update Attack set totick=totick-1 where totick>0";
		    $sqlCountDownStayTicks = "update Attack set staytick=staytick-1 where staytick>0";
		    $sqlCountDownBackTicks = "update Attack set backtick=backtick-1 where backtick>0";
		    $sqlCountDownArmy = "update Army set ticks=ticks-1 where ticks>0";
		    $sqlDeleteAttacks = "delete from Attack where backtick<=0";
		    $sqlDeleteArmy = "delete from Army where ticks<=0";
		    $sqlGetResources = "select pID, acres, gold, metal, food from Attack where backtick<=0";
		    
		    
		    $this->dbRef->query($sqlCountDownToTicks);
		    $resGAT = $this->dbRef->query($sqlGetAttackTypes);
		    
		    $attackTypes = NULL;
		    
		    $milObjAttacker = new Military($this->dbRef);
		    $milObjDefender = new Military($this->dbRef);
		    
		    $newsProvince = new News($this->dbRef, 1);
		    $newsKingdom = new News($this->dbRef, 0);
		    
			$pref = NULL;

		    $buildings = @new Buildings($this->dbRef, $pref);
		    
		    
		    
		    while($data = $this->dbRef->fetchArray($resGAT)) {
			$attackType = $data['attackType'];
			$attackClass = $data['className'];
			$sqlGetAttacks = "select * from Attack where attackType=$attackType and totick<=0 and staytick>0";
			$resAttacks = $this->dbRef->query($sqlGetAttacks);
			if($this->dbRef->numRows() > 0) {
			    require_once("attack/".$attackClass.".class.inc.php");
			    $attackObj = new $attackClass($attackType);
			    while($dataAttack = $this->dbRef->fetchArray($resAttacks)) {
				$attackID = $dataAttack['attackID'];
				$pID = $dataAttack['pID'];
				$targetID = $dataAttack['targetID'];
				$attackObj->doTick($attackID, $milObjAttacker, $milObjDefender, $newsProvince, $newsKingdom, $buildings, $pID, $targetID, $effect);
			    }
			}
			
		    }
		    
		    $this->dbRef->query($sqlCountDownStayTicks);
		    $this->dbRef->query($sqlCountDownBackTicks);
		    $this->dbRef->query($sqlCountDownArmy);
		    
		
			///$sqlMoveNewDB = "update Province p, Attack a set p.acres=p.acres+a.acres, p.gold=p.gold+a.gold, p.food=p.food+a.food, 
//p.metal=p.metal+a.metal where (p.pID=a.pID and backtick<=0)";
		    $createTable = "create temporary table AttackTick select pID, sum(acres) as acres, sum(gold) as gold, sum(food) as food, sum(metal) as metal from Attack where backtick<=0 group by pID;";
	            $updateRes = "update Province, AttackTick set Province.acres=Province.acres+AttackTick.acres, Province.gold=Province.gold+AttackTick.gold, Province.food=Province.food+AttackTick.food, Province.metal=Province.metal+AttackTick.metal where Province.pID=AttackTick.pID";

		    $this->dbRef->query($createTable);
		    echo "<br>".$this->dbRef->error();
		    $this->dbRef->query($updateRes);
		    echo "<br>".$this->dbRef->error();
		    $this->dbRef->query("drop table AttackTick");
		    echo "<br>".$this->dbRef->error();

//			$this->dbRef->query($sqlMoveNewDB);		    

/*		    $resRec = $this->dbRef->query($sqlGetResources);
		    while($data = $this->dbRef->fetchArray($resRec)) {
			$moveSql = "update Province set acres=acres+".$data['acres'].", gold=gold+".$data['gold'].", food=food+".$data['food']." metal=metal+".$data['metal']." where pID=".$data['pID'];
			$this->dbRef->query($moveSql);
		    }  */
		    
		    
		    $this->dbRef->query($sqlDeleteAttacks);
		    $this->dbRef->query($sqlDeleteArmy);
		    $this->dbRef->query("Update Province set morale=least(morale+3, 100)");
		    
		}
		//END FUNCTIONS
	}

?>
