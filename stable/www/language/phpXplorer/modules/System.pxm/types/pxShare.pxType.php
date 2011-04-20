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

require_once dirname(__FILE__) . '/pxMetaDirectories.pxType.php';

/**
 * Handles access to the shared filesystems
 *
 * @extensions pxShare
 * @contains pxData pxUser pxSetting pxFacet
 * @belongsTo :shares
 * @expandSubtypes pxShare pxDirectories
 * @edit
 */
class pxShare extends pxMetaDirectories
{
	/**
	 * phpXplorer path to Vfs instance
	 *
	 * @var string
	 * @edit Select
	 */
	var $sVfs = 'phpXplorer://shares/local.pxVfsFilesystem';


	/**
	 * Base directory
	 *
	 * @var string
	 * @edit Input
	 */
	var $sBaseDir;
	var $sRealBaseDir;


	/**
	 * Base URL
	 *
	 * @var string
	 * @edit Input
	 */
	var $sUrl;


	/**
	 * Has to be set to false if your webserver is not able to handle utf-8 encoded URLs
	 * 
	 * @var boolean
	 * @edit Checkbox
	 */
	var $bUnicodeUrl = true;


	/**
	 * Show selection view
	 * 
	 * @var boolean
	 * @edit Checkbox
	 */
	var $bSelectionView = true;


	/**
	 * @var string
	 * @edit SelectTranslated(namespace=action)
	 */
	var $sDefaultSelection = 'pxDirectories_selectTree';

	
	/**
	 * @var string
	 * @edit SelectTranslated(namespace=action)
	 */
	var $sDefaultView = 'pxMetaDirectories_openDetails';


	/**
	 * Show files and directories in treeview
	 *
	 * @var boolean
	 * @edit Checkbox
	 */
	var $bFullTree = false;


	/**
	 * Limit the number of directory entries per page
	 * 
	 * @var integer
	 * @edit Input
	 * @validate number(min=0)
	 */
	var $iObjectsPerPage = 0;


	/**
	 * Limit the number of search results per page
	 *
	 * @var integer
	 * @edit Input
	 * @validate number(min=0)
	 */
	var $iSearchResultsPerPage = 25;


	/**
	 * Link to HTML page that loads on first access to a share in an inline frame
	 * 
	 * @var string
	 * @edit Input
	 */
	var $sStartpage;
	
	
	/**
	 * Width of the treeview frame
	 * 
	 * @var string
	 * @edit Input
	 * @validate number
	 */
	var $sTreeviewWidth = '240';


	/**
	 * This property is only useful if you use phpXplorer authentication.
	 * With this property you are able to control the creation of .htaccess files in the shared directory
	 * 
	 * 0: no
	 * 1: Valid users
	 * 2: Only user root
	 * 
	 * @var integer
	 * @edit SelectTranslated
	 */
	var $iRestrictWebserverAccess = 0;


	/**
	 * Max length or height of preview images in pixel
	 * 
	 * @var integer
	 * @edit Input
	 */
	var $iThumbnailSize = 100;


	/**
	 * Library for image manipulation
	 * 
	 * 0: GD Library
	 * 1: ImageMagick
	 * 
	 * @var integer
	 * @edit SelectTranslated
	 */
	var $iImageLibrary = 0;


	/**
	 * Quality of JPEG preview images
	 * 
	 * @var integer
	 * @edit Input
	 * @validate number
	 */
	var $iThumbnailQuality = 90;
	
	
	/**
	 * @var boolean
	 * @edit Checkbox
	 */
	var $bUnsecuredImageAccess = false;


	/**
	 * @var boolean
	 * @edit Checkbox
	 */
	var $bLiveSearch = false;	

	
	/**
	 * Image resize mode
	 *
	 * 0: Server
	 * 1: Client
	 *
	 * @var integer
	 * @edit Select
	 */
	var $iImageResize = 0;

	
	/**
	 * @var string
	 * @edit Select
	 */
	var $sBaseType = 'pxMetaDirectories';


