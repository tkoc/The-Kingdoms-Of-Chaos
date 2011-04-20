<?php
/*******************************************************************************
* This program is free software; you can redistribute it and/or modify         *
* it under the terms of the GNU General Public License as published by         *
* the Free Software Foundation; either version 2 of the License.               *
* $Source: /0cvs/TreasurySMF/TreasuryHelp.english-utf8.php,v $                 *
* $Revision: 1.9 $                                                             *
* $Date: 2010/01/25 04:18:19 $                                                 *
* SMF2 Treasury Version 2.03 by Resourcez at resourcez.biz                     *
*******************************************************************************/
global $helptxt;
$helptxt = array();

// Main config parameters
$helptxt['treas_choose_period'] = '<b>Choose Period</b><br />Format <b>must</b> be YYYY-mm-dd<br />The duration over which donations can be totalled can be selected from the drop-down list, in which case you need to leave the date selection boxes empty - when these boxes contain dates, the drop-down period listing no longer functions.<br /><br />If you wish to know totals between two specific dates, then click the small calendar icons to select your "From" and "To" dates.';
$helptxt['treas_duration'] = '<b>Donation Duration</b><br />The duration over which donations are summarized can be monthly, quarterly, half yearly, and yearly.<br />For ease of application, this works on calendar year time periods only.';
$helptxt['treas_dm_show_targets'] = '<b>Show Targets</b><br />Should we display the Goals and Balances?<br />Applies to the main page and also the TinyPortal block.';
$helptxt['treas_dm_show_meter'] = '<b>Show Donormeter</b><br />Should we display the percentage progress DonorMeter?<br />Applies to the main page and also the TinyPortal block.';
$helptxt['treas_group_use'] = '<b>Donor Group Flag</b><br />Automatically assign donors to a group when a donation is made - this is coded as an additional group and will not affect the primary group that a user already belongs to.<br /><br /><b>Note</b>: if you activate this option, make sure you also select a Donor Group in the drop-down box below.';
$helptxt['treas_group_id'] = '<b>Select Donor Group</b><br />Select the group for donors to be automatically assigned to - the drop-down list ignores admin, moderator and post-based groups.';
$helptxt['treas_date_format'] = '<b>Date Format</b><br />Allows basic date formatting choices between US and UK style dates - to avoid the use of non-standard dates incompatible with Treasury. Simply select one and Treasury will adopt that format display throughout admin.';
$helptxt['treas_don_show_button_top'] = '<b>Display Top Button</b><br />This button simply sends the user down the page to an anchor point where they can commit to a donation - handy if your pre-amble is rather long.  However, since some prefer to remove it, this option simply suppresses its display.';
$helptxt['treas_don_button_top'] = '<b>Top Donation Button</b><br />Enter the image name to use at the top of the Treasury main page - this image must be located in the Default theme images folder.';
$helptxt['treas_don_top_img_width'] = '<b>Top Button Dimensions</b><br />Restrict the image dimensions for the top donation button.  To use the image native size leave both boxes blank.';
$helptxt['treas_don_top_img_height'] = '<b>Top Button Dimensions</b><br />Restrict the image dimensions for the top donation button.  To use the image native size leave both boxes blank.';
$helptxt['treas_don_button_submit'] = '<b>Donation Submit Button</b><br />Enter a filename for the image to use at the bottom of the Treasury module to submit a donation - this image must be located in the Default theme images folder.';
$helptxt['treas_don_sub_img_width'] = '<b>Submit Button Dimensions</b><br />Restrict the image dimensions for the PayPal submit button.  To use the image native size leave both boxes blank.';
$helptxt['treas_don_sub_img_height'] = '<b>Submit Button Dimensions</b><br />Restrict the image dimensions for the PayPal submit button.  To use the image native size leave both boxes blank.';
$helptxt['treas_don_name_prompt'] = '<b>Prompt to use Username</b><br />Enter the text for the prompt asking a user if they want their name revealed.';
$helptxt['treas_don_name_yes'] = '<b>Username Display - Yes</b><br />Enter the text for a <b>YES</b> selection at the prompt for users to have their name revealed.';
$helptxt['treas_don_name_no'] = '<b>Username Display - No</b><br />Enter the text for a <b>NO</b> selection at the prompt for users to not have their name revealed.';
$helptxt['treas_don_show_amt'] = '<b>Reveal Amounts</b><br />Should the Treasury module reveal the amount of each donation?  You need to decide this based on how you assess your user group will respond best.';
$helptxt['treas_don_show_date'] = '<b>Reveal Dates</b><br />Should the Treasury module reveal the date of each donation?';
$helptxt['treas_don_show_gross'] = '<b>Show Gross Only</b><br />This option allows you to display gross donations only i.e. the PayPal fees and net figures are not displayed, and the above/below goal figure is adjusted accordingly.';
$helptxt['treas_don_show_info_center'] = '<b>Show in Info Center</b><br />This allows your donations to also be displayed in the Forums Info Center - NOT ACTIVE YET.';
$helptxt['treas_don_text_title'] = '<b>Donations Page Title</b><br />Something short and simple, like expressing your appreciation.';
$helptxt['treas_don_text'] = '<b>Donations Page Title</b><br />This is where you can appeal to your users and your community for donations.<br />Suggestion: Explain why you need donations, what you do with the money and how you manage it. Make them comfortable that they are not throwing their money away, even though they probably are LMAO.';
$helptxt['treas_don_amt_checked'] = '<b>Default Donation Amount Checked</b><br />The Treasury module provides a list of suggested donations amounts.  You can customize the above list.  In this box, specify the number corresponding to which of the amounts listed should be checked by default.';
$helptxt['treas_goal'] = '<b>Monthly Donation Targets</b><br />Enter the dollar amounts for the donation goal for each month.';
$helptxt['treas_don_amount'] = '<b>Suggested Donation Amounts</b><br />The Treasury module provides a list of suggested donations amounts.  You can customize this list below.  You will also select a default value in <b>Which Donation Amount is default?</b>';
$helptxt['treas_show_registry'] = '<b>Show Registry Info</b><br />Should you wish to maintain total transparency of your income from Treasury and expenditure, setting this option to Yes will reveal a registry summary to your Donors only, with a small summary at the bottom of the main Treasury page.<br /><br />You may wish to review/edit your item Names because Treasury will group the data according to each Name.';

