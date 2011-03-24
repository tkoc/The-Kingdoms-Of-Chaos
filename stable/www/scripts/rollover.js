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
// JavaScript Document

news_up = new Image; news_up.src = "img/news.gif";
news_down = new Image; news_down.src = "img/news_over.gif";

guide_up = new Image; guide_up.src = "img/guide.gif";
guide_down = new Image; guide_down.src = "img/guide_over.gif";

forum_up = new Image; forum_up.src = "img/forum.gif";
forum_down = new Image; forum_down.src = "img/forum_over.gif";

register_up = new Image; register_up.src = "img/register.gif";
register_down = new Image; register_down.src = "img/register_over.gif";

login_up = new Image; login_up.src = "img/login.gif";
login_down = new Image; login_down.src = "img/login_over.gif";


// ingame menu
logo_up = new Image; logo_up.src = "img/logo.gif";
logo_down = new Image; logo_down.src = "img/logo_over.gif";

home_up = new Image; home_up.src = "img/home.gif";
home_down = new Image; home_down.src = "img/home_over.gif";

forum_p_up = new Image; forum_p_up.src = "img/forum_p.gif";
forum_p_down = new Image; forum_p_down.src = "img/forum_p_over.gif";

science_up = new Image; science_up.src = "img/knowledge.gif";
science_down = new Image; science_down.src = "img/knowledge_over.gif";

buildings_up = new Image; buildings_up.src = "img/buildings.gif";
buildings_down = new Image; buildings_down.src = "img/buildings_over.gif";

thievery_up = new Image; thievery_up.src = "img/thievery.gif";
thievery_down = new Image; thievery_down.src = "img/thievery_over.gif";

magic_up = new Image; magic_up.src = "img/magic.gif";
magic_down = new Image; magic_down.src = "img/magic_over.gif";

military_up = new Image; military_up.src = "img/military.gif";
military_down = new Image; military_down.src = "img/military_over.gif";

attack_up = new Image; attack_up.src = "img/attack.gif";
attack_down = new Image; attack_down.src = "img/attack_over.gif";

defence_up = new Image; defence_up.src = "img/defence.gif";
defence_down = new Image; defence_down.src = "img/defence_over.gif";

newsg_up = new Image; newsg_up.src = "img/newsg.gif";
newsg_down = new Image; newsg_down.src = "img/newsg_over.gif";

council_up = new Image; council_up.src = "img/council.gif";
council_down = new Image; council_down.src = "img/council_over.gif";

statistics_up = new Image; statistics_up.src = "img/statistics.gif";
statistics_down = new Image; statistics_down.src = "img/statistics_over.gif";

explore_up = new Image; explore_up.src = "img/explore.gif";
explore_down = new Image; explore_down.src = "img/explore_over.gif";

politics_up = new Image; politics_up.src = "img/politics.gif";
politics_down = new Image; politics_down.src = "img/politics_over.gif";

messages_up = new Image; messages_up.src = "img/messages.gif";
messages_down = new Image; messages_down.src = "img/messages_over.gif";

trade_up = new Image; trade_up.src = "img/trade.gif";
trade_down = new Image; trade_down.src = "img/trade_over.gif";

guideg_up = new Image; guideg_up.src = "img/guideg.gif";
guideg_down = new Image; guideg_down.src = "img/guideg_over.gif";

tut_up = new Image; tut_up.src = "img/tutorial.gif";
tut_down = new Image; tut_down.src = "img/tutorial_over.gif";

pref_up = new Image; pref_up.src = "img/pref.gif";
pref_down = new Image; pref_down.src = "img/pref_over.gif";

logout_up = new Image; logout_up.src = "img/logout.gif";
logout_down = new Image; logout_down.src = "img/logout_over.gif";

textmenu_up = new Image; textmenu_up.src = "img/textmenu.gif";
textmenu_down = new Image; textmenu_down.src = "img/textmenu_over.gif";







