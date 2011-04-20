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

px.Class.define('px.ui.widget.Widget',
{
	extend: px.core.Object,

	construct: function(oParent, oParentNode, sType, sProperty, mValue)
	{
		this._oParent = oParent
		this._oParentNode = oParentNode
		this._sType = sType
		this._sProperty = sProperty
		this._oProperty = pxp.oTypes[sType].aProperties[sProperty]
		this._mDefaultValue = mValue
		this._bEdit = this._oProperty.sMode == 'edit' && !oParent.bReadonly

		this._onchange = px.lang.Function.bind(this._setChanged, this)
	},

	destruct: function() {
		if (this._oWidget) {
			if (this._oWidget.onchange) {
				this._oWidget.onchange = null
			}
			if (this._oWidget.onkeyup) {
				this._oWidget.onkeyup = null
			}
			this._oWidget = null
		}
		this._disposeFields('_oParent', '_oParentNode', '_oProperty', '_mDefaultValue', '_onchange')
	}
})

Object.extend(
	px.Proto,
	{
		_setChanged: function() {
			this._oParent.oParent.setChanged(true)
		}
	}
)

//------------------------------------------------------------------------------

px.Class.define('px.ui.widget.Select',
{
	extend: px.ui.widget.Widget,

	construct: function(oParent, oParentNode, sType, sProperty, mValue)
	{
		this.base(arguments, oParent, oParentNode, sType, sProperty, mValue)
		
		if (this._bEdit)
		{
			var oOptions = this._oProperty.oOptions

			this._oWidget = document.createElement('select')
			var oSelect = this._oWidget
			oSelect.name = 'px_' + sProperty
			oSelect.size = 1
			oParentNode.appendChild(oSelect)
			oSelect.onchange = this._onchange

			if (oOptions instanceof Array) {
				for (var o=0, m=oOptions.length; o<m; o++) {
					var oOption = new Option(oOptions[o], o)
					oSelect.options[oSelect.options.length] = oOption
					oOption.selected = mValue == o
				}
			} else {
				for (var o in oOptions) {
					var oOption = new Option(oOptions[o], o)
					oSelect.options[oSelect.options.length] = oOption
					oOption.selected = mValue == o
				}
			}
		}
		else
		{
			oParentNode.appendChild(document.createTextNode(mValue))
		}
	}
})

//------------------------------------------------------------------------------

px.Class.define('px.ui.widget.SelectTranslated',
{
	extend: px.ui.widget.Select,

	construct: function(oParent, oParentNode, sType, sProperty, mValue)
	{
		this.base(arguments, oParent, oParentNode, sType, sProperty, mValue)

		var aOptions = oParentNode.firstChild.options
		for (var i=0, m=aOptions.length; i<m; i++) {
			var sId = aOptions[i].text
			var sNamespace = this._oProperty.aParameters['namespace'] || String('option')
			aOptions[i].text = oTranslation[sNamespace + '.' + sId] || sId
		}
	}
})

//------------------------------------------------------------------------------

px.Class.define('px.ui.widget.SelectManualOptions',
{
	extend: px.ui.widget.Select,

	construct: function(oParent, oParentNode, sType, sProperty, mValue)
	{
		if(oParent.oObject.bNew)
		{			
			this.base(arguments, oParent, oParentNode, sType, sProperty, mValue)

			this._oButton = document.createElement('button')
			var oButton = this._oButton
			oParentNode.appendChild(oButton)
			oButton.appendChild(document.createTextNode('...'))
			oButton.onclick = px.lang.Function.bind(this._loadOptions, this)
		}
		else
		{
			var sText = mValue == null ? '' : String(mValue)
			oParentNode.appendChild(document.createTextNode(sText))
			var oInput = document.createElement('input')
			oInput.name = 'px_' + sProperty
			oInput.type = 'hidden'
			oInput.value = sText
			oParentNode.appendChild(oInput)
		}
	},

	desruct: function() {
		if (this._oButton) {
			this._oButton.onclick = null
			this._disposeFields('_oButton')
		}
	}
})

