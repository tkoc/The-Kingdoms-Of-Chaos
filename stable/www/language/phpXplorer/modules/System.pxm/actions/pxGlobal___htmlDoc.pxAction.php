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

/* Copyright notice */

/**
 * Class for OO generation of XHTML documents 
 * @abstract
 */
class pxGlobal___htmlDoc extends pxAction {
	/**
	 * Document title
	 * 
	 * @access protected
	 * @var string
	 */
	var $sTitle;

	/**
	 * XHTML doctype
	 *
	 * html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
	 * html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"
	 * html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd"
	 *
	 * @access protected
	 * @var string
	 */
	var $sDoctype;

	/**
	 * Language
	 * 
	 * @access protected
	 * @var string
	 */
	var $sLanguage;
	
	/**
	 * Body tag HTML id
	 * 
	 * @access public
	 * @var string
	 */
	var $sBodyId;
	
	var $sHtmlId;

	/**
	 * HTML body content
	 * 
	 * @access protected
	 * @var string
	 */
	var $sBody = '';

	/**
	 * List code for inclusion of external JavaScript and CSS files
	 * 
	 * @access private
	 * @var array
	 */
	var $_aIncludes = array();

	/**
	 * Internal Cascading Style Sheet code
	 * 
	 * @access private
	 * @var string
	 */
	var $_sCSS = '';

	/**
	 * Internal JavaScript code
	 * 
	 * @access private
	 * @var string
	 */
	var $_sJS = '';

	/**
	 * Holds form code if set
	 * 
	 * @access private
	 * @var string
	 */
	var $_sForm;

	/**
	 * Array with hidden form elements to store phpXplorer variables like share, path and action 
	 * 
	 * @access private
	 * @var array
	 */
	var $_aVariables = array();
	
	/**
	 * Must be set to true if output should be a frameset
	 * 
	 * @access protected
	 * @var boolean
	 */
	var $bFrameset = false;

	function pxGlobal___htmlDoc($bSystemIncludes = true)
	{
		global $pxp;

		parent::pxAction();

		$this->sTitle = 'phpXplorer';
		$this->sDoctype = 'html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"';

		if ($bSystemIncludes)
		{
			if ($pxp->aConfig['bDevelopment'])
			{
				require_once $pxp->sModuleDir . '/System.pxm/pxCompiler.php';

				$oModules = pxCompiler::getModules(true);

				foreach ($oModules as $oModule) {
					if (!$oModule->bIndipendent) {
						if (is_array($oModule->aCssIncludes)) {
							foreach ($oModule->aCssIncludes as $sCssInclude) {
								$this->addStyle($pxp->sModuleUrl . '/' . $oModule->sName . '/' . $sCssInclude);
							}
						}
						if (is_array($oModule->aJsIncludes)) {
							foreach ($oModule->aJsIncludes as $sJsInclude) {
								$this->addScript($pxp->sModuleUrl . '/' . $oModule->sName . '/frontend/' . $sJsInclude);
							}
						}
					}
				}
			} else {
				$this->addStyle('phpXplorer.css');
				$this->addScript('phpXplorer.js');
			}

			$this->addStyle('modules/System.pxm/ie.css', 'screen', 'IE');

			$this->addScriptUrl($pxp->sUrl . '/cache/types.js');
			$this->addScriptUrl($pxp->sUrl . '/cache/actions.js');
			#$this->addScriptUrl($pxp->sUrl . '/cache/documentation.js');
			$this->addScriptUrl($pxp->sUrl . '/cache/translations/' . $pxp->sLanguage . '.js');
			

			$this->addScriptCode(
				#'pxp.sClient = "' . $pxp->sClient . '";' .
				'pxp.sShare = "' . $pxp->sShare . '";' .
				'pxp.sRelPathIn = "' . $pxp->sRelPathIn . '";' .
				'pxp.sRelDir = "' . $pxp->sRelDir . '";' .
				'pxp.sEncoding = "' . $pxp->aConfig['sEncoding'] . '";' .
				'pxConst.sModuleUrl = "' . $pxp->sModuleUrl . '";' .
				'pxConst.sGraphicUrl = "' . $pxp->sModuleUrl . '/System.pxm/graphics";'
			);
		}
	}

