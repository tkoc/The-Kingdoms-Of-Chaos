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
px.Class.define('px.ui.Propertyview',
{
	extend: px.io.RemoteView,
	
	construct: function(oParent, oParentNode)
	{
		this.base(arguments, oParent, oParentNode)
		
//		px.html.Element.addClassName(oParentNode, 'pxPropertyview')

		this.aWidgets = []
		this.oGroups = {}

		this.bReadonly = false
		this.sSaveAction = 'editProperties'

		this.oParameters.bFull = true
		this.oParameters.bFillOptions = true
		this.oParameters.bFile = true

		this.sId = px.util.getRandomId()

		this.oForm = document.createElement('form')
		this.oForm.method = 'post'
		this.oForm.action = pxp.sUrl

		oParentNode.appendChild(this.oForm)

		this._oTable = document.createElement('table')
		this._oTable.cellSpacing = 3
		this._oTable.cellPadding = 0
		this._oTable.className = 'pxPropertyview'
		this.oForm.appendChild(this._oTable)

		this._oTableBody = document.createElement('tbody')
		this._oTable.appendChild(this._oTableBody)
	},

	destruct: function()
	{
		for (var sGroup in this.oGroups) {
			this.oGroups[sGroup].firstChild.firstChild.onclick = null
			this.oGroups[sGroup] = null
		}

		this._disposeFields('oForm', '_oTable', '_oTableBody', 'oObject', 'oGroups')
		this._disposeContents('aWidgets')
	}
})

Object.extend(
	px.Proto,
	{
		_update: function(sPath)
		{
			var cElement = px.html.Element
			
			if (this._oTableBody.firstChild) {
				this.clear()
			}

			this.oObject = this.oResults[sPath][0]

			var sType = this.oObject.sType || this.oObject.sId
			var oType = pxp.oTypes[sType]

			if (oType.sSupertype) {
				var aSupertypes = pxp.oTypes[sType].aSupertypes.concat([sType])
			} else {
				var aSupertypes = [sType]
			}

			var sCurrentType = null
			var bFirst = true
			var oPrevious
			var aGroups = []

			for (var t=0; t<aSupertypes.length; t++)
			{
				var oType = pxp.oTypes[aSupertypes[t]]

				for (var sProperty in oType.aProperties)
				{
					if (sCurrentType != aSupertypes[t])
					{
						if (oPrevious) {
							cElement.addClassName(oPrevious, 'last')
						}

						sCurrentType = aSupertypes[t]
						var oTr = document.createElement('tr')

						this.oGroups[sCurrentType] = this._oTableBody.appendChild(oTr)

						var oTd = document.createElement('td')
						oTd.colSpan = 2
						oTd.className = 'group'
						oTr.appendChild(oTd)
						
						var oA = document.createElement('a')
						oA.href = pxConst.DUMMY_LINK
						
						oTd.appendChild(oA)
						oA.onclick = px.lang.Function.bind(this.switchGroup, this, sCurrentType)

						var sExtendedType = pxp.getExtendedType(sCurrentType)

						cElement.appendImage(oA, pxConst.sModuleUrl + '/System.pxm/graphics/collapse.png')

						oA.appendChild(document.createTextNode(' ' + oTranslation['type.' + sExtendedType]))	

						bFirst = true
					}

					oTr = document.createElement('tr')
					this._oTableBody.appendChild(oTr)

					oTd = document.createElement('td')
					oTd.className = 'label' + (bFirst ? ' first' : '')
					oTd.appendChild(document.createTextNode(oTranslation['property.' + sProperty]))
					oTr.appendChild(oTd)

					oTd = document.createElement('td')
					oTd.id = this.sId + '_' + sProperty
					oTd.className = 'value' + (bFirst ? ' first' : '')

					oTr.appendChild(oTd)

					var oProperty = oType.aProperties[sProperty]
					var sWidget = oProperty.sWidget

					if (!pxp.oData[this.oParameters._sOptionCacheKey]) {
						pxp.oData[this.oParameters._sOptionCacheKey] = this.oSettings[sPath].oOptions[sType]
					}

					oProperty.oOptions = pxp.oData[this.oParameters._sOptionCacheKey][sProperty]

					this.aWidgets.push(
						new (px.ui.widget[sWidget] || px.ui.widget.Input)(
							this, oTd, oType.sId, sProperty, this.oObject[sProperty]
						)
					)

					bFirst = false
				}

				oPrevious = oTd
			}
			oPrevious = null

			var aExpandSubtypes = pxp.oTypes[sType].aExpandSubtypes			
			if (aExpandSubtypes) {
				for (var sGroup in this.oGroups) {
					if (!px.lang.Array.contains(aExpandSubtypes, sGroup)) {
						this.switchGroup(sGroup)
					}
				}
			}

//			alert(new Date().getTime() - iStart2)

			this.resize(true)
		},
		
		clear: function(bDispose)
		{
			for (var sGroup in this.oGroups) {
				this.oGroups[sGroup].firstChild.firstChild.onclick = null
				this.oGroups[sGroup] = null
			}

			this._disposeContents('aWidgets')

			this.aWidgets = []

			delete this.oObject

			if (!bDispose) {
				while (this._oTableBody.firstChild) {
					var oRow = this._oTableBody.firstChild
					while (oRow.firstChild) {
						oRow.deleteCell(0)
					}
					this._oTableBody.deleteRow(0)
				}
			}
		},

		resize: function(bFinished) {	
			for (var i=0, l=this.aWidgets.length; i<l; i++) {
				var oWidget  = this.aWidgets[i]
				if (oWidget.resize) {
					oWidget.resize(bFinished)
				}
			}
		},

		save: function()
		{
			// Remove previously set error marks
			var oTr = this._oTableBody.firstChild
			do {
				if (oTr.className != '') {
					oTr.className = ''
					oTr.firstChild.title = null
				}
			} while (oTr = oTr.nextSibling)

			var oResult = this._save({sAction: this.sSaveAction})

			if (oResult.bOk) {
				pxp.refreshView(px.util.dirname(this.oParameters.sPath))
				this.oParent.setChanged(false)
			} else {
				for (var e in oResult) {
					var oTd = $(this.sId + '_' + e)
					if (oTd) {
						var oTr = oTd.parentNode
						oTr.className = 'error'
						oTr.firstChild.title = oTranslation['error.' + oResult[e]]
					} else {
						pxp.showError({sId:oResult[e], aValues:[this.oParameters.sPath]})
					}
				}
				alert(oTranslation['error.validationError'])
			}

			return false
		},

		rename: function(sPath) {
			var oNameTd = $(this.sId + '_sName')
			oNameTd.firstChild.nodeValue = px.util.basename(sPath)
		},

		switchGroup: function(sId)
		{
			var cElement = px.html.Element

			var oTd = this.oGroups[sId].firstChild
			var oImg = oTd.firstChild.firstChild
			var bHidden = oImg.src.indexOf('expand') > -1

			oImg.src = pxConst.sModuleUrl + '/System.pxm/graphics/' + (bHidden ? 'collapse.png' : 'expand.png')
			
			var oNext = oTd.parentNode.nextSibling.firstChild

			while (oNext.className.indexOf('group') == -1) {
				var oParent = oNext.parentNode
				cElement.switchElement(oParent)
				if (!oParent.nextSibling) {
					break
				}
				oNext = oParent.nextSibling.firstChild
			}

			this.resize(true)

			return false
		}
	}
)