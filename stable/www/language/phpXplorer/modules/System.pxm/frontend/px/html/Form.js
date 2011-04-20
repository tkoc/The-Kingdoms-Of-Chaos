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
px.Class.define('px.html.Form')

Object.extend(
	px.Statics,
	{
		aIgnoreTypes: ['file', 'submit', 'image', 'reset', 'button'],

		serialize: function(oForm)
		{
			var cArray = px.lang.Array
			var aResult = []
			for (var i=0, l=oForm.elements.length; i<l; i++)
			{
				var oElement = oForm.elements[i]
				var sName = oElement.name
				if (sName && oElement.type)
				{
					if (cArray.contains(this.aIgnoreTypes, oElement.type)  ) {
						continue
					}
					switch (oElement.type) {
						case 'checkbox':
						case 'radio':
							if (oElement.checked) {
								aResult.push(sName + '=' + encodeURIComponent(oElement.value))
							}
							break
						case 'select-multiple':
							for (var i2=0, l2=oElement.options.length; i2<l2; i2++) {
								if (oElement.options[i2].selected) {
									aResult.push(sName + '=' + encodeURIComponent(oElement.options[i2].value))
								}
							}
							break
						default:
							aResult.push(sName + '=' + encodeURIComponent(oElement.value))
							break	
					}
				}
			}
			return aResult.join('&')
		},
		
	  disable: function(oForm) {
	  	for (var i=0, l=oForm.elements.length; i<l; i++) {
	  		var oElement = oForm.elements[i]
	  		oElement.blur()
	  		oElement.disabled = true
	  	}
	  },
	
	  enable: function(oForm) {
	  	for (var i=0, l=oForm.elements.length; i<l; i++) {
	  		oForm.elements[i].disabled = null
	  	}
	  },
	  
	  activate: function(oElement) {
	    try {
	      oElement.focus()
				oElement.select()
	    } catch (e) {}
	  }
	}
)