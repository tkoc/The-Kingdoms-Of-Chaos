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

//die();
//************************************************
//* tick.php
//*
//* The game engine.  This should be run every tick
//* Author: Anders Elton
//*
//* History:
//*	- Rewrite 31.07.2004
//* - Rewrite 31.11.2009
//* 	> original server.php file was splitted to this "tick.php" and "server.php"
//* - Rewrite 06.01.2010
//*		> The transition from age to age is rewritten
//************************************************
echo "1 ".memory_get_usage()."<br />";
require_once ("./Server.class.inc.php");
require_once ("./ServerStatistics.class.inc.php");
gc_collect_cycles();
echo "2 ".memory_get_usage()."<br />";
$stats = new ServerStatistics($database);
$stats->DoTick();
gc_collect_cycles();
echo "3 ".memory_get_usage()."<br />";
$server = new Server($database);
$server->doTick();
gc_collect_cycles();
echo "4 ".memory_get_usage()."<br />";
var_dump(function_exists('gc_collect_cycles'));

//*
//*
//* functions below
function sendMassMail ($database)
{
	if ($GLOBALS['config']['serverMode'] == 'Beta')
	{
		mywriteLog ($GLOBALS['gameLog'],"\nNot sending spam mail, we're beta server.");
		return;
	}
	mywriteLog ($GLOBALS['gameLog'],"\nSending Email to users...");
	$msg = 'A New Age in The Kingdom of Chaos is Online!

Age '. ($GLOBALS['config']['age']+1) .' has just been placed online for registration.

To view the most recent changes please go to the forum and look in the announcement forum.
http://www.tkoc.net/scripts/forum.php?forumID=5

We hope to see you again for a new exciting age


If you no longer wish to exist on our mailing list,
please reply to this mail with the word unsubscribe in both header and subject.

Regards,
Chaos Admins.
';

	// do not edit below!!!
	$subject = "The Kingdoms of Chaos";
	$mailheaders = "From: Chaos Admin <admin@tkoc.net> \n";
	$mailheaders .= "Reply-To: admin@tkoc.net\n\n";
	
	$num =0;
	$database->query("SELECT * From User WHERE status!='Unsubscribe' AND status!='BADEMAIL' AND ".$GLOBALS['config']['age']." - lastPlayedAge < 10");
	while ($database->numRows() && ($usr=$database->fetchArray())) {
	  $num++;
	  $message =  "Dear $usr[name]\n\n" . $msg;
	  $mail = $usr['email'];
	  mail($mail, $subject, $message, $mailheaders);
	}
	
	mywriteLog ($GLOBALS['gameLog'],"\nDONE ($num messages sent!)");

}
/////////////////////////////////////
// void mywriteLog(filename, txt)
/////////////////////////////////////
//
// parameters:
//    filename: the filename to write to
//    txt     : the string to write
//
// writes txt to file.
/////////////////////////////////////

function mywriteLog ($filename, $txt) {
   if (is_writable($filename)) {

      if (!$handle = fopen($filename, 'a')) {
         print "Cannot open file ($filename)";
         exit;
      }

      if (!fwrite($handle, $txt)) {
         print "Cannot write to file ($filename)";
         exit;
      }
      fclose($handle);
   } else {
      echo "Error writing to $filname, file not writeable";
   }
}

?>