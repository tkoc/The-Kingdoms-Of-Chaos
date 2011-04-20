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

class pxDirectories__editDeleteSelection extends pxAction
{
	function run()
	{
		global $pxp;

		$this->bImplementsEventHandling = true;

		if (!$pxp->oShare->oVfs->is_dir($pxp->sFullPathIn)) {
			$pxp->raiseError('invalidParameter', __FILE__, __LINE__);
		}

		$oQuery = new pxQuery();
		$oQuery->sDirectory = $pxp->sFullPathIn;
		$oQuery->bRecursive = true;
		$oQuery->bPermissionCheck = false;
		$this->getFileSelection($oQuery->aNames);
		$aObjects = $pxp->oShare->oVfs->ls($oQuery);

		$aBookmarksToDelete = array();
		$this->deleteCheck($aObjects, $aBookmarksToDelete);
		
		$aFailedObjects = array();

		foreach ($aObjects as $oObject) {

			$sDir = $pxp->sFullPathIn;

			$sPath = pxUtil::buildPath(
				$sDir,
				$oObject->sName
			);

			if (
				strpos(realpath($sPath), 'D:\htdocs\phpXplorer\test') === false
				&& $pxp->sShare != 'ftp' && $pxp->sShare != 'Authentication' && $pxp->sShare != 'Demo' && $pxp->sShare != 'fontXplorer'
			) {
				die('STOP: ' . $sPath);
			}

			if ($oObject->bDirectory) {

				if (!$pxp->oShare->oVfs->rmdir($sPath)) {					
					$aFailedObjects[] = $oObject->sName;
					continue;
				}
			} else {
				if (!$pxp->oShare->oVfs->unlink($sPath)) {
					$aFailedObjects[] = $oObject->sName;
					continue;
				}
			}

			$oObject->triggerEvents('pxObject_editDelete');
		}

		foreach ($aBookmarksToDelete as $sFullPath => $aInfo) {
			if (!$pxp->oShare->oVfs->file_exists($sFullPath)) {
				$oProfile = $pxp->getObject('phpXplorer://profiles/' . $aInfo['sUser'] . '.pxProfile', false, false);
				if (isset($oProfile)) {
					$oProfile->aBookmarks = array_diff($oProfile->aBookmarks, array($aInfo['sBookmark']));
					$oProfile->store();
				}
				$pxp->oVfs->unlink($pxp->sDir . '/bookmarks/' . $aInfo['sBookmark']);
			}
		}

		if (!empty($aFailedObjects)) {
			
			#print_r($aFailedObjects);

			$pxp->raiseError('selectionAction', __FILE__, __LINE__, $aFailedObjects);	
		}
	}
}
?>