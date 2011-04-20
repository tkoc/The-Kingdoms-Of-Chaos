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

if( !class_exists( "Season" ) ) {
require_once( $GLOBALS['path_www_scripts'] . "effect/EffectBase.class.inc.php" );
class SeasonBase extends EffectBase 
{ 
	var $Database 	  = NULL;
	var $Name         = "FIXME - admin";
	var $Description  = "FIXME - admin";
	var $SeasonTick	  = -1;
	var $SeasonLength = 96;
	
	function SeasonBase(&$db,$tick)
	{
		$this->Database   = $db;
		$this->SeasonTick = $tick;
	}
	
	// just a base thingy
	function DoTick()
	{
		if ( $this->SeasonTick <= 0)
		{
			// shift to next season
			$this->Database->query("Update Config set SeasonTick='$this->SeasonLength', Season='".$this->NextSeason()."'");
		}
		else
		{
			$this->Database->query("UPDATE Config set SeasonTick=SeasonTick-1");
		}
	}
	
	function NextSeason ()
	{
		return -1;
	}
}

class SeasonSummer extends SeasonBase
{
	var $Name = "Summer";
	function SeasonSummer(&$db,$tick)
	{
		$this->SeasonBase($db,$tick);
	}
	function DoTick()
	{
		parent::DoTick();
	}
	function NextSeason ()
	{
		return $GLOBALS['SeasonFactory']->AUTUMN;
	}
	function addBuildingTime() {
		return -10;
	}	
	function addPeasantGrowth() {
		return 10;
	}	
	
	
}

class SeasonWinter extends SeasonBase
{
	var $Name = "Winter";
	function SeasonWinter(&$db,$tick)
	{
		$this->SeasonBase($db,$tick);
	}
	function DoTick()
	{
		parent::DoTick();
	}
	function NextSeason ()
	{
		return $GLOBALS['SeasonFactory']->SPRING;
	}
	function addAttackTime() {
		return 20;
	}
	function addDefense() {
		return 5;
	}
	
	
}

class SeasonAutumn extends SeasonBase
{
	var $Name = "Autumn";
	function SeasonAutumn(&$db,$tick)
	{
		$this->SeasonBase($db,$tick);
	}
	function DoTick()
	{
		parent::DoTick();
	}
	function NextSeason ()
	{
		return $GLOBALS['SeasonFactory']->WINTER;
	}
	function addMilitaryGoldCost() {
		return -10;
	}
	function addMilitaryMetalCost() {
		return -10;
	}
	function addMilitaryFoodCost() {
		return -10;
	}
	function addMetalIncome() {
		return 10;
	}
	
}

class SeasonSpring extends SeasonBase
{
	var $Name = "Spring";

	function SeasonSpring(&$db,$tick)
	{
		$this->SeasonBase($db,$tick);
	}
	function DoTick()
	{
		parent::DoTick();
	}
	function NextSeason ()
	{
		return $GLOBALS['SeasonFactory']->SUMMER;
	}

	function addMorale() {
		return 10;
	}
	

}

class SeasonFactory 
{
	var $SUMMER = 1;
	var $WINTER = 2;
	var $AUTUMN = 3;
	var $SPRING = 4;
	
	var $SeasonList = array();
	function AddSeason($id,$season)
	{
		$this->SeasonList[$id] = $season;
	}
	
	function GetSeason($id)
	{
		if (array_key_exists($id,$this->SeasonList))
		{
			return $this->SeasonList[$id];
		}
		die("NOT SUPPOSED TO HAPPEN - contact admin.");		
	}
	
}
$GLOBALS['SeasonFactory'] = new SeasonFactory;
$GLOBALS['SeasonFactory']->AddSeason($GLOBALS['SeasonFactory']->SUMMER, (new SeasonSummer($GLOBALS['database'], $GLOBALS['config']['SeasonTick'])));
$GLOBALS['SeasonFactory']->AddSeason($GLOBALS['SeasonFactory']->WINTER, (new SeasonWinter($GLOBALS['database'], $GLOBALS['config']['SeasonTick'])));
$GLOBALS['SeasonFactory']->AddSeason($GLOBALS['SeasonFactory']->AUTUMN, (new SeasonAutumn($GLOBALS['database'], $GLOBALS['config']['SeasonTick'])));
$GLOBALS['SeasonFactory']->AddSeason($GLOBALS['SeasonFactory']->SPRING, (new SeasonSpring($GLOBALS['database'], $GLOBALS['config']['SeasonTick'])));
$GLOBALS['CurrentSeason'] = $GLOBALS['SeasonFactory']->GetSeason($GLOBALS['config']['Season']);

} // end if( !class_exists() )
?>