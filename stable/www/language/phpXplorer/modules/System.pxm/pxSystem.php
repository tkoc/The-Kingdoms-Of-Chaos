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

require_once dirname(__FILE__) . '/pxUtil.php';
require_once dirname(__FILE__) . '/pxQuery.php';

class pxSystem
{
	/**
	 * Do not change these default values.
	 * Changes have to be done in .../phpXplorer/config.php
	 */
	var $aConfig = array(
		'sId' => '*',
		'sTitle' => 'webXplorer',
		'sTitleLink' => 'http://www.phpxplorer.org',
		'sSubTitle' => '~subTitle',
		'sLogoUrl' => './modules/Customization.pxm/graphics/logo.png',
		'bContact' => true,
		'sVersion' => '*',
		'bCreateHomes' => false,
		'bDebug' => true,
		'bDevelopment' => true,
		'sSystemLanguage' => 'en',
		'sEncoding' => 'iso-8859-1',
		'sAuthentication' => 'phpXplorer://default.pxAuthenticationHtpasswd',
		'sNoAuthUser' => 'root',
		'sUMask' => '0000',
		'sMkDirMode' => 0755,
		'sDefaultBookmark' => 'test',
		'aImageMagickExtensions' => array('jpg', 'jpeg', 'gif', 'tif', 'tiff', 'png', 'bmp', 'psd', 'svg', 'pdf'),
		'aGdExtensions' => array('jpg', 'jpeg', 'gif', 'png'),
		'aContentLanguages' => array('en', 'de'),
		'aModuleOrder' => array('System'),
		'bAlternativeSessionHandler' => false,
		'_aEssentialTypes' => array('pxObject', 'pxMetaFiles', 'pxDirectories', 'pxMetaDirectories', 'pxType', 'pxShare', 'pxVfs', 'pxVfsFilesystem', 'pxSetting', 'pxRole', 'pxAction', 'pxPhpClass', 'pxPhp', 'pxScript', 'pxTextFiles', 'pxFiles'),
		'_aAllUsersRolesShares' => array(), // automatically filled when compiling
		'_bUiMode' => false,
		'bCleanObjects' => true
	);

	var $sDir = '.';
	var $sUrl = '.';
	var $sCacheDir;

	var $sLanguage = 'en';

	var $sUser;
	#var $sClient;

	var $sCallId;

	var $oAuthentication;

	var $bAllowSelection = false;

	var $sRelPathIn;
	var $sFullPathIn;
	var $sFullPathInDir;
	var $sRelDir;
	
	// Object called by sPath parameter
	var $oObject;

	/**
	 * Action parameter value
	 */
	var $sAction;

	/**
	 * Action file ID {$sType}_{$sAction}
	 */
	var $sFullAction;

	/**
	 * Stores module IDs per action [actionId] => moduleId
	 */
	var $aActions = array();

	// Array to cache action objects
	var $aActionObjects = array();

	/**
	 * Stores all loaded pxType objects
	 */
	var $aTypes;

	var $aRoles;
	var $aFacets;

	var $sShare;
	var $aShares = array();
	
	var $aBookmarks = array();
	
	/**
	 * Shortcut to current share
   */
	var $oShare;

	/**
	 * Shortcut to phpXplorer share´s Vfs object
	 */
	var $oVfs;

	var $iStartTime;

	var $aLanguages = array();
	var $aTranslation = array();
	var $aLanguageCodes = array();

	var $sModuleDir;
	var $sModuleUrl;

	var $_aExtensionToType = array();

	var $_SERVER;
	var $_POST;
	var $_GET;
	var $_COOKIE;
	var $_FILES;
	var $_SESSION;

