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
<?
error_reporting (E_NONE);
session_start();
require_once("private/konf.nohack.php");

?>
<HTML>
<HEAD>
<SCRIPT language=JavaScript type=text/javascript>
	<!--

	function LmOver(elem, clr)
	{
		elem.className = clr;
		elem.style.cursor = 'hand';
	}

	function LmOut(elem, clr)
	{
		elem.className = clr;
	}

	function LmDown(elem, clr)
	{
		elem.className = clr;
	}

	function LmUp(path)
	{
		location.href = path;
	}

	//-->
</SCRIPT>
<TITLE>The Kingdom of Chaos statistics</TITLE>
<LINK href="style.css" type=text/css rel=stylesheet>
</HEAD>
<BODY>

<?
include_once('topstat.php.inc');
?>

<BR><BR>

<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
  <TR>
    <TD vAlign=top>

<?
include_once('meny.php.inc');
?>
    </TD>
    <TD vAlign=top width="100%">
      <DIV align=center>
      <TABLE width="85%">
        <TR>
          <TD>
            <TABLE style="MARGIN-BOTTOM: 5px" cellSpacing=0 cellPadding=0 width="100%" border=0>
              <TR>
                <TD width="50%">
                  <TABLE style="HEIGHT: 20px" cellSpacing=1 cellPadding=2 width="100%" align=left border=0>
                    <TR>
                      <TD class=listingheader style="TEXT-ALIGN: center" width="100%" colSpan=10>
                        <A href="rank.php?action=province&amp;sort=DESC&amp;orderby=score">Top 10 Provinces</A> 
                      </TD>
                    </TR>
<!-- Top 10 Provinces -->
<?
$result = mysql_query("SELECT * FROM statistics,provinces WHERE statistics.tick='$tid[0]' AND statistics.pname=provinces.pname ORDER BY statistics.score DESC LIMIT 10");
$number = 1;
$rows = mysql_num_rows($result);
while ( $data = mysql_fetch_array($result) ) {

	$result2 = mysql_query("SELECT lastp,acres,score FROM statistics WHERE pname='$data[0]' ORDER BY tick DESC LIMIT 1,1");
	$data2 = mysql_fetch_array($result2);
	mysql_free_result($result2);
	
	echo "
                    <TR>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=35>
                        $number
        ";
        
        if ($data[4] == $data2[0]) {
               echo "         
                        <IMG height=9 alt=\"No Change\" src=\"rank_stay.gif\" width=9> 
               ";
         }
         else if ($data[4] < $data2[0]) {
               echo "
                        <IMG height=9 alt=\"Up\" src=\"rank_up.gif\" width=9>
               ";
         }
         else {
               echo " 
                        <IMG height=9 alt=\"Down\" src=\"rank_down.gif\" width=9> 
               ";      
         }
	$result2 = mysql_query("SELECT * FROM provinces WHERE pname='$data[0]'");
	$data3 = mysql_fetch_array($result2);
	mysql_free_result($result2);
	$acresp = 0;
	$scorep = 0;
		if($data2[1]) {
	$acresp = (($data[2] - $data2[1]) / $data2[1]) * 100;
	$acresp = number_format($acresp, 2);
	$acresneg = substr($acresp, 0, 1);
	}
		if($data2[2]) {
	$scorep = (($data[3] - $data2[2]) / $data2[2]) * 100;
	$scorep = number_format($scorep, 2);
	$scoreneg = substr($scorep, 0, 1);
	$ratio = ($data[3]/$data[2]);
	$ratio = number_format( $ratio, 2);
	}
	
         echo "
                      </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: left\" noWrap width=20>
                        <A class=table href=\"rank.php?action=kingdom&amp;kd=$data3[3]\">$data3[3]</A>
                      </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: left\" noWrap width=\"99%\">
                        <A class=table href=\"rank.php?action=province&amp;prov=$data[5]\">$data3[1]</A>
                      </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=40>
                        $data3[2]
                      </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=40>
                        $data[2]
                      </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=55>
                        $data[3]
                      </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=55>
                        $ratio
                      </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: center\" noWrap width=43>
	";
			if ($acresneg == '-') {
				$acresp = substr($acresp,1);
                        	echo "<FONT title=\"- Acres\" color=#ff0000>$acresp%</FONT>";
			}
			else if ($acresp == '0' || !$acresp) {
				$acresp = 0;
                        	echo "<FONT title=\"+-0 Acres\" color=#ffff00>$acresp%</FONT>";		
			}
			else {
                        	echo "<FONT title=\"+ Acres\" color=#00ff00>$acresp%</FONT>";
			}
			echo "
                      </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: center\" noWrap width=43>
                      ";
			if ($scoreneg == '-') {       
				$scorep = substr($scorep,1);               
 	                       echo "<FONT title=\"- Score\" color=#ff0000>$scorep%</FONT>";
                        }
                        else if ($scorep == '0' || !$scorep) {
                        	$scorep = 0;
 	                       echo "<FONT title=\"+-0 Score\" color=#ffff00>$scorep%</FONT>";
                        
                        }
                        else {
 	                       echo "<FONT title=\"+ Score\" color=#00ff00>$scorep%</FONT>";
                        
                        }
	echo "
                      </TD>
                    </TR>

	";
	$number++;
	
}

