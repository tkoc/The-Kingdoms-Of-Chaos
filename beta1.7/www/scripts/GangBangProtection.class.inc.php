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

class GangBangProtection {
		var $province = null;
		var $provinceArray = null;
		var $kiPrArray = null;
		var $enemyProvince = null;
		var $oneProvince = null;
		var $twoProvince = null;
		var $threeProvince = null;
		var $gangbang = 5;
		var $lastError = "";
		var $errLog = null;
		var $dbRef = null;

		function GangBangProtection($dbRef, $pObj) {
			mt_srand( (double) microtime() * 1000000);

			$this->threeProvince = array(0, 10, 25, 47, 49, 51);
			$this->twoProvince = array(0, 3, 12, 16, 21, 36, 45, 51);
			$this->oneProvince = array(0, 1, 6, 8, 14, 18, 20, 22, 29, 31, 33,  37, 39, 41, 51);

			$this->dbRef = $dbRef;
			$this->province = $pObj;
		}

		function setEnemy($pObj) {
			$this->enemyProvince = $pObj;
		}

		function triggered() {
			$retval = true;
				if($this->enemyProvince->attackNum >= $this->gangbang) {
					$retval = true;
				}
				else {
					$retval = false;
				}
			return $retval;
			//return false;
		}

		function addAttack() {
			if($this->enemyProvince->kiId != $this->province->kiId) {
				$this->enemyProvince->incAttackNum();
			}
		}

		function getProvincesAttacking() {
			$getProvinces = "select pID from Attack where targetID=".$this->enemyProvince->pID;
			$this->dbRef->query($getProvinces);
			//GB prot ONLY if more than 5 attacks... so this shall NOT happen.. can happend in a tick change...
			if($this->dbRef->numRows() < 5) {
				$this->lastError = "<br>When approaching ".$this->enemyProvince->provinceName." you notice a bunch of provinces fleeing from ".$this->enemyProvince->provinceName.". Maybe you should wait for a second or two and try attacking again. If you still get this message, you might wanna tell the Gods that something scary is happening in ".$this->enemyProvince->provinceName." in kingdom #".$this->enemyProvince->getkiID()."<br><br>";
				return false;
			}

			$this->provinceArray = null;
			$i = 0;		
			while($data = $this->dbRef->fetchArray()) {
			    $this->provinceArray[$i] = $data['pID'];
				$i++;
			}
			return true;
		}

		function getProvinceAndKingdom() {
			$i = 0;
			$getNamekID = "select Province.pID as pID, Province.provinceName as provinceName, Province.kiID as kiID, Kingdom.name as kingdomName from Province, Kingdom, Attack where Province.kiID=Kingdom.kiID and Province.pID=Attack.pID and (Province.pID=".$this->provinceArray[$i]." or Province.pID=".$this->provinceArray[$i+1]." or Province.pID=".$this->provinceArray[$i+2]." or Province.pID=".$this->provinceArray[$i+3]." or Province.pID=".$this->provinceArray[$i+4].")";
			
			//echo "<br>".$getNamekID."<br>";
			$this->dbRef->query($getNamekID);
			$this->kiPrArray = null;
			$i = 0;
			while($data = $this->dbRef->fetchArray()) {
				$this->kiPrArray[$i] = $data;
				$i++;
			}
		}

		function selectThreeAtRandom() {
			$this->pOne = mt_rand(0, 4);
			$this->pTwo = $this->pOne + 2;
			if($this->pTwo > 4) $this->pTwo -= 4;
			$this->pThree = $this->pTwo + 2;
			if($this->pThree > 4) $this->pThree -= 4;
		}

		function sameKingdomAll() {
			$retval = false;
			if( ($this->kiPrArray[$this->pOne]['kiID'] == $this->kiPrArray[$this->pTwo]['kiID']) && ($this->kiPrArray[$this->pTwo]['kiID'] == $this->kiPrArray[$this->pThree]['kiID']) ) {
				$this->kingdomNameAndID[0]['name'] = $this->kiPrArray[$this->pOne]['kingdomName'];
				$this->kingdomNameAndID[0]['id'] = $this->kiPrArray[$this->pOne]['kiID'];
				$retval = true;
			}
			return $retval;
		}

