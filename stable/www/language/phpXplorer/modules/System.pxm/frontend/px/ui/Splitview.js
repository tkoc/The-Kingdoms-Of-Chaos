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
px.Class.define('px.ui.Splitview',
{
	extend: px.core.Object,

	construct: function(bVertical, sSize, bResize)
	{
		this.oParentNode

		this.sId

		this.oChild1
		this.oChild2

		this.iWidth = 100
		this.iHeight = 100

		this.bResize = bResize != false
		this._bFirstHidden = false
		this.bVertical = bVertical

		this.bSnap = true
		this.sSnapIcon = ''
		this.iSnapSize = 240

		this._sSize = sSize
		this._iSizeFactor = 0.5

		this.iSize
		this.iOldSize

		this._oFrame1
		this._oFrame2
		this._oDrag

		this.oToolbar
		this.oActionview
		this.aPersistentButtons = []

		this._bDragged = false
		this._iSeparatorSize
	},

	destruct: function() {
		if (this.bResize) {
			this._oDrag.onmousedown = null
			this._oDrag.onmouseout = null
		}
		this.oParentNode.onclick = null
  	this._disposeFields('oParentNode', '_oFrame1', '_oFrame2', '_oDrag', 'oActionview', 'aPersistentButtons')
  	this._disposeObjects('oChild1', 'oChild2', 'oToolbar')
	}
})



Object.extend(
	px.Statics,
	{
		resizeH: function(oEvent)
		{
			var oEvent = oEvent || window.event
			oEvent.cancelBubble = true

			var oSel = pxp.oSelectedControl
			oSel.iSize = oEvent.clientY - iDragOffset
			if (oSel.iSize + oSel._iSeparatorSize >= oSel.iHeight) {
				oSel.iSize = oSel.iHeight - oSel._iSeparatorSize
			}
			if (oSel.iSize <= 0) oSel.iSize = 1
			oSel.resizeH()
			if (oSel.oChild1) oSel.oChild1.resize()
			if (oSel.oChild2) oSel.oChild2.resize()
		},

		resizeV: function(oEvent)
		{
			var oEvent = oEvent || window.event			
			oEvent.cancelBubble = true

			var oSel = pxp.oSelectedControl
			oSel.iSize = oEvent.clientX - iDragOffset
			if (oSel.iSize + oSel._iSeparatorSize >= oSel.iWidth) {
				oSel.iSize = oSel.iWidth - oSel._iSeparatorSize
			}
			if (oSel.iSize <= 0) oSel.iSize = 1
			oSel.resizeV()
			if (oSel.oChild1) oSel.oChild1.resize()
			if (oSel.oChild2) oSel.oChild2.resize()
		}
	}
)



