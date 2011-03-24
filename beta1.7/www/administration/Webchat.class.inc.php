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

require_once ("../scripts/globals.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Database.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "User.class.inc.php");
require_once ($GLOBALS['path_www_administration'] . "admin_all.inc.php");
require_once ($GLOBALS['path_www_administration'] . "Div.class.inc.php");

class Webchat {
	var $user = NULL;
	var $database = NULL;
	var $appletCode = "";
	var $formCode = "";

	function Webchat($dbRef, $provRef) {
		$this->user = $provRef;
		$this->database = $dbRef;
	}

	function createAppletCode() {
		$this->appletStart();

		$this->addParam("CABINETS" ,"irc.cab,securedirc.cab,pixx.cab");
		if(!isset($this->user->chatName)) $this->user->chatName = "tkocU".$this->user->userID;
		$this->addParam("nick", substr($this->user->chatName, 0, 9));
		$this->addParam("alternatenick", substr($this->user->chatName, 0, 4)."?????");
		$this->addParam("name", "tkoc player");															//NEED FIX				
		$this->addParam("host", "efnet.xs4all.nl");
		$this->addParam("gui", "pixx");

		$this->addParam("alternateserver1", "irc.efnet.nl 6667");
		$this->addParam("alternateserver2", "irc.daxnet.no 6667");
		$this->addParam("alternateserver3", "irc.prison.net 6667");
		$this->addParam("alternateserver4", "irc.efnet.net 6667");

		$this->addParam("command1", "/join #tkoc");


		$this->addParam("userid", "u".$this->user->userID);														

		$this->addParam("autoconnection", "true");
		$this->addParam("authorizedcommandlist", "all-ctcp-dcc-ignore-j-join-kick-leave-load-list-newserver-part-raw-topic-unload");

		$this->addParam("quitmessage", "#tkoc Webchat - ".$this->user->chatName);



		$this->addParam("style:bitmapsmileys", "true");
		$this->addParam("style:smiley1", ":) img/sourire.gif");
		$this->addParam("style:smiley2", ":-) img/sourire.gif");
		$this->addParam("style:smiley3", ":-D img/content.gif");
		$this->addParam("style:smiley4", ":d img/content.gif");
		$this->addParam("style:smiley5", ":-O img/OH-2.gif");
		$this->addParam("style:smiley6", ":o img/OH-1.gif");
		$this->addParam("style:smiley7", ":-P img/langue.gif");
		$this->addParam("style:smiley8", ":p img/langue.gif");
		$this->addParam("style:smiley9", ";-) img/clin-oeuil.gif");
		$this->addParam("style:smiley10", ";) img/clin-oeuil.gif");
		$this->addParam("style:smiley11", ":-( img/triste.gif");
		$this->addParam("style:smiley12", ":( img/triste.gif");
		$this->addParam("style:smiley13", ":-| img/OH-3.gif");
		$this->addParam("style:smiley14", ":| img/OH-3.gif");
		$this->addParam("style:smiley15", ":'( img/pleure.gif");
		$this->addParam("style:smiley16", ":$ img/rouge.gif");
		$this->addParam("style:smiley17", ":-$ img/rouge.gif");
		$this->addParam("style:smiley18", "(H) img/cool.gif");
		$this->addParam("style:smiley19", "(h) img/cool.gif");
		$this->addParam("style:smiley20", ":-@ img/enerve1.gif");
		$this->addParam("style:smiley21", ":@ img/enerve2.gif");
		$this->addParam("style:smiley22", ":-S img/roll-eyes.gif");
		$this->addParam("style:smiley23", ":s img/roll-eyes.gif");

		$this->addParam("pixx:highlight", "true");
		$this->addParam("pixx:highlightnick", "true");
		$this->addParam("pixx:showchanlist", "false");
		$this->appletEnd();
	}

	function appletStart() {
		$this->appletCode .= "\n\t<applet name='ircApplet' codebase=webchat code='IRCApplet.class' archive='irc.jar,pixx.jar' width=640 height=400>\n";
	}

	function appletEnd() {
		$this->appletCode .= "</applet>";
	}

	function addParam($name, $value) {
		$param = "<param name='".$name."' value='".$value."'>";
		$this->appletCode .= "\t\t".$param."\n";
	}

	function getAppletHTML() {
		$this->handleNickChange();
		$this->createAppletCode();
		$this->createFormCode();
		$applHTML = "";
//		$applHTML .= $this->ircUserguide();
		$applHTML .= $this->appletCode;
		$applHTML .= "<br><br>".$this->formCode;
		$this->appletCode = "";
		return "<center>".$applHTML."</center>";
	}

	function startTable() {
		$startTableStr = "";
		$startTableStr = "<table size='90%'><tr>";
		
		return $startTableStr;
	}

	function endTable() {
		$endTableStr = "";
		$endTableStr = "</tr></table>";
		return $endTableStr;
	}

	function ircUserGuide() {
		$usrGuide = "";
		return $usrGuide;
	}

	function createFormCode() {
		$formCode = "\n<form name='changeNick' class='form' action='".$_SERVER['PHP_SELF']."' method='post'>";
		$formCode .= "\n\tNew nickname: <input type='text' name='newNick' value='".$this->user->chatName."' class='form' size='10'>";
		$formCode .= "\n\t<input type='button' value='Change Nick' onClick=\"document.ircApplet.sendString('/nick '+document.changeNick.newNick.value);document.ircApplet.requestFocus()\">";
		$formCode .= "\n\t<input type='submit' name='changeNickOK' value='Change Default Nickname' class='form'>";
		$formCode .= "\n</form>";
		$this->formCode = $formCode;
	}

	function handleNickChange() {
		if( (isset($_POST['changeNickOK'])) && (strcmp($this->user->chatName, $_POST['newNick']) != 0) ) {
			$newNickName = substr($_POST['newNick'], 0, 9);
			$this->user->updateChatName($newNickName);
		}
	}
}

?>
