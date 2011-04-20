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
?>
<!-- Top Stats -->
<?

$result = mysql_query("SELECT * FROM kingdoms");
$kingcount = mysql_num_rows($result);
mysql_free_result($result);

$result = mysql_query("SELECT * FROM provinces");
$provcount = mysql_num_rows($result);
mysql_free_result($result);

$result = mysql_query("SELECT * FROM statistics");
$statcount = mysql_num_rows($result);
mysql_free_result($result);

$result = mysql_query("SELECT * FROM diverse where thekey='sistetick'");
$sistetick = mysql_fetch_array($result);
mysql_free_result($result);

$result = mysql_query("SELECT tick FROM statistics ORDER BY tick DESC LIMIT 1");
$tid = mysql_fetch_array($result);
mysql_free_result($result);

$tkocdate = $tid[0]/24;
$tkocdatemnd = floor($tkocdate);
$tkocdatear = $tkocdatemnd/12;
$tkocdatear = floor($tkocdatear);
$tkocdatedag = ($tid[0] - ($tkocdatemnd*24))+1;
$fraforrige = date("i",time()-(5*60));
$tkocdatemnd = $tkocdatemnd - ($tkocdatear*12)+1;
$tilneste = 60-$fraforrige;
$tkocdatear = $tkocdatear+1;

?>
<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
  <TR>
    <TD vAlign=bottom align=left>
      <TABLE cellSpacing=0 cellPadding=0 align="right" border=0>
        <TR>
          <TD align=left>Time last:
          </TD>
          <TD align=right><?=$sistetick[1]?>
          </TD>
          <TD width=10><IMG height=1 alt=Spacer src="blank.gif" width=10>
          </TD>
          <TD align=left>TKOC Date:
          </TD>
          <TD align=right>Day <?=$tkocdatedag?>, Month <?=$tkocdatemnd?>, Year <?=$tkocdatear?>
          </TD>
        </TR>
        <TR>
          <TD align=left>Tick:
          </TD>
          <TD align=right><?=$tid[0]?>
          </TD>
          <TD width=10><IMG height=1 alt=Spacer src="blank.gif" width=10>
          </TD>
          <TD align=left>Provinces:
          </TD>
          <TD align=right><?=$provcount?>
          </TD>
        </TR>
        <TR>
          <TD align=right colSpan=2>(~<?=$fraforrige?> minutes ago)
          </TD>
          <TD idth=10><IMG height=1 alt=Spacer src="blank.gif" width=10>
          </TD>
          <TD align=left>Kingdoms:
          </TD>
          <TD align=right><?=$kingcount?>
          </TD>
        </TR>
        <TR>
          <TD align=left>Next:
          </TD>
          <TD align=right>~<?=$tilneste?> minutes
          </TD>
          <TD width=10><IMG height=1 alt=Spacer src="blank.gif" width=10>
          </TD>
          <TD align=left>DB Rows:
          </TD>
          <TD align=right><?=$statcount?>
          </TD>
        </TR>
        <TR>
          <TD align=left>
          </TD>
          <TD align=right>
          </TD>
          <TD width=10><IMG height=1 alt=Spacer src="blank.gif" width=10>
          </TD>
          <TD align=left>
          </TD>
          <TD align=right>
          </TD>
        </TR>
        <TR>
          <TD align=left>
          </TD>
          <TD align=right>
          </TD>
          <TD width=10><IMG height=1 alt=Spacer src="blank.gif" width=10>
          </TD>
          <TD  align=left>
          </TD>
          <TD  align=right>
          </TD>
        </TR>
      </TABLE>
    </TD>
  </TR>
</TABLE>
<!-- End Top Stats -->
