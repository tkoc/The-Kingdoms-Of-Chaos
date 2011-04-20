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
px.Class.define('px.io.RemoteView',
{
	extend: px.core.Object,

	construct: function(oParent, oParentNode)
	{
		this.oParent = oParent
		this.oParentNode = oParentNode

		this.oActiveItem
		this.bControl = true
		this.oResults = {}
		this.oSettings = {}
		this.oParameters = new px.io.RequestParameters()
		
		this.iObjectsPerPage = 50

		this._sOrderBy = 'sName'
		this._sOrderDirection = 'asc'
		this._bBuild = false
	},

	destruct: function() {
		this._disposeFields('oParent', 'oParentNode', 'oParameters', 'oActiveItem')
	}
})

Object.extend(
	px.Statics,
	{
		dragCleanup: function() {
			var oDragable = $('dragable')
			if (oDragable) {
				while (oDragable.firstChild) {
					oDragable.removeChild(oDragable.firstChild)
				}
				document.body.removeChild(oDragable)
			}
		},

		sortActions: function(x1, x2) {
			if (x1.sBaseAction == x2.sBaseAction) {
				//return -1
				return (x1.sTitle > x2.sTitle) ? 1 : -1
			} else {
				return (x1.sBaseAction < x2.sBaseAction) ? 1 : -1
			}
		}
	}
)
	

