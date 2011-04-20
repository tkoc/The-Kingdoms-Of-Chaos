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
require_once("all.inc.php");
require_once("globals.inc.php");
class BFLogger {
	var $province = NULL;
	var $modifyer = 0;
	var $basicRatio = 16;
	var $numberOfRows = 0;
	var $goodPoints = 1;
	var $badPoints = 1;
	var $ratioTable = array(90 => 5, 80 => 4, 75 => 2, 70 => 3, 65 => 4, 55 => 5, 50 => 7, 40 => 8, 30 => 10);
	var $database = NULL;
	var $pointsArray = NULL;
	var $pointsCalculated = false;
	
	function BFLogger($prObj) {
		$this->province = $prObj;
		$this->database = $GLOBALS['database'];
	}
	
	function getModifier() {		
		$this->goodPoints = 0;
		$this->badPoints = 0;
		$sql = "select * from bfTable where pID=".$this->province->pID." and success='true' order by id desc limit 10";
		$mfresult = $this->database->query($sql);
		
		$this->numberOfRows = $this->database->numRows();
		
		while($data = $this->database->fetchArray($mfresult)) {
			$this->goodPoints += $data['gaPoint'];
			$this->badPoints += $data['baPoint'];
		}
		
		if($this->numberOfRows <= 6) $this->goodPoints = max( ($this->basicRatio - $this->numberOfRows), $this->goodPoints );
		
		$this->goodPoints = max($this->goodPoints, 1);
		$this->badPoints = max($this->badPoints, 1);
		$this->modifyer = ($this->goodPoints / $this->badPoints);
		
		return $this->modifyer;
	}
	
	function addAttack($target, $ratio, $success, $acres) {

		$this->goodPoints = 1;
		$this->badPoints = 0;
		$suc = 'false';
		if($success == true) $suc = 'true'; 
		
		$theRatio = round($ratio * 100);


		if( ($theRatio >= 90) ) {
			$this->goodPoints = $this->ratioTable[90];	
		}
		else if( ($theRatio >= 80) && ($theRatio < 90) ) {
			$this->goodPoints = $this->ratioTable[80];
		}
		else if( ($theRatio >= 75) && ($theRatio < 80) ) {
			$this->goodPoints = $this->ratioTable[75];
		}
		else if( ($theRatio >= 70) && ($theRatio < 75) ) {
			$this->badPoints = $this->ratioTable[70];
			$this->goodPoints = 2;
		}
		else if( ($theRatio >= 65) && ($theRatio < 70) ) {
			$this->badPoints = $this->ratioTable[65];
		}
		else if( ($theRatio >= 55) && ($theRatio < 65) ) {
			$this->badPoints = $this->ratioTable[55];
		}
		else if( ($theRatio >= 50) && ($theRatio < 55) ) {
			$this->badPoints = $this->ratioTable[50];
		}
		else if( ($theRatio >= 40) && ($theRatio < 50) ) {
			$this->badPoints = $this->ratioTable[40];
		}
		else {
			$this->badPoints = $this->ratioTable[30];
		}
		
		$sql = "insert into bfTable (pID, tID, gaPoint, baPoint, ratio, success, acres) values (".$this->province->pID.",$target, ".$this->goodPoints.", ".$this->badPoints.", $theRatio, '$suc', $acres)";
		$this->database->query($sql);
	}
	
	function getPoints() {
	}
	
	function getPointsArray() {
	}
}
?>
