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

class pxDirectories__editClipboard extends pxAction
{
	var $oSourceShare;
	var $oDestinationShare;

	var $sSourceShare;
	var $sDestinationShare;

	var $sDestinationPath;
	var $sRelDestinationPath;

	var $_bSameFs = false;
	var $oQuery;

	/**
	 * 
	 */
	function run()
	{
		global $pxp;
		
		$this->bImplementsEventHandling = true;

		// Check source

		$this->oSourceShare =& $pxp->oShare;
		if (!$this->oSourceShare->oVfs->is_dir($pxp->sFullPathIn)) {
			$pxp->raiseError('invalidParameter', __FILE__, __LINE__);
		}
		$this->sSourceShare = $pxp->sShare;


		// Check destination

		$this->sDestinationShare = $pxp->getRequestVar('sDestinationShare');
		if (!$pxp->loadShare($this->sDestinationShare)) {
			$pxp->raiseError('invalidParameter', __FILE__, __LINE__);
		}
		$this->oDestinationShare =& $pxp->aShares[$this->sDestinationShare];		
		
		$this->sRelDestinationPath = $pxp->handlePathParameter($pxp->getRequestVar('sDestinationPath'));
		$this->sDestinationPath = pxUtil::buildPath(
			$this->oDestinationShare->sBaseDir,
			$this->sRelDestinationPath
		);

		if (!$this->oDestinationShare->oVfs->is_dir($this->sDestinationPath)) {
			$pxp->raiseError('invalidParameter', __FILE__, __LINE__);
		}

		$this->_bSameFs = $this->oSourceShare->oVfs === $this->oDestinationShare->oVfs;

		$sMode = $pxp->getRequestVar('sMode');

		switch ($sMode) {
			case 'copy':
				$this->copyFiles();
				break;
			case 'move':
				$this->moveFiles();
				break;
		}
	}
	

	function copyFiles()
	{
		global $pxp;

		$oQuery = new pxQuery;
		$oQuery->sDirectory = $pxp->sFullPathIn;
		$oQuery->bRecursive = true;
		$this->getFileSelection($oQuery->aNames);

		$aObjects = $this->oSourceShare->oVfs->ls($oQuery);


		$aStack = array();
		for ($i = 0; $i < count($aObjects); $i++)
		{
			$oObject =& $aObjects[$i];

			$sName = isset($oObject->_sNewName) ? $oObject->_sNewName : $oObject->sName;
			$sExtension = isset($oObject->_sNewExtension) ? $oObject->_sNewExtension : $oObject->sExtension;
			$sId = str_replace('.' . $sExtension, '', $sName);

			$sDestPath = pxUtil::buildPath($this->sDestinationPath, $sName);

			$iNumber = 1;
			while ($this->oDestinationShare->oVfs->file_exists($sDestPath)) {
				$oObject->_sNewName = $sId . ' [' . $iNumber++ . ']';
				if (!empty($sExtension)) {
					$oObject->_sNewName .= '.' . $sExtension;	
				}
				$sDestPath = pxUtil::buildPath($this->sDestinationPath, $oObject->_sNewName);
			}

			$aStack[] = $oObject;
		}
		
		
		$this->createCheck($aObjects);

		$aFailedObjects = array();

		while ($oObject = array_pop($aStack))
		{
			$sSourcePath = $oObject->getFullPath();
			$sName = isset($oObject->_sNewName) ? $oObject->_sNewName : $oObject->sName;
			
			$sDestDir = pxUtil::buildPath(
				$this->sDestinationPath,
				$oObject->_sNewRelDir
			);
			
			$sDestPath = pxUtil::buildPath(
				$sDestDir,
				$sName
			);
			
			#echo $sDestPath . "<br/>";

			if ($sSourcePath != $sDestPath && strpos($sDestPath, $sSourcePath . '/') !== 0)
			{
				$bOk = false;

				if ($oObject->bDirectory)
				{
					if (!$this->oDestinationShare->oVfs->mkdir($sDestPath)) {
						$pxp->raiseError('selectionAction', __FILE__, __LINE__, array($sDestPath));
					} else {
						$bOk = true;
					}
				}
				else
				{
					if ($this->_bSameFs) {
						if ($this->oSourceShare->oVfs->copy($sSourcePath, $sDestPath)) {
							$bOk = true;
						}
					} else {
						if ($this->_crossCopy($sSourcePath, $sDestPath)) {
							$bOk = true;
						}
					}
				}

				if ($bOk)
				{
					$iNewId = $this->oDestinationShare->oVfs->iLastInsertId;

					if ($oObject->bDirectory) {
						foreach ($oObject->aObjects as $oSubObject) {
							$aStack[] = $oSubObject;
						}
					}

					$oObject->_iNewDatabaseRowId = $iNewId;
					$oObject->_sNewShare = $this->sDestinationShare;
					$oObject->_sNewRelDir = pxUtil::buildPath(
						$this->sRelDestinationPath,
						$oObject->_sNewRelDir
					);
					$sDestDir;
					$oObject->_sNewName = $sName;
					$oObject->triggerEvents('pxObject__editCreate');
					continue;
				}
			}
			
			$aFailedObjects[] = $oObject->sName;
		}

		if (!empty($aFailedObjects)) {
			$pxp->raiseError('selectionAction', __FILE__, __LINE__, $aFailedObjects);	
		} else {
			return true;
		}
	}

