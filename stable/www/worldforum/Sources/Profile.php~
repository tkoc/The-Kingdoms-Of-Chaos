<?php
/**********************************************************************************
* Profile.php                                                                     *
***********************************************************************************
* SMF: Simple Machines Forum                                                      *
* Open-Source Project Inspired by Zef Hemel (zef@zefhemel.com)                    *
* =============================================================================== *
* Software Version:           SMF 2.0 RC1                                         *
* Software by:                Simple Machines (http://www.simplemachines.org)     *
* Copyright 2006-2009 by:     Simple Machines LLC (http://www.simplemachines.org) *
*           2001-2006 by:     Lewis Media (http://www.lewismedia.com)             *
* Support, News, Updates at:  http://www.simplemachines.org                       *
***********************************************************************************
* This program is free software; you may redistribute it and/or modify it under   *
* the terms of the provided license as published by Simple Machines LLC.          *
*                                                                                 *
* This program is distributed in the hope that it is and will be useful, but      *
* WITHOUT ANY WARRANTIES; without even any implied warranty of MERCHANTABILITY    *
* or FITNESS FOR A PARTICULAR PURPOSE.                                            *
*                                                                                 *
* See the "license.txt" file for details of the Simple Machines license.          *
* The latest version can always be found at http://www.simplemachines.org.        *
**********************************************************************************/

if (!defined('SMF'))
	die('Hacking attempt...');

/*	This file has the primary job of showing and editing people's profiles.
	It also allows the user to change some of their or another's preferences,
	and such things.  It uses the following functions:

	void ModifyProfile(array errors = none)
		// !!!

	void loadCustomFields(int id_member, string area)
		// !!!

*/

