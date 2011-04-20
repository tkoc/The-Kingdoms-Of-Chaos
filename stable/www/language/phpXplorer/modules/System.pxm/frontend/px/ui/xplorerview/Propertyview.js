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
px.Class.define('px.ui.xplorerview.Propertyview',
{
	extend: px.core.Object,

	construct: function(oParent) {
		this.oParent = oParent
		this.sCurrentId
	},
	
	destruct: function() {
		this.oEventLabel.onclick = null
		this.oEvent.onchange = null
		this._disposeFields(
			'oParent', 'oParentNode', 'oEventLabel', 'oEvent' // 'oInheritedEventLabel', 'oInheritedEvent', 'oParameterLabel'
		)
	}
})

Object.extend(
	px.Statics,
	{
		_eventLabelClick: function(oEvent) {
			pxp.oSelectedControl = this
			var oCm = px.ui.ContextMenu
			oCm.clear()
			oCm.addItem('action', 'Aktion auslösen', pxConst.sGraphicUrl + '/types/pxAction_pxAction.php.png', false)
			oCm.addItem('email', 'E-Mail senden', pxConst.sGraphicUrl + '/actions/pxGlobal_sendMail.png', false)
			oCm.show(oEvent)
		},

		_eventCodeChange: function() {
			var oView = this.oParent.oXplorerview
			var oActive = oView.oActiveItem
			if (oActive) {
				var sId = oActive.sId	
				if (this.oEvent.value != '') {
					if (!oView.oObject.aEvents[sId]) {
						if (oActive.oA.nextSibling) {
							px.html.Element.appendImage(oActive.oDiv, pxConst.sGraphicUrl + '/bulletGo.png', oActive.oA.nextSibling)
						} else {
							px.html.Element.appendImage(oActive.oDiv, pxConst.sGraphicUrl + '/bulletGo.png')
						}
					}
					oView.oObject.aEvents[sId] = this.oEvent.value
				} else {
					if (oView.oObject.aEvents[sId]) {
						oActive.oDiv.removeChild(oActive.oA.nextSibling)
					}
					delete oView.oObject.aEvents[sId]
				}
			}
		}
	}
)

Object.extend(
	px.Proto,
	{
		init: function(oParentNode)
		{
			this.oParentNode = oParentNode

			px.html.Element.addClassName(this.oParentNode, 'pxXplorerview')

			/*
			var oLabel = document.createElement('label')
			this.oParentNode.appendChild(oLabel)
			oLabel.appendChild(document.createTextNode('Geerbte Ereignisse'))
		
			this.oInheritedEvent = document.createElement('textarea')
			this.oInheritedEvent.cols = 40
			this.oInheritedEvent.rows = 4
			this.oParentNode.appendChild(this.oInheritedEvent)
			this.oInheritedEvent.disabled = true
			*/

			this.oEventLabel = document.createElement('label')
			var oLabel = this.oEventLabel
			this.oParentNode.appendChild(oLabel)
			oLabel.appendChild(document.createTextNode(oTranslation['aEvents']))
			oLabel.onclick = px.lang.Function.bindEvent(px.ui.xplorerview.Propertyview._eventLabelClick, this)
		
			this.oEvent = document.createElement('textarea')
			this.oEvent.cols = 40
			this.oEvent.rows = 9
			this.oParentNode.appendChild(this.oEvent)
			this.oEvent.onchange = px.lang.Function.bind(px.ui.xplorerview.Propertyview._eventCodeChange, this)
		
			/*
			this.oParameterLabel = document.createElement('label')
			this.oParentNode.appendChild(this.oParameterLabel)
			this.oParameterLabel.appendChild(document.createTextNode(oTranslation['aParameters']))
			*/
		},
		
		callAction: function(sAction) {
			switch (sAction) {
				case 'email':
					this.oEvent.value = '<action id="sendMail">' + "\n" +
						'  <param id="from"></param>' + "\n" +
						'  <param id="to"></param>' + "\n" +
						'  <param id="cc"></param>' + "\n" +
						'  <param id="bcc"></param>' + "\n" +
						'  <param id="subject"></param>' + "\n" +
						'  <param id="text"></param>' + "\n" +
						'</action>'
					break
				case 'action':
					this.oEvent.value = '<action id="">' + "\n" +
						'  <param id=""></param>' + "\n" +
						'</action>'
					break
			}
		},

		resize: function(bFinished)
		{
			this.oEvent.style.width = this.oParentNode.offsetWidth - 30 + 'px'
			this.oEvent.style.height = this.oParentNode.offsetHeight - 30 + 'px'
		}	}
)