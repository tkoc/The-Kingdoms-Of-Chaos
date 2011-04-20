<?php

/**
 * German language (informal, "Du") for the Config Manager
 *
 * If your language is not/only partially translated or you found an error/typo,
 * have a look at the following files:
 * - "/lib/tpl/monobook/lang/<your lang>/lang.php"
 * - "/lib/tpl/monobook/lang/<your lang>/settings.php"
 * If they are not existing, copy and translate the English ones. And don't
 * forget to mail the translation to me,
 * Andreas Haerter <andreas.haerter@dev.mail-node.com>. Thanks :-D.
 *
 *
 * LICENSE: This file is open source software (OSS) and may be copied under
 *          certain conditions. See COPYING file for details or try to contact
 *          the author(s) of this file in doubt.
 *
 * @license GPLv2 (http://www.gnu.org/licenses/gpl2.html)
 * @author Andreas Haerter <andreas.haerter@dev.mail-node.com>
 * @link http://www.dokuwiki.org/template:monobook
 * @link http://www.dokuwiki.org/config:lang
 * @link http://www.dokuwiki.org/devel:configuration
 */


//check if we are running within the DokuWiki environment
if (!defined("DOKU_INC")){
    die();
}

//discussion pages
$lang["monobook_discuss"]    = "Diskussions-Tabs/Seiten benutzen?";
$lang["monobook_discuss_ns"] = "Falls ja, folgenden ':namensraum:' als Wurzel für Diskussionen nutzen:";

//site notice
$lang["monobook_sitenotice"]          = "Seitenübergreifenden Hinweis einblenden?";
$lang["monobook_sitenotice_location"] = "Falls ja, folgende wiki-Seite als Hinweis verwenden:";

//navigation
$lang["monobook_navigation"]          = "Navigation anzeigen?";
$lang["monobook_navigation_location"] = "Falls ja, folgende wiki-Seite als Navigation verwenden:";

//custom copyright notice
$lang["monobook_copyright"]          = "Copyright-Hinweis einblenden?";
$lang["monobook_copyright_default"]  = "Falls ja, Standard-Copyright-Hinweis nutzen?";
$lang["monobook_copyright_location"] = "Falls nicht den Standard-Copyright-Hinweis, folgende wiki-Seite als Copyright-Hinweis verwenden:";

//search form
$lang["monobook_search"] = "Suchformular anzeigen?";

//toolbox
$lang["monobook_toolbox"]               = "Toolbox/Werkzeuge anzeigen?";
$lang["monobook_toolbox_default"]       = "Falls ja, Standard-Toolbox nutzen?";
$lang["monobook_toolbox_default_print"] = "Falls die Standard-Toolbox genutzt wird, zusätzlichen Druckversionslink anzeigen?";
$lang["monobook_toolbox_location"]      = "Falls nicht die Standard-Toolbox, folgende wiki-Seite als Toolbox verwenden:";

//donation link/button
$lang["monobook_donate"]          = "'Spenden'-Link/button anzeigen?";
$lang["monobook_donate_default"]  = "Falls ja, Standard-Spendenziel nutzen?";
$lang["monobook_donate_url"]      = "Falls nicht Standard-Spendenziel, folgende URL für Spenden benutzen:";

//other stuff
$lang["monobook_mediamanager_embedded"] = "Mediamanager ins gewöhnliche Layout eingebetten anzeigen?";
$lang["monobook_breadcrumbs_position"]  = "Position der breadcrumb-Navigation (sofern aktiviert):";
$lang["monobook_youarehere_position"]   = "Position der 'Sie befinden sich hier'-Navigation (sofern aktiviert):";
$lang["monobook_cite_author"]           = "Zu nutzender Autorenname in 'Artikel zitieren':";
$lang["monobook_loaduserjs"]            = "Datei 'monobook/user/user.js' laden?";