mysql_free_result($result);
if($rows < 10) {
	$rows = 10 - $rows;
	for($i=1; $i <= $rows;$i++) {
	echo "
                    <TR>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=35>
                      <IMG height=1 alt=Spacer src=\"blank.gif\" width=10>
                      </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: left\" noWrap width=20>
                      <IMG height=1 alt=Spacer src=\"blank.gif\" width=10>
                      </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: left\" noWrap width=\"99%\">
                      <IMG height=1 alt=Spacer src=\"blank.gif\" width=10>
                      </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=40>
                      <IMG height=1 alt=Spacer src=\"blank.gif\" width=10>
                      </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=55>
                      <IMG height=1 alt=Spacer src=\"blank.gif\" width=10>
                      </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: center\" noWrap width=43>
                      <IMG height=1 alt=Spacer src=\"blank.gif\" width=10>
                     </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: center\" noWrap width=43>
                      <IMG height=1 alt=Spacer src=\"blank.gif\" width=10>
                      </TD>
                    </TR>	
        ";		
	}
}
?>
<!-- End Top 10 Provinces -->                      

                  </TABLE>
                </TD>
                <TD width="50%">
                  <TABLE style="HEIGHT: 20px" cellSpacing=1 cellPadding=2 width="100%" align=right border=0>
                    <TR>
                      <TD class=listingheader style="TEXT-ALIGN: center" width="100%" colSpan=9>
                        <A href="rank.php?action=kingdom&amp;sort=DESC&amp;orderby=score">Top 10 Kingdoms</A>
                      </TD>
                    </TR>
<!-- Top 10 Kingdoms -->
<?

