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
/* Kingdom managment class
 *
 */
if( !class_exists( "Kingdom" ) ) {
require_once($GLOBALS['path_www_scripts'] . "Province.class.inc.php");
require_once($GLOBALS['path_root'] . "includes/security.php");
class Kingdom {
	var $database		= false;
	var $kingdomID		= 0;
	var $provinces      = array();
	var $numProvinces   = 0;
	var $totalNetworth  = 0;
	var $banner         = false;
	var $signature		= "";
	var $feedback 		= "";
	var $kingdomPassword= "";
	var $king           = false;
	var $kingdomName    = "";
	var $relationWar    = 0;
	var $relationAlly   = 0;
	var $relationMerge  = false;
	var $relationWarTick = 0;
	
	// external kingdom relations
	var $declaredWarOnUs  = array();
	var $declaredAllyOnUS = array();
	
	function Kingdom ($db, $kingdomID=false) {
		$this->kingdomID = $kingdomID;
		$this->database = $db;
	}
	function doTick () 
	{
		$this->database->query("UPDATE Kingdom set relationWarTick = relationWarTick-1 where relationWarTick > 0");
	//SELECT Kingdom1.kiID AS kiID1, Kingdom1.name AS name1 ,Kingdom1.relationAlly AS relationAlly1, Kingdom2.kiID AS kiID2, Kingdom2.name AS name2, Kingdom2.relationAlly AS relationAlly2 FROM Kingdom AS Kingdom1 LEFT JOIN Kingdom AS Kingdom2 on Kingdom1.relationAlly=Kingdom2.kiID WHERE Kingdom1.relationAlly=Kingdom2.kiID AND Kingdom2.relationAlly=Kingdom1.kiID AND Kingdom1.relationMerge='true' AND Kingdom2.relationMerge='true'
		$mergeList = array();
		$mergedKingdoms = array();
		$sql = "SELECT Kingdom1.kiID AS kiID1, Kingdom1.name AS name1 ,Kingdom1.relationAlly AS relationAlly1, 
		               Kingdom2.kiID AS kiID2, Kingdom2.name AS name2, Kingdom2.relationAlly AS relationAlly2
					       FROM Kingdom AS Kingdom1 LEFT JOIN Kingdom AS Kingdom2 on Kingdom1.relationAlly=Kingdom2.kiID
						        WHERE Kingdom1.relationAlly=Kingdom2.kiID 
								AND Kingdom2.relationAlly=Kingdom1.kiID 
								AND Kingdom1.relationMerge='true' AND Kingdom2.relationMerge='true'";
		if ($this->database->query($sql) && $this->database->numRows())
		{
			while ($item=$this->database->fetchArray()) 
			{
				$mergeList[$item['kiID1']] = $item;
			}
			
			// TODO: stuff.
			reset($mergeList);
			foreach ($mergeList as $merge)
			{
				if (is_array($mergedKingdoms) && array_search($merge['kiID1'],$mergedKingdoms))
				{
//					echo "Already done this!<br>";
				} else
				{
//					echo "$merge[name1] with $merge[name2]<br>";
					$this->tryMerge($merge['kiID1'],$merge['kiID2']);
					$mergedKingdoms[] = $merge['kiID1'];
					$mergedKingdoms[] = $merge['kiID2'];
				}
			}
		}		
		return true;
	}
	function tryMerge($kiID1, $kiID2)
	{
		require_once($GLOBALS['path_www_scripts'] . "News.class.inc.php");
		require_once($GLOBALS['path_www_scripts'] . "ActionLogger.class.inc.php");
		
		// TODO!  check total number of players abs(kiID), because inactive players must be counted as well!
		// then merge kiID2 => kiID1
		$this->database->query("SELECT COUNT(*) AS num from Province where ABS(kiID)='$kiID1' AND status='Alive'");
		$num1 = $this->database->fetchArray();
		$num1 = $num1['num'];
		$this->database->query("SELECT COUNT(*) AS num from Province where ABS(kiID)='$kiID2' AND status='Alive'");
		$num2 = $this->database->fetchArray();
		$num2 = $num2['num'];
		if (($num1 + $num2) <= $GLOBALS['config']['maxProvinceInKD'])
		{
			// report merging to the ones we're at war with as well???
			$this->database->query("UPDATE Province set kiID='$kiID1' WHERE kiID='$kiID2'");
			$this->database->query("UPDATE Kingdom set numProvinces='".($num1+$num2)."', relationMerge='false', relationWar='0', relationAlly='0' WHERE kiID='$kiID1'");
			$this->database->query("UPDATE Kingdom set numProvinces='0',name='Merged with #".$kiID1."', password='',banner='',relationMerge='false', relationAlly='0' WHERE kiID='$kiID2'");
			$news = new News($this->database);
			$news->postNews("We have just <font color=cyan>MERGED</font> with our allies!",$kiID1, $news->SYMBOL_POLITICS );
			$news->postNews("This kingdom has merged with #".$kiID1."",$kiID2, $news->SYMBOL_POLITICS);
			$actionLogger = new ActionLogger($this->database);
			$actionLogger->log($actionLogger->MERGE, $kiID1, $kiID2,$actionLogger->NOVALUE,true);

		}
	}
	function setKingdomPassword ($password)
	{
		$this->database->query("UPDATE Kingdom set password='$password' where kiID='".$this->kingdomID."'");
		$this->kingdomPassword=$password;
	}
	
	function setKingdomName ($name)
	{
		$name = htmlspecialchars($name);
		$this->database->query("UPDATE Kingdom set name='$name' where kiID='".$this->kingdomID."'");
		$this->kingdomName=$name;	
	}
	
	function setKingdomBanner ($banner)
	{
		if (preg_match('/^http/',$banner,$arr))
		{
			$this->database->query("UPDATE Kingdom set banner='$banner' where kiID='".$this->kingdomID."'");
			$this->banner=$banner;
			return true;
		}
		else
		{
			if (strlen($banner) == 0)
			{
				$this->database->query("UPDATE Kingdom set banner='' where kiID='".$this->kingdomID."'");
				$this->banner = '';
				return true;
			}
			return false;
		}
	}
	
	function setKingdomSignature ($signature)
	{
		//$signature = htmlspecialchars($signature);
		$security = new Security;
		$signature = $security->sanitizeData ($signature, "string");
		if (mb_strlen($signature) > 100)
			$this->feedback = '<span style="color:red;">Your signature is greater than the maximum length of 100 characters.</span>';
		else if (mb_strlen($signature) == 0)
			$this->feedback = '<span style="color:red;">You either put an invalid signature or you didn\'t type any signature at all.</span>';
		else {
			$this->database->query("UPDATE Kingdom set signature='$signature' where kiID='".$this->kingdomID."'");
			$this->feedback = '<span style="color:green;">You have updated your Kingdom\'s signature succesfully.</span>';
		}
		$this->signature=$signature;
	}
	
	function setWar ($ID)
	{
		$this->database->query("UPDATE Kingdom set relationWar='$ID', relationWarTick=24 where kiID='".$this->kingdomID."'");
		$this->relationWar = $ID;
	}
	
	function GetWarKingdoms()
	{
		$war = array();
                if (is_array($this->declaredWarOnUs) && (count($this->declaredWarOnUs)>0))
                {
                        foreach ($this->declaredWarOnUs as $w)
                        {

				$war[] = $w['kiID'];
			}
		}
		if ($this->relationWar > 0)
		{
			$war[] = $this->relationWar;
		} 
		return $war;
	}
	
	function setAlly ($ID)
	{
		$this->database->query("UPDATE Kingdom set relationAlly='$ID' where kiID='".$this->kingdomID."'");
		$this->relationAlly = $ID;
	}
	
	function declareWar($war)
	{
		if (($war == $this->kingdomID) || ($war==0)) return false;

		$this->loadKingdom();		
		if ($this->relationWarTick != 0)
			return false;



		$this->setWar($war);
	    require_once($GLOBALS['path_www_scripts'] . "News.class.inc.php");
		$warKD = new Kingdom($this->database, $war);
		$warKD->loadKingdom();
		$news = new News($this->database);
		$news->postNews("We have just declared <font color=red>WAR</font> on " . $warKD->getFullName(),$this->kingdomID, $news->SYMBOL_POLITICS);
		$news->postNews($this->getFullName() . " has just declared <font color=red>WAR</font> on us!",$warKD->kingdomID, $news->SYMBOL_POLITICS);
	}
	
	function endWar()
	{
		$this->loadKingdom();		
		if ($this->relationWarTick != 0)
			return false;

	    require_once($GLOBALS['path_www_scripts'] . "News.class.inc.php");
		$warKD = new Kingdom($this->database, $this->relationWar);
		$warKD->loadKingdom();
		$news = new News($this->database);
		$news->postNews("We have just ended the <font color=red>WAR</font> on " . $warKD->getFullName(),$this->kingdomID, $news->SYMBOL_POLITICS);
		$news->postNews($this->getFullName() . " has just ended the <font color=red>WAR</font> on us!",$warKD->kingdomID, $news->SYMBOL_POLITICS);	
		$this->setWar(0);
	}
	function declareAlly($ally)
	{
		$warKD = new Kingdom($this->database, $this->relationWar);
		if (($warKD->kingdomID == $this->kingdomID) || ($ally==0)) return false;
		$this->setAlly($ally);
	    require_once($GLOBALS['path_www_scripts'] . "News.class.inc.php");
		$allyKD = new Kingdom($this->database, $ally);
		$allyKD->loadKingdom();
		$news = new News($this->database);
		$news->postNews("We have just declared " . $allyKD->getFullName() . " to be an <font color=cyan>ALLIED</font> kingdom",$this->kingdomID,$news->SYMBOL_POLITICS);
		$news->postNews($this->getFullName() . " has just declared us to be an <font color=cyan>ALLIED</font> kingdom!",$allyKD->kingdomID,$news->SYMBOL_POLITICS);
	}
	function endAlly()
	{
	    require_once($GLOBALS['path_www_scripts'] . "News.class.inc.php");
		$allyKD = new Kingdom($this->database, $this->relationAlly);
		$allyKD->loadKingdom();
		$news = new News($this->database);
		$news->postNews("We ended the alliance with " . $allyKD->getFullName() . "",$this->kingdomID,$news->SYMBOL_POLITICS);
		$news->postNews($this->getFullName() . " just ended their alliance with us!",$allyKD->kingdomID,$news->SYMBOL_POLITICS);
		$this->setAlly(0);
	}

	function getFullName()
	{
		return $this->kingdomName  .'(#&nbsp;' .$this->kingdomID .')';
	}
	function setMerge($value)
	{
		if ($value=='true')
			$this->relationMerge = true;
		else
			$this->relationMerge = false;
		$this->database->query("UPDATE Kingdom set relationMerge='$value' WHERE kiID='".$this->kingdomID."'");
	}
	function handlePost()
	{
		if (isset($_POST['kingdomNameChange']))
		{
			$this->setKingdomName($_POST['kingdomName']);
		}
		if (isset($_POST['kingdomPasswordChange']))
		{
			$this->setKingdomPassword($_POST['kingdomPassword']);
		}
		if (isset($_POST['kingdomBannerChange']))
		{
			$this->setKingdomBanner($_POST['kingdomBanner']);
		}
		if (isset($_POST['kingdomSignature']))
		{
			$this->setKingdomSignature($_POST['kingdomSignature']);
		}
		if (isset($_POST['kingdomWarChange']))
		{
			if ($this->relationWar == 0)
				$this->declareWar($_POST['kingdomWar']);
			else
				$this->endWar();
		}
		if (isset($_POST['kingdomAllyChange']))
		{
			if ($this->relationAlly == 0)
				$this->declareAlly($_POST['kingdomAlly']);
			else
				$this->endAlly();
		}
		if (isset($_POST['kingdomMergeChange']))
		{
			$merge = 'false';
			if (isset($_POST['kingdomMerge']))
				$merge = 'true';
			$this->setMerge($merge);
		}		
	}
	function showKingoptions()
	{
		if ($this->relationWar != 0)
			$warText = "Cancel War";
		else
			$warText = "Declare War";
		if ($this->relationAlly != 0)
			$allyText = "Cancel Alliance";
		else
			$allyText = "Ally";
		
		if ($this->feedback != "") {
			$this->feedback = '<tr>'.$this->feedback.'</tr>';
		}
		$html = '<table cellpadding=5 cellspacing=3 width="60%">';
		$html .= $this->feedback;
		$html .= '<tr class="subtitle">
						<td colspan=3><center>Kingdom of '.$this->getFullName().'</center></td>	
					</tr>
					<tr>
						<form action="'.$_SERVER['PHP_SELF'].'" method="POST">';
$html .= $GLOBALS['fcid_post']; 
$html .= '
						<td><b>Kingdom Name</b></td>
						<td><input type="text" size="30" class="form" name="kingdomName" value="'.$this->kingdomName.'"></td>
						<td><input type="submit" class="form" name="kingdomNameChange" value="Change"></td>
						</form>
					</tr>
					<tr>
						<form action="'.$_SERVER['PHP_SELF'].'" method=POST>';
$html .= $GLOBALS['fcid_post'];
$html .= '
						<td><b>Kingdom Password</b></td>
						<td><input type=text size=30 class="form" name=kingdomPassword value="'.$this->kingdomPassword.'"></td>
						<td><input type=submit class="form" name=kingdomPasswordChange value=Change></td>
						</form>
					</tr>
					<tr>
						<td colspan=3>If the kingdom password is set, only people with the correct password will be able to join.  If you want new players into your kingdom, I strongly suggest you leave this field empty.</td>	
					</tr>
					<tr>
						<form action="'.$_SERVER['PHP_SELF'].'" method=POST>';
$html .= $GLOBALS['fcid_post'];
$html .= '
						<td><b>Kingdom Banner</b></td>
						<td><input type=text size=30 class="form" name=kingdomBanner value="'.$this->banner.'"></td>
						<td><input type=submit class="form" name=kingdomBannerChange value=Change></td>
						</form>
					</tr>
					<tr>
						<td colspan=3>The kingdom banner will be visible to all people who scans your kingdom.  You can put a friendly warning here, or just create something that looks cool.  Either way, this is something that will identify your kingdom.  If you dont have a banner now, you can always create one later!</td>	
					</tr>
					<tr>
						<form action="'.$_SERVER['PHP_SELF'].'" method=POST>';
$html .= $GLOBALS['fcid_post'];
$html .= '
						<td><b>Kingdom Signature</b></td>
						<td><input type=text size="60" class="form" name="kingdomSignature" value="'.$this->signature.'"><span style="font-size:11px">100 Characters length</span></td>
						
						<td><input type=submit class="form" name="kingdomSignatureChange" value="Change"></td>
						</form>
					</tr>
					<tr>
						<td colspan=3>The kingdom banner will be visible to all people who scans your kingdom.  You can put a friendly warning here, or just create something that looks cool.  Either way, this is something that will identify your kingdom.  If you dont have a banner now, you can always create one later!</td>	
					</tr>
					<tr>
						<form action="'.$_SERVER['PHP_SELF'].'" method=POST>';
$html .= $GLOBALS['fcid_post'];
$html .= '
						<td><b>War</b></td>
						<td><input type=text size=5 class="form" name=kingdomWar value="'.$this->relationWar.'">('.$this->relationWarTick.' days left)</td>
						<td><input type=submit class="form" name=kingdomWarChange value="'.$warText.'"></td>
						</form>
					</tr>
					<tr>
						<form action="'.$_SERVER['PHP_SELF'].'" method=POST>';
$html .= $GLOBALS['fcid_post'];
$html .= '
						<td><b>Ally</b></td>
						<td><input type=text size=5 class="form" name=kingdomAlly value="'.$this->relationAlly.'"></td>
						<td><input type=submit class="form" name=kingdomAllyChange value="'.$allyText.'"></td>
						</form>
					</tr>
					<tr>
						<td colspan=3><b>You can declare official WAR</b> on a kingdom. This will help you coordinate your attacks on one single enemy. The war is mutual.<br>
						<b>You can also declare a kingdom as an ALLY</b>. If two small kingdoms are declared as allies, they can merge the kingdoms into one big kingdom. To merge, both kingdoms must be allied, and have the merge option turned on. Normal kingdom limitations apply. 
						</td>	
					</tr>
					';
				if ($this->relationAlly != 0)
				{
					$html .= '
					<tr>
						<form action="'.$_SERVER['PHP_SELF'].'" method=POST>';
$html .= $GLOBALS['fcid_post'];
$html .= '
						<td><b>Merge</b></td>
						<td><input type=checkbox class="form" name=kingdomMerge';
						if ($this->relationMerge == true)
						{
							$html .= " CHECKED";
						}
						$html .='></td>
						<td><input type=submit class="form" name=kingdomMergeChange value="Change"></td>
						</form>
					</tr>';				
				}
				$html .='
				 </table>';
		return $html;
	
	}
	//warning!  Cutn paste from old bad code.
	function updateKing () {
		$kID = $this->kingdomID;
		$this->loadKingdom();
		$this->database->query("SELECT count(voteFor) as votes, voteFor as pID from Province where kiID=$kID GROUP by voteFor order by votes DESC");
		$newKing = $this->database->fetchArray(); 
		if (($this->getRequiredKingVotes() <= $newKing['votes']) && $this->king != $newKing['pID']) {
			require_once($GLOBALS['path_www_scripts'] . "News.class.inc.php");
			$news = new News($this->database);
			$kingname = "unknown";
			foreach ($this->provinces as $p)
			{
				if ($p->pID == $newKing['pID'])
				{
					$kingname = $p->provinceName;
				}
			}
			$news->postNews("$kingname is now the new king of our Kingdom!",$kID, $news->SYMBOL_POLITICS);
   			$this->database->query("UPDATE Kingdom set king=$newKing[pID] where kiID=$kID");
		}
	}
	
	function getRequiredKingVotes()
	{
		return round($this->numProvinces * 0.5);
	}
	
	function getKingCandidates()
	{
		$html = "";
		$cand = "";
		$this->database->query("SELECT count(voteFor) as votes, voteFor as pID from Province where kiID=$this->kingdomID GROUP by voteFor order by votes DESC");
		while (($candidate = $this->database->fetchArray()))
		{
			if ($candidate['pID'])
			{
				$cand[] = $candidate;
			}
		}
		if (is_array($cand))
		{
			$html = '<table><tr class="subtitle"><td colspan=2>Candidates</tr>';
			foreach ($cand as $candidate)
			{
				$c = new Province($candidate['pID'], $this->database);
				$c->getProvinceData();
				if ($c->status == 'Alive')
				{
					$html .= "<tr>";
					$html .= "<td>".$c->rulerName." in ". $c->provinceName."</td><td>".$candidate['votes']." votes</td>";
					$html .= "</tr>";
				}
			}
		
			$html .= "</table>";
		}
		return $html;
	}
	
	function createSelectBox()
	{
		$html = '
			<select name=selectProvince class="form">';
		$this->database->query("SELECT provinceName, pID from Province where  kiID='".$this->kingdomID."'");
		while (($item=$this->database->fetchArray())){
			$html .= "<option";
			if (isset($_POST['selectProvince']) && ($_POST['selectProvince']==$item['pID'])) $html .= " SELECTED";
			$html .=" value=\"$item[pID]\">" . htmlspecialchars($item['provinceName']) . "</option>";
		}
		$html .='	</select>';
		
		return $html;
	}
	function loadKingdom ($kingdomID=false) {
		if ($kingdomID!== false) {
			$this->kingdomID = $kingdomID;
		}
		if ($this->database->query("SELECT * from Kingdom WHERE kiID=$this->kingdomID") && $this->database->numRows() ){
			$data = $this->database->fetchArray();
			$this->kingdomName = $data['name'];
			$this->numProvinces = $data['numProvinces'];
			$this->king = $data['king'];
			$this->banner = htmlspecialchars(stripslashes($data['banner']));
			$this->signature = $data['signature'];
			$this->kingdomPassword = $data['password'];
			$this->relationWar     = $data['relationWar'];
			$this->relationWarTick = $data['relationWarTick'];
			$this->relationAlly    = $data['relationAlly'];
			$this->relationMerge   = ($data['relationMerge']=='true') ? true:false;
			$query = "SELECT pID,kiID, provinceName,status,reputation,magicRep,vacationmode ,gender,protection, UNIX_TIMESTAMP(created) as created,aliveTicks, spID as SpeciesID, acres, networth 
						FROM Province where kiID=$this->kingdomID ORDER BY networth DESC";
			if ($this->database->query($query) && $this->database->numRows()) {
				$counter =0;
				while (($data = $this->database->fetchArray())) {
					$temp[] = $data;
				}
				reset($temp);
				foreach ($temp as $data) {
					$data['king'] = $this->king;
					$this->provinces[$counter] = new Province ($data['pID'],$this->database);
					$this->provinces[$counter]->setProvinceData($data);
					$counter++;
				}
			$query = "SELECT name,kiID,relationWar,relationAlly FROM Kingdom WHERE relationWar=$this->kingdomID or relationAlly=$this->kingdomID";
			if ($this->database->query($query) && $this->database->numRows()) {
				while (($data = $this->database->fetchArray())) {
					if ($data['relationWar'] == $this->kingdomID)
						$this->declaredWarOnUs[] = $data;
					if ($data['relationAlly'] == $this->kingdomID)
						$this->declaredAllyOnUs[] = $data;
				}
			}			
			} else {
				$this->numProvinces= 0;
				$this->kingdomName = "Empty Kingdom";
			}
		} else {
			$this->numProvinces= 0;
			$this->kingdomName = "Empty Kingdom";
			return false;
		}
		
	}
	
	function showKingdom ($kingdomID=false) {
		$body = "";
		if ($kingdomID!== false) {
			$this->loadKingdom( $kingdomID );
		}
		
			////////////////////////////////////////
			// Displays the Kingdomname and id
			////////////////////////////////////////
		if (strlen($this->banner)>1) {
			$body .= '<center><img src="'.$this->banner.'" width=400 height=200><br>&nbsp</center>';
		}
		if (mb_strlen($this->signature)!=0) {
			$body .= '<center>'.$this->signature.'</center><br />';
		}
		if ($this->relationWar > 0)
		{
			$warKD = new Kingdom($this->database,$this->relationWar);
			$warKD->loadKingdom();
			$body .= "<center>At <font color=red>WAR</font> with " . $warKD->getFullName() ."</center>";
			
		}
		if (is_array($this->declaredWarOnUs) && (count($this->declaredWarOnUs)>0))
		{
			reset($this->declaredWarOnUs);
			foreach ($this->declaredWarOnUs as $w)
			{
				$body .= "<center>At <font color=red>WAR</font> with " . $w['name'] ."(#".$w['kiID'] .")"."</center>";
			}
		}
		$body .= '<table align="center" width="90%" nowrap cellspacing="2">
		   				<tr>
							<td colspan="6" class="rep3" align="center">
							<center><strong>Kingdom of:&nbsp;&nbsp;&nbsp;' .$this->kingdomName  .' (#' .$this->kingdomID .')</strong></center>
							</td>
						</tr>';

			////////////////////////////////////////
			// Builds table header
			////////////////////////////////////////

		$body .= '<tr>
						<td align="center" nowrap class="rep3">
							<b>Name:</b>
						</td>
						<td align="center" class="rep3">
							<b>Race:</b>
						</td>
						<td align="center" class="rep3">
							<b>Acres:</b>
						</td>
						<td align="center" class="rep3">
							<b>Networth:</b>
						</td>
						<td align="center" class="rep3">
							<b>Thief rank:</b>
						</td>
<td align="left" width="10%" class="rep3">
<b>Magic rank:</b>
</td>

					</tr>';

		if ($this->numProvinces>0) {
			reset($this->provinces);
			foreach ($this->provinces as $province) {
			$uglyhack = false;
			if ($this->kingdomID != $GLOBALS['province']->kiId)
				$uglyhack=true;
//			print_r($province);
				$body .= "<tr><td align='left' class='rep1'>";
				$body .= "<a href=\"provinceAction.php?victim=".$province->pID."\" class='rep'>". $province->provinceName;
				if ($province->isKing()) 
					$body .="&nbsp;<b>(".$province->getShortTitle().")</b>";
				
				if ($province->status == "Alive") {
					if ($province->vacationmode==true)
						$body .=" (Vacation)";
					else if ($province->isProtected() && $province->vacationmode==false)
						$body .= " (Protected)";
				}
				else
					$body .= " (".$province->status.")";
				
				$body .= "</a>";

				$row['speciesName'] = $province->race;
				
				$row['acres']=$province->acres;
				$row['networth']=$province->networth;
				
				$body .= "</td><td align='center' class='rep1'>" .$row['speciesName'] ."
					<td align='center' class='rep1'>" .number_format($row["acres"],0,' ',','). "</td>
					<td align='center' class='rep1'>" .number_format($row["networth"],0,' ',',') ."</td>
					<td align='center' class='rep1'>".(($uglyhack) ? "N/A" : $province->getThieveryRank())."</td>
<td align='center' class='rep1'>".(($uglyhack) ? "N/A" : $province->getMagicRank())."</td>					
</tr>";
			}

			$body .= "</table>";
			$body .= "<br><br><center><img src='../img/hor_ruler.gif' border='0'></img></center>";
			return $body;
		} else {
			return $body;
		}
	}
}
}

?>