Object.extend(
	px.Proto,
	{
		_loadOptions: function()
		{
			var oObject = this._oParent.oObject
			var oButton = this._oButton

			oButton.disabled = true
			oButton.style.display = 'none'
			var oSelect = oButton.previousSibling
			var sRelDir = oObject.sRelDir
			var sName = oObject.sName
			var sPath = px.util.buildPath(sRelDir, sName)			
			var sProperty = 'sRelDir'

			var oOptions = px.io.Request.get(
				'sAction=_openOptions&' +
				'sShare=' + encodeURIComponent(oObject.sShare) +
				'&sPath=' + encodeURIComponent(sPath) +
				'&sProperty=' + sProperty +
				'&sType=' + oObject.sType
			)

			for (var sKey in oOptions) {
				var sValue = oOptions[sKey]		
				var oOption = new Option(sValue, sKey)
				oSelect.options[oSelect.options.length] = oOption
			}

			return false
		}
	}	
)

//------------------------------------------------------------------------------

px.Class.define('px.ui.widget.Multiple',
{
	extend: px.ui.widget.Widget,

	construct: function(oParent, oParentNode, sType, sProperty, mValue)
	{
		this.base(arguments, oParent, oParentNode, sType, sProperty, mValue)

		var cArray = px.lang.Array
		var oProperty = this._oProperty
		var oOptions = oProperty.oOptions

		this._oTagList = document.createElement('ul')
		var oTagList = this._oTagList
		oTagList.className = 'checklist checklistFloat'

		if (oProperty.aParameters['size']) {
			var iSize = oProperty.aParameters['size']
		} else {
			var iSize = px.util.getLength(oOptions)
			if (iSize > 7) iSize = 7
			if (iSize == 0) iSize = 1
		}
		oTagList.style.height = iSize * 2.05 + 0.4 + 'em'

		oParentNode.appendChild(oTagList)

		//var iMax = 0

		for (var o in oOptions) {
			this.addItem(
				o,
				oOptions[o],
				cArray.contains(mValue, o)
			)
			//if (oTagList.lastChild.offsetWidth > iMax) {
				//iMax = oTagList.lastChild.offsetWidth
			//}
		}

		//iMax += 8

		//for (var i=0, m=oTagList.childNodes.length; i<m; i++) {
			//oTagList.childNodes[i].style.width = iMax + 'px'
		//}
	},

	destruct: function() {
		for (var i=0, m=this._oParentNode.firstChild.childNodes.length; i<m; i++) {
			var oChild = this._oParentNode.firstChild.childNodes[i]
			if (oChild.firstChild.onmouseover) {
				oChild.firstChild.onmouseover = null
			}
			if (oChild.firstChild.onmouseout) {
				oChild.firstChild.onmouseout = null
			}
			oChild.firstChild.firstChild.onchange = null
		}
		this._disposeFields('_oTagList')
	}
})

Object.extend(
	px.Proto,
	{
		resize: function()
		{
			if (this._bResized) {
				return
			}

			var oTagList = this._oTagList
			var iMax = 0

			for (var i=0, m=oTagList.childNodes.length; i<m; i++) {
				var iWidth = oTagList.childNodes[i].offsetWidth
				if (iWidth > iMax) {
					iMax = iWidth
				}
			}

			if (iMax == 0) {
				return
			}

			iMax += 8

			for (var i=0, m=oTagList.childNodes.length; i<m; i++) {
				if (iMax > 0) {
					oTagList.childNodes[i].style.width = iMax + 'px'
				}
			}

			this._bResized = true
		},

		addItem: function(sValue, sText, bSelected)
		{
			var cFunction = px.lang.Function
			var sId = px.util.getRandomId()
	
			var oLi = document.createElement('li')
			this._oTagList.appendChild(oLi)
	
			var oLabel = document.createElement('label')
			oLabel.htmlFor = sId
			oLi.appendChild(oLabel)
	
			if (pxp.bIe) {
				oLabel.onmouseover = cFunction.bindEvent(this._itemMouseOver, oLabel)
				oLabel.onmouseout = cFunction.bindEvent(this._itemMouseOut, oLabel)
			}

			var oInput = document.createElement('input')
			oInput.type = 'checkbox'
			oInput.id = sId
			oInput.name = 'px_' + this._sProperty + '[]'
			oInput.value = sValue
			oLabel.appendChild(oInput)
			oInput.checked = bSelected
			oLabel.appendChild(document.createTextNode(sText))
			oInput.onchange = this._onchange
			oInput.disabled = !this._bEdit
		},

		_itemMouseOver: function() {
			this.className = 'hover'
		},

		_itemMouseOut: function() {
			this.className = null
		}
	}
)
//------------------------------------------------------------------------------

