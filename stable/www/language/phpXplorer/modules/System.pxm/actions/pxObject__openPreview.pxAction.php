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
 * Resize and output images
 */
class pxObject__openPreview extends pxAction
{
	var $bForceDirect;

	var $sFileIn;
	var $sThumbFilePath;

	var $_bRemote = false;
	var $_bImageMagick = false;
	var $_bImageMagickType = false;
	var $_bGdType = false;
	var $_bDisplayable = false;
	var $_bRemoteFileLoaded = false;

	/**
	 * 
	 */
	function pxObject__openPreview()
	{
		global $pxp;

		$this->bForceDirect = isset($pxp->_GET['forceDirect']);
		$this->bCache = true;

		$this->_bImageMagick = $pxp->oShare->iImageLibrary == 1;
		
		$sCacheId = md5($pxp->_SERVER['QUERY_STRING']);

		if ($this->_bImageMagick) {
			$this->_bImageMagickType = in_array(
				$pxp->oObject->sExtension,
				$pxp->aConfig['aImageMagickExtensions']
			);
		} else {
			$this->_bGdType = in_array(
				$pxp->oObject->sExtension,
				array('png', 'jpg', 'jpe', 'jpeg', 'gif')
			);
		}

		$this->_bDisplayable = in_array(
			$pxp->oObject->sExtension,
			array('png', 'jpg', 'jpe', 'jpeg', 'gif')
		);

		if ($this->_bDisplayable) {
			$this->sMimeType = 'image/' . $pxp->oObject->sExtension;
			$this->sThumbFilePath = $pxp->sCacheDir . '/images/' . $pxp->oShare->sId . '/' . $sCacheId . '.' . $pxp->oObject->sExtension;
		} else {
			$this->sMimeType = 'image/png';
			$this->sThumbFilePath = $pxp->sCacheDir . '/images/' . $pxp->oShare->sId . '/' . $sCacheId . '.png';
		}
		
		$this->sFileIn = $pxp->sFullPathIn;
		
		$this->_bRemote = $pxp->oShare->oVfs->sType != 'pxVfsFilesystem';

		parent::pxAction();

		$this->sendHeaders();
	}

	/**
	 *
	 */
	function outputFile($sFile)
	{
		global $pxp;
		
		#die($pxp->encodeURIParts($sFile));

  	if ($this->bForceDirect) {
			echo $pxp->oShare->oVfs->file_get_contents($sFile);
  	} else {
  		header('Location: ' . $pxp->encodeURIParts($sFile));
  	}
  	exit;
	}

