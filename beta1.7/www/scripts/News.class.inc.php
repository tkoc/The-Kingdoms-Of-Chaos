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
	require_once($GLOBALS['path_www_scripts'] . 'all.inc.php');
	require_once($GLOBALS['path_www_scripts'] . 'ChaosTime.class.inc.php');


	class News {
	   var $ID;
		var $mode;
		var $dbRef;
		var $newsTable;
		var $txtID;
		
		var $SYMBOL_NONE = 0;
		var $SYMBOL_ATTACK_FOR = 1;
		var $SYMBOL_ATTACK_AGAINST_FAIL = 2;
		var $SYMBOL_POLITICS = 3;
		var $SYMBOL_BEAST = 4;
		var $SYMBOL_ATTACK_AGAINST_SUCCESS = 5;

       		//var $STARTYEAR = 2003;
		//var $STARTMONTH = 4;
		//var $STARTDAY = 9;
		//var $STARTHOUR = 0;
		//var $STARTMIN = 0;
		var $times = NULL;


		function News(&$dbRef, $mode=0, $ID=0) {
			$this->dbRef = &$dbRef;
			$this->mode = $mode;
			$this->ID = $ID;
			//$this->times = new ChaosTime();
			if($mode == 0) {
				$this->newsTable = "News";
				$this->txtID = "kiID";
			}
			else {
				$this->newsTable = "NewsProvince";
				$this->txtID = "pID";
			}
		}

		function GetImageFromSymbol($symbol)
		{
			switch ($symbol)
			{
			case $this->SYMBOL_NONE: return "";
                	case $this->SYMBOL_ATTACK_FOR: return "symbol_attack_for.jpg";
                	case $this->SYMBOL_ATTACK_AGAINST_SUCCESS: return "symbol_attack_against_success.jpg";
                	case $this->SYMBOL_ATTACK_AGAINST_FAIL: return "symbol_attack_against_fail.jpg";
                	case $this->SYMBOL_POLITICS: return "symbol_politics.jpg";
                	case $this->SYMBOL_BEAST: return "symbol_beast.jpg";
			}
		}

		function postNews($text, $ID=0, $symbol=0) {
		  if( ($ID == 0) && ($this->ID == 0) ) {
				return "<br>Could not post news<br>";
			}
			if($ID == 0) {
				$ID=$this->ID;
			}
		//	echo "<br>$text</br>";
			$sqlSelectTicks = "select * from Config";
			$this->dbRef->query($sqlSelectTicks);
			$data = $this->dbRef->fetchArray();
			$tickTime = $data['ticks'];
			$sql = "";
			if ($this->mode == 0)
			{
				$sql = "insert into ".$this->newsTable." (".$this->txtID.", info, timeS, symbol) values($ID, '".addslashes($text)."', $tickTime, $symbol)";
			}
			else
			{
				$sql = "insert into ".$this->newsTable." (".$this->txtID.", info, timeS) values($ID, '".addslashes($text)."', $tickTime)";
			}
		//	echo "<br>$sql</br>";
			$this->dbRef->query($sql);
		//	echo "<br>".$this->dbRef->error()."<br>";
		}

		function postAll($text) {
			$sqlSelectTicks = "select * from Config";
			$this->dbRef->query($sqlSelectTicks);
			$data = $this->dbRef->fetchArray();
			$tickTime = $data['ticks'];
			$sql = "insert into ".$this->newsTable." (".$this->txtID.", info, timeS) values(0, '".addslashes($text)."', $tickTime)";
			$this->dbRef->query($sql);
		}

		function getNews($ID=0) {
			if ($this->mode == 0)
		   		$retVal = "<center><h2>Kingdom News</h2></center>";
			else 
				$retVal = "<center><h2>Province News</h2></center>";
		   if( ($ID == 0) && ($this->ID == 0) ) {
				return "<br>Could not get news<br>";
			}
			if($ID == 0) {
				$ID=$this->ID;
			}
			$sql = "";
			if ($this->mode == 0)
			{
			$sql = "select seen, info, timeS, symbol from ".$this->newsTable." where ".$this->txtID."=$ID or ".$this->txtID."=0 order by neID desc";
			} else 
			{
			$sql = "select seen, info, timeS from ".$this->newsTable." where ".$this->txtID."=$ID or ".$this->txtID."=0 order by neID desc";
			}
			//echo "<br>$sql</br>";
			$this->dbRef->query($sql);

			if( ($this->dbRef->numRows() < 1) && ($this->mode == 1) ) {
				$retVal .= "<center>You have no news</center>";
				return $retVal;
			}
			else if( ($this->dbRef->numRows() < 1) && ($this->mode == 0) ) {
				$retVal .= "<center>Your kingdom has no News</center>";
				return $retVal;
			}

			
			$showOldNews = 'N';
			if( (isset($_GET['showOld']) && ($_GET['showOld']=='yes')) || ($this->mode == 0) ) $showOldNews = 'Y';
			$retVal .= "\n\t<table border='0'>";
			while($row = $this->dbRef->fetchArray()) {
				if( ($row['seen'] == 'N') || ($row['seen'] == $showOldNews) ) {
					$this->times = new ChaosTime($row['timeS']);
					$era = $this->times->getEra();
					$year = $this->times->getYear();
					$month = $this->times->getMonth();
					$day = $this->times->getDay();
					if ($this->mode == 0)
					{
						$symbol = "<TD colspan=2>";
						if ($row['symbol'] != 0)
						{
							$symbol = '<td><img src="' .$GLOBALS['path_domain_img'] . 'symbols/' . $this->GetImageFromSymbol($row['symbol']) .'"</img></td><td>';
						}
						$retVal .= "<TR>$symbol<TABLE border=0>";
						$retVal .= "\n\t\t<tr>\n\t\t\t<td width=40 class='rep4' >Date</td>";
						$retVal .= "\n\t\t\t<td width=\"800\" class='rep3'>$day day of the $month Month of the $year year in the $era era</td>\n\t\t</tr>";
						$retVal .= "\n\t\t<tr>\n\t\t\t<td class='rep1' colspan='2'>".$row['info']."</td>\n\t\t</tr>\n\t\t<tr><td colspan=3>&nbsp;</td></tr>";					
						$retVal .= "</TABLE></td></tr>";
					}
					else
					{
						$retVal .= "\n\t\t<tr>\n\t\t\t<td class='rep4'>Date</td>";
						$retVal .= "\n\t\t\t<td class='rep3'>$day day of the $month Month of the $year year in the $era era</td>\n\t\t</tr>";
						$retVal .= "\n\t\t<tr>\n\t\t\t<td class='rep1' colspan='2'>".$row['info']."</td>\n\t\t</tr>\n\t\t<tr><td>&nbsp;</td></tr>";
					}
				}
			}

			
			$retVal .= "\n\t</table>";

			if( !isset($_GET['showOld']) && ($this->mode==1)  ) {
				$retVal .= "<br><a href='$_SERVER[PHP_SELF]?showOld=yes' class='rep'>List all news</a><br>";
			}


			$updateNews = "update ".$this->newsTable." set seen='Y' where ".$this->txtID."=$ID";
			$this->dbRef->query($updateNews);
		//	$this->doTick();

			return $retVal;
		}

	function doTick() {
			$DAYS = 24*7;
			$sqlSelectTicks = "select * from Config";
			$this->dbRef->query($sqlSelectTicks);
			$data = $this->dbRef->fetchArray();
			$tickTime = $data['ticks'];
			$sqlDelete = "delete from News where (ABS(timeS - $tickTime) > $DAYS)";
//			echo "<br>$sqlDelete";
			$this->dbRef->query($sqlDelete);
//			echo $this->dbRef->error();
			$sqlDelete = "delete from NewsProvince where (ABS(timeS - $tickTime) > $DAYS)";
//			echo "<br>$sqlDelete";
			$this->dbRef->query($sqlDelete);
//			echo $this->dbRef->error();
	}

	}

?>