px.Class.define('px.ui.widget.MultipleTranslated',
{
	extend: px.ui.widget.Multiple,

	construct: function(oParent, oParentNode, sType, sProperty, mValue)
	{
		this.base(arguments, oParent, oParentNode, sType, sProperty, mValue)
		
		var sNamespace = this._oProperty.aParameters['namespace']
		var oList = oParentNode.firstChild
		for (var i=0,l=oList.childNodes.length; i<l; i++) {
			var oLi = oList.childNodes[i]
			var oText = oLi.firstChild.childNodes[1]
			var sLabel = oText.nodeValue
			var sTranslationId = sNamespace ? sNamespace + '.' + sLabel : aOptions[i].text
			oText.nodeValue = oTranslation[sTranslationId] || sLabel
		}
	}
})

//------------------------------------------------------------------------------

px.Class.define('px.ui.widget.Checkbox',
{
	extend: px.ui.widget.Widget,

	construct: function(oParent, oParentNode, sType, sProperty, mValue)
	{
		this.base(arguments, oParent, oParentNode, sType, sProperty, mValue)

		this._oWidget = document.createElement('input')
		var oInput = this._oWidget
		oInput.name = 'px_' + sProperty
		oInput.type = 'checkbox'
		oInput.value = 'true'
		oParentNode.appendChild(oInput)
		oInput.checked = mValue
		oInput.onchange = this._onchange
		oInput.disabled = !this._bEdit
	}
})

//------------------------------------------------------------------------------

px.Class.define('px.ui.widget.Datetime',
{
	extend: px.ui.widget.Widget,

	construct: function(oParent, oParentNode, sType, sProperty, mValue)
	{
		this.base(arguments, oParent, oParentNode, sType, sProperty, mValue)

		var sText = mValue ? px.util.Format.datetime(mValue) : ''

		if (this._bEdit)
		{
			this._oWidget = document.createElement('input')
			var oInput = this._oWidget
			oInput.name = 'px_' + sProperty + '_formated'
			oInput.type = 'text'
			oInput.value = sText
			oInput.size = 20
			oParentNode.appendChild(oInput)
			oInput.onkeyup = this._changed
			oInput.onchange = this._onchange
			oInput = null

			oParentNode.appendChild(document.createTextNode(' '))

			var oInput = document.createElement('input')
			oInput.name = 'px_' + sProperty
			oInput.type = 'text'
			oInput.value = mValue
			oInput.size = 12
			oParentNode.appendChild(oInput)
		}
		else
		{
			oParentNode.appendChild(document.createTextNode(sText))
		}
	}
})

Object.extend(
	px.Proto,
	{
		_changed: function(oEvent) {
			var oInput = px.Event.element(oEvent)
			oInput.nextSibling.nextSibling.value =
				px.util.parseDateTime(oInput.value)
		}
	}
)

//------------------------------------------------------------------------------

px.Class.define('px.ui.widget.Input',
{
	extend: px.ui.widget.Widget,

	construct: function(oParent, oParentNode, sType, sProperty, mValue, bPassword)
	{
		this.base(arguments, oParent, oParentNode, sType, sProperty, mValue)

		var sText = mValue == null ? '' : String(mValue)
		if (this._bEdit) {
			this._oWidget = document.createElement('input')
			var oInput = this._oWidget
			oInput.name = 'px_' + sProperty
			oInput.type = bPassword ? 'password' : 'text'
			oInput.value = sText
			oInput.size = this._calculateSize()
			oParentNode.appendChild(oInput)
			oInput.onkeyup = px.lang.Function.bind(this._resize, this)
			oInput.onchange = this._onchange
			oInput = null
		} else {
			oParentNode.appendChild(document.createTextNode(sText))
		}
		
		var sUnit = this._oProperty.aParameters['unit']
		if (sUnit) {
			oParentNode.appendChild(document.createTextNode(' ' + sUnit))
		}
	}
})

Object.extend(
	px.Proto,
	{
		_resize: function() {
			this._oWidget.size = this._calculateSize()
		},

		_calculateSize: function() {
			var sValue = this._oWidget.value
			if (sValue.length > 0) {
				var iSize = Math.round(sValue.length * 1.2)
				if (iSize > 60) {
					iSize = 60
				}
				if (iSize < 8) {
					iSize = 8
				}
				return iSize
			} else {
				return 8
			}
		}
	}
)

