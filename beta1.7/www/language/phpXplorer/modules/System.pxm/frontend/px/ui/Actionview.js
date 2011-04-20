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
px.Class.define('px.ui.Actionview',
{
	extend: px.core.Object,

  construct: function(oParent)
	{
		this.oParent = oParent
		this.sClass
		//this.sTitle
		this.sIcon
		this.sDropDownIcon

		this.oActions = {}
		this.oToolbars = {}
		this.oActionData = {}

		this.oDefaultParameters

		/**
		 * Label contains title instead of title and selected action id
		 */
		this.bTitleOnly = false
		this.bHighlightLoaded = false

		this._oToolbarNode

		this.sDefault

		this.oSelected
		this.oPrevious

		this.oLabel
		this.oTabview
		this.oToolbar
		this.oActionSelect
		this.aAllowedActions

		this.bSyncParameter = false
		this.bInitialized = false
		this.bToolbarFirst = false
		this.iMenuType = pxConst.MENU_BUTTON // pxConst.MENU_TABS, pxConst.MENU_BUTTON
		this.iToolbarType = pxConst.TOOLBAR_HORIZONTAL // TOOLBAR_VERTICAL
		this.sLabelDrop

		this.iIconSize = 16
	},

	destruct: function()
	{
		switch (this.iMenuType) {
			case pxConst.MENU_TABS:
				this.oTabview.dispose()
			break
			case pxConst.MENU_BUTTON:
				this.oLabel.onclick = null
	 			if (this.sLabelDrop) {
	 				this.oLabel.onmouseover = null
		 			this.oLabel.onmouseout = null
		 			this.oLabel.onmouseup = null
	 			}
			break
		}

		this._disposeFields(
			'oParent', 'oParentNode', 'oDefaultParameters', '_oToolbarNode',
			'sDefault', 'oSelected', 'oLabel', 'oPrevious', 'oTabview',
			'oActionSelect', 'aAllowedActions'
		)
		this._disposeContents('oActions', 'oToolbars', 'oActionData')
	}
})



