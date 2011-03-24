<?php
/*******************************************************************************
* This program is free software; you can redistribute it and/or modify         *
* it under the terms of the GNU General Public License as published by         *
* the Free Software Foundation; either version 2 of the License.               *
* $Source: /0cvs/TreasurySMF/TreasuryReadme.php,v $                            *
* $Revision: 1.29 $                                                            *
* $Date: 2010/01/25 04:18:19 $                                                 *
* SMF2 Treasury Version 2.03 by Resourcez at resourcez.biz                     *
*******************************************************************************/
if (!defined('SMF'))
	die('Hacking attempt...');
loadLanguage('Treasury');
global $scripturl, $boardurl, $modSettings;

echo '
<div class="windowbg">
<div style="text-align:center; padding:5px;" class="titlebg">Information Menu</div>
<div  style="text-align:center; text-decoration:blink;"><a href="http://resourcez.biz /index.php?action=treasury">You can support Treasury development here</a>.</div>
';
if (!$resopen = @fsockopen ('tcp://resourcez.biz', 80, $errno, $errstr, 2))
{
	echo '<div class="tborder">Sorry, Resourcez site not available.</div>';
}
else
{
	$treasinfo = file_get_contents('http://resourcez.biz/treasnews2.php');
	if ($treasinfo)
	{
		$update_version = substr($treasinfo, 0, 4);
		echo '<div style="padding:3px;">';
		echo 'Your version is ', $modSettings['treasury_version'], '&nbsp;-&nbsp; the latest version ', $update_version, ' is <a href="http://custom.simplemachines.org/mods/index.php?mod=916"><b>Available Here</b></a>';
		echo '</div>';
		echo '<div style="border:1px solid pink; padding:3px; height:150px; overflow:auto; font-size:10px;">';
		echo $treasinfo;
		echo '</div>';
	}
	else
	{
		echo '<div class="tborder">Sorry, update info not available.</div>';
	}
	@fclose ($resopen);
}
$smf_treas = substr($modSettings['smfVersion'], 0, 1);
$admin_treas = ($smf_treas == 1) ? '?action=treasuryadmin;' : '?action=admin;area=treasury;';
echo'
<ol>
<li><a href="index.php', $admin_treas, 'sa=readme#ModuleSetup">Treasury Module Setup</a></li>
<li><a href="index.php', $admin_treas, 'sa=readme#PaypalSetup">PayPal Account Setup</a></li>
<li><a href="index.php', $admin_treas, 'sa=readme#OpNotes">Operational Notes</a></li>
<li><a href="index.php', $admin_treas, 'sa=readme#UnInstall">Un-Install Notes</a></li>
<li><a href="index.php', $admin_treas, 'sa=readme#ToDo">To Do</a></li>
<li><a href="index.php', $admin_treas, 'sa=readme#Support">Support</a><br /><br /></li>
</ol>
</div>
<br />
<a id="ModuleSetup"></a>
<div class="windowbg">
<div style="text-align:center; padding:5px;" class="titlebg">Treasury Module Setup</div>
<ol>
<li>Since you are here, I presume you have installed successfully, and followed the instructions provided in the package notes.<br /><br /></li>
<li>In the <a href="', $scripturl.$admin_treas, 'sa=configpaypal" style="text-decoration:underline;"><b>PayPal Config</b></a> tab, ensure you enter your own PayPal email ID (and your PayPal primary currency) - it simply will not work with the default email ID from install.<br /><br /></li>
<li>If you know how to use the PayPal "sandbox", you  can test all you like after setting up a (free) developer account with PayPal.  The sandbox was used to debug this module.<br />If not, get a friend to make some test donations, unless you have a second PayPal account to use.  You can refund these through your PayPal account, without any fees or penalties.<br /><br /></li>
<li>Check the other options in admin for setting up the <a href="', $scripturl.$admin_treas, 'sa=config" style="text-decoration:underline;"><b>Module</b></a> appearance (and <a href="', $scripturl.$admin_treas, 'sa=configblock" style="text-decoration:underline;"><b>Block</b></a> for Portal users).<br /><br /></li>
<li>You will need to setup your viewing and admin <a href="', $scripturl.(substr($modSettings['smfVersion'], 0, 1) == 1 ? '?action=permissions' : '?action=admin;area=permissions'), '" style="text-decoration:underline;"><b>permissions</b></a> for each member group.<br /><br /></li>';
echo '<li>Should you wish to change the name that appears in your menu bar from Donations to something else, you can edit the value for $txt[\'treasury_menu\'] in "Themes/default/languages/Modifications.english.php".<br /><br /></li>
<li>Other Treasury language defines can be found in "Themes/default/languages/Treasury.english.php".<br /><br /></li>
';
echo '<li>Your monthly goals are displayed on the <a href="', $scripturl, '?action=treasury" style="text-decoration:underline;"><b>Treasury</b></a> page.<br /><br /></li>';
if ($smf_treas == 1) {
	echo '<li>Portal users (TinyPortal, SimplePortal, PortaMx) can setup the Donation Block by following <a href="http://resourcez.biz /index.php?topic=92.0"><b>these instructions</b></a>.<br /><br /></li>';
}
echo '</ol></div>
<br />
<a id="PaypalSetup"></a>
<div class="windowbg">
<div style="text-align:center; padding:5px;" class="titlebg">PayPal Account Setup</div>
<br />If you choose to ignore this advice, ask PayPal - they get paid to answer your questions.
I don\'t mean to be rude, but the info is provided for a good reason - so you can help yourself.<br /><br />
If you have problems and do not have full access to the PayPal account profile, do not contact us - it is impossible to problem solve when you cannot directly verify account settings or changes.<br /><br />
I leave your selection of a PayPal account entirely up to you - I will <b>not</b> provide advice on this issue.
After a previous unpleasant experience, I will not leave myself open to absurd threats of legal action.<br />
<ol>
<li>Treasury requires IPN settings in your PayPal account "Profile".<br />
&#8226; Set \'IPN\' to \'On\' in "Instant Payment Notification Preferences".<br/>
&#8226; This will also require a URL to be entered - anything will do, but not blank.<br />
<br />
The URL you set here is NOT important as Treasury operates from its own Notify and Return URLs which ignore, and are independent of, your PayPal settings.<br />
&#8226; <b>Why?</b> If you already have an IPN setting activated for some other program, it will continue to function for that program.<br />
&#8226; <b>Example?</b> If you have already set your IPN URL so that you can use, say, Paid Subscriptions, then you can leave the URL on PayPal as it is, and still use Treasury.<br /><br /></li>
<li>You should also modify "Payment Receiving Preferences" in your "Profile" area.<br />
&#8226; Check your option for "Block payments sent to me in a currency I do not hold:".<br />
&#8226; You should set this to the second option "No, accept them and convert them...".<br />
<br /><b>Failure</b> to do this means you will have to manually confirm each payment within your PayPal account and the donation will NOT show on your site.<br /><br /></li>
<li>Treasury settings in "Website Payment Preferences".<br />
  You need to set,<br />
