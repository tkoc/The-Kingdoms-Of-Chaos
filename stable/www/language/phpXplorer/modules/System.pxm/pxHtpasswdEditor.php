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
/* Copyright notice */

// Include passwd class for password encryption
require_once $pxp->sModuleDir . '/System.pxm/includes/PEAR/Passwd.php';

/**
 * Apache style .htpasswd file editor class
 */
class pxHtpasswdEditor
{
	var $aUsers = array();
	var $aPasswords = array();

	var $filename = '';
	var $aDeletedUsers = array();

	function pxHtpasswdEditor($sFilename)
	{
		if (empty($sFilename)) {
			die('Filename is empty!');
		}

		if(!file_exists($sFilename)) {
			die("File '$sFilename' not found!");
		}

		$this->filename = $sFilename;
		
		$aLines = file($sFilename);
		
		foreach ($aLines as $sLine) {
			$values = explode(':', $sLine);
			array_push($this->aUsers, trim($values[0]));
			array_push($this->aPasswords, trim($values[1]));
		}
	}
	
	function addUser($sUsername, $sPassword)
	{
		for ($u = 0; $u < sizeof($this->aUsers); $u++) {
			if ($this->aUsers[$u] == $sUsername) {
				$this->aPasswords[$u] = $this->_getPassword($sPassword);
				return true;
			}
		}
		
		array_push($this->aUsers, $sUsername);
		array_push($this->aPasswords, $this->_getPassword($sPassword));
		return true;
	}
	
	function deleteUser($sUsername)
	{
		$iSelIndex = -1;
				
		for ($u = 0; $u < sizeof($this->aUsers); $u++) {
			if ($this->aUsers[$u] == $sUsername) {
				$iSelIndex = $u;
			}
		}

		if ($iSelIndex > -1) {
			array_push($this->aDeletedUsers, $iSelIndex);
		} else {
			return false;
		}
	}
	
	function _getPassword($sPassword)
	{
		global $pxp;
		
		$pxp->oAuthentication = $pxp->getObject($pxp->aConfig['sAuthentication']);
		
		if (strpos(strToUpper(PHP_OS), 'WIN') === false) {
			return File_Passwd::crypt_des($sPassword, $pxp->oAuthentication->sSalt);
		} else {
			return File_Passwd::crypt_apr_md5($sPassword, $pxp->oAuthentication->sSalt);
		}
	}
	
	function writeFile()
	{
		global $pxp;

		$content = '';

		for ($u = 0; $u < sizeof($this->aUsers); $u++) {
			if (!in_array($u , $this->aDeletedUsers)) {
				$content .= $this->aUsers[$u] . ':' . $this->aPasswords[$u] . "\n";
			}
		}

		$pxp->oVfs->file_put_contents($this->filename, $content);
	}
}

?>