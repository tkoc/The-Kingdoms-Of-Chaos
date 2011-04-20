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

class pxUtil
{
	function buildPath($sDir, $sFile) {
		if (!empty($sDir)) {
			if (substr($sFile, 0, 1) == '/') {
				$sFile = substr($sFile, 1);
			}
			if (!empty($sFile) && substr($sDir, -1) != '/') {
				return $sDir . '/' . $sFile;
			} else {
				return $sDir . $sFile;
			}
		} else {
			return $sFile;
		}
	}

	function dirname($sDir) {
		$sParentDir = dirname($sDir);
		if($sParentDir == "\\") {
			$sParentDir = '/';
		}
		return $sParentDir;
	}

	function str_replace_once($sNeedle, $sReplace, $sHaystack) {
		$iPos = strpos($sHaystack, $sNeedle);
		if($iPos === false) {
			return $sHaystack;
		}
   return substr_replace($sHaystack, $sReplace, $iPos, strlen($sNeedle));
	}

	function stripSlashesRecursive($mVariable) {
		if (is_array($mVariable)) {
			foreach ($mVariable as $index => $value) {
				$mVariable[$index] = pxUtil::stripSlashesRecursive($value);
			}
		} else {
			$mVariable = stripslashes($mVariable);
		}
		return $mVariable;
	}

	function getBaseAction($sAction)
	{
		$iActionPos = strpos($sAction, '_');
		if ($iActionPos !== false) {
			$iActionPos ++;
		} else {
			$iActionPos = 0;
		}
		if (strpos($sAction, '_', $iActionPos)) {
			$iActionPos ++;
		}
		for ($i = $iActionPos, $m = strlen($sAction); $i < $m; $i ++) {
			$iChar = ord(substr($sAction, $i, 1));
			
			if (($iChar >= 65 && $iChar <= 90)) {
				return substr($sAction, $iActionPos, $i - $iActionPos);
			}
		}
		return substr($sAction, $iActionPos);
	}

	function getArrayString($aArray)
	{
  	$sArray = 'array(';
  	foreach ($aArray as $mKey => $mValue) {
  		if (!is_numeric($mKey)) {
  			$sArray .= '\'' . $mKey . '\'=>';
  		}
  		if (is_array($mValue)) {
  			$sArray .= '' . pxUtil::getArrayString($mValue) . ',';
  		}
			else if (is_numeric($mValue)) {
  			
  		}
			else if (is_bool($mValue)) {
  			$sArray .= (int)$mValue . ',';
  		}
			else {
  			$sArray .= '\'' . $mValue . '\',';
  		}
  	}
		if (!empty($aArray)) {
			$sArray = substr($sArray, 0, strlen($sArray) - 1);
		}
		return $sArray . ')';			
	}

	function getMicrotime() {
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}

	function checkFilename($sName, $bPath = false) {
		$aInvalidChars = array(chr(92), ':', '*', '?', '<', '>', '|', '..');
		if (!$bPath) {
			$aInvalidChars[] = '/';
		} else {
			$aInvalidChars[] = '//';
		}
		for ($i = count($aInvalidChars) - 1; $i >= 0; $i--) {
			if (strpos($sName, $aInvalidChars[$i]) !== false) {
				return false;
			}
		}
		return true;
	}
	
	function translateTitle($sTitle)
	{
		global $pxp;

		if (strpos($sTitle, '~') === 0) {
			$sKey = substr($sTitle, 1);
			if (isset($pxp->aTranslation[$sKey])) {
				return $pxp->aTranslation[$sKey];
			}
		}
		return $sTitle;
	}
}

?>