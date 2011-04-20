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
 * Vfs FTP driver
 *
 * Implements functionality to work with a FTP server instead of the local filesystem
 *
 * @extensions pxVfsFtp
 * @expandSubtypes pxVfsFtp
 * @edit
 */
class pxVfsFtp extends pxVfs
{
	var $aPossibleActions = array(
		'upload' => true,
		'share' => true,
		'edit' => true,
		'md5' => false
	);

	/**
	 * FTP server address/name
	 * 
	 * @var string
	 * @edit Input
	 */
	var $sHost;

	/**
	 * Username for server connection
	 *
	 * @var string
	 * @edit Input
	 */
	var $sUser = 'anonymous';

	/**
	 * Password for server connection
	 * 
	 * @var string
	 * @edit Password
	 */
	var $sPassword = '';

	/**
	 * Server port number used for FTP connections
	 * 
	 * @var integer
	 * @edit Input
	 */
	var $iPort = 21;

	/**
	 * Passive FTP connection are initiated through client instead of server
	 * 
	 * @var boolean
	 * @edit Checkbox
	 */
	var $bPassive = false;

	/**
	 * Use Secure Socket Layer for server connection
	 *
	 * @var boolean
	 * @edit Checkbox 
	 */
	var $bSSL;

	/* internal */

	/**
	 * Type of operating system
	 * 
	 * unix, win or mixed
	 * 
	 * @access private
	 * @var string
	 */
	var $_sOsType;
	
	/**
	 * Server connection handle
	 * 
	 * @access private
	 * @var integer
	 */
	var $_iStream;
	
	/**
	 * Stores received files
	 * 
	 * @access private
	 * @var array
	 */
	var $_aFiles = array();
	
	/**
	 * Array to cache received file lists
	 * 
	 * @access private
	 * @var array
	 */
	#var $_aLists = array();

	
	/**
	 * Constructor
	 */
	function pxVfsFtp() {
		parent::pxVfs();
		$this->iEvalShareTree = 0;
	}

	/**
	 * 
	 */
	function init()
	{
		$this->connect();

		$this->_sOsType = strtolower(ftp_systype($this->_iStream));

		if ($this->_sOsType == 'unknown') {
			$this->_sOsType = 'unix';
		} elseif (strpos($this->_sOsType, 'win') !== false) {
			$this->_sOsType = 'win';
		}

		parent::init();
	}

	/**
	 * 
	 */
	function connect()
	{
		global $pxp;

		if (!isset($this->_iStream) and !$this->bConnected) {

			if (empty($this->iPort)) {
				$this->iPort = 21;
			}

			if (!extension_loaded('ftp')) {
				$pxp->raiseError('extensionNotLoaded', __FILE__, __LINE__, array('FTP'));
			}

			if ($this->bSSL  and  function_exists('ftp_ssl_connect')) {
				$this->_iStream = @ftp_ssl_connect($this->sHost, (int)$this->sPort);
			} else {
				$this->_iStream = @ftp_connect($this->sHost, (int)$this->sPort);
			}
			
			if (!$this->_iStream) {
				$pxp->raiseError('couldNotConnect', __FILE__, __LINE__, array('FTP'));
			}

			if (!@ftp_login($this->_iStream, $this->sUser, $this->sPassword)) {
				$this->disconnect();
				$pxp->raiseError('loginFailed', __FILE__, __LINE__);
			}

			if ($this->bPassive) {
				@ftp_pasv($this->_iStream, true);
			}
		}
		
		$this->bConnected = true;
		
		return true;
	}
	
	/**
	 * 
	 */
	function disconnect() {
		@ftp_quit($this->_iStream);
		$this->_iStream = null;
		$this->bConnected = false;
	}

	/**
	 * Return file data
	 *  
	 * @param string $sPath
	 * 
	 * @return mixed Data of file at $aPath
	 */
	function file_get_contents($sPath)
	{
		parent::file_get_contents($sPath);

		$sTmpPath = parent::getTempPath();

		if (@ftp_get($this->_iStream, $sTmpPath, $sPath, FTP_BINARY) === false) {
			return false;
		}

		$iBytes = filesize($sTmpPath);

		if ($iBytes === 0) {
			return false;
		}

		$iHandle = fopen($sTmpPath, 'rb');
		$sData = fread($iHandle, $iBytes);
		fclose($iHandle);

		unlink($sTmpPath);

    return $sData;
	}
	