// Allow the change or view of profiles...
function ModifyProfile($post_errors = array())
{
	global $txt, $scripturl, $user_info, $context, $sourcedir, $user_profile, $cur_profile;
	global $modSettings, $memberContext, $profile_vars, $smcFunc, $post_errors;

	// Don't reload this as we may have processed error strings.
	if (empty($post_errors))
		loadLanguage('Profile');
	loadTemplate('Profile', 'profile');

	require_once($sourcedir . '/Subs-Menu.php');

	// Did we get the user by name...
	if (isset($_REQUEST['user']))
		$memberResult = loadMemberData($_REQUEST['user'], true, 'profile');
	// ... or by id_member?
	elseif (!empty($_REQUEST['u']))
		$memberResult = loadMemberData((int) $_REQUEST['u'], false, 'profile');
	// If it was just ?action=profile, edit your own profile.
	else
		$memberResult = loadMemberData($user_info['id'], false, 'profile');

	// Check if loadMemberData() has returned a valid result.
	if (!is_array($memberResult))
		fatal_lang_error('not_a_user', false);

	// If all went well, we have a valid member ID!
	list ($memID) = $memberResult;
	$context['id_member'] = $memID;
	$cur_profile = $user_profile[$memID];
	
	// Let's have some information about this member ready, too.
	loadMemberContext($memID);
	$context['member'] = $memberContext[$memID];

	// Is this the profile of the user himself or herself?
	$context['user']['is_owner'] = $memID == $user_info['id'];

	/* Define all the sections within the profile area!
		We start by defining the permission required - then SMF takes this and turns it into the relevant context ;)
		Possible fields:
			For Section:
				string $title:		Section title.
				array $areas:		Array of areas within this section.

			For Areas:
				string $label:		Text string that will be used to show the area in the menu.
				string $file:		Optional text string that may contain a file name that's needed for inclusion in order to display the area properly.
				string $custom_url:	Optional href for area.
				string $function:	Function to execute for this section.
				bool $enabled:		Should area be shown?
				string $sc:		Session check validation to do on save - note without this save will get unset - if set.
				bool $hidden:		Does this not actually appear on the menu?
				bool $password:		Whether to require the user's password in order to save the data in the area.
				array $subsections:	Array of sucsections, in order of appearance.
				array $permission:	Array of permissions to determine who can access this area. Should contain arrays $own and $any.
	*/
	$profile_areas = array(
		'info' => array(
			'title' => $txt['profileInfo'],
			'areas' => array(
				'summary' => array(
					'label' => $txt['summary'],
					'file' => 'Profile-View.php',
					'function' => 'summary',
					'permission' => array(
						'own' => 'profile_view_own',
						'any' => 'profile_view_any',
					),
				),
				'statistics' => array(
					'label' => $txt['statPanel'],
					'file' => 'Profile-View.php',
					'function' => 'statPanel',
					'permission' => array(
						'own' => 'profile_view_own',
						'any' => 'profile_view_any',
					),
				),
				'showposts' => array(
					'label' => $txt['showPosts'],
					'file' => 'Profile-View.php',
					'function' => 'showPosts',
					'subsections' => array(
						'messages' => array($txt['showMessages'], 'profile_view_any'),
						'topics' => array($txt['showTopics'], 'profile_view_any'),
						'attach' => array($txt['showAttachments'], 'profile_view_any'),
					),
					'permission' => array(
						'own' => 'profile_view_own',
						'any' => 'profile_view_any',
					),
				),
				'permissions' => array(
					'label' => $txt['showPermissions'],
					'file' => 'Profile-View.php',
					'function' => 'showPermissions',
					'permission' => array(
						'own' => 'manage_permissions',
						'any' => 'manage_permissions',
					),
				),
				'tracking' => array(
					'label' => $txt['trackUser'],
					'file' => 'Profile-View.php',
					'function' => 'tracking',
					'subsections' => array(
						'user' => array($txt['trackUser'], 'moderate_forum'),
						'ip' => array($txt['trackIP'], 'moderate_forum'),
						'edits' => array($txt['trackEdits'], 'moderate_forum'),
					),
					'permission' => array(
						'own' => 'moderate_forum',
						'any' => 'moderate_forum',
					),
				),
				'viewwarning' => array(
					'file' => 'Profile-View.php',
					'function' => 'viewWarning',
					'select' => 'summary',
					'permission' => array(
						'own' => array('profile_view_own'),
						'any' => array('profile_view_any'),
					),
				),
			),
		),
		'edit_profile' => array(
			'title' => $txt['profileEdit'],
			'areas' => array(
				'account' => array(
					'label' => $txt['account'],
					'file' => 'Profile-Modify.php',
					'function' => 'account',
					'sc' => 'post',
					'password' => true,
					'permission' => array(
						'own' => array('profile_identity_any', 'profile_identity_own', 'manage_membergroups'),
						'any' => array('profile_identity_any', 'manage_membergroups'),
					),
				),
				'forumprofile' => array(
					'label' => $txt['forumprofile'],
					'file' => 'Profile-Modify.php',
					'function' => 'forumProfile',
					'sc' => 'post',
					'permission' => array(
						'own' => array('profile_extra_any', 'profile_extra_own', 'profile_title_own', 'profile_title_any'),
						'any' => array('profile_extra_any', 'profile_title_any'),
					),
				),
				'theme' => array(
					'label' => $txt['theme'],
					'file' => 'Profile-Modify.php',
					'function' => 'theme',
					'sc' => 'post',
					'permission' => array(
						'own' => array('profile_extra_any', 'profile_extra_own'),
						'any' => array('profile_extra_any'),
					),
				),
				'authentication' => array(
					'label' => $txt['authentication'],
					'file' => 'Profile-Modify.php',
					'function' => 'authentication',
					'enabled' => !empty($modSettings['enableOpenID']) || !empty($cur_profile['openid_uri']),
					'sc' => 'post',
					'hidden' => empty($modSettings['enableOpenID']) && empty($cur_profile['openid_uri']),
					'permission' => array(
						'own' => array('profile_identity_any', 'profile_identity_own'),
						'any' => array('profile_identity_any'),
					),
				),
				'notification' => array(
					'label' => $txt['notification'],
					'file' => 'Profile-Modify.php',
					'function' => 'notification',
					'sc' => 'post',
					'permission' => array(
						'own' => array('profile_extra_any', 'profile_extra_own'),
						'any' => array('profile_extra_any'),
					),
				),
				// Without profile_extra_own, settings are accessible from the PM section.
				'pmprefs' => array(
					'label' => $txt['pmprefs'],
					'file' => 'Profile-Modify.php',
					'function' => 'pmprefs',
					'enabled' => allowedTo(array('profile_extra_own', 'profile_extra_any')),
					'sc' => 'post',
					'permission' => array(
						'own' => array('pm_read'),
						'any' => array('profile_extra_any'),
					),
				),
				'ignoreboards' => array(
					'label' => $txt['ignoreboards'],
					'file' => 'Profile-Modify.php',
					'function' => 'ignoreboards',
					'enabled' => !empty($modSettings['allow_ignore_boards']),
					'sc' => 'post',
					'permission' => array(
						'own' => array('profile_extra_any', 'profile_extra_own'),
						'any' => array('profile_extra_any'),
					),
				),
				'buddies' => array(
					'label' => $txt['editBuddies'],
					'file' => 'Profile-Modify.php',
					'function' => 'editBuddies',
					'enabled' => !empty($modSettings['enable_buddylist']) && $context['user']['is_owner'],
					'sc' => 'post',
					'permission' => array(
						'own' => array('profile_extra_any', 'profile_extra_own'),
						'any' => array(),
					),
				),
				'groupmembership' => array(
					'label' => $txt['groupmembership'],
					'file' => 'Profile-Modify.php',
					'function' => 'groupMembership',
					'enabled' => !empty($modSettings['show_group_membership']) && $context['user']['is_owner'],
					'sc' => 'request',
					'permission' => array(
						'own' => array('profile_view_own'),
						'any' => array('manage_membergroups'),
					),
				),
			),
		),
		'profile_action' => array(
			'title' => $txt['profileAction'],
			'areas' => array(
				'sendpm' => array(
					'label' => $txt['profileSendIm'],
					'custom_url' => $scripturl . '?action=pm;sa=send',
					'permission' => array(
						'own' => array(),
						'any' => array('pm_send'),
					),
				),
				'issuewarning' => array(
					'label' => $context['user']['is_owner'] && $cur_profile['warning'] ? $txt['profile_view_warnings'] : $txt['profile_issue_warning'],
					'enabled' => $modSettings['warning_settings']{0} == 1 && (!$context['user']['is_owner'] || $cur_profile['warning']),
					'file' => 'Profile-Actions.php',
					'function' => 'issueWarning',
					'permission' => array(
						'own' => array('issue_warning'),
						'any' => array('issue_warning'),
					),
				),
				'banuser' => array(
					'label' => $txt['profileBanUser'],
					'custom_url' => $scripturl . '?action=admin;area=ban;sa=add',
					'enabled' => $cur_profile['id_group'] != 1 && !in_array(1, explode(',', $cur_profile['additional_groups'])),
					'permission' => array(
						'own' => array(),
						'any' => array('manage_bans'),
					),
				),
				'subscriptions' => array(
					'label' => $txt['subscriptions'],
					'file' => 'Profile-Actions.php',
					'function' => 'subscriptions',
					'enabled' => !empty($modSettings['paid_enabled']),
					'permission' => array(
						'own' => array('profile_view_own'),
						'any' => array('moderate_forum'),
					),
				),
				'deleteaccount' => array(
					'label' => $txt['deleteAccount'],
					'file' => 'Profile-Actions.php',
					'function' => 'deleteAccount',
					'sc' => 'post',
					'password' => true,
					'permission' => array(
						'own' => array('profile_remove_any', 'profile_remove_own'),
						'any' => array('profile_remove_any'),
					),
				),
				'activateaccount' => array(
					'file' => 'Profile-Actions.php',
					'function' => 'activateAccount',
					'sc' => 'get',
					'select' => 'summary',
					'permission' => array(
						'own' => array(),
						'any' => array('moderate_forum'),
					),
				),
			),
		),
	);

	// Auto populate the above!
	$defaultAction = false;
	$defaultInclude = false;
	$context['completed_save'] = false;
	$context['password_areas'] = array();
	$security_checks = array();
	$include_file = false;
	$requestedAreaValid = false;

	foreach ($profile_areas as $section_id => $section)
	{
		// Not even enabled?
		if (isset($section['enabled']) && $section['enabled'] == false)
		{
			unset($profile_areas[$section_id]);
			continue;
		}

		foreach ($section['areas'] as $area_id => $area)
		{
			// Were we trying to see this?
			if (isset($_REQUEST['area']) && $_REQUEST['area'] == $area_id && (!isset($area['enabled']) || $area['enabled'] != false) && !empty($area['permission'][$context['user']['is_owner'] ? 'own' : 'any']))
			{
				$security_checks['permission'] = $area['permission'][$context['user']['is_owner'] ? 'own' : 'any'];

				// Are we saving data in a valid area?
				if (isset($area['sc']) && isset($_REQUEST['save']))
				{
					$security_checks['session'] = $area['sc'];
					$context['completed_save'] = true;
				}

				// Does this require session validating?
				if (!empty($area['validate']))
					$security_checks['validate'] = true;

				// Need to include the file?
				if (!empty($area['file']))
					$include_file = $area['file'];
				
				// The area requested by the user turns out to be a valid and enabled one.
				$requestedAreaValid = true;
			}

			// Can we do this?
			if ((!isset($area['enabled']) || $area['enabled'] != false) && !empty($area['permission'][$context['user']['is_owner'] ? 'own' : 'any']) && allowedTo($area['permission'][$context['user']['is_owner'] ? 'own' : 'any']) && empty($area['hidden']))
			{
				// Replace the contents with a link.
				$profile_areas[$section_id]['areas'][$area_id]['permission'] = true;
				// Should we do this by default?
				if ($defaultAction === false)
				{
					$defaultAction = $area_id;
					$defaultInclude = !empty($area['file']) ? $area['file'] : false;
				}
				// Password required - only if not on OpenID.
				if (!empty($area['password']) && empty($cur_profile['openid_uri']))
					$context['password_areas'][] = $area_id;
			}
			// Otherwise unset it!
			else
				unset($profile_areas[$section_id]['areas'][$area_id]);
		}

		// Is there nothing left?
		if (empty($profile_areas[$section_id]['areas']))
			unset($profile_areas[$section_id]);
	}

	// If we have no sub-action find the default or drop out.
	if (!isset($_REQUEST['area']) && $defaultAction !== false)
	{
		$_REQUEST['area'] = $defaultAction;
		if ($defaultInclude)
			$include_file = $defaultInclude;
	}
	elseif (!isset($_REQUEST['area']) || !$requestedAreaValid)
		isAllowedTo('profile_view_' . ($context['user']['is_owner'] ? 'own' : 'any'));

	// Now the context is setup have we got any security checks to carry out additional to that above?
	if (isset($security_checks['session']))
		checkSession($security_checks['session']);
	if (isset($security_checks['validate']))
		validateSession();
	if (isset($security_checks['permission']))
		isAllowedTo($security_checks['permission']);

	// Is there an updated message to show?
	if (isset($_GET['updated']))
		$context['profile_updated'] = $txt['profile_updated_own'];

	// Set a few options for the menu.
	$menuOptions = array(
		'disable_url_session_check' => true,
		'extra_url_parameters' => array(
			'u' => $context['id_member'],
		),
	);
	// Actually create the menu!
	$profile_include_data = createMenu($profile_areas, $menuOptions);
	unset($profile_areas);

	// Make a note of the Unique ID for this menu.
	$context['profile_menu_id'] = $context['max_menu_id'];
	$context['profile_menu_name'] = 'menu_data_' . $context['profile_menu_id'];

	// Set the selected item.
	$context['menu_item_selected'] = $profile_include_data['current_area'];

	// File to include?
	if (isset($profile_include_data['file']))
		require_once($sourcedir . '/' . $profile_include_data['file']);

	// Make sure that the area function does exist!
	if (!isset($profile_include_data['function']) || !function_exists($profile_include_data['function']))
	{
		destroyMenu();
		fatal_lang_error('no_access');
	}

	// Set the template for this area and add the profile layer.
	$context['sub_template'] = $profile_include_data['function'];
	$context['template_layers'][] = 'profile';

	// All the subactions that require a user password in order to validate.
	$context['require_password'] = in_array($profile_include_data['current_area'], $context['password_areas']);

	// If we're in wireless then we have a cut down template...
	if (WIRELESS && $context['sub_template'] == 'summary' && WIRELESS_PROTOCOL != 'wap')
		$context['sub_template'] = WIRELESS_PROTOCOL . '_profile';

	// These will get populated soon!
	$post_errors = array();
	$profile_vars = array();

	// Right - are we saving - if so let's save the old data first.
	if ($context['completed_save'])
	{
		// If it's someone elses profile then validate the session.
		if (!$context['user']['is_owner'])
			validateSession();

		// Clean up the POST variables.
		$_POST = htmltrim__recursive($_POST);
		$_POST = htmlspecialchars__recursive($_POST);

		if ($context['user']['is_owner'] && $context['require_password'])
		{
			// You didn't even enter a password!
			if (trim($_POST['oldpasswrd']) == '')
				$post_errors[] = 'no_password';

			// Since the password got modified due to all the $_POST cleaning, lets undo it so we can get the correct password
			$_POST['oldpasswrd'] = un_htmlspecialchars($_POST['oldpasswrd']);

			// Does the integration want to check passwords?
			$good_password = false;
			if (isset($modSettings['integrate_verify_password']) && function_exists($modSettings['integrate_verify_password']))
				if (call_user_func($modSettings['integrate_verify_password'], $cur_profile['member_name'], $_POST['oldpasswrd'], false) === true)
					$good_password = true;

			// Bad password!!!
			if (!$good_password && $user_info['passwd'] != sha1(strtolower($cur_profile['member_name']) . $_POST['oldpasswrd']))
				$post_errors[] = 'bad_password';

			// Warn other elements not to jump the gun and do custom changes!
			if (in_array('bad_password', $post_errors))
				$context['password_auth_failed'] = true;
		}

		// Change the IP address in the database.
		if ($context['user']['is_owner'])
			$profile_vars['member_ip'] = $user_info['ip'];

		// Now call the sub-action function...
		if (isset($_REQUEST['area']) && $_REQUEST['area'] == 'activateaccount')
		{
			if (empty($post_errors))
				activateAccount($memID);
		}
		elseif (isset($_REQUEST['area']) && $_REQUEST['area'] == 'deleteaccount')
		{
			if (empty($post_errors))
			{
				deleteAccount2($profile_vars, $post_errors, $memID);
				redirectexit();
			}
		}
		elseif (isset($_REQUEST['area']) && $_REQUEST['area'] == 'groupmembership' && empty($post_errors))
		{
			$msg = groupMembership2($profile_vars, $post_errors, $memID);

			// Whatever we've done, we have nothing else to do here...
			redirectexit('action=profile;u=' . $memID . ';area=groupmembership' . (!empty($msg) ? ';msg=' . $msg : ''));
		}
		// Authentication changes?
		elseif (isset($_REQUEST['area']) && $_REQUEST['area'] == 'authentication')
		{
			authentication($memID, true);
		}
		elseif (isset($_REQUEST['area']) && in_array($_REQUEST['area'], array('account', 'forumprofile', 'theme', 'pmprefs')))
			saveProfileFields();
		else
		{
			$force_redirect = true;
			// Ensure we include this.
			require_once($sourcedir . '/Profile-Modify.php');
			saveProfileChanges($profile_vars, $post_errors, $memID);
		}

		// There was a problem, let them try to re-enter.
		if (!empty($post_errors))
		{
			// Load the language file so we can give a nice explanation of the errors.
			loadLanguage('Errors');
			$context['post_errors'] = $post_errors;
		}
		elseif (!empty($profile_vars))
		{
			// If we've changed the password, notify any integration that may be listening in.
			if (isset($profile_vars['passwd']) && isset($modSettings['integrate_reset_pass']) && function_exists($modSettings['integrate_reset_pass']))
				call_user_func($modSettings['integrate_reset_pass'], $cur_profile['member_name'], $cur_profile['member_name'], $_POST['passwrd1']);

			updateMemberData($memID, $profile_vars);

			// What if this is the newest member?
			if ($modSettings['latestMember'] == $memID)
				updateStats('member');
			elseif (isset($profile_vars['real_name']))
				updateSettings(array('memberlist_updated' => time()));

			// If the member changed his/her birthdate, update calendar statistics.
			if (isset($profile_vars['birthdate']) || isset($profile_vars['real_name']))
				updateSettings(array(
					'calendar_updated' => time(),
				));

			// Anything worth logging?
			if (!empty($context['log_changes']) && !empty($modSettings['modlog_enabled']))
			{
				$log_changes = array();
				foreach ($context['log_changes'] as $k => $v)
					$log_changes[] = array(
						'action' => $k,
						'id_log' => 2,
						'log_time' => time(),
						'id_member' => $memID,
						'ip' => $user_info['ip'],
						'extra' => serialize(array_merge($v, array('applicator' => $user_info['id']))),
					);
				$smcFunc['db_insert']('',
					'{db_prefix}log_actions',
					array(
						'action' => 'string', 'id_log' => 'int', 'log_time' => 'int', 'id_member' => 'int', 'ip' => 'string-16',
						'extra' => 'string-65534',
					),
					$log_changes,
					array('id_action')
				);
			}

			// Have we got any post save functions to execute?
			if (!empty($context['profile_execute_on_save']))
				foreach ($context['profile_execute_on_save'] as $saveFunc)
					$saveFunc();

			// Let them know it worked!
			$context['profile_updated'] = $context['user']['is_owner'] ? $txt['profile_updated_own'] : sprintf($txt['profile_updated_else'], $cur_profile['member_name']);

			// Invalidate any cached data.
			cache_put_data('member_data-profile-' . $memID, null, 0);
		}
	}

	// Have some errors for some reason?
	if (!empty($post_errors))
	{
		// Set all the errors so the template knows what went wrong.
		foreach ($post_errors as $error_type)
			$context['modify_error'][$error_type] = true;
	}
	// If it's you then we should redirect upon save.
	elseif (!empty($profile_vars) && $context['user']['is_owner'])
		redirectexit('action=profile;area=' . $_REQUEST['area'] . ';updated');
	elseif (!empty($force_redirect))
		redirectexit('action=profile;u=' . $memID . ';area=' . $_REQUEST['area']);

	// Call the appropriate subaction function.
	$profile_include_data['function']($memID);

	// Set the page title if it's not already set...
	if (!isset($context['page_title']))
		$context['page_title'] = $txt['profile'] . ' - ' . $txt[$_REQUEST['area']];
}

