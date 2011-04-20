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

require_once ($GLOBALS['path_www_thievery'] . "ThieveryBase.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Effect.class.inc.php");
if( !class_exists( "AssasinateCouncil" ) ) {

class AssasinateCouncil extends ThieveryBase {
	var $difficulity =  -25;  // -20 hard, 20 easy
	var $costInfluence = 30;
	var $reputationLoss = 10;  // each op gives 1 reputation default.
		
	function AssasinateCouncil( $thieveryID ) {
                                                                
		$this->ThieveryBase( $thieveryID,"Assasinate Council", 
		"This will assasinate a council or force them into hiding.  Unfortunately they will know who did it.",
				/* requires */
		array('military' =>0, "infrastructure" => 0, "magic" => 0, "thievery" => 4));    
	}

	function thieveryEffect ($province,$victimProvince) 
	{
		$txt = "The operation was a success!  The council has been assasinated!";
		require_once($GLOBALS['path_www_scripts']."Council.class.inc.php" );
		$council = new Council($victimProvince->database,$victimProvince);
		$council->dropCouncil();
		$victimProvince->postNews($victimProvince->getAdvisorName().", our council has been murdered!  sources indicate that ".$province->provinceName. "(".$province->getkiID().") was responsible!");
		if (parent::thieveryEffect($province,$victimProvince)>0) {
			$txt .= "<br>We are also getting more famous.";			
		}
		$html .= "<center><br>$txt</center>";
		return $html;
	}

}
} // end if( !class_exists() )
?>