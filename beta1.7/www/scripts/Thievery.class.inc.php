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

/* Thievery class
 *
 * This class handles thievery operations.
 *
 * Author: Anders Elton 23.04.2003
 *
 * Worklog:
 *
 */
$GLOBALS['game_debug'] = true;
if( !class_exists("Thievery") ) {
require_once ($GLOBALS['path_www_scripts'] . "Province.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Effect.class.inc.php");
$GLOBALS['path_www_thievery'] = $GLOBALS['path_www_scripts'] . "thievery/";

// static data only needs to be loaded once.
$GLOBALS['thievery_static_data_set'] = false;
$GLOBALS['thievery_static_data_opertations'] = false;
class Thievery {
	var $database;
	var $pID;
	var $thieveryOps=array();	
	var $MIN_INFLUENCE = 40;
	var $GROW_INFLUENCE = 5;
	var $effectObj;					// Effect object
	

    ////////////////////////////////////////////
    // Thievery::Thievery(&$db, $provinceId)
    ////////////////////////////////////////////
	// Parameters:
	//    &$db: Reference to a database object
	//    $provinceId: id to a province
	//
	// Loads all subclasses (thievery operations)
	// and puts them in array thieveryOperations
    ////////////////////////////////////////////

	function Thievery ($db, $provinceId=false, $forcereloadstatic=false) {
		$this->database = $db;
		if ($provinceId) {
		   $this->pID = $provinceId;
        }
		if (($GLOBALS['thievery_static_data_set']==false) || ($forcereloadstatic==true))
		{
			$GLOBALS['thievery_static_data_set'] = true;
			if ($this->database->query("SELECT * FROM ThieveryOps") && $this->database->numRows()) {
				if (!class_exists("ThieveryBase")) {
					require_once($GLOBALS['path_www_thievery']. "ThieveryBase.class.inc.php");
				}
				while (($className = $this->database->fetchArray())) {
					if (!class_exists($className['ClassName'])) {
        	        	require_once($GLOBALS['path_www_thievery'].$className['ClassName'] .".class.inc.php");
            		}
					$id = $className['thoID'];
					$GLOBALS['thievery_static_data_opertations'][$id] = new $className['ClassName'] ($id);
				}
			}
		}
		$this->thieveryOps = $GLOBALS['thievery_static_data_opertations'];
		
	}

    ////////////////////////////////////////////
    // void Thievery::doTick()
    ////////////////////////////////////////////
	// Preforms global update for the class.
	// used in serverscript.
        ////////////////////////////////////////////
	function doTick () {
		// Changed so that humans (raceid=6) can have maximum 110 influence, Soptep - 13/10/2009

		$this->database->query("UPDATE Province set influence=LEAST((influence+$this->GROW_INFLUENCE),110) WHERE spID=6");
		$this->database->query("UPDATE Province set influence=LEAST((influence+$this->GROW_INFLUENCE),100) WHERE spID!=6");
		
		foreach ($this->thieveryOps as $operation)
		{
			$operation->doTick($this->database);
		}
	        $this->database->query("UPDATE ActiveThieveryOps set ticks=ticks-1");
                $this->database->query("DELETE FROM ActiveThieveryOps where ticks<=0");

	}

    ////////////////////////////////////////////
    // string Thievery::getSelectBox()
    ////////////////////////////////////////////
	//
	// Returns a select box in html code with the
	// legal choices for the given province
	//
    ////////////////////////////////////////////

	function getSelectBox () {
		$html = "";
		require_once("Science.class.inc.php");
		$my = new Science ($this->database,$this->pID);
		$html .= "<SELECT NAME=\"selectOperation\" class='form'>";
		if (is_array($this->thieveryOps)) {
			reset($this->thieveryOps);
			foreach ($this->thieveryOps as $item) {
				if ($my->scienceReqOk($item->requires))
				$html .= "<OPTION";
				if (isset($_POST['selectOperation']) && ($_POST['selectOperation']==$item->getID()) )
					$html .= " SELECTED";
				$html .=" value=\"".$item->getID()."\">".$item->getName()."</OPTION>";
			}
		} else $html .= "<OPTION value=0>No ops aviable!</OPTION>";
		$html .= "</SELECT>";
		return $html;
	}

    ////////////////////////////////////////////
	// string Thievery::showAllOps()
    ////////////////////////////////////////////
	//
	// returns:
	//	a html formatted string with all
	//      thievery operations
    ////////////////////////////////////////////

	function showAllOps () {
		if (is_array($this->thieveryOps)) {
			reset($this->thieveryOps);
			foreach ($this->thieveryOps as $item) {
				$html .= "<br>".$item->getID()."  <b>".$item->getName()."</b>";
			}
		} else $html .= "No Operations!";
		return $html;
	}	
    ////////////////////////////////////////////
	// string Thievery::thieveryEffect ($operation, $victim)
    ////////////////////////////////////////////
	//
	// Parameters:
	//    $operation: id of the thievery operation
	//    $victim   : provinceID of the victim
	//
	// This function executes the operation on the
	// given province. It will return a text telling
	// of the success/failure.
	//
	// Returns:
	//    string: html formatted output.
	//
        ////////////////////////////////////////////

	function thieveryEffect ($operation, $victim) {
		$html = "";
		$endageOverride = false;
		if ( ($GLOBALS['config']['status'] == 'Ended') ||  (isset($GLOBALS['game_debug']) && $GLOBALS['game_debug'] == true))
		{
			if ((isset($GLOBALS['game_debug']) && $GLOBALS['game_debug'] == true) && (intval($GLOBALS['user']->access) & $GLOBALS['constants']->USER_ADMIN))
			{
				$GLOBALS['game_debug_data'] .= "<br>Always success, no validation check in thievery ops, due to game debug!";
				$endageOverride = true;
			}
		} 
		if (isset($this->thieveryOps[$operation])) {
	                
            $province = new Province($this->pID,$this->database);
       	    $province->getProvinceData(); $province->getMilitaryData();
			$myThieves = $province->milObject->getMilUnit($GLOBALS['MilitaryConst']->THIEVES);
			if (($victim==$province->pID) && ($endageOverride==false)) {
				return "<center>".$province->getAdvisorName() . ", have you gone mad?  I think our influence and thieves 
					are better used elsewhere!</center>";
			}
			$targetProvince = new Province($victim,$this->database);
            $targetProvince->getProvinceData(); $targetProvince->getMilitaryData();
			if ($targetProvince->isProtected() && ($endageOverride==false)) {
				return "<center>".$province->getAdvisorName() . ", that land is so new, so we do not have any thieves 
					located there.  Wait until the land goes out of protection and try again.</center>"; 
			}
			if ($province->isProtected() && ($endageOverride==false)) {
				return "<center>".$province->getAdvisorName() . ", we can not do any thievery operations while still in protection!</center>"; 
			}
			
			if (($province->influence<$this->MIN_INFLUENCE) && ($endageOverride==false)) {
				return $province->getAdvisorName() . ", we do not have sufficent influence to 
					perform more operations today!";
			} else if (($myThieves['num']<1) && ($endageOverride==false)){
				return $province->getAdvisorName() . ", we do not have any thieves!";
			} else if ((($targetProvince->acres < 1) || ($targetProvince->isAlive()==false)) && ($endageOverride==false)){
				return "<center>".$province->getAdvisorName() . ", this Province is long dead!</center>";
			}
			else {
				if ($endageOverride==false)
					$province->useInfluence($this->thieveryOps[$operation]->costInfluence);
			}


			// new new new new new new new new new new new new new new new new new new
			// new new new new new new new new new new new new new new new new new new

//			$this->effectObj = new Effect( $this->database );
			
			// Old formula put back in place, Soptep: 15/12/09
			$targetTpa = $targetProvince->getTpa($GLOBALS['effectConstants']->ADD_THIEVERY_DEF);
			$myTpa = $province->getTpa($GLOBALS['effectConstants']->ADD_THIEVERY_OFF);
			mt_srand($this->make_seed());
			$successMod = 1 + ($this->thieveryOps[$operation]->getDifficulity() / 100);
			$chance = (($myTpa*$successMod) / ($myTpa + $targetTpa)) * 1000;
			$roll = mt_rand(1,1000);		
			$orgresult = $roll < $chance;
			$success = $orgresult;


/*
			// EDITED FORMULA BY TASOS-------------------------------------------------------------
			
			$myAcres = $province->acres; 
			$targetAcres = $targetProvince->acres; 
			$targetTpa = $targetProvince->getTpa($GLOBALS['effectConstants']->ADD_THIEVERY_DEF);
			$myTpa = $province->getTpa($GLOBALS['effectConstants']->ADD_THIEVERY_OFF);
			mt_srand($this->make_seed());
			
			if($myAcres < $targetAcres)
				$myTpa = pow(($myTpa*(9/10)),((15/10)*($targetAcres - $myAcres)/$myAcres));
				
			if (( $myTpa > 0 ) && ($myTpa < 0.1 ))
				$myTpa = 0.1;
				
			$successMod = 1 + ($this->thieveryOps[$operation]->getDifficulity() / 100);
			$chance = 1000*( $myTpa*$successMod)/($myTpa + $targetTpa) +4*(pow($myTpa,2) - pow($targetTpa,2));
			$roll = mt_rand(1,1000);
			
			if ($roll ==1) $orgresult = 0;
			else if ($roll == 1000) $orgresult = 1;
			else $orgresult =$roll < $chance;
			
			$success = $orgresult;
			
			if ($province->acres == 2806 || $province->acres == 375) {
				echo "myAcres: ".$myAcres."<br />";
				echo "targetAcres: ".$targetAcres."<br />";
				echo "myTpa: ".$myTpa."<br />";
				echo "targetTpa: ".$targetTpa."<br />";
				echo "successMod: ".$successMod."<br />";
				echo "chance: ".$chance."<br />";
				echo "roll: ".$roll."<br />";
				echo "success: ".$success."<br />";
			}
			 
			// END FORMULA BY TASOS--------------------------------------------------------
			*/
			
			// we might have a callback that changes things
			if (($province->OnThieveryAction($orgresult, $targetProvince) == false) && ($orgresult == true))
			{
				$orgresult = false;
/*				echo "fAILED because of callback" . $province->callbackMessage;
				$orgresult = true;
				die;*/
			}

			
			// a callback might stop the thievery
			$success = $targetProvince->OnThieved($orgresult,$province);
			
			if ($success != $orgresult)
			{
/*				echo "fAILED because of callback" . $targetProvince->callbackMessage;
				die;*/
			}
			if (isset($GLOBALS['game_debug']) && $GLOBALS['game_debug'] == true)
			{
				$GLOBALS['game_debug_data'] .= "<br>Thievery result: myTPA: $myTpa, targetTPA: $targetTpa, successMod: $successMod, chance: $chance, roll: $roll, success: ". ($orgresult?"true":"false")."";
			}
			if (isset($GLOBALS['config']['serverMode']) && $GLOBALS['config']['serverMode'] == 'Beta')
			{
				$html .= "<b>BETA DEBUG: </b>" . "<br>Thievery result: myTPA: $myTpa, targetTPA: $targetTpa, successMod: $successMod, chance: $chance, roll: $roll, success: ". ($orgresult?"true":"false")."";
			}
			require_once($GLOBALS['path_www_scripts'] . "ActionLogger.class.inc.php");
			$actionlogger = new ActionLogger($this->database);
			if ( ($success) || ($endageOverride==true) ) {
				$actionlogger->log($actionlogger->THIEVERY,$province->getpID(), $targetProvince->getpID(),$operation,true);
				$html .= $this->thieveryOps[$operation]->thieveryEffect($province,$targetProvince);
			} else { // failure
				$actionlogger->log($actionlogger->THIEVERY,$province->getpID(), $targetProvince->getpID(),$operation,false);
				$html .= $this->thieveryOps[$operation]->thieveryFailure($province,$targetProvince);
			}
		} else {
			$html .= "unexpected error!";
		}
		return $html;
	
	}
	function make_seed() {
    		list($usec, $sec) = explode(' ', microtime());
    		return (float) $sec + ((float) $usec * 100000);
	}

} // end of class


}
?>