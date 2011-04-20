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
<?php $this->aRoles = unserialize('a:4:{s:15:"pxAdministrator";O:6:"pxRole":6:{s:9:"aSubRoles";a:1:{i:0;s:8:"pxEditor";}s:12:"aPermissions";a:2:{s:8:"pxObject";b:1;s:8:"pxGlobal";b:1;}s:7:"aEvents";a:0:{}s:15:"aCompiledEvents";a:0:{}s:11:"aParameters";a:0:{}s:3:"sId";s:15:"pxAdministrator";}s:15:"pxAuthenticated";O:6:"pxRole":6:{s:9:"aSubRoles";a:0:{}s:12:"aPermissions";a:5:{s:9:"pxProfile";b:1;s:34:"pxGlobal.edit.pxGlobal_editProfile";b:1;s:23:"pxVfsAuthenticationUser";b:1;s:6:"pxData";b:1;s:6:"pxUser";b:1;}s:7:"aEvents";a:2:{s:11:"pxUser.edit";s:77:"<action id="_editChanged">
  <param id="sTriggeringAction"></param>
</action>";s:28:"pxVfsAuthenticationUser.edit";s:77:"<action id="_editChanged">
  <param id="sTriggeringAction"></param>
</action>";}s:15:"aCompiledEvents";a:2:{s:11:"pxUser.edit";s:68:"$this->call(\'_editChanged\', array(\'sTriggeringAction\' => $sAction));";s:28:"pxVfsAuthenticationUser.edit";s:68:"$this->call(\'_editChanged\', array(\'sTriggeringAction\' => $sAction));";}s:11:"aParameters";a:0:{}s:3:"sId";s:15:"pxAuthenticated";}s:8:"pxEditor";O:6:"pxRole":7:{s:11:"sParentRole";s:15:"pxAdministrator";s:9:"aSubRoles";a:0:{}s:12:"aPermissions";a:6:{s:6:"pxHtml";b:1;s:41:"pxDirectory.open.pxDirectories_selectTree";b:1;s:8:"pxGlobal";b:1;s:16:"pxDirectory.edit";b:1;s:16:"pxDirectory.open";b:1;s:18:"pxDirectory.select";b:1;}s:7:"aEvents";a:0:{}s:15:"aCompiledEvents";a:0:{}s:11:"aParameters";a:0:{}s:3:"sId";s:8:"pxEditor";}s:10:"pxEveryone";O:6:"pxRole":6:{s:9:"aSubRoles";a:0:{}s:12:"aPermissions";a:16:{s:32:"pxGlobal.open.pxGlobal_openError";b:1;s:32:"pxGlobal.open.pxGlobal_openIndex";b:1;s:36:"pxGlobal.open.pxGlobal_openIndexData";b:1;s:31:"pxGlobal.open.pxGlobal_openInfo";b:1;s:32:"pxGlobal.open.pxGlobal_openLogin";b:1;s:33:"pxGlobal.open.pxGlobal_openLogout";b:1;s:38:"pxGlobal.open.pxDirectories_selectTags";b:1;s:32:"pxGlobal.open.pxGlobal_openShare";b:1;s:16:"pxDirectory.open";b:1;s:10:"pxXml.open";b:1;s:11:"pxText.open";b:1;s:11:"pxHtml.open";b:1;s:16:"pxBlogEntry.open";b:1;s:18:"pxBinaryFiles.open";b:1;s:34:"pxScript.open.pxMetaFiles_openView";b:1;s:34:"pxGlobal.open.pxGlobal_openContact";b:1;}s:7:"aEvents";a:1:{s:13:"pxObject.edit";s:75:"<action id="_editIndex">
  <param id="sTriggeringAction"></param>
</action>";}s:15:"aCompiledEvents";a:1:{s:13:"pxObject.edit";s:66:"$this->call(\'_editIndex\', array(\'sTriggeringAction\' => $sAction));";}s:11:"aParameters";a:0:{}s:3:"sId";s:10:"pxEveryone";}}');?>