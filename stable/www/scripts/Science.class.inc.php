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

/* Science class
 *
 * This class handles the sciences for the different provinces.
 *
 * Author: Anders Elton 01.04.2003
 *
 * Science tree.
 *
 * Military: LeatherArmor->MetalWeapons 	-> MetalArmour	-> Tactics	-> siege engines
 * requires: 	NULL	   Leather, metalwork	   MetalWpons	   MetalArm	   Tactics
 * Infrastr: agriculture -> mining     		-> metalwork  	-> trade 	-> construction
 * requres:    NULL	    agriculture	   	   mining	    metawork	   trade
 * Magic:    Illusions	 -> BattleWizards	-> Attack spells   
 * requires: NULL	    illusions		   Battlewizards
 * thievery: 
 *
 *
 * Workolog:
 *
 * 10.09.03: Anders Elton (removed race stuff for now. Implemented effectClass.)
 * 19.07.03: Anders Elton (adding uniqe race sciences) NB!!! LOTS TODO! NBNBN
 * 23.04.03: Anders Elton (adding getScienceEffect)
 * 18.04.03: Anders Elton (improved constructor) Now builds science tree etc.
 * 09.04.03: Anders Elton (added scienceReqOk) 
 *
 */


if( !class_exists("Science") ) {
// keep to make compatible with old...
$GLOBALS['PATH_TO_SCIENCE'] = "sciences/";
// but here is the new style!
$GLOBALS['www_path_science'] = $GLOBALS['path_www_scripts'] . "sciences/";

// static data only needs to be loaded once.
$GLOBALS['science_static_data_set'] = false;
$GLOBALS['science_static_data_knowledge'] = false;


require_once ($GLOBALS['path_www_scripts'] . "Effect.class.inc.php");
class Science {
	var $database;
	var $pID;
	var $scienceTree=array();  		// array of ENTIRE science tree on this form
						// $scienceTree[sccID] => object

	var $provinceScience = array();		// sciences the province has on this form
						// $provinceScience[sccID] => array(from SQL query)
	var $researching = NULL;
	var $myScienceLevel = array('military' =>0, "infrastructure" => 0, "magic" => 0, "thievery" => 0);
	var $province = NULL;
	var $effectObj = NULL;					// Effect object
	
	// special case for the guide
	var $guideMode = false;
	var $guideScience = array();


    /////////////////////////////////////////////////////
    // Science::Science (&$db, $provinceId)
    /////////////////////////////////////////////////////
    //
	// Parameters:
	//    &$db - Reference to a database object
	//    $provinceId - a provinceId or false
	//
	// This constructor will try to build a complete science
	// tree.  If a provinceId other than false is supplied
	// it will create a province object and get science
	// data for that province.
    //
    /////////////////////////////////////////////////////


	function Science (&$db, $provinceId=false, $forcereloadstatic=false) 
	{
		$this->database = $db;
		$this->getScienceTree();
		if ($provinceId) {
		   $this->pID = $provinceId;
		   $this->province = new Province($provinceId, $db);
		   $this->province->getProvinceData();
		   $this->getProvinceScience();
        }
	}
	function getFakeScience()
	{
		reset($this->guideScience);
		foreach ($this->guideScience as $fake)
		{
			$this->provinceScience[$fake] = array('ticks'=>0, 'pID'=>0,'sccID'=>$fake);
		}
//		$this->provinceScience[$sccID] = $row;
	}
	function setStartScience($pID) 
	{
		$this->pID = $pID;
		$this->province = new Province($pID, $this->database);
		$this->province->getProvinceData();
		reset ($this->scienceTree);
		$raceStartLevel = $this->province->raceObj->race->getStartScience();
		foreach ($this->scienceTree as $value) {
			$sciLevel = $value->getLevel();
			$start = true;
			if ($sciLevel['magic']>0)
				if (!($sciLevel['magic'] & $raceStartLevel['magic']))
					$start = false;
			if ($sciLevel['infrastructure']>0)
				if (!($sciLevel['infrastructure'] & $raceStartLevel['infrastructure']))
					$start = false;
			if ($sciLevel['military']>0)
				if (!($sciLevel['military'] & $raceStartLevel['military']))
					$start = false;
			if ($sciLevel['thievery']>0)
				if (!($sciLevel['thievery'] & $raceStartLevel['thievery']))
					$start = false;
			if ($start)
				$this->database->query("INSERT INTO Science (pID,sccID,ticks) VALUES ('$this->pID','".
										$value->getID()."','0')");
				
		}
	
	}
    
	/////////////////////////////////////////////////////
    // void Science::doTick($ticks)
    /////////////////////////////////////////////////////
	// This function is called from the server. 
	// It makes ALL sciences advance $ticks.. (tick=tick-$tick)
	// it also adds (global) effect of science to a province.  
	// (like agricultures add bonus to food)
    //
    /////////////////////////////////////////////////////
	function doTick($ticks=1) {
		$this->database->query("UPDATE Science set ticks=ticks-$ticks where ticks>0");
		// global effects.
		$this->getScienceTree();
		reset($this->scienceTree);
		foreach ($this->scienceTree as $science) {
			$science->doTick($this->database);
		}
		
	}
    /////////////////////////////////////////////////////
	// bool Science::getScienceTree()
    /////////////////////////////////////////////////////
	// gets science tree from database and loads the classes.
	//
	// Note!  If an invalid class is loaded, the script will terminate
	//
	// returns true : ok
	// false: database empty
    /////////////////////////////////////////////////////
	function getScienceTree () 
	{
		if (($GLOBALS['science_static_data_set']==false) )
		{
			$GLOBALS['science_static_data_set'] = true;

			if ($this->database->query("SELECT * FROM ScienceCat") && $this->database->numRows() ) {
				if (! class_exists("ScienceBase")) {				
					require_once($GLOBALS['PATH_TO_SCIENCE']."ScienceBase.class.inc.php");
				}
				while (($className = $this->database->fetchArray())) {
					if (!class_exists($className['className'])) {
						require_once($GLOBALS['PATH_TO_SCIENCE'].$className['className'] .".class.inc.php");
					}
					$sccID = $className['sccID'];
					//echo $className['className'];
//					$this->scienceTree[$sccID] = new $className['className'] ($sccID);
					$GLOBALS['science_static_data_knowledge'][$sccID] = new $className['className'] ($sccID);
				}


			} else {
				return false;
			}
		}
		$this->scienceTree = $GLOBALS['science_static_data_knowledge'];
		return true;
	}
    
	/////////////////////////////////////////////////////
	// bool Science::researchInProgress ()
    /////////////////////////////////////////////////////
	// Returns:
	//   True:  Province is researching a science
	//   False: Province is *not* researching a science
    /////////////////////////////////////////////////////

	function researchInProgress() 
	{
		if ($this->researching) return true;
		return false;
	}

    
    /////////////////////////////////////////////////////
	// object Science::getScienceObject ($key)
    /////////////////////////////////////////////////////
	//
	// Parameters:
	//    $key: sccId - same key as used in database
	//
	// This function will return an object matching the
	// key given, or false in case of error
	//
	// Returns:
	//    object: an object inherited from the ScienceBase
	//    false: error
	//
    /////////////////////////////////////////////////////

	function getScienceObject($key) {
		if (array_key_exists($key, $this->scienceTree)) {
			return $this->scienceTree[$key];			
		} else {
			return false;
		}
	}

    /////////////////////////////////////////////////////
	// int Science::getScienceNetworth()
    /////////////////////////////////////////////////////
	//
	// This function calculates how much Networth all
	// the sciences for the province is worth.
	//
	// Returns
	//   value of Science in networth
    //
	/////////////////////////////////////////////////////

	function getScienceNetworth() {
		$nw=0;
		if (is_array($this->provinceScience)) {
			reset ($this->provinceScience);
			while (list($key, $value) = each ($this->provinceScience)) {
				$nw += $this->scienceTree[$value['sccID']]->getNW();
		}
		}
		return $nw;
	}

    /////////////////////////////////////////////////////
	// bool Science::getProvinceScience ()
    /////////////////////////////////////////////////////
	//
	// This function will get all the science a province
	// has, including science in progress.
	// It will then call the function setScienceLevel to
	// set correct sciencelevel array
	// 
	// Returns:
	//    True: province has science or science in progress
	//    False: no science or science in progress
    /////////////////////////////////////////////////////

	function getProvinceScience () 
	{
		if ($this->guideMode)
		{
			$this->getFakeScience();
			$this->setScienceLevel();
			return true;
		}
		if ($this->database->query("SELECT * FROM Science where pID = '$this->pID'") && $this->database->numRows() ) {
			while (($row = $this->database->fetchArray())) {
				if ($row['ticks']>0) {
					$this->researching = $row;
				} else {
					$sccID = $row['sccID'];
					$this->provinceScience[$sccID] = $row;
				}
			}

		} else {
			return false;
		}
		$this->setScienceLevel();
		return true;
	}

    /////////////////////////////////////////////////////
	// string Science::showScienceTree ()
    /////////////////////////////////////////////////////
	// 
	// This function shows the entire science tree and
	// the requirements to get each science.
	//
	// Returns:
	//    string: html formatted string
    /////////////////////////////////////////////////////

	function showScienceTree () 
	{
		$html = "";
		reset($this->scienceTree);

		if (is_array($this->scienceTree))
		{
			$html .= "<table border=1>";
			foreach ($this->scienceTree as $value) {
				if ($value->getID() != 18 )
				{
				$html .= "<tr>";
				$html .="<td class='rep1' align='left'>" . $value->getName() . " (". $value->getCategory().")</td>";
				$html .="<td class='rep1'>". $value->getDescription() ."<br>";
				$requires = $value->getRequires();
				$html .= "Requires: <b> infrastructure:</b> " . $requires['infrastructure'];
				$html .= " <b> military: </b>" . $requires['military'];
				$html .= " <b> magic: </b>" . $requires['magic'];
				$html .= " <b> thievery: </b>" . $requires['thievery'];
				$requires = $value->getLevel();
				$html .= "<br>Gives: <b> infrastructure:</b> " . $requires['infrastructure'];
				$html .= " <b> military: </b>" . $requires['military'];
				$html .= " <b> magic: </b>" . $requires['magic'];
				$html .= " <b> thievery: </b>" . $requires['thievery'];
				$html .= "</td>";
				$html .="<td class='rep1' align='right' width='14%'>" .number_format($value->getCostGold(),0,' ',',') ." gc<br>".number_format($value->getCostMetal(),0,' ',',')." metal</td>";
				$html .="<td class='rep1' align='right'>". $value->getTicks()  ."</td>";
				$html .= "</tr>";
				}
			}
			$html .="</table>";
		}
		return $html;
	}
    
	/////////////////////////////////////////////////////
	// array Science::getAviableSciences ()
    /////////////////////////////////////////////////////
	// 
	// collects an array with the sciences you can research
	//
	// Returns:
	//    the array collected.
	//
    /////////////////////////////////////////////////////
	function getAviableSciences() {
		reset ($this->scienceTree);
		foreach ($this->scienceTree as $value) {
			if ($this->canResearch($value->getID())) {
				$arr[] = $value; 
			}
		}
		if (isset($arr))
			return $arr;
		else 
			return false;	
	}

    /////////////////////////////////////////////////////
	// bool Science::canResearch ($sccID)
    /////////////////////////////////////////////////////
	// 
	// Checks if the player can research the advantage.
	//
	// Returns:
	//    True:  the province can research
	//    False: can not research
    /////////////////////////////////////////////////////
	function canResearch ($sccID) {
		if (array_key_exists($sccID, $this->provinceScience)) {
			return false;
		}
		
		if (array_key_exists($sccID, $this->scienceTree)) {

			$req = $this->scienceTree[$sccID]->getRequires();
			// If things fuck up, unncomment this and comment out the line below...
/*			if ($req['military']>$this->myScienceLevel['military']) return false;
			if ($req['infrastructure']>$this->myScienceLevel['infrastructure']) return false;
			if ($req['magic']>$this->myScienceLevel['magic']) return false;
			if ($req['thievery']>$this->myScienceLevel['thievery']) return false;
*/			
			if (!$this->scienceReqOk($req)) return false;  // comment this if things fuck up.
			// check if science is allowed for race.
			
			// special case, exit here for teh guide
			if ($this->guideMode) {
				return true;
			}
			$lvl = $this->scienceTree[$sccID]->getLevel();
			if ($lvl['military']>$this->province->raceObj->race->maxScience['military']) return false;
			if ($lvl['infrastructure']>$this->province->raceObj->race->maxScience['infrastructure']) return false;
			if ($lvl['magic']>$this->province->raceObj->race->maxScience['magic']) return false;
			if ($lvl['thievery']>$this->province->raceObj->race->maxScience['thievery']) return false;
			
		} else echo "unexpected ERROR: $sccID - contact administrator.";
		return true;
			
	}
    /////////////////////////////////////////////////////
	// void Science::setScienceLevel ()
    /////////////////////////////////////////////////////
	// 
	// Sets the science level according to what sciences
	// the province has.
	//
    /////////////////////////////////////////////////////

	function setScienceLevel () 
	{
//		unset($this->myScienceLevel);
		$this->myScienceLevel['military'] = 0;
		$this->myScienceLevel['infrastructure'] = 0;
		$this->myScienceLevel['magic'] = 0;
		$this->myScienceLevel['thievery'] = 0;
	
		reset ($this->provinceScience);
//		print_r($this->provinceScience);
		while (list($key, $value) = each ($this->provinceScience)) {
			$add = $this->scienceTree[$value['sccID']]->getLevel();
			// all this was += before, changed to |= as that should be correct.
//			echo "tmp, in while";
			$this->myScienceLevel['military'] |= $add['military'];
			$this->myScienceLevel['infrastructure'] |= $add['infrastructure'];
			$this->myScienceLevel['magic'] |= $add['magic'];
			$this->myScienceLevel['thievery'] |= $add['thievery'];
		}
	}


    /////////////////////////////////////////////////////
	// string Science::showSciences ()
    /////////////////////////////////////////////////////
	// 
	// Builds a html formatted string showing what sciences
	// a province has
	//
	// Returns:
	//    The html formatted string
	//
    /////////////////////////////////////////////////////
	function showSciences() 
	{
		$html = "";
		$count =0;
		if (is_array($this->provinceScience)) {
			reset($this->provinceScience);
    		while (list($key, $value) = each ($this->provinceScience)) {
				if ($count ==1) $html .= ", ";
 	            $html .= $this->scienceTree[$value['sccID']]->getName();
				$count =1;
			}
		} else {
			$html = "none";
		}
		return $html;	
	}
	
	/////////////////////////////////////////////////////
	// bool Science::research ($key)
    /////////////////////////////////////////////////////
	//
	// Parameters:
	//    $key: sccId - same key as used in database
	//
	// This function will start a research for the given
	// province.  If the key does not excist in the
	// science tree, something is wrong.  It will also
	// call the useResource function in the procince
	// object (to pay for the sciences).  If not enough
	// resources the function will return false.
	//
	// Returns:
	//    True:  success
	//    False: something went wrong.  Research not added
    /////////////////////////////////////////////////////

	function research ($key) 
	{
		if ($this->guideMode)
		{
			$this->guideScience[] = $key;
			return true;
		}
		
		if (array_key_exists($key, $this->scienceTree)) {
			if ($this->pID && !$this->researching) {
				if ($this->province->useResource($this->scienceTree[$key]->getCostGold(),
											     $this->scienceTree[$key]->getCostMetal(),
											      0)==false) return false;
				$this->effectObj = new Effect( $this->database );
				$this->database->query("INSERT INTO Science (pID,sccID,ticks) VALUES ('$this->pID','".
										$this->scienceTree[$key]->getID()."','".
										round($this->scienceTree[$key]->getTicks()* $this->effectObj->getEffect($GLOBALS['effectConstants']->ADD_RESEARCH_TIME,$this->pID)).
										"')");
				// spend gold, metal, food
				$this->researching['sccID'] = $key;
				$this->researching['pID'] = $this->pID;$this->scienceTree[$key]->getTicks();
				$this->researching['ticks'] = round($this->scienceTree[$key]->getTicks()* $this->effectObj->getEffect($GLOBALS['effectConstants']->ADD_RESEARCH_TIME,$this->pID));
				return true;
			}
		}
		return false;
	}
	
	
    /////////////////////////////////////////////////////
	// string Science::showAviableSciences ()
    /////////////////////////////////////////////////////
	// 
	// Builds up small boxes with description of the science
	// also include a form so that science can be researched.
	//
	// Returns:
	//    html formatted string
	//
    /////////////////////////////////////////////////////

	function showAviableSciences () {
		// find sciences from the tree that we dont already have but that we are allowed to research.
		// traversee tree
		$html = "";
		$arr = $this->getAviableSciences();
		if (is_array($arr)) {
			$this->effectObj = new Effect( $this->database );
			//;
			$html .= "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\">";
			$html .= $GLOBALS['fcid_post'];
			$html .= "<input type=hidden name=\"research\" value=\"true\">";
			$html .= "<center><table border='0' cellspacing='1' cellpadding='2' width=\"*\">";
			reset ($arr);
			$html .= "<tr><td class='rep3' colspan=2 width=\"400\">You can research</td><td class='rep3' width=\"100\">Cost</td><td class='rep3' width=\"50\">Days to research</td><td class='rep3'>&nbsp</td></tr>";
			foreach ($arr as $value) {
				if ($this->guideMode == false)
				{
					$time = round($value->getTicks() * $this->effectObj->getEffect($GLOBALS['effectConstants']->ADD_RESEARCH_TIME,$this->pID));
				}
				else
					$time = $value->getTicks();
				
				// Hack to only show the sciences that belong to races except Giants. City planning and Theology - Soptep: 17/01/2010	
				if ( ($value->getID() == 23 || $value->getID() == 24) && @$this->province->spID != 11) {
					continue;
				}
				
				// Hack so post don't send numeric values, "a" needs to be removed at science.php
				$name = $value->getID()."a";
				
					
				$html .= "<tr'>";
				$html .="<td class='rep1'>" . $value->getName() . " (". $value->getCategory().")</td>";
				$html .="<td class='rep1'>". $value->getDescription() ."</td>";
				$html .="<td class='rep1' align='right'>" .number_format($value->getCostGold(),0,' ',',') ." gc<br>".number_format($value->getCostMetal(),0,' ',',')." metal</td>";
				$html .="<td class='rep1' align='right'>". $time ."</td>";
				$html .="<td class='rep1'><input type=submit class='form' name=\"". $name. "\" value='research' title='Click to research this knowledge'></td>";
				$html .= "</tr>";
			}
			$html .= "</table></center>";
			if ($this->guideMode == true)
			{
				reset($this->guideScience);
				foreach ($this->guideScience as $fake)
				{
					$html .= "<input type=hidden name=guide_science[] value=$fake>\n";
				}
			}
			$html .= "</form>";
		} else {
			$html .= "<br>&nbsp<center><table><tr><td>There is nothing to research.</td></tr></table></center>";
		}
		return $html;	
	}

	function percentToFloat ($number) {
		return (float) 1 + ((float) $number/100.0);
	}
    /////////////////////////////////////////////////////
	// float getScienceEffect ($type)
    /////////////////////////////////////////////////////
	// 
	// gets the effect for type.  Type is defined in this file.
	//
	// returns:
	//    float value with the effect.
    /////////////////////////////////////////////////////
	function getScienceEffect($FUNCTION_FROM_EFFECT_CONSTANTS, $pID) {
		// load for province.
		//
		$this->pID = $pID;
		$this->getProvinceScience();
		
		$modifier = 1.00;
		if (is_array($this->provinceScience)) {
			reset($this->provinceScience);
			foreach ($this->provinceScience as $science) {
//				$modifier *=(float) $this->scienceTree[$science['sccID']]->getScienceEffect($type);
				$modifier *= $this->percentToFloat( $this->scienceTree[$science['sccID']]->$FUNCTION_FROM_EFFECT_CONSTANTS() );
//				$modifier *= $this->percentToFloat($this->scienceTree[$science['sccID']]->addInfluence());
//				$this->scienceTree[$science['sccID']]->addInfluence();
			}
		
		}
//		echo "$modifier";
		return (float)$modifier;
	}

        /////////////////////////////////////////////////////
	// bool Science::scienceReqOk ($req)
        /////////////////////////////////////////////////////
	// 
	// chekcs if a province has enough science to fullfill
	// the array req.
	//
	// Returns:
	//    true:  ok or empty array.
	//    false: not enough
	//
        /////////////////////////////////////////////////////
	function scienceReqOk ($req, $pID=false) {
		if ($pID) {
			$this->pID = $pID;
			$this->getProvinceScience();
		}

		if (is_array($req)) {		
			if ($req['military'] && !($req['military'] & $this->myScienceLevel['military'])) return false;
			if ($req['infrastructure'] && !($req['infrastructure'] & $this->myScienceLevel['infrastructure'])) return false;
			if ($req['magic'] && !($req['magic'] & $this->myScienceLevel['magic'])) return false;
			if ($req['thievery'] && !($req['thievery'] & $this->myScienceLevel['thievery'])) return false;
	      	return true;
	   } else return true;
	}

        /////////////////////////////////////////////////////
	// string Science:: getScienceAge ()
        /////////////////////////////////////////////////////
	// 
	// calculates the age of all the sciences.
	//
	// Returns:
	//    Calculated age
	//
        /////////////////////////////////////////////////////
	function getScienceAge() {
		$totalSciences=-1;
		$mySciences=0;  // because of endgame science.
		reset($this->scienceTree); foreach($this->scienceTree as $c) {$totalSciences++;}
		reset($this->provinceScience); foreach($this->provinceScience as $c) {$mySciences++;}
		$completion = (int) (($mySciences/$totalSciences)*100);
/*		if ($completion<10) {
			return "Stone age";
		} else if ($completion<20) {
			return "";
		}*/
		return $completion . "%";
	}

} // end of class


}
?>