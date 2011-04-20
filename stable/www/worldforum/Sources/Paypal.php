<?php
// We calling this directly, if so, your evil...
if (!defined('SMF'))
	die('Hacking attempt...');

function Paypal() {
    global $db_prefix, $context, $txt, $scripturl;

	// Are we allowed to view the map? Keep in mind here we should be a guest unless user is in IE!
	isAllowedTo('payPal_view');


    // Get the template ready.... not really much else to do.
	loadTemplate('Paypal');
	$context['sub_template'] = 'paypal';
	$context['page_title'] = "Donations";
	$context['linktree'][] = array(
		'url' => $scripturl . '?action=paypal',
		'name' => $txt['paypal']
	);
}


?>
