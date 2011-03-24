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

/**
 * @defaultActions pxObject_editProperties
 * @abstract 
 */
class pxMeta{
	/**
	 * @var string
	 */
	var $sExtension;

	/**
	 * @var boolean
	 * @view Checkbox
	 */
	var $bDirectory = false;

	/**
	 * Size of file in bytes. Null if object is a directory
	 * 
	 * @var integer
	 * @view Input
	 * @format number
	 */
	var $iBytes = 0;

	/**
	 * Last edit date and time 
	 *
	 * @var integer
	 * @view Datetime
	 */
	var $dModified = 0;

	/**
	 * Object owner (phpXplorer username)
	 * 
	 * @var string
	 * @edit Select
	 */
	var $sOwner = 'root';

	/**
	 * @var string
	 * @edit Select
	 */
	var $sLanguage;

	/**
	 * @var string
	 * @edit Input
	 */
	var $sTitle;

	/**
	 * Variable object description
	 * 
	 * @var string
	 * @edit Textarea
	 */
	var $sDescription;

	/**
	 * Tags / Keywords
	 *
	 * @var array
	 * @edit MultipleTranslated(namespace=tag)
	 */
	var $aTags = array();

	/**
	 * Operating system permissions
	 * 
	 * @var string
	 */
	var $sOSystemPermissions;
	
	/**
	 * Operating system file owner
	 * 
	 * @var string
	 */
	var $sOSystemOwner;

	/**
	 * Operating system file group
	 * 
	 * @var string
	 */
	var $sOSystemGroup;

	/**
	 * 
	 */
	function pxMeta()
	{
		global $pxp;

		$this->sLanguage = $pxp->sLanguage;

		// Owner has to be checked at all
		#$this->sOwner = $pxp->sUser;
	}
	
	/**
	 * Returns options of selection member types
	 *
	 * @param string $sMember Selection member ID
	 * 
	 * @return array Associative array with options
	 */
	function _getOptions($sMember, $bManualOptions = false)
	{
		global $pxp;

		switch($sMember)
		{
			case 'sOwner':
				if (empty($pxp->aConfig['sAuthentication'])) {
					return array('root' => 'root');
				} else {
					if (!isset($pxp->oAuthentication)) {
						$pxp->oAuthentication = $pxp->getObject($pxp->aConfig['sAuthentication'], false);
						$pxp->oAuthentication->connect();
					}

					$aUsers = array();
	
					foreach ($pxp->oAuthentication->getUsers() as $sUser => $sPassword) {
						$aUsers[$sUser] = $sUser;
					}
	
					return $aUsers;
				}
				break;
			case 'sLanguage':
				$pxp->loadLanguageCodes();
				$aLanguages = array();
				foreach ($pxp->aConfig['aContentLanguages'] as $sLanguage) {
					$aLanguages[$sLanguage] = $pxp->aLanguageCodes[$sLanguage];
				}
				return $aLanguages;
				break;
			case 'aTags':

				$aTags = array();

				// Add system default tags

				$oDir = &$pxp->getObject(
					'phpXplorer://shares/phpXplorer.pxShare',
					false,
					false
				);

				foreach ($oDir->aDefaultTags as $sTag) {
					$aTags[$sTag] = $sTag;
				}

				// Add share default tags
				if ($pxp->oObject->sShare != 'phpXplorer') {
					foreach ($pxp->aShares[$this->sShare]->aDefaultTags as $sTag) {
						$aTags[$sTag] = $sTag;
					}
				}

				// Add level default tags
				$sBaseDir = $pxp->aShares[$this->sShare]->sBaseDir;
				$sDir = pxUtil::buildPath($sBaseDir, $this->sRelDir);

				while ($sDir != $sBaseDir) {
					$oDir = $pxp->oShare->oVfs->get_object($sDir, false, false);
					if (isset($oDir) && $oDir->sType != 'pxShare') {
						foreach ($oDir->aDefaultTags as $sTag) {
							$aTags[$sTag] = $sTag;
						}
					}
					$sDir = pxUtil::dirname($sDir);
				}

				return $aTags;

				break;
			default:
				return parent::_getOptions($sMember, $bManualOptions);
				break;
		}
	}
	var $sType = 'pxMeta';}

?>