$result = mysql_query("SELECT * FROM kingdomstat WHERE time='$tid[0]' ORDER BY score DESC LIMIT 10");
$number = 1;
$rows = mysql_num_rows($result);
while ( $data = mysql_fetch_array($result) ) {

	$result2 = mysql_query("SELECT lastp,acres,score FROM kingdomstat WHERE kingdomId='$data[0]' ORDER BY time DESC LIMIT 1,1");
	$data2 = mysql_fetch_array($result2);
	mysql_free_result($result2);
	
	echo "
                    <TR>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=35>
                        $number
        ";
        
        if ($data[4] == $data2[0]) {
               echo "         
                        <IMG height=9 alt=\"No Change\" src=\"rank_stay.gif\" width=9> 
               ";
         }
         else if ($data[4] < $data2[0]) {
               echo "
                        <IMG height=9 alt=\"Up\" src=\"rank_up.gif\" width=9>
               ";
         }
         else {
               echo " 
                        <IMG height=9 alt=\"Down\" src=\"rank_down.gif\" width=9> 
               ";      
         }
	$result2 = mysql_query("SELECT * FROM kingdoms WHERE kingdomId='$data[0]'");
	$data3 = mysql_fetch_array($result2);
	mysql_free_result($result2);
	$acresp = 0;
	$scorep = 0;
	if($data2[1]) {
	$acresp = (($data[2] - $data2[1]) / $data2[1]) * 100;
	$acresp = number_format($acresp, 2);
	$acresneg = substr($acresp, 0, 1);
	}
	if($data2[2]) {
	$scorep = (($data[3] - $data2[2]) / $data2[2]) * 100;
	$scorep = number_format($scorep, 2);
	$scoreneg = substr($scorep, 0, 1);
	$ratio = ($data[3]/$data[2]);
	$ratio = number_format( $ratio, 2);
	}
	
         echo "
                      </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: left\" noWrap width=20>
                        <A class=table href=\"rank.php?action=kingdom&amp;kd=$data[0]\">$data[0]</A>
                      </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: left\" noWrap width=\"30%\">
                        $data3[1]
                      </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=20>
                        $data3[2]
                      </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=40>
                        $data[2]
                      </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=55>
                        $data[3]
                      </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=55>
                        $ratio
                      </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: center\" noWrap width=43>
	";
			if ($acresneg == '-') {
				$acresp = substr($acresp,1);
                        	echo "<FONT title=\"- Acres\" color=#ff0000>$acresp%</FONT>";
			}
			else if ($acresp == '0' || !$acresp) {
				$acresp = 0;
                        	echo "<FONT title=\"+-0 Acres\" color=#ffff00>$acresp%</FONT>";		
			}
			else {
                        	echo "<FONT title=\"+ Acres\" color=#00ff00>$acresp%</FONT>";
			}
			echo "
                      </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: center\" noWrap width=43>
                      ";
			if ($scoreneg == '-') {
				$scorep = substr($scorep,1);                   
 	                       echo "<FONT title=\"- Score\" color=#ff0000>$scorep%</FONT>";
                        }
                        else if ($scorep == '0' || !$scorep) {
                        	$scorep = 0;
 	                       echo "<FONT title=\"+-0 Score\" color=#ffff00>$scorep%</FONT>";
                        
                        }
                        else {
 	                       echo "<FONT title=\"+ Score\" color=#00ff00>$scorep%</FONT>";
                        
                        }
	echo "
                      </TD>
                    </TR>

	";
	$number++;
	
}

mysql_free_result($result);

if($rows < 10) {
	$rows = 10 - $rows;
	for($i=1; $i <= $rows;$i++) {
	echo "
                    <TR>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=35>
                      <IMG height=1 alt=Spacer src=\"blank.gif\" width=10>
                      </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: left\" noWrap width=20>
                      <IMG height=1 alt=Spacer src=\"blank.gif\" width=10>
                      </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: left\" noWrap width=\"99%\">
                      <IMG height=1 alt=Spacer src=\"blank.gif\" width=10>
                      </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=40>
                      <IMG height=1 alt=Spacer src=\"blank.gif\" width=10>
                      </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: right\" noWrap width=55>
                      <IMG height=1 alt=Spacer src=\"blank.gif\" width=10>
                      </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: center\" noWrap width=43>
                      <IMG height=1 alt=Spacer src=\"blank.gif\" width=10>
                     </TD>
                      <TD class=two style=\"WHITE-SPACE: nowrap; TEXT-ALIGN: center\" noWrap width=43>
                      <IMG height=1 alt=Spacer src=\"blank.gif\" width=10>
                      </TD>
                    </TR>	
        ";		
	}
}

?>
<!-- End Top 10 Kingdoms -->                     
                  </TABLE>
                </TD>
              </TR>
            </TABLE>
            
            
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 5px;">
<tr>
	<td width="50%">
								<table width="100%" cellpadding="2" cellspacing="1" border="0" align="left" style="height: 20px;">
	<tr>					<td class="listingheader" style="text-align: center" width="100%" colspan="7">
							Top 5 Growing provinces
						</td>
				</tr>
<?
$oldscore[] = 0;
$newscore[] = 0;
$growscore[] = 0;
$result = mysql_query("SELECT pname,score FROM statistics WHERE tick='$tid[0]'");
$antall = mysql_num_rows($result);
while ( $data3 = mysql_fetch_array($result) ) {
	$newscore[$data3[0]] = $data3[1];
}
mysql_free_result($result);

$result = mysql_query("SELECT tick FROM statistics ORDER BY tick DESC LIMIT $antall,1");
$data = mysql_fetch_array($result);
mysql_free_result($result);

$result = mysql_query("SELECT pname,score FROM statistics WHERE tick='$data[0]'");

