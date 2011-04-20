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
//**********************************************************************
//* science.php
//*
//*	Handles knowledge for a province.
//* 
//* Author: Anders Elton
//*
//*	History:
//*		01.08.2004: Anders Elton.  Modified to fit new coding style
//**********************************************************************
require_once ("globals.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "all.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Database.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "User.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Province.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "requireLoggedOn.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Science.class.inc.php");

$html = "";

if ($GLOBALS['config']['serverMode'] == 'Beta')
{
	if (isset($_POST['cheat'])) 
	{
		$GLOBALS['database']->query("UPDATE Science set ticks=0 where pID=" . $GLOBALS['province']->pID);
		if ($province->gold < 10000000 )
			$GLOBALS['province']->gainResource(10000000-$province->gold,10000000-$province->metal,10000000 - $province->food);
	}
	$html .= "<center><form action=".$GLOBALS['PHP_SELF']." method=POST><input type=submit name=cheat value=cheat></form></center>";
}

$science = new Science($database,$province->getpID());

if (isset($_POST['research'])) {
	reset($_POST);
	while (list ($key, $val) = each ($_POST)) {
    	if ($val=='research') {
			// remove "a" from $key
			$toResearch = str_replace ("a", "", $key);
		}
	}
	if (!isset ($toResearch)) die("INTERNAL SCRIPT ERROR.  CONTACT ADMINISTRATORS!!!");
	if ($science->research($toResearch)) {
		$html .= "<center><table><tr><td>starting to research: " . $science->scienceTree[$toResearch]->getName()."</td></tr></table></center>";
	} else {
		$html .= "<center><table width=500><tr><td>".$province->getShortTitle() . ", we do not have sufficient resources to start a science project at this time!  Our scientists 
												can not develop new advantages on water and air!</td></tr></table></center>";
	}
}

$html .= "<br>&nbsp<center><table><tr><td>Here is a list of all our knowledge: " . $science->showSciences().".</td></tr></table></center>";

if ($science->researchInProgress()) {
	$html .="<center><table width=600><tr><td>&nbsp</td></tr><tr><td>";
	$researching = $science->getScienceObject($science->researching['sccID']);
	$html .= "<br>".$province->getAdvisorName().", our wise men are already busy researcing the art of <b>".
	$researching->getName()."</b>.  We expect the research to be completed in <b>".
	$science->researching['ticks'] . "</b> days.";
	$html .="</td></tr><tr><td><br><i>".$researching->getDescription()."</i></td></tr>";
	$html .="</td></tr></table></center>";
} else {
	$html .= "<center><table><tr><td>".$province->getAdvisorName().", 
	we are currently not researching any knowledge.  Remember, though, that technological advances are vital 
	to your province success...<br>&nbsp</td></tr></table></center>";
 	$html .= $science->showAviableSciences();
}
$html .= "<br>&nbsp<br>&nbsp<center><img src=\"../img/science_pic.jpg\" border=0></center><br>&nbsp";
templateDisplay($province,$html);
?>
