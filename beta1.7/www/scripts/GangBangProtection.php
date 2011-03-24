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

			$this->threeProvince = array(0, 25, 51);
			$this->twoProvince = array(0, 12, 36, 51);
			$this->oneProvince = array(0, 6, 18, 31, 39, 51);

			$this->dbRef = $dbRef;
			$this->province = $pObj;
		}

		function setEnemy($pObj) {
			$this->enemyProvince = $pObj;
		}

		function triggered() {
			$retval = true;
			if(!is_null($this->enemyProvince)) {
				if($this->enemyProvince->attackNum >= $this->gangbang) {
					$retval = true;
				}
				else {
					$retval = false;
				}
			}
			else {
				$retval = false;
				$this->lastError = "No 'enemyProvince' please 'setEnemy' before 'triggered'";
			}
			$return $retval;
		}

		function addAttack() {
			$this->enemyProvince->incAttackNum();
		}

		function getProvincesAttacking() {
			$getProvinces = "select pID from Attack where targetID=".$this->enemyProvince->pID;
			$this->dbRef->query($getProvinces);
			
			//GB prot ONLY if more than 5 attacks... so this shall NOT happen.. can happend in a tick change...
			if($this->dbRef->numRows() < 5) {
				$this->lastError = "When approaching ".$this->enemyProvince->provinceName." you notice a bunch of provinces fleeing from ".$this->enemyProvince->provinceName.". Maybe you should wait for a second or two and try attacking again. If you still see provinces fleeing, you might wanna tell the Gods that something scary is happening in ".$this->enemyProvince->provinceName." in kingdom #".$this->enemyProvince->getkiID();
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
			$getNamekID = "select Province.pID as pID, Province.provinceName as provinceName, Province.kiID as kiID, Kingdom.name as kingdomName from Province, Kingdom where Province.kiID=Kingdom.kiID and (Province.pID=".$this->provinceArray[$i]." or Province.pID=".$this->provinceArray[$i+1]." or Province.pID=".$this->provinceArray[$i+2]." or Province.pID=".$this->provinceArray[$i+3]." or Province.pID=".$this->provinceArray[$i+4].")";						 
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
			if( ($this->kiPrArray[$this->pOne]['kiID'] == $this->kiPrArray[$this->pTwo]['kiID']) && ($this->kiPrArray[$this->pTwo]['kiID'] == $this->kiPrArray[$this->pThree]['kiID']) ) $retval = true;
			return $retval;
		}

		function sameKingdomTwo() {
			$retval = false;
			if( ($this->kiPrArray[$this->pOne]['kiID'] == $this->kiPrArray[$this->pTwo]['kiID']) || ($this->kiPrArray[$this->pTwo]['kiID'] == $this->kiPrArray[$this->pThree]['kiID']) || ($this->kiPrArray[$this->pOne]['kiID'] == $this->kiPrArray[$this->pThree]['kiID']) ) $retval = true;
			return $retval;
		}

		function generateExtraInformation() {
			$retval = "";
			$infoID = mt_rand(1, 50);
			if(array_search($infoID, $this->oneProvince)) {
				//give away one province
				$retval = " One of the provinces attacking is: ".$this->kiPrArray[$this->pOne]['provinceName']." in ".$this->kiPrArray[$this->pOne]['kingdomName']."(#".$this->kiPrArray[$this->pOne]['kiID'].")";
			}
			else if(array_search($infoID, $this->oneProvince)) {
				$retval = " I managed to gather more information about two of the provinces attacking, and I found out one of the is ".$this->kiPrArray[$this->pOne]['provinceName']." in ".$this->kiPrArray[$this->pOne]['kingdomName']."(#".$this->kiPrArray[$this->pOne]['kiID'].")";
				$retval .= " and the other is: ".$this->kiPrArray[$this->pThree]['provinceName']." in ".$this->kiPrArray[$this->pThree]['kingdomName']."(#".$this->kiPrArray[$this->pThree]['kiID'].")";
				//give away two provinces
			}
			else if(array_search($infoID, $this->oneProvince)) {
				$retval = " Some provinces even have province banners and from those banners I got that one of the provinces attackin is: ".$this->kiPrArray[$this->pOne]['provinceName']." in ".$this->kiPrArray[$this->pOne]['kingdomName']."(#".$this->kiPrArray[$this->pOne]['kiID'].")";
				$retval = " and another is ".$this->kiPrArray[$this->pTwo]['provinceName']." in ".$this->kiPrArray[$this->pTwo]['kingdomName']."(#".$this->kiPrArray[$this->pTwo]['kiID'].")";
				$retval = " and the last one I could see is: ".$this->kiPrArray[$this->pThree]['provinceName']." in ".$this->kiPrArray[$this->pThree]['kingdomName']."(#".$this->kiPrArray[$this->pThree]['kiID'].")";
				//give away three provinces
			}
			return $retval;
		}


		function makeMessage() {
			$message = "";
			if(!$this->getProvincesAttacking()) return $this->lastError;
			$this->getProvinceAndKingdom();
			$this->getThreeAtRandom();

			$kingdomMessage = "";
			if($this->sameKingdomAll()) {
				$kingdomMessage="From the kingdom banners I gathered that three provinces are from the same kingdom. The name of the kingdom is: ";
				$kingdomMessage.= $this->kindomNameAndID[0]['name']."(#".$this->kindomNameAndID[0]['id'].")";
			}
			else if($this->sameKingdomTwo()) {
				$kingdomMessage="As far as I could see, two of the provinces attacking ".$this->enemyProvince->provinceName." was from the kingdom of ";
				$kingdomMessage.= $this->kindomNameAndID[0]['name']."(#".$this->kindomNameAndID[0]['id'].")";
				$kingdomMessage.= " and one of the provinces attacking is from the kingdom of ".$this->kindomNameAndID[1]['name']."(#".$this->kindomNameAndID[1]['id'].")";
			}
			else {
				$kingdomMessage="There are at least three different kingdoms attacking ".$this->enemyProvince->provinceName.". I guess ".$this->enemyProvince->provinceName." ";
				$kingdomMessage.= "is good at making enemies. The kingdom banners I could see was the banners of ".$this->kindomNameAndID[0]['name']."(#".$this->kindomNameAndID[0]['id']."), ".$this->kindomNameAndID[1]['name']."(#".$this->kindomNameAndID[1]['id'].") and ".$this->kindomNameAndID[2]['name']."(#".$this->kindomNameAndID[2]['id'].")";
			}

			$message = "<br><br>As you approach ".$this->enemyProvince->provinceName.";
			$message .= " one of your scouts come running to you. You ask your scout whats wrong, and he tells you: ";
			$message .= "'There are allready at least 5 provinces attacking ".$this->enemyProvince->provinceName.". There is no way for us to attack ".$this->enemyProvince->provinceName." now.";
			$message .= "I am really sorry but I had to flee fast just to don't get caught.. but I still managed to gather this information: ";
			$message .=  $kingdomMessage.".";
			$message .= $this->generateExtraInformation();
			$message .="'.";
			return $message;
		}
	}

?>