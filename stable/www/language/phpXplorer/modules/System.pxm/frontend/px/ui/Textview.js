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
px.Class.define('px.ui.Textview',
{
	extend: px.io.RemoteView,
	
	construct: function(oParent, oParentNode)
	{
		this.base(arguments, oParent, oParentNode)

		this.oParameters.sAction = 'edit'

		this.oForm = document.createElement('form')
		this.oForm.className = 'pxTextview'
		this.oForm.method = 'post'
		this.oForm.action = pxp.sUrl
		oParentNode.appendChild(this.oForm)

		this.oTextarea = document.createElement('textarea')
		this.oTextarea.name = 'sContent'
		this.oTextarea.className = 'pxTextview'
		this.oTextarea.setAttribute('wrap', 'off')
		this.oTextarea.style.overflow = 'auto'	
		this.oForm.appendChild(this.oTextarea)
		this.oTextarea.onchange = px.lang.Function.bind(this.onTextChange, this)		
	},

	destruct: function() {
		this.oTextarea.onchange = null
		this._disposeFields('oParent', 'oParentNode', 'oForm', 'oTextarea')
	}
})

Object.extend(
	px.Proto,
	{
		onTextChange: function() {
			this.oParent.setChanged(true)
		},
		
		save: function()
		{
			var oResult = this._save({sAction: 'edit'})
		
			if (oResult.bOk) {
				pxp.refreshView(px.util.dirname(this.oParameters.sPath))
				this.oParent.setChanged(false)
			}
			return false
		},

		_update: function() {
			this.oTextarea.value = this.oResults[this.oParameters.getPath()]
			px.html.Form.enable(this.oForm)
			this.oTextarea.focus()
		}
	}
)