	/**
	 * All datasource users are allowed to access phpXplorer
	 * and all its shares as owners of the listed roles.
	 * 
	 * If you make use of this feature phpXplorer will not check for
	 * user objects along the system hierarchy.
	 *
	 * @var array
	 * @edit MultipleTranslated(namespace=role)
	 */
	var $aAllUsersRoles = array();


//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

	
	var $bBookmark = false;

	/**
	 * Array to cache getSettings results
	 */
	var $_aSettings;
	
	/**
	 * Virtual filesystem object
	 */
	var $oVfs;

	/**
	 * @access private
	 */
	var $_bInit = false;
	
	var $sRealId;
	var $iRealDirOffset = 0;

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

	/**
	 * Returns options of selection member types
	 *
	 * @param string $sMember Selection member ID
	 * @return array Associative array with options
	 */
	function _getOptions($sMember, $bManualOptions = false)
	{
		global $pxp;

		switch($sMember) {
			case 'sVfs':
				$oQuery = new pxQuery;
				$oQuery->sDirectory = pxUtil::buildPath($pxp->sDir, 'shares');
				$oQuery->aTypes = array('pxVfs');
				$aObjects = $pxp->oVfs->ls($oQuery);
				$aResult = array();
				foreach ($aObjects as $oObject) {
					$aResult['phpXplorer://shares/' . $oObject->sName] = $oObject->sName;
				}
				return $aResult;
				break;
			case 'iRestrictWebserverAccess':
				return array(
	 				0 => 'no',
	 				1 => 'validUsersRedirect',
	 				2 => 'validUsers',
	 				3 => 'rootOnly'
				);
				break;
			case 'iImageLibrary':
				return array(
					0 => 'gdLib',
					1 => 'imageMagick',
					2 => 'none'
				);
				break;
			case 'iImageResize':
				return array(
					0 => 'Server',
					1 => 'Client'
				);
			break;
			case 'sBaseType':
				$pxp->loadTranslation();
				$aResult = array();
				foreach ($pxp->aTypes as $oType) {
					if ($oType->bDirectory) {
						$aResult[$oType->sId] = $pxp->aTranslation['type.' . $oType->sId] . ' (' . $oType->sId . ')';  
					}
				}
				return $aResult;
				break;
			case 'aAllUsersRoles':
				$aRoles = array();
				foreach ($pxp->aRoles as $sKey => $oRole) {
					if ($sKey != 'pxAuthenticated') { # $sKey != 'pxEveryone' && 
						$aRoles[$sKey] = $sKey;
					}
				}
				return $aRoles;
				break;
			case 'sDefaultSelection':
				$aActions = array();
				foreach ($pxp->aActions as $sAction => $aActionInfo) {
					if ($aActionInfo[2] == 'select') {
						$aActions[$sAction] = $sAction;
					}
				}
				return $aActions;
				break;
			case 'sDefaultView':
				$aActions = array();
				foreach ($pxp->aActions as $sAction => $aActionInfo)
				{
					if (strpos($sAction, '__') !== false) {
						continue;
					}
					$sType = substr($sAction, 0, strpos($sAction, '_'));
					if (
						isset($pxp->aTypes[$sType]) &&
						(
							$sType == 'pxDirectories' ||
							in_array('pxDirectories', $pxp->aTypes[$sType]->aSupertypes)
						)
					) {
						if ($aActionInfo[2] == 'open' || $aActionInfo[2] == 'batch') {
							$aActions[$sAction] = $sAction;
						}
					}
				}
				return $aActions;
				break;
			default:
				return parent::_getOptions($sMember, $bManualOptions);
				break;
		}
	}

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

