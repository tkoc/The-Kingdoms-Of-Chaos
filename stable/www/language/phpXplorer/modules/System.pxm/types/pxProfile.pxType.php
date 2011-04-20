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

require_once dirname(__FILE__) . '/pxMetaFiles.pxType.php';

/**
 * phpXplorer user profile
 *
 * A profile stores address information, application state and phpXplorer bookmarks 
 *
 * @extensions pxProfile
 * @belongsTo /profiles
 * @expandSubtypes pxProfile
 * @edit
 */
class pxProfile extends pxMetaFiles
{
	/**
	 * @var string
	 * @edit Input
	 */
	var $sFullName;

	/**
	 * @var string
	 * @edit Input
	 * @validate email(required=true)
	 */
	var $sEmail;

	/**
	 * @var string
	 * @edit Select
	 */
	var $sFrontendLanguage = 'en';

	/**
	 * @var array
	 * @edit Array
	 * @permission pxAdministrator
	 */
	var $aBookmarks = array();

	/**
	 * @var integer
	 * @view Datetime
	 */
	var $iLastLogin;

	/**
	 * @var integer
	 * @view Datetime
	 */
	var $iLastLogout;

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
			case 'sFrontendLanguage':
				$aLanguages = array();
				$pxp->loadLanguageCodes();
				$oQuery = new pxQuery;
				$oQuery->sDirectory = $pxp->sModuleDir . '/System.pxm/translations';
				$oQuery->bPermissionCheck = false;				
				$aFiles = $pxp->oVfs->ls($oQuery);
				foreach ($aFiles as $oFile) {
					$sLanguage = substr($oFile->sName, 0, strpos($oFile->sName, '.'));
					if (strlen($sLanguage) == 2 or strlen($sLanguage) == 3) {
						$aLanguages[$sLanguage] = $pxp->aLanguageCodes[$sLanguage];
					}
				}
				return $aLanguages;
				break;
			default:
				return parent::_getOptions($sMember, $bManualOptions);
				break;
		}
	}

	/**
	 * Constructor
	 */
	function pxProfile()
	{
		global $pxp;
		
		parent::pxFiles();

		$this->sLanguage = $pxp->aConfig['sSystemLanguage'];
	}
}

?>