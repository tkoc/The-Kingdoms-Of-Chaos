<?php
// Version: 2.0 RC1; Combat

// Generate a strip of buttons, out of buttons.
function template_button_strip($button_strip, $direction = 'top', $custom_td = '')
{
	global $settings, $context, $txt, $scripturl;

	// Create the buttons...
	$buttons = array();
	foreach ($button_strip as $key => $value)
		if (!isset($value['test']) || !empty($context[$value['test']]))
			$buttons[] = '<a href="' . $value['url'] . '"' . (isset($value['active']) ? ' class="active"' : '') . (isset($value['custom']) ? ' ' . $value['custom'] : '') . '><span>' . $txt[$value['text']] . '</span></a>';

	if (empty($buttons))
		return '';

	// Make the last one, as easy as possible.
	$buttons[count($buttons) - 1] = str_replace('<span>', '<span class="last">', $buttons[count($buttons) - 1]);

	echo '
		<div class="buttonlist', $direction != 'top' ? '_bottom' : '', '">
			<ul class="clearfix">
				<li>', implode('</li><li>', $buttons), '</li>
			</ul>
		</div>';
}

?>