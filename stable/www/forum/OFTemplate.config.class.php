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
/**********************************************************
* File:		OFTemplate.config.class.php
* Author:	Øystein Fladby
* Date:		26.11.2003
* Version:	1.0
* Description:
*		This file contains the class OFTemplateConfig. 
*		The class contains constants to be used in the @OFTemplate 
* 		class. Change these constants to fit your use in the 
* 		HTML code.
*		OBS! The Tags and the three Headers must be a combination 
*		of symbols you never use elsewhere in the HTML.
**********************************************************/


if( !class_exists( "OFTemplateConfig" ) ) {

class OFTemplateConfig{
	var $templateOpeningTag		= "{-";						// Start tag for all variables/dynamics
	var $templateClosingTag		= "-}";						// End tag for all variables/dynamics
	
	var $variableHeader 			= "V:";			// Identifier for a variable
	var $startDynamicHeader 	= "SD:";	// Identifier for the start of a dynamic block
	var $endDynamicHeader		= "ED:";		// Identifier for the end of a dynamic block
} // end class OFTemplateConfig

$GLOBALS['OFTemplateConfig'] = new OFTemplateConfig();
} //end if ! class exists
?>