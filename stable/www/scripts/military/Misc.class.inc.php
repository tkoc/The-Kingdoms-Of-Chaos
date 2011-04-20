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

class Misc {
function Misc() { 
   mt_srand( (double) microtime() * 1000000);
}
///////////////////////////////////
// getRandomArray
///////////////////////////////////
//  generates a "random" array of length: $size, and randomly spreads $num across the array.
//  returns array.
//////////////////////////////////
 function getRandomArray($_num, $_size) {
	$round = array_fill(0, $_size, 0);
	if($_num >= $_size)
		$numEachRound = (int)($_num/$_size);
	else $numEachRound = 1;
        $numLeft = $_num;
        if($numEachRound<4) {
  //              echo "\n\tDEBUG START";
                $i = 0;
                while(($numLeft > 0))
		{
			$place = mt_rand(0, ($_size-1));
                        $round[$place] += $numEachRound;
			$numLeft -= $numEachRound;
			$i++;
		}
//                print_r($round);
//                echo "\n\tDEBUG END";

	}

	$i = 0;
	$numThisRound = 0;
        $numEachRound = (int)($numLeft/$_size);
        if( ($numEachRound < 1) ) $numEachRound=1;
	while( ($i < $_size) && ($numLeft > 0)) {
		$numThisRound = mt_rand(0, $numEachRound);
		$round[$i] += $numThisRound;
		$numLeft -= $numThisRound;
		$i++;
	}

	if($numLeft > 0) {
		$numEachRound = (int)($numLeft/$_size);
		if($numEachRound < 1)
			$numEachRound = 1;
		$i = 0;
		while( ($i < $_size) && ($numLeft > 0) ) {
			$round[$i] += $numEachRound;
			$numLeft -= $numEachRound;
			$i++;
		}
	}

    	if($numLeft > 0) {
		$round[0] += $numLeft;
	}
	return $round;


        
 }
}

?>