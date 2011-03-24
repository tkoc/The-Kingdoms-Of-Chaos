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
px.Class.define('px.action.pxDirectories_uploadHtml')

Object.extend(
	px.Statics,
	{
		sInputId: 'file_input',

		run: function(oObject) {
			px.action.pxDirectories___upload.run(oObject)
			return false
		},
		
		changeMethod: function(oSelect) {
			location.href = './action.php?' + oSelect.options[oSelect.selectedIndex].value
		},

		addFileInput: function()
		{
			var oContainer = $('file_attach')
			var oNewInput = document.createElement('input')
			oNewInput.setAttribute('type', 'file')
			oNewInput.setAttribute('name', 'aFiles[]')
			oNewInput.setAttribute('class', 'upload')
			oNewInput.className = 'upload'
		
			oNewInput.onchange = function() {
				px.action.pxDirectories_uploadHtml.attachFile(this)
			}

			var random = Math.random()
			var random = random * 10
			oNewInput.setAttribute('id', 'file_' + random)
			this.sInputId = 'file_' + random

			$('submitButton').disabled = false

			return oContainer.appendChild(oNewInput)
		},

		hideInput: function() {
			var oInput = document.getElementById(this.sInputId)
			if (oInput.value != '') {
				oInput.style.display = 'none'
				this.addFileToList()
				return true
			} else {
				return false
			}
		},

		addFileToList: function() {
			var oInput = $(this.sInputId)
			var oContainer = $('aFileSelection')
			var newOption = document.createElement('option')
			var newOptionText = document.createTextNode(oInput.value)
			newOption.appendChild(newOptionText)
			oContainer.appendChild(newOption)
		},

		fileExists: function(sNewFile) {
			var aOptions = document.forms[0].aFileSelection.options
			for (var i=0; i<aOptions.length; i++) {
				if (aOptions[i].text == sNewFile) {
					return true
				}
			}
			return false
		},

		removeSelectedFile: function()
		{
			var oOptions = document.forms[0].aFileSelection.options

			if (oOptions.selectedIndex > -1) {
		
				for (var i=oOptions.length-1; i>-1; i--) {
					if (oOptions[i].selected) {
						
						var oContainer = $('file_attach')
						
						for (var c=0; c<oContainer.childNodes.length; c++) {
							if (oContainer.childNodes[c].value == oOptions[i].text) {
								oContainer.removeChild(oContainer.childNodes[c])
								break
							}
						}
		
						oOptions[i] = null
					}
				}
			}

			$('submitButton').disabled = !(oOptions.length > 0)
		},

		attachFile: function(oInput) {
			if (!this.fileExists(oInput.value)) {
				if (this.hideInput()) {
					var oNewInput = this.addFileInput();
					oNewInput.focus()
				}
			}
		},

		validate: function() {
			if (document.forms[0].aFileSelection.options.length == 0) {
				pxp_alert(getTranslation('validFilename'))
				return false
			}
			return true
		},

		dispose: function() {
			var oContainer = $('file_attach')
			if (oContainer) {
				for (var i=0, l=oContainer.childNodes.length; i<l; i++) {
					oContainer.childNodes[i].onchange = null
				}
			}
		}
	}
)