Object.extend(
	px.Proto,
	{
		_clientSort: function(sColumn)
		{
			var sPath = this.oParameters.getPath()
			var sNewOrder = sColumn.substr(sColumn.indexOf('_') + 1)
			
			var bResult = this.oResults[sPath].sort
			if (bResult) {
				this.oResults[sPath].sort(this.getSortFunction(sNewOrder))
			}
		
			if (sNewOrder == this._sOrderBy) {
				if (this._sOrderDirection == 'asc') {
					this._sOrderDirection = 'desc'
					if (bResult) {
						this.oResults[sPath].reverse()
					}
				} else {
					this._sOrderDirection = 'asc'
				}
			} else {
				this._sOrderDirection = 'asc'
			}
			this._sOrderBy = sNewOrder
			this._fill(0)
		},
		
		getSortFunction: function(sProperty) {
			return function(oObject1, oObject2)
			{
				if (oObject1.bDirectory == oObject2.bDirectory) {
					if (oObject1[sProperty] == oObject2[sProperty]) {
						return 0
					} else {
						return oObject1[sProperty] < oObject2[sProperty] ? -1 : 1
					}
				} else {
					return oObject1.bDirectory ? -1 : 1
				}
			} 
		},

		update: function(oParameters)
		{
			if (!this._bBuild && this.build) {
				this.build()
				this._bBuild = true
			}

			if (oParameters) {
				this.oParameters.set(oParameters)
			}

			if (!this.oParameters._bReload) {
				this.oParameters.iOffset = 0
			}

			new px.io.Request(
				{
					sParameters: this.oParameters.getUrl(),
					onComplete: px.lang.Function.bind(this._updateCallback, this)
				}
			)
		},

		_updateCallback: function(oRequest)
		{
			try {
				var oJson = eval('(' + oRequest.responseText + ')')
			} catch(e) {
				pxp.showError(oRequest.responseText)
				return
			}

			if (oJson.bError) {
				pxp.showError(oJson)
				return
			}

			var sPath = oJson.sDirectory
			var oParameters = this.oParameters

			if (oParameters._bReload) {
				if (px.util.getLength(oJson.mResult) == 0) {
					delete this._bReloading
					delete this._bPopulating
					delete oParameters._bReload
					return false
				}

				this.oResults[sPath] = this.oResults[sPath].concat(oJson.mResult)

		//		Object.extend(this.oSettings[sPath], oJson.oSettings)

				Object.extend(this.oSettings[sPath].aAllowedActions, oJson.oSettings.aAllowedActions)
				Object.extend(this.oSettings[sPath].oOptions, oJson.oSettings.oOptions)

			} else {
				this.oResults[sPath] = oJson.mResult
				this.oSettings[sPath] = oJson.oSettings
			}

			oParameters.sSearchQuery = oJson.sSearchQuery

			if (this.oResults[sPath].length > 1 && this.oResults[sPath].sort) {
				this.oResults[sPath].sort(this.getSortFunction(this._sOrderBy))
				if (this._sOrderDirection == 'desc') this.oResults[sPath].reverse()
			}

			//this._update(sPath)	
			window.setTimeout(px.lang.Function.bind(this._update, this, sPath), 10)

			if (!oParameters._bReload && this.oParent.updateInterface) {
				this.oParent.updateInterface()
			}

	//		var jetzt2 = new Date()
	//		var iStop = jetzt2.getTime()
			//document.title = (parseInt(document.title) + 1)  + ' - ' + (iStop - iStart)
		
			if (oJson.iPhpRuntime) {
				//pxp.log('Runtime: ' + oJson.iPhpRuntime)	
		//		document.title = oJson.iPhpRuntime
			}
			//document.title = new Date() - pxp.iStartTime
		},
		
		_save: function(oParameters, sUrlParameters)
		{
			if (this.oForm) {
				px.html.Form.disable(this.oForm)
			}

			if (oParameters) {
				this.oParameters.set(oParameters)
			}

			var sBody =
				'sShare=' + encodeURIComponent(this.oParameters.sShare) +
				'&sAction=' + this.oParameters.sAction +
				'&sPath=' + encodeURIComponent(this.oParameters.getPath(false))

			if (this.oParameters.sType) {
				sBody +=
					'&sType=' + encodeURIComponent(this.oParameters.sType)
			}

			if (sUrlParameters) {
				sBody +=
					'&' + sUrlParameters
			} else {
				sBody +=
					'&' + px.html.Form.serialize(this.oForm)
			}

			var oResult = px.io.Request.post(sBody)

			if (oResult.bOk) {
				delete this.oParent.oParent.oDefaultParameters._bNew
			}

			if (this.oForm) {
				px.html.Form.enable(this.oForm)
			}

			return oResult
		},
		
		resize: function() {
		},

		showContextMenu: function(oItem, oEvent, sType, aAllowedActions)
		{
			var cArray = px.lang.Array
			var oCm = px.ui.ContextMenu
			oCm.clear()

			var oType = pxp.oTypes[sType]
			var aActions = []

			for (var a=0; a<oType.aActions.length; a++)
			{
				var sAction = oType.aActions[a]
				var sBaseAction = pxp.oActions[sAction][2]

				if (
					!cArray.contains(aAllowedActions, sAction) ||
					sBaseAction == 'create' ||
					sAction.substr(sAction.indexOf('_') + 1, 1) == '_' ||
					(oType.bDirectory && (sBaseAction == 'open' || sBaseAction == 'batch' || sBaseAction == 'select')) ||
					(sAction == 'pxFiles_openPreview' && !px.util.previewCheck(oItem.getExtension()))
				) {
					continue
				}

				aActions.push(
					{
						sId: sAction,
						sTitle: oTranslation['action.' + sAction],
						sBaseAction: sBaseAction,
						sModule: pxp.oActions[sAction][0]
					}
				)
			}

			//if (aActions.length == 0) {
			//	return false
			//}
			
			if (oItem.bDirectory) {
				oCm.addItem(
					'__open',
					oTranslation['open'],
					pxConst.sModuleUrl + '/System.pxm/graphics/types/pxDirectories.png' 
				)
				oCm.addDivider()
			}

			aActions.sort(px.io.RemoteView.sortActions)

			if (aActions.length > 0)
			{
				var sActiveGroup = aActions[0].sBaseAction
	
				for (var a=0; a<aActions.length; a++)
				{	
					if (sActiveGroup != aActions[a].sBaseAction) {
						sActiveGroup = aActions[a].sBaseAction
						oCm.addDivider()
					}
					oCm.addItem(
						aActions[a].sId,
						aActions[a].sTitle,
						pxConst.sModuleUrl + '/' + aActions[a].sModule + '.pxm/graphics/actions/' + aActions[a].sId + '.png' 
					)
				}
				oCm.addDivider()
			}
	
			oCm.addItem(
				'__docLink',
				oTranslation['documentation'],
				pxConst.sModuleUrl + '/System.pxm/graphics/actions/pxGlobal___openDoc.png' 
			)

			delete aActions

			oCm.show(oEvent, 20)
		},

		callAction: function(sAction, oEvent)
		{
			var cArray = px.lang.Array

			if (!sAction) {
				var sType = this.oActiveItem.getType()
				var oType = pxp.oTypes[sType]

				for (var i=0; i<oType.aDefaultActions.length; i++) {
					var sDefaultAction = oType.aDefaultActions[i]
					if (pxp.oActions[sDefaultAction][2] == 'edit') {
						var sAction = sDefaultAction
						break
					}
				}
				if (!sAction) {
					var aAllowedActions = this.oSettings[this.oParameters.getPath()].aAllowedActions[sType]
					if (cArray.contains(aAllowedActions, 'pxObject_editProperties')) {
						var sAction = 'pxObject_editProperties'
					} else {
						var sAction = oType.aDefaultActions[0]
					}
				}
			}

			if (sAction == '__docLink') {
				pxp.showDoc('type/' + this.oActiveItem.getType())
				return
			}
			
			if (sAction == '__open') {
				this.oActiveItem.fileClick(oEvent)
				return
			}

			if (px.action[sAction] && px.action[sAction].run) {
				px.action[sAction].run(this)
				return
			}

			if (!px.action[sAction]) {
				alert('Action "' + sAction + '" does not exist')
				return
			}

			var sType = sAction.substr(0, sAction.indexOf('_'))

			if (pxp.oTypes[sType].aSupertypes instanceof Array) {
				var aSupertypes = pxp.oTypes[sType].aSupertypes.concat(Array(sType))
			} else {
				var aSupertypes = Array(sType)
			}
			
			/*
			if (
				cArray.contains(aSupertypes, 'pxDirectories') &&
				cArray.contains(Array('open', 'batch'), pxp.oActions[sAction][2])
			)
			{
				pxp.oShareview.oSelected.oActionviewList.showAction(
					sAction,
					{sPath: this.oActiveItem.sFilename}
				)
			}
			else
			{
			*/
				for (var sSelected in this.oSelected)
				{
					var sType = this.oSelected[sSelected].getType()
					var sPath = sSelected
					var oView = pxp.oShareview.oSelected.oActionviewFiles

		 			if (!oView.oActions[sPath]) {
		 				oView.addAction(
		 					sPath,
		 					sPath.indexOf('/') == 0 ? sPath.substr(1) : sPath,
		 					px.action.pxGlobal___openEditorview,
		 					true,
							'types/' + sType,
			 				{
								sPath: sPath,
			 					sType: sType,
			 					_sCalledAction: sAction
			 				}
		 				)
		 			}
		 			oView.showAction(sPath)			 		
				}
			//}
			return false
		},

		dirUp: function(oEvent) {
			this.oParameters.sPath = px.util.dirname(this.oParameters.sPath)
			this.oParameters.sSearchQuery = null
			this.update(0)
			if (this.onDirChange) this.onDirChange()
			return false
		},
	
		setAllowSelection: function(bAllow) {
			if (bAllow) {
				px.html.Element.removeClassName(this.oParentNode, 'noSelect')
			} else {
				px.html.Element.addClassName(this.oParentNode, 'noSelect')
			}
		},
	
		_reloadCheck: function(sPath, iOffset, bUpdate)
		{
			if (this._bPopulating && !bUpdate) {
				return -1
			}
		
			this._bPopulating = true

			if (iOffset == null) {
				iOffset = this._iOffset
			}
		
			var aObjects = this.oResults[sPath]
		
			if (!aObjects) { // skip _fill on startup resize call 
				return -1
			}
		
			if (aObjects.length >= this.iVisibleItems) {
				
				var iMaxObjects = aObjects.length
				if (this._iX) {
					var iRows = Math.round(iMaxObjects / this._iX + 0.4555555)
					iMaxObjects = iRows * this._iX
				}

				if (iOffset + this.iVisibleItems > iMaxObjects) {
					iOffset = iMaxObjects - this.iVisibleItems + (this._iX || 1)
				}
			} else {
				iOffset = 0
			}
		
			delete this._bReloading
			delete this.oParameters._bReload

			if (this.iObjectsPerPage && aObjects.length > 0 && aObjects.length % this.iObjectsPerPage == 0)
			{
				if (
					aObjects.length < this.iVisibleItems
					||
					iOffset + this.iVisibleItems > aObjects.length - 5
				)
				{
					this._bReloading = true
					this.update(
						{
							iOffset: aObjects.length,
							_bReload: true
						}
					)
				}
			}
		
			if (iOffset == 0) {
				if (aObjects.length > this.iVisibleItems) {
					this._oScrollBar.scrollTop = 0
				}
			}
			return iOffset
		}
	}
)