	/**
	 * 
	 */
	function store($sPathIn = null) {
		$bResult = parent::store($sPathIn);
		$this->_handleWebserverRestriction();
		return $bResult;
	}

/*~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/

	/**
	 * 
	 */
	function init($sBookmarkId = null, $sBookmarkDir = null)
	{
		global $pxp;

		if ($this->_bInit) {
			return true;
		}

		#if ($this->sBaseDir == '') {
		#	$pxp->raiseError('No directory specified', __FILE__, __LINE__);
		#}

		$this->sRealId = $this->sId;
		if (isset($sBookmarkId)) {
			$this->sId = $sBookmarkId;
			$this->bBookmark = true;
		}

		$this->sRealBaseDir = $this->sBaseDir;
		if (isset($sBookmarkDir)) {
			$this->sBaseDir = pxUtil::buildPath(
				$this->sBaseDir,
				$sBookmarkDir
			);
			$this->iRealDirOffset = strlen($this->sBaseDir) - strlen($this->sRealBaseDir);
		}

		if (empty($this->sTreeviewWidth)) {
			$this->sTreeviewWidth = '24%';
		}

		if (empty($this->sUrl)) {
			$this->sUrl = $this->sBaseDir;
		} else {
			$this->sUrl = pxUtil::buildPath(
				$this->sUrl,
				$sBookmarkDir
			);
		}

		$this->oVfs =& $pxp->getObject($this->sVfs, false);
		$this->oVfs->oShare =& $this;

		$this->sUrl = str_replace('{@root}', $pxp->sUrl, $this->sUrl);
		$this->sBaseDir = str_replace('{@root}', $pxp->sDir, $this->sBaseDir);

		#echo $this->sBaseDir;

		$this->oVfs->oShare =& $this;
		$this->oVfs->init();

		$this->_bInit = true;

		return true;
	}

	/**
	 * 
	 */
	function checkTypePermission($sRelDirIn, $sType, $sOwner)
	{
		global $pxp;

		$oSettings =& $this->getSettings($sRelDirIn, $sOwner);
		
		#echo "---$sRelDirIn-" . $sOwner . "-\n";

		if (!isset($oSettings->aAllowedActions[$sType])) {
			$oType =& $pxp->aTypes[$sType];
			for ($a = 0, $m1 = count($oType->aActions); $a < $m1; $a++) {
				if ($this->checkActionPermission($sRelDirIn, $sType, $oType->aActions[$a], $sOwner)) {
					#echo $sType . '-' . $oType->aActions[$a] . "\n";
					$oSettings->aAllowedActions[$sType][] = $oType->aActions[$a];
				}
			}
		}

		return isset($oSettings->aAllowedActions[$sType]);
	}

	/**
	 *
	 */
	function getRelativePath($sPathIn) {
		$sPath = pxUtil::str_replace_once($this->sBaseDir, '', $sPathIn);
		if (empty($sPath)) return '/';
		if (substr($sPath, 0, 1) != '/') return '/' . $sPath;
		return $sPath;
	}
	
	/**
	 * 
	 */
	function getRealRelativePath($sPathIn) {
		$sPath = pxUtil::str_replace_once($this->sRealBaseDir, '', $sPathIn);
		if (empty($sPath)) return '/';
		if (substr($sPath, 0, 1) != '/') return '/' . $sPath;
		return $sPath;
	}

