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
px.Class.define('px.ui.listview.Listview',
{
  extend: px.io.RemoteView,

  construct: function(oParent, oParentNode)
	{
		this.base(arguments, oParent, oParentNode)
		
		var cFunction = px.lang.Function

		this.oColumns = {}
		
		this.bChecklist = false
		this.bHideExtension = false

		this.aItems = []
		this.oSelected = {}

		this.iVisibleItems

		this.onDirChange

		this._iRowHeight
		this._iOffset = 0
		this._iWidth = -1
		this._iHeight = -1
		this._bScrollH = false
		this._bScrollV = false

		this._bEditing = false
		this._oEditCell = null

		this._iScrollbarSize = pxp.bIe ? 19 : 20

		px.html.Element.addClassName(oParentNode, 'noSelect')

		this._oTable = document.createElement('table')
		var oTable = this._oTable
		oTable.cellSpacing = 0
		oTable.cellPadding = 0
		oTable.className = 'pxListview'
		oParentNode.appendChild(oTable)

		this._oTableHead = document.createElement('thead')
		oTable.appendChild(this._oTableHead)

		this._oTableBody = document.createElement('tbody')
		oTable.appendChild(this._oTableBody)

		this._oScrollBar = document.createElement('div')
		var oScroll = this._oScrollBar
		oScroll.className = 'scrollBar'
		oScroll.style.width = this._iScrollbarSize + 'px'
		oParentNode.appendChild(oScroll)
		oScroll.onscroll = cFunction.bindEvent(this.scrollBarVChange, this)
		oParentNode.onscroll = cFunction.bindEvent(this.scrollBarHChange, this)

		oScroll.appendChild(document.createElement('div'))

		oParentNode.onmousewheel = cFunction.bindEvent(this.mouseWheelChange, this)
		if (oParentNode.addEventListener) {
			oParentNode.addEventListener('DOMMouseScroll', oParentNode.onmousewheel, false)
		}
	},

	destruct: function(bRemove)
	{
		// Config menu icon
		this._oTableHead.firstChild.firstChild.onclick = null

		this._oScrollBar.onscroll = null
		this.oParentNode.onscroll = null

		if (this.oParentNode.removeEventListener) {
			this.oParentNode.removeEventListener('DOMMouseScroll', this.oParentNode.onmousewheel, false)
		}

		this.oParentNode.onmousewheel = null

		this._disposeFields('oSelected', 'onDirChange', '_oScrollBar', '_oTable', '_oTableHead', '_oTableBody')
		this._disposeContents('oColumns', 'aItems')
	}
})

Object.extend(
	px.Statics,
	{
		_oColumnSelection: {
			base: ['iBytes', 'dModified', 'sType'],
			os: ['sOSystemPermissions', 'sOSystemOwner', 'sOSystemGroup'],
			full: ['aTags', 'sTitle', 'sDescription']
		}
	}
)

