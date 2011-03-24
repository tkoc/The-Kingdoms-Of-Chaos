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
// 

if( !class_exists( "AssasinateMilitary" ) ) {
require_once ($GLOBALS['path_www_thievery'] . "ThieveryBase.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Effect.class.inc.php");

class AssasinateMilitary extends ThieveryBase {

	var $difficulity = -20;
	var $costInfluence = 5;
	var $reputationLoss = 5;  // each op gives 1 reputation default.
	var $killRate		= 0.008; // 0,2% = 2 if 1000
	var $randomNess		= 10;
	function AssasinateMilitary( $thieveryID ) {
		$this->ThieveryBase( $thieveryID,"Assasinate Military",
		"Kills enemy troops.",
		/* requires */
		array('military' =>0, "infrastructure" => 0, "magic" => 0, "thievery" => 8));    
	}

	function thieveryEffect ($province,$victimProvince)  
	{
		$sizeMod = (($province->acres<$victimProvince->acres) ? $province->acres/$victimProvince->acres : $victimProvince->acres/$province->acres);
		$this->killRate*=$sizeMod;
		$effect = new Effect( $province->database );
		$txt = "The operation was a success!";
		if (mt_rand(1,4)==1 ) {  // loose some thieves
			$myThieves  = $province->milObject->getMilUnit($province->milObject->MilitaryConst->THIEVES);
			$lost = floor( $effect->getEffect( $GLOBALS['effectConstants']->ADD_THIEVERY_LOSS,$province->getpID() ) *  ($myThieves['num'] * 0.03) );
			$txt .= "<br>We lost $lost thieves in the operation..but<br>";
			$province->milObject->killUnits($GLOBALS['MilitaryConst']->THIEVES,$lost );
		}
		// already has military data
		$enemyThieves  = $victimProvince->milObject->getMilUnit($victimProvince->milObject->MilitaryConst->THIEVES);
		$enemyWizards  = $victimProvince->milObject->getMilUnit($victimProvince->milObject->MilitaryConst->WIZARDS);
		$enemySoldiers = $victimProvince->milObject->getMilUnit($victimProvince->milObject->MilitaryConst->SOLDIERS);
		$enemyDef = $victimProvince->milObject->getMilUnit($victimProvince->milObject->MilitaryConst->DEF_SOLDIERS);
		$enemyOff = $victimProvince->milObject->getMilUnit($victimProvince->milObject->MilitaryConst->OFF_SOLDIERS);
		$enemyElite = $victimProvince->milObject->getMilUnit($victimProvince->milObject->MilitaryConst->ELITE_SOLDIERS);
		
		// what to kill?
		$killSoldiers = intval ($enemySoldiers['num'] * $this->killRate * $this->randomPercent($this->randomNess) * $province->sizeModifier($victimProvince));
		$killOff = intval ($enemyOff['num'] * $this->killRate * $this->randomPercent($this->randomNess) * $province->sizeModifier($victimProvince));
		$killDef = intval ($enemyDef['num'] * $this->killRate * $this->randomPercent($this->randomNess) * $province->sizeModifier($victimProvince));
		$killElite = intval ($enemyElite['num'] * $this->killRate * $this->randomPercent($this->randomNess)* 0.5 * $province->sizeModifier($victimProvince));
		$killThieves = intval ($enemyThieves['num'] * $this->killRate * 2 * $this->randomPercent($this->randomNess) * $province->sizeModifier($victimProvince));
		$killWizards = intval ($enemyWizards['num'] * $this->killRate * 2 * $this->randomPercent($this->randomNess) * $province->sizeModifier($victimProvince));
		$sum = $killSoldiers + $killOff + $killDef + $killElite + $killThieves + $killWizards;
		$txt .= "Our soldiers sneak into the enemy, and is able to assasinate about " . writeChaosNumber($sum) ." troops!";	
		$killList = "";
		if ($sum>0)
		{
			if ($killSoldiers>0)
				$killList .= "$killSoldiers soldiers, ";
			if ($killOff>0)
				$killList .= "$killOff offensive troops, ";
			if ($killDef>0)
				$killList .= "$killDef defensive troops, ";
			if ($killElite>0)
				$killList .= "$killElite elites, ";
			if ($killThieves>0)
				$killList .= "$killThieves thieves and ";
			if ($killWizards>0)
				$killList .= "$killWizards wizards";
			$killList .= " were found dead in the streets.";
		}else 
			$killList = "Assasination attempt on our military, but no military were lost!";
		// this is the slaying.
		$victimProvince->milObject->killUnits($GLOBALS['MilitaryConst']->THIEVES,$killThieves );
		$victimProvince->milObject->killUnits($GLOBALS['MilitaryConst']->WIZARDS,$killWizards );
		$victimProvince->milObject->killUnits($GLOBALS['MilitaryConst']->SOLDIERS,$killSoldiers );
		$victimProvince->milObject->killUnits($GLOBALS['MilitaryConst']->DEF_SOLDIERS,$killDef );
		$victimProvince->milObject->killUnits($GLOBALS['MilitaryConst']->OFF_SOLDIERS,$killOff );
		$victimProvince->milObject->killUnits($GLOBALS['MilitaryConst']->ELITE_SOLDIERS,$killElite );

		if (isset($GLOBALS['game_debug']) && $GLOBALS['game_debug'] == true)
		{
			$GLOBALS['game_debug_data'] .= "<br>AssasinateMilitary::thieveryEffect: $enemyThieves[num], enemyWizards: $enemyWizards[num], enemySoldiers: $enemySoldiers[num], enemyDef: $enemyDef[num], enemyOff: $enemyOff[num], enemyElite, $enemyElite[num] ($killList)";
		}			
		$victimProvince->postNews($victimProvince->getAdvisorName()."News flash: assasinations! " . $killList);
		if (parent::thieveryEffect($province,$victimProvince)>0) {
			$txt .= "<br>We are also getting more famous.";			
		}
		$html = "<center><br>$txt</center>";
		return $html;
	}

}
} // end if( !class_exists() )
?>
