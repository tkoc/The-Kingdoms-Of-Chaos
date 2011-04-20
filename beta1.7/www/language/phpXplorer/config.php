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

// This file is build up as PHP array.
// Assignments have to be comma separated.


// Do not edit this line
$this->aConfig = array_merge($this->aConfig, array(

#'bDebug' => false,
  // Show debug pane. Show full error messages with filename and line number.


#'bDevelopment' => false,
  // Show cache pane (F8) to rebuild the application level cache.
  // All JavaScript and CSS files are included separately.


'aContact' => array(
	'sOrganisation' => 'phpXplorer',
	'sOrganisationInfo' => '~subTitle',
	'sDepartment' => '',
	'sPerson' => 'Tobias Bender',
	'sStreet' => 'Horngasse 6',
	'sCity' => '52064 Aachen',
	'sCountry' => 'Germany',
	'sTelefon' => '+49 241 450 8005',
	'sTelefax' => '',
	'sEmail' => 'tobias@phpxplorer.org'
),
	// Contact form information.
	// All properties get listed as they appear. Just add or change properties as you need.
	// sEmail is required if you will make use of the contact form.


#'bContact' => true,
	// Show contact form


#'sSystemLanguage' => 'en',
  // Used if there is no translation in the requested language.
  // Each translation file gets commented with this language.
  // Default interface language for new users. The interface
  // language can be set individually for/by each user in its profile.
	// Keywords of this language are used to extend all translation files.


#'bCreateHomes' => false,
  // Create a personal folder (home) for each user below ../phpXplorer/homes.


#'sEncoding' => 'utf-8',
  // Has to be set depending on the charset of your translation and filesystem data (Russian -> windows-1251) 


#'sAuthentication' =>  'phpXplorer://default.pxAuthenticationHtpasswd',
  // Leave empty for no authentication.


#'sNoAuthUser' => 'root',
  // User if there is no authentication.


#'sUMask' => '0000',

#'sMkDirMode' => 0755,


#'sDefaultBookmark' => 'Demo',
  // Bookmark/Share to load on startup

	
#'aImageMagickExtensions' => array('jpg', 'jpeg', 'gif', 'tif', 'tiff', 'png', 'bmp', 'psd', 'svg', 'pdf'),
  // List of file extensions that could be displayed by ImageMagick


#'aGdExtensions' => array('jpg', 'jpeg', 'gif', 'png'),


#'aContentLanguages' => array('en', 'de'),


#'aModuleOrder' => array('System'),
  // Module import order


#'sLogoUrl' => './modules/Customization.pxm/graphics/logo.png',

'sId' => '##ID##',

'sVersion' => 'px3Preview'

// Do not edit this line
));

?>