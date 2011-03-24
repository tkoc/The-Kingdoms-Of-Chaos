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
px.Class.define('px.io.Request',
{
	extend: px.core.Object,

	construct: function(oOptions)
	{
		this.oTransport = this.getTransport()

		this.oOptions = {
			sUrl: pxp.sUrl,
			sMethod: 'get',
			bAsync: true,
			sContentType: 'application/x-www-form-urlencoded',
			sParameters: ''
		}

		Object.extend(this.oOptions, oOptions || {})

		this.request()
	},

	destruct: function() {
		
	}
})

Object.extend(
	px.Statics,
	{
		aEvents: ['Uninitialized', 'Loading', 'Loaded', 'Interactive', 'Complete'],

		get: function(sParameters, bEval, sUrl, bRaiseError) {
			return this._request('get', sParameters, bEval, sUrl, bRaiseError)
		},

		post: function(sParameters, bEval, sUrl, bRaiseError) {
			return this._request('post', sParameters, bEval, sUrl, bRaiseError)
		},

		exists: function(sShare, sPath) {
			return this.get(
				'sShare=' + encodeURIComponent(sShare) +
				'&sAction=_editExists' +
				'&sPath=' + encodeURIComponent(sPath),
				true,
				null,
				false
			)
		},

		_request: function(sMethod, sParameters, bEval, sUrl, bRaiseError)
		{
			var oRequest = new px.io.Request(
				{
					bAsync: false,
					sMethod: sMethod,
					sParameters: sParameters
				}
			)

			if (bRaiseError && !oRequest.success()) {
				pxp.log('XmlHttpRequest failed (' + oRequest.oTransport.status + ')')
				return false
			}

			var sResponse = oRequest.oTransport.responseText

			if (bEval != false) {
				if (sResponse != '') {
					try {
						var oJson = eval('(' + sResponse + ')')
					} catch(e) {
						pxp.showError(sResponse)
						return false
					}
					if (oJson.bError && bRaiseError != false) {
						pxp.showError(oJson)
						return false
					} else {
						return oJson
					}
				} else {
					var oJson = {bOk: true}
				}
				return oJson
			} else {
				return sResponse
			}
		}
	}
)

Object.extend(
	px.Proto,
	{
		getTransport: function() {
			if (typeof XMLHttpRequest != 'undefined') {
				return new XMLHttpRequest()
			} else {
				try {
					return new ActiveXObject('Msxml2.XMLHTTP')
				} catch(e) {
					try {
						return new ActiveXObject('Microsoft.XMLHTTP')
					} catch(e) {}
				}
			}
		},

		_complete: false,

		request: function()
		{
			var oOptions = this.oOptions

			this.sUrl = oOptions.sUrl
			this.sMethod = oOptions.sMethod

			var sParameter = oOptions.sParameters
			if (sParameter != '') {
				// when GET, append parameters to URL
				if (this.sMethod == 'get') {
					this.sUrl += (this.sUrl.indexOf('?') > -1 ? '&' : '?') + sParameter
				} else if (/Konqueror|Safari|KHTML/.test(navigator.userAgent)) {
					sParameter += '&_='
				}
			}

			try
			{
				this.oTransport.open(
					this.sMethod.toUpperCase(),
					this.sUrl,
					oOptions.bAsync
				)

				//if (oOptions.bAsync) {
				//	setTimeout(function() { this.respondToReadyState(1) }.bind(this), 10)
				//}

				this.oTransport.onreadystatechange = px.lang.Function.bind(this.onStateChange, this)
				this.setRequestHeaders()

				this.body = this.sMethod == 'post' ? (oOptions.postBody || sParameter) : null

				this.oTransport.send(this.body)

				/* Force Firefox to handle ready state 4 for synchronous requests */
				if (!oOptions.bAsync && this.oTransport.overrideMimeType) {
					this.onStateChange()
				}
			} catch (e) {
				pxp.showError('Could not send XmlHttp request')
			}
		},

		onStateChange: function() {
			var readyState = this.oTransport.readyState			
			if (readyState > 1 && !((readyState == 4) && this._complete)) {
		    this.respondToReadyState(readyState)
			}
		},

		setRequestHeaders: function()
		{
			var oHeaders = {
				'X-Requested-With': 'XMLHttpRequest',
				'Accept': 'text/javascript, text/html, application/xml, text/xml, */*'
			}

			if (this.sMethod == 'post') {
				oHeaders['Content-type'] = this.oOptions.sContentType + '; charset=' + pxp.sEncoding
			}

			/* Force "Connection: close" for older Mozilla browsers to work
			 * around a bug where XMLHttpRequest sends an incorrect
			 * Content-length header. See Mozilla Bugzilla #246651.
			 */
			if (this.oTransport.overrideMimeType && (navigator.userAgent.match(/Gecko\/(\d{4})/) || [0,2005])[1] < 2005) {
				oHeaders['Connection'] = 'close'
			}

			for (var name in oHeaders) {
				this.oTransport.setRequestHeader(name, oHeaders[name])
			}
		},

		success: function() {
		  return !this.oTransport.status || (this.oTransport.status >= 200 && this.oTransport.status < 300)
		},

		respondToReadyState: function(iReadyState)
		{
			var sState = px.io.Request.aEvents[iReadyState]

			if (sState == 'Complete')
			{
				try {
					(this.oOptions['on' + this.oTransport.status]
					|| this.oOptions['on' + (this.success() ? 'Success' : 'Failure')]
					|| pxConst.EMPTY_FUNCTION)(this.oTransport)
					this._complete = true
				} catch (e) {}

				try {
					(this.oOptions['on' + sState] || pxConst.EMPTY_FUNCTION)(this.oTransport)
				} catch (e) {}

				if (sState == 'Complete') {
					this.oTransport.onreadystatechange = pxConst.EMPTY_FUNCTION
				}
			}
		}
	}
)