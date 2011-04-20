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

class pxGlobal_openIndexData extends pxAction
{
	/**
	 * @var array
	 * @access private
	 */
	var $_aBookmarks = array();

	/**
	 * @var string
	 * @access private
	 */
	var $_sDefaultBookmark;

	var $sMimeType = 'text/javascript';

	/**
	 * 
	 */
	function run()
	{
		global $pxp;

		$bFirst = true;
		
		#$sClient = $pxp->getRequestVar('sClient');

		foreach ($pxp->aBookmarks as $sBookmark)
		{
			if (strpos($sBookmark, 'px') === 0 && strlen($sBookmark) == 34)
			{
				// Bookmark
				$sContent = $pxp->oVfs->file_get_contents(
					$pxp->sDir . '/bookmarks/' . $sBookmark
				);

				$sShare = substr($sContent, 0, strpos($sContent, '|'));				
				$pxp->loadShare($sShare);
				$sRelDir = substr($sContent, strpos($sContent, '|') + 1);

				if (isset($pxp->aShares[$sShare])) {

					$oUserShare =& $pxp->aShares[$sShare]->oVfs->get_object(
						pxUtil::buildPath(
							pxUtil::buildPath(
								$pxp->aShares[$sShare]->sRealBaseDir,
								$sRelDir
							),
							'.phpXplorer/' . $pxp->sUser . '.pxUser'
						),
						false,
						false
					);


					if (!empty($oUserShare->sTitle)) {
						$this->_aBookmarks[$sBookmark] =
							$sShare . ': ' . $oUserShare->sTitle;
					} else {
						$this->_aBookmarks[$sBookmark] = $sShare . ': ' . basename($sRelDir);
					}

					if ($bFirst) {
						$this->_sDefaultBookmark = $sBookmark;
						$bFirst = false;
					}
				}

			}
			else
			{
				// Special bookmark __phpXplorer => all shares
				if ($sBookmark == '__phpXplorer')
				{
					$oQuery = new pxQuery();
					$oQuery->sDirectory = $pxp->sDir . '/shares';
					$oQuery->aTypes = array('pxShare');
					$aObjects = $pxp->oVfs->ls($oQuery);

					foreach ($aObjects as $oShare)
					{
						$sShare = $oShare->sId;

						if ($sShare == '_pxUserHomePrototype') {
							continue;
						}

						$oShare =& $pxp->oVfs->get_object(
							$pxp->sDir . '/shares/' . $sShare . '.pxShare',
							false
						);
						

						if (!empty($oShare->sTitle)) {
							$this->_aBookmarks[$sShare] = $oShare->sTitle;
						} else {
							$this->_aBookmarks[$sShare] = $sShare;
						}

						if ($sShare == $pxp->sShare or $bFirst) {
							$this->_sDefaultBookmark = $sShare;
							$bFirst = false;
						}
					}
				}
				else
				{
					// Normal share
					$oShare =& $pxp->oVfs->get_object(
						$pxp->sDir . '/shares/' . $sBookmark . '.pxShare',
						false,
						false
					);

					if (!empty($oShare)) {

						if (!empty($oShare->sTitle)) {
							$this->_aBookmarks[$sBookmark] = $oShare->sTitle;
						} else {
							$this->_aBookmarks[$sBookmark] = $sBookmark;
						}

						if ($oShare->sId == $pxp->sShare or $bFirst) {
							$this->_sDefaultBookmark = $oShare->sId;
							$bFirst = false;
						}
					}
					
				}
			}
		}

		if (count($this->_aBookmarks) < 1) {
			$pxp->raiseError('noBookmarks', __FILE__, __LINE__);
		}

		$oProfile = $pxp->getObject(
			'phpXplorer://profiles/' . $pxp->sUser . '.pxProfile',
			false,
			true
		);

		if (!empty($oProfile->sFullName)) {
			$sName = $oProfile->sFullName;
		} else {
			$sName = $pxp->sUser;
		}

		$bUpload = ini_get('file_uploads');

		$this->sendJson(
			array(
				'bDebug' => $pxp->aConfig['bDebug'],
				'bDevelopment' => $pxp->aConfig['bDevelopment'],
				'sTitle' => $pxp->aConfig['sTitle'],
				'sTitleLink' => $pxp->aConfig['sTitleLink'],
				'sSubTitle' => $pxp->aConfig['sSubTitle'],
				'bContact' => $pxp->aConfig['bContact'],
				'aBookmarks' => $this->_aBookmarks,
				'sUser' => $pxp->sUser,
				'sUserLanguage' => $pxp->sLanguage,
				'sSystemLanguage' => $pxp->aConfig['sSystemLanguage'],
				'sDefaultBookmark' => $pxp->sShare,
				'oProfile' => $oProfile,
				'bUpload' => !empty($bUpload),
				'aGdExtensions' => $pxp->aConfig['aGdExtensions'],
				'aImageMagickExtensions' => $pxp->aConfig['aImageMagickExtensions'],
				'sLogoUrl' => $pxp->aConfig['sLogoUrl']
			)
		);
	}
}

?>