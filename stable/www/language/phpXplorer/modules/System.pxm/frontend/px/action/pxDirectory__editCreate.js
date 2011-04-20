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
px.Class.define('px.action.pxDirectory__editCreate')

Object.extend(
	px.Statics,
	{
		run: function(sNewType, sNewName, bRetype, bRaiseError)
		{
			if (!sNewType) {
				var sNewType = 'pxDirectory'
			}

			var oList = pxp.getListview()
			var sShare = oList.oParameters.sShare
			var sDir = oList.oParameters.sPath
	
			if (!sNewName || bRetype) {
				var sNewName = px.util.getNewFilename(sNewName, 'pxDirectory', true)
				if (sNewName == null) {
					return false
				}
			}

			var sPath = px.util.buildPath(sDir, sNewName)

			var oResult = px.io.Request.exists(sShare, sPath)
			if (oResult.bOk) {
				if (!bRaiseError && bRaiseError != false) {
					alert(oTranslation['error.objectExists'])
					px.action.pxDirectory__editCreate.run(sNewType, sNewName, true)
				}
				return false
			}

			var oResult = px.io.Request.post(
				'sShare=' + encodeURIComponent(sShare) +
				'&sAction=_editCreate' +
				'&sPath=' + encodeURIComponent(sPath) + 
				'&sType=' + encodeURIComponent(sNewType)
			)

			if (oResult.bOk) {
				pxp.refreshView(sDir)
			}

			return false
		}	
	}
)