	/**
	 * 
	 */
	function file_put($sPath, $sTmpPath) {
		$this->mkdir(pxUtil::dirname($sPath));
		$bResult = @ftp_put($this->_iStream, $sPath, $sTmpPath, FTP_BINARY);
		$this->iLastInsertId = parent::file_put($sPath, $sTmpPath, $bResult);
		return $bResult;
	}

	/**
	 * 
	 */
	function file_put_contents($sPath, $sData)
	{
		$this->mkdir(pxUtil::dirname($sPath));

		$sTmpPath = parent::getTempPath();
		$bResult = false;

		if ($sTmpPath !== false) {
			$iHandle = fopen($sTmpPath, 'wb');
			if ($iHandle !== false) {
	    	fwrite($iHandle, $sData);
	    	fclose($iHandle);
	    	$this->file_put($sPath, $sTmpPath);
				unlink($sTmpPath);
				$bResult = true;
			}
		}

		parent::file_put_contents($sPath, $sData, $bResult);
		return $bResult;
	}

	/**
	 *
	 */
	function is_file($sPath)
	{
		$mResult = parent::is_file($sPath);

		if ($mResult !== null) {
			return $mResult;
		} else {
			return ftp_size($this->_iStream, $sPath) > -1; 
			/*
			$aList = ftp_nlist($this->_iStream, $sPath);
			if ($aList === false) {
				return false;
			} else {
				return in_array(basename($sPath), $aList);
			}
			*/
		}
	}

	/**
	 * 
	 */
	function filesize($sPath)
	{
		$mResult = parent::filesize($sPath);

		if ($mResult !== null) {
			return $mResult;
		} else {
			$iBytes = @ftp_size($this->_iStream, $sPath);
			if ($iBytes == -1) {
				$iBytes = 0;
			}
			return $iBytes;
		}
	}

	/**
	 * 
	 */
	function touch($sPath) {
		parent::touch($sPath);
	}

	/**
	 * 
	 */
	function unlink($sPath) {
		if (empty($sPath)) {
			return false;
		}
		$bResult = @ftp_delete($this->_iStream, $sPath);
		parent::unlink($sPath, $bResult);
		return $bResult;
	}
	

    /**
     * Returns an an unsorted file list of the specified directory.
     *
     * @param string $path       The path of the directory.
     * @param mixed $filter      String/hash to filter file/dirname on.
     * @param boolean $dotfiles  Show dotfiles?
     * @param boolean $dironly   Show only directories?
     *
     * @return array  File list on success or PEAR_Error on failure.
     */
    #function _listFolder($path = '', $filter = null, $dotfiles = false,
    #                     $dironly = false)

