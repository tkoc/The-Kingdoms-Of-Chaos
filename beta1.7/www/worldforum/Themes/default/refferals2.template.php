<?php

function template_ref_settings()
{
	global $context, $txt, $scripturl, $modSettings;

	echo '
	<form method="post" action="', $scripturl, '?action=refferals;sa=settings2" accept-charset="', $context['character_set'], '">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	  <tr>
	    <td width="50%" colspan="2"  align="center" class="catbg">
	    <b>', $txt['ref_admin'], '</b></td>
	  </tr>
	  
	  <tr class="windowbg2">
	  	<td>',$txt['ref_showreflink'],'</td>
	  	<td><input type="checkbox" name="ref_showreflink" ' . ($modSettings['ref_showreflink'] ? ' checked="checked" ' : '') . ' /></td>
	  </tr>
	  
	  <tr class="windowbg2">
	  	<td>',$txt['ref_showonpost'],'</td>
	  	<td><input type="checkbox" name="ref_showonpost"  ' . ($modSettings['ref_showonpost']  ? ' checked="checked" ' : '') . ' /></td>
	  </tr>
	  <tr class="windowbg2">
	  	<td>',$txt['ref_trackcookiehits'],'</td>
	  	<td><input type="checkbox" name="ref_trackcookiehits"  ' . ($modSettings['ref_trackcookiehits']  ? ' checked="checked" ' : '') . ' /></td>
	  </tr>	  
	  
	  <tr class="windowbg2">
	  	<td>',$txt['ref_cookietrackdays'],'</td>
	  	<td><input type="text" name="ref_cookietrackdays" size="4" value="' . $modSettings['ref_cookietrackdays'] . '" /></td>
	  </tr>
	  

	
	  <tr>
	    <td colspan="2" class="windowbg2" align="center">
	     <input type="hidden" name="sc" value="', $context['session_id'], '" />
	    <input type="submit" value="',$txt['ref_save_settings'],'" /></td>
	  </tr>
	  </table>
  	</form>';


}


?>