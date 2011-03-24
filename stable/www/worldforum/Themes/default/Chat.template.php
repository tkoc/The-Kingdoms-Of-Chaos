<?php

function template_main() {
global $context, $scripturl, $boardurl, $modSettings;
if (isset($modSettings['irc_server'])) {
	$server = $modSettings['irc_server'];
} else {
	$server = 'irc.villageirc.net';
}
if (isset($modSettings['irc_quit_msg'])) {
	$quitmsg = $modSettings['irc_quit_msg'];
} else {
	$quitmsg = 'bye bye!';
}
if (isset($modSettings['irc_channel'])) {
	$channel = $modSettings['irc_channel'];
} else {
	$channel = '#pjirc';
}

if ($context['user']['is_guest']) {
	$userchatname = 'tkoc' . rand(1,10000);
} else {
	$userchatname = $context['user']['name'];
}
$userchatname = str_replace(" ","_",$userchatname);
$userchatname = str_replace(".","_",$userchatname);

echo' <div style="margin-top:20px; margin-bottom:40px; margin-left:auto; margin-right:auto; text-align:center">
<applet codebase="'. $boardurl. '/pjirc" code=IRCApplet.class archive="irc.jar,pixx.jar" width=735 height=600> 
<param codebase="'. $boardurl. '/pjirc" name="CABINETS" value="irc.cab,securedirc.cab,pixx.cab">
<param name="nick" value="'. $userchatname .'">
<param name="alternatenick" value="'. $userchatname . rand(1,10000) .'">
<param name="fullname" value="tkoc">
<param name="host" value="'. $server . '">
<param name="command1" value="/join #'. $channel . '">
<param name="gui" value="pixx">
<param name="quitmessage" value="'. $quitmsg . '">
<param name="asl" value="true">
<param name="useinfo" value="true">
<param name="style:bitmapsmileys" value="true">
<param name="userid" value="tkoc">';
$smileycount = 1;
foreach ($context['smileys']['postform'] as $smiley_row)
{
foreach ($smiley_row['smileys'] as $smiley)
echo '
<param name="style:smiley'. $smileycount++ .'" value="'. $smiley['code'] .' '. $modSettings['smileys_url'] .'/'. $context['user']['smiley_set'] .'/'. $smiley['filename'] .'">';
}
echo'
<param name="style:backgroundimage" value="false">
<param name="style:sourcefontrule1" value="all all Serif 12">
<param name="style:floatingasl" value="true">
<param name="pixx:timestamp" value="true">
<param name="pixx:highlight" value="true">
<param name="pixx:highlightnick" value="true">
<param name="pixx:nickfield" value="true">
<param name="pixx:styleselector" value="true">
<param name="pixx:setfontonstyle" value="true">
</applet></div>';
}
?>