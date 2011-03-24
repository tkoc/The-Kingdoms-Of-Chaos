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

require_once dirname(__FILE__) . '/../actions/pxObject__editProperty.pxAction.php';

class pxObject_editProperties extends pxAction
{
	/**
	 * Array of object properties
	 * 
	 * @access protected
	 * @var array Array of pxProperty objects
	 */
	var $aProperties = array();

	/**
	 * Main method to start execution of action
	 */
	function run()
	{
		global $pxp;
		
		$pxp->loadType('pxProperty');

		$this->aProperties = $pxp->aTypes[$pxp->oObject->sType]->getAllProperties();

#echo $pxp->oObject->sType;

		if (!empty($pxp->_POST))
		{
			$aErrors = array();

			foreach ($this->aProperties as $sId => $oProperty) {
				pxObject__editProperty::setProperty(
					$oProperty,
					$aErrors
				);
			}

			$this->storeObject($aErrors);
		}
	}
}

?>