	/**
	 * 
	 */
	function run()
	{
		global $pxp;

		if (!$pxp->oVfs->is_dir($pxp->sCacheDir . '/images/' . $pxp->oShare->sId)) {
			$pxp->oVfs->mkdir($pxp->sCacheDir . '/images/' . $pxp->oShare->sId);
		}

		$sType = $pxp->oObject->sType;
		$sExtension = $pxp->oObject->sExtension;

		$sFullName = $sType;
		if (!empty($sExtension)) {
			$sFullName .= '_' . $sExtension;
		}

		if(
			!file_exists($this->sThumbFilePath)
			||
			$pxp->oShare->oVfs->filemtime($this->sFileIn) > filemtime($this->sThumbFilePath)
			||
			true
		){

			$iMaxWidth = (int)$pxp->_GET['iWidth'];
			if (empty($iMaxWidth)) {
				$iMaxWidth = 100;
			}
			$iMaxHeight = (int)$pxp->_GET['iHeight'];
			if (empty($iMaxHeight)) {
				$iMaxHeight = 100;
			}
			
			$bResize = false;

			if ($this->_bImageMagickType || $this->_bGdType) {

				if ($this->bForceDirect)
				{
					$this->_getRemoteFile();
					$aSize = getimagesize($this->sFileIn);
					$iImageWidth = $aSize[0];
					$iImageHeight = $aSize[1];
				}
				else
				{
					$sPath = $this->sFileIn;
					if ($this->_bRemote) {
						$sPath = $pxp->encodeURIParts(
							pxUtil::str_replace_once(
								$pxp->oShare->sBaseDir,
								$pxp->oShare->sUrl . '/',
								$sPath
							)
						);
					}	
					$aSize = getimagesize($sPath);
					$iImageWidth = $aSize[0];
					$iImageHeight = $aSize[1];
				}

				if ($iImageWidth > $iImageHeight) {
					$bResize = $iImageWidth > $iMaxWidth;
				} else {
					$bResize = $iImageHeight > $iMaxHeight;
				}

				if ($bResize) {
					$this->_getRemoteFile();
				}

				if ($this->_bImageMagick and $this->_bImageMagickType) {

					if ($this->_bDisplayable and !$bResize) {
						$this->outputFile(	
							#$pxp->encodeURIParts(
								pxUtil::str_replace_once(
									$pxp->oShare->sBaseDir,
									$pxp->oShare->sUrl . '/',
									$this->sFileIn
								)
							#)
						);
					} else {

						exec(
							'convert ' . escapeshellarg($this->sFileIn . '[0]') .
							' -resize "' . $iMaxWidth . 'x' . $iMaxHeight . '>"' .
							' -quality ' . $pxp->oShare->iThumbnailQuality .
							($bResize ? ' -sharpen 1.2x1+4+0' : '') .
							' "' . $this->sThumbFilePath . '"'
						);
					}

					$this->outputFile($this->sThumbFilePath);

				} else {

					$bGd2 = $this->_bGdVersion2();
	
					if ($bResize) {

						switch ($pxp->oObject->sExtension) {
							case 'jpg': case 'jpeg': case 'jpe':
								$oImageIn = imagecreatefromjpeg($this->sFileIn);
								break;
							case 'png':
								$oImageIn = imagecreatefrompng($this->sFileIn);
								break;
							case 'gif':
								$oImageIn = imagecreatefromgif($this->sFileIn);
								break;
						}
				
						$iFactorX = $iMaxWidth / $iImageWidth;
						$iFactorY = $iMaxHeight / $iImageHeight;
				
						if ($iFactorY > $iFactorX) { // horizontal
				
							$iNewY = $iImageHeight * ($iMaxWidth / $iImageWidth);
									
							if ($bGd2) {
								$oImageOut = imagecreatetruecolor($iMaxWidth, $iNewY);
								imagecopyresampled($oImageOut, $oImageIn, 0, 0, 0, 0, $iMaxWidth, $iNewY, $iImageWidth, $iImageHeight);
							} else {
								$oImageOut = imagecreate($iMaxWidth, $iNewY);
								imagecopyresized($oImageOut, $oImageIn, 0, 0, 0, 0, $iMaxWidth, $iNewY, $iImageWidth, $iImageHeight);
							}
				
						} else { // vertical
				
							$iNewX = $iImageWidth * ($iMaxHeight / $iImageHeight);
				
							if ($bGd2) {
								$oImageOut = imagecreatetruecolor($iNewX, $iMaxHeight);
								imagecopyresampled($oImageOut, $oImageIn, 0, 0, 0, 0, $iNewX, $iMaxHeight, $iImageWidth, $iImageHeight);
							} else {
								$oImageOut = imagecreate($iNewX, $iMaxHeight);
								imagecopyresized($oImageOut, $oImageIn, 0, 0, 0, 0, $iNewX, $iMaxHeight, $iImageWidth, $iImageHeight);
							}
						}
						imagedestroy($oImageIn);

				  	switch ($pxp->oObject->sExtension) {
			  			case 'jpg': case 'jpeg': case 'jpe':
								imagejpeg($oImageOut, $this->sThumbFilePath, $pxp->oShare->iThumbnailQuality);
								break;
							case 'png':
								imagepng($oImageOut, $this->sThumbFilePath);
								break;
							case 'gif':
								imagegif($oImageOut, $this->sThumbFilePath);
								break;
				   	}

						if ($this->bForceDirect) {
							switch($pxp->oObject->sExtension){
								case 'jpg': case 'jpeg': case 'jpe':
									imagejpeg($oImageOut, null, $pxp->oShare->iThumbnailQuality);
									break;
								case 'png':
									imagepng($oImageOut);
									break;
								case 'gif':
									imagegif($oImageOut);
									break;
			 	  		}
						} else {
							$this->outputFile($this->sThumbFilePath);
						}
					} else {

						$this->outputFile(
							#$pxp->encodeURIParts(
								pxUtil::str_replace_once(
									$pxp->oShare->sBaseDir,
									$pxp->oShare->sUrl . '/',
									$this->sFileIn
								)
							#)
						);
					}
				}

			} else {

				$sIconPath = $pxp->sModuleDir . '/' . $pxp->aTypes[$sType]->sModule .
					'.pxm/graphics/types/' . $sFullName . '.png';

				$iPosX = (int)($iMaxWidth / 2) - 8;
				$iPosY = (int)($iMaxHeight / 2) - 8;

				$oImageIcon = imagecreatefrompng($sIconPath);

				if ($this->_bGdVersion2()) {
					$oImageOut = imagecreatetruecolor($iMaxWidth, $iMaxHeight);
					$iColorWhite = imagecolorallocate($oImageOut, 255, 255, 255);
					imagefill($oImageOut, 1, 1, $iColorWhite);
					imagecopymerge($oImageOut, $oImageIcon, $iPosX, $iPosY, 0, 0, 16, 16, 100);
				} else {
					$oImageOut = imagecreate($iMaxWidth, $iMaxHeight);
					$iColorWhite = imagecolorallocate($oImageOut, 255, 255, 255);
					imagefill($oImageOut, 1, 1, $iColorWhite);
					imagecopymerge($oImageOut, $oImageIcon, $iPosX, $iPosY, 0, 0, 16, 16, 100);
				}

				imagepng($oImageOut, $this->sThumbFilePath);
				imagedestroy($oImageIcon);
				$this->outputFile($this->sThumbFilePath);
			}

		} else {
			$this->outputFile($this->sThumbFilePath);
		}
		
		if (isset($oImageOut)) {
			imagedestroy($oImageOut);
		}
	}
	
	function _bGdVersion2()
	{
		if (function_exists('gd_info')) {
			$gdInfo = gd_info();
			return strpos($gdInfo['GD Version'], '2.') !== false ? true : false;
		} else {
			return false;
		}
	}
	
	function _getRemoteFile()
	{
		global $pxp;

		if ($this->_bRemoteFileLoaded) {
			return true;
		}
		if ($this->_bRemote) {
			$pxp->oVfs->file_put_contents(
				$this->sThumbFilePath,
				$pxp->oShare->oVfs->file_get_contents($this->sFileIn)
			);
			$this->sFileIn = $this->sThumbFilePath;
		}
		$this->_bRemoteFileLoaded = true;
	}
}

?>
