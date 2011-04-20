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

/*******************************************************************
Forum Class System - Created by Soptep: 15 February 2010

The purpose of this system is to seperate the algorithms and data
by the presentation. To achieve this we first prepare all the data 
and then the template functions using the data accordingly.
Todo:
- Edit posts: own posts, king, moderators and admins edit all posts
- No create post form when on wrong topic, do not insert post
- Finish edit-save functions
- Secure data
- Add moderation functions, delete - close - top topics
*******************************************************************/

class kdForum {
	var $database;
	var $province;
	var $data = array(
					"topic" 	=> array(), // "topic" is used for the topics
					"post" 		=> array(), // "posts" is used for the posts inside a topic
					"create"	=> array(), // "create" is used for creation of a post
					"feedback" 	=> array()  // "feedback" is used for the final feedback to the user
				);
	var $html = "";
	var $numberOfFeedback = 0;
	
	function __construct ($database, $province) {
		$this->database = $database;
		$this->province = $province;
		
		$this->prepareData();
	}

	
	// START: The basic mechanism
	
	// Usually you won't need to change these functions
	// They are the main functions of the system that
	// prepare all the variables that will be
	// passed later on the template functions
	// The algorithm should remain untouched
	function prepareData () {
		$this->prepareCreatePostForm();
		$this->prepareForum();
	}
	
	
	// You should change these functions according to the different environments
	function dataTemplate () {
		//$this->showFeedback();
		$this->showForum();
		$this->showCreatePostForm();
		
		return $this->html;
	}
	// STOP: The basic mechanism
	
	
	// START: Prepare Data Functions
	function prepareCreatePostForm () {
		if (!isset($_GET['topic']) ) 
			$this->data["create"]["topic"] = 0;
		else 
      		$this->data["create"]["topic"] = (int)$_GET['topic'];
			
		if (isset($_POST["submit"])) {
			$this->data["create"]["poster"] = $this->province->rulerName." in ".$this->province->provinceName;
			if (isset ($_POST['title'])) // We have no title at inner posts
				$this->data["create"]["title"] = addslashes($_POST['title']);
			else 
				$this->data["create"]["title"] = "";
				
			$this->data["create"]["message"] = addslashes($_POST['message']);
			$currenttime = time();
			
			$i=0;
			if ($this->data["create"]["title"] == "" && $this->data["create"]["topic"] == 0) { // We don't need title at inner posts
				$this->data["feedback"][$this->numberOfFeedback] = "noTitle";
				$this->numberOfFeedback++;
				$i++;
			}
			
			if ($this->data["create"]["message"] == "") {
				$this->data["feedback"][$this->numberOfFeedback] = "noMessage";
				$this->numberOfFeedback++;
				$i++;
			}
				
			if ($i == 0) { // No errors locally
				$this->data["feedback"][$this->numberOfFeedback] = "Posted";
				$this->numberOfFeedback++;
				$this->data["create"]["message"] = str_replace ("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $this->data["create"]["message"]);
				$this->data["create"]["message"] = str_replace (" ", "&nbsp;", $this->data["create"]["message"]);
				$this->data["create"]["message"] = str_replace ("\n", "<br />", $this->data["create"]["message"]);
				$this->database->insertPost ($this->province->kiId, $this->province->pID, $this->data["create"]["poster"], $this->data["create"]["topic"], $this->data["create"]["title"], $this->data["create"]["message"], $currenttime);
				$this->data["create"]["poster"] = "";
				$this->data["create"]["title"] = "";
				$this->data["create"]["message"] = "";
			}
		}
		else {
			$this->data["create"]["poster"] = "";
			$this->data["create"]["title"] = "";
			$this->data["create"]["message"] = "";
		}
	}
	
	
	function prepareForum () {
		if (isset($_GET['topic'])) // We are inside of a topic, prepare the topic posts
			$this->prepareInnerPosts ();
		else  // We are at the main page of the forum, prepare a list of topics
			$this->prepareMainPosts ();
	}
	
	
	function prepareInnerPosts () {	
		$_GET['topic'] = (int)$_GET['topic'];
		$result = $this->database->selectField2 ("*", "Forum", "kiID",$this->province->kiId, "id", $_GET['topic']);
		if (mysql_num_rows($result)) {
			// Get the first post which is the parent
			$i=0;
			$post = mysql_fetch_array($result);
			$this->data["post"][$i]['title'] = $post['title'];
			$this->data["post"][$i]['dateSubmitted'] = $post['dateSubmitted'];
			$this->data["post"][$i]['poster'] = $post['poster'];
			$this->data["post"][$i]['message'] = $post['message'];
			$this->data["post"][$i]['id'] = $post['id'];
			$i++;
			
			
			// Then get all the posts that are childs of the first topic
			$result = $this->database->selectField3 ("*", "Forum", "kiID",$this->province->kiId, "parent", $_GET['topic'], "dateSubmitted", "ASC");
			while ($post = mysql_fetch_array($result)) {
				$this->data["post"][$i]['dateSubmitted'] = $post['dateSubmitted'];
				$this->data["post"][$i]['poster'] = $post['poster'];
				$this->data["post"][$i]['message'] = $post['message'];
				$this->data["post"][$i]['id'] = $post['id'];
				$i++;
			}
		}
		else {
			$this->data["feedback"][$this->numberOfFeedback] = "noPosts";
			$this->numberOfFeedback++;
		}
	}
	
	
	function prepareMainPosts () {
		$result = $this->database->selectField2("*", "Forum", "kiID", $this->province->kiId, "parent", 0);
		if (!mysql_num_rows($result)) { // No posts
			$this->data["feedback"][$this->numberOfFeedback] = "noTopics";
			$this->numberOfFeedback++;
		}
		else {
			//$this->numberOfTopics = mysql_num_rows($result);
			$i=0;
			while ($topic = mysql_fetch_array($result)) {
				// Get the number of replies and the last date that a post submitted
				$posts = $this->database->selectField3 ("dateSubmitted","Forum", "kiID", $this->province->kiId, "parent", $topic['id'], "dateSubmitted", "DESC");
				if ($this->data["topic"][$i]["replies"] = mysql_num_rows($posts)) {
					$temp = mysql_fetch_array ($posts);
					$this->data["topic"][$i]['dateSubmitted'] = $temp['dateSubmitted'];
				}
				else // If there are no replies, then get the parent's date submitted
					$this->data["topic"][$i]['dateSubmitted'] = $topic["dateSubmitted"];
				
				// then get the rest info
				$this->data["topic"][$i]['id'] = $topic["id"];
				$this->data["topic"][$i]['title'] = $topic['title'];
				$this->data["topic"][$i]['poster'] = $topic['poster'];
				$i++;
			}
			//$this->numberOfTopics = $i;
		}
	}
	// STOP: Prepare Data Functions
	
	
	// START: Template Functions
	function showFeedback () {
		$html = "";
		foreach ($this->data["feedback"] as $feedback) {
			switch ($feedback) {
				case "noTitle":
					$type = "error";
					$text = "You didn't provide a topic title - please try again."; 
					break;
				case "noMessage":
					$type = "error";
					$text = "You didn't provide a message - please try again."; 
					break;
				case "Posted":
					$type = "success";
					$text = "Your message has been posted - thanks!";
					break;
				case "noTopics":
					$type = "neutral";
					$text = "Your kingdom has no topics. Create one now and share information with your kingdom mates!";
					break;
				case "noPosts":
					$type = "neutral";
					$text = "Are you trying to spy other kingdom's forums? I am afraid that it is not your lucky day... Don't you think that it is much better to improve our province and destroy them?";
					break;		
				default:
					$type = "neutral";
					$text = "While there are no errors, you should not have seen this message. Post it at the WorldForum.";
			}
			$array["class"] = $type;
			$html .= $this->addHtml ($text, "div", $array);
		}
		//$this->html = $html;
		$array["class"] = "feedback";
		$this->html .= $this->addHtml ($html, "div", $array);
	}
	
	
	function showCreatePostForm () {
		$this->html .= "<form method='post' action='' class='createPostForm'>";
		$this->html .= 	"<div>";
		if ($this->data["create"]["topic"] == 0) {
			$this->html .= 	"<span>Title:</span>";
			$this->html .= 	"<input value='".$this->data["create"]["title"]."' type='text' size='65' name='title'>";
		}
		$this->html .= 		"<input class='postButton' type='submit' name='submit' value='Post Message'/>";
		$this->html .= "</div>";
		$this->html .= "<div>";
		$this->html .= 		"<span>Message:</span>";
		$this->html .= 		"<textarea name='message' wrap='physical'>".$this->data["create"]["message"]."</textarea>";
		$this->html .= "</div>";
		$this->html .= "<div>";
		
		$this->html .= "</div>";
		$this->html .= "</form>";
	}
	
	
	function showForum () {
		if (isset($_GET['topic'])) // We are inside of a topic, show the topic posts
			$this->showInnerPosts ();
		else  // We are at the main page of the forum, show a list of topics
			$this->showMainPosts();
	}
	
	
	function showBackLink () {
		$text = "Return to Main Forum";
		$array["href"] = "./kingdomForum.php";
		$text = $this->addHtml ($text, "a", $array);
		unset ($array);
		$array["class"] = "feedback";
		$this->html .= $this->addHtml ($text, "div", $array);
	}
	
	function showInnerPosts () {
		$this->showBackLink ();
		$this->showFeedback ();
		$_GET['topic'] = (int)$_GET['topic'];
		if (!empty($this->data["post"])) { // It might be empty in case that someone changed the $_GET variable
			$this->html .= "<div class='innerForumTitle'>Topic: ".$this->data["post"][0]['title']."</div>";
		
			foreach ($this->data["post"] as $post) {
				$this->html .= "<div class='post'>";
				if (true) { // allowed to edit
					if (isset($_GET['edit']) && $_GET['edit'] == $post["id"]) {
						$button = "<a href='?topic=".$_GET['topic']."&edit=".$post["id"]."'>(Save Post)</a>";
						$post['message'] = str_replace ("&nbsp;&nbsp;&nbsp;&nbsp;", "\t", $post['message']);
						$post['message'] = str_replace ("&nbsp;", " ", $post['message']);
						$post['message'] = str_replace ("<br />", "\n", $post['message']);
						$message = "<div class='message'><textarea name='message' wrap='physical'>".$post['message']."</textarea></div>";
					}
					else {
						$button = "<a href='?topic=".$_GET['topic']."&edit=".$post["id"]."'>(Edit Post)</a>";
						$message = "<div class='message'>".$post['message']."</div>";
					}
				}
				$this->html .= "<div class='poster'>".$post['poster']." ".$button."</div><div class='date'>Date Submitted: ".date("d/m/y, H:i:s", $post['dateSubmitted'])."</div>";
				$this->html .= $message;
				$this->html .= "</div>";
			}
			
			$this->showBackLink ();
		}
	}

	
	function showMainPosts () {
		$this->showFeedback ();
		// use a helping function to sort topics by their last posted post using the arsort function
		// Todo: Sort them by their last posted post or editted post
		if (!empty($this->data["topic"])) {
			/*$this->data["topic"] = $this->subval_sort($this->data["topic"],'dateSubmitted', "arsort"); 
			$this->html .= "<div class='mainforumtitle'><div class='topic'>Topic</div><div class='by'>Created by</div><div class='replies'>Replies</div><div class='views'>Views</div><div class='lastupdated'>Last Updated</div></div>";
			foreach ($this->data["topic"] as $topic)
				$this->html .= "<div class='topiclist'><div class='topic'><a href='?topic=".$topic['id']."'>".$topic['title']."</a></div><div class='by'>".$topic['poster']."</div><div class='replies'>".$topic["replies"]."</div><div class='views'>0</div><div class='lastupdated'>".date("jS F Y, H:i:s", $topic['dateSubmitted'])."</div></div>";*/
			$this->data["topic"] = $this->subval_sort($this->data["topic"],'dateSubmitted', "arsort"); 
			$this->html .= "
			<div class='mainForumTitle'>
				<div class='middle' id='topic'>Topic</div>
				<div class='middle' id='by'>Created by</div>
				<div class='middle' id='replies'>Replies</div>
				<div class='middle' id='views'>Views</div>
				<div class='middle' id='lastUpdated'>Last Updated</div>
			</div>";
			
			foreach ($this->data["topic"] as $topic) {
				$this->html .= "
				<div class='topicList'>
					<div class='middle' id='topic'><a href='?topic=".$topic['id']."'>".$topic['title']."</a></div>
					<div class='middle' id='by'>".$topic['poster']."</div>
					<div class='middle' id='replies'>".$topic["replies"]."</div>
					<div class='middle' id='views'>0</div>
					<div class='middle' id='lastUpdated'>".date("d/m/y, H:i:s", $topic['dateSubmitted'])."</div>
				</div>";
			}
		}
	}
	// STOP: Template Functions
	
	
	// START: Helping functions
	// Function to sort an array (arg1) by the key (arg2) using a sort function (arg3)
	function subval_sort($a,$subkey,$sort) {
		foreach($a as $k=>$v) {
			$b[$k] = $v[$subkey];
		}
		$sort($b);
		foreach($b as $key=>$val) {
			$c[] = $a[$key];
		}
		return $c;
	}
	
	
	// Function to add html tags and attributes to text
	function addHtml ($text, $tag, $attribute=false) {
		$opentag = "<$tag";
		if (is_array($attribute)) {
			foreach ($attribute as $key => $value)
				$opentag .= " $key='$value'";
		}
		$opentag .= ">";
		$closetag = "</$tag>";	
		
		$text = $opentag.$text.$closetag;
		
		return $text;
	}
	// STOP: Helping functions
	
	
	

}
           


?>