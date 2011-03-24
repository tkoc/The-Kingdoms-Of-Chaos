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
 * Base class for user authentication
 *
 * @abstract
 */
class pxAuthentication extends pxMetaFiles{
	/**
	 * Path or DSN to used file/database
	 *
	 * @var string
	 * @edit Input
	 */
	var $sDSN;

	/**
	 * Specifies login type (how user information (name and password) gets captured)
	 *
	 * 0: HTML
	 * 1: HTTP
	 * 2: htaccess
	 *
	 * @var integer
	 * @edit Select
	 */
	var $iLogin = 0;

	/**
	 * Specifies the algorythm used for password encryption
	 * 
	 * none:
	 * auto:
	 * crypt:
	 * md5:
	 * crypt_apr_md5:
	 *
	 * @var string
	 * @edit Select
	 */
	var $sEncryption;

	/**
	 * Encryption salt string
	 *
	 * @var string
	 * @edit Input
	 */
	var $sSalt;

	/**
	 * Returns options of selection member types
	 * 
	 * @param string $sMember Selection member ID
	 * 
	 * @return array Associative array with options
	 */
	function _getOptions($sMember)
	{
		global $pxp;

		switch($sMember){
			case 'iLogin':
				return array(
	 				'HTML',
	 				'HTTP',
	 				'htaccess'
				);
				break;
			case 'sEncryption':
				return array(
					'none' => 'none',
					'auto' => 'auto',
					'crypt' => 'crypt',
					'md5' => 'md5',
					'crypt_apr_md5' => 'crypt_apr_md5'
				);
				break;
			default:
				return parent::_getOptions($sMember);
				break;
		}
	}

	/**
	 * 
	 */
	function connect()
	{
		return true;
	}
	
	/**
	 * 
	 */
	function showLogin($aUserInfo = null)
	{
		global $pxp;

		if ($this->iLogin == 0) { // html
		
			require_once $pxp->sModuleDir . '/System.pxm/actions/pxGlobal_openLogin.pxAction.php';
		
			$sActionClassName = 'pxGlobal_openLogin';
			$oAction =& new $sActionClassName($aUserInfo);

			echo $oAction->generate();
			exit;

		} else { // http

			header('WWW-Authenticate: Basic realm="phpXplorer@' . $pxp->_SERVER['HTTP_HOST'] . '"');
			header('HTTP/1.0 401 Unauthorized');
					
			$pxp->raiseError('accessDenied', __FILE__, __LINE__);
			exit;
		}
	}

	/**
	 * 
	 */
	function getUserInfo()
	{
		global $pxp;

		switch ($this->iLogin) {
		case 0:
			$sAuthUser = $pxp->getRequestVar('pxAuthUser');
			$sAuthPassword = $pxp->getRequestVar('pxAuthPassword');
			return array($sAuthUser, $sAuthPassword);
			break;
		case 1:
			return $this->getHTTPUserInfo();
			break;
		case 2:
			return $this->getHTTPUserInfo();
			break;
		}
	}
	
	/**
	 * 
	 */
	function getHTTPUserInfo()
	{
		global $pxp;

    if (isset($pxp->_SERVER['REMOTE_USER'])) {
			if (!empty($pxp->_SERVER['REMOTE_USER'])) {

				$sPassword = '';

				if (isset($pxp->_SERVER['REMOTE_PW'])) {
					$sPassword = $pxp->_SERVER['REMOTE_PW'];
				}

				if (empty($sPassword)) {
					if (isset($pxp->_SERVER['REMOTE_PASSWORD'])) {
						$sPassword = $pxp->_SERVER['REMOTE_PASSWORD'];
					}
				}

				return array($pxp->_SERVER['REMOTE_USER'], $sPassword);
			}
		}

   	if (isset($pxp->_SERVER['PHP_AUTH_USER'])) {
			if (!empty($pxp->_SERVER['PHP_AUTH_USER'])) {
				
				$sPassword = '';

				if (isset($pxp->_SERVER['PHP_AUTH_PW'])) {
					$sPassword = $pxp->_SERVER['PHP_AUTH_PW'];
				}

				if (empty($sPassword)) {
					if (isset($pxp->_SERVER['PHP_AUTH_PASSWORD'])) {
						$sPassword = $pxp->_SERVER['PHP_AUTH_PASSWORD'];
					}
				}

				return array($pxp->_SERVER['PHP_AUTH_USER'], $sPassword);
			}
		}

		if (isset($pxp->_SERVER['REDIRECT_REMOTE_USER'])) {
			if (!empty($pxp->_SERVER['REDIRECT_REMOTE_USER'])) {

				$sPassword = '';

				if (isset($pxp->_SERVER['REDIRECT_REMOTE_PW'])) {
					$sPassword = $pxp->_SERVER['REDIRECT_REMOTE_PW'];
				}

				if (empty($sPassword)) {
					if (isset($pxp->_SERVER['REDIRECT_REMOTE_PASSWORD'])) {
						$sPassword = $pxp->_SERVER['REDIRECT_REMOTE_PASSWORD'];
					}
				}

				return array($pxp->_SERVER['REDIRECT_REMOTE_USER'], $sPassword);
			}
		}
		
		if (isset($pxp->_SERVER['AUTH_USER'])) {
			if (!empty($pxp->_SERVER['AUTH_USER'])) {

				$sPassword = '';

				if (isset($pxp->_SERVER['AUTH_PW'])) {
					$sPassword = $pxp->_SERVER['AUTH_PW'];
				}

				if (empty($sPassword)) {
					if (isset($pxp->_SERVER['AUTH_PASSWORD'])) {
						$sPassword = $pxp->_SERVER['AUTH_PASSWORD'];
					}
				}

				return array($pxp->_SERVER['AUTH_USER'], $sPassword);
			}
		}

		if (function_exists('getallheaders')) {

			$aHeaders = getallheaders();
			
			if (isset($aHeaders['Authorization'])) {				
				$aParts = split(' ', $aHeaders['Authorization'], 2);
				return explode(':', base64_decode($aParts[1]));
			} else {
				return array(null, null);
			}
		}

		return array(null, null);
	}