		function sameKingdomTwo() {
			$retval = false;
			if( ($this->kiPrArray[$this->pOne]['kiID'] == $this->kiPrArray[$this->pTwo]['kiID'])
			     || ($this->kiPrArray[$this->pTwo]['kiID'] == $this->kiPrArray[$this->pThree]['kiID']) 
			     || ($this->kiPrArray[$this->pOne]['kiID'] == $this->kiPrArray[$this->pThree]['kiID']) ) {
				
				
				if($this->kiPrArray[$this->pOne]['kiID'] == $this->kiPrArray[$this->pTwo]['kiID']) {
					//echo "This one <br>";
					$this->kingdomNameAndID[0]['name'] = $this->kiPrArray[$this->pOne]['kingdomName'];
					$this->kingdomNameAndID[0]['id'] = $this->kiPrArray[$this->pOne]['kiID'];
					$this->kingdomNameAndID[1]['name'] = $this->kiPrArray[$this->pThree]['kingdomName'];
    				$this->kingdomNameAndID[1]['id'] = $this->kiPrArray[$this->pThree]['kiID'];
				}
				else if($this->kiPrArray[$this->pTwo]['kiID'] == $this->kiPrArray[$this->pThree]['kiID']) {
//					echo "Or maybe second<br>";
					$this->kingdomNameAndID[0]['name'] = $this->kiPrArray[$this->pTwo]['kingdomName'];
					$this->kingdomNameAndID[0]['id'] = $this->kiPrArray[$this->pTwo]['kiID'];
					$this->kingdomNameAndID[1]['name'] = $this->kiPrArray[$this->pOne]['kingdomName'];
	   				$this->kingdomNameAndID[1]['id'] = $this->kiPrArray[$this->pOne]['kiID'];
				}
				else {
					$this->kingdomNameAndID[0]['name'] = $this->kiPrArray[$this->pOne]['kingdomName'];
					$this->kingdomNameAndID[0]['id'] = $this->kiPrArray[$this->pOne]['kiID'];
					$this->kingdomNameAndID[1]['name'] = $this->kiPrArray[$this->pTwo]['kingdomName'];
//					echo "WHY!=: ".$this->pTwo."h".$this->kiPrArray[$this->pTwo]['kingdomName'];;
	   				$this->kingdomNameAndID[1]['id'] = $this->kiPrArray[$this->pTwo]['kiID'];
				}
				$retval = true;
			}
			return $retval;
		}

		function sameKingdomNone() {
			$this->kingdomNameAndID[0]['name'] = $this->kiPrArray[$this->pTwo]['kingdomName'];
			$this->kingdomNameAndID[0]['id'] = $this->kiPrArray[$this->pTwo]['kiID'];
			$this->kingdomNameAndID[1]['name'] = $this->kiPrArray[$this->pOne]['kingdomName'];
			$this->kingdomNameAndID[1]['id'] = $this->kiPrArray[$this->pOne]['kiID'];
			$this->kingdomNameAndID[2]['name'] = $this->kiPrArray[$this->pThree]['kingdomName'];
			$this->kingdomNameAndID[2]['id'] = $this->kiPrArray[$this->pThree]['kiID'];
			return true;
		}

		function giveAwayOne($pOne) {
			$retval = "";
			if($this->kiPrArray[$pOne]['pID'] != $this->province->pID) {
					$retval = " Just before I ran back to you I noticed that one of the armies attacking is from the province of: ".$this->kiPrArray[$pOne]['provinceName']." in ".$this->kiPrArray[$pOne]['kingdomName']."(#".$this->kiPrArray[$pOne]['kiID'].")";
			}
			return $retval;
		}