	function pxSystem($bSessionExists = false)
	{
		global $HTTP_SERVER_VARS, $HTTP_POST_VARS, $HTTP_GET_VARS, $HTTP_COOKIE_VARS, $HTTP_POST_FILES, $HTTP_SESSION_VARS;

		$this->iStartTime = pxUtil::getMicrotime();

		if ($this->aConfig['bAlternativeSessionHandler']) {
			require_once dirname(__FILE__) . '/pxSessionHandler.php';
		}

		if (!$bSessionExists) {
			session_start();
		}

		// Make request arrays available for all PHP versions > 4
		if (isset($_SERVER)) {
			$this->_SERVER =& $_SERVER;
			$this->_POST =& $_POST;
			$this->_GET =& $_GET;
			$this->_COOKIE =& $_COOKIE;
			$this->_FILES =& $_FILES;
			$this->_SESSION =& $_SESSION;
		} else {
			$this->_SERVER =& $HTTP_SERVER_VARS;
			$this->_POST =& $HTTP_POST_VARS;
			$this->_GET =& $HTTP_GET_VARS;
			$this->_COOKIE =& $HTTP_COOKIE_VARS;
			$this->_FILES =& $HTTP_POST_FILES;
			$this->_SESSION =& $HTTP_SESSION_VARS;
		}

		// Stop the magician
		if (get_magic_quotes_gpc()) {
			$this->_POST = pxUtil::stripSlashesRecursive($this->_POST);
			$this->_GET = pxUtil::stripSlashesRecursive($this->_GET);
			$this->_COOKIE = pxUtil::stripSlashesRecursive($this->_COOKIE);
		}

		$this->bAllowSelection = $this->getRequestVar('bAllowSelection') == 'true';
		$this->aSelectionFilter = $this->getRequestVar('aSelectionFilter');
		$this->aSelectionFilter = !empty($this->aSelectionFilter) ? explode(',', $this->aSelectionFilter) : array();
	}

