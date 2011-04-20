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

require_once ($GLOBALS['path_www_scripts'] . "Database.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "bbcode.inc.php");  // bb code!

if ( !class_exists("MainForum") ) {
// add code enteries.

class MainForum {
	var $path = "forumpath()";
	var $database;
	var $userObj;
	var $forumArray;
	var $activeForum = false;
	var $access= 1;
	var $html = "";
	var $loadStat = true;
	var $provinceObj = false;
	
	function IsIngameForum() { return false; }


	function MainForum ($database,$userObj=false)
	{
		$this->database = $database;
		$this->userObj = $userObj;
		if (!$this->userObj) $this->access = $GLOBALS['constants']->USER_NORMAL;
		else $this->access = $this->userObj->access;
		$this->handleInput();
		$this->getActiveForum();
		$this->provinceObj = $GLOBALS['province'];		
	}

	function GetForumDropDown($name="ForumDropDown")
	{
		$html = '<SELECT NAME="'.$name.'" class="form">';
		$sql = "SELECT ForumName,ForumID, Access FROM ForumMain where (access & $this->access) AND (ForumMain.kiID<0) GROUP BY ForumID ORDER BY ForumName DESC";

		if ($this->database->query($sql) && $this->database->numRows()) 
		{
			while ($f = $this->database->fetchArray()) 
			{
				$html .= '<OPTION value="'.$f['ForumID'].'">'.$f['ForumName'].'</OPTION>';
			}
		}
		$html .= '</SELECT>';
		return $html;
	}


	function LoadAllForums ()
	{
			$sql = "SELECT ForumName,ForumDescription,MaxThreads, ForumID, Access,MAX(ForumPost.PostTime) as 
lastPost,ForumThread.ThreadName,ForumThread.ThreadID FROM ForumMain left JOIN ForumPost on ForumPost.PostForumID=ForumID LEFT JOIN ForumThread on 
ForumPost.PostThreadID=ForumThread.ThreadID where (access & " . $this->access . ") AND (ForumMain.kiID<0) GROUP BY ForumID ORDER BY lastPost DESC";
			if (intval($this->userObj->access) & $GLOBALS['constants']->USER_ADMIN) 
				if (isset($_REQUEST['forceShowAll']))
				{
					$sql = "SELECT ForumName,ForumDescription,MaxThreads, ForumID, Access,MAX(ForumPost.PostTime) as lastPost,ForumThread.ThreadName,ForumThread.ThreadID FROM ForumMain left JOIN ForumPost on ForumPost.PostForumID=ForumID LEFT JOIN ForumThread on ForumPost.PostThreadID=ForumThread.ThreadID where (access & $this->access) GROUP BY ForumID ORDER BY lastPost DESC";
					$this->loadStat= false;
				}
			if ($this->database->query($sql) && $this->database->numRows()) {
				while (($f = $this->database->fetchArray())) {
					$tmp[] = $f;
				}
				foreach ($tmp as $f) {
					$arr[] = new Forum ($this->database,$this,$this->userObj,$f);
				}
				reset($arr);
				foreach ($arr as $a) {
					if ($this->loadStat)
						$a->loadStatistics();
					$this->forumArray[] = $a;
				}
				reset($this->forumArray);
			} 
	}
	
	function getActiveForum ()
	{
		if (isset($_REQUEST['forumID'])) {
			$arr['ForumID'] = $_REQUEST['forumID'];
			if ($this->checkOK())
			{
				$this->activeForum = new Forum ($this->database,$this,$this->userObj,$arr);
				$this->activeForum->userObj = $this->userObj;
			}
			else
			{
				die("This hacking attemt has been logged.");
			}
		} 
		else 
		{
			$this->LoadAllForums();
		} // else
	}

	function checkOK()
	{
		if (intval($this->userObj->access) & $GLOBALS['constants']->USER_ADMIN) 
			return true;  // skip check for admins.. they have access to all stuff.
		if (isset($_REQUEST['threadID']))
		{
			$this->database->query("SELECT ThreadForumID FROM ForumThread WHERE ThreadID='".$_REQUEST['threadID']."'");
			if ($this->database->numRows())
			{
				$check = $this->database->fetchArray();
				if ($check['ThreadForumID'] != $_REQUEST['forumID']) return false;
			}
			else return false;
		}
		
		if (isset($_REQUEST['forumID']))
		{
			$this->database->query("SELECT kiID FROM ForumMain where ForumID='".$_REQUEST['forumID']."' AND kiID<0");
			if ($this->database->numRows())
				return true;
			else
				return false;
		}
		
		return true;
		
	}

	
	function showPath ()
	{
		$html = '<table cellpadding=5 cellspacing=0><tr class="subtitle" ><td class="TLRB"><a class= biggerblack href="'.$_SERVER['PHP_SELF'] . $GLOBALS['fcid'] . '">Forum @ tkoc:</a>';
		if ($this->activeForum)
			$html .=" &gt " . $this->activeForum->showPath();
		$html .= '</td></tr></table>';
		return $html;
	}
	
	function showLogOff()
	{
		if ($this->userObj->access == $GLOBALS['constants']->USER_NORMAL)
			$nick = "Guest";
		else $nick = $this->userObj->username;
		//$html = '<table cellpadding=5 cellspacing=0><tr class="subtitle" ><td class="TLRB"><a class= biggerblack href="'."http://www.tkoc.net/logoff.php".'">Log out user: '.$nick.'</a>';
		return $html;
	}
	
	function showForum ()
	{
		$html = "";
//		if ($this->provinceObj->kiId < 0) return "You dont have access to this fourm!!!";
		if (isset($_GET['step']) && $_GET['step']=='showeditpost') {
			if (isset($_GET['postID'])) $arr['postID'] = $_GET['postID'];
			else return "Error! No post to edit.";

			$post = new Post($this->database,$this->userObj,$arr);
			$post->loadData($_GET['postID']);
			$this->html .= $post->showEdit();
		}

		if ($this->activeForum) {
			$html .= $this->activeForum->showForum();		
		} else {		// show all forums....
			if ($this->access & $GLOBALS['constants']->USER_ADMIN)
				$html .= '<FORM ACTION="'.$_SERVER['PHP_SELF'].'" METHOD=POST>';
			$html .= '<TABLE width="90%" cellpadding=5 cellspacing=0 border=0>';
			$html .= '<TR class="subtitle">';
			$html .= '<TD class="FTLRB">Forum!</TD><TD class="FTRB">Posts</TD><TD class="FTRB">Threads</TD><TD class="FTRB">Last action</TD>';
			$html .= '<TR>';
			if (is_array($this->forumArray)){
				foreach ($this->forumArray as $f) {
					if (intval($this->access) & $GLOBALS['constants']->USER_ADMIN)
						$extra = "<INPUT TYPE=checkbox name=delforumID[] value=".$f->forumID.">";
					else $extra="";
					if ($f->lastPost)
						$stat = $f->lastPost.', in <a class=biggerblack href="'.$_SERVER['PHP_SELF'].'?forumID='.$f->forumID.'&threadID='.$f->threadID.'">'.$f->lastThread . '</a>';
					else $stat = "&nbsp;";
					//if ($f->lastPostUserID)
					//	$stat .= " started by <b>" . $f->lastPostUserNick . "</b>";
					$html .= '<TR bgcolor=#DAD3B7 style=\"color:#000000\">';
					$html .= '<TD class="FBLR">'.$extra.'<a href="'.$_SERVER['PHP_SELF'] .'?forumID='.$f->forumID.$GLOBALS['forum_fcid'].'" class="biggerblack">'.$f->forumName.'</A><br>'.$f->forumDescription.'</TD>';
					$html .= '<TD class="FBR">'.$f->numPosts.'</TD><TD class="FBR">'.$f->numThreads.'</TD><TD class="FBR">'.$stat.'</TD>';
					$html .= '</TR>';
				}
			}
			if (intval($this->userObj->access) & $GLOBALS['constants']->USER_ADMIN) {
				$html .= '<INPUT TYPE=HIDDEN name=step value="delForum">';
				$html .= '<TR bgcolor=#DAD3B7 style=\"color:#000000\"><TD colspan=1>';
				$html .= "<INPUT TYPE=SUBMIT name=delForum value='Delete selected forum'></TD>
						<TD colspan=1><INPUT TYPE=SUBMIT name=forceShowAll value='Show all Forums (no statisics)'></TD><td colspan=2>&nbsp</td></TR>";
				$html .= '</FORM>';
			}
			$html .= "</TABLE>";
			if (intval($this->userObj->access) & $GLOBALS['constants']->USER_ADMIN) {
				$html .= '<FORM ACTION="'.$_SERVER['PHP_SELF'].'" METHOD=POST>';
				$html .= '&nbsp;<br>&nbsp;<br><TABLE width="100%" cellpadding=5 cellspacing=0 border=0>';
				$html .= '<TR class="subtitle">';
				$html .= '<TD class="FTLRB" colspan=2>Administrasjon</TD>';
				$html .= '<TR>';
				$html .= '<TR bgcolor=#DAD3B7 style=\"color:#000000\">';
				$html .= '	<TD class="FTL">Forum Navn:	</TD>
							<TD class="FTR"><INPUT TYPE=text length=30 maxlength=255 name=forumName>';
				$html .= '</TR>';
				$html .= '<TR bgcolor=#DAD3B7 style="color:#000000">';
				$html .= '	<TD class="FL">Forum Beskrivelse:</TD>
							<TD class="FR"><INPUT TYPE=text length=30 maxlength=255 name=forumDescription>';
				$html .= '<TR bgcolor=#DAD3B7 style="color:#000000">';
				$html .= '	<TD class="FL">Max Threads:</TD>
							<TD class="FR"><INPUT TYPE=text length=30 maxlength=255 name=forumMaxThreads value=50>';
				$html .= '</TR>';
				$html .= '<TR bgcolor=#DAD3B7 style="color:#000000">';
				$html .= '	<TD class="FL">Gi tilgang til:</TD>
							<TD class="FR">Gjester<INPUT TYPE=checkbox name=forumGjest>&nbsp;Brukere<INPUT TYPE=checkbox name=forumBruker>Moderators<INPUT TYPE=checkbox name=forumModerator>&nbsp;Admin<INPUT TYPE=checkbox name=forumAdmin>';
				$html .= '</TR>';
				$html .= '<TR bgcolor=#DAD3B7 style="color:#000000">';
				$html .= '	<TD class="FBL">&nbsp</TD>
							<TD class="FBR"><INPUT TYPE=submit name=newForum value="Opprett">';
				$html .= '</TR>';
				$html .= "</TABLE>";
				$html .= "<INPUT TYPE=HIDDEN NAME=step value=newForum>";
				$html .= "</FORM>";
				
			
			}
			
		}
		
		if (intval($this->userObj->access) == $GLOBALS['constants']->USER_NORMAL) {  // logged in as guest, show login box.
			$html .= '<FORM action="'.$_SERVER['PHP_SELF'].'" METHOD=POST>';
			$html .= '<TABLE width="200" cellpadding=0 cellspacing=0 border=0>';
			$html .= '<TR class="subtitle">';
			$html .= '<TD class="FTLRB" colspan=2>Log in to Forum</td>';
			$html .= '</TR>';
			$html .= '<TR bgcolor=#DAD3B7 style="color:#000000">';
			$html .= '	<TD class="FBL">Username:</TD>
						<TD class="FBR"><INPUT TYPE=text name=username>';
			$html .= '</TR>';
			$html .= '<TR bgcolor=#DAD3B7 style="color:#000000">';
			$html .= '	<TD class="FBL">Password:</TD>
						<TD class="FBR"><INPUT TYPE=password name=password>';
			$html .= '</TR>';
			$html .= '<TR bgcolor=#DAD3B7 style="color:#000000">';
			$html .= '	<TD colspan=2><input type=submit value="Log in"></TD>';
			$html .= '</TR>';
			$html .= '<input type=hidden name=login value=true>';
			$html .= '</FORM>';
		}
		
		
		return $this->html . $html;
	}
	function handleInput ()
	{
		if(!isset($_REQUEST['step']))
			return;
		switch ($_REQUEST['step']) {
			case 'newForum':
				$forum = new Forum($this->database,$this,$this->userObj);
				if (!$forum->create()) $this->html .= "ERROR!  Could not create the forum!";
				break;
			case 'delForum':
				if (isset($_POST['delforumID']) && is_array($_POST['delforumID'])) {
					foreach ($_POST['delforumID'] as $f) {
						$arr['ForumID'] = $f;
						$forum = new Forum($this->database,$this,$this->userObj,$arr);
						$forum->delete();
					}
				} else $this->html .= "Error! You need to select the forum you want to delete!";
				break;
			case 'editPost':
				if (isset($_REQUEST['postID'])) {
					$post = new Post($this->database,$this->userObj,false);
					$post->loadData($_REQUEST['postID']);					
					$post->edit();
					$this->html .= 'Post has been edited.';
				} else $this->html .= 'Error! No post selected.';
			
				break;
			case 'delPost':
				if (is_array($_POST['delPostID'])) {
					foreach ( $_POST['delPostID'] as $p) {
						$arr['PostID'] = $p;
						$post = new Post($this->database,$this->userObj,$arr);
						$post->delete();
					}
				} else $this->html .= "ERROR!  You need to select the posts you want to delete!";
				break;
			case 'threadAction':
				if (is_array($_POST['threadActionID'])) {
					foreach ($_POST['threadActionID'] as $t) {
						$arr['ThreadID'] = $t;
						$arr['ThreadForumID'] = $_REQUEST['forumID'];
						$thread = new Thread($this->database,$this->userObj,$arr);
						if (isset($_POST['delThread']))
							$thread->delete();
						if (isset($_POST['topThread']))
							$thread->top();
						if (isset($_POST['closeThread']))
							$thread->close();
						if (isset($_POST['openThread']))
							$thread->open();
						if (isset($_POST['downThread']))
							$thread->down();
						if (isset($_POST['moveThread']))
							$thread->move();
							
					}
				} else $this->html .= "Error: Please select at least 1 thread!";
				
				break;
			case 'newThread':
				$arr['ThreadForumID'] = $_POST['forumID'];
				$thread = new Thread($this->database,$this->userObj,$arr);
				$thread->create();
				break;
			case 'postReply':
				$arr2['ThreadID'] = $_POST['threadID'];
				$thread = new Thread($this->database,$this->userObj,$arr2);
				$thread->getThreadData();
//		$this->forumID = $dataArray['PostForumID'];
				$arr['PostThreadID'] = $thread->threadID;
				$arr['PostForumID'] = $thread->forumID;
				$post = new Post($this->database,$this->userObj,$arr);
				$post->create();				
				break;
		}
	}
}
}

if ( !class_exists("KingdomForum") ) {
//error_reporting(E_ALL);
require_once("../scripts/Province.class.inc.php");
class KingdomForum extends MainForum {
	function KingdomForum ($database,$userObj, $province)
	{
		$this->database = $database;
		$this->userObj = $userObj;
		$this->provinceObj = $province;
		if ($this->provinceObj->isKing())
			$this->userObj->access |= $GLOBALS['constants']->USER_MODERATOR;
		if (!$this->userObj) $this->access = $GLOBALS['constants']->USER_NORMAL;
		else $this->access = $this->userObj->access;
		$this->handleInput();
		$this->getActiveForum();		
	}
	
	function IsIngameForum() { return true; }
	function getActiveForum ()
	{
		if ($this->provinceObj->kiId < 0) return;
		
		$this->database->query("SELECT ForumID FROM ForumMain WHERE kiID='".$this->provinceObj->kiId."'");
		if ($this->database->numRows()) 
		{
			$arr = $this->database->fetchArray();
			$_REQUEST['forumID'] = $arr['ForumID'];
			if ($this->checkOK())
			{
				$this->activeForum = new Forum ($this->database,$this,$this->userObj,$arr);
				$this->activeForum->userObj = $this->userObj;
			}
			else
			{
				die("This hacking attempt haas been logged. (you have been banned from this game)");
			}
		}
		else	// create the kingdom forum
		{
			$arr['forumID'] = $this->createKingdomForum();
			$this->activeForum = new Forum ($this->database,$this,$this->userObj,$arr);
			$this->activeForum->userObj = $this->userObj;
		
		}
	}
	function checkOK()
	{
		if (isset($_REQUEST['threadID']))
		{
			$this->database->query("SELECT ThreadForumID FROM ForumThread WHERE ThreadID='".$_REQUEST['threadID']."'");
			if ($this->database->numRows())
			{
				$check = $this->database->fetchArray();
				if ($check['ThreadForumID'] != $_REQUEST['forumID']) return false;
			}
			else return false;
		}
		return true;
	}
	function createKingdomForum ()
	{
		$this->database->query("INSERT INTO ForumMain (ForumName, Access, ForumDescription, canPost, kiID) VALUES
								('Kingdom Forum for: ".$this->provinceObj->kiId."',
								 '3','Private kingdom forum! Only your kingdom can see this forum.','3',
								 '".$this->provinceObj->kiId."')");
		return $this->database->lastInsertId();
	}
	function showPath ()
	{
		$html = '<table cellpadding=5 cellspacing=0><tr class="subtitle" ><td class="TLRB">';
		if ($this->activeForum)
			$html .= $this->activeForum->showPath();
		$html .= '</td></tr></table>';
		return $html;
	}
	
	function showLogOff()
	{
		if ($this->userObj->access == $GLOBALS['constants']->USER_NORMAL)
			$nick = "Guest";
		else $nick = $this->userObj->username;
		//$html = '<table cellpadding=5 cellspacing=0><tr class="subtitle" ><td class="TLRB"><a class= biggerblack href="'."http://www.tkoc.net/logoff.php".'">Log out user: '.$nick.'</a>';
		return $html;
	}
}

} /* class exist */


if ( !class_exists("Forum") ) {

class Forum {
	
	var $Parent = NULL;
	var $forumName = "Error: Creating kingdom forum.. Please reclick the forum button.";
	var $forumDescription;
	var $forumID;
	var $numThreads = 0;
	var $numPosts = 0;
	var $lastPost = false;
	var $lastThread = false;
	var $lastPostUserNick = "unknown";
	var $lastPostUserID = false;
	
	var $database;
	var $maxThreads;
	var $access;
	var $moderatorArray = array();
	var $error;
	var $activeThread = false;
	var $threadArray;
	var $userObj = false;
	var $threadID;
	var $canPost = 1;
	var $html = "";

	function showPath ()
	{
		$html = '<a class= biggerblack href="'.$_SERVER['PHP_SELF'].'?forumID='.$this->forumID.'">'.$this->forumName.'</a>';
		if ($this->activeThread)
			return $html ." &gt " . $this->activeThread->showPath();
		else return $html;
	}

	function Forum ($database,$in_parent,$userObj=false, $dataArray=false)
	{
		$this->database = $database;
		$this->userObj = $userObj;
		$this->Parent = $in_parent;
		if ($dataArray)	 {
			$this->setData ($dataArray);
			$this->loadData();
		}
		if ($this->activeThread==false)
			$this->loadThreads();
	}
	function loadData ()
	{
		if (isset($_REQUEST['threadID'])) {
			$arr['ThreadID'] = $_REQUEST['threadID'];
			$arr['ThreadForumID'] = $this->forumID;
			// make sure its "safe" to load this rhread
/*			$this->database->query("SELECT ForumMain.Access as Access FROM ForumThread
									LEFT JOIN ForumMain on ForumThread.ThreadForumID=ForumMain.ForumID
										WHERE ThreadID=".$arr['ThreadID']."
										");*/
			$this->activeThread = new Thread($this->database,$this->userObj,$arr);
//			$this->activeThread->getThreadData();
			$this->activeThread->loadPosts();
			$this->getForumData();
		} else {
			$this->database->query("SELECT * From ForumMain where ForumID=".$this->forumID."");
			if ($this->database->numRows())
				$this->setData($this->database->fetchArray());
		}
	}
	
	function getForumData()
	{
		$this->database->query("SELECT * from ForumMain where forumID=" . $this->forumID);
		if ($this->database->numRows())
			$this->setData($this->database->fetchArray());
	}
	
	function setData ($dataArray)
	{
		if (isset($dataArray['ForumName']))
			$this->forumName = stripslashes ($dataArray['ForumName'] );
		if (isset( $dataArray['ForumID']))
			$this->forumID   = $dataArray['ForumID'];
		if (isset($dataArray['MaxThreads']))
			$this->maxThreads = $dataArray['MaxThreads'];
		if (isset($dataArray['Access']))
			$this->access = $dataArray['Access'];
		if (isset($dataArray['ForumDescription']))	
			$this->forumDescription = stripslashes ( $dataArray['ForumDescription'] );
		if (isset($dataArray['lastPost']))
			$this->lastPost = $dataArray['lastPost'];
		if (isset($dataArray['ThreadName']))
			$this->lastThread = stripslashes ($dataArray['ThreadName']);
		if (isset($dataArray['ThreadID']))	
			$this->threadID = $dataArray['ThreadID'];
		if (isset($dataArray['canPost']))
			$this->canPost = $dataArray['canPost'];
	
	}
	
	function loadThreads ()
	{
		if (isset($_REQUEST['startthread']))
			$start = $_REQUEST['startthread'];
		else $start = 0;
		if ($GLOBALS['constants']->THREADS_PR_PAGE > 0)
			$sqlLimit = ' LIMIT '.$start.',' . ($GLOBALS['constants']->THREADS_PR_PAGE +1);
		else $sqlLimit = '';
//		echo $sqlLimit;
		if ($this->database->query("SELECT ThreadID,ThreadName,ThreadTime,ThreadTop,ThreadClosed,ThreadForumID,ThreadNick,Views,nick,count(*) as numPosts,MAX(PostTime) as lastAction from ForumThread LEFT JOIN ForumPost on ForumPost.PostThreadID=ThreadID LEFT JOIN User on ThreadUserID=UserID where ThreadForumID='".$this->forumID."' GROUP BY ThreadID ORDER BY ThreadTop,lastAction DESC" . $sqlLimit) && $this->database->numRows()) {
			if ($this->database->numRows()>$GLOBALS['constants']->THREADS_PR_PAGE) {
			
			}
			while ($t = $this->database->fetchArray()) {
				$this->threadArray[] = new Thread($this->database,$this->userObj,$t);
			}
		
		}
	}
	function getActiveThread ()
	{
	
	}
	function showForum ()
	{
		$html = "";
		// TODO HER må det legges til om brukeren ikke har rettigheter til å komme inn på forumet.
//		if (!$this->userObj) $this->access = $GLOBALS['constants']->USER_NORMAL;
//		else $this->access = $this->userObj->access;
		$threadCounter = 0;
		if ($this->activeThread) {
			$this->activeThread->updateView();
			return $this->activeThread->showThread();

		} else {		// show all forums....
			if (!(intval($this->access) & intval($this->userObj->access))) {
				return 'Feil: Ingen tilgang.';
			}		
			if ($this->userObj->access & $GLOBALS['constants']->USER_MODERATOR)
				$html .= '<FORM ACTION="'.$_SERVER['PHP_SELF'].'" METHOD=POST>';
			$html .= '<TABLE width="100%" cellpadding=5 cellspacing=0 border=0>';
			$html .= '<TR class="subtitle">';
			$html .= '<TD class="FTLRB">Topic...</TD><TD class="FTRB">By</TD><TD class="FTRB">Posts</TD><TD class="FTRB">Views</TD><TD class="FTRB">Last post</TD>';
			$html .= '</TR>';
			if (is_array($this->threadArray)){
				reset($this->threadArray);
				foreach ($this->threadArray as $f) {
					$threadCounter++;
					if ($threadCounter<$GLOBALS['constants']->THREADS_PR_PAGE) {
					
						if (intval($this->userObj->access) & $GLOBALS['constants']->USER_MODERATOR)
							$extra = "<INPUT TYPE=checkbox name=threadActionID[] value=".$f->threadID.">";
						else $extra="";
						if (strlen($f->threadNick)<1)
							$f->threadNick = "Deleted player";
						$extra2 = "";
						if ($f->threadTop == true)
							$extra2 .= "(Top)";
						if ($f->threadClosed)
							$extra2 .= "(Closed)";
						$html .= '<TR bgcolor=#DAD3B7 style=\"color:#000000\">';
						$html .= '<TD class="FBLR">'.$extra.'<a href="'.$_SERVER['PHP_SELF'] .'?forumID='.$f->forumID.'&threadID='.$f->threadID.'" class="biggerblack">'.$f->threadName.'</A>'.$extra2.'<br>'.$f->threadDescription.'</TD>';
						$html .= '<TD class="FBR">'.$f->threadNick./*'('.$f->threadNickThisAge.*/'</TD><TD class="FBR">'.$f->numPosts.'</TD><TD class="FBR">'.$f->numViews.'</TD><TD class="FBR">'.$f->lastPost.'</TD>';
						$html .= '</TR>';
					}
				}
			}
			if ($threadCounter>$GLOBALS['constants']->THREADS_PR_PAGE) {
				if (!isset($_REQUEST['startthread']))
					$_REQUEST['startthread'] = 0;
				$html .= '<TR class="subtitle">';
				$html .= '<TD class="FTLRB" colspan=5><a href="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&startthread='.($_REQUEST['startthread'] + $GLOBALS['constants']->THREADS_PR_PAGE).'" class=biggerblack>Show next '.$GLOBALS['constants']->THREADS_PR_PAGE.' threads</a></TD>';
				$html .= '</TR>';
			
			}
			$AdminChoice = "";
			if (intval($this->userObj->access) & $GLOBALS['constants']->USER_ADMIN) 
			{
				$AdminChoice = " <INPUT TYPE=SUBMIT name=delThread value='Delete threads'> &nbsp;";
			}



			if (intval($this->userObj->access) & $GLOBALS['constants']->USER_MODERATOR) {
//				$Parent->LoadAllForums();
				if ($this->Parent->IsIngameForum() == true)
				{
					$AdminChoice = " <INPUT TYPE=SUBMIT name=delThread value='Delete threads'> &nbsp;";
				}
				
				$html .= '<INPUT TYPE=HIDDEN name=step value="threadAction">';
				$html .= '<TR bgcolor=#DAD3B7 style=\"color:#000000\"><TD colspan=5>';
				$forumDropDown = $this->Parent->GetForumDropDown();
//				print_r($this->Parent);
				$html .= "	$AdminChoice 
							<INPUT TYPE=SUBMIT name=topThread value='Top threads'> &nbsp;
							<INPUT TYPE=SUBMIT name=downThread value='Down threads'> &nbsp;
							<INPUT TYPE=SUBMIT name=closeThread value='Close threads'> &nbsp;
							<INPUT TYPE=SUBMIT name=openThread value='Open threads'> &nbsp;";

				if ($this->Parent->IsIngameForum() == false || (intval($this->userObj->access) & $GLOBALS['constants']->USER_ADMIN))
				{
				$html .= "
							<INPUT TYPE=SUBMIT name=moveThread value='Move threads'> &nbsp; $forumDropDown";
				}
				$html .= "
							</TD></TR>";
				$html .= '<INPUT TYPE=HIDDEN NAME=forumID VALUE='.$this->forumID.'>';
				$html .= '</FORM>';
			}
			$html .= "</TABLE>";
			
			if ($this->Parent->IsIngameForum()) {
				if ((intval($this->userObj->access) & $GLOBALS['constants']->USER_VOICE) && (intval($this->userObj->access) & intval($this->canPost))) {
					$html .= '<FORM ACTION="'.$_SERVER['PHP_SELF'].'" METHOD=POST>';
					$html .= '&nbsp;<br>&nbsp;<br><TABLE width="100%" cellpadding=5 cellspacing=0 border=0>';
					$html .= '<TR class="subtitle">';
					$html .= '<TD class="FTLRB" colspan=2>New Thread</TD>';
					$html .= '</TR>';
					$html .= '<TR bgcolor=#DAD3B7 style=\"color:#000000\">';
					$html .= '	<TD class="FTL">Name:	</TD>
								<TD class="FTR"><INPUT TYPE=text length=30 maxlength=255 name=threadName>';
					$html .= '</TR>';
					$html .= '<TR bgcolor=#DAD3B7 style=\"color:#000000\">';
					$html .= '	<TD class="FL">Content:</TD>
								<TD class="FR"><TEXTAREA cols=100 rows=30 name=postInnhold></TEXTAREA></TD>
							  </TR>';
	/*				$html .= '<TR bgcolor=#DAD3B7 style=\"color:#000000\">';
					$html .= '	<TD class="L">Max Threads:</TD>
								<TD class="R"><INPUT TYPE=text length=30 maxlength=255 name=forumMaxThreads value=50>';
					$html .= '</TR>';*/
	/*				$html .= '<TR bgcolor=#DAD3B7 style=\"color:#000000\">';
					$html .= '	<TD class="L">Gi tilgang til:</TD>
								<TD class="R">Gjester<INPUT TYPE=checkbox name=forumGjest>&nbsp;Brukere<INPUT TYPE=checkbox name=forumBruker>OPer<INPUT TYPE=checkbox name=forumOp>&nbsp;Master OPer<INPUT TYPE=checkbox name=forumMaster>Admin<INPUT TYPE=checkbox name=forumAdmin>';
					$html .= '</TR>';*/
					$html .= '<TR bgcolor=#DAD3B7 style="color:#000000">';
					$html .= '	<TD class="FBL">&nbsp</TD>
								<TD class="FBR"><INPUT TYPE=submit name=newThread value="Create"></TD>';
					$html .= '</TR>';
					$html .= "</TABLE>";
					$html .= "<INPUT TYPE=HIDDEN NAME=step value=newThread>";
					$html .= "<INPUT TYPE=HIDDEN NAME=forumID value=".$this->forumID.">";
					$html .= "</FORM>";
				}
			
			}
			
		}
		return $this->html . $html;
	}
	
	function loadStatistics ()
	{
		$this->database->query("SELECT count(*) as threadAntall from ForumThread where ThreadForumID=".$this->forumID."");
		$tmp = $this->database->fetchArray();
		$this->numThreads = $tmp['threadAntall'];
		$this->database->query("SELECT count(*) as postAntall from ForumPost where PostForumID=".$this->forumID."");
		$tmp = $this->database->fetchArray();
		$this->numPosts = $tmp['postAntall'];
		$this->database->query("SELECT MAX(PostTime) as lastPost,PostForumID, PostThreadID,ThreadName, PostUserID, PostNick ,User.nick,User.username from ForumPost LEFT JOIN ForumThread on ForumThread.ThreadID=PostThreadID LEFT JOIN User on PostUserID=User.userID  where PostForumID=".$this->forumID." group by PostForumID, PostThreadID ORDER BY lastPost desc limit 1");
		$tmp = $this->database->fetchArray();
		$this->threadID = $tmp['PostThreadID'];
		$this->lastThread = $tmp['ThreadName'];
		$this->lastPostUserNick = $tmp['PostNick'];
		$this->lastPostUserID = $tmp['PostUserID'];
	}
	
	function create()
	{
		if (strlen($_POST['forumName'])<5) return false;
		if (strlen($_POST['forumDescription'])<5) return false;
		if ($_POST['forumMaxThreads']<1) return false;
		$access = 0;
		if (isset($_POST['forumGjest'])) $access += $GLOBALS['constants']->USER_NORMAL;
		if (isset($_POST['forumBruker'])) $access += $GLOBALS['constants']->USER_VOICE;
		if (isset($_POST['forumOp'])) $access += $GLOBALS['constants']->USER_OP;
		if (isset($_POST['forumMaster'])) $access += $GLOBALS['constants']->USER_CHANMASTER;
		if (isset($_POST['forumAdmin'])) $access += $GLOBALS['constants']->USER_ADMIN;
		if (isset($_POST['forumModerator'])) $access += $GLOBALS['constants']->USER_MODERATOR;

		$this->database->query("INSERT INTO ForumMain (ForumName, ForumDescription, MaxThreads, Access, canPost) values ('".$_POST['forumName']."','".$_POST['forumDescription']."','".$_POST['forumMaxThreads']."','".$access."','".$access."')");
		return true;
	}
	function delete ()
	{
//		echo "del forum with: " . $this->forumID;	
		if (intval($this->userObj->access) & $GLOBALS['constants']->USER_ADMIN) {
			$this->database->query("DELETE FROM ForumMain where ForumID=".$this->forumID."");
			$this->database->query("DELETE FROM ForumThread where ThreadForumID=".$this->forumID."");
			$this->database->query("DELETE FROM ForumPost where PostForumID=".$this->forumID."");
		}
		// TODO!! NB SLETTE POST; THREAD; ADMIN!
	}
}
}


if ( !class_exists("Thread") ) {
class Thread {
	var $database;
	var $forumID;
	var $userObj;
	var $threadName = "";
	var $threadTime;
	var $threadNick = "";
	var $threadDescription = "";
	var $threadNickThisAge;
	var $threadID;
	var $postArray;
	var $numPosts=0;
	var $numViews=0;
	var $lastPost = "&nbsp;";
	var $threadTop = false;
	var $threadClosed = false;
	var $bbcode;
	var $threadReadAccess = 0;
	var $postBack = "#DAD3B7";
	var $postHeader = "#93886C";
		
	function Thread ($database,$userObj=false, $arr=false) 
	{
		$this->database = $database;
		$this->userObj = $userObj;
		$this->setData($arr);
		$this->bbcode = new bbcode();
		// </td></tr></table><BR>

		$this->bbcode->add_tag(array('Name'=>'b','HtmlBegin'=>'<span style="font-weight: bold;">','HtmlEnd'=>'</span>'));
		$this->bbcode->add_tag(array('Name'=>'i','HtmlBegin'=>'<span style="font-style: italic;">','HtmlEnd'=>'</span>'));
		$this->bbcode->add_tag(array('Name'=>'u','HtmlBegin'=>'<span style="text-decoration: underline;">','HtmlEnd'=>'</span>'));
		$this->bbcode->add_tag(array('Name'=>'quote','HtmlBegin'=>'Quote:<BR><table cellpadding=5; cellspacing=0; width=100% border=0 style="border-left:1px #000000 solid; border-top:1px #000000 solid;"><tr class="subtitle"><td class="column">','HtmlEnd'=>'</td></tr></table><BR>'));
		$this->bbcode->add_tag(array('Name'=>'link','HasParam'=>true,'HtmlBegin'=>'<a href="%%P%%">','HtmlEnd'=>'</a>'));
		$this->bbcode->add_tag(array('Name'=>'color','HasParam'=>true,'ParamRegex'=>'[A-Za-z0-9#]+','HtmlBegin'=>'<span style="color: %%P%%;">','HtmlEnd'=>'</span>','ParamRegexReplace'=>array('/^[A-Fa-f0-9]{6}$/'=>'#$0')));
		$this->bbcode->add_tag(array('Name'=>'email','HasParam'=>true,'HtmlBegin'=>'<a href="mailto:%%P%%">','HtmlEnd'=>'</a>'));
		$this->bbcode->add_tag(array('Name'=>'size','HasParam'=>true,'HtmlBegin'=>'<span style="font-size: %%P%%pt;">','HtmlEnd'=>'</span>','ParamRegex'=>'[0-9]+'));
		$this->bbcode->add_tag(array('Name'=>'bg','HasParam'=>true,'HtmlBegin'=>'<span style="background: %%P%%;">','HtmlEnd'=>'</span>','ParamRegex'=>'[A-Za-z0-9#]+'));
		$this->bbcode->add_tag(array('Name'=>'s','HtmlBegin'=>'<span style="text-decoration: line-through;">','HtmlEnd'=>'</span>'));
		$this->bbcode->add_tag(array('Name'=>'align','HtmlBegin'=>'<div style="text-align: %%P%%">','HtmlEnd'=>'</div>','HasParam'=>true,'ParamRegex'=>'(center|right|left)'));
		$this->bbcode->add_tag(array('Name'=>'code','HtmlBegin'=>'Code:<BR><table cellpadding=5; cellspacing=0; width=100% border=0 style="border-left:1px #000000 solid; border-top:1px #000000 solid;"><tr class="subtitle"><td class="column" style="font-family: monospace; font-size: 12px;"><pre>','HtmlEnd'=>'</pre></td></tr></table><BR>'));
		$this->bbcode->add_alias('url','link');

	}

	function showPath ()
	{
		$html = '<a class= biggerblack href="'.$_SERVER['PHP_SELF'].'?forumID='.$this->forumID.'&threadID='.$this->threadID.'">'.$this->threadName.'</a>';
		return $html;
	}

	function getActiveThread ()
	{
		if (isset($_REQUEST['threadID'])) {
			echo "there is an active thread. loading.";
			$this->threadID = $_REQUEST['threadID'];
			
			// get all the posts.
			
		} else {	// do nothing...?
		
		}
	}
	
	function getThreadData ()
	{
		$this->database->query("SELECT ThreadID,ThreadReadAccess,ThreadName,ThreadTime,ThreadTop,ThreadClosed,ThreadForumID,ThreadNick,Views,nick from ForumThread LEFT JOIN User on ThreadUserID=UserID where ThreadID='".$this->threadID."'");
		$this->setData($this->database->fetchArray());
	}
	
	function setData ($arr)
	{
		if (isset($arr['ThreadForumID']))
			$this->forumID = $arr['ThreadForumID'];
		if (isset($arr['ThreadID']))
			$this->threadID = $arr['ThreadID'];
		if (isset($arr['ThreadName']))
			$this->threadName = stripslashes($arr['ThreadName']);
		if (isset($arr['ThreadTime']))
			$this->threadTime = $arr['ThreadTime'];
		if (isset($arr['ThreadNick']))
			$this->threadNick = stripslashes($arr['ThreadNick']);
		if (isset($arr['nick']))
			$this->threadNickThisAge = stripslashes($arr['nick']);
		if (isset($arr['Views']))
			$this->numViews = $arr['Views'];
		if (isset($arr['ThreadTop']))
			$this->threadTop = ($arr['ThreadTop']=='true') ? true:false;
		if (isset($arr['ThreadClosed']))
			$this->threadClosed = ($arr['ThreadClosed']=='true') ? true:false;
		if (isset($arr['numPosts']))
			$this->numPosts = $arr['numPosts'];
		if (isset($arr['lastAction']))
			$this->lastPost = $arr['lastAction'];
		if (isset($arr['ThreadReadAccess']))
			$this->threadReadAccess = $arr['ThreadReadAccess'];
	}
	function create()
	{
		if (strlen($_POST['threadName'])<2) return false;
//		echo "forumID " . $this->forumID ."newThread";
//		echo "Userobj" . $this->userObj->userID;
		$this->database->query("INSERT INTO ForumThread (ThreadName,ThreadTime,ThreadUserID,ThreadForumID, ThreadNick) VALUES ('".$_POST['threadName']."',NOW(),'".$this->userObj->userID."','".$this->forumID."','".$this->userObj->nick."')");
		$arr['PostThreadID'] = $this->database->lastInsertId();
		$arr['PostForumID']  = $this->forumID;
//		echo "TEST - POSTING.";
		$post = new Post ($this->database,$this->userObj,$arr);
		$post->create();
	}
	
	function loadPosts()
	{
		$this->getThreadData();
		// NB!! JOIN TO GET USER!
		if ($this->database->query("SELECT PostID,text,PostForceShow,PostEdit,PostTime,PostEdit,PostEditTime,PostNick,User.nick,User.username,User.access,User.signature,User.image,PostUserID
									FROM ForumPost 
									LEFT JOIN User on PostUserID=UserID 
									LEFT JOIN ForumMain on PostForumID=ForumID
										WHERE PostThreadID=".$this->threadID."
										AND (ForumMain.Access & ".$this->userObj->access." )
										ORDER BY PostTime ASC") && $this->database->numRows()) {
			$this->numPosts = $this->database->numRows();
			while ($p = $this->database->fetchArray()) $this->postArray[] = new Post($this->database,$this->userObj,$p);
		} else {
			$this->numPosts = 0;
		}
	}
	
	function showThread ()
	{
		$posts = 0;
		$html = '<TABLE width="100%" cellpadding=0 cellspacing=0 border=0>';
		$html .= '<TR class="subtitle">';
		$html .= '<TD class="FTLRB"><table width="100%" cellpadding=5><tr class="subtitle"><td>'.$this->threadName.'('.$this->numPosts.' posts)</TD></TR></table></td>';
		$html .= '</TR>';
		if (intval($this->userObj->access) & $GLOBALS['constants']->USER_MODERATOR) {
			$html .= '<FORM ACTION="'.$_SERVER['PHP_SELF'].'" METHOD=POST>';
		}
		if ($this->threadReadAccess != 0)
		{
			if (intval($this->userObj->access) & $this->threadReadAccess)
			{
//				$post->text .= "<br>&nbsp;[b]Access limited thread!  access level = $this->threadReadAccess[/b]";
				$text = "[b]This thread contains read-limited content.  Access granted.[/b]";
			}
			else
			{
				$text = "[b]This thread contains read-limited content.  You are not allowed to read.[/b]";
			}

			$text .= "<br>Some special posts might still be shown.";
//				$post->text = "[b]This thread contains read-limited content.  You are not allowed to read.[/b]";
//				$this->
				$html .= '<TR>
							<TD>';
					$html .= '<table width="100%" border="0" cellspacing="0" cellpadding="4" bgcolor=#000000>
								<TR height=25px>
									<TD class="FBL" bgcolor='.$this->postBack.' valign=middle style="font-size:10pt">
										<a name="post2"></a>
										<A HREF="#" class=biggerblack>'.'Access'.'</A>
								</TD>';
					$html .= '		<TD class="FBLR" bgcolor='.$this->postBack.' valign=middle>
										<table cellpadding=0; cellspacing=0; border=0; style="width:100%">
											<TR>
												<TD>&nbsp;Thread notification!</td>
											</TR>
										</table>
									</TD>';
					$html .= '	</TR>';
					$html .= '	<TR>
									<TD class="FBL" bgcolor='.$this->postBack.' valign=top style="font-size:7.5pt">';
					// TODO! LEGG TIL BRUKERSTATUS
					$html .= '<BR><img src="images/1p.gif" width=160 height=1><BR><BR>
									</TD>
									<TD class="FBLR" width=100% bgcolor='.$this->postBack.' valign=top style="font-size:9pt">
										';
					$html .= $this->bbcode->parse_bbcode ($text) .'';
										$html .='<br><br>
									</TD>
								</TR>
								<tr>
								<td class="FBL" bgcolor='.$this->postBack.' align="left">&nbsp;';
					$html .='
								</td>
								<td class="FBLR" bgcolor='.$this->postBack.' nowrap align="left">
					    			<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<TR>
											<td valign="middle" align="left">
											<b>'."Access message".'</b>
											</td>
											<td valign="middle" align="right" width="2%"><a class="black" href="#top"><IMG border=0 alt="to the top" SRC="images/top.gif"></a>
											</td>
										</TR>
									</table>
								</TD>
							</TR>
							<tr> 
					          <td class="FBLR" bgcolor=#DDDDDD colspan=2><img src="images/1p.gif">
							  </td>
							</tr>
						</table>';
					$html .= '</TD>
							</TR>';

		}
		if (is_array($this->postArray)) {
			foreach ($this->postArray as $post) {
				$posts++;
				if ($this->threadReadAccess != 0)
				{
					if ((intval($this->userObj->access) & $this->threadReadAccess) == false)
					{
						if ($post->PostForceShow == false)
							continue;
					}

/*					if ($post->PostForceShow == true)
					{
						$post->text .= "<br>[b]Allowing showing of post in restricted thread![/b]";
					}*/
				}
				// if show top post is true, then show first post.
				// TODO!
				// only show latest XX posts.
//	var $POST_PR_PAGE	= 20;
//	var $POST_ORDER		= 'ASC';  // ASC / DESC
//	var $KEEP_FIRST_POST_TOPPED = true;		// if true, keeps first post in thread at top.
				if (!isset($_REQUEST['forumstartmsg']))
					$_REQUEST['forumstartmsg'] = '';
				if ((
					( ($this->numPosts-$posts) <= $GLOBALS['constants']->POST_PR_PAGE) || 
					($_REQUEST['forumstartmsg']=='all') || 
					($posts==1 && $GLOBALS['constants']->KEEP_FIRST_POST_TOPPED)) ) {
					
					if (strlen($post->nick)<1)
						$post->nick = "Deleted player";
					$extra_title = "";
					$uAccess = "";
					if ($GLOBALS['constants']->USER_DONATED_CASH  & intval($post->postUserAccess))
						$extra_title = "Exalted ";
					if ($GLOBALS['constants']->USER_VOICE  & intval($post->postUserAccess))
						$uAccess = "Player";
					if ($GLOBALS['constants']->USER_MODERATOR  & intval($post->postUserAccess))
						$uAccess = "Moderator";

					$uAccess = $extra_title . $uAccess;


					if ($GLOBALS['constants']->USER_GAME_ADMIN  & intval($post->postUserAccess))
						$uAccess = "Game Administrator";

					if ($post)
					$html .= '<TR>
							<TD>';
					$html .= '<table width="100%" border="0" cellspacing="0" cellpadding="4" bgcolor=#000000>
								<TR height=25px>
									<TD class="FBL" bgcolor='.$this->postBack.' valign=middle style="font-size:10pt">
										<a name="post2"></a>
										<A HREF="#" class=biggerblack>'.$post->nick.'</A>
								</TD>';
					$html .= '		<TD class="FBLR" bgcolor='.$this->postBack.' valign=middle>
										<table cellpadding=0; cellspacing=0; border=0; style="width:100%">
											<TR>
												<TD><b>Posted:</b>'.$post->postTime;
					if ($post->edited) {
						$html .= "&nbsp; <b>Last edited:</b>" . $post->editTime;
					}
					$html .='</td>
											</TR>
										</table>
									</TD>
								</TR>';
					$html .= '	<TR>
									<TD class="FBL" bgcolor='.$this->postBack.' valign=top style="font-size:7.5pt">';
					$html .= 'Post:'.$posts.'/'.$this->numPosts . '<BR>';
					// Ingame avatar is removed cause of different size, doesn't fit anymore - Soptep: 08/02/2010
					/*if ($post->image) {
						$html .= '		<IMG src="'.$post->image.'" border="0" width="100" height="100">';
					}*/
					// TODO! LEGG TIL BRUKERSTATUS
					$html .= '<BR><img src="images/1p.gif" width=160 height=1><BR><BR>
									</TD>
									<TD class="FBLR" width=100% bgcolor='.$this->postBack.' valign=top style="font-size:9pt">
										';
					if ($post->postUserAccess == $GLOBALS['constants']->USER_NORMAL)
					{
						if (intval($this->userObj->access) & $GLOBALS['constants']->USER_MODERATOR) 
						{
							$post->text .= "<br>&nbsp;<br>[i]This user has lost access to post to this forum.  To normal users this message will look like this:[/i]<br>";
							$post->text .= "[b]This user has lost Forum access due to violation of the tkoc.net rules[/b]";
						}
						else
							$post->text = "[b]This user has lost Forum access due to violation of the tkoc.net rules[/b]";
					}
					$html .= $this->bbcode->parse_bbcode ($post->text) .'
										<BR><BR><BR>';
										// Get worldforum signature instead of the old ingame
										$myUser = mysql_fetch_array ($GLOBALS['forumdb']->selectField ("*", "smf_members", "member_name", $post->postUsername));
										if (strlen($myUser["signature"])>0) { 
											$html .= '----------------------------------------------------------------------------------------------------------<BR><div class="sig">'.$this->bbcode->parse_bbcode ($myUser["signature"]).'</div>';
										}
										// Old code
										/*if (strlen($post->signature)>0) {
											$html .= '--------------------------------------------------------<BR><div class="sig">'.$post->signature.'</div>';
										}*/
										$html .='
									</TD>
								</TR>
								<tr>
								<td class="FBL" bgcolor='.$this->postBack.' align="left">&nbsp;';
					if (intval($this->userObj->access) & $GLOBALS['constants']->USER_MODERATOR) {
						$html .= '<INPUT TYPE=CHECKBOX name=delPostID[] value="'.$post->postID.'">';
					}
					if (($post->postUserID == $this->userObj->userID) || (intval($this->userObj->access) & $GLOBALS['constants']->USER_MODERATOR)) {
						$html .= '<a href="'.$_SERVER['PHP_SELF'].'?forumID='.$this->forumID.'&threadID='.$this->threadID.'&postID='.$post->postID.'&step=showeditpost" style="color:black;">edit post</a>';
					}
					
					if (strcmp($post->postUsername, "unknown????") == 0) {
						$post->postUsername = "Old Player";
					}
					
					if (intval($this->userObj->access) & $GLOBALS['constants']->USER_GAME_ADMIN) {
						$html .= '<br><a href="/administration/gameUsers.php?userID='.$post->postUserID.'" style="color:black;">'.$post->postUsername.'</a>';
					}
					$html .='
								</td>
								<td class="FBLR" bgcolor='.$this->postBack.' nowrap align="left">
					    			<table width="100%" border="0" cellspacing="0" cellpadding="0">
										<TR>
											<td valign="middle" align="left">
											<b>'.$post->postUsername.' - '.$uAccess.'</b>
											</td>
											<td valign="middle" align="right" width="2%"><a class="black" href="#top"><IMG border=0 alt="to the top" SRC="images/top.gif"></a>
											</td>
										</TR>
									</table>
								</TD>
							</TR>
							<tr> 
					          <td class="FBLR" bgcolor=#DDDDDD colspan=2><img src="images/1p.gif">
							  </td>
							</tr>
						</table>';
					$html .= '</TD>
							</TR>';
				}
			}
		}

		// display page control.
		if ($this->numPosts > $GLOBALS['constants']->POST_PR_PAGE) {
			$html .= '<TR >
						<TD class="FBLR" colspan=2 bgcolor='.$this->postBack.' nowrap align="left">
						<A HREF="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&forumstartmsg=all" class=biggerblack>Show all posts</a>
						</TD>
					  </TR>';
			
		}


		if (intval($this->userObj->access) & $GLOBALS['constants']->USER_MODERATOR) {
			$html .= '<TR >
						<TD class="FBLR" colspan=2>
						<INPUT TYPE=submit value="Delete selected posts" name=delPost>
						<INPUT TYPE=HIDDEN value=delPost name=step>
						<INPUT TYPE=HIDDEN name=threadID value='.$this->threadID.'>
						<INPUT TYPE=HIDDEN name=forumID value='.$this->forumID.'>
						</FORM>
						</TD>
					  </TR>';
		}

		
		$html .= '</TABLE>';
		

//		require_once($GLOBALS['path_domain_ads'] . "ad_public_forum_bottom.php");
//		$html .= $GLOBALS['ad_public_forum_bottom'] . "<br>TTTTTTTTTTTTTEEEEEEEEEEEEEEEEEEEEEEEST";
		if ((intval($this->userObj->access) & $GLOBALS['constants']->USER_VOICE) && ($this->threadClosed==false)) {
		$html .='
			<FORM ACTION="'.$_SERVER['PHP_SELF'] .'" method=POST>
			<TABLE width="100%" cellpadding=5 cellspacing=0 border=0>
				<TR class="subtitle">
					<TD class="FTLRB" colspan=2>Post Reply:</TD>
				</TR>
				<TR bgcolor='.$this->postBack.' style=\"color:#000000\">
					<TD class="FL">Text:</TD>
					<TD class="FR"><TEXTAREA cols=100 rows=30 name=postInnhold></textarea></TD>
				</TR>
				<TR bgcolor='.$this->postBack.' style=\"color:#000000\">
					<TD class="FBL">&nbsp</TD>
					<TD class="FBR"><INPUT TYPE=submit name=newPost value="Create"></TD>
				</TR>
			</TABLE>
			<INPUT TYPE=HIDDEN name=step value=postReply>
			<INPUT TYPE=HIDDEN name=threadID value="'.$this->threadID.'">
			<INPUT TYPE=HIDDEN name=forumID value="'.$this->forumID.'">
			</FORM>';
		}
		return $html;
	}
	
	function updateView()
	{
		$this->numViews++;
		$this->database->query("UPDATE ForumThread set Views=Views+1 where ThreadID=".$this->threadID."");
	}
	
	function delete ()
	{
		if (intval($this->userObj->access) & $GLOBALS['constants']->USER_MODERATOR) {
			$this->database->query("DELETE FROM ForumThread where ThreadID=".$this->threadID."");
			$this->database->query("DELETE FROM ForumPost where PostThreadID=".$this->threadID."");
		}
	}
	
	function top ()
	{
		if (intval($this->userObj->access) & $GLOBALS['constants']->USER_MODERATOR) {
			$this->database->query("UPDATE ForumThread set ThreadTop='true' WHERE ThreadID=".$this->threadID."");
		}
	
	}
	function down ()
	{
		if (intval($this->userObj->access) & $GLOBALS['constants']->USER_MODERATOR) {
			$this->database->query("UPDATE ForumThread set ThreadTop='false' WHERE ThreadID=".$this->threadID."");
		}
	
	}

	function close ()
	{
		if (intval($this->userObj->access) & $GLOBALS['constants']->USER_MODERATOR) {
			$this->database->query("UPDATE ForumThread set ThreadClosed='true' WHERE ThreadID=".$this->threadID."");
		}
	
	}

	function open ()
	{
		if (intval($this->userObj->access) & $GLOBALS['constants']->USER_MODERATOR) {
			$this->database->query("UPDATE ForumThread set ThreadClosed='false' WHERE ThreadID=".$this->threadID."");
		}
	
	}

	function move ()
	{
		if (intval($this->userObj->access) & $GLOBALS['constants']->USER_MODERATOR) {
//			$this->database->query("UPDATE ForumThread set ThreadClosed='false' WHERE ThreadID=".$this->threadID."");
			echo "Moving from $this->forumID to $_POST[ForumDropDown]   ";
			print_r($_POST);
			// update all posts of this thread
			$this->database->query("UPDATE ForumPost set PostForumID='".$_POST['ForumDropDown']."' WHERE PostThreadID=".$this->threadID."");
			// update thread itself
			$this->database->query("UPDATE ForumThread set ThreadForumID='".$_POST['ForumDropDown']."' WHERE ThreadID=".$this->threadID."");
			
// Thread.ThreadForumID
// Post.PostForumId
// Post.

		}
	
	}

}


}

if ( !class_exists("Post") ) {
class Post {
	var $database;
	var $access;
	var $userObj;
	var $threadID;
	var $forumID;
	var $nick;
	var $postNickThisAge;
	var $postUsername = "unknown????";
	var $postUserAccess = 3;
	var $postTime;
	var $image;
	var $text;
	var $signature;
	var $postUserID;
	var $userID;
	var $postID;
	var $edited = false;
	var $editTime = "";
	var $PostForceShow = false;

	var $postBack = "#DAD3B7";
	var $postHeader = "#93886C";

	function Post ($database,$userObj=false, $dataArray=false)
	{
		$this->database = $database;
		$this->userObj = $userObj;
		if ($dataArray)
			$this->setData($dataArray);
	}
	
	function setData ($dataArray)
	{
		if (isset($dataArray['PostForceShow']))
			$this->PostForceShow = ($dataArray['PostForceShow'] == 'true');
		if (isset($dataArray['PostID']))
			$this->postID = $dataArray['PostID'];
		if (isset($dataArray['PostThreadID']))
			$this->threadID = $dataArray['PostThreadID'];
		if (isset($dataArray['PostForumID']))
			$this->forumID = $dataArray['PostForumID'];
		if (isset($dataArray['nick']))
			$this->postNickThisAge = stripslashes ($dataArray['nick']);
		if (isset($dataArray['PostNick']))
			$this->nick = stripslashes ($dataArray['PostNick']);
		if (isset($dataArray['username']))
			$this->postUsername = stripslashes ($dataArray['username']);
		if (isset($dataArray['access']))
			$this->postUserAccess = stripslashes ($dataArray['access']);
		if (isset($dataArray['PostTime']))
			$this->postTime = $dataArray['PostTime'];
		if (isset($dataArray['image']))
			$this->image = stripslashes($dataArray['image']);
		if (isset($dataArray['text']))
			$this->text = stripslashes($dataArray['text']);
		if (isset($dataArray['signature']))
			$this->signature = stripslashes($dataArray['signature']);
		if (isset($dataArray['PostUserID']))
			$this->postUserID = $dataArray['PostUserID'];
		if (isset($dataArray['PostEdit']))
			if ($dataArray['PostEdit'] == "true") {
				$this->edited=true;
				$this->editTime = $dataArray['PostEditTime'];
			}
	}
	function loadData ($postID=false)
	{
		if ($postID)
			$this->postID = $postID;
		
		$this->database->query("SELECT PostID,text,PostEdit,PostTime,PostUserID,PostThreadID,PostForumID,PostEditTime from ForumPost where PostID='".$this->postID."'");
		$this->setData($this->database->fetchArray());
	}
	function create ()
	{
		if (strlen($_POST['postInnhold'])<1) return false;
		if (!($this->userObj->userID>0)) return false;
		// TODO, legge på <br>...
		$innhold = htmlspecialchars($_POST['postInnhold']);
		$innhold = str_replace("\n","<br />",$innhold);
    	$innhold = mysql_real_escape_string($innhold);
		$this->database->query("INSERT INTO ForumPost (text,PostTime,PostUserID,PostThreadID,PostForumID,PostNick) VALUES ('".$innhold."',NOW(),'".$this->userObj->userID."','".$this->threadID."','".$this->forumID."','".$this->userObj->nick."')");
	}
	function delete()
	{
		if (intval($this->userObj->access) & $GLOBALS['constants']->USER_MODERATOR) {
			$this->database->query("DELETE FROM ForumPost where PostID=".$this->postID."");
		} else echo "ukjent feil: ikke rettigheter." . $GLOBALS['constants']->USER_MODERATOR . " vs " .$this->userObj->access;
	}
	function edit ()
	{
		if (strlen($_POST['postInnhold'])<1) return false;
		if (($this->userObj->userID != $this->postUserID) && !(intval($this->userObj->access) & $GLOBALS['constants']->USER_MODERATOR)) return false;

		$innhold = htmlspecialchars($_POST['postInnhold']);
		$innhold = str_replace("\n","<br />",$innhold);
		$innhold = mysql_real_escape_string($innhold);
//		echo "Post - debug.";
		$this->database->query("UPDATE ForumPost set text='".$innhold."',PostEditTime=NOW(), PostEdit='true' where PostID='".$this->postID."'");
	
	}	

	function showEdit ()
	{
		// Put for compatibility reasons, only for age #28, after it, it can be removed.
		$this->text = str_replace("<br>","\n",$this->text);
		
		$html ='
			<FORM ACTION="'.$_SERVER['PHP_SELF'] .'" method=POST>
			<TABLE width="100%" cellpadding=5 cellspacing=0 border=0>
				<TR class="subtitle">
					<TD class="FTLRB" colspan=2>Edit:</TD>
				</TR>
				<TR bgcolor='.$this->postBack.' style=\"color:#000000\">
					<TD class="FL">Text:</TD>
					<TD class="FR"><TEXTAREA cols=100 rows=20 name=postInnhold>'.str_replace("<br />","\n",$this->text).'</textarea></TD>
				</TR>
				<TR bgcolor='.$this->postBack.' style=\"color:#000000\">
					<TD class="FBL">&nbsp</TD>
					<TD class="FBR"><INPUT TYPE=submit name=editPost value="Edit"></TD>
				</TR>
			</TABLE>
			<INPUT TYPE=HIDDEN name=step value=editPost>
			<INPUT TYPE=HIDDEN name=postID value="'.$this->postID.'">
			<INPUT TYPE=HIDDEN name=threadID value="'.$this->threadID.'">
			<INPUT TYPE=HIDDEN name=forumID value="'.$this->forumID.'">
			</FORM>';
		return $html;	
	}

}

}

?>