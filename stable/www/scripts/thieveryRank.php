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
/*
 *
 *
 *  Anders Elton: Gjorde kompatibelt med php 4.1.x
 * Øystein patcha muligheten for å stjele ressurser innad i kingedømmet ved å sende minusressurser!!!
 */


require_once ("globals.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "all.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Database.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "User.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Province.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Effect.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "requireLoggedOn.inc.php");
require_once($GLOBALS['path_www_scripts'] . "ActionLogger.class.inc.php");
$actionLogger = new ActionLogger($GLOBALS['database']);

$province = $GLOBALS['province'];

$body = "";
$body .= "    <br><center>";
$body .= GetStatLinks();
$body .= "   <br></center>";


$dummy=null;
$raceObj = new Race ($database,$dummy);

$race =$raceObj->getAllRaces();
foreach ($race as $r) {
        $id = $r->getID();
        $name = $r->getName();
        $races[$id] = $name;  
}


$sql = "SELECT Province.pID,Province.showThieveryRank,Province.reputation, Province.provinceName, Province.acres, Province.networth, Province.kiID, Kingdom.name, Province.spID FROM Province, Kingdom WHERE Kingdom.kiID=Province.kiID and Province.status='Alive' AND Province.kiID>0 ORDER BY reputation DESC";

$GLOBALS['database']->query($sql);
$counter = 0;
$last = 99999999999999999;
while ($item = $database->fetchArray()) {
	if ($item['reputation'] <= $last)
	{
		++$counter;
		$last = $item['reputation'];
	}
        $add = $item;
        $add['race'] = $races[$item['spID']];
        $add['kingdom'] = $item['kiID'];
	$add['rank'] = $counter;
        $list[] = $add;
}

reset ($list);

$body .= "<center>";
$body .= "<table><tr class='subtitle'><td>Province</td><td>Rank</td></tr>";

$rel = 0;
foreach ($list as $element)
{
	if ($element['showThieveryRank'] == 'true')
	{
		++$rel;
		$body .= "<tr>";
		$body .= "<td>" . $element['provinceName'] . "</td><td>$rel <b>(" . $element['rank'] . ")</b></td>";
		$body .= "</tr>";
	}
}

$body .= "</table>";
$body .= "</center>";
templateDisplay($province,$body);
?>
