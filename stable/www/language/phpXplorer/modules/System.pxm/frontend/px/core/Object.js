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
px.Class.define('px.core.Object',
{
	extend : Object,
	destruct: function()
	{
//		alert("destruct object")
	}
})

Object.extend(
	px.Statics,
	{
		dispose: function() {
			for (var i in this) {
				switch (typeof this[i]) {
					case undefined:
					case 'object':
					case 'function':
						delete this[i]
					break;
				}
			}
		}
	}
)

Object.extend(
	px.Proto,
	{
		base: function(args)
		{
		  if (arguments.length === 1) {
		    return args.callee.base.call(this)
		  } else {
		    return args.callee.base.apply(this, Array.prototype.slice.call(arguments, 1))
		  }
		},
		
		dispose: function()
		{
      if (this.__bDisposed) {
      	alert(this.classname +  ' already disposed')
        return
      }

      this.__bDisposed = true
      
      pxp.log('Dispose ' + this.classname)

			var clazz = this.constructor

			while (clazz.superclass) {
				if (clazz.$$destructor) {
					clazz.$$destructor.call(this);
				}
				clazz = clazz.superclass;
			}

			if (px.dev) {
				px.dev.MemoryLeak.check(this)
			}
		},

		_disposeFields: function()
		{
			for (var i=0, l=arguments.length; i<l; i++)
			{
				var sName = arguments[i]				
				if (this[sName] == null) {
					continue
				}

				if (!this.hasOwnProperty(sName)) {
					alert(this.classname + " has no own field " + name)
					continue
				}

				this[sName] = null
      }
    },

		_disposeObjects: function()
		{
			for (var i=0, l=arguments.length; i<l; i++)
			{
				var sName = arguments[i]
				if (this[sName] == null) {
					continue
				}

				if (!this.hasOwnProperty(sName)) {
					alert(this.classname + " has no own field " + name)
					continue
				}

				this[sName].dispose()
				this[sName] = null
  		}
		},

		_disposeContents: function()
		{
			for (var i=0, l=arguments.length; i<l; i++)
			{
				var sName = arguments[i]
				if (this[sName] == null) {
					continue
				}

				if (!this.hasOwnProperty(sName)) {
					alert(this.classname + " has no own field " + name)
					continue
				}

				if (this[sName] instanceof Array) {
					for (var i2=0, l2=this[sName].length; i2<l2; i2++) {
						this[sName][i2].dispose()
						this[sName][i2] = null
					}
				} else if (this[sName] instanceof Object) {
					for (var sKey in this[sName]) {
						this[sName][sKey].dispose()
						this[sName][sKey] = null
					}
				}

				this[sName] = null
  		}
		}
	}
)