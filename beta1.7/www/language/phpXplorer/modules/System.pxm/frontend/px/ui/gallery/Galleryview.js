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
px.Class.define('px.ui.gallery.Galleryview',
{
	extend: px.io.RemoteView,

	construct: function(oParent, oParentNode)
	{
		this.base(arguments, oParent, oParentNode)
		
		var cFunction = px.lang.Function

		this.aItems = []
		this.oSelected = {}

		this.iVisibleItems

		this.iThumbnailSize = parseInt(this.oParent.oShare.iThumbnailSize)
		this.iPadding = 10
		this.iIconSize = 32
		this.bFloat = true
		this.aAdditionalExtensions = null
		this.bDirectUpdate = false
		this.oPreviewParameter = {}

		this.onDirChange

		this._iX
		this._iY	
		this._iItemHeight

		this._iImageLib = this.oParent.oParent.oParent.oShare.iImageLibrary
		this._iOffset = 0
		
		// flags
		// this._bLoading
		// this._bReset

		this._oLoadTimeout

		px.html.Element.addClassName(oParentNode, 'noSelect')

		this._oLoadingImage = new Image()
		this._oLoadingImage.onload = cFunction.bind(this.imageLoadCallback, this)
		this._oLoadingImage.onerror = cFunction.bind(this.imageLoadError, this)

		this._oScrollBar = document.createElement('div')
		this._oScrollBar.className = 'scrollBar'
		this._oScrollBar.style.width = pxp.bIe ? '19px' : '20px'
		oParentNode.appendChild(this._oScrollBar)
		this._oScrollBar.onscroll = cFunction.bindEvent(this.scrollBarChange, this)
		
		var oSizer = document.createElement('div')
		this._oScrollBar.appendChild(oSizer)
		
		oParentNode.onmousewheel = cFunction.bindEvent(this.mouseWheelChange, this)
		if (oParentNode.addEventListener) {
			oParentNode.addEventListener('DOMMouseScroll', oParentNode.onmousewheel, false)
		}
	},

	destruct: function()
	{
		this._oLoadingImage.onload = null
		this._oLoadingImage.onerror = null
	
		this._oScrollBar.onscroll = null
	
		if (this.oParentNode.removeEventListener) {
			this.oParentNode.removeEventListener('DOMMouseScroll', this.oParentNode.onmousewheel, false)
		}
		this.oParentNode.onmousewheel = null
		
		this._disposeFields('_oLoadingImage', '_oScrollBar', 'onDirChange')
		this._disposeContents('aItems')
	}
})