    function _ls(&$oQuery, &$aFiles, $sCurrentDir)
    {
        #$conn = $this->_connect();
        #if (is_a($conn, 'PEAR_Error')) {
        #    return $conn;
        #}

        $type = pxVfs::strtolower(@ftp_systype($this->_iStream));
        if ($type == 'unknown') {
            // Go with unix-style listings by default.
            $type = 'unix';
        } elseif (strpos($type, 'win') !== false) {
            $type = 'win';
        } elseif (strpos($type, 'netware') !== false) {
            $type = 'netware';
        }
        
        @ftp_chdir($this->_iStream, $sCurrentDir);

        #$olddir = $this->getCurrentDirectory();
        #if (!empty($path)) {
        #    $res = $this->_setPath($path);
        #    if (is_a($res, 'PEAR_Error')) {
        #        return $res;
        #    }
        #}

        if ($type == 'unix') {
            // If we don't want dotfiles, We can save work here by not
            // doing an ls -a and then not doing the check later (by
            // setting $dotfiles to true, the if is short-circuited).
            #if ($dotfiles) {
            #    $list = ftp_rawlist($this->_iStream, '-al');
            #    $dotfiles = true;
            #} else {
                $list = ftp_rawlist($this->_iStream, '-l');
            #}
        } else {
           $list = ftp_rawlist($this->_iStream, '');
        }

        if (!is_array($list)) {
            #if (isset($olddir)) {
            #    $res = $this->_setPath($olddir);
            #    if (is_a($res, 'PEAR_Error')) {
            #        return $res;
            #    }
            #}
            return array();
        }

        /* If 'maplocalids' is set, check for the POSIX extension. */
        #$mapids = false;
        #if (!empty($this->_params['maplocalids']) &&
        #    extension_loaded('posix')) {
        #    $mapids = true;
        #}

        $currtime = time();

        foreach ($list as $line) {
            $file = array();
            $item = preg_split('/\s+/', $line);
            if ($type == 'unix' || ($type == 'win' && !preg_match('|\d\d-\d\d-\d\d|', $item[0]))) {
                if (count($item) < 8 || substr($line, 0, 5) == 'total') {
                    continue;
                }
                $file['sPermissions'] = $item[0];
                #if ($mapids) {
                #    if (!isset($this->_uids[$item[2]])) {
                #        $entry = posix_getpwuid($item[2]);
                #        $this->_uids[$item[2]] = (empty($entry)) ? $item[2] : $entry['sName'];
                #    }
                #    $file['sOwner'] = $this->_uids[$item[2]];
                #    if (!isset($this->_uids[$item[3]])) {
                #        $entry = posix_getgrgid($item[3]);
                #        $this->_uids[$item[3]] = (empty($entry)) ? $item[3] : $entry['sName'];
                #    }
                #    $file['sGroup'] = $this->_uids[$item[3]];
								#
                #} else {
                    $file['sOwner'] = $item[2];
                    $file['sGroup'] = $item[3];
                #}
                $file['sName'] = substr($line, strpos($line, sprintf("%s %2s %5s", $item[5], $item[6], $item[7])) + 13);

                // Filter out '.' and '..' entries.
                if (preg_match('/^\.\.?\/?$/', $file['sName'])) {
                    continue;
                }

                // Filter out dotfiles if they aren't wanted.
                #if (!$dotfiles && substr($file['sName'], 0, 1) == '.') {
                #    continue;
                #}

                $p1 = substr($file['sPermissions'], 0, 1);
                if ($p1 === 'l') {
                    $file['sLink'] = substr($file['sName'], strpos($file['sName'], '->') + 3);
                    $file['sName'] = substr($file['sName'], 0, strpos($file['sName'], '->') - 1);
                    $file['sOsType'] = '**sym';

                   if ($this->is_dir($file['sLink'])) {
                              $file['sLinktype'] = '**dir';
                                                    } else {
                                                    $parts = explode('/', $file['sLink']);
                                                    $name = explode('.', array_pop($parts));
                                                    if (count($name) == 1 || ($name[0] === '' && count($name) == 2)) {
                                                        $file['sLinktype'] = '**none';
                                                        } else {
                                                            $file['sLinktype'] = pxVfs::strtolower(array_pop($name));
                                                            }
                                                                   }
                } elseif ($p1 === 'd') {
                    $file['sOsType'] = '**dir';
                } else {
                    $name = explode('.', $file['sName']);
                    if (count($name) == 1 || (substr($file['sName'], 0, 1) === '.' && count($name) == 2)) {
                        $file['sOsType'] = '**none';
                    } else {
                        $file['sOsType'] = pxVfs::strtolower($name[count($name) - 1]);
                    }
                }
                if ($file['sOsType'] == '**dir') {
                    $file['iBytes'] = -1;
                } else {
                    $file['iBytes'] = (int)$item[4];
                }
                if (strpos($item[7], ':') !== false) {
                    $file['dModified'] = strtotime($item[7] . ':00' . $item[5] . ' ' . $item[6] . ' ' . date('Y', $currtime));
                    // If the ftp server reports a file modification date more
                    // less than one day in the future, don't try to subtract
                    // a year from the date.  There is no way to know, for
                    // example, if the Vfs server and the ftp server reside
                    // in different timezones.  We should simply report to the
                    //  user what the FTP server is returning.
                    if ($file['dModified'] > ($currtime + 86400)) {
                        $file['dModified'] = strtotime($item[7] . ':00' . $item[5] . ' ' . $item[6] . ' ' . (date('Y', $currtime) - 1));
                    }
                } else {
                    $file['dModified'] = strtotime('00:00:00' . $item[5] . ' ' . $item[6] . ' ' . $item[7]);
                }
            } elseif ($type == 'netware') {
                $file = Array();
                $file['sPermissions'] = $item[1];
                $file['sOwner'] = $item[2];
                if ($item[0] == 'd') {
                    $file['sOsType'] = '**dir';
                } else {
                    $file['sOsType'] = '**none';
                }
                $file['iBytes'] = (int)$item[3];
                $file['sName'] = $item[7];
                $index = 8;
                while ($index < count($item)) {
                    $file['sName'] .= ' ' . $item[$index];
                    $index++;
                }
            } else {
                /* Handle Windows FTP servers returning DOS-style file
                 * listings. */
                $file['sPermissions'] = '';
                $file['sOwner'] = '';
                $file['sGroup'] = '';
                $file['sName'] = $item[3];
                $index = 4;
                while ($index < count($item)) {
                    $file['sName'] .= ' ' . $item[$index];
                    $index++;
                }
                $file['dModified'] = strtotime($item[0] . ' ' . $item[1]);
                if ($item[2] == '<DIR>') {
                    $file['sOsType'] = '**dir';
                    $file['iBytes'] = -1;
                } else {
                    $file['iBytes'] = (int)$item[2];
                    $name = explode('.', $file['sName']);
                    if (count($name) == 1 || (substr($file['sName'], 0, 1) === '.' && count($name) == 2)) {
                        $file['sOsType'] = '**none';
                    } else {
                        $file['sOsType'] = pxVfs::strtolower($name[count($name) - 1]);
                    }
                }
            }

            // Filtering.
            #if ($this->_filterMatch($filter, $file['sName'])) {
            #    unset($file);
            #    continue;
            #}

            if ($oQuery->bOnlyDirectories && $file['sOsType'] != '**dir') {
                unset($file);
                continue;
            }

  					if (!empty($oQuery->aNames) and $sCurrentDir == $oQuery->sDirectory) {
  						if (!in_array($file['sName'], $oQuery->aNames)) {
  							unset($file);
  							continue;
  						}
  					}

  					$file['bDirectory'] = $file['sOsType'] == '**dir';

            $aFiles[$file['sName']] = $file;

            unset($file);
        }

 				#uksort($files, 'strnatcmp');
				#if ($oQuery->sOrderDirection == 'desc') {
 				#	$files = array_reverse($files);
 				#}

 				#print_r($aFiles);
 				#echo $oQuery->bFull;
 				#die("sdf");

        #if (isset($olddir)) {
        #    $res = $this->_setPath($olddir);
        #    if (is_a($res, 'PEAR_Error')) {
        #        return $res;
        #    }
        #}

        return $aFiles;
    }

