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

echo '<?xml version="1.0" encoding="ISO-8859-1"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
<title>phpXplorer Installation</title>
<style type="text/css">

body {
	font-family: Georgia;
	text-align: center;
	color: #444;
}

#content {
	width: 40em;
	margin: auto;
	text-align: left;
	padding-top: 1.5em;
}

p, h1, h2, h3, ul {
	margin: 0;
	padding: 0;
}

h1 img {
	float: right;
	padding-right: 0.5em;
}

h1, h2 {
	font-weight: normal;
	overflow: hidden;
	color: #39C;
	font-family: Tahoma;
	font-size: 1.6em;
}

h1 {
	color: #036;
}

h2 {
	/*border-top: 1px solid #EEE;*/
	padding-top: 0.6em;
	margin-top: 0.4em;
	margin-bottom: 0.2em;	
}

ul {
	list-style: none;
	padding: 0.5em 0.5em 0 0.5em;
}

ul li {
	font-weight: bold;
}

ul li.dirReadonly {
	color: red;
}

ul li.dirOk {
	color: green;
}

ul li span.baseDir {
	font-weight: normal;
	color: #AAA;
}

p, ul {
	line-height: 1.6em;	
}

a {
	color: #36C;
}

img.status {
	margin-left: 0.5em;
}

p.input {
	padding-top: 0.4em;	
}

p.input img {
	margin-right: 0.5em;
	vertical-align: middle;
}

p button {
	display: block;
	margin-top: 1.2em;
}

</style>
<script type="text/javascript">
</script>
</head>
<body>

<form method="post" action="./install.php">

<?php

class pxInstaller
{
	var $sLanguage = 'en';
	var $aAllLanguages = array('de', 'en');
	var $aLanguages = array();
	var $aLanguageCodes = array();
	
	var $sPassword = '';
	
	var $aWritableDirs = array(
		'.htpasswd',		
		'.phpXplorer',
		'bookmarks',
		'homes',
		'cache',
		'profiles',
		'shares',
		'config.php',
		'install.php'
	);

	function pxInstaller()
	{
		if (isset($_POST['language'])) {
			if (in_array($_POST['language'], $this->aAllLanguages)) {
				$this->sLanguage = $_POST['language'];
			}
		}

		if (isset($_POST['password'])) {
			$this->sPassword = $_POST['password'];
		}
	}

	function buildGui()
	{
		$this->aLanguages[$this->sLanguage] = array();

		require_once dirname(__FILE__) . '/modules/Installation.pxm/translations/' . $this->sLanguage . '.php';
		$this->aTranslation =& $this->aLanguages[$this->sLanguage];
		
// LANGUAGE SETTING

		$sSystemLangDir = dirname(__FILE__) . '/modules/System.pxm/translations/';

		if (file_exists($sSystemLangDir . $this->sLanguage . '.languages.php')) {
			require $sSystemLangDir . $this->sLanguage . '.languages.php';
		} else {
			require $sSystemLangDir . 'en.languages.php';
		}

		$sHtml =
			'<div id="content">' .
			'<h1><img src="./modules/Customization.pxm/graphics/logo.png" alt="" /></h1>' . 
			'<h2>' . $this->translate('installation') . '</h2>' .
			'<p>' . $this->translate('installationText') . '</p>' .
			
			'<h2>' . $this->translate('chooseLanguage') . '</h2>' .
			'<p>' . $this->translate('chooseLanguageText') . '</p>' .
			'<p class="input">' .
				'<img src="./modules/System.pxm/graphics/comment.png" alt="" class="inline" />' . 
				'<select class="inline" name="language" size="1" onchange="this.form.submit()">';

			foreach ($this->aAllLanguages as $sLanguageCode) {
				$sSel = $sLanguageCode == $this->sLanguage ? ' selected="selected"' : '';
				$sHtml .=
					'<option value="' . $sLanguageCode . '"' . $sSel . '>' .
						$this->aLanguageCodes[$sLanguageCode] .
					'</option>';
			}
			
			$sHtml .=
				'</select>' .
			'</p>';
			
// DIRECTORY PERMISSIONS

		$sHtml .=
			'<h2>' . $this->translate('webserverPermissions') . '</h2>' .
			'<p>' . $this->translate('webserverPermissionsText') . '</p>' .
			'<ul class="writableDirs">';
		
		$bAllOk = true;
		foreach ($this->aWritableDirs as $sDir) {
			$bOk = is_writable(dirname(__FILE__) . '/' . $sDir);
			if (!$bOk) {
				$bAllOk = false;
			}
			$sHtml .=
				'<li class="dir' . ($bOk ? 'Ok' : 'Readonly') . '">' .
					'<span class="baseDir">&hellip;/phpXplorer/</span>' . $sDir . '/';
			if ($bOk) {
				$sHtml .= '<img src="./modules/System.pxm/graphics/tick.png" alt="" class="status" />';
			}
			$sHtml .= '</li>';
		}
		$sHtml .= '</ul>';
		if (!$bAllOk || true) {
			$sHtml .= '<p><button type="submit">' . $this->translate('recheck') . '</button></p>';
		}
		
// ADMIN PASSWORD
		
		$sHtml .=
			'<h2>' . $this->translate('adminPassword') . '</h2>' .
			'<p>' . $this->translate('adminPasswordText') . '</p>' .
			'<p class="input">' .
				'<img src="./modules/System.pxm/graphics/key.png" alt="" class="inline" />' .
				'<input type="password" name="password" value="' . $this->sPassword . '" />' .
			'</p>';
		
		
		$sHtml .= '</div>';

		return $sHtml;
	}

	function translate($sId) {
		$sText = $this->aTranslation[$sId];
		$bSwitch = true;
		while (strpos($sText, '~') !== false) {
			$sText = pxInstaller::str_replace_once('~', $bSwitch ? '&raquo;' : '&laquo;', $sText);
			$bSwitch = !$bSwitch;
		}
		return $sText;
	}

	function str_replace_once($sNeedle, $sReplace, $sHaystack) {
		$iPos = strpos($sHaystack, $sNeedle);
		if($iPos === false) {
			return $sHaystack;
		}
	  return substr_replace($sHaystack, $sReplace, $iPos, strlen($sNeedle));
	}
}

$oInstaller = new pxInstaller();

echo $oInstaller->buildGui();

?>
</form>

</body>
</html>