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

if( !class_exists( "RobSupplies" ) ) {
require_once ($GLOBALS['path_www_thievery'] . "ThieveryBase.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Effect.class.inc.php");

class RobSupplies extends ThieveryBase {

	var $costInfluence = 4;
	var $difficulity = -5;
	var $reputationLoss = 2;  // each op gives 1 reputation default.

    function RobSupplies( $thieveryID ) 
	{
		$this->ThieveryBase( $thieveryID,"Rob Supplies",
		"Robs food, metal and gold from a target.  The more resources your target has, the more you get back.<br>
		Remember that only one thief can carry so much..",
		/* requires */
		array('military' =>0, "infrastructure" => 0, "magic" => 0, "thievery" => 0));    
	}

	function thieveryEffect ($province,$victimProvince) 
	{
		$txt = "The operation was a success!";
		$thieves = $province->milObject->getMilUnit($GLOBALS['MilitaryConst']->THIEVES);

		$maxResources = $thieves['num']*17* $province->sizeModifier($victimProvince);
		$gold = (int) min($maxResources,($victimProvince->gold*0.015));
		$food = (int) min($maxResources,($victimProvince->food*0.02));
		$metal = (int) min($maxResources,($victimProvince->metal*0.02));
		$victimProvince->useResource($gold,$metal,$food);
		$province->gainResource($gold,$metal,$food);
		$txt .=" we stole " . writeChaosNumber($gold) . "gc, " . writeChaosNumber($food) ." food and ".writeChaosNumber($metal) ." metal with " . $thieves['num'] . " thieves";
		$victimProvince->postNews($victimProvince->getAdvisorName().", $gold gc, $food food and $metal metal is missing from our stocks!");
		if (parent::thieveryEffect($province,$victimProvince)>0) {
			$txt .= "<br>We are also getting more famous.";			
		}
		$html = "<center><br>$txt</center>";
		return $html;
	}

}
} // end if( !class_exists() )
?>
