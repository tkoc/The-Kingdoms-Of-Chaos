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
	$randArr = array_fill(0, $_size, 0);										//initialize array, fill with zeros
	$numEachRound = 0;															//amount for each entry in array

	$numLeft = $_num;															//How much more to fill in!!

	//set amount of each entry in array
	if($_num > $_size) $numEachRound = floor($_num/$_size);
	else $numEachRound = 1;

	if($numEachRound == 1) {													//if num <= size just fill them in at random positions in the array :)
		while($numLeft > 0) {							
			$position = mt_rand(0, ($_size-1));									//get random position
			$randArr[$position] += $numEachRound;
			$numLeft -= 1;
		}
	}
	else if ($numEachRound > 1){												//else if num > size
		$i = 0;
		$nextRound = 0;
		while(($i < $_size) && ($numLeft > 0)) {                                 //while more positions left... OR more to put into array....
			$numToPutInArray = $numEachRound - $nextRound;						//subtract what we added last round.... (to make it NOT overflow)
			$thisRound = floor(mt_rand(0, ($numEachRound/2)));					//make a random value to add
			$numToPutInArray += $thisRound;										//add it
			if($numToPutInArray < 0) $numToPutInArray = 0;						//if neagtive to put in array (should not be possible)
			$randArr[$i] += $numToPutInArray;									//put it in array
			$numLeft -= $numToPutInArray;										//subtract on numLeft
			$nextRound = $thisRound;											//set thisAdded to be subtractet next
			$i++;
		}
		$i -= 1;
		$randArr[$i] -= $nextRound;												//subtract what should have been subtractet last next round
		$numLeft += $nextRound;													//countdown on array, means increase numleft!!
		while($numLeft > 0) {													//insert last on(es) standing :)
			$position = mt_rand(0, ($_size-1));									//get random position
			$randArr[$position] += 1;
			$numLeft -= 1;
		}
	}

	return $randArr;
 }
}

?>