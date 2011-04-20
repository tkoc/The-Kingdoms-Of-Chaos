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
 * Vfs local filesystem driver
 *
 * Implements functionality to work with the local filesystem
 *
 * @extensions pxVfsFilesystem
 * @edit
 */
class pxVfsFilesystem extends pxVfs{
	/**
	 * 
	 */
	function pxVfsFilesystem() {
		parent::pxVfs();
		$this->bConnected = true;
	}

	/**
	 * Return file data
	 * 
	 * @param string $sPath
	 * 
	 * @return mixed Data of file at $aPath
	 */
	function file_get_contents($sPath)
	{
		parent::file_get_contents($sPath);

		if (function_exists('file_get_contents')) {
			return @file_get_contents($sPath);
		} else {
			$rHandle = @fopen($sPath, 'rb');

			if (!$rHandle) {
				return false;
			}

			$sData = '';
		
			while (!feof($rHandle)) {
				$sData .= fgets($rHandle, 4096);
			}

			fclose($rHandle);

			return $sData;
		}
	}

	/**
	 * 
	 */
	function file_put($sPath, $sTmpPath) {
		$this->mkdir(pxUtil::dirname($sPath));
		$bResult = @copy($sTmpPath, $sPath);
		$this->iLastInsertId = parent::file_put($sPath, $sTmpPath, $bResult);
		return $bResult;
	}

	/**
	 * 
	 */
	function file_put_contents($sPath, $sData)
	{
		$this->mkdir(pxUtil::dirname($sPath));

		$bResult = false;

		$rHandle = @fopen($sPath, 'wb');

		if ($rHandle !== false) {
			fwrite($rHandle, $sData);
			fclose($rHandle);
			$bResult = true;
		}

		parent::file_put_contents($sPath, $sData, $bResult);
		
		return $bResult;
	}
	
	/**
	 * 
	 */
	function is_file($sPath)
	{
		$bResult = parent::is_file($sPath);

		if ($bResult !== null) {
			return $bResult;
		} else {
			return is_file($sPath); 
		}
	}

	/**
	 * 
	 */
	function filesize($sPath) {
		$iResult = parent::filesize($sPath);
		if ($iResult !== null) {
			return $iResult;
		} else {
			return filesize($sPath); 
		}
	}

	/**
	 * 
	 */
	function touch($sPath) {
		parent::touch($sPath);
		touch($sPath);
	}

	/**
	 * 
	 */
	function unlink($sPath) {
		$bResult = @unlink($sPath);
		parent::unlink($sPath, $bResult);
		return $bResult;
	}

	/**
	 * 
	 */
	function _ls(&$oQuery, &$aFiles, $sCurrentDir)
	{
		global $pxp;

		#$sRegExp = $oQuery->getRegExp();

 		if (!empty($oQuery->aNames) and $sCurrentDir == $oQuery->sDirectory) {

 			foreach ($oQuery->aNames as $sName) {
 				$aFiles[$sName] = array(
					'sName' => $sName,
					'bDirectory' => is_dir($sCurrentDir . '/' . $sName)
				);
 			}

 		} else {

 			$rHandle = @opendir($sCurrentDir);
 			if ($rHandle) {

  			while (false !== ($sName = readdir($rHandle))) {
  		  	if ($sName == '.' or $sName == '..') {
  		  		continue;
  		  	}

 					$aFiles[$sName] = array(
 						'sName' => $sName,
						'bDirectory' => is_dir($sCurrentDir . '/' . $sName)
					);

					if ($oQuery->sOrderBy != 'sName') {
	 					switch ($oQuery->sOrderBy) {
 							case 'iBytes':
	 							$aFiles[$sName]['iBytes'] = filesize($sCurrentDir . '/' . $sName);
 								break;
 							case 'dModified':
	 							$aFiles[$sName]['dModified'] = filemtime($sCurrentDir . '/' . $sName);
 								break;
 							case 'sType':
	 							$aFiles[$sName]['sType'] = null;
 								$aFiles[$sName]['sExtension'] = null;
 								$pxp->getTypeKeyByExtension($sName, $aFiles[$sName]['bDirectory'], $aFiles[$sName]['sType'], $aFiles[$sName]['sExtension']);
 						}
					}
 				}

 				closedir($rHandle);
 			}
 		}
	}

