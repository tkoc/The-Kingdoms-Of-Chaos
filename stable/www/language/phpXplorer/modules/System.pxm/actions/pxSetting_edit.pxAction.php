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

class pxSetting_edit extends pxAction
{
	var $_sPhpEventCode;

	/**
	 * 
	 */
	function run()
	{
		global $pxp;

		if ($pxp->_POST) {

			$sPermissions = $pxp->getRequestVar('sPermissions');
			if (isset($sPermissions)) {
				$pxp->oObject->aPermissions = array();
				$aPermissions = explode('|', $sPermissions);
				foreach ($aPermissions as $sPermissions) {
					$pxp->oObject->aPermissions[$sPermissions] = true;
				}
			}

			$sEvents = $pxp->getRequestVar('sEvents');
			if (isset($sEvents)) {
				$pxp->oObject->aEvents = array();
				$pxp->oObject->aCompiledEvents = array();
				$aEvents = explode('||', $sEvents);
				foreach ($aEvents as $sEventAssignment) {
					if (!empty($sEventAssignment)) {
						$aParts = explode('|', $sEventAssignment);
						$pxp->oObject->aEvents[$aParts[0]] = $aParts[1];
						$sCompiled = $this->_compileEvent($aParts[1]);
						$pxp->oObject->aCompiledEvents[$aParts[0]] = $sCompiled;
					}
				}
			}

			$pxp->oObject->store();

			#print_r($pxp->oObject);
		}
	}

	/**
	 * Compile XML based event script to executeable PHP code
	 * 
	 * @access private
	 *
	 * @param string $sEventXml XML based event script
	 *
	 * @return string Executeable PHP code
	 */
	function _compileEvent($sEventXml)
	{
		$this->_sPhpEventCode = '';

		$rParser = xml_parser_create();
		xml_set_element_handler($rParser, array(&$this, '_startElement'), array(&$this, '_endElement'));
		xml_set_character_data_handler($rParser, array(&$this, '_characterData'));
		
		if (!xml_parse($rParser, '<handler>' . $sEventXml . '</handler>')) {			
			die(sprintf('XML error: %s at line %d',
				xml_error_string(xml_get_error_code($rParser)),
				xml_get_current_line_number($rParser))
			);
		}

		xml_parser_free($rParser);

		return $this->_sPhpEventCode;
	}

	var $_aActionStack = array();
	var $_iIfDepth = 0;

	/**
	 * @access private
	 */
	function _startElement($rParser, $sName, $aAttributes) {
		global $pxp;

		switch (strtoupper($sName)) {
			case 'IF':
				$this->_sPhpEventCode .= 'if (';
				$this->_iIfDepth++;
				break;
			case 'THEN':
				$this->_sPhpEventCode .= '{';
				break;
			case 'ELSE':
				$this->_sPhpEventCode .= ' else {';
				break;
			case 'ACTION':

				if (!isset($aAttributes['ID'])) {
					die('Missing id attribute of action tag');
				}

				if (isset($pxp->aActions['pxGlobal_' . $aAttributes['ID']])) {
					$aActionStartCode =
						'$pxp->callGlobalAction(\'' . $aAttributes['ID'] . '\', array(';
				} else {
					$aActionStartCode =
						'$this->call(\'' . $aAttributes['ID'] . '\', array(';
				}

				if (empty($this->_aActionStack)) {
					$this->_sPhpEventCode .= $aActionStartCode;
				} else {
					$iLastAction = count($this->_aActionStack) - 1;
					$iLastParam = count($this->_aActionStack[$iLastAction][1]) - 1;
					$this->_aActionStack[$iLastAction][1][$iLastParam][1] .=
						chr(39) . ' . ' . $aActionStartCode;
				}

				$this->_aActionStack[] = array($aAttributes['ID'], array());

				break;
			case 'PARAM':

				if (!isset($aAttributes['ID'])) {
					die('Missing param tag ID');
				}

				$iLastAction = count($this->_aActionStack) - 1;

				switch ($aAttributes['ID']) {
					case 'sTriggeringAction':
						$this->_aActionStack[$iLastAction][1][] = array(
							$aAttributes['ID'],
							'$sAction', // value
							true // filled with PHP
						);
						break;
					default:
						$this->_aActionStack[$iLastAction][1][] = array(
							$aAttributes['ID'],
							null, // value
							false // filled with PHP
						);
						break;
				}
				break;
		}
	}

	/**
	 * @access private
	 */
	function _endElement($rParser, $sName) {

		switch ($sName) {
			case 'HANDLER':
				#$this->_sPhpEventCode .= ';';
				break;
			case 'IF':
				$this->_sPhpEventCode .= ')';
				$this->_iIfDepth--;
				break;
			case 'THEN':
				$this->_sPhpEventCode .= '}';
				break;
			case 'ELSE':
				$this->_sPhpEventCode .= '}';
				break;
			case 'ACTION':

				$sActionEndCode = '';
				$aLastAction = array_pop($this->_aActionStack);

				for ($a = 0, $m = count($aLastAction[1]); $a < $m; $a++) {
					if (is_numeric($aLastAction[1][$a][1]) or $aLastAction[1][$a][2] == true) {
						$sActionEndCode .=
							chr(39) . $aLastAction[1][$a][0] . chr(39) . ' => ' . $aLastAction[1][$a][1];
					} else {
						$sActionEndCode .=
							chr(39) . $aLastAction[1][$a][0] . chr(39) . ' => ' . chr(39) . $aLastAction[1][$a][1] . chr(39);
					}
					if ($a + 1 < $m) {
						$sActionEndCode .= ', ';
					}
				}
				$sActionEndCode .= '))';

				if (empty($this->_aActionStack)) {

					$this->_sPhpEventCode .= $sActionEndCode;
				
					if ($this->_iIfDepth == 0) {
						$this->_sPhpEventCode .= ';';
					}

				} else {

					$iLastAction = count($this->_aActionStack) - 1;
					$iLastParam = count($this->_aActionStack[$iLastAction][1]) - 1;

					$this->_aActionStack[$iLastAction][1][$iLastParam][1] .=
						$sActionEndCode . ' . ' . chr(39);
				}

				break;
		}
	}

	/**
	 * @access private
	 */
	function _characterData($rParser, $sData){
		
		if (trim($sData) != '') {
			$iLastAction = count($this->_aActionStack) - 1;
			if (isset($this->_aActionStack[$iLastAction])) {
				$iLastParam = count($this->_aActionStack[$iLastAction][1]) - 1;

				if (
					in_array(
						$this->_aActionStack[$iLastAction][1][$iLastParam][0],
						array('sTriggeringAction')
				)) {
					return false;
				}

				if (is_numeric($sData)) {
					$this->_aActionStack[$iLastAction][1][$iLastParam][1] .= $sData;
				} else {
					$this->_aActionStack[$iLastAction][1][$iLastParam][1] .= addslashes($sData);
				}
			}
		}
	}
}

?>