	/**
	 *
	 */
	function init($sShare = null, $sRelPathIn = null, $sUrl = null)
	{
		if (isset($sUrl)) {
			$this->sUrl = $sUrl;
		}

		$this->sCacheDir = $this->sDir . '/cache';
		$this->sModuleDir = $this->sDir . '/modules';
		if (!isset($this->sModuleUrl)) {
			$this->sModuleUrl = $this->sUrl . '/modules';
		}
		
		$bBuild = false;

		if (!is_dir($this->sCacheDir . '/types'))
		{
			$bBuild = true;

			if (!is_dir($this->sCacheDir)) {
				mkdir($this->sCacheDir, $this->aConfig['sMkDirMode']);
			}
			mkdir($this->sCacheDir . '/types', $this->aConfig['sMkDirMode']);

			foreach ($this->aConfig['_aEssentialTypes'] as $sType) {
				copy(
					$this->sModuleDir . '/System.pxm/types/' . $sType . '.pxType.php',
					$this->sCacheDir . '/types/' . $sType . '.pxType.php'
				);
			}
		}

		$this->loadType('pxRole');
		$this->loadType('pxShare');
		$this->loadType('pxAction');
		$this->loadType('pxType');
		$this->loadType('pxVfs');
		$this->loadType('pxVfsFilesystem');



#!!! NICHT $this->oShare nutzen da es später für aktuelle freigabe genutzt wird

		// Instantiate phpXplorer share manually
		$this->aShares['phpXplorer'] =& new pxShare();
		$this->oShare =& $this->aShares['phpXplorer'];
		$this->oShare->sId = 'phpXplorer';
		$this->oShare->sBaseDir = $this->sDir;
		$this->oShare->sUrl = $this->sUrl;
		$this->oVfs =& new pxVfsFilesystem();
		$this->oVfs->iEvalShareTree = 1;
		$this->oVfs->bOwnerPermission = true;
		#$this->oVfs->bSize = false; // save startup time
		$this->oVfs->oShare =& $this->oShare;
		$this->oShare->sBaseType = 'pxMetaDirectories';
		$this->oShare->oVfs =& $this->oVfs;

		$this->_loadConfiguration();

		#if ($bBuild) {
		#	$oCompiler = $this->getCompiler();
		#	foreach ($this->aConfig['_aEssentialTypes'] as $sType) {
		#		$oCompiler->compileType($sType);
		#	}
		#}

		// Check sAction parameter
		$this->sAction = $this->getRequestVar('sAction');
		if (!pxUtil::checkFilename($this->sAction)) {
			$this->raiseError('invalidActionParam', __FILE__, __LINE__);
		}

		srand((double)microtime() * 1000000);
		$this->sCallId = md5(rand() . $this->aConfig['sId']);

		umask(intval($this->aConfig['sUMask'], 8));

		// Authenticate user and load its profile settings into the session

		if (!empty($this->aConfig['sAuthentication'])) {

			if (isset($this->_COOKIE['PHPSESSID'])) {

				if (!isset($this->_SESSION['pxp_sUser'])) {

					$bForceLogin = $this->getRequestVar('bForceLogin');

					$this->loadAuthentication();

					$aUserInfo = $this->oAuthentication->getUserInfo();	

					$bForceLogin = $this->getRequestVar('bForceLogin');

					if (!isset($aUserInfo[0]) && !isset($aUserInfo[1])) {
						if (
							$this->oAuthentication->verifyUser(array('everyone', 'everyone'))
							&&
							!isset($bForceLogin)
						) {
							$aUserInfo = array('everyone', 'everyone');
						}
					}

					if (!$this->oAuthentication->verifyUser($aUserInfo)) {
						$this->oAuthentication->showLogin($aUserInfo);
					} else {

						$this->_SESSION['pxp_sUser'] = $aUserInfo[0];

						#if (isset($this->oAuthentication->aAllUsersRoles)) {
						#	if (!empty($this->oAuthentication->aAllUsersRoles)) {
						#		$this->_SESSION['pxp_aAllUsersRoles'] = $this->oAuthentication->aAllUsersRoles;
						#	}
						#}

						$oProfile = $this->getObject('phpXplorer://profiles/' . $aUserInfo[0] . '.pxProfile', false, false);

						if (!isset($oProfile) || $oProfile === false) {
							$this->loadType('pxProfile');
							$oProfile =& new pxProfile();
							$oProfile->sOwner = $aUserInfo[0];
						}

						if ($this->aConfig['bCreateHomes'])
						{
							if (!$this->oVfs->is_dir($this->sDir . '/homes/' . $aUserInfo[0])) {

								$this->oVfs->mkdir($this->sDir . '/homes/' . $aUserInfo[0]);

								$oDefaultHomeShare = $this->getObject(
									'phpXplorer://shares/_pxUserHomePrototype.pxShare'
								);

								$oDefaultHomeShare->sBaseDir = './../homes/' . $aUserInfo[0];
								#$oDefaultHomeShare->sTitle = $this->aTranslation['personalFiles'];
								$oDefaultHomeShare->store(
									'phpXplorer://shares/home_' . $aUserInfo[0]
								);

								$oProfile->aBookmarks[] = 'home_' . $aUserInfo[0];

								# create new home $aUserInfo[0]
							}
						}
						
						$oProfile->iLastLogin = time();
						$oProfile->store('phpXplorer://profiles/' . $aUserInfo[0] . '.pxProfile');

						#$this->_SESSION['pxp_sClient'] = $this->getRequestVar('pxClient'); 
						$this->_SESSION['pxp_sLanguage'] = $oProfile->sFrontendLanguage;
						$this->_SESSION['pxp_aBookmarks'] = $oProfile->aBookmarks;
						$this->_SESSION['pxp_sFullName'] = $oProfile->sFullName;

						if ($aUserInfo[0] != 'everyone') {
							if (!in_array('__phpXplorer', $oProfile->aBookmarks)) {
								$oProfileEveryone = $this->getObject('phpXplorer://profiles/everyone.pxProfile', false);
								if (isset($oProfileEveryone)) {
									$this->_SESSION['pxp_aBookmarks'] =
										array_merge(
											$this->_SESSION['pxp_aBookmarks'],
											$oProfileEveryone->aBookmarks
										);
								}
							}
							$this->_SESSION['pxp_bAuth'] = true;
						} else {
							$this->_SESSION['pxp_bAuth'] = false;
						}

						if ($this->oAuthentication->iLogin > 0) {
							$this->_SESSION['pxp_bLogin'] = true;
						}

						session_write_close();

						if ($this->oAuthentication->iLogin > 0) {						
							if ($this->sAction == 'openLogout') {
								header('Location: ' . $this->sUrl);
							} else {
								header('Location: ' . $this->_SERVER['PHP_SELF'] . '?' . $this->_SERVER['QUERY_STRING']);
							}
							exit;
						}
					}
				} else {
					
					if (
						$this->_SESSION['pxp_ip_address'] != $this->getIPAddress()
						or
						$this->_SESSION['pxp_user_agent'] != $this->getUserAgent()
					) {
						$this->loadAuthentication();
						$this->oAuthentication->showLogin();
					}
				}
				$this->sUser = $this->_SESSION['pxp_sUser'];
				#$this->sClient = $this->_SESSION['pxp_sClient'];
				$this->sLanguage = $this->_SESSION['pxp_sLanguage'];
				$this->aBookmarks = $this->_SESSION['pxp_aBookmarks'];

			} else {

				$oProfile = $this->getObject('phpXplorer://profiles/everyone.pxProfile', false);
				if (isset($oProfile)) {
					$this->sLanguage = $oProfile->sFrontendLanguage;
					$this->aBookmarks = $oProfile->aBookmarks;
				}
				$this->sUser = 'everyone';
				#$this->sClient = $this->getRequestVar('pxClient');
			}
		}
		else
		{
			$this->sUser = $pxp->aConfig['sNoAuthUser'];
			$this->aBookmarks = array('__phpXplorer');
		}

		$this->aBookmarks = array_merge($this->aBookmarks, $this->aConfig['_aAllUsersRolesShares']);

		// Load share
		if (isset($sShare)) {	
			$this->sShare = $sShare;
		} else {
			$this->sShare = $this->getRequestVar('sShare');
			if (!isset($this->sShare)) {
				if (
					in_array('__phpXplorer', $this->aBookmarks)
					||
					in_array($this->aConfig['sDefaultBookmark'], $this->aBookmarks)
				) {
					$this->sShare = $this->aConfig['sDefaultBookmark'];
				} else {
					$this->sShare = $this->aBookmarks[0];
				}
			}
		}

		#if (!empty($this->sShare))
		#{
			if (strpos($this->sShare, 'px') === 0 && strlen($this->sShare) == 34)
			{
				$sBookmark = $this->oVfs->file_get_contents(
					$this->sDir . '/bookmarks/' . $this->sShare
				);

				$sBookmarkShare = substr($sBookmark, 0, strpos($sBookmark, '|'));
				$sBookmarkDir = substr($sBookmark, strpos($sBookmark, '|') + 1);
				
				#$sRelPathIn

				if ($this->loadShare($sBookmarkShare, $this->sShare, $sBookmarkDir)) {
					$this->oShare = &$this->aShares[$sBookmarkShare];
				}
			} else {
				if ($this->loadShare($this->sShare)) {
					$this->oShare =& $this->aShares[$this->sShare];
				} else {
					$this->sShare = 'phpXplorer';
					if ($this->loadShare($this->sShare)) {
						$this->oShare =& $this->aShares[$this->sShare];
					} else {
						$this->raiseError('shareNotFound', __FILE__, __LINE__, array($this->sShare));
					}
				}
			}
		#}

		// Check sPath parameter and create instance of the object sPath points to
		$this->sRelPathIn =	$this->handlePathParameter(
			isset($sRelPathIn) ? $sRelPathIn : $this->getRequestVar('sPath')
		);	

		$this->sFullPathIn = pxUtil::buildPath($this->oShare->sBaseDir, $this->sRelPathIn);

		$this->sFullPathInDir = pxUtil::dirname($this->sFullPathIn);

		$sNewTypeIn = $this->getRequestVar('sType');

		$bNoRootDir = false;
		if ($this->oShare->oVfs->sType != 'pxVfsFilesystem' && ($this->sFullPathIn == '/')) { #|| $this->sFullPathInDir == '/'
			$bNoRootDir = true;
			#$sNewType = 'pxDirectory';
			$sNewType = $this->oShare->sBaseType;
		} else {
			$sNewType = $sNewTypeIn;
		}

		if (!$bNoRootDir) {
			$this->oObject =& $this->oShare->oVfs->get_object(
				$this->sFullPathIn,
				$this->sRelPathIn != '/',
				empty($sNewType)
			);
		}

		if (isset($this->oObject))
		{
			if ($this->sFullPathIn == $this->oShare->sBaseDir) {
				$this->oObject->sRelDir = null;
			}
		}
		else
		{
			$sBaseAction = pxUtil::getBaseAction($this->sAction);

			if (
				(isset($sNewType) && isset($this->aTypes[$sNewType]))
				||
				$bNoRootDir
			) {

				$this->loadType($sNewType);
				#require_once $this->sModuleDir . '/' . $this->aTypes[$sNewType]->sModule . '.pxm/types/' . $sNewType . '.pxType.php';

				$this->oObject = new $sNewType;
				$this->oObject->sShare = $this->sShare;
				$this->oObject->sName = basename($this->sRelPathIn);
				if (!isset($this->oObject->sRelDir)) {
					$this->oObject->sRelDir = pxUtil::dirname($this->sRelPathIn);
				}
				#echo $sNewType;
				$this->oObject->sType = $sNewType;
				$this->oObject->bDirectory = $this->aTypes[$sNewType]->bDirectory;
				//$this->sOwner = $this->sUser;
				$this->oObject->bNew = true;
				$this->oObject->sOwner = $this->sUser;

				if (!empty($sNewTypeIn) && !empty($sBaseAction) && $sBaseAction != 'open')
				{
					$this->sRelDir = $this->oObject->bDirectory ? $this->sRelPathIn : pxUtil::dirname($this->sRelPathIn);

					if ($this->oShare->checkCreatePermission(
						pxUtil::dirname($this->sRelPathIn),
						$sNewType)
					) {

						$oType = &$this->aTypes[$sNewType];
						$this->addExtension($this->sFullPathIn, $oType);
						
						// Check if filename type and stated type are the same
						$sName = basename($this->sFullPathIn);
						$sType = null;
						$sExtension = null;
						$this->getTypeKeyByExtension(
							$sName,
							$oType->bDirectory,
							$sType,
							$sExtension
						);
						if ($sType != $sNewType) {
							$this->raiseError('notAllowedToCreateType', __FILE__, __LINE__, array($sType, pxUtil::dirname($this->sRelPathIn)));
						}

						if ($oType->bDirectory) {
							$this->oShare->oVfs->mkdir($this->sFullPathIn, true);
						} else {
							$this->oShare->oVfs->mkdir($this->sFullPathInDir, true);
							$sNew = '';
							$this->oShare->oVfs->file_put_contents($this->sFullPathIn, $sNew, true);
						}
					} else {
						$this->raiseError('notAllowedToCreateType', __FILE__, __LINE__, array($sNewType, pxUtil::dirname($this->sRelPathIn)));
					}
				}
			} else {
				$this->raiseError('fileNotFound', __FILE__, __LINE__, array($this->sFullPathIn));
			}
		}

		$this->sRelDir = $this->oObject->bDirectory ? $this->sRelPathIn : pxUtil::dirname($this->sRelPathIn);

		/**
		 * Run action
		 */
		
		if (!isset($this->sAction) && !$this->oObject->bDirectory) {
			$this->sAction = $this->aTypes[$this->oObject->sType]->aDefaultActions[0];
		}

		if (isset($this->sAction)) {
			# possible ? (should be removed if all action calls are without type) 
			if (strpos($this->sAction, '_') !== false && strpos($this->sAction, '_') !== 0) {
				$sAction = substr($this->sAction, strpos($this->sAction, '_') + 1);
			} else {
				$sAction = $this->sAction;
			}

			if (isset($this->aActions['pxGlobal_' . $sAction])) {
				$this->callGlobalAction($sAction);
			} else {
				$this->oObject->call($sAction);
			}
		}
	}

