<?php
/*
Referrals System
Version 3.0
http://www.smfmodders.com/
*/
function refferalsMain()
{
	loadtemplate('refferals2');

	$subActions = array(
		'settings' => 'refferalsSettings',
		'settings2' => 'refferalsSettings2',
	);

	if (isset($_REQUEST['sa']))
		$sa = $_GET['sa'];
	else
		$sa = '';

	if (!empty($subActions[$sa]))
		$subActions[$sa]();
	else
		RefferalsLinkClick();
}

function ProcessRefferalLink()
{
	global $modSettings, $smcFunc;
	
	if (!isset($_REQUEST['referredby']))
		return;
		
	$refferedBy = (int) $_REQUEST['referredby'];
	
	if (empty($refferedBy) && isset($_COOKIE['refferedby']))
		$refferedBy = (int) $_COOKIE['refferedby'];
		
	if (empty($refferedBy) && isset($_SESSION['refferedby']))
		$refferedBy = (int) $_SESSION['refferedby'];	
	
	
	if (!empty($refferedBy) && !isset($_COOKIE['refferedby']) && !isset($_SESSION['refferedby']))
	{
		
		// Track hits??
		if ($modSettings['ref_trackcookiehits'])
		{
			 $smcFunc['db_query']('', "
			UPDATE {db_prefix}members
			SET referrals_hits = referrals_hits + 1 
			WHERE ID_MEMBER =  " . $refferedBy);
		}
		
		// Set cookie
		$cookie_url = url_parts(!empty($modSettings['localCookies']), !empty($modSettings['globalCooki0es']));
		setcookie('refferedby', $refferedBy, time() + ($modSettings['ref_cookietrackdays']  * 24 * 60 * 60), $cookie_url[1], $cookie_url[0], 0);
		
	
		$_SESSION['refferedby'] = $refferedBy ;
	}
	
}

function refferalsProcessSignup($refferedByName = '',$NewMemberID = 0)
{
	global $smcFunc;
	
	if (empty($NewMemberID))
		return;
	
	$refferedMemberID = 0;
	$refferedByName = htmlspecialchars($refferedByName,ENT_QUOTES);
	$result = $smcFunc['db_query']('', "
			SELECT 
				ID_MEMBER 
			FROM {db_prefix}members
			WHERE member_name =  '$refferedByName' OR real_name = '$refferedByName'" );
	$refferalRow = $smcFunc['db_fetch_assoc']($result);
	
	if (!empty($refferalRow['ID_MEMBER']))
		$refferedMemberID = $refferalRow['ID_MEMBER'];
	else 
	{
		if (isset($_COOKIE['refferedby']))
			$refferedMemberID = (int) $_COOKIE['refferedby'];
		
		if (isset($_SESSION['refferedby']))
			$refferedMemberID = (int) $_SESSION['refferedby'];	
	}
		
	
	
	if (!empty($refferedMemberID))
	{
		 $smcFunc['db_query']('', "
			UPDATE {db_prefix}members
			SET referrals_no = referrals_no + 1 
			WHERE ID_MEMBER =  " . $refferedMemberID );
		 
		 // Update reffered by
		  $smcFunc['db_query']('', "
			UPDATE {db_prefix}members
			SET  referred_by = $refferedMemberID
			WHERE ID_MEMBER =  " . $NewMemberID );
		
	}
	
	
	
}

function RefferalsLinkClick()
{
	ProcessRefferalLink();
	
	// Redirect
	redirectexit('action=register');
}

function refferalsSettings()
{
	global $txt, $context;
	
	isAllowedTo('admin_forum');
	
	$context['sub_template'] = 'ref_settings';
	$context['page_title'] = $txt['ref_admin'];
	
	
	$context[$context['admin_menu_name']]['tab_data'] = array(
			'title' =>  $txt['ref_admin'],
			'description' => '',
			'tabs' => array(
				'settings' => array(
					'description' => $txt['ref_settings'],
				),
			),
		);



}

function refferalsSettings2()
{
	isAllowedTo('admin_forum');
	
	checkSession('post');
	
	$ref_cookietrackdays = (int) $_REQUEST['ref_cookietrackdays'];
	$ref_showreflink = isset($_REQUEST['ref_showreflink']) ? 1 : 0;
	$ref_showonpost = isset($_REQUEST['ref_showonpost']) ? 1 : 0;
	$ref_trackcookiehits = isset($_REQUEST['ref_trackcookiehits']) ? 1 : 0;
	
	updateSettings(
	array(
	'ref_cookietrackdays' => $ref_cookietrackdays, 
	'ref_showreflink' => $ref_showreflink,
	'ref_showonpost' => $ref_showonpost,
	'ref_trackcookiehits' => $ref_trackcookiehits,
	
	));


	redirectexit('action=admin;area=refferals;sa=settings');
}


?>