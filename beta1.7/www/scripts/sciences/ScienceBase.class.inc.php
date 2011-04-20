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

/* Science class to be extended by all sciences. Contains all functionality
 * available to a science.
 * 
 * Author: Anders Elton 01.04.2003
 * 
 *  
 * Version: test
 *
 * Changelog:
 * 23.04.03: Added getScienceEffect. (always returs 1) 
 */

if( !class_exists("ScienceBase") ) {

require_once (WWW_SCRIPT_PATH . "effect/EffectBase.class.inc.php" );

class ScienceBase extends EffectBase {
	var $scID;			// id
	var $name;			// science name
	var $ticks;			// number of ticks to research
	var $costGold;		// the cost of this science in gold
	var $costMetal;		// the cost of this science in metal
	var $description;
	var $category = "not set";		// what categoy does it belong to?
	var $networth = 1000;
				// magic, thievery, military, infrastructure
	
	// the next two arrays is in the form $requires['military']=2; $requires['thievery']=0;
	// bit values are used!!! 1 | 2 | 4 | 8 | 16 | 32 | 64 ...

	
	var $requires= NULL;  	//array! what kind of levels are required
	var $scienceLevel=NULL;	//array! this research also gives levels..
	
	function ScienceBase( $inScID,$inCat, $inName, $inTicks, $inCostGold, $inCostMetal, $inDescription,$inReq, $inGives ) {
		$this->scID = $inScID;
		$this->name = $inName;
		$this->ticks = $inTicks;
		$this->costGold = $inCostGold;
		$this->costMetal = $inCostMetal;
		$this->description = $inDescription;
		$this->requires = $inReq;
		$this->scienceLevel = $inGives;
		$this->category = $inCat;
	}
	
	////////////////////////////////////////////
	// BuildingBase::getXxx
	////////////////////////////////////////////
	// Various functions to get the info recieved in the constructor
	// Returns:
	//    value recieved by constructor
	////////////////////////////////////////////
	function getNW()
	{
		return $this->networth;
	}
	function getID() {
		return $this->scID;
	}
	function getName() {
		return $this->name;
	}
	function getCostGold() {
		return $this->costGold;
	}
	function getCostMetal() {
		return $this->costMetal;
	}
	function getTicks() {
		return $this->ticks;
	}
	function getDescription() {
		return $this->description;
	}
	function getCategory() {
		return $this->category;
	}
	function getRequires () {
		return $this->requires;
	}
	function getLevel () {
		return $this->scienceLevel;
	}
	function doTick(&$database) {
		return 0;
	}
}

} // end if( !class_exists() )
?>