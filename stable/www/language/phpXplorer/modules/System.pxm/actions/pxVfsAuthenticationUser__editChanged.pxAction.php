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
 * Rename/delete user profile and user shares
 */
class pxVfsAuthenticationUser__editChanged extends pxAction
{
	/**
	 *
	 */
	function run(&$oObject, $aParameters = null)
	{
		global $pxp;

		$oObject->_bCancelBubble = true;

		$sAction = $aParameters['sTriggeringAction'];

		$sShare = $pxp->oShare->sId;
		
		$aNames = array();
		$this->getFileSelection($aNames, 'aNames', false);
		
		$aNewNames = array();
		$this->getFileSelection($aNewNames, 'aNewNames', false);

		switch ($sAction)
		{
			case 'editRename':

				$sMode = $pxp->getRequestVar('sMode');
				if ($sMode == 'move') {

					for ($i=0,$l=count($aNames); $i<$l; $i++) {
					{
						$sName = $aNames[$i];
						$sNewName = $aNewNames[$i];

						$oProfile = $pxp->getObject(
							'phpXplorer://profiles/' . $sName . '.pxProfile',
							false,
							false
						);

						if (isset($oProfile)) {
							
							$sDir = pxUtil::buildPath($pxp->sDir, 'profiles');
							$pxp->oVfs->rename(
								$sDir . '/' . $sName . '.pxProfile',
								$sDir . '/' . $sNewName . '.pxProfile'
							);

							foreach ($oProfile->aBookmarks as $sBookmark)
							{
								if (strpos($sBookmark, 'px') === 0 && strlen($sBookmark) == 34)
								{
									$sContent = $pxp->oVfs->file_get_contents($pxp->sDir . '/bookmarks/' . $sBookmark);
									$aParts = explode('|', $sContent);
									$sShare = $aParts[0];
									$pxp->loadShare($sShare);
									
									$sDir = pxUtil::buildPath($pxp->aShares[$sShare]->sBaseDir, $aParts[1]);
									$sDir = pxUtil::buildPath($sDir, '.phpXplorer');
									
									$pxp->aShares[$sShare]->oVfs->rename(
										$sDir . '/' . $sName . '.pxUser',
										$sDir . '/' . $sNewName . '.pxUser'
									);
								}
							}
						}
					}
				}
			}

			break;

			case 'editDelete':

				for ($i=0,$l=count($aNames); $i<$l; $i++)
				{				
					$sName = $aNames[$i];

					$oProfile = $pxp->getObject(
						'phpXplorer://profiles/' . $sName . '.pxProfile',
						false,
						false
					);

					if (isset($oProfile)) {

						$sDir = pxUtil::buildPath($pxp->sDir, 'profiles');

						$pxp->oVfs->unlink($sDir . '/' . $sName . '.pxProfile');

						foreach ($oProfile->aBookmarks as $sBookmark)
						{
							if (strpos($sBookmark, 'px') === 0 && strlen($sBookmark) == 34)
							{
								$sContent = $pxp->oVfs->file_get_contents($pxp->sDir . '/bookmarks/' . $sBookmark);
								$aParts = explode('|', $sContent);
								$sShare = $aParts[0];
								$pxp->loadShare($sShare);

								$sDir = pxUtil::buildPath($pxp->aShares[$sShare]->sBaseDir, $aParts[1]);
								$sDir = pxUtil::buildPath($sDir, '.phpXplorer');

								$pxp->aShares[$sShare]->oVfs->unlink($sDir . '/' . $sName . '.pxUser');
							}
						}
					}
				}
			break;
		}

	}
}
?>