//------------------------------------------------------------------------------

px.Class.define('px.ui.widget.InputTranslated',
{
	extend: px.ui.widget.Input,

	construct: function(oParent, oParentNode, sType, sProperty, mValue)
	{
		this.base(arguments, oParent, oParentNode, sType, sProperty, mValue, false)	
		var sNamespace = this._oProperty.aParameters['namespace']
		var sTranslation = oTranslation[(sNamespace != '' ? sNamespace + '.' : '') + mValue]

		if (this._bEdit) {
			this._oWidget.value = sTranslation
		} else {
			oParentNode.firstChild.nodeValue = sTranslation
		}

	}
})

//------------------------------------------------------------------------------

px.Class.define('px.ui.widget.Password',
{
	extend: px.ui.widget.Input,

	construct: function(oParent, oParentNode, sType, sProperty, mValue) {
		this.base(arguments, oParent, oParentNode, sType, sProperty, mValue, true)
	}
})

//------------------------------------------------------------------------------

px.Class.define('px.ui.widget.Textarea',
{
	extend: px.ui.widget.Widget,

	construct: function(oParent, oParentNode, sType, sProperty, mValue)
	{
		this.base(arguments, oParent, oParentNode, sType, sProperty, mValue)

		var oProperty = this._oProperty
		var aParameters = oProperty.aParameters

		if (mValue) {
			if (mValue instanceof Array) {
				var sText = String(mValue.join(String.fromCharCode(13)))
			} else {
				if (mValue instanceof Object) {
					var sText = ''
				} else {
					var sText = String(mValue)
				}
			}
		} else {
			var sText = ''
		}

		if (this._bEdit)
		{
			this._oWidget = document.createElement('textarea')
			var oTextarea = this._oWidget
			oTextarea.name = 'px_' + sProperty

			oTextarea.cols = aParameters.cols || 60
			oTextarea.rows = aParameters.rows || 6

			if (!aParameters.nowrap) {
				oTextarea.setAttribute('wrap', 'off')
				oTextarea.style.overflow = 'auto'
			}

			oTextarea.value = sText
			oParentNode.appendChild(oTextarea)
			oTextarea.onchange = this._onchange
			oTextarea = null
		}
		else
		{
			oParentNode.appendChild(document.createTextNode(sText))
		}
	}
})

Object.extend(
	px.Proto,
	{
		resize: function() {
			if (this._bEdit) {
				var iWidth = this._oParent.oParentNode.offsetWidth - this._oParentNode.offsetLeft - 35
				if (iWidth > 0) {
					this._oParentNode.firstChild.style.width = iWidth - 10 + 'px'
				}
			}
		}
	}
)

//------------------------------------------------------------------------------

px.Class.define('px.ui.widget.Array',
{
	extend: px.ui.widget.Textarea,

	construct: function(oParent, oParentNode, sType, sProperty, mValue) {
		this.base(arguments, oParent, oParentNode, sType, sProperty, mValue)
	}
})

//------------------------------------------------------------------------------

px.Class.define('px.ui.widget.ImageClipping',
{
	extend: px.ui.widget.Input,

	construct: function(oParent, oParentNode, sType, sProperty, mValue)
	{
		this.base(arguments, oParent, oParentNode, sType, sProperty, mValue)
		
		if (this._bEdit) {
			var oButton = document.createElement('button')
			oParentNode.appendChild(oButton)
			oButton.appendChild(
				document.createTextNode(
					oTranslation['choose']
				)
			)
			oButton.onclick = px.lang.Function.bindEvent(this._showPicker, oParent)
		}
	},

	destruct: function() {
		if (this._bEdit) {
			this._oParentNode.childNodes[1].onclick = null
		}
	}
})

Object.extend(
	px.Proto,
	{
		_showPicker: function(oEvent) {
			var oElement = px.Event.element(oEvent)
			var w = window.open('./modules/System.pxm/includes/imageClipper.php')
			pxp.oData['temp.oTargetElement'] = oElement.previousSibling
			pxp.oData['temp.sImageUrl'] = this.oParent.oShare.sUrl + this.oParameters.sPath
//			w.sImageUrl = this.oParent.oShare.sUrl + this.oParameters.sPath
			return false
		}
	}
)