		function giveAwayTwo($pOne, $pTwo) {
			$retval = "";
			if( ($this->province->pID == $this->kiPrArray[$pOne]['pID'])
				|| ($this->province->pID == $this->kiPrArray[$pTwo]['pID']) ) {

				if($this->province->pID == $this->kiPrArray[$pOne]['pID']) $tmp = $this->kiPrArray[$pTwo]['provinceName']." in ".$this->kiPrArray[$pTwo]['kingdomName']."(#".$this->kiPrArray[$pTwo]['kiID'].")";
				else $tmp = $this->kiPrArray[$pOne]['provinceName']." in ".$this->kiPrArray[$pOne]['kingdomName']."(#".$this->kiPrArray[$pOne]['kiID'].")";
				$retval = " Just before I ran back to you I noticed that one of the armies attacking is from the province of: ".$tmp;
			}
			else if($this->kiPrArray[$this->pOne]['pID'] == $this->kiPrArray[$this->pThree]['pID']) {
				$retval = " I managed to gather some information about two of the armies attacking, and I found that both of the armies are from the Province ".$this->kiPrArray[$this->pOne]['provinceName']." in the kingdom of: ".$this->kiPrArray[$this->pOne]['kingdomName']."(#".$this->kiPrArray[$this->pOne]['kiID'].")";
			}
			else {
				$retval = " I managed to gather some information about two of the armies attacking, and I found that one of the armies is from the Province ".$this->kiPrArray[$this->pOne]['provinceName']." in ".$this->kiPrArray[$this->pOne]['kingdomName']."(#".$this->kiPrArray[$this->pOne]['kiID'].")";
				$retval .= " and the other army is from the province of: ".$this->kiPrArray[$this->pThree]['provinceName']." in ".$this->kiPrArray[$this->pThree]['kingdomName']."(#".$this->kiPrArray[$this->pThree]['kiID'].")";
			}
			return $retval;
		}

		function giveAwayThree() {
			$retval = "";
			if( ($this->kiPrArray[$this->pOne]['pID'] != $this->province->pID)
				&& ($this->kiPrArray[$this->pTwo]['pID'] != $this->province->pID)
				&& ($this->kiPrArray[$this->pThree]['pID'] != $this->province->pID) ) {
				
				if( ($this->kiPrArray[$this->pOne]['pID'] != $this->kiPrArray[$this->pTwo]['pID'])
					&& ($this->kiPrArray[$this->pTwo]['pID'] != $this->kiPrArray[$this->pThree]['pID'])
					&& ($this->kiPrArray[$this->pOne]['pID'] != $this->kiPrArray[$this->pThree]['pID']) ) {
				
					$retval = " I managed to gather information about three of the armies, and on of them is from the province of: ".$this->kiPrArray[$this->pOne]['provinceName']." in ".$this->kiPrArray[$this->pOne]['kingdomName']."(#".$this->kiPrArray[$this->pOne]['kiID'].")";
					$retval = " and another is from the province of: ".$this->kiPrArray[$this->pTwo]['provinceName']." in ".$this->kiPrArray[$this->pTwo]['kingdomName']."(#".$this->kiPrArray[$this->pTwo]['kiID'].")";
					$retval = " and the last army I could find a province-banner for is the province of: ".$this->kiPrArray[$this->pThree]['provinceName']." in ".$this->kiPrArray[$this->pThree]['kingdomName']."(#".$this->kiPrArray[$this->pThree]['kiID'].")";
				}
				else if( ($this->kiPrArray[$this->pOne]['pID'] == $this->kiPrArray[$this->pTwo]['pID'])
						 && ($this->kiPrArray[$this->pOne]['pID'] != $this->kiPrArray[$this->pThree]['pID']) ){
					$retval = $this->giveAwayTwo($this->pOne, $this->pThree);
				}
				else if( ($this->kiPrArray[$this->pTwo]['pID'] == $this->kiPrArray[$this->pThree]['pID'])
						 && ($this->kiPrArray[$this->pOne]['pID'] != $this->kiPrArray[$this->pTwo]['pID']) ){
					$retval = $this->giveAwayTwo($this->pOne, $this->pTwo);
				}
				else if( ($this->kiPrArray[$this->pOne]['pID'] == $this->kiPrArray[$this->pThree]['pID'])
						 && ($this->kiPrArray[$this->pOne]['pID'] != $this->kiPrArray[$this->pTwo]['pID']) ){
					$retval = $this->giveAwayTwo($this->pTwo, $this->pThree);
				}
			}
			return $retval;
		}