// Load any custom fields for this area... no area means load all, 'summary' loads all public ones.
function loadCustomFields($memID, $area = 'summary')
{
	global $context, $txt, $user_profile, $smcFunc, $user_info;

	// Get the right restrictions in place...
	$where = 'active = 1';
	if (!allowedTo('admin_forum') && $area != 'register')
	{
		// If it's the owner they can see two types of private fields, regardless.
		if ($memID == $user_info['id'])
			$where .= $area == 'summary' ? ' AND private < 3' : ' AND (private = 0 OR private = 2)';
		else
			$where .= $area == 'summary' ? ' AND private < 2' : ' AND private = 0';
	}

	if ($area == 'register')
		$where .= ' AND show_reg != 0';
	elseif ($area != 'summary')
		$where .= ' AND show_profile = {string:area}';

	// Load all the relevant fields - and data.
	$request = $smcFunc['db_query']('', '
		SELECT col_name, field_name, field_desc, field_type, field_length, field_options,
			default_value, bbc
		FROM {db_prefix}custom_fields
		WHERE ' . $where,
		array(
			'area' => $area,
		)
	);
	$context['custom_fields'] = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		// Shortcut.
		$exists = $memID && isset($user_profile[$memID], $user_profile[$memID]['options'][$row['col_name']]);
		$value = $exists && $user_profile[$memID]['options'][$row['col_name']] ? $user_profile[$memID]['options'][$row['col_name']] : '';

		// If this was submitted already then make the value the posted version.
		if (isset($_POST['customfield']) && isset($_POST['customfield'][$row['col_name']]))
			$value = $smcFunc['htmlspecialchars']($_POST['customfield'][$row['col_name']]);

		// HTML for the input form.
		$output_html = $value;
		if ($row['field_type'] == 'check')
		{
			$true = (!$exists && $row['default_value']) || $value;
			$input_html = '<input type="checkbox" name="customfield[' . $row['col_name'] . ']" ' . ($true ? 'checked="checked"' : '') . ' class="check" />';
			$output_html = $true ? $txt['yes'] : $txt['no'];
		}
		elseif ($row['field_type'] == 'select')
		{
			$input_html = '<select name="customfield[' . $row['col_name'] . ']">';
			$options = explode(',', $row['field_options']);
			foreach ($options as $k => $v)
			{
				$true = (!$exists && $row['default_value'] == $v) || $value == $v;
				$input_html .= '<option value="' . $k . '" ' . ($true ? 'selected="selected"' : '') . '>' . $v . '</option>';
				if ($true)
					$output_html = $v;
			}

			$input_html .= '</select>';
		}
		elseif ($row['field_type'] == 'radio')
		{
			$input_html = '<fieldset>';
			$options = explode(',', $row['field_options']);
			foreach ($options as $k => $v)
			{
				$true = (!$exists && $row['default_value'] == $v) || $value == $v;
				$input_html .= '<label for="customfield_' . $row['col_name'] . '_' . $k . '"><input type="radio" name="customfield[' . $row['col_name'] . ']" id="customfield_' . $row['col_name'] . '_' . $k . '" value="' . $k . '" ' . ($true ? 'checked="checked"' : '') . '>' . $v . '</label><br />';
				if ($true)
					$output_html = $v;
			}
			$input_html .= '</fieldset>';
		}
		elseif ($row['field_type'] == 'text')
		{
			$input_html = '<input type="text" name="customfield[' . $row['col_name'] . ']" ' . ($row['field_length'] != 0 ? 'maxlength="' . $row['field_length'] . '"' : '') . ' size="' . ($row['field_length'] == 0 || $row['field_length'] >= 50 ? 50 : ($row['field_length'] > 30 ? 30 : ($row['field_length'] > 10 ? 20 : 10))) . '" value="' . $value . '" />';
		}
		else
		{
			@list ($rows, $cols) = @explode(',', $row['default_value']);
			$input_html = '<textarea name="customfield[' . $row['col_name'] . ']" ' . (!empty($rows) ? 'rows="' . $rows . '"' : '') . ' ' . (!empty($cols) ? 'cols="' . $cols . '"' : '') . '>' . $value . '</textarea>';
		}

		if ($row['bbc'])
			$output_html = parse_bbc($output_html);

		$context['custom_fields'][] = array(
			'name' => $row['field_name'],
			'desc' => $row['field_desc'],
			'type' => $row['field_type'],
			'input_html' => $input_html,
			'output_html' => $output_html,
			'value' => $value,
		);
	}
	$smcFunc['db_free_result']($request);
}

?>