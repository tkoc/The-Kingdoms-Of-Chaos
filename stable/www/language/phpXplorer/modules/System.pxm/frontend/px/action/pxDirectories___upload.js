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
px.Class.define('px.action.pxDirectories___upload')

Object.extend(
	px.Statics,
	{
		run: function(oObject, sActionIn)
		{
			var sShare = pxp.oShareview.oSelected.oShare.sId
			var oView = pxp.getListview()
			var sPath = oView.oParameters.sPath
			var sAction = sActionIn || 'uploadHtml'
	
			if (oObject.bControl) {
				for (var sFileName in oView.oSelected) {
					//sPath = px.util.buildPath(
					//	sPath,
					//	sFileName
					//)
					sPath = sFileName
					break
				}
			}

			var iTop = (document.body.offsetHeight / 2) - 240
			var iLeft = (document.body.offsetWidth * 0.4) - 320
	
			var oWin = window.open(
				'./action.php?sShare=' + sShare + '&sAction=' + sAction + '&sPath=' + encodeURIComponent(sPath),
				px.util.getRandomId(),
				'top=' + iTop + ',left=' + iLeft + ',width=640,height=480,scrollbars=yes,resizable=yes'
			)

			return false
		}
	}
)