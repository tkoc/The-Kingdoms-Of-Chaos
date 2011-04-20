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
// Guide Rollover

// clock 12. rules
rules_up = new Image;	 	rules_up.src 		= "../img/guide/rulesregister_off.jpg";
rules_down = new Image;  	rules_down.src 		= "../img/guide/rulesregister.jpg";
rules_middle = new Image; 	rules_middle.src	= "../img/guide/center.jpg";

//clock 1. basics
basics_up = new Image;	 	basics_up.src 		= "../img/guide/basics_off.jpg";
basics_down = new Image;  	basics_down.src 	= "../img/guide/basics.jpg";
basics_middle = new Image; 	basics_middle.src	= "../img/guide/basics_middle.jpg";

//clock 2. races
races_up = new Image;	 	races_up.src 		= "../img/guide/races_off.jpg";
races_down = new Image;  	races_down.src  	= "../img/guide/races.jpg";
races_middle = new Image; 	races_middle.src	= "../img/guide/races_middle.jpg";

// clock 3. council
council_up = new Image;		council_up.src 		= "../img/guide/council_off.jpg";
council_down = new Image;  	council_down.src  	= "../img/guide/council.jpg";
council_middle = new Image; council_middle.src	= "../img/guide/council_middle.jpg";

// clock 4. knowledge
knowledge_up = new Image;	  knowledge_up.src 		= "../img/guide/knowledge_off.jpg";
knowledge_down = new Image;   knowledge_down.src  	= "../img/guide/knowledge.jpg";
knowledge_middle = new Image; knowledge_middle.src	= "../img/guide/knowledge_middle.jpg";

// clock 5. thievery
thievery_up = new Image;	  thievery_up.src 		= "../img/guide/thievery_off.jpg";
thievery_down = new Image;   thievery_down.src  	= "../img/guide/thievery.jpg";
thievery_middle = new Image; thievery_middle.src	= "../img/guide/thievery_middle.jpg";

// clock 6. magic
magic_up = new Image;	  magic_up.src 		= "../img/guide/magic_off.jpg";
magic_down = new Image;   magic_down.src  	= "../img/guide/magic.jpg";
magic_middle = new Image; magic_middle.src	= "../img/guide/magic_middle.jpg";

// clock 7. milwar
milwar_up = new Image;	  milwar_up.src 		= "../img/guide/milwar_off.jpg";
milwar_down = new Image;   milwar_down.src  	= "../img/guide/milwar.jpg";
milwar_middle = new Image; milwar_middle.src	= "../img/guide/milwar_middle.jpg";

// clock 8. buildings
buildings_up = new Image;	  buildings_up.src 		= "../img/guide/buildings_off.jpg";
buildings_down = new Image;   buildings_down.src  	= "../img/guide/buildings.jpg";
buildings_middle = new Image; buildings_middle.src	= "../img/guide/buildings_middle.jpg";

// clock 9. communication
communication_up = new Image;	  communication_up.src 		= "../img/guide/communication_off.jpg";
communication_down = new Image;   communication_down.src  	= "../img/guide/communication.jpg";
communication_middle = new Image; communication_middle.src	= "../img/guide/center.jpg";

function MouseOverRoutine(ButtonName)
{
	if (ButtonName=="rulesregister")
	{
		document.rulesregister.src = rules_down.src;
		document.middle.src = rules_middle.src;
	}

	if (ButtonName=="basics")
	{
		document.basics.src = basics_down.src;
		document.middle.src = basics_middle.src;
	}


	if (ButtonName=="races")
	{
		document.races.src = races_down.src;
		document.middle.src = races_middle.src;
	}
	
	if (ButtonName=="council")
	{
		document.council.src = council_down.src;
		document.middle.src = council_middle.src;
	}
	
	if (ButtonName=="knowledge")
	{
		document.knowledge.src = knowledge_down.src;
		document.middle.src = knowledge_middle.src;
	}
	
	if (ButtonName=="thievery")
	{
		document.thievery.src = thievery_down.src;
		document.middle.src = thievery_middle.src;
	}



if (ButtonName=="magic")

{

	document.magic.src = magic_down.src;

	document.middle.src = magic_middle.src;

}



if (ButtonName=="milwar")

{

	document.milwar.src = milwar_down.src;

	document.middle.src = milwar_middle.src;

}



if (ButtonName=="buildings")

{

	document.buildings.src = buildings_down.src;

	document.middle.src = buildings_middle.src;

}



if (ButtonName=="communication")

{

	document.communication.src = communication_down.src;

	document.middle.src = communication_middle.src;

}





}



function MouseOutRoutine(ButtonName)

{

if (ButtonName=="rulesregister")

{

	document.rulesregister.src = rules_up.src;

}



if (ButtonName=="basics")

{

	document.basics.src = basics_up.src;

}



if (ButtonName=="races")

{

	document.races.src = races_up.src;

}



if (ButtonName=="council")

{

	document.council.src = council_up.src;

}



if (ButtonName=="knowledge")

{

	document.knowledge.src = knowledge_up.src;

}



if (ButtonName=="thievery")

{

	document.thievery.src = thievery_up.src;

}



if (ButtonName=="magic")

{

	document.magic.src = magic_up.src;

}



if (ButtonName=="milwar")

{

	document.milwar.src = milwar_up.src;

}



if (ButtonName=="buildings")

{

	document.buildings.src = buildings_up.src;

}



if (ButtonName=="communication")

{

	document.communication.src = communication_up.src;

}



// always reset to middle image

document.middle.src= "../img/guide/center.jpg";

}

