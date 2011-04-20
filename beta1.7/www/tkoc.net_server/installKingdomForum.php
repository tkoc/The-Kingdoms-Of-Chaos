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

$path = "../data/data.php";
@include ($path);
while (empty($currentPath)) {
	$path = "../".$path;
	@include ($path);
}

// Linking to the database
$link_id = mysql_connect($dbhost,$dbusername,$dbpassword, true);
if(!link_id)
	die ('Could not connect this database:'. mysql_error());
echo 'Connection to DATABASE.................................................SUCCESS<br>';
/*** Select the specific database ***/
mysql_select_db($dbname,$link_id);
/************************************/

/*
$query = 'CREATE TABLE FORUM('.
		 'FO_Eid		VARCHAR(30) 	NOT NULL,'.
		 'FO_id 		INT				NOT NULL AUTO_INCREMENT,'.
		 'PRIMARY KEY (FO_id) )';	 

$result = mysql_query($query,$link_id);

if(!$result)
{
	die('Could not perform query'.$query.mysql_error().'<br>');
}
echo "Query CREATE TABLE.....................................................SUCCESS<br>";*/


$query =	'CREATE TABLE Forum('.
			'id 			INT 			NOT NULL AUTO_INCREMENT,'.
			'kiID			INT			 	NOT NULL,'.
			'pID			INT			 	NOT NULL,'.
			'poster 		VARCHAR(150) 	NOT NULL,'.
			'parent 		INT 			NOT NULL,'.
			'title 			VARCHAR(255) 	NOT NULL,'.
			'message 		TEXT 			NOT NULL,'.
			'dateSubmitted 	INT 			NOT NULL,'.
			'dateEditted	INT 			NOT NULL,'.
			'PRIMARY KEY (id) )';

$result = mysql_query($query,$link_id);

if(!$result)
{
	die('Could not perform query'.$query.mysql_error().'<br>');
}

echo "Query CREATE TABLE.....................................................SUCCESS<br>";

?>