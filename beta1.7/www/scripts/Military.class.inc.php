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

	/**
	* Military class v2
	***
	* author:	Jørgen Belsaas
	* This is a major update, with a lot of internals updated.
	* The important update for use is: now takes Province OBJECT instead of pID
	***
	* Changelog:
	* 27.10.2003 - Jørgen - Adde to functions (startActionMessage & endActionMessage) to help with output
							on retrain and banish.
	* 26.10.2004 - Jørgen - Made code in showOwnMilitaryBlock, writeMilitaryUnit, and trainMilitary more readable
	*					  - Removed the anoying slash that pops up when training military....
	*					  - Corrected some spelling errors
	* 18.10.2004 - Jørgen - The whole system crashed because NO ONE a pObj when they initialize this object.
	*						speed gains will be canceld out when using pID. 
	*						Added support for BOTH pID and pObj.
	*						Cleaned up some old code that posted some warnings
	* 13.10.2004 - Jørgen - Updated from older military class... to see old changelog see backup!!!
	*/
	if(!class_exists("Military")) {
		$GLOBALS['militray_static_data_set'] = false;
		$GLOBALS['military_static_data_types'] = false;
		$GLOBALS['military_static_data_types2'] = false;

		/**
		* Require all needed includefiles and classes.
		* 
		*/
		require_once("globals.inc.php");
		require_once($GLOBALS['path_www_scripts']."Science.class.inc.php");
 		require_once($GLOBALS['path_www_scripts']."Buildings.class.inc.php");
		require_once($GLOBALS['path_www_scripts']."military/MilitaryConstants.class.inc.php");
		require_once($GLOBALS['path_www_scripts']."Effect.class.inc.php");
		require_once($GLOBALS['path_www_scripts']."Misc.class.inc.php");
		require_once($GLOBALS['path_www_scripts']."Province.class.inc.php");

		/**
		* Military.class
		* Handles military units training/killing/banishing
		* Each military unit has its own class
		* This class is just to manage military.
		*/
		class Military {
			var $debugdata = "";
			var $pID;
			var $province = NULL;
			var $database = NULL;
			var $effect = NULL;
			var $militaryConstants = NULL;
			var $MilitaryConst = NULL;
			var $misc = NULL;
			var $science = NULL;
			var $loadtime = NULL;
			var $reduseCost = 0;
			var $reduseTrTime = 0;
			var $justtrained = false;

			var $buildings = NULL;


			//military
			var $militaryTypesArray = NULL;
			var $militaryTypesArray2 = NULL;
			var $militaryAll = NULL;
			var $militaryHome = NULL;
			var $militaryWar = NULL;
			var $militaryTraining = NULL;
			var $militaryInProgress = NULL;
			var $militaryNoTrain = NULL;
			var $Army = NULL;
			
			/**
			* Military($dbRef, $prRef)
			***
			* $dbRef , a refrence to the databaseobject that is going to be used
			* $prRef , a refrence to the provinceobject this militaryobject belongs to
			***
			* The constructur initializes the object
			*/
			function Military(&$dbRef, $prRef=NULL, $debugmode=false) {
				$this->debugmode = $debugmode;
				$this->debugmode = false;
				if($this->debugmode == true) {
					$startTime = clock();
					//echo "StartTime: $startTime<br>";
				}

				$this->database = $dbRef;
				$this->militaryConstants = $GLOBALS['MilitaryConst'];
				$this->MilitaryConst = $GLOBALS['MilitaryConst'];

				//Not all scripts use province object.. haveto check :( FOR BACKWARD COMPABILITY
				if(!is_null($prRef)) { 
					if(is_object($prRef)) {
						$this->province = $prRef;
					}
					else $this->province = new Province($prRef, $dbRef);
									
					             //FOR BACKWARD COMPABILITY!!!!!!

					if(!is_null($this->province)) {
						$this->initializeObject();
					}
					//$this->province->updateMilitaryExperience($this->province->getpID());

					if($this->debugmode == true) {
						$endTime = clock();
						$loadTime = $endTime - $startTime;
						$this->debugdata .= "Militaryclass loaded in $loadTime<br>\n";
						$this->loadtime = $loadTime;
					}
				}
				//if($this->debugmode == true) echo "MilitaryClass Loaded In $loadTime<br>";
			}

			/**
			* initializeObject
			***
			* Initializes object, loads militarytypes and military.
			* if there is no military for the province.. the province will get military according to his race loaded and stored.
			*/
			function initializeObject() {
				$this->province->getProvinceData();
				$this->pID = $this->province->pID;
				$this->science = new Science($this->database, $this->province->pID);
				$this->misc = new Misc();
				$this->getMilitaryTypes(); //loads some static data..

				if($this->noMilitary()) { //when creating object ONLY create military when there are no military!! (i.e. when no lines in MILITARY TABLE)
					if(!$this->initializeNewMilitary()) {
						if($this->debugmode == true) $this->debugdata .= "initializeObject: Fatal error, could NOT initialize new military";
						return false;
					}
				}
				$this->militaryAll = $this->getMilitary();
				$this->militaryHome = $this->getMilitaryHome();
				$this->militaryWar = $this->getMilitaryOut();
			}

			/**
			* noMilitary()
			***
			* returns:
			*   false - if there is no militaryrows for current province.....
			*   true  - if there is militaryrows for current province
			*/
			function noMilitary() {
				$retval = true;
				$sqlSelectMilitary = "select mID from Military where pID=".$this->province->pID;
				if($this->debugmode == true) {
					$this->debugdata .= "noMilitary: sql-query: ".$sqlSelectMilitary."<br>\n";
				}
				$this->database->query($sqlSelectMilitary);
				//echo "nr: ".$this->database->numRows()." should yield "
				if($this->database->numRows() > 0) $retval = false;
				return $retval;
			}

			/**
			* initializeNewMilitary()
			***
			* Initializes new military, and initializes the users/province militarytable
			***
			* returns:
			*    false - if an error
			*    true  - if no error
			*/
			function initializeNewMilitary() {
				if( (is_null($this->militaryTypesArray)) || ($GLOBALS['militray_static_data_set'] == false)) {
					$this->getMilitaryTypes();
					if($this->debugmode == true) {
						$this->debugdata .= "initializeNewMilitary: sql-query: ".$sqlGetMilitary."<br>\n";
					}
				}
				
				if (is_array ($this->militaryTypesArray)) {
					foreach($this->militaryTypesArray as $militaryType) {
						//TODO:
						//
						//Sjekk om raser stemmer overens i provins å militartype
						//Hvis stemmer overens... se om denne provins har denne typen i sin militærtabell.. hvis ikke.. legg inn
						//require_once("Province.class.inc.php");
						//$province = new Province($this->pID, $this->dbRef);
						//$province->getProvinceData();
						$milObj = $militaryType['obj'];
						//if(is_object($milObj)) echo "So far so good, finding Object<br>";
						if( strcasecmp($milObj->raceRequirements(), $this->province->race) == 0 ) {
							$mID = $milObj->getID();
							$getMil = "select * from Military where pID=".$this->province->pID." and mID=$mID";
							$this->database->query($getMil);
							if($this->database->numRows() < 1) {
	
								$insertSql = "insert into Military (pID, mID, num) values (".$this->province->pID.", $mID, 0)";
								$this->database->query($insertSql);
							}
						}
					}
				}
				return true;
			}

			/**
			* getMilitaryTypes
			***
			* gets ALL militarytypes associated with real military units and stores them in an 'static' global array
			***
			* returns:
			*    an array of the military types this province race got (associated with trhe correct militaryunit
			*/
			function getMilitaryTypes() {
				$globalArray = NULL;

				if($GLOBALS['militray_static_data_set'] == false) {
					$sqlGetMilitary = "select * from MilitaryT";
					if($this->debugmode == true) {
						$this->debugdata .= "getMilitaryTypes: sql-query: ".$sqlGetMilitary."<br>\n";
					}
					$militaryTypes = $this->database->query($sqlGetMilitary);
					while($data = $this->database->fetchArray($militaryTypes)) {
						$tmpArray = NULL;
						$mID=$data['mID'];
						if(!class_exists($data['className'])) {
							$path = $this->militaryConstants->PATH_TO_MILITARY;
							require_once($path.$data['className'].".class.inc.php");
						}
						$milObj = new $data['className']($mID);
						$tmpArray = array("obj"=>$milObj, "className"=>($data['className']));
						$globalArray[$milObj->getID()] = $tmpArray;
						if( strcasecmp($milObj->raceRequirements(), $this->province->race) == 0 ) {
							$militaryTypesArray[$milObj->getMilType()] = $tmpArray; 
							$militaryTypesArray2[$milObj->getID()] = $tmpArray;
						}
					}
					if (isset($militaryTypesArray2)) $this->militaryTypesArray2 = $militaryTypesArray2;
					if (isset($militaryTypesArray)) $this->militaryTypesArray = $militaryTypesArray;
					$GLOBALS['military_static_data_types'] = $globalArray;
					$GLOBALS['militray_static_data_set'] = true;
				}
				else if(is_null($this->militaryTypesArray)){
					$globalArray = $GLOBALS['military_static_data_types'];
					foreach($globalArray as $milArray){
						$milObj = $milArray['obj'];
						if( strcasecmp($milObj->raceRequirements(), $this->province->race) == 0 ) {
							if($this->debugmode == true) {
								$this->debugdata .= "getMilitaryTypes: className - ".$milArray['className']."<br>\n";
							}
							$militaryTypesArray[$milObj->getMilType()] = $milArray; //new $data['className']($mID);
							$militaryTypesArray2[$milObj->getID()] = $milArray; //new $data['className']($mID);
						}
					}
					if (isset($militaryTypesArray2)) $this->militaryTypesArray2 = $militaryTypesArray2;
					if (isset($militaryTypesArray)) $this->militaryTypesArray = $militaryTypesArray;
				}
				return $this->militaryTypesArray;
			}

			/**
			* getMilitary
			***
			* gets all military (both home and away)
			***
			* returns:
			*    an array with the military and the total number.
			*/
			function getMilitary($force=false) {
				if($GLOBALS['militray_static_data_set'] == false) $this->getMilitaryTypes();
				$retval = NULL;
				if(is_null($this->militaryAll) || ($force==true)) {
					$tmpMilitary = NULL;
					$sqlGetMilitary = "select * from Military where pID=".$this->province->pID;
					$allMilitary = $this->database->query($sqlGetMilitary);
					$path = $this->militaryConstants->PATH_TO_MILITARY;
					$deb = 0;
					while($data = $this->database->fetchArray($allMilitary)) {
						$mID = $data['mID'];
						$className = $this->militaryTypesArray2[$mID]['className'];
						if($this->debugmode == true) { 
							$this->debugdata .= "getMilitary: className: ".$className."<br>\n";
						}

						//if(!class_exists($datClassName['className'])) {
						//	require_once($path.$className.".class.inc.php");
						//}
						$militaryObject = $this->militaryTypesArray2[$mID]['obj'];
						if($this->requirementsOK($militaryObject)) {
							$deb++;
							$tmpArray = $data;
							$tmpArray['object'] = $militaryObject;
							$tmpMilitary[$militaryObject->getMilType()] = $tmpArray; //array( "num" =>$num, "object" =>$militaryObject);
						}
					}
					$this->militaryAll = $tmpMilitary;
				}
				$retval = $this->militaryAll;
				return $retval;
			}

			/**
			* getMilitaryHome
			***
			* gets number of military that are left when military in training and war is substracted
			***
			* returns:
			*    an array with the military and the number of military home.
			*/
			function getMilitaryHome($force=false) {
				if(( $force == true) || (is_null($this->militaryHome)) ) {
					if(is_null($this->militaryAll)) $this->getMilitary($force);
					$allMil = $this->militaryAll;
				
					if(is_null($this->militaryWar)) $this->getMilitaryOut($force);
					$milInWar = $this->militaryWar;

					if(is_null($this->militaryTraining)) $this->getMilitaryInTraining($force);
					$milInTr = $this->militaryTraining;

					$retval = $allMil;
					if (is_array ($allMil)) {
						foreach($allMil as $milarry) {
							$obj = $milarry['object'];
							$type = $obj->getMilType();
		
							if(isset($milInTr[$type])) $milInTrNum = $milInTr[$type];
							else $milInTrNum = 0;
							if(isset($milInWar[$type]['num'])) $milInWarNum = $milInWar[$type]['num'];
							else $milInWarNum = 0;
	
							$retval[$type]['num'] = ($milarry['num'] - $milInTrNum - $milInWarNum);
						}
					}
					$this->militaryHome = $retval;

				}
				return $this->militaryHome;
			}

			/**
			* getMilitaryOut
			***
			* gets all military out in war
			***
			* returns:
			*    an array with the military and the total number of military out in war.
			*/
			function getMilitaryOut($force=false) {
				if( ($force == true) || (is_null($this->militaryWar)) ) {
					if(is_null($this->militaryTypesArray2)) $this->getMilitaryTypes();
					$sqlGetMil = "select mID, sum(num) as num from Army where pID=".$this->province->pID." group by mID order by mID";
					$milOut = $this->database->query($sqlGetMil);
					$tmpMilitary = NULL;
					while($data = $this->database->fetchArray()) {
						$mID = $data['mID'];
						$path = $this->militaryConstants->PATH_TO_MILITARY;
						//$className = $this->militaryTypesArray2[$mID]['className'];
						//require_once($className.".class.inc.php");
						//$militaryObject
						$tmpArray = $data;
						$militaryObject = $tmpArray['object'] = $this->militaryTypesArray2[$mID]['obj'];
						$tmpMilitary[$militaryObject->getMilType()] = $tmpArray;
					}
					$this->militaryWar = $tmpMilitary;
				}
				return $this->militaryWar;
			}

			
			/**
			* getMilitaryInTraining
			***
			* gets the sum of each militarytype in training
			***
			* returns:
			*    an array the num in training of each military.
			*/
			function getMilitaryInTraining($force=false) {
				if( (is_null($this->militaryTraining)) || ($force == true) ) {
					$sqlGetMilitary = "select sum(num) as num, mID from ProgressMil where pID=".$this->province->pID." group by mID order by mID asc";
					$this->database->query($sqlGetMilitary);
					$military2 = NULL;
					$retArray = NULL;
					$deb= 0;
					while($data = $this->database->fetchArray()) {
						$deb++;
						$mID = $data['mID'];
						$military2[$mID] = $data;
					}
					
					if (is_array ($this->militaryTypesArray)) {
						foreach($this->militaryTypesArray as $milType) {
							$milObj = $milType['obj'];
							$mID = $milObj->getID();
							if(isset($military2[$mID]['num'])) $miliNum = $military2[$mID]['num'];
							else $miliNum = 0;
							$retArray[$milObj->getMilType()] = $miliNum;
						}
					}
					$this->militaryTraining = $retArray;
				}
				return $this->militaryTraining;
			}

			/**
			* requirementsOK
			***
			* checks if theis province has the right science requirement for given militarytype
			***
			* returns:
			*    an array the num in training of each military.
			*/
			function requirementsOK($milObj) {
				$result = true;
				$sciReqArr = $milObj->scienceRequirements();
				if( !$this->science->scienceReqOk( $sciReqArr ) ) {
					$result = false;
				}

				return $result;
			}
			
			function getLoadTime() {
				return $this->loadtime;
			}

			/**
			* trainMilitary
			***
			* handels the main militarypage where people can train/banish military
			**
			* returns a html formated output to use when displaying page
			*/
			function trainMilitary() {
				$output = "";
				
				$this->effect = new Effect($this->database);

				$output .= "<center>".$this->province->getShortTitle().", this is where you can keep track of the military you've got.<br>Both your own military and military you have gotten under your command by other means.<br><br>In this room you're also able to draft, retrain and banish military.<br><br>Your soldiers Morale is: ".($this->province->morale)."%<br>";
				
				//Handle input
				$output .= $this->handleTrainedMilitary();
				$output .= $this->handleRetrainedMilitary();
				$output .= $this->handleBanishedMilitary();
				//show ordinary output

				$output .= $this->showMilitary();
				$output .= $this->showReTrain();
				$output .= $this->showBanish();

				$output .= $this->showInTraining();
								

				return $output;

			}

			/**
			* handleTrainedMilitary
			***
			* handels training of military units
			***
			* returns: html formatted output
			*/
			function handleTrainedMilitary() {
				$output = "";
				$data = $this->parseTrainInput();
				$output .= $this->handleParsedTrainedMilitary($data);
				return $output;
			}

			/**
			* handleRetrainedMilitary
			***
			* handels military sent to retrain as recruits
			***
			* returns html formatted output
			*/
			function handleRetrainedMilitary() {
				$output = "";
				if(isset($_POST['drpMilOK'])) {
					$milType = $_POST['drpMilitary'];
					$milNum = $_POST['drpNum'];
					$milHome = $this->militaryHome;
					$gotNum = $milHome[$milType]['num'];
					$province = $this->province;
					$milObjtr = $milHome[$this->militaryConstants->SOLDIERS]['object'];
					$milObj = $milHome[$milType]['object'];
					$output .= $this->startActionInformationMessage();
					if( ($gotNum >= $milNum) && ($milNum > 0)) {
						$this->killUnits($milType, $milNum, false);
						$this->insertTrain($milObjtr, $milNum, $milObjtr->getTicks());

						$output .= "<br>".$province->getAdvisorName().", $milNum ".$milObj->getName()." are now retraining to become ".$milObjtr->getName()." once more";

					}
					else {
						$output .= $province->getShortTitle()." you cannot retrain more military than you actually got!";
					}
					$output .= $this->endActionInformationMessage();
				}
				return $output;
			}

			/**
			* handleBanishedMilitary
			***
			* handels military banished from province for forever (and ever)
			***
			* returns html formatted output
			*/
			function handleBanishedMilitary() {
				$output = "";
				if(isset($_POST['banMilOK'])) {
					$milType = $_POST['banMilitary'];
					$milNum = $_POST['banNum'];
					$milHome = $this->militaryHome;
					$gotNum = $milHome[$milType]['num'];
					$province = $this->province;
					$output .= $this->startActionInformationMessage();
					if(($gotNum >= $milNum) && ($milNum > 0) && (is_numeric($milNum)) ) {
						$milObj = $milHome[$milType]['object'];
						$newNum = $gotNum-$milNum;
						$sqlNew = "update Military set num=num-$milNum where mID=".$milObj->getID()." and pID=".$this->province->pID;
						$this->database->query($sqlNew);

						$newGold = floor(($milObj->getCostGold() * (mt_rand($this->militaryConstants->BAN_RES_MIN, $this->militaryConstants->BAN_RES_MAX) / 100) )* $milNum);
						$newMetal = floor(($milObj->getCostMetal() * (mt_rand($this->militaryConstants->BAN_RES_MIN, $this->militaryConstants->BAN_RES_MAX) / 100 ) ) * $milNum);
						$newFood = floor(($milObj->getCostFood() * (mt_rand($this->militaryConstants->BAN_RES_MIN, $this->militaryConstants->BAN_RES_MAX) / 100 ) ) * $milNum);

						$output .= "<br>".$province->getAdvisorName().", you have banished $milNum ".$milObj->getName()." from your province, luckily you took my advise and searched the ";
						if($milType == $this->militaryConstants->WIZARDS) {
							$output .= "wizard-towers ";
						}
						else if($milType == $this->militaryConstants->THIEVES) {
							$output .= "inns ";
						}
						else {
							$output .= "barracks ";
						}
						$province->gainResource($newGold, $newMetal, $newFood);
						$output .= "and you managed to gather these resources:<br>";
						$output .= $newGold." gold coins <br>";
						$output .= $newMetal." kg of metal and <br>";
						$output .= $newFood." kg of food";
					}
					else {
						$output .= $province->getShortTitle()." you cannot banish more military than you actually got!";
					}
				$output .= $this->endActionInformationMessage();
				}
				return $output;
			}

			/**
			* showMilitary
			***
			* shows a table over current militaryresources
			***
			* returns html formatted table(s)
			*/
			function showMilitary() {
				$output = "";

				if($this->justtrained == true) {
					//$this->militaryAll = NULL;
					//$this->militaryHome = NULL;
					//$this->militaryWar = NULL;
					//$this->militaryAll = $this->getMilitary();
					//$this->militaryHome = $this->getMilitaryHome();
					//$this->militaryWar = $this->getMilitaryOut();
				}
				$output .= $this->showOwnMilitaryBlock();
				$output .= $this->showMagickMilitary();
				$output .= $this->showTotalAtmDefense();
							
				return $output;
			}

			/**
			*
			***
			*
			*/
			function showReTrain() {
				$output = "";
				
				$output .= "<br><table width='70%' border='0'><tr><td><center>";
				
				$milObj = $this->militaryHome[$this->militaryConstants->SOLDIERS]['object'];
				
				$output .= "\n".$this->province->getShortTitle().", you might want to retrain your military, and this is the right place to perform such a task. I just want to remind you - to retrain military, they first have to redo recruit school and become ".$milObj->getName()." once more.<br>";
				//$output .= "\n".$this->province->getShortTitle().", you might want to retrain your military, and this is the right place to performe such a task. I just want to remind you - to retrain military, they first have to redo recruite school and become  once more.<br>";

				$output .= "\n<form name='dropMilitary' class='form' action='".$this->self()."' method='post'>";
				$output .= $GLOBALS['fcid_post'];
				$output .= "\n\t<select name='drpMilitary' class='form'>";

				foreach($this->militaryHome as $milHomeArr) {
					$milObj = $milHomeArr['object'];
					if( ( $milObj->getMilType() != $this->militaryConstants->SOLDIERS ) && ( $this->requirementsOK($milObj) ) ) {
						$output .= "\n\t\t<option value='".$milObj->getMilType()."'>".$milObj->getName()."</option>";
					}
				}
				
				$output .= "\n\t</select>\n\t&nbsp;<input type='text' name='drpNum' class='form' size='6'>\n\t&nbsp;<input type='submit' name='drpMilOK' value='Retrain' class='form'>\n</form></center></td></tr></table>";

				return $output;
			}

			/**
			*
			***
			*
			*/
			function showBanish() {
				$output = "";
				$output .= "<br><table width='70%' border='0'><tr><td><center>";
//				$milObj = $this->militaryHome[$this->militaryConstants]['object'];
				
				$output .= "\n".$this->province->getShortTitle().", I don't know if this is a wise thing to do, but if you really think this is the only way, this is the place to banish military from your province. My advise is to search barracks, wizard-towers and inns to see if you can find any resources left behind by your banished military ";

				$output .= "\n<form name='banishMilitary' class='form' action='".$this->self()."' method='post'>\n\t<select name='banMilitary' class='form'>";

				foreach($this->militaryHome as $milHomeArr) {
					$milObj = $milHomeArr['object'];
					if($this->requirementsOK($milObj)) {
						$output .= "\n\t\t<option value='".$milObj->getMilType()."'>".$milObj->getName()."</option>";
					}
				}

				$output .= "\n\t</select>\n\t&nbsp;<input type='text' name='banNum' class='form' size='6'>\n\t&nbsp;<input type='submit' name='banMilOK' value='Banish' class='form'>\n</form></center></td></tr></table>";
				return $output;
			}

			/**
			*
			***
			*
			*/
			function showInTraining() {
				$output = "";
				
				$table = NULL;

				$output .= "<br><br><center><img src='../img/hor_ruler.gif'></center><br><br>";
				$output .= "<br><center><h1>Military in training</h1><br>";

				$this->getMilitaryInProgress();
				$table = $this->parseMilitaryInProgress();
				$output .= $this->showMilitaryInProgress($table);

				$output .= "</center><br><br><br>";


				return $output;
			}

			/**
			* showOwnMilitaryBlock
			***
			* shows information about current military resources
			***
			* returns html formatted table
			*/
			function showOwnMilitaryBlock() {
			
				$this->reduseCost = $this->addMilCost();
				$this->reduseTrTime = $this->addMilTime();
				$output = "";

				$output .= "";
				$output .= "
							<center>
							<form class='form' action='".$this->self()."' method='post' name='trnMil'>";
				$output .= $GLOBALS['fcid_post'];
				$output .= "		<table width='95%' class='buildingsTable'>
								<tr>
									<td class='buildings' width='10%'>Name</td>
									<td class='buildings' width='5%'>Home</td>
									<td class='buildings' width='4%'>In training</td>
									<td class='buildings' width='4'>At war</td>
									<td class='buildings' width='5%'>Total</td>
									<td class='buildings' width='6%'>Days to train</td>
									<td class='buildings' width='10%'>Train from</td>
									<td class='buildings' width='19%'>Cost</td>
									<td class='buildings' width='10%'>Num to train</td>
									<td class='buildings' width='23%'>Total Cost</td>
								</tr>";

				

				foreach($this->militaryAll as $militaryRow) {
					$milObj = $militaryRow['object'];
					$milNum = $militaryRow['num'];
					if($this->requirementsOK($milObj)) $output .= $this->writeMilitaryUnit($milObj, $milNum);
				}
							

				$output .= "
							</table>
							<br>
							<input type='submit' name='trainMilitary' class='form' value='Train' title='Click to train military'>
							</form>";

				return $output;
			}

			/**
			* showMagickMilitary
			***
			* shows information about military drafted by dark arts and other magick ;)
			***
			* returns html formatted table
			*/
			function showMagickMilitary() {
				$output = "";
				return $output;
			}

			/**
			* showTotalAtmDefense
			***
			* shows information about current defense.. includes magick military + ordinar own military + help from others
			***
			* returns html formatted table
			*/
			function showTotalAtmDefense() {
				$output = "";
				return $output;
			}

			/**
			* addMilCost
			***
			* calculates additional Militarycost
			***
			* returns html formatted table
			*/
			function addMilCost() {
				$milCost['gold'] = $this->effect->getEffect($this->militaryConstants->ADD_MILITARY_GOLD_COST, $this->province->pID);
				$milCost['metal'] = $this->effect->getEffect($this->militaryConstants->ADD_MILITARY_METAL_COST, $this->province->pID);
				$milCost['food'] = $this->effect->getEffect($this->militaryConstants->ADD_MILITARY_FOOD_COST, $this->province->pID);
				return $milCost;
			}
			
			/**
			* addMilTime
			***
			* calculates additional trainingtime
			***
			* returns html formatted table
			*/
			function addMilTime() {
				return ($this->effect->getEffect($this->militaryConstants->ADD_MILITARY_TRAIN_TIME, $this->province->pID));
			}

			/**
			* writeMilitaryUnit
			***
			* writes a row in militarytable (writeOwnMilitaryBlock)
			***
			* returns html formatted row in table
			*/
			function writeMilitaryUnit($obj, $num) {
				$output = "";

				//first calculate all needed numbers and extra text vars
				
				if(isset($this->militaryWar[$obj->getMilType()]['num'])) $warNum=$this->militaryWar[$obj->getMilType()]['num'];
				else $warNum=0;

				$days = $obj->getTicks();
				
				if ($GLOBALS['config']['serverMode'] == 'Beta') {
					$days = ceil($days/2);
				}

				$days *= $this->reduseTrTime;
				$days = floor($days);

				$costGold = $obj->getCostGold();
				$costMetal = $obj->getCostMetal();
				$costFood = $obj->getCostFood();

				$costGold = floor($costGold * $this->reduseCost['gold']);
				$costMetal = floor($costMetal * $this->reduseCost['metal']);
				$costFood = floor($costFood * $this->reduseCost['food']);

                $i = $obj->getMilType();

				$costText = "";

				$costSums = "document.trnMil.id$i.value=Math.round(document.trnMil.id$i.value);";
				$costBoxes = "";

				if($costGold > 0) {
					$costText .= $costGold." gold";
					$costSums .= "document.trnMil.c".$i."goldCost.value=(document.trnMil.id$i.value*$costGold);";
					$costBoxes .= "<input class='readOnly' type='text' maxlength='9' size='9' name='c".$i."goldCost' readonly='1' value='0'>";
				}
				
				if($costMetal > 0) {
					$costText .= ", ".$costMetal." metal";
					$costSums .= "document.trnMil.c".$i."metalCost.value=(document.trnMil.id$i.value*$costMetal);";
					$costBoxes .= ", <input class='readOnly' type='text' maxlength='9' size='9' name='c".$i."metalCost' readonly='1' value='0'>";
				}
				
				if($costFood > 0) {
					$costText .= ", ".$costFood." food";
					$costSums .= "document.trnMil.c".$i."foodCost.value=(document.trnMil.id$i.value*$costFood);";
					$costBoxes .= ", <input class='readOnly' type='text' maxlength='9' size='9' name='c".$i."foodCost' readonly='1' value='0'>";
				}

				//then make output 
				$output .= "
								<tr>
									<td class='buildings' width='10%'>".$obj->getName()."</td>
									<td class='buildings' width='6%'>".$this->militaryHome[$obj->getMilType()]['num']."</td>
									<td class='buildings' width='6%'>".$this->militaryTraining[$obj->getMilType()]."</td>
									<td class='buildings' width='6%'>$warNum</td>
									<td class='buildings' width='6%'>$num</td>
									<td class='buildings' width='6%'>$days</td>
									<td class='buildings' width='9%'>".$obj->trainFrom()."</td>
									<td class='buildings' width='16%'>$costText</td>
									<td class='buildings' width='10%'><input type='text' class='form' name='id$i' title='Enter a number to train' size='5' onChange='$costSums'></td>
									<td class='buildings' width='23%'>$costBoxes</td>
								</tr>";
				
				return $output;
			}

			/**
			* getMilitaryInProgress
			***
			* gets Military in progress table
			***
			* returns data fetched from table
			*/
			function getMilitaryInProgress() {
				$sqlGetInProgress = "select * from ProgressMil where pID=".$this->province->pID." order by mID";
				$this->database->query($sqlGetInProgress);
				if($this->database->numRows() > 0) {
					while($data = $this->database->fetchArray()) {
						$this->militaryInProgress[] = $data;
					}
				}
				return $this->militaryInProgress;
			}

			/**
			* parseMilitaryInProgress
			***
			* parses data from getMilitaryInProgress and puts it in a nice little table(array)
			***
			* returns array with data..
			*/
			function parseMilitaryInProgress() {
				$row = NULL;
				$table = NULL;
				if(!is_null($this->militaryInProgress)) {
					foreach($this->militaryInProgress as $progressRow) {
						$ticks = $progressRow['ticks'];
						$mID = $progressRow['mID'];
						$row[$ticks] = $progressRow;
						$table[$mID][$ticks] = $progressRow['num'];
						$table[$mID][0] = $mID;
					}
				}
				return $table;
			}

			/**
			* showMilitaryInProgress
			***
			* writes a html table based on array as parameter. Array should be the same as returned from
			* parseMilitaryInProgress. The written table will show all military in training an when they are due to be finished
			***
			* returns a html formatted table
			*/
			function showMilitaryInProgress($table) {
				$output = "";
				$output .= "\n<table class='buildingsTable' width='95%'>";
				$output .= "\n\t<tr>\n\t\t<td width='15%' class='buildings'><center>Military</center></td>";
				for($i = 1; $i<=24; $i++) {
				        $output.= "\n\t\t<td width='3%' class='buildings'><center>$i</center></td>";
			    }
				$output .= "<td width='7%' class='buildings'><center>Total</center></td>\n\t</tr>";

				if(!is_null($table)) {
					$milCount = NULL;

					foreach($table as $data) {
						$output .= "\n\t<tr>";
						$mID = $data[0];
						$milObj = $this->militaryTypesArray2[$mID]['obj'];
						$output .= "\n\t\t<td width='15%' class='buildings'><center>".$milObj->getName()."</center></td>";
						for($i = 1; $i <= 24; $i++) {
							$output .= "\n\t\t<td width='3%' class='buildings'><center>";
							if(isset($data[$i]) && $data[$i]>0) {
								$output .= $data[$i];
								$milCount[] = $data[$i];
							}
							else $output .= "&nbsp;";
							$output .= "</center></td>";
						}
						$output .= "\n\t\t<td width='7%' class='buildings'><center>".array_sum($milCount)."</center></td>";
						$milCount = NULL;
						$output .= "\n\t</tr>";
					}
				}
				$output .= "\n</table>";

				return $output;
			}

			/**
			* parseTrainInput
			***
			* parses input from trainblcok in the main militarypage
			***
			* returns the parsed input in an array
			*/
			function parseTrainInput() {
				$data = NULL;
				if(isset($_POST['trainMilitary'])) {
					$this->reduseCost = $this->addMilCost(); //make sure we get real costs...
					$reversePOST = array_reverse($_POST); //we want to pop the last element first
					for($i = 1; $i <= count($this->militaryAll); $i++) {
						end($reversePOST);	//jump to end of array so we actually pop the last element
						$j = 0;
						while( (substr(key($reversePOST), 0, 2) != 'id') && ($j < count($reversePOST)) ) {
							 //idXX, where XX is the type identificator of the military, holds the number to train...
							 //$j makes sure we don't get out of bounds on array
							 array_pop($reversePOST);  //pop element.. we don't need it
							 end($reversePOST);        //jump to last element just to be on the safe side..
							 $j++;
						}

						//jumping out of while-loop because we now have found idXX
						end($reversePOST);								//I really don't need to do this, because we are allready at end of array...
						$milType = (int)substr(key($reversePOST), 2);	//get the XX(type) part of idXX
						$milNum = current($reversePOST);				//get the number of type XX to train.
						if( (is_numeric($milNum)) && ($milNum > 0) ) $data[] = array("milType" => $milType, "milNum" => $milNum);    //store parsed information

						array_pop($reversePOST);						//pop current idXX and look for next...
					}
				}
				return $data;
			}

			/**
			* handleParsedTrainedMilitary
			***
			* writes a html table based on array as parameter. Array should be the same as returned from
			* parseMilitaryInProgress. The written table will show all military in training an when they are due to be finished
			***
			* returns a html formatted table
			*/
			function handleParsedTrainedMilitary($data) {
				$output = "";
				if(!is_null($data)) {



					$output .= $this->startActionInformationMessage();
					
					foreach($data as $milToTrain) {
						$milType = $milToTrain['milType'];
						$milNum = $milToTrain['milNum'];
						$milObj = $this->militaryAll[$milType]['object'];
						
						$costGold = floor($milObj->getCostGold() * $this->reduseCost['gold']);
						$costMetal = floor($milObj->getCostMetal() * $this->reduseCost['metal']);
						$costFood = floor($milObj->getCostFood() * $this->reduseCost['food']);

						$costGold *= $milNum;
						$costMetal *= $milNum;
						$costFood *= $milNum;
						
						$resources = array( "gold" => $costGold, "metal" => $costMetal, "food" => $costFood );
						
						$output .= $this->train($milObj, $milNum, $resources);

					}
					
					$output .= $this->endActionInformationMessage();
				}
				return $output;
			}

			/**
			* train
			***
			* tries to train $milNum number of military $milObj.
			***
			* returns a html formated text string with the result of training...
			*/
			function train($milObj, $milNum, $resources) {
				$output = "";
				if(is_null($this->buildings)) $this->buildings = new Buildings($this->database, $this->province);
				
				if($milObj->getMilType() == $this->militaryConstants->SOLDIERS) {
					$req = $this->province->peasants;
					$reqName = "peasants";
				}
				else {
					$req = $this->militaryHome[$this->militaryConstants->SOLDIERS]['num'];
					$reqMilObj = $this->militaryHome[$this->militaryConstants->SOLDIERS]['object'];
					$reqName = $reqMilObj->getName();
				}
				
					
				if($req >= $milNum) $numOK = true; else $numOK = false;

				if($milObj->housingRequirements()) {
					$hsFunc = $milObj->getHouseConst();
					$housing = $this->buildings->getHousing($hsFunc, $this->province->pID);
					$totMil = $this->getTotalMil($milObj);
					//echo "d".$housing."a".$totMil.$hsFunc.$this->province->pID."<br>";
					if( ($totMil+$milNum) <= $housing) $housingOK = true; else $housingOK = false;
				}
				else $housingOK = true;
				
				if($numOK && $housingOK) {
					if($this->province->useResource($resources['gold'], $resources['metal'], $resources['food'])) {
						if($milObj->getMilType() == $this->militaryConstants->SOLDIERS) {
							$this->province->usePeasants($milNum);
						}
						else {
							$this->killUnits($this->militaryConstants->SOLDIERS, $milNum, false);
							$this->getMilitaryHome(true);                                   
						}
						$this->insertTrain($milObj, $milNum);

						
						$output = "
										<br> You successfully sent $milNum ".$milObj->getName()." to training";
					}
					else {
						$output .= "
										<br> Not enough resources to train $milNum ".$milObj->getName();
					}
				}
				else {
					if(!$numOK) $output .= "
										<br> Not enough $reqName to train $milNum ".$milObj->getName();
					if(!$housingOK) $output .= "
										<br> Not enough housing to train $milNum ".$milObj->getName();
				}
//				$output .= "trying to train: ".$milObj->getName()." num: $milNum, resources needed: gold: ".$resources['gold']." metal: ".$resources['metal']."food: ".$resources['food']."<br>";
				return $output;
			}

			/**
			* inserTrain
			***
			* inserts £milNum of $milObj->getName() in training for $ticks days
			***
			* returns nothing at all!!!!! *muhahahahah*
			*/
			function insertTrain($milObj, $milNum, $ticks=0) {
				$this->justtrained = true;
				$useTicks = $milObj->getTicks();
				if($ticks != 0) {
					$useTicks=$ticks;
				}
				if ($GLOBALS['config']['serverMode'] == 'Beta') {
					$useTicks = ceil($useTicks/2);
				}

				$arrayToInsert = $this->misc->getRandomArray($milNum, $useTicks);
				$i = 1;
				foreach($arrayToInsert as $numThisRound) {
				    $sqlGetMil = "select num from ProgressMil where pID=".$this->province->pID." and ticks=$i and mID=".$milObj->getID();
				    $this->database->query($sqlGetMil);
				    if($data = $this->database->fetchArray()) {
					   $newNum = $data['num'] + $numThisRound;
					   $sqlUp = "update ProgressMil set num=$newNum where ticks=$i and pID=".$this->province->pID." and mID=".$milObj->getID();
					   $this->database->query($sqlUp);
				    } 
					else {
					   $insNum = round($numThisRound);
					   if($insNum>0) {
						$sqlIn = "insert into ProgressMil (pID, mID, ticks, num) values (".$this->province->pID.", ".$milObj->getID().", $i, $insNum)";
						$this->database->query($sqlIn);
					   }
				    }
				    $i++;
				}
			    $sql = "update Military set num=num+$milNum where pID=".$this->province->pID." and mID=".$milObj->getID();
				$sqlMilPop = "update Province set militaryPopulation=militaryPopulation+$milNum where pID=".$this->province->pID;
				$this->militaryAll[$milObj->getMilType()]['num'] += $milNum;
				$this->militaryTraining[$milObj->getMilType()] += $milNum;
			    $this->database->query($sql);
				$this->database->query($sqlMilPop);
			}

			function killUnits($varUnitType, $varNum, $resurrect=true) {
				$varNum = (int)$varNum;
				$newNum = 0;
				$milObj = @$this->militaryAll[$varUnitType]['object'];
				if( @$this->militaryAll[$varUnitType]['num'] - $varNum > 0) {
					$newNum = $this->militaryAll[$varUnitType]['num'] - $varNum;
				
				
				  if($resurrect == true) {
					 $varNum -= ceil( ($varNum/100) * mt_rand(10,50));
					 $getAllReadyDead = "select mID, mType, ticks, num from DeadMilitary where pID=".$this->province->pID." and mType=$varUnitType and ticks=".$this->militaryConstants->RESTICKS;
					 $sqlInsertDead = "insert into DeadMilitary (pID, mID, mType, ticks, num) values (".$this->province->pID.", ".$milObj->mID.", $varUnitType, ".$this->militaryConstants->RESTICKS.", $varNum)";
					 $sqlUpdateDead = "update DeadMilitary set num=num+$varNum where pID=".$this->province->pID." and mID=".$milObj->mID." and mType=$varUnitType and  ticks=".$this->militaryConstants->RESTICKS;
					
					 $this->database->query($getAllReadyDead);
					 $sqlRealQuery = "";
					 if($this->database->numRows() > 0) $sqlRealQuery = $sqlUpdateDead;
					 else $sqlRealQuery = $sqlInsertDead;
					 $this->database->query($sqlRealQuery);
				   }
                }
				$sqlUpdate = "update Military set num=$newNum where pID=".$this->province->pID." and mID=".$milObj->mID;
				$sqlMilPop = "update Province set militaryPopulation=militaryPopulation-$varNum where pID=".$this->province->pID;
				$this->militaryAll[$varUnitType]['num'] = $newNum;
				$this->militaryHome[$varUnitType]['num'] = max(0, @$this->militaryHome[$varUnitType]['num'] - $varNum);
				$this->database->query($sqlUpdate);
				$this->database->query($sqlMilPop);

				//$this->military = $this->getMilitary();
				
			}

			/**
			* getMilitaryNotTr()
			***
			* returns all military except the military in training..
			*/
			function getMilitaryNotTr() {
				$allMil = $this->militaryAll;
				$milInTr = $this->getMilitaryInTraining();
				$retVal = $allMil;
				if (is_array ($retVal)) {
					foreach($retVal as $milarry) {
						$obj = $milarry['object'];
						$type = $obj->getMilType();
						$retVal[$type]['num'] = $milarry['num'] - $milInTr[$type]; //- $milInWar[$type]['num'];  
					}
				}
				$this->militaryNoTrain = $retVal;
			        
				return $retVal; //$this->getMilitary($this->MilitaryConst->OWN_MILITARY);
			}

			/**
			* setArmy()
			***
			* set the total defensive/attacking army (if no parameter sets defending), else sets the army acording to param
			*/
			function setArmy($army=NULL) {
				if(is_null($army)){
				    $this->getMilitaryTypes();
					$this->Army = $this->getMilitaryHome();
				}
				else {
					if(is_array($army)) {
						$army2 = NULL;
						$this->getMilitaryTypes();
						foreach($army as $mil) {
						   $mID = $mil['mID'];
						   $milObj = $this->militaryTypesArray2[$mID]["obj"];
						   $mil['object'] = $milObj;
					           $army2[] = $mil; 
						}
						$army = $army2;
					}
				    $this->Army = $army;
				}
			}

		    /**
			* getArmyPoints
			***
			* returns total attack/defenspoint of army set in 'setArmy'
			*/
			function getArmyPoints() {
			    $attack = 0;
			    $defense = 0;
			    foreach($this->Army as $military) {
					$milObj = $military['object'];
					$attack += $military['num'] * $milObj->getAttack();
					$defense += $military['num'] * $milObj->getDefense();
			    }
			    return array("attack" => $attack, "defense" => $defense);
			}

			/**
			* getArmy()
			***
			* returns current army set by 'setArmy'
			*/
			function getArmy() {
				return $this->Army;
			}

		    /**
			* getArmyNetworth
			***
			* returns the networth of current military, returns SOLDIERS networth for military in training!
			*/
			function getArmyNetworth() {
			    $armyNetworth = 0;
				$this->getMilitaryNotTr();
				$milTr = $this->getMilitaryInTraining(true);
				if (is_array ($this->militaryNoTrain)) {
					foreach($this->militaryNoTrain as $mil) {
						$milObj = $mil['object'];
						$armyNetworth += $mil['num'] * $this->militaryConstants->NETWORTH[$milObj->getMilType()];
					}
				}
				
				if (is_array ($milTr)) {
					foreach($milTr as $milNum) {
						$armyNetworth += ($milNum * $this->militaryConstants->NETWORTH[($this->militaryConstants->SOLDIERS)]);
					}
				}
			    return $armyNetworth;
			}


			/**
			* self()
			***
			* returns the path to current script...
			*/
			function self() {
				return $_SERVER['PHP_SELF'];
			}



/**
			* getDeadCount()
			*
			* - returns an array of the form theArray[] = ['mType']['num']
			*   foreach($theArray as $data) will give you the array: $data['mType']['num']
			* - returns false if there is no military to resurrect.
			*/
			function getDeadCount() {
				$sqlGetDead = "select mType as type, sum(num) as num from DeadMilitary where pID=".$this->province->pID." group by mType";
			//	echo "<br>$sqlGetDead<br>";
				$this->database->query($sqlGetDead);

				if($this->database->numRows() > 0) {
				   while($data = $this->database->fetchArray()) {
					  $retArray[] = $data;
				   }
				   return $retArray;
				}
				else {
				   return false;
				}
			}
			
			/**
			* removeFromDeadCount()
			* Removes military from resurrectable military....
			* 
			* $type - wich military type to remove from resurrectable
			* $num  - the amount of $type to remove
			*
			* - returns the number of military removed... (should be the same as you tell it to remove unless the number is higher
			*   than the number you get from getDeadCount)
			* - returns false if there is no military to remove (getDeadCount should also return false in this scenario)
			*/
			function removeFromDeadCount($type, $num) {
				$totalNum = $num;
				$sqlGetDeadCount = "select mType as type, sum(num) as num from DeadMilitary where pID=".$this->province->pID." and mType=$type group by mType";
				$result = $this->database->query($sqlGetDeadCount);
				if($numData = $this->database->fetchArray()) {
					if($numData['num'] > $num) {
						$sqlGetByTicks = "select mType as type, num, ticks from DeadMilitary where pID=".$this->province->pID." and mType=$type order by ticks asc";
						$resultByTicks = $this->database->query($sqlGetByTicks);
						while(($toRemoveFrom = $this->database->fetchArray($resultByTicks)) && ($num > 0)) {
							if($num >= $toRemoveFrom['num']) {
								$sqlDelete = "delete from DeadMilitary where pID=$".$this->province->pID." and mType=$type and ticks=".$toRemoveFrom['ticks'];
								$this->database->query($sqlDelete);
								$num -= $toRemoveFrom['num'];
							}
							else {
								$newNum = $toRemoveFrom['num'] - $num;
								$sqlUpdateCount = "update DeadMilitary set num=$newNum where pID=".$this->province->pID." and mType=$type and ticks=".$toRemoveFrom['ticks'];
								$this->database->query($sqlUpdateCount);
								$num -= $toRemoveFrom['num'];
							}
						}
					}
					else {
						$totalNum = $numData['num'];
						$sqlDelete = "delete from DeadMilitary where pID=".$this->province->pID." and mType=$type";
						$this->database->query($sqlDelete);
					}
					//$this->createMilitary($type, $totalNum);
					return $totalNum;
				}
				else {
					return false;
				}
			}

			/**
			* createMilitary()
			* creates military in current province..
			*
			* $type - the kind of military to create
			* $num  - the amount of $type to create
			* 
			* - returns true if can create military
			* - returns false if not enough sciences to have that $type of military...
			*/
		    function create($type, $num) {
//			   $this->military = $this->getMilitary();

			   
			   if( isset( $this->militaryAll[$type]['object'] ) ) {
				$milObj = $this->militaryAll[$type]['object'];
			   	if($this->requirementsOK($milObj)) {
			   	   	$sqlUpdateMilNum = "update Military set num=num+$num where pID=".$this->province->pID." and mID=".$milObj->mID;
			      		$this->database->query($sqlUpdateMilNum);
				  	return true;
			   	}
			   }
			   return false;
			}
			
			/***************
			* doTick()
			***
			* handels gamedataupdate!
			*/
			function doTick() {
			    $sqlUpdate = "update ProgressMil RIGHT JOIN Province on (ProgressMil.pID=Province.pID) set ticks=ticks-1 where (ticks>0 AND Province.vacationmode='false')";
			    $sqlDelete = "delete from ProgressMil where (ticks<=0)";
			    $this->database->query($sqlUpdate);
			    $this->database->query($sqlDelete);
			
			    $zeroOut = "update Province set militaryPopulation=0";
			    $tempTable = "create temporary table milSum select pID, sum(num) as num from Military group by pID";
				// had to change the query in mysql 5
//			    $updateMilPop = "update Province, milSum set Province.militaryPopulation=milSum.num where Province.pID=milSum.pID";
				$updateMilPop = "update Province RIGHT JOIN milSum on Province.pID=milSum.pID set militaryPopulation=milSum.num";
			    $delTableMilPop = "drop table milSum";

				$updateDeadMil = "update DeadMilitary set ticks=ticks-1 where ticks>0";
				$deleteDeadMil = "delete from DeadMilitary where ticks<=0";
				$this->database->query($updateDeadMil);
				$this->database->query($deleteDeadMil);

			    $this->database->query($zeroOut);
			    $this->database->query($tempTable);
			    $this->database->query($updateMilPop);
			    $this->database->query($delTableMilPop);
			}

			function startActionInformationMessage() {
				$output = "";
				$output .= "
							<br><br><center>
							<table class='buildingsTable' width='50%'>
								<tr>
									<td class='buildings'><center>";
				return $output;
			}

			function endActionInformationMessage() {
				$output = "";
				$output .= "
										<br>&nbsp
									</td>
								</tr>
							</table>
							</center><br><br>";
				return $output;
			}


			//OLD AND UNUSED??

			function setpID($pID) {
			    $this->pID = $pID;
			}
			function getTotalMil($milObj) {
				$mID = $milObj->getID();
				$sql1 = "select num from Military where mID=$mID and pID=".$this->province->pID;
				$this->database->query($sql1);
				$data = $this->database->fetchArray();
				return $data['num'];
			}
			   
		    function getMilID($type) {
			    $milObj = $this->militaryAll[$type]['object'];
			    return $milObj->getID();
			}

			function getMilObj($mType) {
			    return $this->militaryTypesArray[$mType]["obj"];
			}

			function getMilTypeObj($mID) {
			   	foreach($this->militaryTypesArray as $milType) {
					$milObj = $milType["obj"];
					if($milObj->getID() == $mID) {
						return $milObj;
					}
				}
			}
			
			function getMilUnit($mType) {
				//$mil = $this->getMilitaryHome();
				$mil = $this->militaryHome;
                                //echo "<br>$mType<br>";
				//echo "<pre>";
                                //print_r($mil);
                                //echo "</pre>";
				if(isset($mil[$mType])) {
					$milObj = $mil[$mType]['object'];
                                	//echo $milObj;
                                	$num = $mil[$mType]['num'];
					$retVal = array("object"=>$milObj, "num"=>$num);
				}
				else {
					$milObj = $this->militaryTypesArray[$mType]["obj"];
 					$retVal = array("object"=>$milObj, "num"=>0);
				}
				return $retVal;
			}

		
		}//END MILITARCLASS
	} //END if(!class_exists("Military")) (line 02)
?>