Object.extend(
	px.Proto,
	{
		addAction: function(sId, sTitle, oAction, bDefault, sIcon, oParameters)
		{
			if (sIcon) {
				var iPos = sIcon.indexOf('/')
				if (iPos > -1)
				{
					var sNamespace = sIcon.substr(0, iPos)
					var sValue = sIcon.substr(iPos+1)
					var sModuleDir = pxConst.sModuleUrl + '/'

					switch (sNamespace) {
						case 'types':
							var sIconPath = sModuleDir + pxp.oTypes[sValue].sModule + '.pxm/graphics/types/' + sValue + '.png'
						break
						case 'actions':
							var sIconPath = sModuleDir + pxp.oActions[sValue][0] + '.pxm/graphics/actions/' + sValue + '.png'
						break
					}
				}
				if (!sIconPath) {
					var sIconPath = sIcon
				}
			}

			this.oActionData[sId] = {
				oAction: oAction,
				oParameters: oParameters,
				sTitle: sTitle,
				sIcon: sIconPath,
				dispose: function() {
					this.oAction = null
					this.oParameters = null
				}
			}

			if (bDefault) {
				this.sDefault = sId
			}

			if (this.bInitialized) {
			  	switch (this.iMenuType) {
			  		case pxConst.MENU_TABS:
							if (this.oTabview.iTabCount > 0) {
								this.oTabview.oInsertBefore = this.oTabview.oParentNode.firstChild
							} else {
								delete this.oTabview.oInsertBefore
							}
							this.oTabview.addTab(sId, sTitle, true)
			  			break
			  		case pxConst.MENU_BUTTON:
							this._setIconState()
			  			break
			  	}
			}

//			px.html.Element.removeClassName(this._oToolbarNode, 'pxBarDisabled')
		},




		changeAction: function(sAction) {
			this.showAction(
				sAction,
				this.oDefaultParameters
			)
			return false
		},




		showAction: function(sId, oParameters)
		{
			var oActions = this.oActions
			var oActionData = this.oActionData

			if (!oActionData[sId]) {
				alert('There is no action "' +  sId + '"')
				return false
			}

			if (this.oSelected)
			{
				if (this.oSelected.sId == sId) {
					return true
				}

				if (this.oSelected.oDiv) {
					this.oSelected.oDiv.style.display = 'none'
					this.oToolbars[this.oSelected.sId].oDiv.style.display = 'none'
				}
			}

			if (!oActions[sId])
			{
				this.oToolbars[sId] = new px.ui.toolbar.Toolbar(this, this._oToolbarNode)
				this.oToolbars[sId].iIconSize = this.iIconSize

				oActions[sId] = new oActionData[sId].oAction(sId, this, this.oParentNode, oActionData[sId].oParameters || this.oDefaultParameters)
//				oActions[sId].sTitle = oActionData[sId].sTitle
				oActions[sId].sIcon = oActionData[sId].sIcon

				this.oSelected = oActions[sId]

				oActions[sId].bInitialized = true
			}
			else
			{
				this.oSelected = oActions[sId]

				if (this.bSyncParameter) {
					if (oParameters) {
						oActions[sId].oChild.oParameters.set(oParameters)
					} else {
						if (this.oPrevious && oActions[sId].oChild) {
							oActions[sId].oChild.oParameters.sync(this.oPrevious.oChild.oParameters)
						}
					}
					oActions[sId].oChild.update()
				}

				if (oParameters && oParameters._sCalledAction && oActions[sId].oChild && oActions[sId].oChild.changeAction) {
					oActions[sId].oChild.changeAction(oParameters._sCalledAction)		
				}

				oActions[sId].oDiv.style.display = 'block'
				this.oToolbars[sId].oDiv.style.display = 'block'

				this.resize(true)
			}

			this.updateMenu()

			this.oPrevious = oActions[sId]
			
			px.html.Element.removeClassName(this._oToolbarNode, 'pxBarDisabled')

			return false
		},



		resize: function(bFinished) {
			if (this.oSelected && this.oSelected.bInitialized) {
				this.oSelected.resize(bFinished)
			}
		},



		init: function(oParentNode, oDefaultParameters)
		{
			var cFunction = px.lang.Function
			var cElement = px.html.Element

			this.oParentNode = oParentNode

			if (oDefaultParameters) {
				this.oDefaultParameters = oDefaultParameters
			}

			this._oToolbarNode = document.createElement('div')
			this._oToolbarNode.className =
				'pxBar ' +
				(this.iToolbarType == pxConst.TOOLBAR_HORIZONTAL ? 'pxTbHorizontal': 'pxTbVertical') +
				(this.sClass ? ' ' + this.sClass + 'Menu' : '') +
				(px.util.getLength(this.oActionData) == 0 ? ' pxBarDisabled' : '')

			this.oParentNode.appendChild(this._oToolbarNode)

			switch (this.iMenuType) {
				case pxConst.MENU_TABS:
					this.oTabview = new px.ui.tabview.Tabview(this, this._oToolbarNode)
			  		for (var a in this.oActionData) {
			  			this.oTabview.addTab(
			  				a,
			  				this.oActionData[a].sTitle,
			  				a == this.sDefault
			  			)
			  		}
			  		this.oTabview.onTabClick = px.lang.Function.bind(this.changeAction, this)
				break
				case pxConst.MENU_BUTTON:
					this.oLabel = document.createElement('a')		
					cElement.addClassName(this.oLabel, 'pxTb')
					cElement.addClassName(this.oLabel, 'pxBarButton')
					this.oLabel.href = pxConst.DUMMY_LINK
					this._oToolbarNode.appendChild(this.oLabel)
		
					if (this.sIcon) {
						var oImage = cElement.appendImage(this.oLabel, pxConst.sGraphicUrl + '/' + this.sIcon, null, this.iIconSize)
						cElement.addClassName(oImage, 'icon')
						//if (this.iToolbarType == pxConst.TOOLBAR_VERTICAL) {
						//	oImage.style.marginLeft = '0.72em'
						//}
					} else {
						var oImage = cElement.appendImage(this.oLabel, pxConst.sGraphicUrl + '/dummy16.png', null, this.iIconSize)
					}

					this.oLabel.appendChild(
						document.createTextNode(this.sTitle || '')
					)
					this.oLabel.title = ''
					this.oLabel.onclick = cFunction.bindEvent(this.showContextMenu, this)

					if (this.sLabelDrop) {
						this.oLabel.onmouseover = cFunction.bindEvent(this._labelMouseOver, this)
						this.oLabel.onmouseout = cFunction.bindEvent(this._labelMouseOut, this)
						this.oLabel.onmouseup = cFunction.bindEvent(this._labelMouseUp, this)
					}
					this._setIconState()
				break
			}
		
			// enshure there is a default action
			if (!this.sDefault) {
				for (var a in this.oActionData) {
					this.sDefault = a
					break
				}
			}
		
			if (this.sDefault) {
				this.showAction(
					this.sDefault,
					this.oDefaultParameters
				)
			}

			this.bInitialized = true
		},




		showContextMenu: function(oEvent)
		{
			var cArray = px.lang.Array

			if (px.util.getLength(this.oActionData) > 1)
			{
				pxp.setActiveControl(this)
				var oCm = px.ui.ContextMenu
				oCm.clear()
		
				for (var sAction in this.oActionData)
				{
					if (this.aAllowedActions) {
						if (!cArray.contains(this.aAllowedActions, sAction)) {
							continue
						}
					}
					
					var sIcon = this.oActionData[sAction].sIcon
					var sClass
					if (this.bHighlightLoaded) {
						if (this.oActions[sAction] == this.oSelected) {
							var sClass = 'pxCMSelected'
						} else if (this.oActions[sAction]) {
							var sClass = 'pxCMLoaded'
						} else {
							var sClass = null
						}
						if (sClass) {
							sIcon = sIcon.replace('.png', 'Loaded.png')
						}
					}

					oCm.addItem(
						sAction,
						this.oActionData[sAction].sTitle,
						sIcon,
						false,
						sClass
					)
				}

				if (this.addAdditionalMenuItems instanceof Function) {
					this.addAdditionalMenuItems()
				}

				oCm.show(oEvent)
			}
			return false
		},




		callAction: function(sAction) {
			this.changeAction(sAction)
		},




		saveNew: function(oAction)
		{
			if (this.oDefaultParameters._bNew) {
		
				var sPath = this.oDefaultParameters.sPath
		
				var oForm = oAction.oChild.oForm
				if (oForm && oForm.px_sRelDir) {
					var oSelectDir = oForm.px_sRelDir
					var sRelDir = oSelectDir.options[oSelectDir.selectedIndex].value
				} else {
					var sRelDir = px.util.dirname(sPath)
				}
				var sNewType = this.oDefaultParameters.sType
				var sNewName = px.util.getNewFilename(px.util.basename(sPath), sNewType)
				if (sNewName != null) {
		
					var sNewPath = px.util.buildPath(sRelDir, sNewName)
					var oListview = pxp.getListview()
		
					var oResult = px.io.Request.exists(oListview.oParameters.sShare, sNewPath)
					if (oResult.bOk) {
						if (oResult.bDirectory) {
							alert(oTranslation['error.objectExists'])
							return false
						} else {
							if (!confirm(oTranslation['allowOverwrite'])) {
								return false
							}
						}
					}
		
					//alert(this.oDefaultParameters.sPath + ' -> ' + sNewPath)
		
					if (sPath != sNewPath) {
						this.oParent.oParent.renameAction(
							sPath,
							sNewPath,
							sNewPath
						)
					}
					//delete this.oDefaultParameters._bNew
				} else {
					return false
				}
			}
			return true
		},




		renameAction: function(sOldId, sId, sTitle)
		{
			if (!this.oActions[sOldId]) {
				return false
			}
			if (this.oActions[sId]) {
				alert('Action "' + sId + '" already exists')
				return false
			}

			this.oToolbars[sId] = this.oToolbars[sOldId]

			var oAction = this.oActions[sId] = this.oActions[sOldId]
			var oActionData = this.oActionData[sId] = this.oActionData[sOldId]

			oAction.sId = sId
			oActionData.sTitle = (oActionData.sTitle.indexOf('*') === 0 ? '*' : '') + sTitle
			oAction.rename()

			/*
		 	switch (this.iMenuType) {
		 		case pxConst.MENU_TABS:
					// B U I L D  M E !
		 			break
		 		case pxConst.MENU_BUTTON:
		 			var _sLabelTitle = (this.sTitle ? this.sTitle + ': ' : '') + sId
		 			this.oLabel.title = _sLabelTitle
					this.oLabel.childNodes[1].nodeValue = this.bTitleOnly ? this.sTitle : _sLabelTitle
		 			break
		 	}
		 	*/

			delete this.oActions[sOldId]
			
			this.updateMenu()
		},




		removeAction: function()
		{
			if (!this.bInitialized) {
				return false
			}

			if (this.oSelected.bChanged) {
				if (!confirm(oTranslation['discardChanges'])) {
					this.oSelected.showChanged()
					return false
				}
			}

			var sAction = this.oSelected.sId

			var oDiv

			oDiv = this.oToolbars[sAction].oDiv
			this.oToolbars[sAction].dispose()
			this._oToolbarNode.removeChild(oDiv)
			
			oDiv = this.oActions[sAction].oDiv
			this.oActions[sAction].dispose()
			this.oActionData[sAction].dispose()

//			pxp.checkSubTree(oDiv)

			this.oParentNode.removeChild(oDiv)

			delete this.oSelected
			delete this.oActions[sAction]
			delete this.oActionData[sAction]
			delete this.oToolbars[sAction]

			switch (this.iMenuType) {
				case pxConst.MENU_TABS:
					this.oTabview.removeTab(sAction)
					if (this.oTabview.iTabCount > 0) {
						this.oTabview.setSelected(this.oTabview.oTabs[px.util.getLastProperty(this.oTabview.oTabs)].sId)
					}
	  			break
	  		case pxConst.MENU_BUTTON:
	  			if (px.util.getLength(this.oActions) > 0) {
	  				this.changeAction(px.util.getLastProperty(this.oActions))
					} else {
						var _sLabelTitle = (this.sTitle ? this.sTitle + ': ' : '')
						this.oLabel.title = _sLabelTitle
						this.oLabel.childNodes[1].nodeValue = this.bTitleOnly ? this.sTitle : _sLabelTitle
					}
					this._setIconState()
	  			break
	  	}
	  	
	  	if (px.util.getLength(this.oActionData) == 0) {	  	
		  	px.html.Element.addClassName(this._oToolbarNode, 'pxBarDisabled')
	  	}

			return false
		},




		updateMenu: function()
		{
			var cElement = px.html.Element
			var sId = this.oSelected.sId
			switch (this.iMenuType) {
				case pxConst.MENU_TABS:
					this.oTabview.setSelected(sId)
					break
				case pxConst.MENU_BUTTON:
					var _sLabelTitle = (this.sTitle ? this.sTitle + ': ' : '') + this.oActionData[sId].sTitle
					this.oLabel.title = _sLabelTitle
					this.oLabel.childNodes[1].nodeValue = this.bTitleOnly ? this.sTitle : _sLabelTitle
					break
			}
		},




		addActions: function(aBaseActions, oParameters)
		{
			var cArray = px.lang.Array
			var sType = oParameters.sType

			if (pxp.oTypes[sType].aSupertypes instanceof Array) {
				var aSupertypes = pxp.oTypes[sType].aSupertypes.concat(Array(sType))
			} else {
				var aSupertypes = Array(sType)
			}

			var sDefaultAction
			for (var i=0; i<pxp.oTypes[sType].aDefaultActions.length; i++) {
				var sDefault = pxp.oTypes[sType].aDefaultActions[i]
				if (cArray.contains(aBaseActions, pxp.oActions[sDefault][2])) {
					var sDefaultAction = sDefault
				}
			}

			var sDefaultAction = oParameters._sCalledAction || sDefaultAction

			for (var a in pxp.oActions)
			{
				if (a.indexOf('__') > -1 && a != sDefaultAction) {
					continue
				}

				var sActionType = a.substr(0, a.indexOf('_'))
				if (cArray.contains(aBaseActions, pxp.oActions[a][2]) && cArray.contains(aSupertypes, sActionType)) {				
					if (px.action[a] && !px.action[a].run) {
						this.addAction(
							a,
							oTranslation['action.' + a],
							px.action[a],
							a == sDefaultAction,
							'actions/' + a
						)
					}
				}
			}
		},




		_labelMouseOver: function(oEvent) {
			if (pxp.bDragging && pxp.sDragType == this.sLabelDrop) {
				px.html.Element.addClassName(this.oLabel, 'drop')
			}			
		},




		_labelMouseOut: function(oEvent) {
			px.html.Element.removeClassName(this.oLabel, 'drop')
		},




		_labelMouseUp: function(oEvent) {
			if (px.html.Element.hasClassName(this.oLabel, 'drop')) {
				pxp.oSelectedControl.callAction(null, oEvent)
			}
		},



		
		_setIconState: function(sValue) {
			if (px.util.getLength(this.oActionData) > 1) {
				if (!this.oLabel.childNodes[2]) {
					px.html.Element.appendImage(this.oLabel, pxConst.sGraphicUrl + '/' + (this.sDropDownIcon || 'dropDown.png'))
				}
			} else {
				if (this.oLabel.childNodes[2]) {
					this.oLabel.removeChild(this.oLabel.childNodes[2])
				}
			}
		}
	}
)
	