Object.extend(
	px.Proto,
	{
		init: function(oParentNode)
		{
			var cFunction = px.lang.Function
			
			this.oParentNode = oParentNode
			//this.oParentNode.style.overflow = 'hidden'

			this._oFrame1 = document.createElement('div')
			if (this.sId) {
				this._oFrame1.id = this.sId + 'Frame1'
			}

			this.oParentNode.appendChild(this._oFrame1)

			var sSize = this._sSize

			if (sSize) {
				if (!isNaN(sSize)) {
					this._iSizeFactor = sSize
				} else {
					if (sSize.indexOf('px')) {
						this._iSizeFactor = parseInt(sSize)
						this._iSizeFactor = this._iSizeFactor / this.oParentNode.offsetHeight
					} else {
						this._iSizeFactor = parseInt(sSize)
					}
				}
				if (this._iSizeFactor > 1) {
					if (this._iSizeFactor > 100) {
						this._iSizeFactor = this._iSizeFactor / 1000
					} else {
						this._iSizeFactor = this._iSizeFactor / 100
					}
				}
			}

			if (this.bResize) {
				this._oDrag = document.createElement('div')
				this.oParentNode.appendChild(this._oDrag)
				this._oDrag.onmousedown = cFunction.bindEvent(this.separatorMouseDown, this)
				this._oDrag.onmouseout = cFunction.bindEvent(this.separatorMouseOut, this)
				if (this.sId) {
					this._oDrag.id = this.sId + 'Separator'
				}
			}

			this._oFrame2 = document.createElement('div')
			if (this.sId) {
				this._oFrame2.id = this.sId + 'Frame2'
			}

			this.oParentNode.appendChild(this._oFrame2)

			if (this.bSnap)
			{
				this.oToolbar = new px.ui.toolbar.Toolbar(this, this.oParentNode)
				px.html.Element.addClassName(this.oToolbar.oDiv, 'pxSplitToolbar')
	
				this.oToolbar.addButton(
				{
					sId: 'expand',
					sIcon: 'expand' + this.sSnapIcon + '.png',
					oOnClick: cFunction.bind(this.showFirst, this, null),
					bHidden: true
		  	})

		  	var oActionview = this.oActionview
		  	if (oActionview) {
					for (var sAction in oActionview.oActionData) {
						var oData = oActionview.oActionData[sAction]
						this.oToolbar.addButton(
						{
							sId: sAction,
							sIcon: oData.sIcon,
							sTitle: oData.sTitle,
							oOnClick: cFunction.bind(this.showFirst, this, null, true, sAction),
							bHidden: !px.lang.Array.contains(this.aPersistentButtons, sAction)
						})
					}
		  	}

				this.oToolbar.addButton(
				{
					sId: 'collapse',
					sIcon: 'collapse' + this.sSnapIcon + '.png',
					oOnClick: cFunction.bindEvent(this.hideFirst, this)
		  	})
			}

			this.resizeBorderFrame()

			if (this.bVertical) {
				this.initV()
			} else {
				this.initH()
			}

			if (this.oChild1) {
				this.oChild1.init(this._oFrame1)
				if (this.oChild1.update) {
					this.oChild1.update()
				}
			}

			if (this.oChild2) {
				this.oChild2.init(this._oFrame2)
				if (this.oChild2.update) {
					this.oChild2.update()
				}
			}			
		},




		changeOrientation: function(bVertical)
		{
			var bNewValue = bVertical != null ? bVertical : !this.bVertical
			
			if (this.bVertical == bNewValue) {
				return false
			} else {
				this.bVertical = bNewValue
			}

			if (this.bVertical) {
				this.initV()
			} else {
				this.initH()
			}
		
			this.resize(true)
			
			if (this.oChild1 && this.oChild1.update) {
				this.oChild1.update()
			}
			if (this.oChild2 && this.oChild2.update) {
				this.oChild2.update()
			}
			if (this._bFirstHidden) {
				this.showFirst(null, false)
			}
		},




		initH: function() {
			if (this.bResize) {
				this._oDrag.className = 'pxSplitSeparatorH'
				if (!this._iSeparatorSize) {
					this._iSeparatorSize = this._oDrag.offsetHeight
				}
				this._oDrag.style.height = this._iSeparatorSize + 'px'
			} else {
				this._iSeparatorSize = 0
			}
			this._oFrame1.className = this._oFrame2.className = 'pxSplitH'
			this.iOldSize = this.iSize = Math.round(this.iHeight * this._iSizeFactor)
			this.resizeH()
		},




		initV: function() {
			if (this.bResize) {
				this._oDrag.className = 'pxSplitSeparatorV'
				if (!this._iSeparatorSize) {
					this._iSeparatorSize = this._oDrag.offsetWidth
				}
				this._oDrag.style.width = this._iSeparatorSize + 'px'
			} else {
				this._iSeparatorSize = 0
			}
			this._oFrame1.className = this._oFrame2.className = 'pxSplitV'
			this.iOldSize = this.iSize = Math.round(this.iWidth * this._iSizeFactor)
			this.resizeV()
		},




		dragCleanup: function(oEvent)
		{
			var cElement = px.html.Element

			if (!this._bDragged) {
				this._bDragged = false;
				return false
			}
			
			if (this.bResize)
			{
				var oSel = pxp.oSelectedControl

				if (cElement.hasClassName(oSel._oDrag, 'drag'))
				{
					cElement.removeClassName(oSel._oDrag, 'drag')
					if (this.iSize != this.iOldSize) {
						this.iOldSize = this.iSize
						if (this.oChild1) this.oChild1.resize(true)
						if (this.oChild2) this.oChild2.resize(true)
						if (this._bFirstHidden) {
							this.showFirst(null, false)
						} else {
							if (this.iSize < 60) {
								this.hideFirst(oEvent)
							}
						}
						return false
					}
					else
					{
						//this.resize(true)
						
						var oCm = px.ui.ContextMenu

						oCm.clear()

						oCm.addItem(
							'changeOrientation',
							oTranslation['changeOrientation'],
							pxConst.sGraphicUrl + '/rotate.png'
						)

						oCm.show(oEvent, 1)						
					}
				}
			}
		},




		showFirst: function(oEvent, bResize, sAction)
		{
			var cElement = px.html.Element
			
			if (oEvent) {
				var oElement = px.Event.element(oEvent)
				if (oElement != this.oParentNode && !sAction) {
					return true
				}
//				oEvent.cancelBubble = true
			}

			var oToolbar = this.oToolbar
			var oDiv = oToolbar.oDiv
			var sClass = 'pxSplitToolbarVertical';
			var bHidden = cElement.hasClassName(oDiv, sClass)

			cElement.removeClassName(oDiv, sClass)

			if (bResize !== false && bHidden) {
				var iSnap = this.iSnapSize
				var iSize = this._iPreviousSize || iSnap
				if (iSize < iSnap) {
					iSize = iSnap
				}
				this.iOldSize = this.iSize = iSize
			}
			
			if (bHidden)
			{
				for (var sId in oToolbar.oButtons) {
					if (!px.lang.Array.contains(this.aPersistentButtons, sId)) {
						oToolbar[sId == 'collapse' ? 'showButton' : 'hideButton'](sId)
					}
				}

				this.resize(true)
				this._oFrame1.style.visibility = ''
				this.oParentNode.onclick = null
				this._bFirstHidden = false
			}

			if (this.oActionview && sAction) {
				this.oActionview.showAction(sAction)
			}

			return false
		},


		
		hideFirst: function(oEvent)
		{
			var oToolbar = this.oToolbar

			this._oFrame1.style.visibility = 'hidden'			

			px.html.Element.addClassName(oToolbar.oDiv, 'pxSplitToolbarVertical')

			this._iPreviousSize = this.iSize
			this.iOldSize = this.iSize = 22
			this.resize(true)

			this.oParentNode.onclick = px.lang.Function.bindEvent(this.showFirst, this)

			for (var sId in oToolbar.oButtons) {
				if (!px.lang.Array.contains(this.aPersistentButtons, sId)) {
					oToolbar[sId == 'collapse' ? 'hideButton' : 'showButton'](sId)
				}
			}

			this._bFirstHidden = true
			if (oEvent) {
				oEvent.cancelBubble = true
			}

			return false
		},




		callAction: function(sAction) {
			switch (sAction) {
				case 'changeOrientation':
					this.changeOrientation()
					break
			}
		},




		resizeBorderFrame: function() {
			if (this.oParentNode.offsetWidth > 0) {
				this.iWidth = this.oParentNode.offsetWidth
			}
		
			if (this.oParentNode.offsetHeight > 0) {
				this.iHeight = this.oParentNode.offsetHeight - this._oFrame1.offsetTop
			}
		},




		resize: function(bFinished)
		{
			this.resizeBorderFrame()
			if (this.bVertical) {
				if (this.resizeV()) bFinished = true
			} else {
				if (this.resizeH()) bFinished = true
			}
			if (this.oChild1) this.oChild1.resize(bFinished)
			if (this.oChild2) this.oChild2.resize(bFinished)
		},




		save: function() {
			if (this.oChild1 && this.oChild1.save) this.oChild1.save()
			if (this.oChild2 && this.oChild2.save) this.oChild2.save()
		},




		resizeH: function()
		{
			this._bDragged = true
			var bResized = false
			if (this.bResize) {
				if (this.iSize + this._iSeparatorSize + 10 > this.iHeight) {
					this.iSize = this.iHeight - 10
					if (this.iSize <= 0) this.iSize = 1
					bResized = true
				}
			}

			var iHeight = this.iHeight - this.iSize - this._iSeparatorSize
			if (iHeight <= 0) iHeight = 1
		
			if (this.bResize) {
				this._oDrag.style.width = this.iWidth + 'px'
			}
			this._oFrame1.style.height = this.iSize + 'px'
			this._oFrame1.style.width = this.iWidth + 'px'
			this._oFrame2.style.height = iHeight + 'px'
			this._oFrame2.style.width = this.iWidth + 'px'
			
			if (this.bSnap) {
				this.oToolbar.oDiv.style.top = '2px'
				this.oToolbar.oDiv.style.left = this._oFrame1.offsetWidth - this.oToolbar.oDiv.offsetWidth - 4 + 'px'
			}

			return bResized
		},




		resizeV: function()
		{
			this._bDragged = true
			var bResized = false
			if (this.bResize) {
				if (this.iSize + this._iSeparatorSize + 10 > this.iWidth) {
					this.iSize = this.iWidth - 10
					if (this.iSize <= 0) this.iSize = 1
					bResized = true
				}
			}
		
			var iWidth = this.iWidth - this.iSize - this._iSeparatorSize
			if (iWidth <= 0) iWidth = 1
		
			if (this.bResize) {
				this._oDrag.style.height = this.iHeight + 'px'
			}
			this._oFrame1.style.height = this.iHeight + 'px'
			this._oFrame1.style.width = this.iSize + 'px'
			this._oFrame2.style.height = this.iHeight + 'px'
			this._oFrame2.style.width = iWidth + 'px'
			
			if (this.bSnap) {
				this.oToolbar.oDiv.style.top = '2px'
				this.oToolbar.oDiv.style.left = this.iSize - this.oToolbar.oDiv.offsetWidth + 'px'
			}
		
			return bResized
		},




		separatorMouseDown: function(oEvent) {
			var cElement = px.html.Element
			pxp.setActiveControl(this)
			this._oDrag.className = 'pxSplitSeparator' + (this.bVertical ? 'V' : 'H') + ' drag'
			pxp.startDrag('resize')
			if (this.bVertical) {
				iDragOffset = oEvent.clientX - cElement.getLeftOffset(this._oDrag) + cElement.getLeftOffset(pxp.oSelectedControl.oParentNode)
				document.onmousemove = px.ui.Splitview.resizeV
			} else {
				iDragOffset = oEvent.clientY - cElement.getTopOffset(this._oDrag) + cElement.getTopOffset(pxp.oSelectedControl.oParentNode)
				document.onmousemove = px.ui.Splitview.resizeH
			}
		},




		separatorMouseOut: function(oEvent) {
			if (this._oDrag.className.indexOf('drag') > -1) {
				this.resize()
			}
		}
	}
)