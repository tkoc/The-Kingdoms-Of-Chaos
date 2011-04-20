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
px.Class.define('px.io.RequestParameters',
{
	extend: px.core.Object,
	construct: function()
	{
		this.sShare
		this.sAction = '_openJson'
		this.sPath = '/'
		this.aNames = []
		this.aTypes = []
		this.bFull
		this.sOrderBy = 'sName'
		this.sOrderDirection = 'asc'
		this.sSearchQuery
		this.bOnlyDirectories
		this.bFillOptions
		this.bOsPermissions
		this.bFilesize
		this.bHierarchical
		this.bRecursive
		this.bRecursiveFlat
		this.iOffset = 0
		this.bFile
	}
})

Object.extend(
	px.Proto,
	{
		set: function(oParameters) {
			if (oParameters) {
				for (var o in oParameters) {
					this[o] = oParameters[o]
				}
			}
		},

		sync: function(oParameters) {
			this.sPath = oParameters.sPath
			this.sSearchQuery = oParameters.sSearchQuery
			this.sOrderBy = oParameters.sOrderBy
			this.sOrderDirection = oParameters.sOrderDirection
		},
		
		getUrl: function()
		{
			var aParams = []

			for (var p in this) {
				if (p.indexOf('_') == 0 || p == 'classname') {
					continue
				}

				switch (typeof this[p]) {
					case 'object':
						if (this[p] && this[p].length) {
							var aValues = []
							for (var i=0; i<this[p].length; i++) {
								aValues.push(encodeURIComponent(this[p][i]))
							}
							aParams.push(p + '=' + aValues.join('|'))
						}
						break
					default:
						if (this[p] instanceof Function) {
							continue
						}
						if (this[p] != null && this[p] != undefined) {
							aParams.push(p + '=' + encodeURIComponent(this[p]))
						}
						break
				}
			}

			return aParams.join('&')
		},

		getPath: function(bShare)
		{
			var sPath = ''
			if (bShare != false) {
				sPath += this.sShare + ':'
			}
			sPath =  px.util.buildPath(sPath, this.sPath)
			if (this.aNames.length > 0) {
				sPath = px.util.buildPath(sPath, this.aNames[0])
			}
			return sPath
		}
	}
)