	function moveFiles()
	{
		global $pxp;

		$oQuery = new pxQuery;
		$oQuery->sDirectory = $pxp->sFullPathIn;
		$oQuery->bRecursive = true;
		$oQuery->bPermissionCheck = false;
		$this->getFileSelection($oQuery->aNames);

		$aObjects = $this->oSourceShare->oVfs->ls($oQuery);

		$aBookmarksToRename = array();
		$this->createCheck($aObjects);
		$this->deleteCheck($aObjects, $aBookmarksToRename);

		$aFailedObjects = array();

		
		for ($i = 0, $m1 = count($aObjects); $i < $m1; $i++)
		{
			$oObject =& $aObjects[$i];

			$sSourcePath = $oObject->getFullPath();
			$sName = isset($oObject->_sNewName) ? $oObject->_sNewName : $oObject->sName;
			$sDestPath = pxUtil::buildPath($this->sDestinationPath, $sName);
			
			$bOk = false;
			
			if ($this->_bSameFs)
			{
				if ($sSourcePath != $sDestPath && strpos($sDestPath, $sSourcePath . '/') !== 0) {
					if (!$this->oDestinationShare->oVfs->file_exists($sDestPath)) {
						if ($this->oSourceShare->oVfs->rename(
							$sSourcePath,
							$sDestPath
						)) {
							$bOk = true;
						}
					}
				}
			} else
			{
				if ($this->_crossCopy($sSourcePath, $sDestPath))
				{					
					$bOk = true;
					
					if ($this->oSourceShare->oVfs->is_dir($sSourcePath)) {
						$this->oSourceShare->oVfs->rmdir($sSourcePath);
					} else {
						$this->oSourceShare->oVfs->unlink($sSourcePath);
					}
				}
			}
			
			if ($bOk) {
				$oObject->sShare = $this->sDestinationShare;
				$oObject->sRelDir = $this->sRelDestinationPath;
				$oObject->sName = $sName;
				$oObject->triggerEvents('pxObject_editRename');
			} else {
				$aFailedObjects[] = $oObject->sName;
			}
		}

	/** Rename moved bookmarks */

		foreach ($aBookmarksToRename as $sFullPath => $aInfo)
		{
			if (!$pxp->oShare->oVfs->file_exists($sFullPath))
			{
				$sNewBookmarkDir = pxUtil::buildPath(
					$this->sRelDestinationPath,
					pxUtil::dirname($aInfo['sNewRelDir'])
				);

				$sNewBookmarkId = 'px' . md5($pxp->aConfig['sId'] . $this->sDestinationShare . $sNewBookmarkDir);
				$sNewBookmark = $this->sDestinationShare . '|' .  $sNewBookmarkDir;

				$oProfile = $pxp->getObject('phpXplorer://profiles/' . $aInfo['sUser'] . '.pxProfile', false, false);
				if (isset($oProfile)) {
					$oProfile->aBookmarks = array_diff($oProfile->aBookmarks, array($aInfo['sBookmark']));
					$oProfile->aBookmarks[] = $sNewBookmarkId;
					$oProfile->store();
				}

				$pxp->oVfs->unlink($pxp->sDir . '/bookmarks/' . $aInfo['sBookmark']);

				$pxp->oVfs->file_put_contents(
					$pxp->sDir . '/bookmarks/' . $sNewBookmarkId,
					$sNewBookmark
				);
			}
		}

		if (!empty($aFailedObjects)) {
			$pxp->raiseError('selectionAction', __FILE__, __LINE__, $aFailedObjects);	
		} else {
			return true;
		}
	}