while ( $data2 = mysql_fetch_array($result) ) {
	$oldscore[$data2[0]] = $data2[1];
}
mysql_free_result($result);
foreach ($oldscore as $key => $value) {
	if ($oldscore[$key] && $newscore[$key]) {
	$growscore[$key] = (($newscore[$key]-$oldscore[$key])/$oldscore[$key]) * 100;
	$growscore[$key] = number_format($growscore[$key], 2);
	} 
	else {
	$growscore[$key] = 0;
	}
}
arsort($growscore);
$whilern = 1;
$numm = 0;
for ($i=1;$i<6;$i++) {
list ($key, $val) = each ($growscore);
$result = mysql_query("SELECT * FROM provinces WHERE pname='$key'");
$data4 = mysql_fetch_array($result);
mysql_free_result($result);
$result = mysql_query("SELECT score,acres FROM statistics WHERE pname='$key' AND tick='$tid[0]'");
$data5 = mysql_fetch_array($result);
mysql_free_result($result);
$scoreneg = substr($val, 0, 1);
?>	
			<tr>
						<td class="two" style="text-align: right;white-space:nowrap;" width="28" nowrap="nowrap"><?=$i?></td>
                				<td class="two" style="text-align: right;white-space:nowrap;" width="20" nowrap="nowrap"><a href="rank.php?action=kingdom&amp;kd=<?=$data4[3]?>" class="table"><?=$data4[3]?></a></td>
                				<td class="two" style="text-align: left;white-space:nowrap;" width="99%" nowrap="nowrap"><a href="rank.php?action=province&amp;prov=<?=$data4[0]?>"><?=$data4[1]?></a></td>
                				<td class="two" style="text-align: right;white-space:nowrap;" width="40" nowrap="nowrap"><?=$data4[2]?></td>
                				<td class="two" style="text-align: right;white-space:nowrap;" width="40" nowrap="nowrap"><?=$data5[1]?></td>
                				<td class="two" style="text-align: right;white-space:nowrap;" width="55" nowrap="nowrap"><?=$data5[0]?></td>
                				<td class="two" style="text-align: center;white-space:nowrap;" width="43" nowrap="nowrap">
<?
			if ($scoreneg == '-') {
				$val = substr($val,1);
                        	echo "<FONT title=\"- Score\" color=#ff0000>$val%</FONT>";
			}
			else if ($val == '0' || !$val) {
				$val = 0;
                        	echo "<FONT title=\"+-0 Score\" color=#ffff00>$val%</FONT>";		
			}
			else {
                        	echo "<FONT title=\"+ Score\" color=#00ff00>$val%</FONT>";
			}
			?>
                				</td>

                			</tr>
<?
}


?>
				</table>
			</td>
	<td width="50%">
				<table width="100%" cellpadding="2" cellspacing="1" border="0" align="right" style="height: 20px;">
	<tr>					<td class="listingheader" style="text-align: center" width="100%" colspan="7">
							Top 5 Losing Provinces					</td>
				</tr>
<?
asort($growscore);
$whilern = 1;
$numm = 0;
for ($i=1;$i<6;$i++) {
list ($key, $val) = each ($growscore);
$result = mysql_query("SELECT * FROM provinces WHERE pname='$key'");
$data4 = mysql_fetch_array($result);
mysql_free_result($result);
$result = mysql_query("SELECT score,acres FROM statistics WHERE pname='$key' AND tick='$tid[0]'");
$data5 = mysql_fetch_array($result);
mysql_free_result($result);
$scoreneg = substr($val, 0, 1);
?>	
			<tr>
						<td class="two" style="text-align: right;white-space:nowrap;" width="28" nowrap="nowrap"><?=$i?></td>
                				<td class="two" style="text-align: right;white-space:nowrap;" width="20" nowrap="nowrap"><a href="rank.php?action=kingdom&amp;kd=<?=$data4[3]?>" class="table"><?=$data4[3]?></a></td>
                				<td class="two" style="text-align: left;white-space:nowrap;" width="99%" nowrap="nowrap"><a href="rank.php?action=province&amp;prov=<?=$data4[0]?>"><?=$data4[1]?></a></td>
                				<td class="two" style="text-align: right;white-space:nowrap;" width="40" nowrap="nowrap"><?=$data4[2]?></td>
                				<td class="two" style="text-align: right;white-space:nowrap;" width="40" nowrap="nowrap"><?=$data5[1]?></td>
                				<td class="two" style="text-align: right;white-space:nowrap;" width="55" nowrap="nowrap"><?=$data5[0]?></td>
                				<td class="two" style="text-align: center;white-space:nowrap;" width="43" nowrap="nowrap">
<?
			if ($scoreneg == '-') {
				$val = substr($val,1);
                        	echo "<FONT title=\"- Score\" color=#ff0000>$val%</FONT>";
			}
			else if ($val == '0' || !$val) {
				$val = 0;
                        	echo "<FONT title=\"+-0 Score\" color=#ffff00>$val%</FONT>";		
			}
			else {
                        	echo "<FONT title=\"+ Score\" color=#00ff00>$val%</FONT>";
			}
			?>
                				</td>

                			</tr>
<?
}
?>
				</table>
			</td>