&#8226; Set/Leave \'Auto Return\' to \'On\', <b>repeat \'On\'</b> in "Website Payment Preferences".<br />
&#8226; Set/Leave \'Payment Data Transfer\' to \'Off\' in "Website Payment Preferences".<br /><br /></li>
<li>Settings in "Currency Balances".<br />
These shouldn\'t need changing - PayPal default is fine - a primary currency which is Open and all other currencies Closed.<br />
If you do not have special reasons for operating with multiple open currencies, DO NOT fiddle!<br />
If you do have special reasons, you <b>will not receive exchange rate and settle amounts from PayPal</b> for non-primary currencies which you have Open.<br />
You WILL have to manually edit all Treasury transactions for currencies you receive that are not your Primary Currency!<br /><br /></li>
</ol>
</div>
<br />
<a id="OpNotes"></a>
<div class="windowbg">
<div style="text-align:center; padding:5px;" class="titlebg">Operational Notes</div>
<ol>
<li>The <a href="', $scripturl.$admin_treas, 'sa=registry" style="text-decoration:underline;"><b>Registry</b></a> tab allows you to manage your site\'s <b>receipts &amp; expenses</b> with basic entries to record them.  You can also total your most recent user contributions as a single entry in the register by <a href="', $scripturl.$admin_treas, 'sa=registry" style="text-decoration:underline;"><b>Reconciling</b></a> your paypal receipts.  Should you wish to provide full disclosure to your donors, you can elect to display a summary of your Income &amp; Expenses to them.<br /><br /></li>
<li>The <a href="', $scripturl.$admin_treas, 'sa=configblock" style="text-decoration:underline;"><b>Block</b></a> is provided for Portal installations where you can have a side-block.  It allows you to display your current monthly donation goal and what funds have been received towards that goal. Display of Goals and/or Donormeter is now selectable. It also lists the users who have contributed in the current month.<br />The Treasury main page already provides all of this information.<br /><br /></li>
<li>There are help tips for admin options that describe their use.<br />Just click the question mark for a pop-up to see the descriptions.<br /><br /></li>
<li>Treasury will look at varying time periods, depending on the option you choose - monthly, quarterly, half yearly and yearly.  Alternatively, you can choose to manage donations on an event basis, with set targets for each donation campaign.<br /><br /></li>
<li>Users can view their personal donation summary through their <a href="', $scripturl, '?action=profile;u=1;sa=showDonations" style="text-decoration:underline;"><b>Profile</b></a> - viewable only by the user or admin.<br /><br /></li>
<li>Treasury will also account for any refunds that you process - they will automatically cancel out the original donation and a record will be saved in the Transaction Log.  The donor\'s profile will show the original donation as well as the refund.<br /><br /></li>
<li>Got an Internal Server <b>Error 500</b> when PayPal returns to your site?<br />Check file permissions for ', $boardurl, '/ipntreas.php are 644 or 755 (CHMOD to 644 if they are 777 or 666).<br />Similarly if you are using ', $boardurl, '/Sources/DonationBlock.php under a Portal.<br /><br /></li>
<li>You can verify that your site will respond to PayPal by <a href="', $boardurl, '/ipntreas.php?dbg=1" style="text-decoration:underline;" target="_blank"><b>clicking here</b></a>.<br />
&#8226; This will also place an entry in your <a href="', $scripturl.$admin_treas, 'sa=translog" style="text-decoration:underline;"><b>transaction log</b></a>.<br /><br /></li>
<li>If you are having problems with transactions not appearing, check the <a href="', $scripturl.$admin_treas, 'sa=translog" style="text-decoration:underline;"><b>Transaction Log</b></a> for any clues to problems.<br />
&#8226; if they pay by echeck (3 days to clear) the log will contain "pending_reason => echeck".<br /><br /></li>
<li>Whenever IPN data is not stored in your database, you will have to manually enter the data from your PayPal Email in the bottom row <a href="', $scripturl.$admin_treas, 'sa=donations" style="text-decoration:underline;"><b>here</b></a>.<br /><br /></li>
<li>Treasury accepts pending payments, like eCheck, and stores the info in the database, with status of \'Pending\'.  When the eCheck clears, it should now receive the PayPal IPN info and automatically update your datbase - otherwise, you can change status to \'Completed\' in <a href="', $scripturl.$admin_treas, 'sa=donations" style="text-decoration:underline;"><b>Donations</b></a> and the donation will appear in your goals and donor list - you will need to add data for the fee, settle amount and exchange rate.<br /><br /></li>
<li>Membergroup subscriptions - not to be confused with PayPal subscriptions.&nbsp; Auto assignment to a special Supporters group of your choice was introduced previously - now you can choose to allow that membership only for your donation duration i.e. monthly, quarterly, etc. after which their group membership will automatically expire.<br /><br />
Each subsequent donation by a given donor will simply extend the expiry date by the duration from which the donation was made.<br />
e.g. your duration is Monthly, a donation on 15th Feb will expire on 15th March.<br />
Same donor contributes again on 27th Feb, so the expiry is extended to 27th March.<br /><br /></li>
<li>Events based donations - this is an alternative to the existing time-based donation system.<br />You choose one or the other - it does <b>not support both</b> simultaneously.<br />It will only operate for one event Campaign at a time, and you must decide when to end any given campaign.<br /><br /></li>
<li>Treasury was initially designed for multi-purpose use, collating information on the basis of paypal transactions for different email addresses.<br />
&#8226; this means that the "business" field for a transaction is expected to match the "receiver email" address you specified in your <a href="', $scripturl.$admin_treas, 'sa=configpaypal" style="text-decoration:underline;"><b>PayPal Config</b></a> tab.<br />
&#8226; if the two don\'t match up, the donation will be ignored in summaries, so you need to edit the "business" field in your database \'smf_treas_donations\' table.<br />(perhaps in later versions we will put this capability to use).<br /><br /></li>
<li>The green bar below the goal summary (near the bottom of the block for Portal users) is the percent achievement of your monthly goal.<br /><br /></li>
</ol></div>
<br />
<a id="UnInstall"></a>
<div class="windowbg">
<div style="text-align:center; padding:5px;" class="titlebg">Un-Install Notes</div>
<ol>
<li>Points 2, 3 &amp; 4 apply to <b>most</b> mods, not just Treasury.</li>
<li>You must run Uninstall before upgrading so that all existing Treasury changes and files can be removed.<br />
Note: for theme changes, Uninstall will only modify the default theme.<br />
Any manual changes you made to other themes you must manually reverse yourself.<br /></li>
<li>To avoid any warnings below, it is recommended that you first uninstall mods added after Treasury,
and uninstall them in the <b>REVERSE ORDER</b> that you installed them.<br /></li>
<li>If you do have warnings below, continuing the uninstall process <b>WILL</b> create issues with your site.<br />
Use the <a href="http://resourcez.biz /PackageParser/index.php" style="text-decoration:underline;"><b>Package Parser</b></a> and check the <b>Uninstall</b> option to provide guidelines to manual removal of Treasury.<br />
Then determine what caused this issue and fix that.<br /><br /></li>
<li>Note: Uninstall will deliberately <b>NOT</b> remove the Treasury database tables.<br />
For permanent Uninstall you will need to manually <b>DROP</b> these tables from the database:<br />
- smf_log_treasurey<br />
- smf_treas_config<br />
- smf_treas_donations<br />
- smf_treas_events<br />
- smf_treas_registry<br />
- smf_treas_subscribers<br />
- smf_treas_targets<br />
(assumes you used smf_ for your prefix)<br /><br /></li>
</ol></div>
<br />
<a id="ToDo"></a>
<div class="windowbg">
<div style="text-align:center; padding:5px;" class="titlebg">To Do</div>
<ol>
<li>Should they occur, bug/security fixes only.</li>
<li>Further enhancements will be released in DonationsPro only.<br /><br /></li>
</ol></div>
<br />
<a id="Support"></a>
<div class="windowbg">
<div style="text-align:center; padding:5px;" class="titlebg">Support</div>
<ol>
<li>Bugs must be reported through the <a href="http://resourcez.biz /index.php?action=bugger" style="text-decoration:underline;"><b>Bugger</b></a> system.<br /><br /></li>
<li>Any other support is <a href="http://resourcez.biz /index.php?board=7.0" style="text-decoration:underline;"><b>Available Here</b></a>, provided you provide a website link, have full access to your PayPal account, and are prepared to provide screenshots if and when requested - we cannot work in a vacuum.<br /><br /></li>
<li>Before requesting support, make sure you check <a href="http://resourcez.biz /index.php?board=9.0" style="text-decoration:underline;"><b>Treasury FAQ</b></a>, especially if you have installation issues, most of which have absolutely nothing to do with Treasury itself.<br /><br /></li>
<li>PayPal support is from the <a href="https://www.paypal.com/IntegrationCenter/ic_home.html" target="_blank" style="text-decoration:underline;"><b>PayPal</b></a> site - there are some things you simply must learn yourself.<br /><br /></li>
</ol>
AFAIK, all SMF security requirements are met, and all bugs resolved, so please enjoy!
<br /><br />
</div>';

?>