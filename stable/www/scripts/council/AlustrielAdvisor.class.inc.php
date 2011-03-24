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



if( !class_exists("AlustrielAdvisor") ) {

require_once("CouncilBase.class.inc.php");

//require_once("/home/tkoc.net/scripts/all.inc.php");

require_once($GLOBALS['WWW_SCRIPT_PATH']. "Province.class.inc.php");

class AlustrielAdvisor extends CouncilBase {

	var $addedMagicChance 	= 10;

	var $addedMagicProtection = 10;





	// inherited variables.

	var $councilName = "Lady Alustriel";

	var $councilHistory = "Lady Alustriel has just reached the highest rank you can get as  

an elven spellcaster. Using magic is as natural to 

			her as speech is to humans. She favors to use offensive spells to wear her targets down, before striking swiftly to serve justice. 

			

		";

	var $costToHire = array('gold' =>1500000, "metal" => 0, "food" => 0, "peasants" => 0);



	var $requires=array('military' =>0, "infrastructure" => 0, "magic" => 0, "thievery" => 0);		// science requirements.

	var $races =array("Elf", "Human", "Dwarf");

	var $image = "../img/Leftpictures/Council_leftpicture3.jpg";	// other varibles.



	function BasicAdvisor ($coID) 

	{

		$this->CouncilBase ($coID);

	}



	function addMagicChance($province=NULL) {

		return $this->addedMagicChance;

	}

	function addMagicProtection($province=NULL) {

		return $this->addedMagicProtection;

	}

	

	function introduction () {

		$html =/* $this->provinceObj->getAdvisorName() . */"I am $this->councilName.<br>$this->councilHistory";

	}

	function showMood() {

		// switch med moods.

		$html = "<center>".$this->councilName . " smiles as you apporach her.</center><br>&nbsp;";

		return $html;

	}

	

	/*
	function getBasicCouncil () {

		

		return "<center><table>

				<tr><td><b>Resources</b></td><td><b>Last day</b></td><td><b>This month</b></td></tr>

				<tr><td>Gold: </td><td>".$this->writeChange($this->provinceObj->incomeChange)."</td><td>".$this->writeChange($this->provinceObj->incomeTotal)."</td></tr>

				<tr><td>Food: </td><td>".$this->writeChange($this->provinceObj->foodChange)."</td><td>".$this->writeChange($this->provinceObj->foodTotal)."</td></tr>

				<tr><td>Metal: </td><td>".$this->writeChange($this->provinceObj->metalChange)."</td><td>".$this->writeChange($this->provinceObj->metalTotal)."</td></tr>

				<tr><td>Peasant growth: </td><td>".$this->writeChange($this->provinceObj->peasantChange)."</td><td>".$this->writeChange($this->provinceObj->peasantTotal)."</td></tr>

			</table></center>

		";

	}*/

	function getMilitaryCouncil () {

		$html = parent::getMilitaryCouncil();

		if ($this->provinceObj->peasants > $this->provinceObj->militaryPopulation)

			$html .= "It's time to attack and expand our lands!<br>";

		if ($this->provinceObj->peasants < 100)

			$html .= "We need more workers to get metal!<br>";

		return $html;

	}

	function getThieveryCouncil () {

		$html = "";

		if ($this->provinceObj->getTpa() < 1)

			$html .= "We need more spies to infiltrate our enemies!<br>";

/*		$thieveryRank = $this->provinceObj->getReputationRank();

		if ($thieveryRank<10)

			$html .="Our splendid thieves currently enjoy the reputation of being the $thieveryRank best thief-guild in the world";

		else 

			$html .= "Our thieves currently has the reputation of being the $thieveryRank best thief-guild in the world";

			*/

		return $html;

	}

	function getMagicCouncil () {

		return "";

	}

/*        function getCouncil() {

			$html = "";

                $this->provinceObj->getProvinceData();

                $this->provinceObj->database->query("select * from ScienceCat right join Science on Science.sccID=ScienceCat.sccID where className='EndGameScience'");

                if ($this->provinceObj->database->numRows()) {

                        $endGameData = $this->provinceObj->database->fetchArray();

                        $html = "<CENTER>" . $this->provinceObj->getAdvisorName() . ", there is a madman running about telling people about the Apocalypse..";

                        $html .= "<br>The prophecy says the world will end in about " . max(($endGameData['ticks'] + 40 - rand(0,100)),0) . " days!</CENTER>";

                }

                $html .= $this->getBasicCouncil();

                $html .= $this->getMilitaryCouncil();

                $html .= $this->getThieveryCouncil();

                $html .= $this->getMagicCouncil();

                $html .= $this->getKnowledgeCouncil();

                $html .= $this->getSpecialCouncil();

                return $html;

        }



*/

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
