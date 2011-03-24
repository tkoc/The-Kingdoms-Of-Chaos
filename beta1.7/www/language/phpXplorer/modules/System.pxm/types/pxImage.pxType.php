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

require_once dirname(__FILE__) . '/pxBinaryFiles.pxType.php';

/**
 * @defaultActions pxFiles_openPreview
 * @extensions
 *   jpg => image/jpeg
 *   jpeg => image/jpeg
 *   gif => image/gif
 *   tif => image/tiff
 *   tiff => image/tiff
 *   png => image/png
 *   bmp => image/bmp
 *   psd => application/octet-stream
 */
class pxImage extends pxBinaryFiles
{
	/**
	 * @var integer Image width in pixel
	 * @view Input(unit=px)
	 * @validate number(min=0)
	 */
	var $iWidth = 0;

	/**
	 * @var integer Image height in pixel
	 * @view Input(unit=px)
	 * @validate number(min=0)
	 */
	var $iHeight = 0;

	/**
	 * @var string
	 * @edit Input
	 */
	var $sOriginator = '';

	/**
	 * Format: x1,y1-x2,y2
	 * @var string
	 * @edit ImageClipping
	 */
	var $sPreviewClipping = '';

	/**
	 * Fill members of this object with extracted file meta data
	 */
	function loadFileMetaData()
	{
		global $pxp;

		$oVfs =& $pxp->aShares[$this->sShare]->oVfs; 

		$aInfo = $oVfs->getimagesize($this->getFullPath());

		if (isset($aInfo[0])) $this->iWidth = $aInfo[0]; 
		if (isset($aInfo[1])) $this->iHeight = $aInfo[1];

		if ($pxp->moduleExists('JpegMetadataToolkit') and $this->sType == 'pxJpeg') {
			
			$globals['HIDE_UNKNOWN_TAGS'] = true;
			
			$sModuleDir = $pxp->sModuleDir . '/JpegMetadataToolkit.pxm/includes/';

			include_once $sModuleDir . 'JPEG.php';
			include_once $sModuleDir . 'EXIF.php';
			include_once $sModuleDir . 'JFIF.php';
			include_once $sModuleDir . 'Photoshop_IRB.php';
			include_once $sModuleDir . 'PictureInfo.php';
			include_once $sModuleDir . 'XMP.php';

			include_once $sModuleDir . 'Photoshop_File_Info.php';
			
			# ! auf ftp vorbereiten

			$jpeg_header_data = get_jpeg_header_data($this->getFullPath());
			
			$aTags = get_photoshop_file_info(
				get_EXIF_JPEG(
					$this->getFullPath()
				),
				read_XMP_array_from_text(
					get_XMP_text($jpeg_header_data)
				),
				get_Photoshop_IRB($jpeg_header_data)
			);

			#print_r($aTags);

			if (isset($aTags['title']) && trim($aTags['title']) != '') {
				$this->sTitle = $aTags['title'];
			}

			if (isset($aTags['caption']) && trim($aTags['caption']) != '') {
				$this->sDescription = $aTags['caption'];
			}

			#print_r(get_jpeg_App12_Pic_Info($jpeg_header_data));
			#print_r(get_Photoshop_IRB($jpeg_header_data));
			#print_r(get_Meta_JPEG($this->getFullPath())); 
			#print_r(get_EXIF_JPEG($this->getFullPath()));
			#print_r(read_XMP_array_from_text(get_XMP_text($jpeg_header_data)));

		} else {

			// IPTC

			if (isset($aInfo['APP13'])) {
				#$aIptc = iptcparse($aInfo['APP13']);
				#print_r($aIptc);
			}
		}
	}

	/**
	 * Write meta data of this object into the file
	 */
	#function writeFileMetaData(){
		
	#}
}

?>