	/**
	 * 
	 */
	function callGlobalAction($sAction, $aParameters = null, $bCheckPermission = true)
	{
		$this->sFullAction = 'pxGlobal_' . $sAction;

		if ($bCheckPermission) {
			if (!$this->oShare->checkActionPermission(
				$this->oObject->sRelDir,
				'pxGlobal',
				$this->sFullAction,
				isset($this->oObject->sOwner) ? $this->oObject->sOwner : 'root' 
			)) {
				$this->raiseError('notAllowedToRunAction', __FILE__, __LINE__, array($this->sFullAction));
			}
		}

		if (!isset($this->aActionObjects[$this->sFullAction])) {
			require_once
				$this->sModuleDir . '/' . $this->aActions[$this->sFullAction][0] .
				'.pxm/actions/' . $this->sFullAction . '.pxAction.php';
			$this->aActionObjects[$this->sFullAction] =&
				new $this->sFullAction;
		}

		return
			$this->aActionObjects[$this->sFullAction]->run(
				$this->oObject,
				$aParameters
			);
	}

	/**
	 * Cache types and actions
	 * 
	 * Check for type and action changes. 
	 * If there are changes, load all type and action objects into their
	 * arrays and serialize the arrays into a file called .../cache/config.php.
	 * If there are no changes, just load and deserialize config.php.
	 * 
	 * @access private
	 */
	function _loadConfiguration()
	{
		$sConfig = $this->sDir . '/config.php';
		$sCachedConfig = $this->sCacheDir . '/data.php';

		$bCacheExists = file_exists($sCachedConfig);

    if (!$bCacheExists || filemtime($sConfig) > filemtime($sCachedConfig))
    {
    	$GLOBALS['pxp'] =& $this;

			$oCompiler = $this->getCompiler();

    	if (!$this->oVfs->is_dir($this->sCacheDir)) {
    		#$this->oVfs->mkdir($this->sCacheDir);
    		$this->oVfs->mkdir($this->sCacheDir . '/classes');
    	}

			$oCompiler =& new pxCompiler();
			if ($bCacheExists) {
				require $sCachedConfig;
				$oCompiler->compileParts();
			} else {
				$oCompiler->compileAll();
			}
    }

		require $sCachedConfig;
	}