function MouseOverRoutine(ButtonName)
{
if (ButtonName=="news")
{document.news.src = news_down.src;}
if (ButtonName=="guide") 
{document.guide.src = guide_down.src;}
if (ButtonName=="forum") 
{document.forum.src = forum_down.src;}
if (ButtonName=="register") 
{document.register.src = register_down.src;}
if (ButtonName=="login") 
{document.login.src = login_down.src;}
//ingame menu
if (ButtonName=="logo") 
{document.logo.src = logo_down.src;}
if (ButtonName=="home") 
{document.home.src = home_down.src;}
if (ButtonName=="forum_p") 
{document.forum_p.src = forum_p_down.src;}
if (ButtonName=="science") 
{document.science.src = science_down.src;}
if (ButtonName=="buildings") 
{document.buildings.src = buildings_down.src;}
if (ButtonName=="thievery") 
{document.thievery.src = thievery_down.src;}
if (ButtonName=="magic") 
{document.magic.src = magic_down.src;}
if (ButtonName=="military") 
{document.military.src = military_down.src;}
if (ButtonName=="attack") 
{document.attack.src = attack_down.src;}
if (ButtonName=="defence") 
{document.defence.src = defence_down.src;}
if (ButtonName=="newsg") 
{document.newsg.src = newsg_down.src;}

if (ButtonName=="council") 
{document.council.src = council_down.src;}
if (ButtonName=="statistics") 
{document.statistics.src = statistics_down.src;}
if (ButtonName=="explore") 
{document.explore.src = explore_down.src;}
if (ButtonName=="politics") 
{document.politics.src = politics_down.src;}


if (ButtonName=="messages") 
{document.messages.src = messages_down.src;}
if (ButtonName=="trade") 
{document.trade.src = trade_down.src;}
if (ButtonName=="guideg") 
{document.guideg.src = guideg_down.src;}
if (ButtonName=="tut") 
{document.tut.src = tut_down.src;}
if (ButtonName=="pref") 
{document.pref.src = pref_down.src;}


if (ButtonName=="logout") 
{document.logout.src = logout_down.src;}

if (ButtonName=="textmenu") 
{document.textmenu.src = textmenu_down.src;}
}

function MouseOutRoutine(ButtonName)
{
if (ButtonName=="news") 
{document.news.src = news_up.src;}
if (ButtonName=="guide") 
{document.guide.src = guide_up.src;}
if (ButtonName=="forum") 
{document.forum.src = forum_up.src;}
if (ButtonName=="register") 
{document.register.src = register_up.src;}
if (ButtonName=="login") 
{document.login.src = login_up.src;}
// ingame menu
if (ButtonName=="logo") 
{document.logo.src = logo_up.src;}
if (ButtonName=="home") 
{document.home.src = home_up.src;}
if (ButtonName=="forum_p") 
{document.forum_p.src = forum_p_up.src;}
if (ButtonName=="science") 
{document.science.src = science_up.src;}
if (ButtonName=="buildings") 
{document.buildings.src = buildings_up.src;}
if (ButtonName=="thievery") 
{document.thievery.src = thievery_up.src;}
if (ButtonName=="magic") 
{document.magic.src = magic_up.src;}
if (ButtonName=="military") 
{document.military.src = military_up.src;}
if (ButtonName=="attack") 
{document.attack.src = attack_up.src;}
if (ButtonName=="defence") 
{document.defence.src = defence_up.src;}

if (ButtonName=="newsg") 
{document.newsg.src = newsg_up.src;}

if (ButtonName=="council") 
{document.council.src = council_up.src;}
if (ButtonName=="statistics") 
{document.statistics.src = statistics_up.src;}
if (ButtonName=="explore") 
{document.explore.src = explore_up.src;}
if (ButtonName=="politics") 
{document.politics.src = politics_up.src;}

if (ButtonName=="messages") 
{document.messages.src = messages_up.src;}

if (ButtonName=="trade") 
{document.trade.src = trade_up.src;}
if (ButtonName=="guideg") 
{document.guideg.src = guideg_up.src;}
if (ButtonName=="tut") 
{document.tut.src = tut_up.src;}

if (ButtonName=="pref") 
{document.pref.src = pref_up.src;}

if (ButtonName=="logout") 
{document.logout.src = logout_up.src;}

if (ButtonName=="textmenu") 
{document.textmenu.src = textmenu_up.src;}
}
