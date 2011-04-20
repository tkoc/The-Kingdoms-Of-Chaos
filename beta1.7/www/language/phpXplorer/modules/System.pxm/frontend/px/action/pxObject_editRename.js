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
px.Class.define('px.action.pxObject_editRename')

Object.extend(
	px.Statics,
	{
		run: function(oView)
		{
			var cFunction = px.lang.Function
			var oFirst

			if (!oView) {
				return false
			}

			oView.setAllowSelection(true)

			px.ui.ContextMenu.hide()
	
			for (var sSelected in oView.oSelected)
			{
				var oItem = oView.oSelected[sSelected]
				var oNameNode = oItem.oNameNode
		
				if (oNameNode.style.display == 'none') {
					continue
				}
	
				oNameNode.style.display = 'none'
	
				var oInput = document.createElement('input')
				oInput.setAttribute('autocomplete', 'off')
	
				if (oNameNode.nextSibling) {
					oNameNode.parentNode.insertBefore(oInput, oNameNode.nextSibling)
				} else {
					oNameNode.parentNode.appendChild(oInput)
				}
	
				oInput.value = oNameNode.firstChild.nodeValue
				oInput.onblur = cFunction.bindEvent(this.exitControl, this)
				oInput.onclick = cFunction.cancelEvent
				oInput.onmousedown = cFunction.cancelEvent
				oInput.onkeydown = cFunction.bindEvent(this.controlKeyDown, oInput)
				oInput.onkeyup = cFunction.bindEvent(this.controlKeyUp, oInput)
				oInput.oView = oView
				oInput.oItem = oItem
				
				oInput.focus()
				oInput.select()
				break
			}
		},
	
		controlKeyDown: function(oEvent) {
			if (oEvent.keyCode) {
				if (pxp.bAltDown && oEvent.keyCode == 219) return false
				if (pxp.bShiftDown && oEvent.keyCode == 55) return false
				if (pxp.bShiftDown && oEvent.keyCode == 61) return false
				if (pxp.bShiftDown && oEvent.keyCode == 190) return false
				if (pxp.bShiftDown && oEvent.keyCode == 187) return false
				if (pxp.bShiftDown && oEvent.keyCode == 219) return false
				if (oEvent.keyCode == 226) return false
			}
		},
	
		controlKeyUp: function(oEvent) {
			if (oEvent.keyCode) {
				if (oEvent.keyCode == 27) {
					px.action.pxObject_editRename.hideControl(this)
				}
				else if (oEvent.keyCode == 13) {
					this.onblur = null
					px.action.pxObject_editRename.exitControl(oEvent)
				}
			}
		},
	
		hideControl: function(oInput)
		{	
			oInput.oView.setAllowSelection(false)
	
			oInput.onblur = null
			oInput.onclick = null
			oInput.onmousedown = null
			oInput.onkeydown = null
			oInput.onkeyup = null
			oInput.oView = null
			oInput.oItem = null
	
			oInput.previousSibling.style.display = 'inline'
			oInput.parentNode.removeChild(oInput)
		},
	
		exitControl: function(oEvent)
		{
			document.onmousemove = null
	
			var oInput = px.Event.element(oEvent)
			var oNameNode = oInput.previousSibling
			var oView = oInput.oView
	
			if (oNameNode.firstChild.nodeValue != oInput.value) {
	
				if (!px.action.pxObject_editRename.checkFilename(oInput)) {
					alert(oTranslation['error.invalidFilename'])
					px.action.pxObject_editRename.hideControl(oInput)
					return false
				}
	
				var oItem = oInput.oItem
	
				var sShare = oItem.oParent.oParameters.sShare
				var sDirname = oItem.getDirname()
				var sName = oItem.getFilename()
				var sNewName = oInput.value
	
				var oResult = px.io.Request.post(
					'sAction=_editClipboard' +
					'&sShare=' + encodeURIComponent(sShare) +
					'&sPath=' + encodeURIComponent(sDirname) +
					'&aNames=' + encodeURIComponent(sName) +
					'&aNewNames=' + encodeURIComponent(sNewName) +
					'&sDestinationPath=' + encodeURIComponent(sDirname) +
					'&sDestinationShare=' + encodeURIComponent(sShare) +
					'&sMode=move',
					true,
					null,
					false
				)
	
				if (oResult.bOk) {
					var sNewPath = px.util.buildPath(sDirname, sNewName)
					oView.oParent.oParent.oParent.oActionviewFiles.renameAction(
						oItem.sFilename,
						sNewPath,
						sNewPath.substr(1)
					)
					oNameNode.firstChild.nodeValue = sNewName
					pxp.refreshView(sDirname)
				} else {
					pxp.showError(oResult)
				}
			}
			px.action.pxObject_editRename.hideControl(oInput)
		},

		checkFilename: function(oInput) {
			return px.util.checkFilename(oInput.value) && px.util.trim(oInput.value) != ''
		}
	}
)