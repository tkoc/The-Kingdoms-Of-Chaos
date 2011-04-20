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
 * @extensions pxVfsAuthentication
 * @edit
 */
class pxVfsAuthentication extends pxVfs
{
	var $_oAuthentication;

	/**
	 * Cached result of $this->_oAuthentication->getUsers()
	 */
	var $_aUsers;

	var $aPossibleActions = array(
		'upload' => false,
		'share' => false,
		'edit' => true,
		'md5' => false
	);
	
	/**
	 * Constructor
	 */
	function pxVfsAuthentication() {
		parent::pxVfs();
		$this->iEvalShareTree = 0;
	}

	/**
	 * 
	 */
	function init() {
		$this->connect();
		parent::init();
	}

	/**
	 * 
	 */
	function connect() {
		global $pxp;

		if (!$this->_oAuthentication) {
			$this->_oAuthentication = $pxp->getObject($pxp->aConfig['sAuthentication'], false);
			$this->_oAuthentication->connect();
			$this->_aUsers = $this->_oAuthentication->getUsers();
		}
		return true;
	}
	
	/**
	 * 
	 */
	function disconnect() {
		unset($this->_oAuthentication);
	}

	/**
	 * Return file data
	 *  
	 * @param string $sPath
	 * 
	 * @return mixed Data of file at $aPath
	 */
	function file_get_contents($sPath) {
    return false;
	}
	
	/**
	 * 
	 */
	function file_put($sPath, $sTmpPath) {
		return false;
	}

	/**
	 * 
	 */
	function file_put_contents($sPath, $sData) {
		return false;
	}

	/**
	 *
	 */
	function is_file($sPath) {
		$mResult = parent::is_file($sPath);
		if ($mResult !== null) {
			return $mResult;
		} else {
			return isset($this->_aUsers[substr($sPath, 1)]);
		}
	}

	/**
	 * 
	 */
	function filesize($sPath) {
		$mResult = parent::filesize($sPath);

		if ($mResult !== null) {
			return $mResult;
		} else {
			return '';
		}
	}

	/**
	 * 
	 */
	function touch($sPath) {
		parent::touch($sPath);
	}

	/**
	 * 
	 */
	function unlink($sPath) {
		$bResult = $this->_oAuthentication->removeUser(substr($sPath, 1));
		parent::unlink($sPath, true);
		return $bResult;
	}

	/**
	 * 
	 */
	function _ls(&$oQuery, &$aFiles, $sCurrentDir)
	{
		foreach ($this->_aUsers as $sUser => $sPassword) {
 			if (!empty($oQuery->aNames) and $sCurrentDir == $oQuery->sDirectory and !in_array($sUser, $oQuery->aNames)) {
 				continue;
 			}
	  	$aFiles[$sUser]['sName'] = $sUser;
 			$aFiles[$sUser]['bDirectory'] = false;
 			$aFiles[$sUser]['iBytes'] = '';
 			$aFiles[$sUser]['dModified'] = '';
 			$aFiles[$sUser]['sType'] = 'pxVfsAuthenticationUser';
 			$aFiles[$sUser]['sExtension'] = 'pxAuthUser';
 			$aFiles[$sUser]['sOwner'] = $sUser;
 		}
  }

	/**
	 * Store serialized objects in filesystem
	 */
	function store_object(&$oObject)
	{
		global $pxp;

		$oObject->sName = str_replace('.pxAuthUser', '', $oObject->sName);

		if (!parent::store_object($oObject)) {
			if (isset($this->_aUsers[$oObject->sName])) {
				if (!empty($oObject->sPassword)) {
					if ($oObject->sPasswordConfirm == $oObject->sPassword) {
						return $this->_oAuthentication->changePassword($oObject->sName, $oObject->sPassword);
					}
				}
			} else {
				return $this->_oAuthentication->addUser($oObject->sName, $oObject->sPassword);
			}
		}
		return true;
	}

	/**
	 * 
	 */
	function mkdir($sPath) {
		return false;
	}
	
	/**
	 * 
	 */
	function is_dir($sPath) {
		return $sPath == '/';
	}

	/**
	 * 
	 */
	function &scandir($sDir) {
		return $this->_oAuthentication->getUsers();
	}

	/**
	 * 
	 */
	function rmdir($sPath) {
		return false;
	}

	/**
	 * 
	 */
	function file_exists($sPath) {
		$mResult = parent::file_exists($sPath);

		if ($mResult !== null) {
			return $mResult;
		} else {
			return $this->is_file($sPath); 
		}
	}

	/**
	 * 
	 */
	function filemtime($sPath) {
		return -1;
	}

	/**
	 * 
	 */
	function filectime($sPath) {
		return -1;
	}

	/**
	 * 
	 */
	function os_permissions($sPath) {
		return '';
	}

	/**
	 * 
	 */
	function os_owner($sPath) {
		return '';
	}

	/**
	 * 
	 */
	function os_group($sPath) {
		return '';
	}

	/**
	 * 
	 */
	function rename($sPathOld, $sPathNew) {
		$sUserOld = substr($sPathOld, 1);
		$sUserNew = substr($sPathNew, 1);
		$sPassword = $this->_oAuthentication->getPassword($sUserOld);
		$this->_oAuthentication->addUser($sUserNew, $sPassword, false);
		$this->_oAuthentication->removeUser($sUserOld);
		parent::rename($sPathOld, $sPathNew, true);
		return true;
	}
	
	/**
	 *
	 */
	function copy($sPathFrom, $sPathTo) {
		$sUserFrom = substr($sPathFrom, 1);
		$sUserTo = substr($sPathTo, 1);
		#$sPassword = $this->_oAuthentication->getPassword($sUserFrom);
		#$this->_oAuthentication->addUser($sUserTo, $sPassword, false);
 		parent::copy($sUserFrom, $sUserTo);
  }
}

?>