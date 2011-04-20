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
px.Class.define('px.ui.listview.Row',
{
	extend: px.ui.ListItem,
  
  construct: function(oParent) {
		this.oParent = oParent
		this.iIndex
		this.oTr
		this.sFilename
		this.bDirectory
		this.oA
		this.oNameNode
	},

	destruct: function()
	{
		this.oA.onclick = null
		var oTr = this.oTr
		oTr.onclick = null
		oTr.ondblclick = null
		oTr.onmouseover = null
		oTr.onmouseout = null
		oTr.onmousedown = null
		oTr.onmouseup = null

		for (var c=0, m2=oTr.childNodes.length; c<m2; c++) {
			var oTd = oTr.childNodes[c]
			if (oTd.onclick) oTd.onclick = null
		}

		this._disposeFields('oParent', 'oTr', 'oA', 'oNameNode', '_oEditCell')		
	}
})

Object.extend(
	px.Proto,
		{
		selectItem: function(oEvent, bDrag) {
			var oParent = this.oParent
			var sFilename = this.sFilename
			if (oEvent.ctrlKey || bDrag || oParent.bChecklist) {
				if (oParent.oSelected[sFilename] && !bDrag) {
					delete oParent.oSelected[sFilename]
				} else {
					oParent.oSelected[sFilename] = this
				}
			} else {
				oParent.oSelected = {}
				oParent.oSelected[sFilename] = this
			}
			oParent._fill()
		},
		
		editClick: function(oEvent)
		{
			var cFunction = px.lang.Function
			var cElement = px.html.Element
			var cForm = px.html.Form

			var oParent = this.oParent
			var oElement = px.Event.element(oEvent)
			var oTd = oElement.nodeName == 'A' ? oElement.parentNode : oElement
			var sPath = oParent.oParameters.getPath()
			var aObjects = oParent.oResults[sPath]
			var oSettings = oParent.oSettings[sPath]

			var iRowIndex = oTd.parentNode.id.substr(1)
			var sColumn = oTd.className
			var sType = oParent.aItems[iRowIndex - oParent._iOffset].getType()
			var oOptions = oSettings.oOptions[sType][sColumn]

			cElement.hide(oTd.firstChild)

			oParent._bEditing = true
			this._oEditCell = oTd

			if (oTd.childNodes.length < 2)
			{
				switch (oParent.oColumns[sColumn]['sType'])
				{
					case 'select':
						var oSelect = document.createElement('select')
						oSelect.onmousedown = oSelect.onmouseup = oSelect.onclick = cFunction.cancelEvent
						oSelect.onblur = cFunction.bindEvent(this.exitEdit, this)

						oTd.appendChild(oSelect)

						for (var sOption in oOptions) {
							var oOption = new Option(oOptions[sOption], sOption)
							oSelect.options[oSelect.options.length] = oOption
							oOption.selected = sOption == aObjects[iRowIndex][sColumn]
							oSelect.appendChild(oOption)
						}
		
						cForm.activate(oSelect)
						oSelect = null
		
						break;
					default:
						alert("def")
						break;
				}
			} else {
				cElement.show(oTd.childNodes[1])
				cForm.activate(oTd.childNodes[1])
			}

			oEvent.cancelBubble = true
			return false
		},

		exitEdit: function(oEvent)
		{
			var cElement = px.html.Element

			var oParent = this.oParent
			var oElement = px.Event.element(oEvent)
			var oTd = oElement.parentNode
			var sPath = oParent.oParameters.getPath()
			var aObjects = oParent.oResults[sPath]
			var oSettings = oParent.oSettings[sPath]
			var iRowIndex = oTd.parentNode.id.substr(1)
			var sColumn = oTd.className
			var oObject = aObjects[iRowIndex]

			switch (oParent.oColumns[sColumn]['sType']) {
				case 'select':
					var mValue = oTd.childNodes[1].options[oTd.childNodes[1].selectedIndex].value
					var mOption = oSettings.oOptions[oObject['sType']][sColumn][mValue]
					break;
				default:
					alert("def")
					break;
			}
			
			if (oObject[sColumn] != mValue)
			{
				oObject[sColumn] = mValue
				oTd.firstChild.firstChild.nodeValue = mOption || mValue

				var sObjectPath = px.util.buildPath(
					oObject['sRelDir'],
					oObject['sName']
				)

				var oResult = oParent._save(
					{sAction: '_editProperty'},
					'sPath=' + sObjectPath + '&sProperty=' + sColumn + '&&px_' + sColumn + '=' + mValue
				)

				// Check for errors in result

				oParent.oParameters.sAction = '_openJson'
			}
		
			cElement.show(oTd.firstChild)
			cElement.hide(oTd.childNodes[1])

			this._oEditCell = null
			oParent._bEditing = false
		},
		
		rowMouseOver: function(oEvent) {
			oEvent.cancelBubble = true
			if (pxp.bIe) {
				this.oTr.className = 'hover'
			}
			if (
				pxp.bDragging &&
				this.bDirectory &&
				pxp.sDragType == 'item' &&
				!pxp.oSelectedControl.oSelected[this.sFilename] &&
				this.sFilename.indexOf(pxp.oSelectedControl.oActiveItem.sFilename) != 0 &&
				this.getType() != 'pxVirtualDirectory'
			) {
				//this.oNameNode.className = ''
				px.html.Element.addClassName(this.oNameNode, 'drop')
			}
		},
		
		rowMouseOut: function() {
			if (pxp.bIe) {
				this.oTr.className = null
			}
			if (this.bDirectory) {
				px.html.Element.removeClassName(this.oNameNode, 'drop')
			}
		},
		
		rowMouseUp: function(oEvent) {
			if (this.bDirectory && px.html.Element.hasClassName(this.oNameNode, 'drop')) {
				px.action.pxObject_editClipboard.itemDrop(this)
			}
		}
	}
)