	/**
	 * Store serialized objects in filesystem
	 */
	function store_object(&$oObject)
	{
		global $pxp;

		if (!parent::store_object($oObject))
		{
			$sFullPath = pxUtil::buildPath(
				$pxp->aShares[$oObject->sShare]->sBaseDir,
				$oObject->sRelDir . '/' . $oObject->sName
			);

			if ($pxp->aTypes[$oObject->sType]->bDirectory) {
				if (!$this->is_dir($sFullPath)) {
					if (!$this->mkdir($sFullPath)) {
						return false;
					}
				}
			} else {
				if (!$this->is_file($sFullPath)) {
					$sNew = '';
					if (!$this->file_put_contents($sFullPath, $sNew)) {
						return false;
					}
				}
			}

			$sDir = pxUtil::dirname($sFullPath);

			$this->makeMetaDataDirectory($sDir);

			$sDataPath = $sDir . '/.phpXplorer/.objects/' . $oObject->sName . '.serializedPhp';

			$sSerialized = serialize($oObject);

			if (!$this->file_put_contents($sDataPath, $sSerialized)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * 
	 */
	function mkdir($sPath)
	{
		global $pxp;
		
		$bResult = false;
		if (!$this->is_dir($sPath)) {
			$bResult = true;
			if (!$this->is_dir(pxUtil::dirname($sPath))) {
				$bResult = $this->mkdir(pxUtil::dirname($sPath));
			}
			if ($bResult) {
				$bResult = @ftp_mkdir($this->_iStream, $sPath);
			}
		}
		$this->iLastInsertId = parent::mkdir($sPath, $bResult);
		return $bResult;
	}
	
	/**
	 * 
	 */
	function is_dir($sPath)
	{
		$mResult = parent::is_dir($sPath);

		if ($mResult !== null) {
			return $mResult;
		} else {
			return @ftp_chdir($this->_iStream, $sPath); 
		}
	}
	
	/**
	 * 
	 */
	function scandir($sDir)
	{
		$mResult = parent::scandir($sDir);

		if ($mResult !== null) {
			return $mResult;
		} else {
			return ftp_nlist($this->_iStream, $sDir);
		}
	}

	/**
	 * 
	 */
	function rmdir($sPath)
	{
		$bResult = true;

		if (!isset($this->_oQuery)) {
			$this->_oQuery =& new pxQuery;
		}

		$this->_oQuery->sDirectory = $sPath;

		foreach ($this->ls($this->_oQuery) as $oObject) {
  		if ($oObject->bDirectory) {
  			$bResult = $this->rmdir($sPath . '/' . $oObject->sName);
  		} else {
  			$bResult = $this->unlink($sPath . '/' . $oObject->sName);
  		}
  		if ($bResult === false) {
  			break;
  		}
  	}

  	if ($bResult) {
  		$bResult = @ftp_rmdir($this->_iStream, $sPath);
  	}

  	parent::rmdir($sPath, $bResult);
  	return $bResult;
	}

	/**
	 * 
	 */
	function file_exists($sPath)
	{
		$mResult = parent::file_exists($sPath);

		if ($mResult !== null) {
			return $mResult;
		} else {
			return $this->is_file($sPath) || $this->is_dir($sPath); 
		}
	}

	/**
	 * 
	 */
	function filemtime($sPath)
	{
		$mResult = parent::filemtime($sPath);

		if ($mResult !== null) {
			return $mResult;
		} else {
			$iData = ftp_mdtm($this->_iStream, $sPath);

			if($iData == -1) {
				$iData = 0;
			}

			return $iData;
		}
	}

	/**
	 * 
	 */
	function filectime($sPath)
	{
		$mResult = parent::filectime($sPath);

		if ($mResult !== null) {
			return $mResult;
		} else {
			return 0;
		}
	}

	/**
	 * 
	 */
	function os_permissions($sPath) {
		return '';
	}

	/**
	 * 
	 */
	function os_owner($sPath) {
		return '';
	}
	
	/**
	 * 
	 */
	function os_group($sPath) {	
		return '';
	}

	/**
	 * 
	 */
	function rename($sPathOld, $sPathNew)
	{
		$bResult = @ftp_rename($this->_iStream, $sPathOld, $sPathNew);
		parent::rename($sPathOld, $sPathNew, $bResult);
		return $bResult;
	}
	
	function copy($sPathFrom, $sPathTo)
	{
  	$bResult =	$this->file_put_contents(
			$sPathTo,
  		$this->file_get_contents($sPathFrom)
  	);
		if ($bResult) {
			$this->iLastInsertId = parent::copy($sPathFrom, $sPathTo);
		}
		return $bResult;
	}
	
	/**
	 *
	 */
	/*
	function copy($sPathFrom, $sPathTo)
	{
  	if ($this->is_dir($sPathTo)) {
  		$sNewDest = $sPathTo . '/' . basename($sPathFrom);
  	} else {
  		$sNewDest = $sPathTo;
  	}

  	if ($this->is_dir($sPathFrom)) {

			$this->mkdir($sNewDest);

			if (!isset($this->_oQuery)) {
				$oQuery =& new pxQuery;
			}

			$this->_oQuery->sDirectory = $sPathFrom;
  		foreach ($this->ls($this->_oQuery) as $oObject) {
  			$this->copy($sPathFrom . '/' . $oObject->sName, $sNewDest);
  		}
  	} else {

  		$this->file_put_contents(
  			$sNewDest,
  			$this->file_get_contents($sPathFrom)
  		);

  		$this->iLastInsertId = parent::copy($sPathFrom, $sNewDest);
  	}
	}
	*/
	
	/**
	 * 
	 */
	function _getTmpFile($sPath)
	{
		$sTmpPath = parent::getTempPath();

		if (@ftp_get($this->_iStream, $sTmpPath, $sPath, FTP_BINARY) === false) {
			return false;
		}

		return $sTmpPath;
	}
}

?>