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
//die();
	require_once("Military.class.inc.php");
    require_once("Misc.class.inc.php");
	require_once("Effect.class.inc.php");

	mt_srand( (double)(microtime() * 1000000) );
	class Explore {
		//private
		var $exploredLand = 0;
		var $land = 0;
		var $num = 0;
		var $landPrSoldier = 0;
		var $soldiersPrLand = 0;
		var $STARTLAND=200;
		var $pObj = NULL;
		var $dbRef = NULL;
		var $rndAmount = 5;  // 5%
		var $growthFC = 0.2; //(5/40);
		var $cost = 300;
		var $ticks = 24;
		var $militaryConstants = NULL;
		var $milObj = NULL;
	    var $gold = 0;
	    var $soldiers = 0;
	    var $misc=NULL;
		var $MAXLAND = 50;
	    var $effect = NULL;
		var $maMilS = 0;

		function Explore($dbRef, $pObj) {
			//$this->pID = $pID;
			$this->dbRef = $dbRef;
			$this->pObj=$pObj;
			if(!is_null($pObj)) {
				$this->militaryConstants = $GLOBALS['MilitaryConst'];
				$pID=$this->pObj->pID;
				$this->milObj = new Military($dbRef, $pID);
				$this->milObj->initializeObject();
			    
				$this->pObj->getProvinceData();
				$sql = "select * from Explore where pID=".$this->pObj->pID;
				$this->dbRef->query($sql);
                                //echo "<br>".$this->dbRef->error();
				if($this->dbRef->numRows() < 1) {
					$sql = "insert into Explore (pID, exploredLand) values ( ".$this->pObj->pID.", 0)";
					$this->dbRef->query($sql);
					$this->exploredLand = 0;
				}
				else {
					$data = $this->dbRef->fetchArray();
					$this->exploredLand = $data['exploredLand'];
				}
				$this->land = $this->pObj->acres;
			        $this->gold = $this->pObj->gold;
				$useLand = max($this->land, ($this->exploredLand+$this->land));
			        $this->misc = new Misc();
			        $this->effect = new Effect($this->dbRef);
			        $this->cost *= $this->effect->getEffect($GLOBALS['MilitaryConst']->ADD_EXPLORE_GOLD_COST, $this->pObj->pID);
				$this->cost = ceil($this->cost);
				$lval = ($useLand / 1200);
				$this->cost += floor($lval*$lval*$lval*$lval);
				$grmod =  (($useLand*(2.2))/1200);
				if( ($useLand > 1500) && ($useLand < 2000)) $grmod *= ($useLand / 1200);
				if( ($useLand > 2000) && ($useLand < 3000)) $grmod *= ($useLand / 800);
				if($useLand > 3000) $grmod *= ($useLand / 400);
				if($useLand > 1200) $this->growthFC *= (1200/($grmod*$useLand));
			}
		}

		function getLandPrSoldier() {
			$useLand = max($this->land, ($this->exploredLand+$this->land));
			$this->landPrSoldier = ($this->STARTLAND/ ($useLand-($useLand * ($this->growthFC)) ));
			return $this->landPrSoldier;
		}

		function getSoldiersPrAcre() {
			if($this->landPrSoldier <= 0) $this->getLandPrSoldier();
			$this->soldiersPrLand = ceil(1/$this->landPrSoldier);
			return $this->soldiersPrLand;
		}

		function getLandToExplore() {
			if($this->landPrSoldier <= 0) $this->getLandPrSoldier();
			$land = $this->num * $this->landPrSoldier;
			if($land > (($this->land/100)*$this->MAXLAND)) $land = (($this->land/100)*$this->MAXLAND);
			$rndPr = mt_rand(-$this->rndAmount, $this->rndAmount);
			$extraLand = ( ($land/100) * $rndPr );
			$land += $extraLand;
			$land = floor($land);
			return $land;
		}

		// Goblins:    [num] [pris] [totalpris]
		function exploreLand() {
			$newLand = $this->getLandToExplore();
			$landArray = $this->misc->getRandomArray($newLand, $this->ticks);
			$sql1 = "update Explore set exploredLand=exploredLand+$newLand where pID=".$this->pObj->pID;
			//echo "<br>SQL: $sql1<br>";
			$this->dbRef->query($sql1);
			$i = 0;
			foreach($landArray as $land) {
				//$curKey = current($landArray);
				$ticks = ++$i;
				//echo "<br>land: $land, ticks: $ticks";
				if($land > 0) {
					$selectSQL = "select * from ProgressExpl where pID=".$this->pObj->pID." and tick=$ticks";
					$this->dbRef->query($selectSQL);
					//echo "ERROR: ".$this->dbRef->error();
					if($this->dbRef->numRows() > 0) {
						$sqlUpdate = "update ProgressExpl set num_acers=num_acers+$land where pID=".$this->pObj->pID." and tick=$ticks";
						$this->dbRef->query($sqlUpdate);
					}
					else {
						//echo "<br>NÅ SKAL JEG GJØRE DETTE";
						$sqlInsert = "insert into ProgressExpl (pID, tick, num_acers) values (".$this->pObj->pID.", $ticks, $land)";
						$this->dbRef->query($sqlInsert);
						//echo "ERROR: ".$this->dbRef->error();
					}
				}
			}
		}

		function self() {
			return $_SERVER['PHP_SELF'];
		}

		function writeExplore() {
			$retVal = "";
			$mil = $this->milObj->getMilUnit($this->militaryConstants->SOLDIERS);
			/*echo "<pre>";
		    print_r($mil);
			echo "</pre>";*/
			$milObj = $mil['object'];
			$num = $mil['num'];

			$this->maMilS = floor((($this->land/100)*$this->MAXLAND) / $this->getLandPrSoldier());

			$information = "<center>This is where you send your ".$milObj->getName()." to explore land. Every single one of your ".$milObj->getName()." is capable
			<br>of exploring <b><i>about</i></b> ".round($this->getLandPrSoldier(),2)." acres. In other words, you need <b><i>about</i></b> ".round($this->getSoldiersPrAcre(), 2)." 
			".$milObj->getName()." <br>to explore one acre. Before you send anyone to explore, be aware that your ".$milObj->getName()." will probably settle down in
			<br>the new lands, and desert your army<br><br><b><i>Just a final word... you'll probably not be able to explore more than<br> ".$this->MAXLAND."% of your own acre amount, so i will advise not to send any more than $this->maMilS ".$milObj->getName()."</i></b></center>";

			$retVal .= "<br><center><h1>Exploring</h1></center><br><br>$information";

			$table = "\n<br><center><br/><br/><form class='form' name='explForm' action='".$this->self()."' method='post'>";
			$table .= $GLOBALS['fcid_post'];
			$table .= "<table class='buildingsTable' width='80%'>\n\t<tr>";
			$table .= "\n\t\t<td class='buildings' width='50%'>\n\t\t\t<center>".$milObj->getName()." at home:</center>";
			$table .= "\n\t\t</td>";
			$table .= "\n\t\t<td class='buildings' width='16%'>\n\t\t\t<center>".$milObj->getName()." to send:</center>";
			$table .= "\n\t\t</td>";
			$table .= "\n\t\t<td class='buildings' width='16%'>\n\t\t\t<center>cost</center>";
			$table .= "\n\t\t</td>";
			$table .= "\n\t\t<td class='buildings' width='16%'>\n\t\t\t<center>Total Cost</center>";
			$table .= "\n\t\t</td>\n\t</tr>\n\t<tr>";
			
			$table .= "\n\t\t<td class='buildings' width='50%'>\n\t\t\t<center>$num</center>";
			$table .= "\n\t\t</td>";
			$table .= "\n\t\t<td class='buildings' width='16%'>\n\t\t\t<center><input class='form' type='text' name='num' value='0' size='7' onChange='document.explForm.num.value=Math.round(document.explForm.num.value);
	document.explForm.totCost.value=(document.explForm.num.value * $this->cost);'></center>";
			$table .= "\n\t\t</td>";
			$table .= "\n\t\t<td class='buildings' width='16%'>\n\t\t\t<center>$this->cost</center>";
			$table .= "\n\t\t</td>";
			$table .= "\n\t\t<td class='buildings' width='16%'>\n\t\t\t<center><input type='text' name='totCost' size='12' class='readOnly' readonly='1' value='0'></center>";
			$table .= "\n\t\t</td>\n\t</tr>";
			
			$table .= "\n</table><br><input class='form' type='Submit' name='exploreOK' value='Explore Land'></form></center>";

			$retVal .= $table;

			return $retVal;
		}

		function handleExplore() {
			$retVal = "";
		        $mil = $this->milObj->getMilUnit($this->militaryConstants->SOLDIERS);
			$milObj = $mil['object'];
		        $this->soldiers = $mil['num'];
		     //   echo "<br>debug handleExplore---";
			if(isset($_POST['num'])) $this->num = $_POST['num'];
			else $this->num = 0;
			if(isset($_POST['exploreOK']) && is_numeric($this->num) && $this->num > 0) {
			 //   echo "<br>debug handleExplore: SubmitButton is pushed, and the amount to send to training is numeric";
				$cost = $this->num * $this->cost;
			      //  echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;cost: $cost, gold, $this->gold, soldiers: $this->soldiers";
			        
				if(($cost <= $this->gold) &&  ($this->num <= $this->soldiers)) {
					$this->maMilS = floor((($this->land/100)*$this->MAXLAND) / $this->getLandPrSoldier());
				       // echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Enough gold and recruits";
					$this->milObj->killUnits($this->militaryConstants->SOLDIERS, $this->num, false);
					$this->pObj->useResource($cost, 0, 0);
					$this->exploreLand();
					if($this->num > $this->maMilS) {
						$retVal .= "<center><br><br>I see you didn't follow my advice... so now a good deal of the ".$milObj->getName()." you sent were of no use. I wish I could tell you what happened to them, but I really don't know. There are rumors about a big red dragon lurking in the mountains.. but I really don't know.</center>";
					}
					$retVal .= "<center><br><br>You have sent $this->num ".$milObj->getName()." to explore land<br><br></center>";
				}
				else {
					if($cost > $this->gold) $retVal .= "<center><br>Wouldn't it be wise to make sure you got enough gold?<br></center>";
					if($this->num > $this->soldiers) $retVal .= "<center><br>Sorry, but you dont have enough ".$milObj->getName()."<br></center>";					
				}
			}
			else {
				//ERROR
			}
			return $retVal;
		}

		function showExplorProg() {
//			$mil = $this->milObj->getMilUnit($this->militaryConstants->SOLDIERS);
			/*echo "<pre>";
		    print_r($mil);
			echo "</pre>";*/
//			$milObj = $mil['object'];
			$sql = "select * from ProgressExpl where pID=".$this->pObj->pID." order by tick asc";
			//echo "SQL: $sql";
			$this->dbRef->query($sql);
			//echo "ERR ".$this->dbRef->error();
			$retVal = "\n<center><table class='buildingsTable' width='95%'>\n\t<tr>";
			$retVal .= "\n\t\t<td class='buildings'>Days:</td>";
			for($j = 0; $j < 24; $j++) {
				$retVal .= "\n\t\t<td class='buildings'>".($j+1)."</td>";
			}
			$retVal .= "\n\t\t<td class='buildings'>total</td>\n\t</tr>\n\t<tr>";
			$retVal .= "\n\t\t<td class='buildings'>Acres under exploring</td>";
			$i = 0;
			$sumNum = 0;
			$dataArray = array_fill(1, 24, 0);
			while($data = $this->dbRef->fetchArray()) {
				$tick = $data['tick'];
				$dataArray[$tick] = $data['num_acers'];
		//		$sum += $data['num_acres'];
				$i++;
			}
			

			foreach($dataArray as $data) {
				if($data > 0) $retVal .= "\n\t\t<td class='buildings' width='3%'>".$data."</td>";
				else $retVal .= "\n\t\t<td class='buildings'>&nbsp;</td>";
				$sumNum = $sumNum + $data;
			}
			if($sumNum > 0) $retVal .= "\n\t\t<td class='buildings' width='3%'>".$sumNum."</td>";
			else $retVal .= "\n\t\t<td class='buildings'></td>";

			$retVal .= "\n\t</tr>\n</table>";
			return $retVal;
		}

		function doTick() {
			$sqlCountDown = "update ProgressExpl RIGHT JOIN Province on (ProgressExpl.pID=Province.pID) set tick=tick-1 where (tick>0 AND Province.vacationmode='false')";
			$sqlUpdate = "update Province RIGHT JOIN ProgressExpl on (Province.pID=ProgressExpl.pID AND ProgressExpl.tick<=0) set Province.acres=Province.acres+ProgressExpl.num_acers";
			$sqlDelete = "delete from ProgressExpl where tick<=0";
			$this->dbRef->query($sqlCountDown);
			$this->dbRef->query($sqlUpdate);
			$this->dbRef->query($sqlDelete);
		}

		function run() {
			$retVal = "";
			$retVal .= $this->writeExplore();
			$retVal .= "<br/><br/><br/><br/><center><h1>Land currently being explored</h1></center>".$this->showExplorProg();
			return $retVal;
		}

	}
?>
