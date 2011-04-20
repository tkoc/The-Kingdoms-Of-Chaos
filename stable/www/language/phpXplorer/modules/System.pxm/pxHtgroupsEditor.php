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

/* Copyright notice */

/**
 * Apache style .htgroups file editor class
 */
class pxHtgroupsEditor
{
	var $aGroups = array();
	var $sFilename = '';
	var $deletedGroups = array();

	var $filePasswd;

	function pxHtgroupsEditor($sFilename)
	{
		if (empty($sFilename)) {
			die('Filename is empty');
		}

		if (!file_exists($sFilename)) {
			die('File "' . $sFilename . '" not found');
		}

		$this->sFilename = $sFilename;
		
		$aLines = file($sFilename);
		
		foreach ($aLines as $sLine) {
			$arr1 = explode(':', $sLine);
			$arr2 = explode(' ', $arr1[1]);

			$this->aGroups[$arr1[0]] = array();

			foreach($arr2 as $item2) {
				array_push($this->aGroups[$arr1[0]], trim($item2));
			}
		}
	}

	function addGroup($sGroupname, $aUsers = array())
	{
		$this->aGroups[$sGroupname] = $aUsers;
		return true;
	}

	function addUser($sGroupname, $sUsername)
	{
		if(!in_array($sUsername, $this->aGroups[$sGroupname]))
			array_push($this->aGroups[$sGroupname], $sUsername);
	}

	function hasGroup($sUsername)
	{
		foreach ($this->aGroups as $sKey => $aValues) {
			if (in_array($sUsername, $aValues)) {
				return true;
			}
		}
		return false;
	}

	function deleteUser($sUsername)
	{
		foreach ($this->aGroups as $sKey => $aValues) {
			if (in_array($sUsername, $aValues)) {
				for ($i = 0, $m = count($this->aGroups[$sKey]); $i < $m; $i++) {
					if ($this->aGroups[$sKey][$i] == $sUsername) {
						unset($this->aGroups[$sKey][$i]);
					}
				}
			}
		}
	}

	function deleteGroup($sGroupname)
	{
		$this->aGroups[$sGroupname] = array();
		return true;
	}

	function writeFile()
	{
		global $pxp;

		$pxp->oVfs->file_put_contents($this->sFilename, $this->getCode());
	}

	function getCode()
	{
		$content = '';

		foreach($this->aGroups as $sKey => $aValues) {
			if (!empty($aValues)) {
				$content .= $sKey . ":" . implode(' ', $aValues) . "\n";
			}
		}
		return $content;
	}
}

?>