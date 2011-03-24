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
<?php
require_once ("../scripts/globals.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "Database.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "User.class.inc.php");
require_once ($GLOBALS['path_www_administration'] . "admin_all.inc.php");
require_once ($GLOBALS['path_www_administration'] . "Div.class.inc.php");
require_once ($GLOBALS['path_www_scripts'] . "isLoggedOn.inc.php");
require_once ("requireLogon.inc.php");

$text = "";
if (isset($_POST['step']))
{
	$text = "<table width=100%>";

	switch ($_POST['step'])
	{
	case 'vacationMode':
			require_once ($GLOBALS['path_www_scripts'] . "Province.class.inc.php");
			$detailProvince = new Province($user->getpID(), $GLOBALS['database']);
			$detailProvince->getProvinceData();
			$detailProvince->getMilitaryData();
			$milOut = $detailProvince->milObject->getMilitaryOut();
			if (is_array($milOut) || ($detailProvince->influence < 80) || ($detailProvince->mana < 80)) {
					$text .= '<TR class="row"><TD colspan=2>You can not go into vacation mode at this point. <b>You can not go into vacation mode while taking hostile actions.</b></TD></TR>';
			}
			else
			{
				if ($_POST['vacationConfirm'] == 'yes')
				{
					$user->database->query("UPDATE Province set vacationMode='true', vacationTicks=0 WHERE pID='$user->pId'");
					$text .= '<TR class="row"><TD colspan=2>Province is now in vacation freeze. <b></b></TD></TR>';					
				}
				else
				{
					$text .= '<TR class="row"><TD colspan=2>Incorrect confirmation.  type: yes <b>not sent to vacation mode</b></TD></TR>';
				}
			}
			
	break;
		case 'deleteProvince':
			require_once ($GLOBALS['path_www_scripts'] . "Province.class.inc.php");
			$detailProvince = new Province($user->getpID(), $GLOBALS['database']);
			$detailProvince->getProvinceData();
			$detailProvince->getMilitaryData();
			$milOut = $detailProvince->milObject->getMilitaryOut();
			if (is_array($milOut)) {
					$text .= '<TR class="row"><TD colspan=2>You can not delete youself at this point. <b>You can not delete while taking hostile actions</b></TD></TR>';
			}
			else
			{
				if ($user->password == (sha1(md5($_POST['deletePassword'],tsikirikilakistan)))) 
				{
					$user->database->query("UPDATE Province set status='Deleted' WHERE pID='$user->pId'");
					$text .= '<TR class="row"><TD colspan=2>Province deleted. <b>In about 1 hour you will be allowed to recreate</b></TD></TR>';
				} 
				else // incorrect password
				{
					$text .= '<TR class="row"><TD colspan=2>Incorrect password! Province <b>not deleted</b></TD></TR>';
				}
			}
		break;
		case 'update': 
/*			if ($user->name != ($_POST['name'])) {
				$text .= '<TR class="row"><TD colspan=2>Your name has been changed!</TD></TR>';
				$user->updateName($_POST['name']);
			}*/
			if ($user->email != ($_POST['email'])) {
				$text .= '<TR class="row"><TD colspan=2>Your email has been changed!</TD></TR>';
				$user->updateEmail($_POST['email']);
			}
			if (($user->allowUserUpdateNick && isset($_POST['nick'])) &&($user->nick != ($_POST['nick']))) {
				$text .= '<TR class="row"><TD colspan=2>Your nick has been changed!</TD></TR>';
				$user->updateNick($_POST['nick']);
			}
			if ($user->signature != ($_POST['userSignature'])) {
				$text .= '<TR class="row"><TD colspan=2>Signature has been changed!</TD></TR>';
				$user->updateSignature($_POST['userSignature']);
			}
			if (($user->allowUserUpdateImage && isset($_POST['image'])) &&($user->image != ($_POST['image']))) {
				$text .= '<TR class="row"><TD colspan=2>Your avatar has been changed!</TD></TR>';
				$user->updateImage($_POST['image']);
			}
			
		break;
		
		case 'newPass':
			if ($user->password != (sha1(md5($_POST['oldPassword'],tsikirikilakistan)))) {
				$text .= '<TR class="row"><TD colspan=2>Invalid password!</TD></TR>';
				break;
			}
			if (strlen($_POST['password1'])<6) {
				$text .= '<TR class="row"><TD colspan=2>That password is to easy...Find a better one!</TD></TR>';
				break;
			}
			if ($_POST['password2'] != $_POST['password1']) {
				$text .= '<TR class="row"><TD colspan=2>The new password and the retype does not match!</TD></TR>';
				break;
			}
			$text .= '<TR class="row"><TD colspan=2>Your new password is now: <b>'.$_POST['password1'].'</b></TD></TR>';
			$user->updatePassword(sha1(md5($_POST['password1'],tsikirikilakistan)));		
		break;
	
	}

	$text .= "</table>";
}


