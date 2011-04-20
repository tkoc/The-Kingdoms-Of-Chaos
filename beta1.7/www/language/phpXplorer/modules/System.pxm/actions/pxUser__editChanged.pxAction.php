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
 * Create bookmark files and add bookmarks to user profile
 */
class pxUser__editChanged extends pxAction
{
	/**
	 *
	 */
	function run(&$oObject, $aParameters = null)
	{
		global $pxp;

		$oObject->_bCancelBubble = true;

		$sAction = $aParameters['sTriggeringAction'];

		if ($sAction != 'editProperties') {
			return true;
		}

		$sShare = $pxp->oShare->sId;
		$sBookmarkDir = pxUtil::dirname(pxUtil::dirname($pxp->sRelPathIn));
		$bInSystem = $sShare == 'phpXplorer';
		$bInShares = $bInSystem && $sBookmarkDir == '/shares';

		if (!$bInSystem && !$bInShares)
		{
			$sBookmarkId = 'px' . md5($pxp->aConfig['sId'] . $sShare . $sBookmarkDir);
			$sBookmarkFile = $pxp->sDir . '/bookmarks/' . $sBookmarkId;

			if (!$pxp->oVfs->file_exists($sBookmarkFile)) {
				$pxp->oVfs->file_put_contents(
					$sBookmarkFile,
					$sShare . '|' . $sBookmarkDir
				);
			}
		}

		// Add bookmark to user profile
		$sUserId = substr($pxp->oObject->sName, 0, strpos($pxp->oObject->sName, '.'));
		$sProfilePath = 'phpXplorer://profiles/' . $sUserId . '.pxProfile';
		$oProfile = $pxp->getObject($sProfilePath, false, false);

		if (!isset($oProfile)) {
			# check if user exists
			$pxp->loadType('pxProfile');
			$oProfile = new pxProfile();
			$oProfile->sOwner = $sUserId;
		}

		if ($bInSystem && !$bInShares) {
			$sProfileBookmark = '__phpXplorer';
		} else {
			if ($bInShares) {
				$sShareName = basename(pxUtil::dirname($pxp->sRelPathIn));
				$sShareId = str_replace('.pxShare', '', $sShareName);
				$sProfileBookmark = $sShareId;
			} else {
			// 	Add the bookmark ID of the current folder
				$sProfileBookmark = $sBookmarkId;
			}
		}

		if (!in_array($sProfileBookmark, $oProfile->aBookmarks)) {
			$oProfile->aBookmarks[] = $sProfileBookmark;
			$oProfile->store($sProfilePath);
		}
	}
}
?>