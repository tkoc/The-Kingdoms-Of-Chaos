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

// Database Data
$dbhost=""; // Usually localhost
$dbusername="";
$dbpassword="";
$dbname = $dbOldGame = ""; // The game's databases
$dbForum =""; // The forum's database

// Facebook Data
$fb_api_key= "";
$fb_secret = "";

/******************************
* The first variables are the new ones, the rest are getting initialized for compatibility issues
******************************/
// Path data
//$server = "E:/Server/tkoc.net/";
$currentPath = $base_www = $server = ""; // Path to the game instance e.g. D:/Server/tkoc.net/new/beta1.7/www/
$rootPath = ""; // Path to the main path of the folders e.g. D:/Server/tkoc.net/new/beta1.7/www/

// Domain data
$currentDomain = $domain = ""; // URL of the game instance e.g. http://localhost/tkoc.net/new/beta1.7/www/
$rootDomain = ""; // URL of the game instance e.g. http://localhost/tkoc.net/new/beta1.7/www/

$domaincookie = "";
?>