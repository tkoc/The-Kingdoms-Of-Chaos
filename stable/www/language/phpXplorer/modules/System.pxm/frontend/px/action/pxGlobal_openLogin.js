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
px.Class.define('px.action.pxGlobal_openLogin')

Object.extend(
	px.Statics,
	{
		oLoginview: null,
	
		run: function()
		{			
			this.oLoginview = new px.ui.Actionview()
			this.oLoginview.sClass = 'loginBar'
			this.oLoginview.iMenuType = pxConst.MENU_TABS

			this.oLoginview.addAction(
				'login',
				oTranslation['action.pxGlobal_openLogin'],
				px.action.pxGlobal_openLoginForm,
				true
			)
			
			/*
			this.oLoginview.addAction(
				'register',
				'Neuanmeldung',
				px.action.pxGlobal_openLoginForm
			)
			*/

			this.oLoginview.init($('loginContainer'))
		},

		dispose: function() {
			this.oLoginview.dispose()
		},
	
		login: function(oEvent) {
			//if (oEvent && oEvent.keyCode != 13) {
			//	return
			//}
			document.forms[0].submit()
		},
	
		guestLogin: function() {
	  	var oForm = document.forms[0]
	  	oForm.pxAuthUser.value = 'everyone'
	  	oForm.pxAuthPassword.value = 'everyone'
	  	px.action.pxGlobal_openLogin.login()
		},
		
		signup: function() {
			location.href = './action.php?sAction=pxGlobal_editProfile'
		}
	}
)

px.Event.addListener(window, 'load', px.lang.Function.bind(px.action.pxGlobal_openLogin.run, px.action.pxGlobal_openLogin))