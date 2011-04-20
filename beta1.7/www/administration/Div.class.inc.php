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

if( !class_exists( "Index" ) ) {
require_once ($GLOBALS['path_www_scripts'] . 'Database.class.inc.php');
require_once ($GLOBALS['path_www_scripts'] . 'User.class.inc.php');

class Index {

	var $database;
	var $html;

	function Index ($database)
	{
		$this->database = $database;
		$news = new GameNews($this->database);
		$this->html = "<h2>News!</h2><br>".$news->text;
	}
}
}


if( !class_exists( "GameNews" ) ) {
class GameNews {

	var $database;
	var $text;
	var $numNews = 0;
	var $userObj;
	function GameNews ($database, $userObj = false)
	{
		$this->userObj = $userObj;
		$this->database = $database;
		$this->getNews();
	}
	
	function getNews ()
	{
		if (isset($_GET['startmsg']))
			$start = $_GET['startmsg'];
		else $start =0;
	
		$this->numNews = 0;
		$this->text = "";
		if ($this->database->query("SELECT news,header,DATE_FORMAT(timestamp,'%d.%m.%y %H:%i:%s') as time, nick from adminNews LEFT JOIN User on User.userID=adminNews.userID ORDER BY timestamp desc LIMIT $start,10") && $this->database->numRows()>0) {
			$html = "";
			while ($news = $this->database->fetchArray()) {
				$html .="
				<table width=100% border=\"0\" cellspacing=\"0\" cellpadding=\"0\" >
					<TR class=subtitle>
						<TD class=\"TLR\" colspan=\"2\">
						<b>".stripslashes($news['header'])."</b>
						</TD>
					</TR>
					<TR bgcolor=#FFFFFF style=\"color:#000000\">
						<TD class=\"TLR\" colspan=\"2\">
						".stripslashes($news['news'])."<br>&nbsp;<br>
						<i>- $news[nick], $news[time]</i>
						</TD>
					</TR>
				</table>";

			}
		$this->text = $html ."<a href=\"".$_SERVER['PHP_SELF']."?startmsg=".($start+10)."\">next 10 messages</a>";
		}
		return $this->text;
	}
	
	function showCreateNews ()
	{
		if ($this->userObj== false) return "Only for logged in users!";
		
		$html = "	
			<form method=POST action=\"$PHP_SELF\">
	   		<table border=\"0\">
    	  	<tr>
				<th colspan=\"2\">Write the news here: html is allowed.</th>
			</tr>
			<tr>
				<td>Header:</td><td><input type=text style=\"width=250px;\" name=header></td>
			</TR>
	   	  	<tr valign=\"top\">
			  	 <td width=\"150\">Signature: <b>" . $this->userObj->nick . "</b></td><td><TEXTAREA name=news cols=50 rows=10></TEXTAREA></td>
			</tr>
			<tr>
				<td>&nbsp </td><td><input type=submit name=step value=\"Preview\">&nbsp&nbsp<input type=submit value=\"Post\" name=step></td>
			</tr>
			</table>
			</form>";
		return $html;

	}
	function handleInput ()
	{
		switch ($_POST['step']) {
	      case "Post":
   		     $this->database->query("INSERT INTO adminNews (news,header,userID,timestamp) values ('$_POST[news]','$_POST[header]','".$this->userObj->userID."',NOW())");
	   	     $html .= "<strong><i>News added to database.</i></strong>";
			 return $html . $this->showCreateNews() . "<br>" . $this->getNews();
			 break;
		
			default:
				return $this->showCreateNews() . "<br>" . $this->getNews();
		}
	
	}
	
}
}

?>