	/**
	 * Tries to create share object
	 * 
	 * @param string $sId Share identification
	 * @return boolean True on success false on failure
	 */
	function loadShare($sId, $sBookmarkId = null, $sBookmarkDir = null)
	{
		if (!isset($this->aShares[$sId])) {
			if ($sId == '_pxUserHomePrototype') {
				return false;
			}
			if (file_exists($this->sDir . '/shares/' . $sId . '.pxShare')) {
				$this->aShares[$sId] =&
					$this->getObject($this->sDir . '/shares/' . $sId . '.pxShare', false);
					
				$this->aShares[$sId]->init($sBookmarkId, $sBookmarkDir);

			} else {
				return false;
			}
		}
		if (isset($sBookmarkId)) {
			$this->aShares[$sBookmarkId] =& $this->aShares[$sId];
		}
		return true;
	}

	/**
	 * Load language file
	 * 
	 * @param string $sId Language code
	 * @param string $sModule Module ID
	 */
	function loadLanguage($sId, $sModule = 'System.pxm') {
		if (!isset($this->aLanguages[$sId])) {
			$this->aLanguages[$sId] = array();
		}
		$sDir = $this->sModuleDir . '/' . $sModule . '/translations/';
		if (is_dir($sDir)) {
			if (file_exists($sDir . $sId . '.php')) {
				require_once $sDir . $sId . '.php';
			}
		}
	}