	/**
	 * 
	 */
	function &getSettings($sRelDirIn, $sOwner)
	{
		global $pxp;

		#echo "-" . $sRelDirIn . ":" . $sOwner . "-<br/>";

		if (!isset($sOwner)) {
			#$sOwner = $pxp->sUser;
			$sOwner = 'root';
		}

		if (empty($sRelDirIn)) {
			$sRelDir = '/';
		} else {
			$sRelDir = $sRelDirIn;
		}

		$bOwner = $this->oVfs->bOwnerPermission && $sOwner == $pxp->sUser;

		if (!$bOwner) {
			#echo $sRelDirIn . '  -  '. $sOwner . " - " . $pxp->sUser;
		}

		#$bOwner = true;

		$sCacheKey = $sRelDir . ($bOwner ? '_owner' : '');

		// Return cached results
		if (isset($this->_aSettings[$sCacheKey])) {
			return $this->_aSettings[$sCacheKey];
		}

		// Fill level array with paths to system, share and share hierarchy paths 

		$aLevels = array();
		$bUserExists = false;
		$mCacheBase = false;

		if (isset($this->_aSettings['/'])) {
			$mCacheBase = '/';
		} else {

			$aLevels[] = $pxp->sDir;
			$aLevels[] = $pxp->sDir . '/shares/' . $this->sId . '.pxShare';

			if ($this->oVfs->iEvalShareTree > 0) {				
				$aLevels[] = pxUtil::buildPath($this->sBaseDir, '.phpXplorer');
			}
		}

		if ($this->oVfs->iEvalShareTree > 0 && $sRelDir != '/') {
			$_aParts = explode('/', $sRelDir);
			$_sBasePath = '';
			for ($p = 1, $m0 = count($_aParts); $p < $m0; $p++) {
				$_sBasePath .= '/' . $_aParts[$p];
				$aLevels[] = $this->sBaseDir . $_sBasePath . '/.phpXplorer';
				if (isset($this->_aSettings[$_sBasePath])) {
					$mCacheBase = $_sBasePath;
					$aLevels = array();
				}
			}
		}

		#print_r($aLevels);

		if ($mCacheBase !== false) {
			$bUserExists = true;
			$this->_aSettings[$sCacheKey] = $this->_aSettings[$mCacheBase];
		} else {
			$this->_aSettings[$sCacheKey] =& new pxSetting;
		}

		$oSet =& $this->_aSettings[$sCacheKey];


		// All datasource users are allowed to access phpXplorer.
		if (!empty($this->aAllUsersRoles)) { # && $pxp->sUser != 'everyone' ?
			if ($pxp->sUser == 'root') {
				$oSet->aRoles[] = 'pxAdministrator';
			} else {
				$oSet->aRoles = array_merge($oSet->aRoles, $this->aAllUsersRoles);
			}
			$bUserExists = true;
		}

		if (isset($pxp->_SESSION['pxp_bAuth']) && $pxp->_SESSION['pxp_bAuth'] == true) {
			$oSet->aRoles[] = 'pxAuthenticated';
		}

		/**
		 * Collect settings for each level in $this->_aSettings
		 */

		$aSettings = array();
		//$aOwnerSettings = array();

		for ($a = 0, $m1 = count($aLevels); $a < $m1; $a++)
		{
			$sLevelBase = $aLevels[$a] . '/.phpXplorer/.objects/';

			if ($a < 2) {
				$oVfs =& $pxp->aShares['phpXplorer']->oVfs;
			} else {
				$oVfs =& $this->oVfs;
			}

			if (empty($pxp->_SESSION['pxp_aAllUsersRoles']))
			{
				// Is there a user file on the current level ?
				$sPath = $aLevels[$a] . '/' . $pxp->sUser . '.pxUser';

				if ($oVfs->is_file($sPath)) {
					$oUser =& $oVfs->get_object($sPath, false, false);
					if (isset($oUser)) {
						$bUserExists = true;
						$oSet->aRoles = array_merge($oSet->aRoles, $oUser->aRoles);
					}
				} else {
					if (!$bUserExists) {
						$sPath = $aLevels[$a] . '/everyone.pxUser';
						if ($oVfs->is_file($sPath)) {
							$bUserExists = true;
						}
					}
				}

				if ($bOwner)
				{
					$sPath = $aLevels[$a] . '/owner.pxUser';

					if ($oVfs->is_file($sPath)) {
						$oUser =& $oVfs->get_object($sPath, false, false);
						if (isset($oUser)) {
							$bUserExists = true;
							$oSet->aRoles = array_merge($oSet->aRoles, $oUser->aRoles);
						}
					}
				}
			}

			if ($a > 1 && $this->oVfs->iEvalShareTree < 2) {
				continue;
			}

			// Include user settings
			if ($pxp->sUser != 'everyone') {
				$sPath = $aLevels[$a] . '/' . $pxp->sUser . '.pxSetting';
				if ($oVfs->is_file($sPath)) {
					$oSetting =& $oVfs->get_object($sPath, false, false);
					if (isset($oSetting)) {
						$aSettings[] = $oSetting;
					}
				}
			}

			// Include role settings
			for ($r = 0, $m2 = count($oSet->aRoles); $r < $m2; $r++) {
				$sPath = $aLevels[$a] . '/' . $oSet->aRoles[$r] . '.pxSetting';
				if ($oVfs->is_file($sPath)) {
					$oSetting =& $oVfs->get_object($sPath, false, false);
					if (isset($oSetting)) {
						$aSettings[] = $oSetting;
					}
				}
			}
		}

		if (!$bUserExists) {
			if ($pxp->sUser == 'everyone' && $pxp->aConfig['_bUiMode']) {
				#unset($pxp->_SESSION['pxp_sUser']);
				$pxp->loadAuthentication();
				$pxp->oAuthentication->showLogin();
			} else {
				$pxp->raiseError('accessDenied', __FILE__, __LINE__, array($pxp->_SERVER['QUERY_STRING']));
			}
		}

#print_r($oSet->aRoles);

		// Add module roles
		for ($r = 0, $m3 = count($oSet->aRoles); $r < $m3; $r++) {
			if (isset($pxp->aRoles[$oSet->aRoles[$r]])) {
				$aSettings[] =& $pxp->aRoles[$oSet->aRoles[$r]];
			}
		}

		// Add all settings to $this->_aSettings
		for ($r = 0, $m4 = count($aSettings); $r < $m4; $r++) {
			$oSet->aCompiledEvents = array_merge($oSet->aCompiledEvents, $aSettings[$r]->aCompiledEvents);
			$oSet->aPermissions = array_merge($oSet->aPermissions, $aSettings[$r]->aPermissions);
			$oSet->aParameters = array_merge($oSet->aParameters, $aSettings[$r]->aParameters);
		}

		#if ($sRelDir == '/') {
		#	$pxp->_SESSION[$this->sId] = $this->_aSettings['/'];
		#}
		
		#print_r($this->_aSettings[$sCacheKey]);

		return $this->_aSettings[$sCacheKey];
	}

