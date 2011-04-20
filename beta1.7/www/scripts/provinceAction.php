<?php
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

/* showProvince.php	 - test script for login
 *
 *  Displays province info. according to template blank.html
 */
require_once ("all.inc.php"); 
require_once ("User.class.inc.php");
require_once ("Province.class.inc.php");
require_once ("isLoggedOn.inc.php");

// intro
$victim = new Province ($_REQUEST['victim'], $database);
$victim->getProvinceData();
$html = "<center><table width=700><tr><td>" . $province->getAdvisorName() . "
, you have asked for the attention of your advisors dealing with <b>".$victim->provinceName."(#$victim->kiId)</b>.  What do you want to do?

</td></tr>
<tr><td>I want to write a message!
<form action=\"message.php\" method=GET>
<input type=hidden name=kiID value=$victim->kiId>
<input type=hidden name=pID value=$victim->pID>
<input type=submit name=sendMessage value=\"Go to writing room\" class='form'>
</form>
</td></tr>
<tr><td>I want to consult my thieves!
<form action=\"thievery.php\" method=POST>
<input type=hidden name=selectProvince value=$victim->pID>
<input type=hidden name=kingdom value=$victim->kiId>
<input type=submit value=\"Contact advisor!\" class='form'>
</form>
</td></tr>
<tr><td>I want to consult my Wizards!
<form action=\"magic.php\" method=GET>
<input type=hidden name=targetpID value=$victim->pID>
<input type=hidden name=kiID value=$victim->kiId>
<input type=submit value=\"Contact advisor!\" class='form'>
</form>
</td></tr>

<tr><td>I want to consult my Military Advisor!
<form action=\"Attack.php\" method=POST>
<input type=hidden name=selectedProvince value=$victim->pID>
<input type=hidden name=selectedKingdom value=$victim->kiId>
<input type=submit name=\"kingdomOK\" value=\"Contact advisor!\" class='form'>
</form>
</td></tr>

<tr><td><a href='report.php?kingdomId=$victim->kiId' class='rep'> -- Show Kingdoms --</a>
&nbsp;&nbsp;
<a href='top50.php' class='rep'> -- Top 50 --</a>
</td></tr></table></center>";

templateDisplay($province,$html);

$database->shutdown();
?>