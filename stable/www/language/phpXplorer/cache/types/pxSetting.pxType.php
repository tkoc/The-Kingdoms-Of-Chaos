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
 * A bundle of permissions, events and parameters to build roles or define local settings for users 
 *
 * @extensions pxSetting
 * @belongsTo pxShare pxData phpXplorer:
 * @defaultActions pxSetting_edit
 * @edit
 */
class pxSetting extends pxMetaFiles{
	/**
	 * Permissions can be either given for a whole type,
	 * a base action or a special action.
	 * 
	 * ('typeID', 'typeID.baseAction', 'typeID.mySpecialAction')
	 *
	 * @var array
	 */
	var $aPermissions = array();

	/**
	 * Events can be intercepted if something happens on type,
	 * base action or action level. An event should trigger
	 * predefined actions to avoid the evaluation of PHP code. 
	 * 
	 * ('type' => 'do()', 'type.baseAction' => 'do()', 'type.mySpecialAction' => 'do()')
	 * 
	 * @var array
	 */
	var $aEvents = array();
	
	/**
	 * 
	 */
	var $aCompiledEvents = array();

	/**
	 * Action parameters
	 * 
	 * ('type.action.parameter' => 123) 
	 * 
	 * @var array
	 */
	var $aParameters = array();

	/* internal */

	/**
	 * User roles array gets fill during setting collection 
	 *
	 * @var array
	 */
	var $aRoles = array('pxEveryone');

	/**
	 * Associative array which stores allowed actions for a type
	 * 
	 * @var array 
	 */
	var $aAllowedActions = array();

	/**
	 * Prevent serialisation of private, runtime and empty members
	 */
	function __sleep() {
		$aVars = array_flip(parent::__sleep());

		unset($aVars['aRoles']);
		unset($aVars['aAllowedActions']);

		return array_keys($aVars);
	}
	var $sType = 'pxSetting';}

?>