</tr>
</table>

<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 5px;">
<tr>
	<td width="50%">
				<table width="100%" cellpadding="2" cellspacing="1" border="0" align="left" style="height: 20px;">
	<tr>					<td class="listingheader" style="text-align: center" width="100%" colspan="7">
							Top 5 Owning Provinces					</td>
				</tr>

<?
$oldacres[] = 0;
$newacres[] = 0;
$growacres[] = 0;
$result = mysql_query("SELECT pname,acres FROM statistics WHERE tick='$tid[0]'");
$antall = mysql_num_rows($result);
while ( $data3 = mysql_fetch_array($result) ) {
	$newacres[$data3[0]] = $data3[1];
}
mysql_free_result($result);

$result = mysql_query("SELECT tick FROM statistics ORDER BY tick DESC LIMIT $antall,1");
$data = mysql_fetch_array($result);
mysql_free_result($result);

$result = mysql_query("SELECT pname,acres FROM statistics WHERE tick='$data[0]'");

while ( $data2 = mysql_fetch_array($result) ) {
	$oldacres[$data2[0]] = $data2[1];
}
mysql_free_result($result);
foreach ($oldacres as $key => $value) {
	if ($oldacres[$key] && $newacres[$key]) {
	$growacres[$key] = (($newacres[$key]-$oldacres[$key])/$oldacres[$key]) * 100;
	$growacres[$key] = number_format($growacres[$key], 2);
	} 
	else if ($oldacres[$key] && !$newacres[$key]) {
		delete_prov($key);
	}
	else {
	$growacres[$key] = 0;
	}
}
arsort($growacres);
$whilern = 1;
$numm = 0;
for ($i=1;$i<6;$i++) {
list ($key, $val) = each ($growacres);
$result = mysql_query("SELECT * FROM provinces WHERE pname='$key'");
$data4 = mysql_fetch_array($result);
mysql_free_result($result);
$result = mysql_query("SELECT score,acres FROM statistics WHERE pname='$key' AND tick='$tid[0]'");
$data5 = mysql_fetch_array($result);
mysql_free_result($result);
$acresneg = substr($val, 0, 1);
?>	
			<tr>
						<td class="two" style="text-align: right;white-space:nowrap;" width="28" nowrap="nowrap"><?=$i?></td>
                				<td class="two" style="text-align: right;white-space:nowrap;" width="20" nowrap="nowrap"><a href="rank.php?action=kingdom&amp;kd=<?=$data4[3]?>" class="table"><?=$data4[3]?></a></td>
                				<td class="two" style="text-align: left;white-space:nowrap;" width="99%" nowrap="nowrap"><a href="rank.php?action=province&amp;prov=<?=$data4[0]?>"><?=$data4[1]?></a></td>
                				<td class="two" style="text-align: right;white-space:nowrap;" width="40" nowrap="nowrap"><?=$data4[2]?></td>
                				<td class="two" style="text-align: right;white-space:nowrap;" width="40" nowrap="nowrap"><?=$data5[1]?></td>
                				<td class="two" style="text-align: right;white-space:nowrap;" width="55" nowrap="nowrap"><?=$data5[0]?></td>
                				<td class="two" style="text-align: center;white-space:nowrap;" width="43" nowrap="nowrap">
<?
			if ($acresneg == '-') {
				$val = substr($val,1);
                        	echo "<FONT title=\"- Acres\" color=#ff0000>$val%</FONT>";
			}
			else if ($val == '0' || !$val) {
				$val = 0;
                        	echo "<FONT title=\"+-0 Acres\" color=#ffff00>$val%</FONT>";		
			}
			else {
                        	echo "<FONT title=\"+ Acres\" color=#00ff00>$val%</FONT>";
			}
			?>
                				</td>

                			</tr>
<?
}
?>

				</table>
			</td>
	<td width="50%">
				<table width="100%" cellpadding="2" cellspacing="1" border="0" align="right" style="height: 20px;">
	<tr>					<td class="listingheader" style="text-align: center" width="100%" colspan="7">
							Top 5 Owned Provinces					</td>
				</tr>

