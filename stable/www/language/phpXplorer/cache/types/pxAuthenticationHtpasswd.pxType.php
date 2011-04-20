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

require_once dirname(__FILE__) . '/pxAuthentication.pxType.php';

/**
 * Implements functionality to handle authentication against Apache style .htaccess files
 *
 * @extensions pxAuthenticationHtpasswd
 * @belongsTo phpXplorer:
 * @edit
 */
class pxAuthenticationHtpasswd extends pxAuthentication{
	/**
	 * Array to hold .htpasswd file in memory
	 *
	 * @access private
	 * @var array
	 */
	var $_aUsers = array();

	/**
	 * Loads .htpasswd file into _aUsers array
	 *
	 * @return boolean Returns if sDSN path exists
	 */
	function connect()
	{
		global $pxp;

		$aLines = file($pxp->resolvePxpPath($this->sDSN));

		if ($aLines !== false) {

			foreach ($aLines as $sLine) {
				$aValues = explode(':', $sLine);
				$this->_aUsers[trim($aValues[0])] = trim($aValues[1]);
			}

			return true;

		} else {

			return false;
		}
	}

	/**
	 * Writes modified _aUsers array back to sDSN path
	 * 
	 * @access private
	 */
	function _store()
	{
		global $pxp;

		$sFile = '';

		foreach ($this->_aUsers as $sUser => $sPassword) {
			$sFile .= $sUser . ':' . $sPassword . chr(13) . chr(10);
		}

		return $pxp->oVfs->file_put_contents(
			$pxp->resolvePxpPath($this->sDSN),
			$sFile
		);
	}

	/**
	 * Tests if users exists and returns its password or false
	 *
	 * @param string $sUser Username
	 * @return boolean True or false depending on existence
	 */
	function getPassword($sUser)
	{
		if (isset($this->_aUsers[$sUser])) {
			return $this->_aUsers[$sUser];
		}

		return false;
	}
	
	/**
	 * Returns array with all users
	 *
	 * @return array Returns array with all users
	 */
	function getUsers()
	{
		return $this->_aUsers;
	}
	
	/**
	 * Add new user to user database
	 *
	 * @param string $sUser New username
	 * @param string $sPassword New password
	 * @param boolean $bEncrypt Encrypt new password
	 * @return boolean True or false depending on success
	 */
	function addUser($sUser, $sPassword, $bEncrypt = true)
	{
		if (!isset($this->_aUsers[$sUser])) {
			if ($bEncrypt) {
				$sPassword = $this->encrypt($sPassword, $sUser);
			}
			$this->_aUsers[$sUser] = $sPassword;
			return $this->_store();

		} else {
			return false;
		}
	}

	/**
	 * Remove user from user database
	 *
	 * @param string $sUser Name of user to delete
	 * @return boolean True or false depending on success
	 */
	function removeUser($sUser)
	{
		if (isset($this->_aUsers[$sUser])) {

			unset($this->_aUsers[$sUser]);
		
			return $this->_store();

		} else {
			return false;
		}
	}

	/**
	 * Change password of existing user
	 *
	 * @param string $sUser Name of user
	 * @param string $sPassword New password
	 * @return boolean True or false depending on success
	 */
	function changePassword($sUser, $sPassword)
	{
		if (isset($this->_aUsers[$sUser])) {

			$this->_aUsers[$sUser] = $this->encrypt($sPassword, $sUser);

			return $this->_store();

		} else {
			
			return false;
		}
	}
	var $sType = 'pxAuthenticationHtpasswd';}
