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
px.Class.define('px.action.pxObject_editClipboard')

Object.extend(
	px.Statics,
	{
		aDropTypes: Array('item'),
		
		run: function(oView)
		{
			var oControl = pxp.oSelectedControl
	
			if (!px.action.pxObject___edit.checkSelection(oControl)) {
				return false
			}
	
			pxp.aClipboard.length = 0
			pxp.oClipboardSourceShare = oView.oParent.oShare
			for (var sSelected in oControl.oSelected) {
				if (oControl.oSelected[sSelected].getType() != 'pxVirtualDirectory') {
					pxp.aClipboard.push(sSelected)
				}
			}
			// show clipboard dropdown icons of all shares
			this.switchClipboardState(true)
		},

		switchClipboardState: function(bFilled) {
			for (var sAction in pxp.oShareview.oActions) {
				var oAction = pxp.oShareview.oActions[sAction]
				if (oAction.bInitialized) {
					for (var sAction2 in oAction.oActionviewList.oActions) {
						var oAction2 = oAction.oActionviewList.oActions[sAction2]
						if (oAction2.bInitialized) {
							var oButton = oAction2.oToolbar.oButtons['clipboard']
							if (bFilled) {
								oButton.showDropDown()
								oButton.changeImage(pxConst.sGraphicUrl + '/toolbar/clipboardOn.png')
							} else {
								oButton.hideDropDown()
								oButton.changeImage(pxConst.sGraphicUrl + '/toolbar/clipboard.png')
							}
						}
					}
				}
			}
		},
	
		clear: function() {
			pxp.aClipboard.length = 0
			this.switchClipboardState(false)
		},

		showMenu: function(oEvent)
		{
			pxp.oSelectedControl = px.action.pxObject_editClipboard

			if (pxp.aClipboard.length > 0) {
				var oCm = px.ui.ContextMenu
				oCm.clear()
	
				var sShareTitle = pxp.oClipboardSourceShare.sTitle || pxp.oClipboardSourceShare.sId
	
				var oItem = oCm.addItem('__copy', sShareTitle, pxConst.sGraphicUrl + '/pxShareLoaded.png', true)
				oItem.style.color = '#999'
	
				var sSourceDir
	
				for (var a=0; a<pxp.aClipboard.length; a++) {
					if (!sSourceDir) {
						sSourceDir = px.util.dirname(pxp.aClipboard[a])
					}
					oCm.addItem(pxp.aClipboard[a], pxp.aClipboard[a].substr(1), pxConst.sGraphicUrl + '/dummy16.png', true)
				}
	
				oCm.addDivider()
				oCm.addItem('__copy', 'Kopieren', pxConst.sGraphicUrl + '/copy.png')
				
				var oTargetParameters = pxp.getListview().oParameters
				var sSourcePath = px.util.buildPath(pxp.oClipboardSourceShare.sId + ':', sSourceDir)
	
				if (sSourcePath != oTargetParameters.getPath()) {
					oCm.addItem('__move', 'Verschieben', pxConst.sGraphicUrl + '/move.png')
				}
				oCm.addDivider()
				oCm.addItem('__clear', 'Leeren', pxConst.sGraphicUrl + '/close.png')
				oCm.show(oEvent)
			}
			return false
		},
	
		callAction: function(sAction) {
			switch (sAction) {
				case '__copy':
					this.runAction('copy')
					break
				case '__move':
					this.runAction('move')
					break
				case '__clear':
					this.clear()
					break
			}
		},
	
		itemDrop: function(oToItem) {
			this.run(oToItem.oParent)
			this.runAction('move', oToItem.getShare(), oToItem.sFilename)
		},
	
		runAction: function(sMode, sDestinationShare, sDestinationPath)
		{
			var oListview = pxp.getListview()

			if (!sDestinationShare) {
				var oParam = oListview.oParameters
				var sDestinationShare = oParam.sShare
				var sDestinationPath = oParam.sPath
			}

			var bOpened = false
			var aNames = []
	
			for (var c=0; c<pxp.aClipboard.length; c++) {
				if (pxp.aClipboard[c] == oListview.oParameters.sPath && sMode == 'move') {
					bOpened = true
				}
				aNames.push(px.util.basename(pxp.aClipboard[c]))
				if (!sShare) {
					var sShare = pxp.oClipboardSourceShare.sId
					var sDirname = px.util.dirname(pxp.aClipboard[c])
				}
			}
	
			// Navigate to top if current directory will be removed
			if (bOpened) {			
				oListview.oParameters.sPath = '/'
				oListview.update()
			}
	
			var oResult = px.io.Request.post(
				'sAction=_editClipboard' +
				'&sShare=' + encodeURIComponent(sShare) +
				'&sPath=' + encodeURIComponent(sDirname) +
				'&aNames=' + encodeURIComponent(aNames.join('|')) +
				'&sDestinationPath=' + encodeURIComponent(sDestinationPath) +
				'&sDestinationShare=' + encodeURIComponent(sDestinationShare) +
				'&sMode=' + sMode
			)
	
			if (oResult.bOk) {
				pxp.oShareview.oSelected.refreshView(sDirname)
				pxp.oShareview.oSelected.refreshView(sDestinationPath)			
				if (sMode == 'move') {
					this.clear()
				}
			}
		}
	}
)