	/**
	 * Generate XHTML document
	 *
	 * @param array $bSeparate Returns an array with document parts if set to true
	 * @return mixed String or array depending on $bSeparate
	 */
	function generate($bSeparate = false)
	{
		global $pxp;

		$sMeta =
			'<?xml version="1.0" encoding="' . $this->sEncoding . '"?>' .
			'<!DOCTYPE ' . $this->sDoctype . '>' .
			'<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . $this->sLanguage . '"' . (isset($this->sHtmlId) ? ' id="' . $this->sHtmlId . '"' : '') . '>' .
			'<head><title>' . $this->sTitle . '</title>' .
			'<meta name="generator" content="phpXplorer" />' .
			'<meta http-equiv="content-type" content="' . $this->sMimeType . ';charset=' . $this->sEncoding . '" />'
		;

		$sHead = implode('', $this->_aIncludes);
		
		if ($pxp->aConfig['bDebug']) {
			$this->addScriptCode('if(window.pxp)pxp.iPhpRuntime=' . (pxUtil::getMicrotime() - $pxp->iStartTime)) ;
		}

		if (!empty($this->_sJS)) {
			$sHead .= '<script type="text/javascript">' .
				$this->_sJS .
			'</script>';
		}

		if (!empty($this->_sCSS)) {
			$sHead .= '<style type="text/css">' .
				$this->_sCSS .
			'</style>';
		}

		$sBody = '</head>';

		$sContent = '';

		if (!$this->bFrameset) {
			$sBody .= '<body' . (isset($this->sBodyId) ? ' id="' . $this->sBodyId . '"' : '') . '>';

			if (isset($this->_sForm)) {
				$sContent .= $this->_sForm;
			}
		}

		$sContent .= $this->sBody;

		if (isset($this->_sForm) and !$this->bFrameset) {
			$sContent .= '</form>';
		}

		if ($bSeparate) {
			return array(
				'head' => $sHead,
				'content' => $sContent
			);
		} else {

			$sHtml = $sMeta . $sHead . $sBody . $sContent;

			if (!$this->bFrameset) {
				$sHtml .= '</body>';
			}

			return $sHtml . '</html>';
		}
	}

	/**
	 * Start XHTML document generation and send it to the browser
	 */
	function run()
	{
		global $pxp;

		//parent::run();
		$this->sendHeaders();

		$this->buildHead();
		$this->buildBody();
		$this->buildFoot();

		echo $this->generate();
	}

	/**
	 * Adds an external JavaScript file to the document
	 * 
	 * @param string $sUrl URL of the file to include
	 */
	function addScriptUrl($sUrl) {
		$this->_aIncludes[] = '<script src="' . $sUrl . '" type="text/javascript"></script>' . "\n";
	}

	/**
	 * Adds an external JavaScript file to the document
	 * 
	 * @param string $sName Name of the script that should be included from the module/includes directory
	 */
	function addScript($sUrlIn, $sModule = 'System')
	{
		global $pxp;
		
		if (strpos($sUrlIn, $pxp->sUrl) !== 0) {
			$sUrl = pxUtil::buildPath($pxp->sUrl, $sUrlIn);
		} else {
			$sUrl = $sUrlIn;
		}
		$this->_aIncludes[] = '<script src="' . $sUrl . '" type="text/javascript"></script>' . "\n";
	}

	/**
	 * Adds an external CSS file to the document
	 *
	 * @param string $sName Name of the sheet that should be included from the module/theme directory
	 * @param string $sMedia Output media (http://en.selfhtml.org/css/formate/einbinden.htm#link_media)
	 */
	function addStyle($sPath, $sMedia = 'screen', $sConditional = null)
	{
		global $pxp;

		$sUrl = $pxp->sUrl . '/' . $sPath;
		
		$sHtml = '';
		
		if (isset($sConditional)) {
			$sHtml .= '<!--[if ' . $sConditional . ']>';
		}

		$sHtml .= '<link rel="stylesheet" type="text/css" media="' . $sMedia . '" href="' . $sUrl . '" />' . "\n";

		if (isset($sConditional)) {
			$sHtml .= '<![endif]-->'; 
		}

		$this->_aIncludes[] = $sHtml;
	}

	/**
	 * Adds JavaScript code to the document
	 *
	 * @param string $sCode JavaScript code
	 */
	function addScriptCode($sCode, $bPrepend = false) {
		if ($bPrepend) {
			$this->_sJS = $sCode . $this->_sJS;
		} else {
			$this->_sJS .= $sCode;
		}
	}

	/**
	 * Adds CSS code to the document
	 * 
	 * @param string $sCode CSS code
	 */
	function addStyleCode($sCode) {
		$this->_sCSS .= $sCode;
	}

	/**
	 * Add/configure form that encloses the whole document body
	 * 
	 * @param string $sAction HTML form action
	 * @param string $sMethod HTML form method
	 * @param string $sTarget HTML form target
	 */
	function setForm($sAction = null, $sMethod = 'post', $sTarget = null)
	{
		global $pxp;

		if (!isset($sAction)) {
			$sAction = $pxp->sUrl . '/action.php';
		}
		
		$sAction = str_replace('&', '&amp;', $sAction);

		$this->_sForm =
			'<form action="' . $sAction . '" ' .
			'method="' . $sMethod . '" ' .
			'onsubmit="return false" accept-charset="' . $this->sEncoding . '"';

		if (isset($sTarget)) {
			$this->_sForm .= ' target="' . $sTarget . '"';
		}

		if ($sMethod == 'post') {
			$this->_sForm .= ' enctype="multipart/form-data"';
		}

		$this->_sForm .= '>';
	}

	/**
	 * @abstract
	 */
	function buildHead()
	{
	}

	/**
	 * @abstract
	 */
	function buildBody()
	{
	}

	/**
	 * @abstract
	 */
	function buildFoot()
	{
	}
}


?>