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

if( !class_exists( "RecruitPlayers" ) ) {
class RecruitPlayers {

	var $database			= NULL;
	var $recruitBonus	 	= array ('income'=>1,'acres'=>5);
	var $signupURL 			= "http://www.tkoc.net/reg.html";
	var $MAX_RECRUIT_BONUS	= 5;		// only get bonus from 5 players, after that you'll need to wait an age.
	var $WWW_DIR			= "/home/chaos/www/scripts/";
	var $MIN_PLAY_TIME		= 7;		// minimum play time in real life days
	var $MAX_ACRES_GAIN		= 100;

	// user info	
	var $userID				= 0;		// userID
	var $playersRecruited 	= 0;		// the total number of recruitments
	var $totalBonusCollected= 0;		// 
	var $bonusThisAge		= 0;		// number of recruits this age 
	var $bonusCollected		= true;
	
	function RecruitPlayers (&$database, $userID=0)
	{
		$this->database = $database;
		$this->userID = $userID;
	}
	function doTick()
	{
		$this->database->query("SELECT kiID,userID, Province.pID,Province.provinceName,recruitedBy FROM Province left JOIN User on User.pID=Province.pID WHERE aliveTicks=(".$this->MIN_PLAY_TIME."*24) AND recruitedBy>0 AND recruitBonusCollected='false'");
		if ($this->database->numRows())	
		{
			while ($n[] = $this->database->fetchArray());
			reset($n);
			foreach ($n as $i)
			{
				if ($i['kiID'] < 0)
				{
					$txt = 'Recruit bonus, failed.  Inacitve user!';
					$type = 2;
				} else {
					$type = 1;
					$txt = 'Recruit bonus, success.';
					$this->_giveBonus($i['recruitedBy'], $i['userID']);
				}
				$this->_logBonus($i['recruitedBy'], $i['userID'],$type,$txt);
			}
		}
	}
	
	/****************************************************************************
	 * _logBonus
	 *
	 * Logs the bonus in a log table.
	 *
	 * this function should only be called from dotick
	 *
	 *
	 * Types: 1 = success, 2 = province inactive
	 ***************************************************************************/
	function _logBonus ($userID, $userID_newplayer, $type,$txt)
	{
		$this->database->query("INSERT INTO bonusLog (byUser,toUser,txt,type) VALUES ('$userID_newplayer','$userID','$txt','$type')");
	}
	/****************************************************************************
	 * _giveBonus
	 *
	 * gives bonus to $userID, $userID_newplayer is the new player that has signed up.
	 *
	 * this function should only be called from dotick
	 *
	 ***************************************************************************/

	function _giveBonus ($userID, $userID_newplayer)
	{
		$this->database->query("UPDATE User set recruitBonusCollected='true' WHERE userID='$userID_newplayer'");

		// increase the number of bonuses the user has
		$this->database->query("SELECT pID, recruitBonusThisAge FROM User where userID='$userID'");
		$n = $this->database->fetchArray();
		if ( ($n['recruitBonusThisAge'] < $this->MAX_RECRUIT_BONUS) && ($n['pID'] > 0) ) 
		{
			$pID = $n['pID'];
			$this->database->query("UPDATE User set recruitBonusThisAge=recruitBonusThisAge+1 WHERE userID='$userID'");
			// give bonus to the user.
			require_once($this->WWW_DIR."News.class.inc.php");
			$newsObject = new News($this->database,1,$pID);
			$this->database->query("SELECT incomeChange,metalChange,foodChange,acres FROM Province where pID='".$pID."'");
			$n = $this->database->fetchArray();
			$gold = $n['incomeChange'] * $this->recruitBonus['income'];
			$food = $n['foodChange'] * $this->recruitBonus['income'];
			$metal= $n['metalChange'] * $this->recruitBonus['income'];
			$acres = floor( ($n['acres']*$this->recruitBonus['acres'])/100);
			$acres = min($acres,$this->MAX_ACRES_GAIN);
			$txt = 'You have been granted a bonus of '.$gold .'gc, '.$metal.' metal, '. $food . ' food and '.$acres .' acres by the Gods';
			$newsObject->postNews($txt);
			$this->database->query("UPDATE Province set gold=gold+$gold, metal=metal+$metal, food=food+$food, acres=acres+$acres WHERE pID='".$pID."' LIMIT 1");
			
		}
	}
	/****************************************************************************
	 * getUserData
	 *
	 * Collects recruitdata for a user.
	 *
	 ***************************************************************************/
	function getUserData ()
	{
		if ($this->userID==0) return false;
		$this->database->query("SELECT count(*) as playersRecruited FROM User where recruitedBy='".$this->userID."'");
		$n = $this->database->fetchArray();
		$this->playersRecruited = $n['playersRecruited'];
		$this->database->query("SELECT recruitedBy,recruitBonus, recruitBonusCollected,recruitBonusThisAge FROM User where userID='".$this->userID."'");
		$n = $this->database->fetchArray();
		$this->bonusThisAge = $n['recruitBonusThisAge'];
		$this->bonusCollected = ($n['recruitBonusCollected']=='true') ? true:false;
		return true;
	}
	
	/****************************************************************************
	 * getUserData
	 *
	 * shows recruit data for a user.
	 *
	 ***************************************************************************/
	function showUserData ()
	{
		$html = '<TABLE bgcolor=#CCCCCC cellpadding=5 cellspacing=0 border=0>
					<TR>
						<TD>You have recruited:</TD>
						<TD>'.$this->playersRecruited.'</TD>
					</TR>
					<TR>
						<TD>Recruit bonuses <b>this age</b>:</TD>
						<TD>'.$this->bonusThisAge.'</TD>
					</TR>
					<TR>
						<TD colspan=2>Remember that you will not collect the bonus from a recruited player until he/she has played for 7 real life days.</TD>
					</TR>
					<TR>
						<TD colspan=2>&nbsp;</TD>
					</TR>
					<TR>
						<TD colspan=2>If you wish to recruit a player, please copy and paste this link to him/her:<br>
						<a href="'.$this->signupURL.'?recruitedBy='.$this->userID.'">'.$this->signupURL.'?recruitedBy='.$this->userID.'</a>
						</TD>
					</TR>
					<TR>
						<TD colspan=2>&nbsp;</TD>
					</TR>
					<TR>
						<TD colspan=2>The bonus for recruiting a player is:<br>
						<i>'.$this->recruitBonus['income'].' day(s) worth of income<br>
						'.$this->recruitBonus['acres'].'% increase in acres.</i>
						</TD>
					</TR>
				</TABLE>';
		return $html;
	
	}
	
}
}


?>