<?
asort($growacres);
$whilern = 1;
$numm = 0;
for ($i=1;$i<6;$i++) {
list ($key, $val) = each ($growacres);
$result = mysql_query("SELECT * FROM provinces WHERE pname='$key'");
$data4 = mysql_fetch_array($result);
mysql_free_result($result);
$result = mysql_query("SELECT score,acres FROM statistics WHERE pname='$key' AND tick='$tid[0]'");
$data5 = mysql_fetch_array($result);
mysql_free_result($result);
$acresneg = substr($val, 0, 1);
?>	
			<tr>
						<td class="two" style="text-align: right;white-space:nowrap;" width="28" nowrap="nowrap"><?=$i?></td>
                				<td class="two" style="text-align: right;white-space:nowrap;" width="20" nowrap="nowrap"><a href="rank.php?action=kingdom&amp;kd=<?=$data4[3]?>" class="table"><?=$data4[3]?></a></td>
                				<td class="two" style="text-align: left;white-space:nowrap;" width="99%" nowrap="nowrap"><a href="rank.php?action=province&amp;prov=<?=$data4[0]?>"><?=$data4[1]?></a></td>
                				<td class="two" style="text-align: right;white-space:nowrap;" width="40" nowrap="nowrap"><?=$data4[2]?></td>
                				<td class="two" style="text-align: right;white-space:nowrap;" width="40" nowrap="nowrap"><?=$data5[1]?></td>
                				<td class="two" style="text-align: right;white-space:nowrap;" width="55" nowrap="nowrap"><?=$data5[0]?></td>
                				<td class="two" style="text-align: center;white-space:nowrap;" width="43" nowrap="nowrap">
<?
			if ($acresneg == '-') {
				$val = substr($val,1);
                        	echo "<FONT title=\"- Acres\" color=#ff0000>$val%</FONT>";
			}
			else if ($val == '0' || !$val) {
				$val = 0;
                        	echo "<FONT title=\"+-0 Acres\" color=#ffff00>$val%</FONT>";		
			}
			else {
                        	echo "<FONT title=\"+ Acres\" color=#00ff00>$val%</FONT>";
			}
			?>
                				</td>

                			</tr>

                	<?
                	}
                	?>		
				</table>
			</td>
</tr>
</table>

            
            
          </TD>
        </TR>
        <TR>
          <TD>
            &nbsp;<BR><BR>
          </TD>
        </TR>
        <TR>
          <TD colSpan=2> 
<!-- Admin Message -->
            <TABLE style="HEIGHT: 20px" cellSpacing=1 cellPadding=2 width="100%" align=center border=0>
              <TR>
                <TD class=one style="TEXT-ALIGN: center" width="100%">
                  <B>Badstaile on Tkoc </B>
                </TD>
              </TR>
              <TR>
                <TD class=two style="TEXT-ALIGN: left" width="100%">
                From now on the previously known as badstaile is moved to stats.tkoc.net.For questions, suggestions or bugs feel free to contact us at admin@tkoc.net
                <br><br>tasosos
                </TD>
              </TR>
            </TABLE>
 <!-- End Admin Message -->

          </TD>
        </TR>
      </TABLE>
      </DIV>
    <BR><BR><BR>
    </TD>
  </TR>
</TABLE>

</BODY>
</HTML>
<?
function delete_prov($province) {
	$result = mysql_query("DELETE FROM provinces WHERE pname='$province'");
	$result = mysql_query("DELETE FROM statistics WHERE pname='$province'");
}
?>