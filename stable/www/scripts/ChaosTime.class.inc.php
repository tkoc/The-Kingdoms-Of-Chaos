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

class ChaosTime {
        var $STARTYEAR = 2003;
		var $STARTMONTH = 4;
		var $STARTDAY = 9;
		var $STARTHOUR = 0;
		var $STARTMIN = 0;
                var $timeStamp = 0;
                var $chaosTime = 0;
		var $startDate = "23 April 2003";
                var $startTime = 0;
		
		function ChaosTime($timeStamp=0) {
			//echo "<br>MyTime: $timeStamp";
			$this->timeStamp = $timeStamp;
//			print_r($this);
		}

		function format($_inp) {
			if($_inp == 0) {
				$ret = "1st";
			}
			else if($_inp == 1) {
				$ret = "2nd";
			}
			else if($_inp == 2) {
				$ret = "3rd";
			}
			else {
				$ret = ($_inp+1)."th";
			}
			return $ret;
		}
	
		function getEra() {
			$_era = (int)(($this->timeStamp)/2880);
			return $this->format($_era);
		}

		function getYear() {
			$_era = (int)(($this->timeStamp)/2880);
			$_daysLeft = $this->timeStamp - (int)($_era * 2880);
			$_years = (int)($_daysLeft/288);
			return $this->format($_years);
		}

		function getMonth() {
			$_era = (int)(($this->timeStamp)/2880);
			$_daysLeft = $this->timeStamp - ($_era * 2880);
			$_years = (int)($_daysLeft/288); 
			$_daysLeft -= ($_years * 288);
			$_month = (int)($_daysLeft/24);
			return $this->format($_month);
		}
		
		function getDay() {
			$_era = (int)(($this->timeStamp)/2880);
			$_daysLeft = $this->timeStamp - ($_era * 2880);
			$_years = (int)($_daysLeft/288); 
			$_daysLeft -= ($_years * 288);
			$_month = (int)($_daysLeft/24);
			$_daysLeft -= ($_month * 24);
			return $this->format($_daysLeft);
		}

	}
?>