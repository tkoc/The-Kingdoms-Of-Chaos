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
 * Author: Anders Elton 
 * 
 *  
 * Version: test
 *
 * Changelog:
 */

if( !class_exists("BeastBase") ) {

require_once (WWW_SCRIPT_PATH . "effect/EffectBase.class.inc.php" );

class BeastBase extends EffectBase{
	var $bID;			// id
	var $provinceObj;
	var $kingdomID = -1;

	var $beastName = "The Beast Name";
	var $attacktodamage = 0.002; // attack power required to damage 1%.  multiplied by kingdom nw.
	var $goldCost = 5;
	var $foodCost = 5;
	var $metalCost = 5;
	var $strength = 100;
	var $senderID = -1;
	var $foodLeft = 0;
	var $metalLeft = 0;
	var $goldLeft = 0;
	var $beastID = -1;


		
	var $image = "../img/Leftpictures/Council_leftpicture.jpg";
	var $callbackMessage = "todo: one of the admins forgot something...";	
	////////////////////////////////////////////
	// ThieveryBase::ThieveryBase (...)
	//
	// sets class variables according to input.
	////////////////////////////////////////////
	function BeastBase($inID) 
	{
		$this->bID = $inID;
	}
	
	function InvestResource($gold, $metal, $food)
	{
		$GLOBALS['database']->query("UPDATE Beast set goldLeft=GREATEST(0, goldLeft-$gold),
							metalLeft=GREATEST(0, metalLeft-$metal),
							foodLeft=GREATEST(0,foodLeft-$food) WHERE ID='".$this->beastID."'");
	}
	
	function SetData($in)
	{
		$this->beastID = $in['ID'];
		$this->kingdomID = $in['kiID'];
		$this->goldLeft = $in['goldLeft'];
		$this->metalLeft = $in['metalLeft'];
		$this->foodLeft = $in['foodLeft'];
		$this->strength = $in['strength'];
		$this->senderID = $in['senderID'];
	}
	function IsCompleted()
	{
		return ($this->goldLeft + $this->foodLeft + $this->metalLeft) == 0;
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
	
	function OnThieveryAction($result, $province)
	{
		return $result;
	}	
	function OnThieved($org, $province)
	{
		return $org;
	}

	////////////////////////////////////////////
	// bool Council::doTick(&$database)
	////////////////////////////////////////////
	//
	// preforms global update for the thievery op.
	// (currently not in use, just for future)
	////////////////////////////////////////////

	function doTick(&$database) {
		// if other beasts are in this kingdom, damage them!
		$database->query("update Beast set strength=strength-2 where ID!=$this->beastID AND kiID=$this->kingdomID AND (Beast.goldLeft=0 AND Beast.foodLeft=0 AND Beast.metalLeft=0)");
		return 0;
	}
	////////////////////////////////////////////
	// ThieveryBase::getXxx
	////////////////////////////////////////////
	// Various functions to get the info recieved in the constructor
	// Returns:
	//    value recieved by constructor
	////////////////////////////////////////////
	function introduction () {
		return "";
	}
	function setProvince($provinceObj) {
		$this->provinceObj = $provinceObj;
	}

	function getImage ()
	{
		return $this->image;
	}
	function getID() {
		return $this->bID;
	}
	function getName() {
		return $this->beastName;
	}
	
}

} // end if( !class_exists() )
?>
