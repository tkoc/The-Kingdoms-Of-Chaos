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
px.Class.define('px.action.pxObject___edit',
{
	extend: px.Action,

	construct: function(sId, oParent, oParentNode, oParametersIn)
	{
		this.base(arguments, sId, oParent, oParentNode)

		var cFunction = px.lang.Function

		this.oToolbar.addButton(
			{
				sId: '__docLink',
				sLabel: oTranslation['toolbar.documentation'],
				sIcon: 'helpLight.png',
				oOnClick: cFunction.bind(this.openDocs, this),
				bRight: true
			}
		)
		this.oToolbar.addButton(
			{
				sId: 'save',
				sLabel: oTranslation['toolbar.save'],
				sIcon: 'disk.png',
				oOnClick: cFunction.bind(this.save, this),
				bRight: true
			}
		)
	}
})

Object.extend(
	px.Statics,
	{
		checkSelection: function(oControl) {
			// all source files have to in the same directory
			if (oControl instanceof px.ui.treeview.Treeview) {
				var sActiveDir
				var bAllInSameDir = true
				for (var sSelected in oControl.oSelected) {
					var oSelectedItem = oControl.getNode(sSelected)
					if (sActiveDir && sActiveDir != oSelectedItem.getDirname()) {
						bAllInSameDir = false
						break
					}
					sActiveDir = oSelectedItem.getDirname()
				}
				if (!bAllInSameDir) {
					alert(oTranslation['allInSameDir'])
					return false
				}
			}
			return true
		},

		runSelectionAction: function(sParameters)
		{
			var oControl = pxp.oSelectedControl
			var sShare = pxp.oShareview.oSelected.oShare.sId
		
			if (!px.action.pxObject___edit.checkSelection(oControl)) {
				return false
			}
		
			var oListview = pxp.getListview()
			var bOpened = false
			var aFiles = []
		
			for (var sSelected in oControl.oSelected) {
				if (oControl.oSelected[sSelected].getType() != 'pxVirtualDirectory') {
					if (sSelected == oListview.oParameters.sPath) {
						bOpened = true
					}
					aFiles.push(px.util.basename(sSelected))
					if (!sDirname) {
						var sDirname = px.util.dirname(sSelected)
					}
				}
			}
		
			// Navigate to top if current directory will be removed
			if (bOpened) {
				oListview.oParameters.sPath = '/'
				oListview.update()
			}
		
			sParameters = sParameters.replace(/{@share}/g, encodeURIComponent(sShare))
			sParameters = sParameters.replace(/{@dirname}/g, encodeURIComponent(sDirname))

			var oResult = px.io.Request.post(
				'sShare=' + encodeURIComponent(sShare) +
				'&sPath=' + encodeURIComponent(sDirname) +
				'&aNames=' + encodeURIComponent(aFiles.join('|')) +
				'&' + sParameters
			)

			if (oResult.bOk) {
				pxp.refreshView(sDirname)
			}
		}
	}
)

Object.extend(
	px.Proto,
	{
		openDocs: function() {
			if (this.oChild.oObject.sType) {
				pxp.showDoc('type/' + this.oChild.oObject.sType)
			} else {
				pxp.showDoc('action/' + this.oParent.oSelected.sId)
			}
		}
	}
)