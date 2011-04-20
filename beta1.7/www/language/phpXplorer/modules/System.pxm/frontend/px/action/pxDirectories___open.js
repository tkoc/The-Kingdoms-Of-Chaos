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
px.Class.define('px.action.pxDirectories___open',
{
	extend: px.Action,

	construct: function(sId, oParent, oParentNode, oParameters, oControl)
	{
		this.base(arguments, sId, oParent, oParentNode)

		var cFunction = px.lang.Function

		this.oChild = new oControl(this, this.oDiv)
		this.oChild.onDirChange = cFunction.bind(this.onDirChange, this)
		this.oChild.iObjectsPerPage = this.oShare.iObjectsPerPage

		if (oParameters) {
			this.oChild.oParameters.set(oParameters)
		} else {
			if (this.oParent.oPrevious) {
				this.oChild.oParameters.sync(this.oParent.oPrevious.oChild.oParameters)
			}
		}

		this.oToolbar.addButton(
			{
				sId: 'dirUp',
				sLabel: 'Aufwärts',
				sIcon: 'toolbar/dirUp.png',
				oOnClick: cFunction.bindEvent(this.oChild.dirUp, this.oChild),
				sTitle: oTranslation['toolbar.dirUp'],
				bDisabled: true
			}
		)

		this.oToolbar.addButton(
			{
				sId: 'create',
				sLabel: oTranslation['action.pxGlobal_openCreate'],
				sIcon: 'toolbar/create.png',
				oOnClick: cFunction.bindEvent(px.action.pxGlobal_openCreate.showMenu, this)
			}
		)

		this.oToolbar.addButton(
			{
				sId: 'upload',
				sIcon: 'toolbar/upload.png',
				sLabel: oTranslation['toolbar.upload'],
				oOnClick: px.action.pxDirectories_uploadHtml.run
			}
		)

		this.oToolbar.addButton(
			{
				sId: 'share',
				sIcon: 'toolbar/share.png',
				sLabel: oTranslation['toolbar.share'],
				oOnClick: px.action.pxDirectories__editShare.run
			}
		)

		this.oToolbar.addButton(
			{
				sId: 'clipboard',
				sLabel: oTranslation['toolbar.clipboard'],
				sIcon: 'toolbar/clipboard.png',
				sIconOver: 'clipboardOver.png',
				oOnClick: cFunction.bindEvent(px.action.pxObject_editClipboard.showMenu, this),
				bDropDown: true,
				oDropHandler: px.action.pxObject_editClipboard
			}
		)
		if (pxp.aClipboard.length == 0) {
			this.oToolbar.oButtons['clipboard'].hideDropDown()
		}
		
		this.oToolbar.addButton(
			{
				sId: 'cancel',
				sIcon: 'toolbar/cancel.png',
				sLabel: oTranslation['toolbar.cancel'],
				oOnClick: cFunction.bindEvent(this._cancelSelection, this)
			}
		)

		this.oToolbar.hideButton('upload')
		this.oToolbar.hideButton('create')
		this.oToolbar.hideButton('clipboard')
		this.oToolbar.hideButton('share')
		this.oToolbar.hideButton('cancel')
	}
})

Object.extend(
	px.Statics,
	{
		changeDirectory: function(oEvent)			
		{
			var oA = px.Event.element(oEvent)
			var oButtons = this.oParent.oParent.oToolbar.oButtons
			
			for (var sButton in oButtons) {
				if (oButtons[sButton].oA == oA) {
					var sPath = oButtons[sButton].sId.substr(oButtons[sButton].sId.indexOf('_') + 1)
					this.oChild.oParameters.sPath = sPath
					this.oChild.update()
				}
			}
			return false
		}
	}
)

Object.extend(
	px.Proto,
	{
		onDirChange: function() {
			this.oParent.oParent.cancelSearch()
		},
		
		updateInterface: function()
		{
			var cFunction = px.lang.Function
			var sPath = this.oChild.oParameters.sPath

			if (sPath != '/') {
				this.oToolbar.enableButton('dirUp')
			} else {
				this.oToolbar.disableButton('dirUp')
			}

			var oSettings = this.oChild.oSettings[this.oChild.oParameters.getPath()]
		
			this.oParent.aAllowedActions = oSettings.aAllowedActions[oSettings.sDirectoryType]
		
			var sSearchQuery = this.oChild.oParameters.sSearchQuery
			var bCreate = oSettings.bCreatePermission && !sSearchQuery
			var bUpload = oSettings.bUploadPermission && !sSearchQuery && pxp.oData.bUpload
			var bClipboard = bCreate || bUpload
			var bShare = oSettings.bSharePermission && !sSearchQuery
			
			var oTb = this.oToolbar
			bUpload ? oTb.showButton('upload') : oTb.hideButton('upload')
			bCreate ? oTb.showButton('create') : oTb.hideButton('create')
			bClipboard ? oTb.showButton('clipboard') : oTb.hideButton('clipboard')
			bShare ? oTb.showButton('share') : oTb.hideButton('share')
			sSearchQuery ? oTb.showButton('cancel') : oTb.hideButton('cancel')
		
			//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

			for (var sButton in this.oParent.oParent.oToolbar.oButtons) {
				if (sButton.indexOf('pxPath') == 0) {
					this.oParent.oParent.oToolbar.removeButton(sButton)
				}
			}

			var sSubPath = '/'
			if (sPath != '/') {
				var aDirs = sPath.split('/')
				for (var a=1, m=aDirs.length; a<m; a++) {
					sSubPath = px.util.buildPath(sSubPath, aDirs[a])
					this.oParent.oParent.oToolbar.addButton(
						{
							sId: 'pxPath_' + sSubPath,
							sLabel: '/ ' + aDirs[a],
							oOnClick: cFunction.bindEvent(px.action.pxDirectories___open.changeDirectory, this)
						}
					)
				}
			}
		
			//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
			
			var oActions = this.oParent.oParent.oActionviewSelection.oActions

			if (oActions.pxDirectories_selectTags && oActions.pxDirectories_selectTags.bInitialized) {
				oActions.pxDirectories_selectTags.loadTags()
			}
		
			if (oActions.pxDirectories_selectTree && oActions.pxDirectories_selectTree.bInitialized) {
				var oTreeview = oActions.pxDirectories_selectTree.oChild
				var sPath = this.oChild.oParameters.sPath
				var iPos = sPath.lastIndexOf('/')
				if (iPos > 0) {
					var oNode = oTreeview.getNode(sPath.substr(0, iPos))
					if (!oNode.bExpanded) {
						oNode.expand()
					}
				}
				oTreeview.setActiveItem(sPath)
			}
		},

		_cancelSelection: function() {
			var oShareviewAction = this.oParent.oParent
			oShareviewAction.cancelSearch(false)
			var oAction = oShareviewAction.oActionviewSelection.oActions.pxDirectories_selectTags
			if (oAction && oAction.bInitialized) {
				oAction.clearTagSelection(false)
			}
			oShareviewAction.updateSearch()
			
			return false
		}
	}
)