	/**
	 * 
	 */
	function loadLanguageCodes() {
		if (empty($this->aLanguageCodes)) {
			if (file_exists($this->sModuleDir . '/System.pxm/translations/' . $this->sLanguage . '.languages.php')) {
				require $this->sModuleDir . '/System.pxm/translations/' . $this->sLanguage . '.languages.php';
			}	else {
				require $this->sModuleDir . '/System.pxm/translations/' . $this->aConfig['sSystemLanguage'] . '.languages.php';
			}
		}
	}
	
	function loadTranslation() {
		require_once $this->sDir . '/cache/translations/' . $this->sLanguage . '.php';
	}
	
	/**
	 * 
	 */
	function getIPAddress() {
		if (isset($this->_SERVER['REMOTE_ADDR'])) {		
			return $this->_SERVER['REMOTE_ADDR'];
		}
		if(isset($this->_SERVER['REMOTE_HOST'])) {
			return $this->_SERVER['REMOTE_HOST'];
		}
		return '';
	}
	
	/**
	 * 
	 */
	function getUserAgent() {
		if(isset($this->_SERVER['HTTP_USER_AGENT'])) {
			return $this->_SERVER['HTTP_USER_AGENT'];
		}
		return '';
	}

	/**
	 * Raise phpXplorer error
	 *
	 * @param integer $iNumber Error code
	 * @param string $sText Additional text to extend phpXplorer's error message
	 */
	function raiseError($sId, $sFileIn, $sLine, $aValues = null) {
		$this->callGlobalAction(
			'openError',
			array (
				'sId' => $sId,
				'sFileIn' => basename($sFileIn),
				'sLine' => $sLine,
				'aValues' => $aValues
			),
			false
		);
	}

