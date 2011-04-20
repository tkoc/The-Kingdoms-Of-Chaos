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

// this advisor is for all.

if( !class_exists("ArrkAdvisor") ) {
require_once("CouncilBase.class.inc.php");
//require_once("/home/tkoc.net/scripts/all.inc.php");
require_once($GLOBALS['WWW_SCRIPT_PATH']. "Province.class.inc.php");
class ArrkAdvisor extends CouncilBase {
	// inherited variables.
	var $councilName = "Arrk Maneater";
	var $councilHistory = "Arrk Maneater is bright considering that he is a troll. Arrk, however, usually likes to say that being dumb is not necesarrily 
	a bad thing: --you can't go around thinking all day if you want to get things done. He has eaten smart (and tasty) people, and it usually 
	ends up with them trying to 'think' their way out of his mouth..";
	var $upkeep = array('gold' =>0, "metal" => 0, "food" => 0, "peasants" => 0);
	var $costToHire = array('gold' =>1500000, "metal" => 0, "food" => 0, "peasants" => 0);

	var $requires=array('military' =>0, "infrastructure" => 0, "magic" => 0, "thievery" => 0);		// science requirements.
	var $races =array("Orc","Undead", "Giant");
	var $image = "../img/Leftpictures/Council_leftpicture5.jpg";	// other varibles.

	function BasicAdvisor ($coID) 
	{
		$this->CouncilBase ($coID);
	}
	
	function addAttack($province=NULL) {
		return 8;
	}
	
	
	function introduction () {
		$html =/* $this->provinceObj->getAdvisorName() . */"I am $this->councilName.<br>$this->councilHistory";
	}
	function showMood() {
		// switch med moods.
		$html = "<center>".$this->councilName . " grunts at you as you approach him.</center><br>&nbsp;";
		return $html;
	}
	
/*	function getBasicCouncil () {
		
		return "<center><table>
				<tr><td><b>Resources</b></td><td><b>Last day</b></td><td><b>This month</b></td></tr>
				<tr><td>Shiny things: </td><td>".$this->writeChange($this->provinceObj->incomeChange)."</td><td>".$this->writeChange($this->provinceObj->incomeTotal)."</td></tr>
				<tr><td>Taste good: </td><td>".$this->writeChange($this->provinceObj->foodChange)."</td><td>".$this->writeChange($this->provinceObj->foodTotal)."</td></tr>
				<tr><td>Taste bad: </td><td>".$this->writeChange($this->provinceObj->metalChange)."</td><td>".$this->writeChange($this->provinceObj->metalTotal)."</td></tr>
				<tr><td>Peasant growth: </td><td>".$this->writeChange($this->provinceObj->peasantChange)."</td><td>".$this->writeChange($this->provinceObj->peasantTotal)."</td></tr>
			</table></center>
		";
	}*/
	function getMilitaryCouncil () {
		$html = parent::getMilitaryCouncil();
		if ($this->provinceObj->peasants > $this->provinceObj->militaryPopulation)
			$html .= "Train your peasants into soldiers!  Women and children will not bring us glory!<br>";
		if ($this->provinceObj->peasants < 1000)
			$html .= "We need more workers to get shiny things!<br>";
		return $html;
	}
	function getThieveryCouncil () {
		$html = "";
	
		if ($this->provinceObj->getTpa() < 1)
			$html .= "We need more spies!  They know where we can find food..<br>";
/*		$thieveryRank = $this->provinceObj->getReputationRank();
		if ($thieveryRank<10)
			$html .="Our splendid thieves currently enjoy the reputation of being the $thieveryRank best thief-guild in the world.";
		else 
			$html .= "Our thieves currently has the reputation of being the $thieveryRank best thief-guild in the world";
*/
		return $html;
	}
	function getMagicCouncil () {
		return "";
	}
	function getSpecialCouncil () {
		return "<center><table>
				<tr><td colspan=2 align=CENTER><b>Population</b></td></tr>
				<tr><td>Peasants: </td><td>".$this->writeNumber($this->provinceObj->peasants)."</td></tr>
				<tr><td>Military: </td><td>".$this->writeNumber($this->provinceObj->militaryPopulation)."</td></tr>
				<tr><td>Sum: </td><td>".$this->writeNumber($this->provinceObj->population)."</td></tr>
				<tr><td>Max population: </td><td>".$this->writeNumber($this->provinceObj->buildingPeasantPopulation)."</td></tr>
			</table></center>
		";
	}

}






}



?>