Object.extend(
	px.Proto,
	{	
		build: function()
		{
			var cFunction = px.lang.Function
			var cElement = px.html.Element
		
			this._iWidth = this.oParentNode.offsetWidth
			this._iHeight = this.oParentNode.offsetHeight

			var iDirectories = 0
			var oDoc = document

			if (this._oTableHead.childNodes.length > 0) {
				this.clear()
			}

			var oTr = this._oTableHead.insertRow(0)
			oTr.className = 'header'
			
			if (this.bChecklist) {
				var oTh = oDoc.createElement('th')
				var sClass = 'checkbox'
				oTh.className = sClass
				oTr.appendChild(oTh)
			}
			
			var oTh = oDoc.createElement('th')
			var sClass = 'icon'
			oTh.className = sClass
			oTr.appendChild(oTh)

			cElement.appendImage(oTh, pxConst.sGraphicUrl + '/configure.png')
			oTh.onclick = cFunction.bindEvent(this._showConfig, this)

			for (var c in this.oColumns)
			{
				var oCol = this.oColumns[c]
				//oCol.oParent = this
				oCol.oTh = oDoc.createElement('th')
				oCol.oTh.id = 'header_' + c
				oCol.oTh.style.width = this.oColumns[c]['iWidth'] + 'px'
				oCol.oTh.appendChild(oDoc.createTextNode(oTranslation['property.' + c]))

				oTr.appendChild(oCol.oTh)

				var oImage = new Image()
				oImage.className = 'orderIndicator'
				if (this._sOrderBy != oCol.sId) {
					oImage.style.display = 'none'
				}
				oImage.src = pxConst.sGraphicUrl + '/' + this._sOrderDirection + '.png'
				oImage.style.width = oImage.style.height = 13 + 'px'
				oCol.oTh.appendChild(oImage)

				oCol.oTh.onmousemove = cFunction.bindEvent(oCol.headerMouseMove, oCol)
				oCol.oTh.onmousedown = cFunction.bindEvent(oCol.headerMouseDown, oCol)
				oCol.oTh.onmouseup = cFunction.bindEvent(oCol.headerMouseUp, oCol)
				oCol.oTh.onmouseover = cFunction.bindEvent(oCol.headerMouseOver, oCol)
				oCol.oTh.onmouseout = cFunction.bindEvent(oCol.headerMouseOut, oCol)
			}

			var iContainerHeight = this.oParentNode.offsetHeight - oTr.offsetHeight
			if (this._bScrollH) {
				iContainerHeight -= this._iScrollbarSize
			}
			var iRowSum = 0
			this.iVisibleItems = 0
			var oRowClass = px.ui.listview.Row

			while (iRowSum < iContainerHeight)
			{
				var oRow = new oRowClass(this)

				this.aItems.push(oRow)

				oRow.oTr = this._oTableBody.insertRow(this._oTableBody.rows.length)
				oTr = oRow.oTr

				oTr.style.visibility = 'hidden'

				if (this.bChecklist) {
					var oTd = oTr.insertCell(oTr.cells.length)
					oTd.className = 'icon'					
					var oImage = new Image()
					oImage.src = pxConst.sGraphicUrl + '/checkbox.png'
					oTd.appendChild(oImage)
				}

				// icon
				var oTd = oTr.insertCell(oTr.cells.length)
				oTd.className = 'icon'
				cElement.appendImage(oTd, pxConst.sGraphicUrl + '/dummy16.png')

				for (var c in this.oColumns)
				{
					oTd = oTr.insertCell(oTr.cells.length)
					oTd.style.textAlign = this.oColumns[c].sAlign
					cElement.addClassName(oTd, c)
					oTr.appendChild(oTd)

					var sType = this.oColumns[c].sType

					switch (sType) {
						case 'link':
							oRow.oA = oDoc.createElement('a')
							var oA = oRow.oA
							oA.href = pxConst.DUMMY_LINK
							oA.appendChild(oDoc.createTextNode('X'))
							oTd.appendChild(oA)
							oA.onclick = cFunction.bindEvent(oRow.fileClick, oRow)
							oRow.oNameNode = oA
							break
						case 'select':
							oRow.oA = oDoc.createElement('a')
							var oA = oRow.oA
							oA.href = pxConst.DUMMY_LINK
							oA.appendChild(oDoc.createTextNode('X'))
							oTd.appendChild(oA)
							oTd.onclick = cFunction.bindEvent(oRow.editClick, oRow)
							break
						default:
							oTd.appendChild(oDoc.createTextNode('X'))
							break
					}
				}

				oTr.onclick = cFunction.bindEvent(oRow.itemClick, oRow)
				oTr.ondblclick = cFunction.bindEvent(oRow.itemDblClick, oRow)
				oTr.onmousedown = cFunction.bindEvent(oRow.mouseDown, oRow)
				oTr.onmouseover = cFunction.bindEvent(oRow.rowMouseOver, oRow)
				oTr.onmouseout = cFunction.bindEvent(oRow.rowMouseOut, oRow)
				oTr.onmouseup = cFunction.bindEvent(oRow.rowMouseUp, oRow)

				if (!this._iRowHeight) {
					this._iRowHeight = oTr.offsetHeight
				}

				this.iVisibleItems++
				iRowSum += this._iRowHeight
			}

			oTr = null
			oTh = null
			oImage = null
			oTd = null
			oRow = null
		},

		clear: function()
		{
			// Config menu icon
			this._oTableHead.firstChild.firstChild.onclick = null

			for (var c in this.oColumns) {
				var oColumn = this.oColumns[c]
				var oTh = oColumn.oTh
				oTh.onmousemove = null
				oTh.onmousedown = null
				oTh.onmouseup = null
				oTh.onmouseover = null
				oTh.onmouseout = null
				oColumn.oTh = null
//				this.oColumns[c].dispose()
			}

			for (var r=0, m=this.aItems.length; r<m; r++) {
				this.aItems[r].dispose()
				this.aItems[r] = null
			}

			var oRow = this._oTableHead.firstChild
			while (oRow.firstChild) {
				oRow.deleteCell(0)
			}
			this._oTableHead.deleteRow(0)
	
			while (this._oTableBody.firstChild) {
				var oRow = this._oTableBody.firstChild
				while (oRow.firstChild) {
					oRow.deleteCell(0)
				}
				this._oTableBody.deleteRow(0)
			}

			this.aItems.length = 0
		},

		_update: function(sPath)
		{
			if (this.oParameters.sSearchQuery) {
				if (!this.oColumns['sRelDir']) {
					this.clear()
					this.addColumn('sRelDir', 140, 'text', 'left', null, true)
					this.build()
				}
			} else {
				if (this.oColumns['sRelDir'] && this.oColumns['sRelDir'].bTemp) {
					delete this.oColumns['sRelDir']
					this.build()
				}
			}
		
			if (this.oParameters._bReload) {
				this._fill(null, true)
			} else {
				this._fill(0, true)
			}
		},

		_fill: function(iOffset, bUpdate)
		{
			var sPath = this.oParameters.getPath()
		
			iOffset = this._reloadCheck(sPath, iOffset, bUpdate)

			if (iOffset == -1) {
				return false
			}

			var aObjects = this.oResults[sPath]
			var oOptions = this.oSettings[sPath].oOptions

			for (var r=0,m=this.iVisibleItems; r<m; r++)
			{
				var oObject = aObjects[r + iOffset]
				var oRow = this.aItems[r]
				oTr = oRow.oTr

				if (!oObject) {
					oTr.style.visibility = 'hidden'
					oRow.iIndex = -1
					oTr.id = null
					continue
				} else {
					oTr.style.visibility = ''
					oRow.iIndex = (r + iOffset)
					oTr.id = 'r' + oRow.iIndex
				}

				var sPath = oObject.sRelDir
				if (sPath != '/') {
					sPath += '/'
				}
				oRow.sFilename = sPath + oObject.sName
				oRow.bDirectory = oObject.bDirectory

				var sExtendedType = pxp.getExtendedType(oObject.sType, oObject.sExtension)
				var sModule = pxp.oTypes[oObject.sType]['sModule']
				
				var bSelected = this.oSelected[oRow.sFilename]

				if (this.bChecklist) {
					oTr.childNodes[0].firstChild.src =
						pxConst.sGraphicUrl + '/checkbox' + (bSelected ? 'Checked' : '') + '.png'
					var iPos = 1
				} else {
					var iPos = 0
				}				

				oTr.childNodes[iPos].firstChild.src = pxConst.sModuleUrl + '/' + sModule + '.pxm/graphics/types/' + sExtendedType + '.png'

				var iCellCount = iPos+1

				for (var c in this.oColumns)
				{
					var oColumn = this.oColumns[c]

					oTd = oTr.childNodes[iCellCount]
					oTd.style.textAlign = oColumn.sAlign

					var mValue = oObject[c] || ''

					if (oColumn.oFormat) {
						mValue = oColumn.oFormat(mValue)
					}

					switch (oColumn.sType)
					{
						case 'link':
							var oA = oTd.firstChild
							if (c == 'sName') {
								if (bSelected) {
									oA.className = 'selected'
								} else {
									if (oA.className) {
										oA.className = null
									}
								}
								if (this.bHideExtension) {
									mValue = oObject.sId
								}
							}
							oA.firstChild.nodeValue = mValue
							break
						case 'type':
							oTd.firstChild.nodeValue = oTranslation['type.' + sExtendedType]
							break
//						case 'date':
//							oTd.firstChild.nodeValue = px.util.formatDateTime(mValue)
//							break
						case 'select':
							oTd.firstChild.firstChild.nodeValue =
								oOptions[oObject.sType][c][mValue]
							break
						case 'multiple':
							var sValue = ''
							if (mValue.join) {
								sValue = mValue.join(', ')
//								sValue = mValue.toArray().join(',')
							}
							oTd.firstChild.nodeValue = sValue
							break
						case 'array':
							oTd.firstChild.nodeValue = mValue.join ? mValue.join(', ') : ''
							break
						default:
							oTd.firstChild.nodeValue = mValue
							break
					}
					iCellCount++
				}
			}
		
			this._iOffset = iOffset
		
			var iHeight = 0
			if (aObjects.length) {
				iHeight = Math.round(aObjects.length * this._iRowHeight + 2 * this._iRowHeight)
			}
			if (iHeight > this._oScrollBar.offsetHeight) {
				this._oScrollBar.firstChild.style.height = iHeight + 'px'
				this._oScrollBar.style.visibility = ''
			} else {
				this._oScrollBar.style.visibility = 'hidden'
			}

			if (!this._bReloading) {
				delete this._bPopulating
				delete this._bReloading
			}
		},
		
		addColumn: function(sId, iWidth, sType, sAlign, oFormat, bTemp) {
			if (!this.oColumns[sId]) {
				this.oColumns[sId] = new px.ui.listview.Column(this, sId, iWidth, sType, sAlign, oFormat, bTemp)
			}
		},
		
		removeColumn: function(sId) {
			this.oColumns[sId].dispose()
			delete this.oColumns[sId]
		},

		dragCleanup: function()
		{
			px.io.RemoteView.dragCleanup.call(this)
		
			var oDragColumn = $('dragColumn')
			if (oDragColumn) {
				oDragColumn.style.display = 'none'
			}
			oDragColumn = null
			
			var iPos = this.bChecklist ? 2 : 1
		
			var aHeaders = pxp.oSelectedControl._oTableHead.firstChild.childNodes
			
			for (var i=iPos; i<aHeaders.length; i++)
			{
				if (aHeaders[i].className) {
					if (px.html.Element.hasClassName(aHeaders[i], 'drop')) {
						var oNewColumns = {}, iColumnCounter = iPos
						for (var c in this.oColumns) {
							if (iColumnCounter == i) {
								oNewColumns[this.oActiveItem.sId] = this.oActiveItem
								oNewColumns[c] = this.oColumns[c]
							} else {
								if (this.oColumns[c] != this.oActiveItem) {
									oNewColumns[c] = this.oColumns[c]
								}
							}
							iColumnCounter++
						}
						this.oColumns = oNewColumns
		
						this.build()
						this._fill()
						
						return
					}
					aHeaders[i].className = null
				}
			}
		},
		
		clearSelection: function() {
			if (!this.bChecklist) {
				delete this.oSelected
				this.oSelected = {}
				this._fill()
			}
		},

		sort: function(sColumn) {
			this.oColumns[this._sOrderBy].oTh.childNodes[1].style.display = 'none'
			this._clientSort(sColumn)
			var oImage = this.oColumns[this._sOrderBy].oTh.childNodes[1]
			oImage.src = pxConst.sGraphicUrl + '/' + this._sOrderDirection + '.png'
			oImage.style.display = 'inline'
		},

		scrollBarVChange: function(oEvent) {
			this._fill(
				Math.round(this._oScrollBar.scrollTop / this._iRowHeight + 0.4555555)
			)
		},
		
		scrollBarHChange: function(oEvent) {
			var iDiff = (this._oTable.offsetWidth - this.oParentNode.offsetWidth) - this.oParentNode.scrollLeft
			this._oScrollBar.style.left = (this._oTable.offsetWidth - iDiff - this._oScrollBar.offsetWidth) + 'px'
		},

		resize: function(bFinished) {
		
			//this._oTableContainer.style.width = this.oParentNode.offsetWidth - 100 + 'px'
			if (bFinished) {
				/*
				if (
					this._iWidth != this.oParentNode.offsetWidth ||
					this._iHeight != this.oParentNode.offsetHeight
				) {
					alert("changed")
					alert(this._iHeight + '    ' + this.oParentNode.offsetHeight)
				}
				*/

				this._bScrollH = this.oParentNode.offsetWidth < this._oTable.offsetWidth
				this._bScrollV = this.oParentNode.offsetHeight < this._oTable.offsetHeight

				/*
				var iScrollHeight = this.oParentNode.offsetHeight
				if (this._bScrollH) {
					iScrollHeight -= this._iScrollbarSize
				}

				* this._oScrollBar.style.height = this.oParentNode.offsetHeight + 'px'
				*/

				//document.title = this._bScrollV

				this.build()
				this._fill()
				this.scrollBarHChange()
				this.scrollBarVChange()
			}
		},

		mouseWheelChange: function(oEvent) {
			var iDelta = oEvent.detail || (oEvent.wheelDelta / 40 * -1)
			var iNewOffset = this._iOffset + iDelta
			this._oScrollBar.scrollTop = iNewOffset * this._iRowHeight
			if (iNewOffset >= 0) {
				this._fill(iNewOffset)
			}
		},

		_showConfig: function(oEvent)
		{
			var oCm = px.ui.ContextMenu
			var oColumns = px.ui.listview.Listview._oColumnSelection

			pxp.setActiveControl(this)

			oCm.clear()

			for (var sId in pxp.oTypes['pxMeta'].aProperties) {
				oCm.addItem(
					'pxCol|' + sId,
					oTranslation['property.' + sId],
					pxConst.sGraphicUrl + '/' + (this.oColumns[sId] ? 'tick' : 'dummy16') + '.png'
				)
			}

			oCm.show(oEvent)
		},

		callActionSub: function(sAction, oEvent)
		{
			var cArray = px.lang.Array
			var oColumns = px.ui.listview.Listview._oColumnSelection

			if (sAction.indexOf('pxCol|') === 0)
			{
				var sId = sAction.substr(sAction.indexOf('|')+1)

				this.clear()

				if (this.oColumns[sId]) {
					this.removeColumn(sId)
				} else {
					var aProperties = pxp.oTypes['pxMeta'].aProperties
					var oProperty = aProperties[sId]
					this.addColumn(
						sId,
						140,
						oProperty ? oProperty.sDataType : 'text',
						'left'
					)
					if (oProperty.sFormat) {
						this.oColumns[sId].oFormat = px.util.Format[oProperty.sFormat]
					}
				}

				var bOs = false, bFull = false
				for (var c in this.oColumns) {
					if (cArray.contains(oColumns.os, c)) bOs = true
					if (cArray.contains(oColumns.full, c)) bFull = true					
				}
				this.oParameters.bOsPermissions = bOs
				this.oParameters.bFull = bFull
				
//				alert(this.oParameters.bFull)

				this.build()
				this.update()
			}
			else
			{
				this.callAction.call(this, sAction, oEvent)
			}
		}
	}
)