	/**
	 * Return request variable @sId or null
	 * 
	 * @param string $sId Request variable name
	 * @return mixed Value of request variable or NULL
	 */
	function getRequestVar($sId)
	{
		if (isset($this->_GET[$sId])) {
			return $this->decodeURI($this->_GET[$sId]);
		} else {
			if (isset($this->_POST[$sId])) {
				if (is_array($this->_POST[$sId])) {
					return array_map(array($this, 'decodeURI'), $this->_POST[$sId]);
				} else {
					return $this->decodeURI($this->_POST[$sId]);
				}
			} else {
				return null;
			}
		}
	}

	/**
	 * Resolve corresponding phpXplorer type by file extension
	 *
	 * @param string $sFilename Filename to resolve
	 * @param boolean $bDirectory Needs to know if @sFilename is a file or directory
	 *
	 * @return string phpXplorer type ID
	 */
	function getTypeKeyByExtension($sFilename, $bDirectory = false, &$sTypeId, &$sExtension)
	{
		if ($bDirectory && strpos($sFilename, '.') === false) {
			$sTypeId = 'pxDirectory';
		}
		$sTypeId = null;
		$sExtension = $sFilename;

		do {
			$sExtension = substr(strstr($sExtension, '.'), 1);

			if (isset($this->_aExtensionToType[$sExtension])) {
				$sTypeId = $this->_aExtensionToType[$sExtension];				
			} else {
				$sLowerExtension = strToLower($sExtension);

				if (isset($this->_aExtensionToType[$sLowerExtension])) {
					$sTypeId = $this->_aExtensionToType[$sLowerExtension];
					$sExtension = $sLowerExtension;
				}
			}

			if (isset($sTypeId)) {
				if ($bDirectory) {
					if (!$this->aTypes[$sTypeId]->bDirectory) {						
						$sTypeId = 'pxDirectory';
						$sExtension = null;
					}
				}
				return;
			}
		} while (strpos($sExtension, '.') !== false);

		if ($bDirectory) {
			$sTypeId = 'pxDirectory';
		} else {
			$sTypeId = 'pxFile';
		}

		if (!isset($this->aTypes[$sTypeId]->aExtensions[$sExtension])) {
			$sExtension = null;
		}
	}

	/**
	 * 
	 */
	function &getObject($sPathIn, $bCheckPermission = true, $bRaiseError = true)
	{
		$mPos = strpos($sPathIn, '://');

		if ($mPos == false) {

			$mResult =& $this->aShares['phpXplorer']->oVfs->get_object($sPathIn, $bCheckPermission, $bRaiseError);

		} else {

			$sShare = substr($sPathIn, 0, $mPos);
			$sPath = substr($sPathIn, $mPos + 2);

			if (strpos($sPath, $this->aShares[$sShare]->sBaseDir) !== 0) {
				$sPath = pxUtil::buildPath($this->aShares[$sShare]->sBaseDir, $sPath);
			}

			$mResult =& $this->aShares[$sShare]->oVfs->get_object($sPath, $bCheckPermission, $bRaiseError);
		}

		return $mResult;		
	}

	/**
	 * 
	 */
	function decodeURI($sURI) {
		return utf8_decode(rawurldecode($sURI));
	}

	/**
	 * 
	 */
	#function encodeURI($sURI){
	#	return rawurlencode(utf8_encode($sURI));
	#}

	/**
	 * 
	 */
	function getExtendedTypeName($sType)
	{
		$oType =& $this->aTypes[$sType];
		if (
			!empty($oType->aExtensions[0]) && $oType->sId != $oType->aExtensions[0]
			&&
			!$oType->bAbstract
		) {
			return $oType->sId . '_' . $oType->aExtensions[0];
		} else {
			return $oType->sId;
		}
	}
	