// PayPal setup parameters
$helptxt['treas_receiver_email'] = '<b>PayPal Receiver Email</b><br /><b>!!VERY IMPORTANT!!</b><br />This is the email address registered in your PayPal account to receive money with.  NOTE: Create an email address specifically and only for receiving donations, i.e. donations@yoursite.com.<br />The Donormeter will list any payments to the email you list here.';
$helptxt['treas_pp_ty_url'] = '<b>PayPal Return Page</b><br />This is the file that users will be returned to when they complete a donation.  It is preset, but you may choose to make some customized return of your own.  Just make sure you comply with PayPal\'s requirements for a return page.';
$helptxt['treas_pp_notify_url'] = '<b>PayPal Notify Page</b><br />This box shows the link to the IPN recorder. You can click on this link to verify that the IPN recorder is functioning correctly.  This does not guarantee that your PayPal setup is correct - it just confirms basic Treasury functionality is good.  We strongly recommend you leave this file alone.';
$helptxt['treas_pp_cancel_url'] = '<b>PayPal Cancel Page</b><br />Enter a web page that users will be taken to when they cancel their payment - default is the main Treasury page.';
$helptxt['treas_pp_itemname'] = '<b>PayPal Item Name</b><br />Enter the IPN item name used for your donations - this is sent to PayPal and returned to you.';
$helptxt['treas_pp_item_num'] = '<b>PayPal Item Number</b><br />Enter the IPN item number used for your donations - this is sent to PayPal and returned to you.';
$helptxt['treas_pp_currency'] = '<b>PayPal Currencies</b><br />Select from USD EUR GBP CAD YEN AUD - these options are hard-coded into a selectable drop-down list to avoid user errors.';
$helptxt['treas_pp_currency2'] = '<b>Accept Other Currencies</b><br />Accept donations in currencies other than your own - recommended, but the choice is always yours.  If you don\'t accept them, you will most likely have to enter every donation manually into Treasury.<br />Make sure you also set Payment Receiving Preferences accordingly in your PayPal account (see the <a href="javascript:window.open(\'%1$s?action=admin;area=treasury;sa=readme#PaypalSetup\'); self.close();" class="new_win"><b>ReadMe</b></a>).';
$helptxt['treas_pp_sandbox'] = '<b>PayPal Sandbox Flag</b><br />For development, adds sandbox to the url for offline paypal testing.  Setting up a sandbox account is entirely between you and PayPal.  Oh, and should you inadvertently set this to "Yes", your donors will not be able to donate.';
$helptxt['treas_pp_image_url'] = '<b>Site Logo Image</b><br />You can have a custom image displayed at the top of the PayPal screen when your users are donating.  Enter the name of the image to display here.<br />NOTE: If you enter a file from a non-secure server your users will continually be warned that they are about to display secure and non-secure information.';
$helptxt['treas_pp_get_addr'] = '<b>User Postal Address</b><br />Would you like PayPal to gather the user shipping address?  Users can opt out of this.  This could be useful if you wanted to send them holiday cards or something.  However, it is preferable that you don\'t use it - many people are suspicious of sites requesting information like this, and it may reduce your donations.';
$helptxt['treas_ipn_dbg_lvl'] = '<b>IPN Debugging Levels</b><br />There is an IPN logging feature which has three log levels:<br />1) OFF<br />2) Log only Errors<br />3) Log everything<br />This log is stored in the \"log_treasurey\" table.';
$helptxt['treas_ipn_log_entries'] = '<b># IPN Log Entries</b><br />Enter the maximum number of log entries to keep in the log table -   default is set to 50.';

