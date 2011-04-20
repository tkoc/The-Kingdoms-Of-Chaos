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

// 

if( !class_exists( "Riots" ) ) {
require_once ($GLOBALS['path_www_thievery'] . "ThieveryBase.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Effect.class.inc.php");

class Riots extends ThieveryBase {

	var $COST_INCREASE = 3;
	var $difficulity =  -15;
    function Riots( $thieveryID ) 
	{
		$this->ThieveryBase( $thieveryID,"Riots",
		"Convinces peasants to riot against their leader.  This will halve income for the target province.",
		/* requires */
		array('military' =>0, "infrastructure" => 0, "magic" => 0, "thievery" => 1));    
	}

        ////////////////////////////////////////////
        // bool Thievery::doTick(&$database)
        ////////////////////////////////////////////
        //
        // preforms global update for the thievery op.
        // (currently not in use, just for future)
        ////////////////////////////////////////////

        function doTick(&$database) {
		$database->query("UPDATE Province RIGHT join ActiveThieveryOps on Province.pID=ActiveThieveryOps.pID set Province.goldIncomeChange=Province.goldIncomeChange/2 WHERE ActiveThieveryOps.thieveryOperationID='".$this->thID ."'");
                return 0;
        }



	function thieveryEffect ($province,$victimProvince) 
	{
		if ( $this->CanAddLastingOperation($province, $victimProvince) == true)
		{
 			$days = rand(1,7);
			if ($this->AddLastingOperation($province, $victimProvince, $days) == true)
			{
				$txt = "The operation was a success!  We have caused peasants to riot in the streets for $days days";
				$victimProvince->postNews($victimProvince->getAdvisorName()." , our peasants are rioting in the streets!");
				if (parent::thieveryEffect($province,$victimProvince)>0) {
					$txt .= "<br>We are also getting more famous.";			
				}
			}
		}
		else
		{
			$txt = "The peasants are already rioting!";
		}
		$html = "<center><br>$txt</center>";
		return $html;
	}

}
} // end if( !class_exists() )
?>