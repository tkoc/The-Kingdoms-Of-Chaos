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


// Include this function on your pages
function print_gzipped_page() {

    global $HTTP_ACCEPT_ENCODING;
    if( headers_sent() ){
        $encoding = false;
    }elseif( strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') !== false ){
        $encoding = 'x-gzip';
    }elseif( strpos($HTTP_ACCEPT_ENCODING,'gzip') !== false ){
        $encoding = 'gzip';
    }else{
        $encoding = false;
    }

    if( $encoding ){
        $contents = ob_get_contents();
        ob_end_clean();
        header('Content-Encoding: '.$encoding);
        print("\x1f\x8b\x08\x00\x00\x00\x00\x00");
        $size = strlen($contents);
        $contents = gzcompress($contents, 9);
        $contents = substr($contents, 0, $size);
        print($contents);
        exit();
    }else{
        ob_end_flush();
        exit();
    }
}

// At the beginning of each page call these two functions
ob_start("ob_gzhandler");
ob_implicit_flush(0);
error_reporting (E_NONE);

//include_once 'facebook/facebook.php';
//$fb=new Facebook();
//$frc = new FacebookRestClient("b1dcf6266502006b442c2c8a748021f1", "a6abddbe696cff08e12858051a23c367");
//print_r ($fb->require_login());
//$fb_user=$fb->get_loggedin_user();

header("Pragma: Public");
header("Cache-Control: max-age=3600, must-revalidate");
$offset = 60 * 60 * 24 * 3;
$ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
header($ExpStr);

$path = "../data/data.php";
@include ($path);
while (empty($currentPath)) {
	$path = "../".$path;
	@include ($path);
}

require_once($currentPath."scripts/all.inc.php");

$pagepath = str_replace ($currentDomain,"", $_SERVER['REQUEST_URL']);
$count1 = substr_count($pagepath, '.php');
$count2 = substr_count($pagepath, 'fb');

if ($count1 == 0 && $count2==0)
	$pagepath .= "index.php";
else if ($count1 == 0 && $count2!=0) // Facebook fix when sending data for the first page
	$pagepath = "index.php";
	

$pagepath = str_replace (".php","", $pagepath);

//require_once ($GLOBALS['path_root']."language/english.php");

$translation = "english";
//ob_start(); // start buffer

//$mylanguage ["full-page"] = ob_get_contents(); // assign buffer contents to variable
//ob_end_clean(); // end buffer and remove buffer contents
//$mylanguage ["full-page"] = str_replace ("<php>","", $mylanguage ["full-page"]);
//$mylanguage ["full-page"] = str_replace ("</php>","", $mylanguage ["full-page"]);


require_once ($GLOBALS['currentGamePath']."includes/security.php");
$security = new Security;

$css = $GLOBALS['currentGameDomain']."css/chaosnew-old.css";

$css = '<link rel="stylesheet" href="'.$css.'" type="text/css" />';

/*$headerjavascript = '<script type="text/javascript" src="'.$GLOBALS['path_domain_root'].'javascript/rollover.js"></script>';*/

$headerjavascript = '';
$headerjavascript .= '<script type="text/javascript" language="javascript" src="'.$GLOBALS['currentGameDomain'].'javascript/shortcut.js"></script>';
if (isset($GLOBALS['extra_javascript']))
	$headerjavascript .= $GLOBALS['extra_javascript'];
	


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<?php
@include_once ($GLOBALS['languagePath'].$translation."/".$pagepath.".txt");
@include_once ($GLOBALS['languagePath'].$translation."/header.txt");
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php echo $css; ?>
	<title><?php echo $mylanguage['head_title']; ?></title>
	<meta name="description" content="<?php echo $mylanguage['head_description']; ?>" />
	<meta name="keywords" content="<?php echo $mylanguage['head_keywords']; ?>" />
	<meta name="Generator" content="<?php echo $mylanguage['head_generator']; ?>" />
	<meta name="robots" content="index, follow" />
	<?php echo $headerjavascript; ?>
</head>
<body class="body">
	<div class="page-container">
		<div class="logo"><a href="<?php echo $GLOBALS['rootDomain']; ?>"><img src="<?php echo $GLOBALS['rootDomain']; ?>images/logo.png" alt="<?php echo $mylanguage['logo']; ?>" /></a></div>
		<div class="buttons">
			<ul>
				<li>
					<a href="<?php echo $GLOBALS['currentGameDomain']; ?>index.php">Home</a>
				</li>
				<li>
					<a href="<?php echo $GLOBALS['currentGameDomain']; ?>history.php">History</a>
				</li>
				<li>
					<a href="<?php echo $GLOBALS['currentGameDomain']; ?>results.php">Results</a>
				</li>
				<li>
					<a href="<?php echo $GLOBALS['currentGameDomain']; ?>about.php">Credits</a>
				</li>
				<li>
					<a href="<?php echo $GLOBALS['rootDomain']; ?>guide-old">Guide</a>
				</li>
				<li>
					<a href="<?php echo $GLOBALS['rootDomain']; ?>worldforum">Forum</a>
				</li>
				<?php
				if ($GLOBALS['context']['user']['is_logged']) {
					echo '	<li>
								<a href="'.$GLOBALS['currentGameDomain'].'scripts/showProvince.php">Game</a>
							</li>';
					echo '
							<li>
								'.ssi_logout($GLOBALS['currentGameDomain']."index.php").'
							</li>
							';
							
				}
				else {
					echo '	<li>
								<a href="'.$GLOBALS['rootDomain'].'worldforum/index.php?action=register">Register</a>
							</li>
					
							<li>
								<a href="'.$GLOBALS['currentGameDomain'].'login.php">Login</a>
							</li>';
				}
				?>
			</ul>
		</div>

