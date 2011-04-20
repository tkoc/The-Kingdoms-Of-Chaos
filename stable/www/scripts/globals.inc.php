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

//**********************************************************************
//* Globals
//*
//* ALL pre-set global variables goes here, and ONLY variables. no functions
//*
//*	This file should be included in EVERY script. (At the top)
//* 
//* Author: Anders Elton
//**********************************************************************

//**********************************************************************
//* Functions
//**********************************************************************

//**********************************************************************
//* clock()
//* Used to time the server / scripts. takes timestamp in microtime
//**********************************************************************
function myclock()
{
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}


$path = "../data/data.php";
@include ($path);
while (empty($currentPath)) {
	$path = "../".$path;
	@include ($path);
}

//**********************************************************************
//* Variables
//**********************************************************************

// paths (serverside)
$GLOBALS['path_root'] 			= $base_www;
$GLOBALS['path_home'] 			= $base_www;
$GLOBALS['path_www'] 			= $base_www;
$GLOBALS['maindomain']			= $base_www;
$GLOBALS['path_www_scripts']	= $GLOBALS['path_scripts'] = $GLOBALS['path_www'].'scripts/';
$GLOBALS['path_language']		= $GLOBALS['maindomain'].'dokuwiki/data/pages/language/';
$GLOBALS['path_includes']		= $GLOBALS['path_www'].'includes/';
$GLOBALS['path_www_administration'] 	= $GLOBALS['path_www'].'administration/';
$GLOBALS['path_server']			= $base_www."tkoc.net_server/";
$GLOBALS['FILE_ERROR_LOG']		= $base_www . 'error.log';
$GLOBALS['FILE_LOG']			= $base_www.'game.log';
$GLOBALS['server_email']		= 'admin@tkoc.net';
// old style definitions
$GLOBALS['WWW_SCRIPT_PATH']		= $GLOBALS['path_www_scripts'];
define("WWW_SCRIPT_PATH",$GLOBALS['path_www_scripts']);

// www-related stuff
$GLOBALS['domain_name'] 		= "";
$GLOBALS['path_domain_root'] 	= $domain;
$GLOBALS['path_domain_img']		= $GLOBALS['path_domain_root'] . 'img/';
$GLOBALS['path_domain_script']	= $GLOBALS['path_domain_root'] . 'scripts/';
$GLOBALS['path_domain_ads']		= $GLOBALS['path_domain_root'] . 'adscripts/';
$GLOBALS['path_domain_admin']	= $GLOBALS['path_domain_root'] . 'administration/';
$GLOBALS['path_domain_thoc']	= $GLOBALS['path_domain_root'] . 'thoc/';
$GLOBALS['site_header']			= 'The Kingdoms of Chaos';
$GLOBALS['script_mode']         = 'web';
$GLOBALS['extra_javascript']    = '';

// game stuff
$GLOBALS['game_debug'] 			= false;		// display/collect debug stuff?
$GLOBALS['game_start_clock']	= myclock();		// timestamp
$GLOBALS['game_debug_data']		= "";			// a place for scripts to write debug output.

// forum
if ( !class_exists("Constants") ) {
class Constants {
	// access
	var $USER_NORMAL 	= 1;
	var $USER_VOICE  	= 2;
	var $USER_OP	 	= 4;
	var $USER_KING	 	= 4; // used for forum
	var $USER_DEVELOPER	= 4; // ignore the other two.. yay, hack ftw!
	var $USER_DONATED_CASH  = 8;
	var $USER_POST_NEWS = 16;
	var $USER_MODERATOR = 32; // forum
	var $USER_ADMIN		= 64;
	var $USER_GAME_ADMIN= 128;
	var $USER_GOD		= 256;
	// other stuff
	var $POST_PR_PAGE	= 15;
	var $POST_ORDER		= 'ASC';  // ASC / DESC
	var $KEEP_FIRST_POST_TOPPED = true;		// if true, keeps first post in thread at top.
	var $THREADS_PR_PAGE = 20;
	
	// ranks
	var $THIEVERY_RANKS		= array(
			"Peasant"     =>  800,
			"Bandit"      => 1001,
			"Thief Lord"  => 1500,
			"Thief Baron" => 2250,
			"Viscount" => 3000,
			"Thief King"  => 4000);

	var $MAGIC_RANKS = array(  
				"Ungifted" 	=>   50,
				"Gifted" 	=>  100,
				"Student" 	=>  200,
				"Apprentice" 	=>  500,
				"Maegi" 	=> 1000,
				"Battle mage" 	=> 1750,
				"High mage" 	=> 3000,
				"Archmage" 	=> 5000);


}
$GLOBALS['constants'] = new Constants;
}

?>