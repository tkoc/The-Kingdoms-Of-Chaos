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
// Øystein made a change here 11.11.2003 to get the right military object from
// the military class and output the right text.

if( !class_exists( "SpyOnMilitary" ) ) {
require_once ($GLOBALS['path_www_thievery'] . "ThieveryBase.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Effect.class.inc.php");

class SpyOnMilitary extends ThieveryBase {
	var $difficulity = 15;
	var $costInfluence = 3;
	var $optimalThieves = 1000;	
	var $randomNess = 5;

	function SpyOnMilitary( $thieveryID ) 
	{
		$this->ThieveryBase( $thieveryID,"Spy on Military", 
		"This will let you see if another player has military out or not.  If you want to attack someone, its wise to use this operation first.",
		/* requires */
		array('military' =>0, "infrastructure" => 0, "magic" => 0, "thievery" => 0));    
	}

	function thieveryEffect ($province,$victimProvince) 
	{
		$txt = "The operation was a success!";
		$victimProvince->getMilitaryData();
		$milOut = $victimProvince->milObject->getMilitaryOut();
		if (is_array($milOut)) {
			$txt .="<br>Our reports indicate that there are military out!";
			foreach( $milOut as $out ) {
				$txt .="<br>about ".writeChaosNumber(intval($this->randomPercent($this->randomNess) * $out['num']))." ".$out['object']->getName()." are out in war";
			}
		}
		else $txt .="<br>Our reports indicate that all military is home.";
		if (parent::thieveryEffect($province,$victimProvince)>0) {
			$txt .= "<br>We are also getting more famous.";			
		}
		$html = "<center><br>$txt</center>";
		return $html;
	}

}
} // end if( !class_exists() )
?>
