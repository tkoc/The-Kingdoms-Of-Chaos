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
px.Class.define('px.action.pxGlobal_openCreate')

Object.extend(
	px.Statics,
	{
		run: function(sType, sNewName)
		{
			var sAction = sType + '__editCreate'

			if (px.action[sAction] && px.action[sAction].run) {
				px.action[sAction].run()
				return
			}

			var oType = pxp.oTypes[sType]

			if (!sNewName) {
				var sNewName = ''
				if (sType != 'pxData') {
					sNewName = (oType.bDirectory ? oTranslation['newDirectory'] : oTranslation['newFile']) + ' ' + pxp.iNewFileCount++
				}
				sNewName = pxp.addValidExtension(sNewName, sType)
			}

			var oListview = pxp.getListview()
			var sPath = oListview.oParameters.sPath
			var sFullPath = px.util.buildPath(sPath, sNewName)

			var oView = pxp.oShareview.oSelected.oActionviewFiles
 			if (!oView.oActions[sFullPath]) {
 				oView.addAction(
 					sFullPath,
					sFullPath.indexOf('/') == 0 ? sFullPath.substr(1) : sFullPath,
 					px.action.pxGlobal___openEditorview,
 					true,
					'types/' + sType,
	 				{
	 					sPath: sFullPath,
	 					sType: sType,
						_bNew: true,
						sNewName: sNewName
	 				}
 				)
 			}
 			oView.showAction(sFullPath)
		},

		showMenu: function(oEvent)
		{
			pxp.setActiveControl(px.action.pxGlobal_openCreate)
	
			var sShare = this.oParent.oParent.sId
			var sPath = this.oChild.oParameters.sPath
					
			var aTypes = px.io.Request.get('sAction=openCreate&sShare=' + encodeURIComponent(sShare) + '&sPath=' + encodeURIComponent(sPath))
	
			var oCm = px.ui.ContextMenu
			oCm.clear()
	
			for (var t=0; t<aTypes.length; t++)
			{
				var oType = pxp.oTypes[aTypes[t]]
				var sExtendedType = pxp.getExtendedType(oType.sId)
	
				oCm.addItem(
					oType.sId,
					oTranslation['type.' + sExtendedType],
					'./modules/' + oType.sModule + '.pxm/graphics/types/' + sExtendedType + '.png'
				)
			}
			oCm.show(oEvent)
	
			return false
		},

		callAction: function(sAction) {
			this.run(sAction)
		}
	}
)