	/**
	 * Check if user is allowed to perform $sAction on $sPath level
	 */
	function checkActionPermission($sRelDirIn, $sType, $sAction, $sOwner)
	{
		global $pxp;

		#$sBaseAction = pxSystem::get BaseAction($sAction);
		
		if (isset($pxp->aActions[$sAction][2])) {
			$sBaseAction = $pxp->aActions[$sAction][2];
		} else {
			$sBaseAction = pxUtil::getBaseAction($sAction);
		}

		// Nobody should edit the base directory object itself
		if (empty($sRelDirIn)) {
			if ($sBaseAction == 'pxObject_editDelete' || $sBaseAction == 'pxObject_editProperties') {
				return false;
			}
		}

		$oSettings =& $pxp->oShare->getSettings($sRelDirIn, $sOwner);

		#print_r($oSettings);

		if (
			isset($oSettings->aPermissions[$sType])
			or
			isset($oSettings->aPermissions[$sType . '.' . $sBaseAction])
			or
			isset($oSettings->aPermissions[$sType . '.' . $sBaseAction . '.' . $sAction])
		) {
			return true;
		}
		else
		{
			$oType =& $pxp->aTypes[$sType];

			for ($s = 0, $m1 = count($oType->aSupertypes); $s < $m1; $s++)
			{				
				$sSupertype = $oType->aSupertypes[$s];

				if (
					isset($oSettings->aPermissions[$sSupertype])
					or
					isset($oSettings->aPermissions[$sSupertype . '.' . $sBaseAction])
					or
					isset($oSettings->aPermissions[$sSupertype . '.' . $sBaseAction . '.' . $sAction])
				) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * 
	 */
	function checkCreatePermission($sPath, $sNewType)
	{
		global $pxp;

		$oNewType = $pxp->aTypes[$sNewType];

		if (!$this->checkActionPermission($sPath, $oNewType->sId, 'pxObject__editCreate', $pxp->sUser)) {		
			return false;
		}

	/** Check if new type is allowed to be created in the upper directory **/ 

		$sContainertype = '';
		$sExtension = '';
		$pxp->getTypeKeyByExtension($sPath, true, $sContainertype, $sExtension);

		$oContainerType =& $pxp->aTypes[$sContainertype];

		if (!empty($oContainerType->aContenttypes))
		{
			$aValidTypes = array();

			foreach ($oContainerType->aContenttypes as $sContenttype) {
				if (isset($pxp->aTypes[$sContenttype])) {
					$aValidTypes[] = $sContenttype;
					$aValidTypes = array_merge($aValidTypes, $pxp->aTypes[$sContenttype]->aAllSubtypes);
				}
			}

			if (!in_array($oNewType->sId, $aValidTypes)) {
				return false;
			}
		}		

	/** Check if new type could be be created in the upper directory **/

		if (!empty($oNewType->aContainertypes)) {

			$bIn = false;

			foreach ($oNewType->aContainertypes as $sContainertype)
			{
				// Check if path pattern is in current directory
				if (strpos($sContainertype, '/') !== false) {
					if (strpos($sPath, $sContainertype) !== false) {
						$bIn = true;
						break;
					}
				}

				if (substr($sContainertype, -1) == ':') {
					if (
						substr($sContainertype, 0, strlen($sContainertype) -1) == $this->sId
						and
						$pxp->sRelDir == '/'
					) {
						$bIn = true;
						break;
					}
				}

				if (substr($sContainertype, 0, 1) == ':') {
					if (basename($sPath) == substr($sContainertype, 1)) {
						$bIn = true;
						break;							
					}
				} else {
					
					$sType = null;
					$sExtension = null;
					$pxp->getTypeKeyByExtension(
						$sPath,
						true,
						$sType,
						$sExtension
					);

					if ($sType == $sContainertype) {
						$bIn = true;
						break;
					}
				}
				
			}
			if (!$bIn) {
				return false;
			}
		}
		return true;
	}

	/**
	 * 
	 */
	function _handleWebserverRestriction()
	{
		global $pxp;
		
		if (!empty($pxp->aConfig['sAuthentication'])) {
			if (!isset($pxp->oAuthentication)) {
				$pxp->oAuthentication = $pxp->getObject($pxp->aConfig['sAuthentication']);
				$pxp->oAuthentication->connect();
			}
		}

		$pxp->loadShare($this->sId);
		$sHtpasswdPath = pxUtil::buildPath($this->sBaseDir, '.htpasswd');
		$sHtaccessPath = pxUtil::buildPath($this->sBaseDir, '.htaccess');
		$sCacheHtpasswdPath = $pxp->sCacheDir . '/images/' . $this->sId . '/.htpasswd';
		$sCacheHtaccessPath = $pxp->sCacheDir . '/images/' . $this->sId . '/.htaccess';

		switch ($this->iRestrictWebserverAccess) {
		case 0:
			/**
			 * Access not restricted
			 */
			if (isset($pxp->aShares[$this->sId])) {
				if ($pxp->aShares[$this->sId]->oVfs->is_file($sHtaccessPath)) {
					$sFile = $pxp->aShares[$this->sId]->oVfs->file_get_contents($sHtaccessPath);
					if (strpos($sFile, 'Generated by phpXplorer') !== false) {
						$pxp->aShares[$this->sId]->oVfs->unlink($sHtpasswdPath);
						$pxp->aShares[$this->sId]->oVfs->unlink($sHtaccessPath);
						$pxp->oVfs->unlink($sCacheHtpasswdPath);
						$pxp->oVfs->unlink($sCacheHtaccessPath);
					}
				}
			}
			return true;
			break;
		case 1:
		case 2:
			/**
			 * Only valid users should be able to access files directly by URL.
			 * Create .htpasswd file
			 */

			$aUsers = array();
			$sFile = '';
			
			$oQuery =& new pxQuery;
			$oQuery->sDirectory = $pxp->sDir;
			$oQuery->aTypes = array('pxUser');

			foreach ($pxp->oVfs->ls($oQuery) as $oObject) {
				$aUsers[] = substr($oObject->sName, 0, strpos($oObject->sName, '.'));
			}

			$oQuery->sDirectory = $pxp->sDir . '/shares/' . $this->sId . '.pxShare';

			foreach ($pxp->oVfs->ls($oQuery) as $oObject) {
				$aUsers[] = substr($oObject->sName, 0, strpos($oObject->sName, '.'));
			}

			foreach($aUsers as $sUser) {
				$sFile .= $sUser . ':' . $pxp->oAuthentication->getPassword($sUser) . "\r\n";
			}

			$pxp->aShares[$this->sId]->oVfs->file_put_contents(
				$sHtpasswdPath,
				$sFile
			);

			break;
		case 3:

			/**
			 * Only user root is allowed to access files directly by URL.
			 * Create .htpasswd file
			 */

			$sFile = 'root:' . $pxp->oAuthentication->getPassword('root');

			$pxp->aShares[$this->sId]->oVfs->file_put_contents(
				$sHtpasswdPath,
				$sFile
			);
			break;
		}

		/**
		 * Create .htaccess file
		 */
		$pxp->aShares[$this->sId]->oVfs->file_put_contents(
			$sHtaccessPath,
			'# Generated by phpXplorer' . "\r\n" .
			'AuthType Basic' . "\r\n" .
			'AuthName "phpXplorer@' . $pxp->_SERVER['HTTP_HOST'] . '"' . "\r\n" .
			'AuthUserFile "' . $sHtpasswdPath . '"' . "\r\n" .
			'Require valid-user'
		);
		
		
		if ($this->iRestrictWebserverAccess > 0) {

			// protect image cache directory too

			if (!$pxp->oVfs->is_dir($pxp->sCacheDir . '/images/' . $this->sId)) {
				$pxp->oVfs->mkdir($pxp->sCacheDir . '/images/' . $this->sId);
			}

			$pxp->oVfs->file_put_contents($sCacheHtpasswdPath, $sFile);

			$pxp->oVfs->file_put_contents(
				$sCacheHtaccessPath,
				'# Generated by phpXplorer' . "\r\n" .
				'AuthType Basic' . "\r\n" .
				'AuthName "phpXplorer@' . $pxp->_SERVER['HTTP_HOST'] . '"' . "\r\n" .
				'AuthUserFile "' . $sHtpasswdPath . '"' . "\r\n" .
				'Require valid-user'
			);
		}
	}
	
	/**
	 * Return the name of the shared folder or the share id
	 * depending on the request parameter
	 * 
	 * @return string Share label
	 */
	function getLabel()
	{
		global $pxp;

		if (strpos($pxp->sShare, 'px') === 0 and strlen($pxp->sShare) == 34) {
			// Bookmark
			$sContent = $pxp->oVfs->file_get_contents(
				$pxp->sDir . '/bookmarks/' . $pxp->sShare
			);
			$sRelDir = substr($sContent, strpos($sContent, '|') + 1);
			return basename($sRelDir);
		} else {
			// Share
			return $pxp->sShare;
		}
	}
	
	/**
	 * 
	 */
	function getAllowedTypes($sRelDirIn, $sOwner)
	{
		global $pxp;
		
		$oSettings =& $this->getSettings($sRelDirIn, $sOwner);

		$aTypes = array();
		foreach ($oSettings->aPermissions as $sPermission => $iSet) {
			$iPos = strpos($sPermission, '.');
			if ($iPos !== false) {
				$sType = substr($sPermission, 0, $iPos);
				$aTypes[] = $sType; 
			} else {
				$aTypes[] = $sPermission;
			}
		}
		$aTypes = array_unique($aTypes);

		return $aTypes;
	}

}

?>