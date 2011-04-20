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

require_once ("globals.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "all.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Database.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "User.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Province.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Effect.class.inc.php");

doTick();

die();
	
	function tryMerge($kiID1, $kiID2)
	{
		// TODO!  check total number of players abs(kiID), because inactive players must be counted as well!
		// then merge kiID2 => kiID1
		$database = $GLOBALS['database'];
		$database->query("SELECT COUNT(*) from Province where ABS(kiID)='$kiID1'");
		$num1 = $database->fetchArray();
		$database->query("SELECT COUNT(*) from Province where ABS(kiID)='$kiID2'");
		$num2 = $database->fetchArray();
		if (($num1 + $num2) <= $GLOBALS['config']['maxProvinceInKD'])
		{
			
		}
	}
	function doTick () 
	{
		$database = $GLOBALS['database'];
	//SELECT Kingdom1.kiID AS kiID1, Kingdom1.name AS name1 ,Kingdom1.relationAlly AS relationAlly1, Kingdom2.kiID AS kiID2, Kingdom2.name AS name2, Kingdom2.relationAlly AS relationAlly2 FROM Kingdom AS Kingdom1 LEFT JOIN Kingdom AS Kingdom2 on Kingdom1.relationAlly=Kingdom2.kiID WHERE Kingdom1.relationAlly=Kingdom2.kiID AND Kingdom2.relationAlly=Kingdom1.kiID AND Kingdom1.relationMerge='true' AND Kingdom2.relationMerge='true'
		$mergeList = array();
		$mergedKingdoms = array();
		$sql = "SELECT Kingdom1.kiID AS kiID1, Kingdom1.name AS name1 ,Kingdom1.relationAlly AS relationAlly1, 
		               Kingdom2.kiID AS kiID2, Kingdom2.name AS name2, Kingdom2.relationAlly AS relationAlly2
					       FROM Kingdom AS Kingdom1 LEFT JOIN Kingdom AS Kingdom2 on Kingdom1.relationAlly=Kingdom2.kiID
						        WHERE Kingdom1.relationAlly=Kingdom2.kiID 
								AND Kingdom2.relationAlly=Kingdom1.kiID 
								AND Kingdom1.relationMerge='true' AND Kingdom2.relationMerge='true'";
		if ($database->query($sql) && $database->numRows())
		{
			while ($item=$database->fetchArray()) 
			{
				$mergeList[$item['kiID1']] = $item;
			}
			
			// TODO: stuff.
			reset($mergeList);
			foreach ($mergeList as $merge)
			{
				if (is_array($mergedKingdoms) && array_search($merge['kiID1'],$mergedKingdoms))
				{
					echo "Already done this!<br>";
				} else
				{
					echo "$merge[name1] with $merge[name2]<br>";
					tryMerge($merge['kiID1'],$merge['kiID2']);
					$mergedKingdoms[] = $merge['kiID1'];
					$mergedKingdoms[] = $merge['kiID2'];
				}
			}
		}		
		return true;
	}
?>