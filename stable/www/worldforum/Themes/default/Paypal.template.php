<?php
// Version: Paypal

function template_paypal() {
	global $context, $modSettings, $scripturl, $txt, $settings;
	

	if ($modSettings['payPalEnable']) 
	{
	echo'
	<table width="100%" border="0" cellspacing="0" cellpadding="3" align="center">
			<tr>
				<td>', theme_linktree(), '</td>
			</tr>
		</table>
		<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="tborder">
			<tr class="titlebg">';
			// in the table you can change the text to whatever you wish to say
				echo'<td>' , $txt['paypal'] , '</td>
			</tr>
			<tr><td>
			<span style="font-family: Verdana, sans-serif; font-size: 100%; ">', $modSettings['payPalReason'], '</span>';

				echo'</td></tr><tr>
				<td class="windowbg">';

					// load the paypal form...you will need a paypal merchant account...go to paypal for more info...paste the complete form from paypal
					
		echo '
					<span style="font-family: Verdana, sans-serif; font-size: 100%; ">', $modSettings['payPalKey'], '</span>';

				echo'</td>
			</tr>
		</table>';
}  else {

	echo '<table width="100%" border="0" cellspacing="0" cellpadding="4" align="center" class="tborder">
		<tr class="titlebg">';
			// in the table you can change the text to whatever you wish to say
				echo'<td>' , $txt['paypal'] , '</td>
			</tr>
			<tr><td class="windowbg">
			<span style="font-family: Verdana, sans-serif; font-size: 100%; ">', $txt['DonationsNotEnabled'], '</span>
			</td></tr>
		</table>';


}


}

?>