// TinyPortal block settings
$helptxt['treas_dm_title'] = '<b>Title of Block</b><br />Enter a customized title for the TinyPortal block title.<br />NOTE: This is not not to be confused with the text displayed on the main Treasury page - this is set separately in Main Config.';
$helptxt['treas_dm_comments'] = '<b>Special Comments</b><br />For the TinyPortal block - enter comments, image, whatever you want here, much like the prologue on the main page - handy if you wish to suppress the goals section.';
$helptxt['treas_dm_name_length'] = '<b>Username Length</b><br />The username length, in characters, shown in the TinyPortal block can be controlled to avoid width issues.';
$helptxt['treas_dm_num_don'] = '<b>Donors Listed in Block</b><br />Enter the number of donors that should be listed in the TinyPortal block.<br />-1 = List none<br /> 0 = Unlimited<br /> # = The max number to list<br />Donors are always listed from newest to oldest from the top down.';
$helptxt['treas_dm_show_date'] = '<b>Show Date in Block</b><br />Should the TinyPortal block display the Date that each donation was made?';
$helptxt['treas_dm_show_amt'] = '<b>Show Amount in Block</b><br />Should the TinyPortal block display the Amount of each donation?  You need to decide this based on how you assess your user group will respond best.';
$helptxt['treas_dm_button'] = '<b>Donate Button in Block</b><br />Enter a filename for the image used in the TinyPortal block - this image must be located in the Default theme images directory.';
$helptxt['treas_dm_img_width'] = '<b>Block Button Dimensions</b><br />Restrict the image dimensions for the TinyPortal block donate button.  To use the image native size leave both boxes blank.';
$helptxt['treas_dm_img_height'] = '<b>Block Button Dimensions</b><br />Restrict the image dimensions for the TinyPortal block donate button.  To use the image native size leave both boxes blank.';

// Event based donation system.
$helptxt['treas_event_active'] = '<b>Active Event</b><br />This will show "Not Active" until you add an event, then the titles of all events you add will be shown in this drop-down list.<br />Activating an event will change the display of the Treasury page title and text, and will switch you from a time-based donation summary to an event-based summary.';
$helptxt['treas_events_title'] = '<b>Event Title</b><br />This is limited to 25 characters and will be displayed in the active drop-down list, and also as the title at the top of the main Treasury page.';
$helptxt['treas_events_descr'] = '<b>Description</b><br />Here you describe what this donation campaign is all about - when an event is activated, this will automatically replace the normal page text you have for time-based donations.<br />So make your sales pitch is  compelling and relevant to your objective for this donation campaign.<br /><br /><b>Note</b>: if you want line breaks in this text, you MUST use &lt;br / &gt; - carriage returns will stop the script from allowing edit.';
$helptxt['treas_events_target'] = '<b>Target</b><br />Naturally, the amount you wish to receive during this donation campaign.';
$helptxt['treas_events_actual'] = '<b>Actual</b><br />The actual amount of money received so far during this donation campaign.  Treasury tags each donation received with the event ID so it can keep track.';
$helptxt['treas_events_start'] = '<b>Date Start</b><br />Format <b>must</b> be YYYY-mm-dd<br />More for your record keeping and for sorting than anything else at this stage.<br />To enter a date it is best that you click the calendar icon and select a date - then Events can be sure of getting the format it needs for storage.  The script will not work if you have entered an incorrect format.';
$helptxt['treas_events_end'] = '<b>Date End</b><br />Format <b>must</b> be YYYY-mm-dd or empty.<br />If left empty, that\'s fine - Events will simply take empty as meaning <b>Open</b> and will state that on the main page.<br />To enter a date it is best that you click the calendar icon and select a date - then Events can be sure of getting the format it needs for storage.  The script will not work if you have entered an incorrect format.';

