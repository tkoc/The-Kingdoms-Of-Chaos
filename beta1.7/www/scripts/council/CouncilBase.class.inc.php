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

/* 
 * 
 * Author: Anders Elton 
 * 
 *  
 * Version: test
 *
 * Changelog:
 */

if( !class_exists("CouncilBase") ) {

require_once (WWW_SCRIPT_PATH . "effect/EffectBase.class.inc.php" );

class CouncilBase extends EffectBase{
	var $coID;			// id
	var $provinceObj;

	var $councilName = "No name";
	var $councilHistory = "No history";
	var $costToHire = array('gold' =>0, "metal" => 0, "food" => 0, "peasants" => 0);
	var $upkeep		= array('gold' =>0, "metal" => 0, "food" => 0, "peasants" => 0);
		
	var $requires=array('military' =>0, "infrastructure" => 0, "magic" => 0, "thievery" => 0);		// science requirements.
	var $races = array("Dwarf","Human","Elf","Orc", "Undead", "Giant");			// array with race id(s).
	var $image = "../img/Leftpictures/Council_leftpicture.jpg";
	var $callbackMessage = "todo: one of the admins forgot something...";	
	////////////////////////////////////////////
	// ThieveryBase::ThieveryBase (...)
	//
	// sets class variables according to input.
	////////////////////////////////////////////
	function CouncilBase($inID) 
	{
		$this->coID = $inID;
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

	function OnAssasination($province)
	{
		return false;
	}
	////////////////////////////////////////////
	// bool Council::doTick(&$database)
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
	function introduction () {
		return "";
	}
	function setProvince($provinceObj) {
		$this->provinceObj = $provinceObj;
	}
	function getCouncil() {
		$html = "";
//		$this->provinceObj = $provinceObj;
		$this->provinceObj->getProvinceData();
        $this->provinceObj->database->query("select * from ScienceCat right join Science on Science.sccID=ScienceCat.sccID where className='EndGameScience'");
        if ($this->provinceObj->database->numRows()) 
		{
        	$endGameData = $this->provinceObj->database->fetchArray();
            $html = "<CENTER>" . $this->provinceObj->getAdvisorName() . ", there is a madman running about telling people about the Apocalypse..";
            $html .= "<br>The prophecy says the world will end in about " . abs(max(($endGameData['ticks'] + 40 - rand(0,100)),0)) . " days!</CENTER>";
        }
		$html .= "<table><tr valign=top><td colspan=2><center>";
		$html .= $this->getSeasonCouncil() . "</center>";
		$html .= "</td></tr><tr valign=top>";
		$html .= "<td valign=top>";
		$html .= $this->getBasicCouncil();
		$html .= $this->getMilitaryCouncil();
		$html .= $this->getThieveryCouncil();
		$html .= $this->getMagicCouncil();
		$html .= $this->getKnowledgeCouncil();
		$html .= $this->getSpecialCouncil();
		$html .= $this->showRankCouncil();
		$html .= "</td><td valign=top>";
		$html .= $this->getBonusCouncil();
		$html .= "</td></tr></table>";
		
		return $html;
	}
	function showMood () {
		return "Content";
	}
	function getBasicCouncil () {
		
		return "<center><table>
				<tr ALIGN=RIGHT><td><b>Resources</b></td><td><b>Last day</b></td><td><b>Expenses</b></td><td><b>This month</b></td></tr>
				<tr ALIGN=RIGHT><td>Gold: </td><td>".$this->writeChange($this->provinceObj->incomeChange)."</td><td>".$this->writeChange(-$this->provinceObj->goldExpenses)."</td><td>".$this->writeChange($this->provinceObj->incomeTotal)."</td></tr>
				<tr ALIGN=RIGHT><td>Food: </td><td>".$this->writeChange($this->provinceObj->foodChange)."</td><td>".$this->writeChange(-$this->provinceObj->foodExpenses)."</td><td>".$this->writeChange($this->provinceObj->foodTotal)."</td></tr>
				<tr ALIGN=RIGHT><td>Metal: </td><td>".$this->writeChange($this->provinceObj->metalChange)."</td><td>".$this->writeChange(-$this->provinceObj->metalExpenses)."</td><td>".$this->writeChange($this->provinceObj->metalTotal)."</td></tr>
				<tr ALIGN=RIGHT><td>Peasant growth: </td><td>".$this->writeChange($this->provinceObj->peasantChange)."</td><td>&nbsp;</td><td>".$this->writeChange($this->provinceObj->peasantTotal)."</td></tr>
			</table></center>
		";
	}
	function getMilitaryCouncil () {
		return "<center><table>
				<tr><td> Attacks Made:</td><td><b>".$this->provinceObj->attackMade."</b> (<i>".$this->provinceObj->attackWins." wins</i>)</td></tr>
				<tr><td> Attacks Suffered:</td><td><b>".$this->provinceObj->attackSuffered."</b> (<i>".$this->provinceObj->attacksSufferedLost." lost</i>)</td></tr>
				</table>
				</center>";
	}
	function getThieveryCouncil () {
		return "";
	}
	function getMagicCouncil () {
		return "";
	}
	function getKnowledgeCouncil () {
		return "";
	}
	function getSpecialCouncil () {
		return "";
	}

	function getSeasonCouncil() {
		return "The current season is " . $GLOBALS['CurrentSeason']->Name . ".  There is ".$GLOBALS['CurrentSeason']->SeasonTick." days until the season changes";
	
	}

	
	function ordinalize($number) {
		if (in_array(($number % 100),range(11,13))){
			return $number.'th';
      		}
		else{
      			switch (($number % 10)) {
      				case 1:
      					return $number.'st';
      					break;
      				case 2:
      					return $number.'nd';
      					break;
      				case 3:
      					return $number.'rd';
      				default:
      					return $number.'th';
     					break;
      			}
      		}
	}


	function showRankCouncil()
	{
		$html = "<center><br><table><tr><td>";
		// networth
		$nwRank = $this->provinceObj->getNetworthRank();
		$nwRank1 = (int)$nwRank;
		$ordinalized_number = $this->ordinalize($nwRank1);
		if ($nwRank<10)
			$html .="Our powerful land is ranked $ordinalized_number in the world";
		else 
			$html .= "Our land is ranked as the $ordinalized_number most powerful nation in the world";
		$html .= "</td></tr><tr><td>";
		// acres
		$acresRank = $this->provinceObj->getAcreRank();
		$acresRank1 = (int)$acresRank;
		$ordinalized_number = $this->ordinalize($acresRank1);
		if ($acresRank<10)
			$html .="Our great province is the $ordinalized_number largest in the world";
		else 
			$html .= "Our province is the $ordinalized_number largest in the world.";
		$html .= "</td></tr><tr><td>";
		
		// thievery
		$thieveryRank = $this->provinceObj->getReputationRank();
		$thieveryRank1 = (int)$thieveryRank;
		$ordinalized_number = $this->ordinalize($thieveryRank1);
		if ($thieveryRank<10)
			$html .="Our splendid thieves currently enjoys the reputation of being the $ordinalized_number best thief-guild in the world";
		else 
			$html .= "Our thieves currently have the reputation of being the $ordinalized_number best thief-guild in the world";
		$html .= "</td></tr><tr><td>";
		// magic
		$wizardRank = $this->provinceObj->getMagicRepRank();
		$wizardRank1 = (int)$wizardRank;
		$ordinalized_number = $this->ordinalize($wizardRank1);
		if ($wizardRank<10)
			$html .="Our famous wizards currently enjoys the reputation of being the $ordinalized_number best wizards in the world";
		else 
			$html .= "Our wizards currently have the reputation of being the $ordinalized_number best wizard-guild in the world";
		$html .= "</td></tr><tr><td>";
		$militaryRep = $this->provinceObj->getMilitaryExperience();
		$expRank = $this->provinceObj->getExperienceRepRank();
		$expRank1 = (int)$expRank;
		$ordinalized_number = $this->ordinalize($expRank1);
		$html .= "Our army currently enjoys the reputation of being the $ordinalized_number most experienced army in the world"; // Removed: "($militaryRep experience)"
		$html .= "</td></tr><tr><td>";
		
		
		//Allrank by tasosos///////////////////////////////////////////////////////////////////////////////////////////////////
		$allRank = $this->provinceObj->getAllAvgRank();
		$allRank1=(int)$allRank;
		$ordinalized_number = $this->ordinalize($allRank1);
		//$this->provinceObj->getAllAvgRank();
		if ($allRank ==1)
			$html .="Our perfect province is the best on average at the moment.";
		else if ($allRank < 10)
			$html .="Our glorious province is the $ordinalized_number best on average at the moment";
		else
			$html .= "Our province is the $ordinalized_number best on average at the moment";
		
		////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$html .= "</td></tr><tr>";

		$html .= "</tr>";
		$html .= "<tr><td>";
                
		//$html .= '<form action="'.$_SERVER['PHP_SELF'].'" METHOD=POST>';
                //$html .= $GLOBALS['fcid_post'];
                //$html .= '<input type=hidden name=step value=thieveryrank>';
		//$html .= '<input type=submit value="Make thievery rank public">';
		//$html .= '</form>';
		
		$html .= "</td></tr>";
		$html .= "</table>";

		
		return $html . "</center>";
	}
	
	function getBonusCouncil()
	{
		$eff = &$this->provinceObj->effectObj;
		$pid = $this->provinceObj->pID;
		$magic_prot = round(($eff->getEffect($GLOBALS['effectConstants']->ADD_MAGIC_PROTECTION,$pid)-1)*100,2) + round(($eff->getEffect($GLOBALS['effectConstants']->ADD_MAGIC_RESISTANCE,$pid)-1)*100,2);
		
		
		// Hack to show the king bonus at the council page - Soptep: 11/01/2010
		if ($this->provinceObj->isKing()) {
			$maxKingBonus = 115; //15% later
			$minKingBonus = 102;
			
			$this->provinceObj->database->query("Select * From Config");
			$config = $this->provinceObj->database->fetchArray();
			$maxProvinceInKD = $config["maxProvinceInKD"];
			
			$this->provinceObj->database->query("Select * From Kingdom Where king=$pid");
			$kingdom = $this->provinceObj->database->fetchArray();
			
			$bonusStep = (int) (($maxKingBonus - $minKingBonus) / $maxProvinceInKD);
			
			if ($kingdom["numProvinces"]==1)
				$bonus = $minKingBonus / 100;
			else if ($kingdom["numProvinces"]==$maxProvinceInKD)
				$bonus = $maxKingBonus / 100;
			else 
				$bonus = ($minKingBonus + ($bonusStep*($kingdom["numProvinces"]-1))) / 100;
		}
		else 
			$bonus = 1;
		
		
		$html = '<table>
					<tr><td colspan=2><b>Bonuses</b></td></tr>
<tr><td ALIGN=LEFT>Income</td><td ALIGN=LEFT>'.round(($eff->getEffect($GLOBALS['effectConstants']->ADD_GOLD_INCOME,$pid)*$bonus-1)*100,2).'%</td></tr>
<tr><td ALIGN=LEFT>Food</td><td ALIGN=LEFT>'.round(($eff->getEffect($GLOBALS['effectConstants']->ADD_FOOD_INCOME,$pid)*$bonus-1)*100,2).'%</td></tr>
<tr><td ALIGN=LEFT>Metal</td><td ALIGN=LEFT>'.round(($eff->getEffect($GLOBALS['effectConstants']->ADD_METAL_INCOME,$pid)*$bonus-1)*100,2).'%</td></tr>
<tr><td ALIGN=LEFT>Peasants</td><td ALIGN=LEFT>'.round(($eff->getEffect($GLOBALS['effectConstants']->ADD_PEASANT_GROWTH,$pid)-1)*100,2).'%</td></tr>
<tr><td ALIGN=LEFT>&nbsp;</td><td ALIGN=LEFT>&nbsp;</td></tr>
<tr><td ALIGN=LEFT>Military</td><td ALIGN=LEFT>'.round(($eff->getEffect($GLOBALS['effectConstants']->ADD_ATTACK,$pid)-1)*100,2).'% / '.round(($eff->getEffect($GLOBALS['effectConstants']->ADD_DEFENSE,$pid)-1)*100,2).'%</td></tr>
<tr><td ALIGN=LEFT>Thievery</td><td ALIGN=LEFT>'.round(($eff->getEffect($GLOBALS['effectConstants']->ADD_THIEVERY_OFF,$pid)-1)*100,2).'% / '.round(($eff->getEffect($GLOBALS['effectConstants']->ADD_THIEVERY_DEF,$pid)-1)*100,2).'%</td></tr>
<tr><td ALIGN=LEFT>Magic Protection</td><td ALIGN=LEFT>'.$magic_prot.'%</td></tr>
<tr><td ALIGN=LEFT>Magic Power</td><td ALIGN=LEFT>'.round(($eff->getEffect($GLOBALS['effectConstants']->ADD_MAGIC_CHANCE,$pid)-1)*100,2).'%</td></tr>
<tr><td ALIGN=LEFT>&nbsp;</td><td ALIGN=LEFT>&nbsp;</td></tr>
<tr><td ALIGN=LEFT>Housing</td><td ALIGN=LEFT>'.round(($eff->getEffect($GLOBALS['effectConstants']->ADD_PEASANT_HOUSING,$pid)-1)*100,2).'%</td></tr>
				</table>';
		
		return $html;
	}
	
	
	
	function showCost () {
		return "<table><tr><td>gold:</td><td>" .number_format($this->costToHire['gold'],0,' ',','). "</td></tr>" .
					"<tr><td>metal:</td><td>".number_format($this->costToHire['metal'],0,' ',','). "</td></tr>" .
				"<tr><td>food:</td><td>".number_format($this->costToHire['food'],0,' ',','). "</td></tr>" .
				"<tr><td>peasants:</td><td>" .number_format($this->costToHire['peasants'],0,' ',','). "</td></tr>
				</table>";
	}

	function showUpkeep () {
		return "<table><tr><td>gold:</td><td>" .number_format($this->upkeep['gold'],0,' ',','). "</td></tr>" .
				"<tr><td>metal:</td><td>".number_format($this->upkeep['metal'],0,' ',','). "</td></tr>" .
				"<tr><td>food:</td><td>".number_format($this->upkeep['food'],0,' ',','). "</td></tr>" .
				"<tr><td>peasants:</td><td>" .number_format($this->upkeep['peasants'],0,' ',','). "</td></tr>
				</table>";
	}
	
	
	function writeChange ($number) {
		if ($number>=0) {
			return '<font color="#99FFBB">+'.number_format($number,0,' ',',').'</font>';
		} else {
			return '<font color="#FFAAAA">'.number_format($number,0,' ',',').'</font>';
		}
	}

	function writeNumber ($number) {
		if ($number>=0) {
			return '<font color="#99FFBB">'.number_format($number,0,' ',',').'</font>';
		} else {
			return '<font color="#FFAAAA">'.number_format($number,0,' ',',').'</font>';
		}
	}


	function getImage ()
	{
		return $this->image;
	}
	function getID() {
		return $this->coID;
	}
	function getName() {
		return $this->councilName;
	}
	function getCouncilHistory() {
		return $this->councilHistory;
	}
	function getRequires () {
		return $this->requires;
	}
	function raceOk ($inRace) {
		reset ($this->races);
		foreach ($this->races as $race) {
			if ($race==$inRace) return true;
		}
		return false;
	}
	
	function wantsToBeHired(&$text)
	{
		return true;
	}
}

} // end if( !class_exists() )
?>