Object.extend(
	px.Proto,
	{		
		clear: function()
		{
			for (var i=0; i<this.aItems.length; i++)
			{
				var oItem = this.aItems[i]

				while (oItem.oDiv.firstChild) {
					oItem.oDiv.removeChild(oItem.oDiv.firstChild)
				}
				this.oParentNode.removeChild(oItem.oDiv)

				oItem.dispose()
			}
			this.aItems.length = 0
		},

		build: function()
		{
			var cFunction = px.lang.Function
			var cElement = px.html.Element
			var iDirectories = 0
			var oDoc = document
		
			if (this.oParentNode.childNodes.length > 0) {
				this.clear(true)
			}
			
			if (this.bFloat) {
				cElement.addClassName(this.oParentNode, 'pxGalleryviewFloat')
			} else {
				cElement.removeClassName(this.oParentNode, 'pxGalleryviewFloat')
			}
			
			this._sIconMargin = (this.iThumbnailSize - this.iIconSize + this.iPadding) / 2 + 'px'
			
			this._sPreviewParameter = ''
			for (var sId in this.oPreviewParameter) {
				this._sPreviewParameter += '&' + sId + '=' + encodeURIComponent(this.oPreviewParameter[sId])
			}

			//this._iItemHeight = this.iThumbnailSize + this.iPadding + 20
			delete this._iItemHeight
			this._iItemWidth = this.iThumbnailSize + this.iPadding + 20

			this._iX = Math.round((this.oParentNode.offsetWidth) / (this._iItemWidth + 10) - 0.45555555)
			if (this._iX < 1 || !this.bFloat) {
				this._iX = 1
			}

			var iContainerHeight = this.oParentNode.offsetHeight

			this.iVisibleItems = 0
			var iCount = 0

			while (iContainerHeight > 0)
			{
				var oItem = new px.ui.gallery.Item()
				oItem.oParent = this
				this.aItems.push(oItem)
				oItem.oDiv = oDoc.createElement('div')
				oItem.oDiv.style.visibility = 'hidden'
				this.oParentNode.appendChild(oItem.oDiv)
				var oDiv = oItem.oDiv
				oDiv.className = 'pxGalleryview'

				oItem.oImage = new Image()
				oItem.oImage.src = pxConst.sGraphicUrl + '/dummy.png'
				oDiv.appendChild(oItem.oImage)

				oItem.oNameNode = oDoc.createElement('a')
				var oA = oItem.oNameNode
				oA.className = 'pxGalleryview'
				oA.href = pxConst.DUMMY_LINK
				oA.appendChild(oDoc.createTextNode('X'))
				oDiv.appendChild(oA)
				oA.onclick = cFunction.bindEvent(oItem.fileClick, oItem)

				oDiv.onclick = cFunction.bindEvent(oItem.itemClick, oItem)
				oDiv.ondblclick = cFunction.bindEvent(oItem.itemDblClick, oItem)
				oDiv.onmousedown = cFunction.bindEvent(oItem.mouseDown, oItem)
				oDiv.onmouseover = cFunction.bindEvent(oItem.itemMouseOver, oItem)
				oDiv.onmouseout = cFunction.bindEvent(oItem.itemMouseOut, oItem)
				oDiv.onmouseup = cFunction.bindEvent(oItem.itemMouseUp, oItem)

				if (!this._iItemHeight) {
					oItem.oImage.height = this.iThumbnailSize
					if (this.bFloat) {
						oItem.oImage.width = this.iThumbnailSize
					}
					this._iItemHeight = oDiv.offsetHeight + this.iPadding
					this._iY = Math.round(iContainerHeight / (this._iItemHeight + 10) + 0.4555555) || 1
					this.iVisibleItems = this._iX * this._iY
				}

				if (this.bFloat) {
					oDiv.style.width = this._iItemWidth + 'px'
				}
				oDiv.style.height = this._iItemHeight + 'px'

				iCount++

				if (this.iVisibleItems == iCount) {
					break
				}
			}

			oItem = null
			oA = null
			oDiv = null
		},

		_update: function(sPath) {
			if (this.oParameters._bReload) {
				this._fill(null, true)
			} else {
				this._fill(0, true)
			}
		},

		_fill: function(iOffset, bUpdate, bNoImages)
		{		
			var sPath = this.oParameters.getPath()

			iOffset = this._reloadCheck(sPath, iOffset, bUpdate)

			if (iOffset == -1) {
				return false
			}

			this._iOffset = iOffset

			var aObjects = this.oResults[sPath]

			for (var r=0; r<this.iVisibleItems; r++)
			{
				var oObject = aObjects[r + iOffset]
				var oItem = this.aItems[r]
				oDiv = oItem.oDiv

				if (!oObject) {
					oDiv.style.visibility = 'hidden'
					oItem.iIndex = -1
					oItem.oImage.src = pxConst.sGraphicUrl + '/dummy.png'
				} else {
					oDiv.style.visibility = 'visible'
					oItem.iIndex = r + iOffset
			   		var sPath = oObject.sRelDir
			   		if (sPath != '/') sPath += '/'
			   		oItem.sFilename = sPath + oObject.sName
					oItem.oNameNode.firstChild.nodeValue = oObject.sName
			   		oItem.bDirectory = oObject.bDirectory
					
					oItem.oDiv.title = oObject.sTitle || oObject.sName

					if (this._iImageLib != 2 && px.util.previewCheck(oObject.sExtension, this.aAdditionalExtensions)) {
						oItem.bThumbnail = true
					} else {
						oItem.bThumbnail = false
					}
					
					if (!this.bDirectUpdate || !px.util.previewCheck(oObject.sExtension, this.aAdditionalExtensions))
					{
						oItem.oImage.style.height = this.iIconSize + 'px'
						if (this.bFloat) {
							oItem.oImage.style.width = this.iIconSize + 'px'
						}
						oItem.oImage.style.marginTop = this._sIconMargin
						oItem.oImage.style.marginBottom = this._sIconMargin
	
						var sExtendedType = pxp.getExtendedType(oObject.sType, oObject.sExtension)
						var sModule = pxp.oTypes[oObject.sType]['sModule']
						
						//if (this.bDirectUpdate && px.lang.Array.contains(this.aAdditionalExtensions, oObject.sExtension)) {
						//	oItem.oImage.src = pxConst.sModuleUrl + '/System.pxm/graphics/dummy.png'
						//} else {
							oItem.oImage.src = pxConst.sModuleUrl + '/' + sModule + '.pxm/graphics/types/' + sExtendedType + '.png'
						//}
					}
					
					if (this.bDirectUpdate) {
						//delete oItem.oImage.style.height
						//delete oItem.oImage.style.width						
						oItem.oImage.src =
							'./preview.php?sShare=' + this.oParent.oShare.sId + '&sAction=_openPreview&sPath=' +
							encodeURIComponent(oItem.sFilename) + '&iWidth=' + this.iThumbnailSize + '&iHeight=' + this.iThumbnailSize + this._sPreviewParameter						
					}
				}
			}

			var iHeight = 0
			if (aObjects.length) {
				var iHeight = (aObjects.length / this._iX) * (this._iItemHeight + 10)
			}
			if (iHeight > this._oScrollBar.offsetHeight) {
				this._oScrollBar.firstChild.style.height = iHeight + 'px'
				this._oScrollBar.style.visibility = 'visible'
			} else {
				this._oScrollBar.style.visibility = 'hidden'
			}
		
			this.setSelection()

			if (!this._bReloading) {
				delete this._bPopulating
				delete this._bReloading
		
				if (this._iImageLib != 2) {
					if (bNoImages == null) {
						if (!this._bLoading) {	
							this._iCurrentItemIndex = 0
							if (!this.bDirectUpdate)
								this.loadNextImage()
						} else {
							this._bReset = true
						}
					}
				}
			}
		},

		loadNextImage: function()
		{
			var oItem = this.aItems[this._iCurrentItemIndex]

			while (oItem && !oItem.bThumbnail) {
				this._iCurrentItemIndex++
				var oItem = this.aItems[this._iCurrentItemIndex]
			}

			if (oItem && oItem.iIndex > -1) {
				
				if (this._bLoading) {
					return
				}

				this._bLoading = true

				if (oItem.bThumbnail) {
					this._oLoadingImage.src =
						'./preview.php?sShare=' + this.oParent.oShare.sId + '&sAction=_openPreview&sPath=' +
						encodeURIComponent(oItem.sFilename) + '&iWidth=' + this.iThumbnailSize + '&iHeight=' + this.iThumbnailSize + this._sPreviewParameter
				}
			}
		},

		imageLoadCallback: function()
		{
			if (this._bReset) {
				delete this._bLoading
				delete this._bReset
				this._iCurrentItemIndex = 0
				this.loadNextImage()
				return false
			}

			var oItem = this.aItems[this._iCurrentItemIndex]
		
			oItem.oImage.src = this._oLoadingImage.src
			oItem.oImage.style.width = this._oLoadingImage.width + 'px'
			oItem.oImage.style.height = this._oLoadingImage.height + 'px'
			
			var iSpace = this.iThumbnailSize - this._oLoadingImage.height + 10
			var iTop = Math.round(iSpace / 2)
			var iBottom = iSpace - iTop
			
			oItem.oImage.style.marginTop = iTop + 'px'
			oItem.oImage.style.marginBottom = iBottom + 'px'
			this._iCurrentItemIndex++
		
			delete this._bLoading

			this.loadNextImage()
		},
		
		imageLoadError: function()
		{
			delete this._bLoading
			this._iCurrentItemIndex++
			this.loadNextImage()
		},
		
		dragCleanup: function() {
			px.io.RemoteView.dragCleanup.call(this)
		},
		
		clearSelection: function() {
			delete this.oSelected
			this.oSelected = {}
			this.setSelection()
		},
		
		setSelection: function() {
			for (var r=0; r<this.iVisibleItems; r++) {
				var oItem = this.aItems[r]
				if (this.oSelected[oItem.sFilename]) {
					px.html.Element.addClassName(oItem.oNameNode, 'selected')
				} else {
					px.html.Element.removeClassName(oItem.oNameNode, 'selected')
				}
			}
		},

		sort: function(sColumn) {
			this._clientSort(sColumn)
		},
		
		scrollBarChange: function(oEvent) {
			var iOffset = Math.round(this._oScrollBar.scrollTop / (this._iItemHeight + 10) + 0.4555555) * this._iX
			if (iOffset != this._iOffset) {
				if (this._oLoadTimeout) {
					window.clearTimeout(this._oLoadTimeout)
				}
				this._oLoadTimeout = window.setTimeout(px.lang.Function.bind(this._fill, this, iOffset), 100)
			}
		},
		
		mouseWheelChange: function(oEvent)
		{
			var iDelta = oEvent.detail || (oEvent.wheelDelta / 40 * -1)
			if (iDelta > 0) {
				var iNewOffset = this._iOffset + this._iX
			} else {
				var iNewOffset = this._iOffset - this._iX
			}
			if (iNewOffset >= 0) {
				this._iOffset = iNewOffset
				this._oScrollBar.scrollTop = iNewOffset * (this._iItemHeight + 10) / this._iX
				this._fill(iNewOffset)
			}
		},
		
		resize: function(bFinished) {
			if (bFinished) {
				this.build()
				this._fill()
			}
		}
	}
)