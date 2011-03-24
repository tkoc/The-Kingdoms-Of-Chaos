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
px.util =
{
// STRING

	trim: function(sString) {
		return sString.replace(/^\s+/, '').replace(/\s+$/, '')
	},

	tagFirstChar: function(sString, sTag) {
		return '<' + sTag + '>' + sString.substr(0, 1) + '</' + sTag + '>' + sString.substr(1)
	},

	dirname: function(sPath) {
		var iPos = String(sPath).lastIndexOf('/')
		var sPath = sPath.substr(0, iPos)
		if (sPath == '') {
			return '/'
		}
		return sPath
	},

	basename: function(sPath) {
		var iPos = String(sPath).lastIndexOf('/')
		if (iPos != -1) {
			return sPath.substr(iPos + 1)
		} else {
			return sPath
		}
	},

	buildPath: function()
	{
		var sPath = ''
		for (var a=0; a<arguments.length; a++)
		{
			if (sPath.lastIndexOf('/') == sPath.length - 1) {
				if (arguments[a].substr(0, 1) == '/' && arguments[a] != '/' && a != 0) {
					sPath += arguments[a].substr(1)
				} else {
					sPath += arguments[a]
				}
			} else {
				if (arguments[a].substr(0, 1) == '/') {
					sPath += arguments[a] 
				} else {
					sPath += '/' + arguments[a]
				}				
			}
		}
		return sPath
	},

	getRandomId: function() {
		return 'px' + String(Math.random()).substr(2)
	},

	addLeadingZero: function(iNumber) {
		var sPart = String(iNumber)
		if (sPart.length == 1) {
			sPart = '0' + sPart
		}
		return sPart
	},

	parseDateTime: function(sDate)
	{
		var aParts = sDate.split(' ')
		var aDateParts = []
		var aTimeParts = []
	
		for (var i=0; i<aParts.length; i++) {
			var sPart = px.util.trim(aParts[i])
			if (sPart != '') {
				if (aDateParts.length == 0) {
					var aDateParts = sPart.split('-')
					continue
				}
				var aTimeParts = sPart.split(':')
				break
			}
		}
	
		var iYear = Number(aDateParts[0])
		var iMonth = Number(aDateParts[1])
		iMonth--
		var iDay = Number(aDateParts[2])
	
		var iHour = Number(aTimeParts[0])
		if (isNaN(iHour)) iHour = 0
	 
		var iMinute = Number(aTimeParts[1])
		if (isNaN(iMinute)) iMinute = 0
		
		var iSecond = Number(aTimeParts[2])
		if (isNaN(iSecond)) iSecond = 0
	
		var oDate = new Date(iYear, iMonth, iDay, iHour, iMinute, iSecond) 
	
		return oDate.getTime() / 1000
	},
	
// MIXED

	sleep: function(iMilliSeconds) {
		var now = new Date()
		var dExitTime = now.getTime() + iMilliSeconds
		while (true) {
			dNow = new Date()
			if (dNow.getTime() > dExitTime) {
				return
			}
		}
	},

	checkFilename: function(sName, bPath)
	{
		var aInvalidChars = [String.fromCharCode(92), ':', '*', '?', '<', '>', '|', '..']
		if (!bPath) {
			aInvalidChars.push('/')
		} else {
			aInvalidChars.push('//')
		}
		for (var i=aInvalidChars.length-1; i>=0; i--) {
			if (sName.indexOf(aInvalidChars[i]) > -1) {
				return false
			}
		}
		if (bPath) {
			if (px.util.trim(sName.replace(/\//g, '')) == '') {
				return false
			}
		}
		return true
	},
	
	getNewFilename: function(sDefault, sType, bPath) {
		var sNewName = sDefault || ''
		while (sNewName != null) {
			sNewName = prompt(oTranslation['newName'], sNewName)
			if (sNewName != null) {
				if (px.util.checkFilename(sNewName, bPath)) {
					sNewName = pxp.addValidExtension(sNewName, sType)
					if (px.util.trim(sNewName) != '') {
						break
					}
				}
				alert(oTranslation['error.invalidFilename'])
			}
		}
	
		return sNewName
	},

	previewCheck: function(sExtension, aAdditionalExtensions)
	{
		if (aAdditionalExtensions && px.lang.Array.contains(aAdditionalExtensions, sExtension)) {
			return true
		}
		return px.lang.Array.contains(
			(pxp.oShareview.oSelected.oShare.iImageLibrary == 1 ?
				pxp.oData.aImageMagickExtensions : pxp.oData.aGdExtensions),
			sExtension
		)
	},

// OBJECT

	getLength: function(oObject) {
		if (oObject instanceof Array) {
			return oObject.length
		} else {
			var i = 0
			for (var s in oObject) i++
			return i
		}
	},

	getLastProperty: function(oObject) {
		var v
		for (var p in oObject) v = p
		return v
	},

	getFirstProperty: function(oObject) {
		for (var p in oObject) return p
	},
	
	setMaxHeight: function(oObject) {
		if (oObject) {
			oObject.style.height = oObject.parentNode.offsetHeight - oObject.offsetTop + 'px'
		}
	}

}