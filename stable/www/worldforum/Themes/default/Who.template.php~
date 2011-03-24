<?php
// Version: 2.0 RC1; Who

// The only template in the file.
function template_main()
{
	global $context, $settings, $options, $scripturl, $txt;

	// Display the table header and linktree.
	echo '
	<div style="padding: 3px;">', theme_linktree(), '</div>
	<form action="', $scripturl, '?action=who" method="post" id="whoFilter" accept-charset="', $context['character_set'], '">
	<table cellpadding="3" cellspacing="0" border="0" width="100%" class="tborder">
		<tr class="titlebg">
			<td colspan="3">
				', $txt['who_title'], '
			</td>
		</tr>
		<tr class="catbg">
			<td width="30%"><a href="' . $scripturl . '?action=who;start=', $context['start'], ';show=', $context['show_by'], ';sort=user', $context['sort_direction'] != 'down' && $context['sort_by'] == 'user' ? '' : ';asc', '" rel="nofollow">', $txt['who_user'], ' ', $context['sort_by'] == 'user' ? '<img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a></td>
			<td style="width: 14ex;"><a href="' . $scripturl . '?action=who;start=', $context['start'], ';show=', $context['show_by'], ';sort=time', $context['sort_direction'] == 'down' && $context['sort_by'] == 'time' ? ';asc' : '', '" rel="nofollow">', $txt['who_time'], ' ', $context['sort_by'] == 'time' ? '<img src="' . $settings['images_url'] . '/sort_' . $context['sort_direction'] . '.gif" alt="" />' : '', '</a></td>
			<td>', $txt['who_action'], '</td>
		</tr>';

	// This is used to alternate the color of the background.
	$alternate = true;

	// For every member display their name, time and action (and more for admin).
	foreach ($context['members'] as $member)
	{
		// $alternate will either be true or false. If it's true, use "windowbg2" and otherwise use "windowbg".
		echo '
		<tr class="windowbg', $alternate ? '2' : '', '">
			<td>';

		// Guests don't have information like icq, msn, y!, and aim... and they can't be messaged.
		if (!$member['is_guest'])
		{
			echo '
				<div style="float: right; width: 14ex;">
					', $context['can_send_pm'] ? '<a href="' . $member['online']['href'] . '" title="' . $member['online']['label'] . '">' : '', $settings['use_image_buttons'] ? '<img src="' . $member['online']['image_href'] . '" alt="' . $member['online']['text'] . '" align="middle" />' : $member['online']['text'], $context['can_send_pm'] ? '</a>' : '', '
					', $member['icq']['link'], ' ', $member['msn']['link'], ' ', $member['yim']['link'], ' ', $member['aim']['link'], '
				</div>';
		}

		echo '
				<span', $member['is_hidden'] ? ' style="font-style: italic;"' : '', '>', $member['is_guest'] ? $member['name'] : '<a href="' . $member['href'] . '" title="' . $txt['profile_of'] . ' ' . $member['name'] . '"' . (empty($member['color']) ? '' : ' style="color: ' . $member['color'] . '"') . '>' . $member['name'] . '</a>', '</span>';

		if (!empty($member['ip']))
			echo '
				(<a href="' . $scripturl . '?action=', ($member['is_guest'] ? 'trackip' : 'profile;sa=tracking;area=ip;u=' . $member['id']), ';searchip=' . $member['ip'] . '">' . $member['ip'] . '</a>)';

		echo '
			</td>
			<td nowrap="nowrap">', $member['time'], '</td>
			<td>', $member['action'], '</td>
		</tr>';

		// Switch alternate to whatever it wasn't this time. (true -> false -> true -> false, etc.)
		$alternate = !$alternate;
	}

	// No members?
	if (empty($context['members']))
		echo '
		<tr class="windowbg2">
			<td colspan="3" align="center">
				', $txt['who_no_online_' . ($context['show_by'] == 'guests' || $context['show_by'] == 'spiders' ? $context['show_by'] : 'members')], '
			</td>
		</tr>';

	echo '
		<tr class="catbg">
			<td colspan="3">
				<div style="float: left;">
					<b>', $txt['pages'], ':</b> ', $context['page_index'], '
				</div>
				<div class="smalltext" style="float: right; font-weight: normal;">', $txt['who_show1'], '
					<select name="show" onchange="document.forms.whoFilter.submit();">';

	foreach ($context['show_methods'] as $value => $label)
		echo '
						<option value="', $value, '" ', $value == $context['show_by'] ? ' selected="selected"' : '', '>', $label, '</option>';
	echo '
					</select>
					<noscript>
						<input type="submit" value="', $txt['go'], '" />
					</noscript>
				</div>
			</td>
		</tr>
	</table>
	</form>';
}

function template_credits()
{
	global $context, $txt;

	// The most important part - the credits :P.
	echo '
	<div class="tborder windowbg2" id="credits">
		<h3 class="headerpadding">', $txt['credits'], '</h3>';

	foreach ($context['credits'] as $section)
	{
		if (isset($section['pretext']))
			echo '
		<p>', $section['pretext'], '</p>';

		if (isset($section['title']))
			echo '
		<h4 class="marginbottom">', $section['title'], '</h4>';

		echo '
		<ul class="normallist">';

		foreach ($section['groups'] as $group)
		{
			echo '
			<li class="smallpadding">';

			if (isset($group['title']))
			echo '
				<strong>', $group['title'], '</strong>: ';

			// Try to make this read nicely.
			if (count($group['members']) <= 2)
				echo implode($txt['credits_and'], $group['members']);
			else
			{
				$last_peep = array_pop($group['members']);
				echo implode(', ', $group['members']), ', ', $txt['credits_and'], ' ', $last_peep;
			}

			echo '
			</li>';
		}
		echo '
		</ul>';

		if (isset($section['posttext']))
			echo '
		<p>', $section['posttext'], '</p>';
	}

	echo '
		<h3 class="headerpadding">', $txt['credits_copyright'], '</h3>
		<h4 class="margintop">', $txt['credits_forum'], '</h4>', '
		<p>', $context['copyrights']['smf'];

	if (!empty($context['copyright_removal_validate']))
		echo '<br />
			', $context['copyright_removal_validate'];

	echo '
		</p>';

	if (!empty($context['copyrights']['mods']))
	{
		echo '
		<h4>', $txt['credits_modifications'], '</h4>
		<p>', implode("<br />\n", $context['copyrights']['mods']), '</p>';
	}

	echo '
	</div>';
}
?>