		function generateExtraInformation() {
			$retval = "";
			$infoID = mt_rand(1, 50);
			if(array_search($infoID, $this->oneProvince)) {
				$retval = $this->giveAwayOne($this->pOne);
			}
			else if(array_search($infoID, $this->twoProvince)) {
				$retval = $this->giveAwayTwo($this->pOne, $this->pThree);				
			}
			else if(array_search($infoID, $this->threeProvince)) {
				$retval = $this->giveAwayThree();
				//give away three provinces
			}
			return $retval;
		}


		function makeMessage() {
			$message = "<center><table width='40%'><tr><td>";
			if(!$this->getProvincesAttacking()) return $this->lastError;
			$this->getProvinceAndKingdom();
			$this->selectThreeAtRandom();

//					echo "<pre>";
//					print_r(array_values($this->kiPrArray)); echo "</pre>";
			$kingdomMessage = "";
			if($this->sameKingdomAll()) {
				if($this->province->kiId == $this->kingdomNameAndID[0]['id']) {
					$kingdomMessage = " And as far as I can see, 3 of those armies are from our kingdom. Isn't it time we let this province alone for a while?";
				}
				else {
					$kingdomMessage=" And From the kingdom banners I gathered that three of the armies are from the same kingdom. The name of the kingdom is: ";
					$kingdomMessage.= $this->kingdomNameAndID[0]['name']."(#".$this->kingdomNameAndID[0]['id'].").";
				}
			}
			else if($this->sameKingdomTwo()) {
				if( ($this->province->kiId == $this->kingdomNameAndID[0]['id']) 
					|| ($this->province->kiId == $this->kingdomNameAndID[1]['id']) ) {

					if($this->province->kiId == $this->kingdomNameAndID[0]['id']) {
						$kingdomMessage = " And two of the armies are from our kingdom, and a third army from the kingdom of: ".$this->kingdomNameAndID[1]['name']."(#".$this->kingdomNameAndID[1]['id']."). Maybe we should leave this province alone for the time being?";
					}
					else {
						$kingdomMessage = " And two of the armies are from the kingdom of: ".$this->kingdomNameAndID[0]['name']."(#".$this->kingdomNameAndID[0]['id'].") and a third army thats from our kingdom.";
					}
				}
				else {
					$kingdomMessage = " And two of the armies are from the kingdom of: ".$this->kingdomNameAndID[0]['name']."(#".$this->kingdomNameAndID[0]['id'].") and a third army thats from the kingdom of: ".$this->kingdomNameAndID[1]['name']."(#".$this->kingdomNameAndID[1]['id'].").";
				}
			}
			else if($this->sameKingdomNone()) {
				$kingdomMessage=" And there are at least three different kingdoms attacking ".$this->enemyProvince->provinceName.". I guess ".$this->enemyProvince->provinceName." ";
				$kingdomMessage.= "is good at making enemies. The kingdom banners I could see was the banners of ".$this->kingdomNameAndID[0]['name']."(#".$this->kingdomNameAndID[0]['id']."), ".$this->kingdomNameAndID[1]['name']."(#".$this->kingdomNameAndID[1]['id'].") and ".$this->kingdomNameAndID[2]['name']."(#".$this->kingdomNameAndID[2]['id'].")";
			}

			$message .= "<br><b>As you approach ".$this->enemyProvince->provinceName;
			$message .= " one of your scouts come running to you. You ask your scout whats wrong, and he tells you:</b> <br><br>";
			$message .= "<i>'There are allready at least 5 different armies attacking ".$this->enemyProvince->provinceName.". So there is no way for us to attack ".$this->enemyProvince->provinceName." now.";
			$message .=  $kingdomMessage;
			$message .= $this->generateExtraInformation();
			$message .="'.</i>";
			return $message."<br><br></td></tr></table></center><br><br>";
		}
	}

?>