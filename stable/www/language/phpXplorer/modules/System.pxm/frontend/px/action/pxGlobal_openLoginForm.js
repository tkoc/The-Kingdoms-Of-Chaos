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
px.Class.define('px.action.pxGlobal_openLoginForm',
{
	extend: px.Action,

	construct: function(sId, oParent, oParentNode, oParameters)
	{
		this.base(arguments, sId, oParent, oParentNode)
		
		
		var oLabel = document.createElement('label')
		oLabel.appendChild(document.createTextNode(oTranslation['property.sUsername']))
		this.oDiv.appendChild(oLabel)
	
		var oInputName = document.createElement('input')
		oInputName.type = 'text'
		oInputName.id = oInputName.name = 'pxAuthUser'
		this.oDiv.appendChild(oInputName)
		
		var oLabel = document.createElement('label')
		oLabel.appendChild(document.createTextNode(oTranslation['property.sPassword']))
		this.oDiv.appendChild(oLabel)

		oInputPassword = document.createElement('input')
		oInputPassword.type = 'password'
		oInputPassword.id = oInputPassword.name = 'pxAuthPassword'
		this.oDiv.appendChild(oInputPassword)
		
		this.oButtonLogin = document.createElement('button')
		this.oDiv.appendChild(this.oButtonLogin)
		px.Event.addListener(this.oButtonLogin, 'click', px.action.pxGlobal_openLogin.login)
		this.oButtonLogin.appendChild(document.createTextNode(oTranslation['action.pxGlobal_openLogin']))
	
		this.oButtonLogin = document.createElement('button')
		this.oDiv.appendChild(this.oButtonLogin)
		px.Event.addListener(this.oButtonLogin, 'click', px.action.pxGlobal_openLogin.guestLogin)
		this.oButtonLogin.appendChild(document.createTextNode(oTranslation['guestAccess']))

		oInputName.focus()
		oInputName.select()

		oInput = null
		oSelect = null
		oTd = null
		oTr = null
		oBody = null
		oTable = null
		oInputName = null
		
		
	}
})