	/**
	 * Cross filesystem copy function
	 */
	function _crossCopy($sSourcePath, $sDestPath)
	{
		$sData = $this->oSourceShare->oVfs->file_get_contents($sSourcePath);
		if ($sData !== false) {
  		if ($this->oDestinationShare->oVfs->file_put_contents(
  			$sDestPath,
  			$sData
  		)) {
  			
  			$oObject = $this->oSourceShare->oVfs->get_object($sSourcePath, true, false);
  			if (isset($oObject)) {
 				  $oObject->sShare = $this->sDestinationShare;
 				  $oObject->sRelDir = $this->sRelDestinationPath;
 				  $oObject->sName = basename($sDestPath);
  				$oObject->store();
  			} 			
  			
  			return true;
  		}
		}
		return false;
	}
	

	function createCheck(&$aObjects)
	{
		global $pxp;

		$aStack = array();

		$aNewNames = array();
		$this->getFileSelection($aNewNames, 'aNewNames', false);
		$iObjects = count($aObjects);
		$iNewNames = count($aNewNames);
		$bNewNames = $iNewNames == $iObjects;

		for ($i = 0; $i < $iObjects; $i++) {
			$oObject1 =& $aObjects[$i];
			$oObject1->_sNewRelDir = '/';
			$aStack[] = $oObject1;
			if ($bNewNames) {
				$sType = null;
				$sExtension = null;
				$pxp->getTypeKeyByExtension($aNewNames[$i], $oObject1->bDirectory, $sType, $sExtension);
				$oObject1->_sNewType = $sType;
				$oObject1->_sNewExtension = $sExtension;
				$oObject1->_sNewName = $aNewNames[$i];
			}
		}

		while ($oObject2 = array_pop($aStack))
		{
			$sDirCheck = pxUtil::buildPath(
				$this->sRelDestinationPath,
				$oObject2->_sNewRelDir
			);
			
			$sTypeCheck = isset($oObject2->_sNewType) ? $oObject2->_sNewType : $oObject2->sType;

			if (!$this->oDestinationShare->checkCreatePermission($sDirCheck, $sTypeCheck)) {
				$pxp->raiseError('notAllowedToCreateEveryFile', __FILE__, __LINE__, array($sDirCheck, $sTypeCheck));
			}

			if ($oObject2->bDirectory) {
				foreach ($oObject2->aObjects as $oSubObject) {
					$oSubObject->_sNewRelDir = pxUtil::buildPath(
						$oObject2->_sNewRelDir,
						isset($oObject2->_sNewName) ? $oObject2->_sNewName : $oObject2->sName 
					);
					$aStack[] = $oSubObject;
				}
			}
		}		
	}

}
?>