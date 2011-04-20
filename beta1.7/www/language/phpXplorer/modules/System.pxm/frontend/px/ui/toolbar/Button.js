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
px.Class.define('px.ui.toolbar.Button',
{
	extend: px.core.Object,

	construct: function(oParent, oOptions)
	{
		var cFunction = px.lang.Function
		var cElement = px.html.Element

		this.oParent = oParent
		this.oParentNode = oParent.oDiv

		this.sId = oOptions.sId
		this.sLabel = pxp.translateTitle(oOptions.sLabel)
		this.sIcon = oOptions.sIcon
		this.sTitle = oOptions.sTitle
		this.bRight = oOptions.bRight
		this.bDropDown = oOptions.bDropDown || false
		this.oOnClick = oOptions.oOnClick
		this.oDropHandler = oOptions.oDropHandler
		this.iType = oOptions.iType || pxConst.TOOLBAR_BUTTON_LINK
		this.sClass = oOptions.sClass

		if (oOptions.bLabel) {
			this.oA = document.createElement('span')
		} else {
			this.oA = document.createElement('a')
			this.oA.href = pxConst.DUMMY_LINK
		}

		this.oA.className = this.sClass || 'pxTb'

		if (oOptions.bDisabled) {
			px.html.Element.addClassName(this.oA, 'pxTbDisabled')
		}

		this.oParentNode.appendChild(this.oA)

		if (this.bRight) {
			var iOffset = 0
			for (var sButton in this.oParent.oButtons) {
				if (this.oParent.oButtons[sButton].bRight) {
					iOffset += 2 + this.oParent.oButtons[sButton].oA.offsetWidth
				}
			}
			this.oA.style.position = 'absolute'
			this.oA.style.right = iOffset + 'px'
		}

		if (this.sIcon) {
			var sSrc = this.sIcon.indexOf('.pxm') > -1 ? this.sIcon : pxConst.sGraphicUrl + '/' + this.sIcon
			this.oImage = cElement.appendImage(this.oA, sSrc, null, this.oParent.iIconSize, 'icon')
			if (oOptions.oOnIconClick && oOptions.oOnIconClick instanceof Function) {
				this.oImage.onclick = oOptions.oOnIconClick
			}
		}

		switch (this.iType) {
			case pxConst.TOOLBAR_BUTTON_LINK:
				if (this.sLabel) {
					this.oA.appendChild(document.createTextNode(' ' + this.sLabel))
				}
			break
			case pxConst.TOOLBAR_BUTTON_INPUT:
				this.oInput = document.createElement('input')
				this.oInput.type = 'text'
				if (this.sLabel) {
					this.oInput.value = this.sLabel
				}
				this.oA.appendChild(this.oInput)
				this.oInput.onclick = cFunction.cancelEvent
				if (oOptions.oInputOnKeyUp && oOptions.oInputOnKeyUp instanceof Function) {
					this.oInput.onkeyup = oOptions.oInputOnKeyUp
				}
				if (oOptions.oInputOnFocus && oOptions.oInputOnFocus instanceof Function) {
					this.oInput.onfocus = oOptions.oInputOnFocus
				}
				if (oOptions.oInputOnBlur && oOptions.oInputOnBlur instanceof Function) {
					this.oInput.onblur = oOptions.oInputOnBlur
				}
			break
		}

		var sDropDownIcon = this.oParent.oParent.sDropDownIcon || 'dropDown.png'
	
		if (this.bDropDown) {
				cElement.appendImage(this.oA, pxConst.sGraphicUrl + '/' + sDropDownIcon)
		}
		if (this.sTitle) {
			this.oA.title = this.sTitle
		}

		if (this.oOnClick && this.oOnClick instanceof Function && !oOptions.bDisabled) {
			this.oA.onclick = this.oOnClick
		}

		if (this.oDropHandler) {
			this.oA.onmouseover = cFunction.bindEvent(px.ui.toolbar.Button.mouseOver, this)
			this.oA.onmouseout = cFunction.bindEvent(px.ui.toolbar.Button.mouseOut, this)
			this.oA.onmouseup = cFunction.bindEvent(px.ui.toolbar.Button.mouseUp, this)
		}
	},
	
	destruct: function()
	{
		switch (this.iType) {
			case pxConst.TOOLBAR_BUTTON_LINK:
			break
			case pxConst.TOOLBAR_BUTTON_INPUT:
				if (this.oInput.onkeyup) this.oInput.onkeyup = null
				if (this.oInput.onfocus) this.oInput.onfocus = null
				if (this.oInput.onblur) this.oInput.onblur = null
				if (this.oInput.onclick) this.oInput.onclick = null
			break
		}

		if (this.sIcon) {
			if (this.oImage.onclick) {
				this.oImage.onclick = null
			}
			this.oImage = null
		}

		if (this.oA.onclick) this.oA.onclick = null
		if (this.oDropHandler) {
			this.oA.onmouseover = null
			this.oA.onmouseout = null
			this.oA.onmouseup = null
		}

		this._disposeFields('oParent', 'oParentNode', 'oA', 'oOnClick', 'oDropHandler', 'oImage', 'oInput')
	}
})

Object.extend(
	px.Statics,
	{
		mouseOver: function(oEvent) {
			if (pxp.bDragging && px.lang.Array.contains(this.oDropHandler.aDropTypes, pxp.sDragType)) {
				px.html.Element.addClassName(this.oA, 'drop')
			}
		},
		
		mouseOut: function(oEvent) {
			px.html.Element.removeClassName(this.oA, 'drop')
		},
		
		mouseUp: function(oEvent) {
			if (px.html.Element.hasClassName(this.oA, 'drop')) {
				this.oDropHandler.run(pxp.oSelectedControl)
			}
		}
	}
)

Object.extend(
	px.Proto,
	{
		showDropDown: function() {
			this.oA.lastChild.style.display = 'inline'
		},
		
		hideDropDown: function() {
			this.oA.lastChild.style.display = 'none'
		},
		
		changeImage: function(sSrc) {
			px.html.Element.changeImage(this.oA.firstChild, sSrc)
		}
	}
)