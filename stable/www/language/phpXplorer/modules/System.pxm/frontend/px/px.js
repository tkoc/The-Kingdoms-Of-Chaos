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
Object.extend = function(destination, source) {
  for (var property in source) {
    destination[property] = source[property];
  }
  return destination;
}

var px =
{
	action: {},
	util: {},
	ui: {},
	io: {},

	Proto: null,
	Statics: null,
	Class:
	{
		__registry : {},

		define: function(sPath, oConfig)
		{	
			var oClass

			if (!oConfig) {
				oConfig = {}
			}

			if (oConfig.construct) {
				oClass = oConfig.construct
			} else {
				oClass = function() {}
			}

			this.createNamespace(sPath, oClass)

			if (oConfig.extend) {

        var oHelper = function(){}
        oHelper.prototype = oConfig.extend.prototype
        var oProto = new oHelper

        oClass.prototype = oProto

				oClass.base = oClass.superclass = oConfig.extend

				oProto.constructor = oClass
			}
			
			if (oConfig.destruct) {
				oClass.$$destructor = oConfig.destruct
			}

			oClass.classname = oClass.prototype.classname = sPath;

			//oClass.prototype.classname = 

			/*
					for (var sKey in oConfig.statics) {
						oClass[sKey] = oConfig.statics[sKey]
					}
					for (var sKey in oConfig.members) {
						oClass.prototype[sKey] = oConfig.members[sKey]
					}
			*/

				px.Statics = oClass
				px.Proto = oClass.prototype

				//this.__registry[sPath] = oClass

				return oClass
		},
		
		createNamespace: function(sPath, oObject)
		{
			var aParts = sPath.split('.')
			var parent = window
			var sPart = aParts[0]
			
			for (var i=0, m=aParts.length-1; i<m; i++, sPart=aParts[i])
			{
				if (!parent[sPart]) {
					parent = parent[sPart] = {}
				} else {
					parent = parent[sPart]
				}
			}
			
			if (parent[sPart] != undefined) {
				alert('An object of the name "' + name + '" aready exists')
			}
			
			parent[sPart] = oObject

			return sPart
		}
	}
}