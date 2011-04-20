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

$this->aLanguages['es'] = array_merge($this->aLanguages['es'], array(
  'type.pxXml' => '', // XML files
  'type.pxXml_xhtml' => '', // XHTML file
  'type.pxXml_xml' => '', // XML file
  'property.bSearchMatchCase' => '', // Match case
  'property.sQuery' => '', // Query
  'type.pxVirtualDirectory' => '', // Virtual directory
  'type.pxVirtualDirectory_pxv' => '', // Virtual directory
  'type.pxVideo' => '', // Video files
  'type.pxVideo_fla' => '', // Macromedia Flash
  'type.pxVideo_swf' => '', // Shockwave Flash
  'type.pxVideo_mov' => '', // Quicktime video
  'type.pxVideo_wmv' => '', // WMV video
  'type.pxVideo_avi' => '', // AVI video
  'type.pxVideo_mpeg' => '', // MPEG video
  'type.pxVideo_mpg' => '', // MPEG video
  'property.bSSL' => '', // SSL
  'property.bPassive' => '', // Passive
  'property.iPort' => '', // Port
  'property.sPassword' => '', // Password
  'property.sUser' => '', // User
  'property.sHost' => '', // Host
  'type.pxVfsFtp' => '', // FTP driver
  'type.pxVfsFilesystem' => '', // OS driver
  'property.sPasswordConfirm' => '', // Password confirm
  'property.sPassword' => '', // Password
  'type.pxVfsAuthenticationUser' => '', // User
  'type.pxVfsAuthenticationUser_pxAuthUser' => '', // User
  'type.pxVfsAuthentication' => '', // Authentification driver
  'property.bDbPaging' => '', // Database paging
  'property.bOwnerPermission' => '', // Owner permissions
  'property.iEvalShareTree' => '', // Evaluate share tree settings
  'property.bLog' => '', // Write log
  'property.sIndexPassword' => '', // Password
  'property.sIndexUser' => '', // User
  'property.sIndexPropertyTable' => '', // Index property table
  'property.sIndexTable' => '', // Index filesystem table
  'property.sIndexDSN' => '', // Index datasource
  'property.bIndexed' => '', // Indexed
  'type.pxVfs' => '', // Virtual File System
  'property.aRoles' => '', // Roles
  'type.pxUser' => '', // User share
  'property.aExpandSubtypes' => '', // Expand subtypes
  'property.aDefaultActions' => '', // Default actions
  'property.sSupertype' => '', // Supertype
  'property.aContainertypes' => '', // Container types
  'property.aContenttypes' => '', // Content types
  'property.aMimeTypes' => '', // MIME types
  'property.aExtensions' => '', // Extensions
  'property.bAbstract' => '', // Abstract
  'property.bCreate' => '', // Create
  'property.bDirectory' => '', // Directory
  'type.pxType' => '', // Type
  'type.pxType_pxType.php' => '', // Type
  'type.pxTextFiles' => '', // Text files
  'type.pxText' => '', // Text file
  'type.pxText_serializedPhp' => '', // Serialized PHP Object
  'type.pxText_css' => '', // CSS file
  'type.pxText_ini' => '', // INI file
  'type.pxText_txt' => '', // Text file
  'type.pxSvg' => '', // SVG file
  'type.pxSvg_svg' => '', // Scalable Vector Graphics
  'property.aAllUsersRoles' => '', // Role(s) of all users
  'property.sBaseType' => '', // Base type
  'property.iImageResize' => '', // Image resizing
  'property.bLiveSearch' => '', // Live search
  'property.bUnsecuredImageAccess' => '', // Unsecured thumbnail access
  'property.iThumbnailQuality' => '', // Thumbnail quality
  'property.iImageLibrary' => '', // Image library
  'property.iThumbnailSize' => '', // Thumbnail size
  'property.iRestrictWebserverAccess' => '', // Create .htaccess
  'property.sTreeviewWidth' => '', // Treeview width
  'property.sStartpage' => '', // Startpage
  'property.iSearchResultsPerPage' => '', // Search results per page
  'property.iObjectsPerPage' => '', // Objects per page
  'property.bFullTree' => '', // Tree with files
  'property.sDefaultView' => '', // Default view
  'property.sDefaultSelection' => '', // Default selection
  'property.bSelectionView' => '', // Show selection view
  'property.bUnicodeUrl' => '', // Use UTF8 encoded URLs
  'property.sUrl' => '', // URL
  'property.sBaseDir' => '', // Base directory
  'property.sVfs' => '', // Filesystem
  'type.pxSetting' => '', // Setting
  'type.pxScript' => '', // Source code
  'type.pxScript_as' => '', // ActionScript source
  'type.pxScript_java' => '', // Java source
  'type.pxScript_sql' => '', // SQL source
  'type.pxScript_pl' => '', // Perl source
  'type.pxScript_rb' => '', // Ruby source
  'type.pxScript_py' => '', // Python source
  'type.pxScript_asp' => '', // ASP source
  'type.pxScript_js' => '', // JavaScript source
  'property.sParentRole' => '', // Parent role
  'type.pxRole' => '', // Role
  'property.sStore' => '', // Store
  'property.sPermission' => '', // Permission
  'property.aFormatParameters' => '', // Format parameter
  'property.sFormat' => '', // Format
  'property.aValidationParameters' => '', // Validation parameter
  'property.sValidation' => '', // Validation
  'property.aParameters' => '', // Parameters
  'property.sValue' => '', // Value
  'property.sWidget' => '', // Widget
  'property.sDataType' => '', // Data type
  'property.sMode' => '', // Mode
  'type.pxProperty' => '', // pxp property
  'property.iLastLogout' => '', // Last log out
  'property.iLastLogin' => '', // Last log in
  'property.aBookmarks' => '', // Bookmarks
  'property.sFrontendLanguage' => '', // Language
  'property.sEmail' => '', // Email address
  'property.sFullName' => '', // Full name
  'type.pxProfile' => '', // User profile
  'type.pxPhpClass' => '', // PHP class
  'type.pxPhp' => '', // PHP file
  'type.pxPhp_phtml' => '', // PHP
  'type.pxPhp_php5' => '', // PHP
  'type.pxPhp_php4' => '', // PHP
  'type.pxPhp_php' => '', // PHP
  'type.pxPdf' => '', // PDF file
  'type.pxPdf_pdf' => '', // Portable Document Format
  'type.pxOpenDocument' => '', // OpenDocument files
  'type.pxOpenDocument_odb' => '', // OpenDocument Database
  'type.pxOpenDocument_odg' => '', // OpenDocument Drawing
  'type.pxOpenDocument_odp' => '', // OpenDocument Presentation
  'type.pxOpenDocument_ods' => '', // OpenDocument Spreadsheet
  'type.pxOpenDocument_odt' => '', // OpenDocument Text
  'type.pxOffice' => '', // Office document
  'property.sType' => '', // Type
  'property.sRelDir' => '', // Directory
  'property.sName' => '', // Name
  'type.pxObject' => '', // Object
  'type.pxMsOffice' => '', // Microsoft Office filess
  'type.pxMsOffice_rtf' => '', // RTF document
  'type.pxMsOffice_ppt' => '', // Microsoft Powerpoint
  'type.pxMsOffice_xls' => '', // Microsoft Excel
  'type.pxMsOffice_doc' => '', // Microsoft Word
  'property.bIndipendent' => '', // Indipendent
  'property.aJsIncludes' => '', // JavaScript files
  'property.aCssIncludes' => '', // CSS files
  'property.aTutorials' => '', // Tutorials
  'property.sDescription' => '', // Description
  'property.sVersion' => '', // Version
  'property.sOwnerMail' => '', // Email address
  'property.sOwner' => '', // Owner
  'type.pxModule' => '', // Module
  'type.pxModule_pxm' => '', // Module
  'property.fPosition' => '', // Position
  'property.bVisible' => '', // Visible
  'property.bActive' => '', // Active
  'property.dCreated' => '', // Created
  'property.aTags' => '', // Tags
  'property.sDescription' => '', // Description
  'property.sTitle' => '', // Title
  'property.sLanguage' => '', // Language
  'property.sOwner' => '', // Owner
  'property.dModified' => '', // Modified
  'property.iBytes' => '', // Bytes
  'property.bDirectory' => '', // Directory
  'type.pxMetaFiles' => '', // Files with meta data
  'property.fPosition' => '', // Position
  'property.bVisible' => '', // Visible
  'property.bActive' => '', // Active
  'property.dCreated' => '', // Created
  'property.aTags' => '', // Tags
  'property.sDescription' => '', // Description
  'property.sTitle' => '', // Title
  'property.sLanguage' => '', // Language
  'property.sOwner' => '', // Owner
  'property.dModified' => '', // Modified
  'property.iBytes' => '', // Bytes
  'property.bDirectory' => '', // Directory
  'type.pxMetaDirectories' => '', // Directories with meta data
  'property.fPosition' => '', // Position
  'property.bVisible' => '', // Visible
  'property.bActive' => '', // Active
  'property.dCreated' => '', // Created
  'property.aTags' => '', // Tags
  'property.sDescription' => '', // Description
  'property.sTitle' => '', // Title
  'property.sLanguage' => '', // Language
  'property.sOwner' => '', // Owner
  'property.dModified' => '', // Modified
  'property.iBytes' => '', // Bytes
  'property.bDirectory' => '', // Directory
  'type.pxMeta' => '', // Meta data
  'property.sPreviewClipping' => '', // Preview clipping
  'property.sOriginator' => '', // Originator
  'property.iHeight' => '', // Height
  'property.iWidth' => '', // Width
  'type.pxImage' => '', // Images
  'type.pxImage_psd' => '', // Photoshop document
  'type.pxImage_bmp' => '', // BMP image
  'type.pxImage_png' => '', // PNG image
  'type.pxImage_tiff' => '', // Tagged Image File Format
  'type.pxImage_tif' => '', // Tagged Image File Format
  'type.pxImage_gif' => '', // GIF image
  'type.pxImage_jpeg' => '', // JPEG image
  'type.pxImage_jpg' => '', // JPEG image
  'type.pxHtml' => '', // HTML file
  'type.pxHtml_shtml' => '', // Server Side Includes
  'type.pxHtml_html' => '', // HTML
  'type.pxHtml_htm' => '', // HTML
  'type.pxGlobal' => '', // Global
  'type.pxFiles' => '', // Files
  'type.pxFile' => '', // File
  'type.pxDirectory' => '', // Directory
  'property.aDefaultTags' => '', // Default tags
  'type.pxDirectories' => '', // Directories
  'type.pxData' => '', // phpXplorer directory
  'type.pxData_objects' => '', // Object data
  'type.pxData_phpXplorer' => '', // phpXplorer data
  'type.pxBinaryFiles' => '', // Binary file
  'type.pxBinary' => '', // Binary files
  'type.pxBinary_bin' => '', // Macintosh programm
  'type.pxBinary_exe' => '', // Windows programm
  'property.sColumnPassword' => '', // Password column
  'property.sColumnUser' => '', // User column
  'property.sTable' => '', // Table
  'property.sPassword' => '', // Password
  'property.sUsername' => '', // Benutzername
  'type.pxAuthenticationPdo' => '', // PDO authentication
  'type.pxAuthenticationHtpasswd' => '', // htpasswd authentication
  'property.sSalt' => '', // Salt
  'property.sEncryption' => '', // Encryption
  'property.iLogin' => '', // Login
  'property.sDSN' => '', // Connection
  'type.pxAuthentication' => '', // pxp Authentication
  'type.pxAudio' => '', // Audio files
  'type.pxAudio_ogg' => '', // OGG audio
  'type.pxAudio_wav' => '', // WAV audio
  'type.pxAudio_mp3' => '', // MP3 audio
  'type.pxArchive' => '', // Archive files
  'type.pxArchive_jar' => '', // JAR archive
  'type.pxArchive_rar' => '', // RAR archive
  'type.pxArchive_zip' => '', // ZIP archive
  'type.pxApache' => '', // Apache files
  'type.pxApache_htpasswd' => '', // Apache passoword file
  'type.pxApache_htgroups' => '', // Apache group file
  'type.pxApache_htaccess' => '', // Apache configuration file
  'type.pxAction' => '', // Action
  'type.pxAction_pxAction.php' => '', // Action
  'action.pxTextFiles_edit' => '', // Edit text
  'action.pxShare_editUpdateIndex' => '', // Update index
  'action.pxShare_editBuildIndex' => '', // Build index
  'action.pxSetting_edit' => '', // Settings
  'action.pxPhp_openSource' => '', // PHP source
  'action.pxObject_editRename' => '', // Rename
  'action.pxObject_editProperties' => '', // Edit properties
  'action.pxObject_editDelete' => '', // Delete
  'action.pxObject_editCopy' => '', // Duplicate
  'action.pxObject_editClipboard' => '', // To clipboard
  'action.pxObject___edit' => '', // Common editor functionality
  'action.pxMetaFiles_openView' => '', // Open in new window
  'action.pxMetaFiles_openDownload' => '', // Download
  'action.pxMetaDirectories_openDetails' => '', // Details
  'action.pxMetaDirectories_batchShares' => '', // Details (shares)
  'action.pxGlobal_openPhpInfo' => '', // PHP Info
  'action.pxGlobal_openLoginForm' => '', // Login form
  'action.pxGlobal_openLogin' => '', // Login
  'action.pxGlobal_openInfo' => '', // phpXplorer information
  'action.pxGlobal_openHome' => '', // Homepage
  'action.pxGlobal_openCreate' => '', // Create
  'action.pxGlobal___openXplorerview' => '', // Dateitypen
  'action.pxGlobal___openShareview' => '', // Share
  'action.pxGlobal___openEditorview' => '', // File
  'action.pxFiles_openPreview' => '', // Preview
  'action.pxFiles__openInline' => '', // View
  'action.pxDirectory__editCreate' => '', // Create directory
  'action.pxDirectories_uploadHtml' => '', // Upload (HTML)
  'action.pxDirectories_selectTree' => '', // Files
  'action.pxDirectories_selectTags' => '', // Tags
  'action.pxDirectories_openPreview' => '', // Preview
  'action.pxDirectories__editShare' => '', // Edit user share(s)
  'action.pxDirectories___upload' => '', // Common upload functionality
  'action.pxDirectories___open' => '', // Common open functionality
  'action.pxVfsAuthenticationUser__editChanged' => '',
  'action.pxUser__editChanged' => '', // pxUser changed
  'action.pxType__openJson' => '', // Open JSON
  'action.pxTextFiles_edit' => '', // Edit text
  'action.pxTextFiles__open' => '', // Load text
  'action.pxShare_editUpdateIndex' => '', // Update index
  'action.pxShare_editBuildIndex' => '', // Build index
  'action.pxSetting_edit' => '', // Settings
  'action.pxPhp_openSource' => '', // PHP source
  'action.pxObject_editProperties' => '', // Edit properties
  'action.pxObject__openXml' => '', // Serialize to XML
  'action.pxObject__openPreview' => '', // Preview
  'action.pxObject__openOptions' => '', // Open options
  'action.pxObject__openNew' => '', // Instantiate
  'action.pxObject__openJson' => '', // Serialize to JSON
  'action.pxObject__editSwitchTag' => '', // Switch tag
  'action.pxObject__editProperty' => '', // Edit property
  'action.pxObject__editIndex' => '', // Index object
  'action.pxObject__editExists' => '', // Check existence
  'action.pxObject__editCreate' => '', // Create object
  'action.pxMetaFiles_openView' => '', // Open in new window
  'action.pxMetaFiles_openDownload' => '', // Download
  'action.pxGlobal_sendMail' => '', // Send email
  'action.pxGlobal_openShare' => '', // Open share
  'action.pxGlobal_openPhpInfo' => '', // PHP Info
  'action.pxGlobal_openLogout' => '', // Logout
  'action.pxGlobal_openLogin' => '', // Login
  'action.pxGlobal_openIndexData' => '', // Open index data
  'action.pxGlobal_openIndex' => '', // Index
  'action.pxGlobal_openError' => '', // Error message
  'action.pxGlobal_openCreate' => '', // Create
  'action.pxGlobal_openContact' => '', // Contact
  'action.pxGlobal___htmlDoc' => '', // Common HTML functionality
  'action.pxDirectories_uploadHtml' => '', // Upload (HTML)
  'action.pxDirectories__openDefaultTags' => '', // List tags
  'action.pxDirectories__editDeleteSelection' => '', // Delete selection
  'action.pxDirectories__editClipboard' => '', // Edit clipboard
  'action.pxDirectories___upload' => '', // Common upload functionality
  'writeFileMetaData' => '', // Write meta data to file
  'welcome' => '', // Welcome
  'upload.uploadMethod' => '', // Upload method
  'upload.upload' => '', // Upload
  'upload.targetDirectory' => '', // Target directory
  'upload.removeSelection' => '', // Remove selection
  'upload.overwriteFiles' => '', // Overwrite files with same name
  'upload' => '', // Upload
  'toolbar.upload' => '', // Upload
  'toolbar.share' => '', // Share
  'toolbar.settings' => '', // Settings
  'toolbar.search' => '', // Search
  'toolbar.save' => '', // Save
  'toolbar.logInOut' => '', // Log in/out
  'toolbar.loadFileMetaData' => '', // Load file meta data
  'toolbar.documentation' => '', // Documentation
  'toolbar.dirUp' => '', // Change to upper directory
  'toolbar.clipboard' => '', // Clipboard
  'toolbar.cancel' => '', // Cancel
  'tags' => '', // Tags
  'system' => '', // System
  'subTitle' => '', // Open Source File System Server
  'state' => '', // State
  'sortBy' => '', // Sort by
  'share.phpXplorer' => '', // phpXplorer system
  'share.Authentication' => '', // User manager
  'selection' => '', // Selection
  'searchTerm' => '', // Search term
  'search' => '', // Search term
  'saveTo' => '', // Save to ...
  'sView' => '', // View
  'sUsername' => '', // Username
  'sTable' => '', // Table
  'sShare' => '', // Share
  'sProperty' => '', // Property
  'sPath' => '', // Path
  'sOptions' => '', // Options
  'sOSystemPermissions' => '', // Permissions
  'sOSystemOwner' => '', // Owner
  'sOSystemGroup' => '', // Group
  'sMimeType' => '', // MIME type
  'sLastName' => '', // Last name
  'sInstruction' => '', // Instruction
  'sIndex' => '', // Index
  'sId' => '', // ID
  'sGroup' => '', // Group
  'sFirstname' => '', // Firstname
  'sFile' => '', // File
  'sDir' => '', // Directory
  'sDatabase' => '', // Database
  'sClient' => '', // Client
  'sBaseActions' => '', // base actions
  'sBaseAction' => '', // base action
  'sAuthentication' => '', // Authentication
  'sActions' => '', // Actions
  'sAction' => '', // Action
  'role.pxEveryone' => '', // Everyone
  'role.pxEditor' => '', // Editor
  'role.pxAuthenticated' => '', // Authenticated
  'role.pxAdministrator' => '', // Administrator
  'refreshView' => '', // Refresh
  'publish' => '', // Publish
  'properties' => '', // Properties
  'previousPage' => '', // Previous page
  'personalFiles' => '', // Personal files
  'paste' => '', // Paste
  'orOperator' => '', // disjunction (OR)
  'option.validUsersRedirect' => '', // Valid users with redirect
  'option.validUsers' => '', // Valid users
  'option.rootOnly' => '', // User root only
  'option.none' => '', // None
  'option.no' => '', // No
  'option.imageMagick' => '', // ImageMagick
  'option.gdLib' => '', // GD Graphics Library
  'option.evalUsers' => '', // Shares only
  'option.evalAll' => '', // All settings
  'openedFiles' => '', // File
  'openTab' => '', // Open page in new window
  'nextPage' => '', // Next page
  'newFile' => '', // New file
  'newDirectory' => '', // New directory
  'list' => '', // List
  'line' => '', // Line
  'inheritedBy' => '', // inherited by
  'info' => '', // Info
  'index' => '', // Indizieren
  'iType' => '', // Type
  'iPosition' => '', // Position
  'home.shares' => '', // You have got access to the following shares
  'home' => '', // Homepage
  'handleEvent' => '', // Handle event
  'guestAccess' => '', // Guest access
  'general' => '', // General
  'exit' => '', // Exit
  'everyone' => '', // guest
  'error.couldNotWrite' => '', // Could not write to file "%s"
  'error.validationError' => '', // Please correct the invalid values
  'error.userExists' => '', // Username already exists
  'error.unknownValidationMethod' => '', // Unknows validation method "%s"
  'error.tokenizerNotFound' => '', // Missing PHP Tokenizer Extension
  'error.shareNotFound' => '', // Share "%s" does not exist
  'error.selectionAction' => '', // The following objects could not be processed: %s
  'error.passwordsNotEqual' => '', // Passwords are not equal
  'error.objectExists' => '', // Object of the same name already exists
  'error.notEmpty' => '', // Please enter a value
  'error.notAllowedToRunAction' => '', // You are not allowed to run action %s
  'error.notAllowedToDeleteType' => '', // You are not allowed to delete %s files
  'error.notAllowedToDeleteEveryFile' => '', // You are not allowed to delete every file of your selection
  'error.notAllowedToCreateType' => '', // You are not allowed to create %s files
  'error.notAllowedToCreateEveryFile' => '', // You are not allowed to create every file (%s) of your selection in the target directory (%s)
  'error.noUploads' => '', // Your PHP installation does not support uploading files
  'error.noReceiver' => '', // Missing receiver address
  'error.noBookmarks' => '', // No share available
  'error.loginFailed' => '', // Login failed
  'error.invalidUri' => '', // Invalid URI
  'error.invalidString' => '', // Invalid characters
  'error.invalidPathParam' => '', // Invalid path parameter (%s)
  'error.invalidParameter' => '', // Invalid parameter(s)
  'error.invalidNumber' => '', // Invalid number
  'error.invalidFilename' => '', // Invalid filename
  'error.invalidEmail' => '', // Invalid email address
  'error.invalidDate' => '', // nvalid date
  'error.invalidActionParam' => '', // Unknown action Aktion %s
  'error.fileNotFound' => '', // Unknown file %s
  'error.extensionNotLoaded' => '', // %s extension not loaded
  'error.couldNotConnect' => '', // %s server connection failed
  'error.accessDenied' => '', // Access denied
  'editProfile' => '', // Edit profile
  'documentation' => '', // Documentation
  'discardChanges' => '', // Discard changes
  'development' => '', // Development
  'debug' => '', // Debug
  'cut' => '', // Cut
  'contact.sTelefon' => '', // Phone
  'contact.sPerson' => '', // Person in charge
  'contact.sEmail' => '', // Email
  'contact' => '', // Contact
  'compressed' => '', // Compressed
  'comment' => '', // Comment
  'collapseAll' => '', // Collapse all treeview nodes
  'close' => '', // Close
  'clearSelection' => '', // Clear selection
  'clear' => '', // Clear
  'choose' => '', // choose
  'changePassword' => '', // Change password
  'changeOrientation' => '', // Change orientation
  'batch' => '', // Batch edit
  'bTreeReload' => '', // Tree reload
  'bKey' => '', // Key
  'bExtendDoc' => '', // Create files automatically
  'bDelete' => '', // Delete
  'andOperator' => '', // conjunction (AND)
  'allInSameDir' => '', // All source files must be in the same directory
  'administration' => '', // Administration
  'actions' => '', // Actions
  'aSupertypes' => '', // Supertypes
  'aSubtypes' => '', // Subtypes
  'aShares' => '', // Shares
  'aPermissions' => '', // Permissions
  'aEvents' => '', // Events
  '' => '',
  'URL' => 'URL',
  'add' => 'Agregar',
  'address' => 'Dirección',
  'allowOverwrite' => 'Sobreescribir',
  'attachments' => 'Adjuntos',
  'back' => 'Atrás',
  'content' => 'Contenido',
  'copy' => 'Copiar',
  'create' => 'Crear',
  'dataFormat' => 'Formato de fecha',
  'database' => 'Base de datos',
  'default' => 'Defecto',
  'delete' => 'Borrar',
  'description' => 'Descripción',
  'download' => 'Descargar',
  'edit' => 'Editar',
  'encoding' => 'Codificación',
  'error' => 'Error',
  'extract' => 'Extraer',
  'extractSelection' => 'Extraer Selección',
  'firstname' => 'Nombre',
  'form' => 'Formulario',
  'forward' => 'Volver',
  'free' => 'Libre',
  'from' => 'De',
  'htgroupsNotFound' => 'El archivo .htgroups no se pudo encontrar',
  'htpasswdNotFound' => 'El archivo .htpasswd no se pudo encontrar',
  'icon' => 'Icono',
  'insert' => 'Insertar',
  'install' => 'Instalar',
  'key' => 'Llave',
  'keyword' => 'Palabra clave',
  'keywords' => 'Palabras clave',
  'language' => 'Lenguaje',
  'message' => 'Mensaje',
  'method' => 'Metodo',
  'name' => 'Nombre',
  'newName' => 'Porfavor introduce un nuevo nombre',
  'number' => 'Número',
  'of' => 'de',
  'open' => 'Abrir',
  'openURL' => 'Abrir URL',
  'path' => 'Path',
  'phpXplorer' => 'phpXplorer',
  'preview' => 'Vista previa',
  'proportional' => 'Proporcional',
  'reallyDelete' => 'Estás seguro de que quieres borrar',
  'receiver' => 'Receptor',
  'select' => 'Seleccionar',
  'send' => 'Enviar',
  'server' => 'Servidor',
  'style' => 'Estilo',
  'subject' => 'Asunto',
  'timeFormat' => 'Formato de tiempo',
  'user' => 'Usuario',
  'users' => 'Usuarios',
  'yes' => 'si',
  'type.pxShare' => 'Archivo compartido pxp'
));

?>