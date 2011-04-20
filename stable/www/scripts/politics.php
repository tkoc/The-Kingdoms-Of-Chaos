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
//* file: Politics.php
//*
//* Author: Anders Elton
//*
//* History:
//*	
//**********************************************************************

require_once ("globals.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "all.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Database.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "User.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Province.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "News.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Kingdom.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "requireLoggedOn.inc.php");

$kingdom = new Kingdom($GLOBALS['database'], $province->getKiId());
if (isset($_POST['vote']))
{
  $province->vote($_POST['selectProvince']);
  $kingdom->updateKing();
  $province->getProvinceData();
}

$kingdom->loadKingdom(); 
$kingdom->handlePost();
$kingdom->loadKingdom(); 

$html = "<center>";

// additional king options.
if ($province->isKing())
{
  $html .= "<h2>King options:</h2><br>";
  $html .= $kingdom->showKingoptions();
}

// Not voted yet?  Message that its smart to vote!
$html .= '<h2>Vote options:</h2><br><FORM action="'.$_SERVER['PHP_SELF'].'" method=POST>';
$html .= $GLOBALS['fcid_post']; 
if ($province->voteFor == 0)
{
  $html .= "<br>You have not yet voted!";
} else
{
  $vp = new Province($province->voteFor, $GLOBALS['database']);
  $vp->getProvinceData();
  $html .= "<br>You have voted for <b>".$vp->getAdvisorName()." of ".$vp->provinceName."</b>";
}
$html .= "<br>" . $kingdom->getRequiredKingVotes() . " votes is required to become King.";
$html .= "<br>" . $kingdom->getKingCandidates();
$html .= '<br><br>';
$html .= $kingdom->createSelectBox();
$html .= '<INPUT TYPE=SUBMIT class="form" NAME=vote VALUE=VOTE></FORM>';
$html .= "</center>";
templateDisplay($province,$html,"../img/Cornerpictures/Politics_picture.jpg","");
?>
