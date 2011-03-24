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

class pxObject__editProperty extends pxAction
{
	/**
	 * @access protected
	 * @var string
	 */
	var $sProperty = array();

	/**
	 * Array of object properties
	 * 
	 * @access protected
	 * @var array Array of pxProperty objects
	 */
	var $aProperties = array();

	/**
	 * 
	 */
	function pxObject__editProperty()
	{
		global $pxp;
		
		$pxp->loadType('pxProperty');

		$this->sProperty = $pxp->getRequestVar('sProperty');
		$this->aProperties = $pxp->aTypes[$pxp->oObject->sType]->getAllProperties();
		parent::pxAction();
	}

	function setProperty($oProperty, &$aErrors)
	{
		global $pxp;

		if ($oProperty->sMode == 'edit') {

			$mValue = $pxp->getRequestVar('px_' . $oProperty->sId);

			// Validation

			if (isset($oProperty->sValidation)) {

				require_once $pxp->sModuleDir . '/System.pxm/includes/PEAR/Validate.php';

				switch ($oProperty->sValidation) {
				case 'number':
					if (!Validate::number($mValue, $oProperty->aValidationParameters)) {
						$aErrors[$oProperty->sId] = 'invalidNumber';
					}
					break;
				case 'email':
					if (!empty($mValue)) {
						if (!Validate::email($mValue, isset($oProperty->aValidationParameters['check_domain']))) {
							$aErrors[$oProperty->sId] = 'invalidEmail';
						}
					} else {
						if (isset($oProperty->aValidationParameters['required'])) {
							$aErrors[$oProperty->sId] = 'invalidEmail';
						}
					}
					break;
				case 'string':
					if (!Validate::string($mValue, $oProperty->aValidationParameters)) {
						$aErrors[$oProperty->sId] = 'invalidString';
					}
					break;
				case 'uri':
					if (!empty($mValue)) {
						if (!Validate::uri($mValue, $oProperty->aValidationParameters)) {
							$aErrors[$oProperty->sId] = 'invalidUri';
						}
					} else {
						if (isset($oProperty->aValidationParameters['required'])) {
							$aErrors[$oProperty->sId] = 'invalidUri';
						}
					}
					break;
				case 'date':
					if (!Validate::date($mValue, $oProperty->aValidationParameters)) {
						$aErrors[$oProperty->sId] = 'invalidDate';
					}
					break;
				case 'filename':
					if (!empty($mValue)) {
						if (!pxUtil::checkFilename($mValue)) {
							$aErrors[$oProperty->sId] = 'invalidFilename';
						}
					} else {
						if (isset($oProperty->aValidationParameters['required'])) {
							$aErrors[$oProperty->sId] = 'invalidFilename';
						}
					}
					break;
				case 'equalTo':
					$mValue2 = $pxp->getRequestVar('px_' . $oProperty->aValidationParameters['compareTo']);
					if ($mValue != $mValue2) {
						$aErrors[$oProperty->sId] = 'passwordsNotEqual';
					}
				break;
				default:
					$pxp->raiseError('unknownValidationMethod', __FILE__, __LINE__, array($oProperty->sValidation));
					break;
				}
			}
			switch ($this->aProperties[$oProperty->sId]->sDataType) {
			case 'array':
				if (is_array($mValue)) {
					$pxp->oObject->setValue($oProperty->sId, $mValue);
				} else {
					if (trim($mValue) != '') {
						$mValue = str_replace(chr(13) . chr(10), chr(13), $mValue);
						$mValue = str_replace(chr(10), chr(13), $mValue);
						$aValuesIn = explode(chr(13), $mValue);
						$aValues = array();
						foreach ($aValuesIn as $sValue) {
							if (!empty($sValue)) {
								$aValues[] = $sValue;
							}
						}
						$pxp->oObject->setValue($oProperty->sId, $aValues);
					} else {
						$pxp->oObject->setValue($oProperty->sId, null);
					}
				}
			break;
			case 'integer':
			case 'int':
				$pxp->oObject->setValue($oProperty->sId, (int)$mValue);
			break;
			case 'boolean':
			case 'bool':
			case 'checkbox':
				$pxp->oObject->setValue($oProperty->sId, $mValue == 'true');
			break;
			default: // string
				$pxp->oObject->setValue($oProperty->sId, $mValue);
			break;
			}
		}
	}

	/**
	 * Main method to start execution of action
	 */
	function run()
	{
		global $pxp;

		if ($pxp->_POST)
		{
			$aErrors = array();

			$this->setProperty(
				$this->aProperties[$this->sProperty],
				$aErrors
			);
			
			$this->storeObject($aErrors);
		}
	}
}

?>