	/**
	 * Store serialized objects in filesystem
	 */
	function store_object(&$oObject)
	{
		global $pxp;

		if (!parent::store_object($oObject))
		{
			$sFullPath = pxUtil::buildPath(
				$pxp->aShares[$oObject->sShare]->sBaseDir,
				$oObject->sRelDir . '/' . $oObject->sName
			);

			if ($pxp->aTypes[$oObject->sType]->bDirectory) {
				if (!$this->is_dir($sFullPath)) {
					if (!$this->mkdir($sFullPath)) {
						return false;
					}
				}
			} else {
				if (!$this->is_file($sFullPath)) {
					$sNew = '';
					if (!$this->file_put_contents($sFullPath, $sNew)) {
						return false;
					}
				}
			}

			$sDir = pxUtil::dirname($sFullPath);

			$this->makeMetaDataDirectory($sDir);

			$sDataPath = $sDir . '/.phpXplorer/.objects/' . $oObject->sName . '.serializedPhp';

			$sSerialized = serialize($oObject);

			if (!$this->file_put_contents($sDataPath, $sSerialized)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * 
	 */
	function mkdir($sPath)
	{
		global $pxp;

		$bResult = false;
		if (!$this->is_dir($sPath)) {
			$bResult = true;
			if (!$this->is_dir(pxUtil::dirname($sPath))) {
				$bResult = $this->mkdir(pxUtil::dirname($sPath));
			}
			if ($bResult) {
				$bResult = @mkdir($sPath, intval($pxp->aConfig['sMkDirMode'], 8));
			}
		}

		$this->iLastInsertId = parent::mkdir($sPath, $bResult);
		return $bResult;
	}

	/**
	 * 
	 */
	function is_dir($sPath) {
		$bResult = parent::is_dir($sPath);

		if ($bResult !== null) {
			return $bResult;
		} else {
			return is_dir($sPath); 
		}
	}
	
	/**
	 * 
	 */
	function &scandir($sDir)
	{
		$aResult = parent::scandir($sDir);

		if ($aResult !== null) {
			return $aResult;
		} else {
			$aFiles = array();
			if (function_exists('scandir')) {
				$aFiles = scandir($sDir);
				return $aFiles;
			} else {
				$rHandle = @opendir($sDir);
				if ($rHandle) {
					while (false !== ($sName = readdir($rHandle))) {
						if ($sName == '.' or $sName == '..') {
							continue;
						}
						$aFiles[] = $sName;
					}
				}
			}
			return $aFiles;
		}
	}

	/**
	 * 
	 */
	function rmdir($sPath)
	{
		if (empty($sPath)) {
			return false;
		}

		$bResult = true;
		
		if (!isset($this->_oQuery)) {
			$this->_oQuery =& new pxQuery;
		}

		$this->_oQuery->sDirectory = $sPath;

		foreach ($this->ls($this->_oQuery) as $oObject) {
  		if ($oObject->bDirectory) {
  			$bResult = $this->rmdir($sPath . '/' . $oObject->sName);
  		} else {
  			$bResult = $this->unlink($sPath . '/' . $oObject->sName);	
  		}
  		if ($bResult === false) {
  			break;
  		}
  	}

  	if ($bResult) {
  		$bResult = @rmdir($sPath);
  	}

  	parent::rmdir($sPath, $bResult);
  	return $bResult;
	}

	/**
	 * 
	 */
	function file_exists($sPath)
	{
		$bResult = parent::file_exists($sPath);

		if ($bResult !== null) {
			return $bResult;
		} else {
			return file_exists($sPath); 
		}
	}

	/**
	 * 
	 */
	function filemtime($sPath) {
		$dResult = parent::filemtime($sPath);
		if ($dResult !== null) {
			return $dResult;
		} else {
			return filemtime($sPath);
		}
	}

	/**
	 * 
	 */
	function filectime($sPath) {
		$dResult = parent::filectime($sPath);
		if ($dResult !== null) {
			return $dResult;
		} else {
			return filectime($sPath); 
		}
	}

	/**
	 * 
	 */
	function os_permissions($sPath) {	
		$iPerms = fileperms($sPath);
  	$sPerms = '';
  	if(($iPerms & 0xC000) === 0xC000)      // Unix domain socket
     $sPerms = 's';
    elseif(($iPerms & 0x4000) === 0x4000)  // Directory
     $sPerms = 'd';
    elseif(($iPerms & 0xA000) === 0xA000)  // Symbolic link
     $sPerms = 'l';
    elseif(($iPerms & 0x8000) === 0x8000)  // Regular file
     $sPerms = '-';
    elseif(($iPerms & 0x6000) === 0x6000)  // Block special file
     $sPerms = 'b';
    elseif(($iPerms & 0x2000) === 0x2000)  // Character special file
     $sPerms = 'c';
    elseif(($iPerms & 0x1000) === 0x1000)  // Named pipe
     $sPerms = 'p';
    else                                  // Unknown
     $sPerms = '?';
     // owner
     $sPerms .= (($iPerms & 0x0100) ? 'r' : '&minus;') . (($iPerms & 0x0080) ? 'w' : '&minus;') . (($iPerms & 0x0040) ? (($iPerms & 0x0800) ? 's' : 'x' ) : (($iPerms & 0x0800) ? 'S' : '&minus;')); 
     // group
     $sPerms .= (($iPerms & 0x0020) ? 'r' : '&minus;') . (($iPerms & 0x0010) ? 'w' : '&minus;') . (($iPerms & 0x0008) ? (($iPerms & 0x0400) ? 's' : 'x' ) : (($iPerms & 0x0400) ? 'S' : '&minus;')); 
     // world
     $sPerms .= (($iPerms & 0x0004) ? 'r' : '&minus;') . (($iPerms & 0x0002) ? 'w' : '&minus;') . (($iPerms & 0x0001) ? (($iPerms & 0x0200) ? 't' : 'x' ) : (($iPerms & 0x0200) ? 'T' : '&minus;'));
     $sPerms = str_replace('&minus;', '-', $sPerms);
     return $sPerms;
	}
	
	/**
	 * 
	 */
	function os_owner($sPath) {
  	if (function_exists('posix_getpwuid')) {
  		$aInfo = @posix_getpwuid(fileowner($sPath));
  		return $aInfo['name'];
  	} else {
  		return '';
  	}
	}
	
	/**
	 * 
	 */
	function os_group($sPath) {
  	if (function_exists('posix_getpwuid')) {
  		$aInfo = @posix_getgrgid(filegroup($sPath));
  		return $aInfo['name'];
  	} else {
  		return '';
  	}
	}

	/**
	 * 
	 */
	function rename($sPathOld, $sPathNew) {
		$bResult = @rename($sPathOld, $sPathNew);
		if ($bResult) {
			parent::rename($sPathOld, $sPathNew, $bResult);
		}
		return $bResult;
	}

	/**
	 * 
	 */
	function copy($sPathFrom, $sPathTo) {
		$bResult = copy($sPathFrom, $sPathTo);
		if ($bResult) {
			$this->iLastInsertId = parent::copy($sPathFrom, $sPathTo);
		}
		return $bResult;
	}

	/*
	function copy($sPathFrom, $sPathTo, $bRecursive = false)
	{
  	if ($this->is_dir($sPathTo)) {
  		$sNewDest = $sPathTo . '/' . basename($sPathFrom);
  	} else {
  		$sNewDest = $sPathTo;
  	}

		if (!$bRecursive) {
			parent::copy($sPathFrom, $sNewDest);			
		}

  	if ($this->is_dir($sPathFrom)) {
			$this->mkdir($sNewDest);
			if (!isset($this->_oQuery)) {
				$this->_oQuery =& new pxQuery;
			}
			$this->_oQuery->sDirectory = $sPathFrom;

  		foreach ($this->ls($this->_oQuery) as $oObject) {
  			$this->copy(
  				$sPathFrom . '/' . $oObject->sName,
  				$sNewDest,
  				true
  			);
  		}
  	} else {
  		copy($sPathFrom, $sNewDest);
  	}
	}
	*/
	

	/**
	 * 
	 */
	function getimagesize($sPath) {
		parent::getimagesize($sPath);
		return @getimagesize($sPath);
	}
	var $sType = 'pxVfsFilesystem';}

?>