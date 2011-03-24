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
die("EDIT TO SEND!!!");
require_once ("/home/chaos/www/scripts/all.inc.php");
require_once ("/home/chaos/www/scripts/Database.class.inc.php");
// sending mass mail to all users

$msg = 'A New Age in The Kingdom of Chaos is Online!

Age '.$GLOBALS['config']['age'].' has just been placed online for registration.

To view the most recent changes please go to the forum and look in the announcement forum.
http://www.tkoc.net/scripts/forum.php?forumID=5
                        
We hope to see you again for a new exciting age
Regards,
         
Chaos Admins.
';

// do not edit below!!!
$subject = "The Kingdoms of Chaos";
$mailheaders = "From: Chaos Admin <admin@tkoc.net> \n";
$mailheaders .= "Reply-To: admin@tkoc.net\n\n";

// set up database
$database = new Database($DBLOGIN,$DBPASSW,$DBHOST,$DBDATABASE);
if ($database->connect()) {
  $num =0;
  $database->query("SELECT * From User where status='Active'");
  while ($database->numRows() && ($usr=$database->fetchArray())) {
	$num++;
	$message =  "Dear $usr[name]\n\n" . $msg;
//echo $message;
        $mail = $usr['email'];
        mail($mail, $subject, $message, $mailheaders);
	echo "sending mail ($num)\n";
  }

} else die ("DATABASE ERROR!");


$database->shutdown();
?>
