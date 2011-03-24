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
//**********************************************************************
//* file: magic.php
//*
//* Handles the magic page
//*
//* Author: Øystein Fladby	28.04.2003
//*
//* History
//*		01.08.04: Anders Elton.  Rewrote to fit new coding standard.
//**********************************************************************

require_once ("all.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "User.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Province.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Magic.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "requireLoggedOn.inc.php");

//**********************************************************************
//*	Code starts here
//**********************************************************************

//templateDisplay($province,"Updating","../img/Cornerpictures/Magic_picture.jpg","");
//die();

$magic = new Magic( $database, $province );
$html = $magic->getDisplay();
$html .= "<center><img src='../img/wizardtower.jpg' border='0'></img></center>";
$province->getProvinceData();

templateDisplay($province,$html,"../img/Cornerpictures/Magic_picture.jpg","");
?>
