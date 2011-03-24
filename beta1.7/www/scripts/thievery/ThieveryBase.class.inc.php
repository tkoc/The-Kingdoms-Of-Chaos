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
/* 
 * 
 * Author: Anders Elton 23.04.2003
 * 
 *  
 * Version: 1.1
 *
 * Changelog:
 * 23.02.04 Rewrote baseclass. (removed parameters in constructor)
 			* added difficulity
			* 
 */

if( !class_exists("ThieveryBase") ) {
require_once( $GLOBALS['path_www_scripts'] . "effect/EffectBase.class.inc.php" );

class ThieveryBase extends EffectBase {
	var $thID;			// id
	var $name;			// op name
	var $description;
	var $requires;		// science requirements.
	var $costInfluence= 3;	
	var $difficulity =  0; //[-20] - [20]  ([hard] - [easy])
	var $thieveryLoss = 5;  // 5% base
	var $reputationLoss = 1;  // each op gives 1 reputation default.
	var $optimalThieves = -1; // -1 means all thieves.
	var $MaxStack = 1;

	////////////////////////////////////////////
	// ThieveryBase::ThieveryBase (...)
	//
	// sets class variables according to input.
	////////////////////////////////////////////
	function ThieveryBase($inThID, $inName, $inDescription,$inReq) {
		$this->thID = $inThID;
		$this->name = $inName;
		$this->description = $inDescription;
		$this->requires = $inReq;
	}
	
	////////////////////////////////////////////
	// float Thievery::make_seed()
	////////////////////////////////////////////
	// returns a seed used in random generators
	////////////////////////////////////////////
	function make_seed() {
    		list($usec, $sec) = explode(' ', microtime());
    		return (float) $sec + ((float) $usec * 100000);
	}

	////////////////////////////////////////////
	// string Thievery::thieveryEffect($province, $myTpa, $victimTpa, $victimProvince)
	////////////////////////////////////////////
	// Parameters:
	//    $province: a province object of the source
	//    $victimProvince: province object of the victim
	//
	// NOTE!  all province object are required to have loaded
	// getMilitaryData - having a valid milObject.
	////////////////////////////////////////////
	function thieveryEffect ($province,$victimProvince) {
		$vRep = $victimProvince->getReputation();
		$mRep = $province->getReputation();
		
		if ($vRep>0){
			if ($mRep==0)
				$mRep = 1;
			//$gainRep = floor ( (($mRep/$vRep) +1 ) * $this->reputationLoss);
			$gainRep = floor ( $this->reputationLoss );
			
			$province->updateReputation($gainRep);
			$victimProvince->updateReputation(-$gainRep);
			
			return $gainRep;
		} else {
			return 0;
		}
	}


	function thieveryFailure ($province,$victimProvince)
	{
		$enemy_thieves = $victimProvince->milObject->getMilUnit($GLOBALS['MilitaryConst']->THIEVES);
		$thieves = $province->milObject->getMilUnit($GLOBALS['MilitaryConst']->THIEVES);
		if ($thieves['num'] < 1)
			$thieves['num'] = 1;
		if ($enemy_thieves['num'] < 1)
			$enemy_thieves = 1;
		$ratio = ( $enemy_thieves['num'] / $thieves['num'] );
//		$ratio = 1;
		if ($ratio > 1)
			$ratio = 1;

		$ratio = $ratio * 0.05;

		$optimal = $this->getOptimalThieves($province,$victimProvince);
        $effect = new Effect( $province->database );
//		if ($this->hasSmartThieves($province)==true) echo "you have smart thieves";
//		else echo "you have not smart thieves";
		if ( ($optimal>0) &&  ($optimal<$thieves['num']) && ($this->hasSmartThieves($province)==true)) {
			$lost = (floor( $effect->getEffect( $GLOBALS['effectConstants']->ADD_THIEVERY_LOSS,$province->getpID() ) *  ($ratio*$optimal) ))+1;
			$txt = "The operation was a failure.  Fortunately we did not send more thieves than we needed!";
		} else {
	        $lost = (floor( $effect->getEffect( $GLOBALS['effectConstants']->ADD_THIEVERY_LOSS,$province->getpID() ) *  ($ratio*$thieves['num']) )) +1;
			$txt = "The operation was a failure.  If we are lucky our thieves wont get caught!";
		}
        $txt .= " we also lost $lost thieves in the operation";
		$province->milObject->killUnits($GLOBALS['MilitaryConst']->THIEVES,$lost );
		if (mt_rand(1,3)!=2)
			$victimProvince->postNews("we have found thieves from ". $province->provinceName ."(".$province->kiId.") in our Province.  They were all executed." );
		else 
			$victimProvince->postNews("we have found thieves causing trouble in our Province.  Unfortunately they were all executed without revealing important information." );
		return '<CENTER>'.$txt . '</CENTER>';
	}
	////////////////////////////////////////////
	// bool Thievery::doTick(&$database)
	////////////////////////////////////////////
	//
	// preforms global update for the thievery op.
	// (currently not in use, just for future)
	////////////////////////////////////////////

	function doTick(&$database) {
		return 0;
	}
	////////////////////////////////////////////
	// ThieveryBase::getXxx
	////////////////////////////////////////////
	// Various functions to get the info recieved in the constructor
	// Returns:
	//    value recieved by constructor
	////////////////////////////////////////////
	function getDifficulity()
	{
		return $this->difficulity;  // -20 hardest, 20 easiest.
	}
	function getID() {
		return $this->thID;
	}
	function getName() {
		return $this->name;
	}
	function getDescription() {
		return $this->description;
	}
	function getRequires () {
		return $this->requires;
	}
	
	
	// the optimal thief number for this mission.
	// -1 means everyone.
	function getOptimalThieves ($province,$victimProvince)
	{
		return $this->optimalThieves;  // -1
	}
	
	function hasSmartThieves ($province)
	{
		$effect = new Effect( $province->database );
		if ($effect->getEffect( $GLOBALS['effectConstants']->SMART_THIEVES,$province->getpID()) > 1.0 )
			return true;
		else return false;
	}
	
	function randomPercent ($percent)
	{
		return (( mt_rand( (-1*$percent),$percent) /100)+1);
	}

	function CanAddLastingOperation ($province,$victimProvince)
	{
		$province->database->query("SELECT count(*) as num from ActiveThieveryOps where pID='".$victimProvince->pID."' AND thieveryOperationID='".$this->thID."'");
		$res = $province->database->fetchArray();
//		$province->database->showDebugData();
		if ($res['num'] >= $this->MaxStack)
		{
			return false;
		}
		return true;
	}

	function AddLastingOperation($province, $victimProvince, $ticks)
	{
		if ($this->CanAddLastingOperation($province, $victimProvince) == false)
		{
			die (" Programming error. "); 
			return false;
		}

		$province->database->query("INSERT INTO ActiveThieveryOps ( doneByID,pID, ticks, thieveryOperationID) VALUES ('".$province->pID."', '".$victimProvince->pID."', '$ticks','".$this->thID."')");
//		$province->database->showDebugData();
		return true;
	}
}

} // end if( !class_exists() )
?>