$html = "";
$html .= $text;
$html .= '
			<table cellpadding=5; cellspacing=0; border=0; style="width:100%">
			<TR class="subtitle"><TD class="TL">User details:</TD><TD class="TLR">Password</TD></TR>
			<TR bgcolor=#CCCCCC>
				<!-- User details -->
				<TD class="TL">
					<TABLE bgcolor=#CCCCCC cellpadding=5 cellspacing=0 border=0>
							<FORM action="'.$_SERVER['PHP_SELF'].'" method=POST>
							<TR>
								<TD>Username:</TD>
								<TD>'.$user->username.'</TD>
							</TR>
							<TR>
								<TD>Name:</TD>
';
//								<TD><input type="text" name="name" value="'.$user->name.'" style="width:265px"></input></TD>
	$html .='
								<TD>'.$user->name.'</TD>
							</TR>
							<TR>
								<TD>Email:</TD>
								<TD><input type="text" name="email" value="'.$user->email.'" style="width:265px"></input></TD>
							</TR>
							<TR>
								<TD>Nick (forum):</TD>';
if ($user->allowUserUpdateNick) $html .= '<TD align="left"><input type="text" name="nick" value="'.$user->nick.'" style="width:265px"></input></TD>';
else $html .='<TD align="left">'.$user->nick.'</TD>';
$html .='
							</TR>
							<TR>
								<TD>Avatar (forum):</TD>';
if ($user->allowUserUpdateImage) $html .= '<TD align="left"><input type="text" name="image" value="'.$user->image.'" style="width:265px"></input></TD>';
else $html .='<TD align="left">'. (($user->image=="") ? "none":$user->image) .'</TD>';
$html .='
							</TR>
							<TR>
								<TD>Signature:</TD>';
if ($user->allowUserUpdateSignature) $html .= '<TD><TEXTAREA cols=40 rows=4 name=userSignature>'.str_replace("<br>","\n",$user->signature).'</TEXTAREA></TD>';
else $html .='<TD align="left">'. (($user->signature == "") ? "none":$user->signature) .'</TD>';
$html .='					</TR>
							<TR>
								<TD>&nbsp;</TD>
								<TD align="left"><INPUT type="submit" name="Update" value="Update"></INPUT></TD>
							</TR>
							
							<INPUT type="hidden" name="userID" value="'.$user->userID.'">
							<INPUT type="hidden" name="step" value="update">
							</FORM>
					</TABLE>
				</TD>
				<!-- Password -->
				<TD valign=top class="TLR">
					<TABLE cellpadding=5 cellspacing=0 border=0>
						<FORM action="'.$_SERVER['PHP_SELF'].'" method=POST>
						<TR>
							<TD>Old password:</TD>
							<TD><input type="password" name="oldPassword" value="" style="width:125px"></input></TD>
						</TR>
						<TR>
							<TD>New password:</TD>
							<TD><input type="password" name="password1" value="" style="width:125px"></input></TD>
						</TR>
						<TR>
							<TD>Retype new password:</TD>
							<TD><input type="password" name="password2" value="" style="width:125px"></input></TD>
						</TR>
						<TR>
							<TD>&nbsp;</TD>
							<TD align="left"><INPUT type="submit" name="newPass" value="Set Password"></INPUT></TD>
							<INPUT type="hidden" name="userID" value="'.$user->userID.'">
							<INPUT type="hidden" name="step" value="newPass">
						</TR>
						</FORM>
					</TABLE>
				</TD>
			</TR>';
if ($user->pId>0)
{
	$html .='
			<TR class="subtitle"><TD class="TL">Province:</TD><TD class="TLR">&nbsp;</TD></TR>
			<TR bgcolor=#CCCCCC>
				<!-- User details -->
				<TD class="TL">
					<TABLE bgcolor=#CCCCCC cellpadding=5 cellspacing=0 border=0>
						<TR>
						<FORM action="'.$_SERVER['PHP_SELF'].'" method=POST>
							<TD>
								<INPUT type="submit" name="deleteProvince" value="Delete my Province">
							</TD>
							<TD>
								<input type="text" name="deletePassword" style="width:265px"></input>
							</TD>
						<INPUT type="hidden" name="step" value="deleteProvince">
						</FORM>
						</TR>
						<TR>
						<FORM action="'.$_SERVER['PHP_SELF'].'" method=POST>
							<TD>
								<INPUT type="submit" name="vacationMode" value="Put my Province in vacation mode">
							</TD>
							<TD>
								<input type="text" name="vacationConfirm" style="width:265px"></input>
							</TD>
						<INPUT type="hidden" name="step" value="vacationMode">
						</FORM>
						</TR>
						<TR>
							<TD COLSPAN=2>
if you wish to enter vacation mode your amries must be home, and your mana and influence at least 80% each.<br>
Your province will enter vacationmode immidiatly, preventing anyone from attacking, casting spells and do thievery on you.<br>
During vacation mode your province will not grow in acres, your amries will not train, your peasants will not eat, you will not generate any income.  Basically your province will be in total FREEZE while you are in vacation mode.
Your province will be freezed for at LEAST 48 hours.<br>
To enter vacation mode type in yes in the textfield.
							</TD>
						</TR>
					</TABLE>
				</TD>
				<!-- Empty -->
				<TD valign=top class="TLR">
					&nbsp;
				</TD>
			</TR>
		';
}
	$html .='
		</table>
		';

AdminTemplateDisplay("User profile for: <b>$user->username</b>",$user,$html);
?>
