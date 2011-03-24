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
// this advisor is for all.

if( !class_exists("BasicAdvisor") ) {
require_once("CouncilBase.class.inc.php");
//require_once("/home/tkoc.net/scripts/all.inc.php");
require_once($GLOBALS['WWW_SCRIPT_PATH']. "Province.class.inc.php");
class BasicAdvisor extends CouncilBase {
	var $councilName = "Lady Brienne";
	var $councilHistory ='Lady Brienne is a highborn scholar, currently in university. She makes good reports, 
	and deals best with local trade. She is not trained in the art of war, and only knows the basics of thievery 
	and magic. She won\'t provide you with new knowledge, but she might come with research related suggestions if she feel it is necessary. ';

	var $costToHire = array('gold' =>0, "metal" => 0, "food" => 0, "peasants" => 0);
		
	var $requires=array('military' =>0, "infrastructure" => 0, "magic" => 0, "thievery" => 0);		// science requirements.
	var $races = array("Dwarf","Human","Elf","Orc","Undead", "Giant");			// array with race id(s).
	var $image = "../img/Leftpictures/Council_leftpicture.jpg";
	
	function BasicAdvisor ($coID)
	{
		$this->CouncilBase ($coID);
	}
	function introduction () {
		$html =/* $this->provinceObj->getAdvisorName() . */"I am $this->councilName.<br>$this->councilHistory";
	}
	function showMood() {
		// switch med moods.
		$html = "<center>You find ".$this->councilName . " just where you expected her to be -- in the marketplace counting coin, 
		and looking over the local trade.  She looks content when she hands over her reports for today.</center><br>&nbsp;";
		return $html;
	}
	
	function getBasicCouncil () {
		
		return parent::getBasicCouncil();
	}
	function getMilitaryCouncil () {
		return parent::getMilitaryCouncil();
	}
	function getThieveryCouncil () {
		return "";
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