	/**
	 * 
	 */
	function encrypt($sPassword, $sSalt = null)
	{	
		global $pxp;
		
		if (!isset($this->sEncryption)  or  $this->sEncryption == 'auto') {

			if (strpos(strtolower(PHP_OS), 'win') === false) {

				$this->sEncryption = 'crypt';

			} else {

				$this->sEncryption = 'crypt_apr_md5';
			}
		}

		if (isset($this->sSalt) && !empty($this->sSalt)) {

			$sCurrentSalt = $this->sSalt;

		} else {

			$sCurrentSalt = $sSalt;
		}

  	switch ($this->sEncryption) {

  		case 'crypt':
  			return crypt($sPassword, $sCurrentSalt);				
  		break;

  		case 'none':
  			return $sPassword;
  		break;

  		case 'md5':
  			return md5($sPassword);
  		break;

  		case 'crypt_apr_md5':
			
				require_once $pxp->sModuleDir . '/System.pxm/includes/PEAR/Passwd.php';

  			return File_Passwd::crypt_apr_md5($sPassword, $sCurrentSalt);
  		break;
  	}
	}
	
	/**
	 * Verifies user info against database
	 *
	 * @param array $aUserInfo Array with username and password
	 * @return boolean True of false depending on success
	 */
	function verifyUser($aUserInfo = null) {
		global $pxp;

		if (!isset($aUserInfo)) {
			return false;
		}

		if (!isset($aUserInfo[0])  or  !isset($aUserInfo[1])) {
			return false;
		}

		$sUser = $aUserInfo[0];
		$sPassword = $aUserInfo[1];
		$sStoredPassword = $this->getPassword($sUser);

		if ($sStoredPassword !== false) {
			if ($sStoredPassword == $this->encrypt($sPassword, $sUser)) {

				$pxp->_SESSION['pxp_ip_address'] = $pxp->getIPAddress();
				$pxp->_SESSION['pxp_user_agent'] = $pxp->getUserAgent();

				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Tests if users exists and returns its password or false
	 *
	 * @abstract
	 * @param string $sUser Username
	 * @return boolean True or false depending on existence
	 */
	function getPassword($sUser) {
		die('Function getUser is not implemented in ' . get_class($this) . ' class.');
	}

	/**
	 * Returns array with all users
	 *
	 * @abstract
	 * @return array Returns array with all users
	 */
	function getUsers() {
		die('Function getUsers is not implemented in ' . get_class($this) . ' class.');
	}
	
	/**
	 * Add new user to user database
	 *
	 * @abstract
	 * @param string $sUser New username
	 * @param string $sPassword New password
	 * @param boolean $bEncrypt Encrypt new password
	 * @return boolean True or false depending on success
	 */
	function addUser($sUser, $sPassword, $bEncrypt = true) {
		die('Function changePassword is not implemented in ' . get_class($this) . ' class.');
	}

	/**
	 * Remove user from user database
	 *
	 * @abstract
	 * @param string $sUser Name of user to delete
	 * @return boolean True or false depending on success
	 */
	function removeUser() {
		die('Function changePassword is not implemented in ' . get_class($this) . ' class.');
	}

	/**
	 * Change password of existing user
	 *
	 * @abstract
	 * @param string $sUser Name of user
	 * @param string $sPassword New password
	 * @return boolean True or false depending on success
	 */
	function changePassword() {
		die('Function changePassword is not implemented in ' . get_class($this) . ' class.');		
	}
	var $sType = 'pxAuthentication';}

?>