	/**
	 * Enshure that sNewName has got a valid and allowed extension
	 * 
	 * @access public
	 */
	function addExtension(&$sFile, &$oType) {
		if (!empty($oType->aExtensions)) {
			$bIn = false;
			foreach ($oType->aExtensions as $sExtension) {
				if (strpos($sFile, '.' . $sExtension) !== false) {
					$bIn = true;
					break;
				}
			}
			if (!$bIn) {
				$sFile .= '.' . $oType->aExtensions[0];
			}
		}
	}

	function loadAuthentication() {
		$this->oAuthentication = $this->getObject($this->aConfig['sAuthentication'], false);
		$this->oAuthentication->connect();
	}
	
	function handlePathParameter($sParameter)
	{
		$sParameter = trim($sParameter);

		if (!pxUtil::checkFilename($sParameter, true)) {
			$this->raiseError('invalidPathParam', __FILE__, __LINE__, array($sParameter));
		}
		
		// .htgroups and .htpasswd strings are not allowed in URLs on some servers
		#$this->sRelPathIn = str_replace('._BYPASS_ht_', '.ht', $this->sRelPathIn);

		if (substr($sParameter, 0, 1) != '/') {
			$sParameter = '/' . $sParameter;
		}
		if ($sParameter != '/') {
			$iLen = strlen($sParameter);
			if ($sParameter{$iLen - 1} == '/') {
				$sParameter = substr($sParameter, 0, $iLen - 1);
			}
		}
		return $sParameter;
	}
	
	/**
	 * 
	 */
	function encodeURIParts($sURI) {
		$aParts = explode('/', $sURI);
		foreach ($aParts as $iIndex => $sValue) {
			if (strpos($sValue, 'http:') === false) {
				if ($this->oShare->bUnicodeUrl) {
					$aParts[$iIndex] = rawurlencode(utf8_encode($sValue));
				} else {
					$aParts[$iIndex] = rawurlencode($sValue);
				}
			} else {
				$aParts[$iIndex] = $sValue;
			}
		}
		return implode('/', $aParts);
	}

	function resolvePxpPath($sPathIn)
	{
		$mPos = strpos($sPathIn, '://');

		if ($mPos == false) {
			$sShare = 'phpXplorer';
			$sPath = $sPathIn;
		} else {
			$sShare = substr($sPathIn, 0, $mPos);
			$sPath = substr($sPathIn, $mPos + 2);
		}

		$this->loadShare($sShare);

		if (strpos($sPathIn, $this->aShares[$sShare]->sBaseDir) !== 0) {
			$sPath = pxUtil::buildPath($this->aShares[$sShare]->sBaseDir, $sPath);
		}

		return $sPath;
	}


	function moduleExists($sModuleId) {
		return $this->oVfs->is_dir($this->sModuleDir . '/' . $sModuleId . '.pxm');
	}
	
	
	function &getCompiler()
	{
		global $_oCompiler;

		require_once dirname(__FILE__) . '/pxCompiler.php';

		if (!isset($_oCompiler)) {
			$_oCompiler = new pxCompiler;
		}
		
		return $_oCompiler;		
	}

	/**
	 * 
	 */
	function loadType($sType)
	{
		if (class_exists($sType)) {
			return true;
		}
		$sCacheFile = $this->sCacheDir . '/types/' . $sType . '.pxType.php';

		if ($this->aConfig['bDevelopment'])
		{
			if (in_array($sType, $this->aConfig['_aEssentialTypes'])) {
				$sSourceFile = $this->sModuleDir . '/System.pxm/types/' . $sType . '.pxType.php';
				if (!file_exists($sCacheFile) || filemtime($sSourceFile) > filemtime($sCacheFile)) {
					copy($sSourceFile, $sCacheFile);
				}
			} else {
				$sModule = isset($this->aTypes[$sType]) ? $this->aTypes[$sType]->sModule : 'System';
				if (filemtime($sCacheFile) < filemtime($this->sModuleDir . '/' . $sModule . '.pxm/types/' . $sType . '.pxType.php')) {			
					$oCompiler = $this->getCompiler();
					$oCompiler->compileType($sType, true);
				}
			}
		}
		require_once $sCacheFile;
	}
}

?>