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

require_once( "Div.func.inc.php" );
require_once( "all.inc.php" );
require_once( "Database.class.inc.php" );

$database = new Database($DBLOGIN,$DBPASSW,$DBHOST,$DBDATABASE);
$database->connect();

if (strstr($_POST['user'],'@')) {
	$sql ="select username, password, email from User where email='" .$_POST['user'] ."'";
} else {

	$sql ="select username, password, email from User where username='" .$_POST['user'] ."'";
}
//echo $sql;

if ($result = $database->query($sql)) {

$row = $database->fetchArray();

$email = $row['email'];
$userName = $row['username'];
$password = $row['password'];


if (sendMail($email, $userName, $password)) {}

echo "
<html>
<head>
<meta http-equiv='refresh' content='1;url=../login.html?pass=yes&fail=no&email=yes'>
</head>
</html> ";

} else{

echo "
<html>
<head>
<meta http-equiv='refresh' content='1;url=../login.html?pass=yes&fail=yes'>
</head>
</html> ";


}
      function sendMail($e, $u, $p) {
                $msg = "This mail is sent to you regarding your password\n";
                $msg .= "Your accont information is: \n\n";
                $msg .= "\tUsername and password\n";
                $msg .= "\tUsername:\t\t\t$u\n";
                $msg .= "\tPassword:\t\t\t$p\n";
                $msg .= "\n\n\tTo log in please go to http://www.tkoc.net/login.html";
                $to = $e;
                $subject = "The Kingdoms of Chaos";
                $mailheaders = "From: Chaos Admin <prosjekt@pad.thurmann.net> \n";
                $mailheaders .= "Reply-To: prosjekt@pad.thurmann.net\n\n";
                mail($to, $subject, $msg, $mailheaders);
}
?>