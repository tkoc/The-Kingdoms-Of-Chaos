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
/* isLoggedOn.php
 * 
 *  Creates database object, checks if user is logged on, creates province object.
 * Author: Anders Elton
 * 
 */
require_once ("all.inc.php");
require_once ("User.class.inc.php");
require_once ("Province.class.inc.php");
require_once ("Race.class.inc.php");

$database = $GLOBALS['database'];
$database->connect();

$dummy=null;
$raceObj = new Race ($database,$dummy);

$race =$raceObj->getAllRaces();
foreach ($race as $r) {
	$id = $r->getID();
	$name = $r->getName();
	$races[$id] = $name;
}

$database->query("SELECT ticks, status, lastTickTime, NOW() as currentTime from Config");
$config = $database->fetchArray();

$database->query("SELECT provinceName, spID, kiID, networth, acres from Province where kiID>0") or die ($database->error());

while ($item = $database->fetchArray()) {
	$add = $item;
	$add['race'] = $races[$item['spID']];
	$add['kingdom'] = $item['kiID'];
	$list[] = $add;
}

reset($list);

header("Content-Type: text/plain; charset=ISO-8859-1\n");
echo "Statistics Chaos\n";
echo "Chaos time: $config[ticks] status: $config[status] lastTick: $config[lastTickTime] currentTime: $config[currentTime]\n";
$counter =0;
echo "NR PROVINCE RACE NETWORTH ACRES KINGDOM\n\n";
foreach ($list as $province) {
echo $counter++ . ' "'. $province['provinceName'] . '" ' . $province['race'] . " " . $province['networth'] . " " . $province['acres'] .' '. $province['kingdom'] .  "\n";

}

?>
