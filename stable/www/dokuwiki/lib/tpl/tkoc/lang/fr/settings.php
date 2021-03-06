<?php

/**
 * French language for the Config Manager
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
 * @author Tamara Göbes <amafinn.de>
 * @link http://www.dokuwiki.org/template:monobook
 * @link http://www.dokuwiki.org/config:lang
 * @link http://www.dokuwiki.org/devel:configuration
 */


//check if we are running within the DokuWiki environment
if (!defined("DOKU_INC")){
    die();
}

//discussion pages
$lang["monobook_discuss"]    = "Utiliser des pages/ onglets de discussion?";
$lang["monobook_discuss_ns"] = "Si oui, prendre la racine suivante ':espace de noms:' pour des discussions:";

//site notice
$lang["monobook_sitenotice"]          = "Afficher la note sur tous les sites?";
$lang["monobook_sitenotice_location"] = "Si oui, utiliser la page-wiki suivante comme note:";

//navigation
$lang["monobook_navigation"]          = "Afficher navigation?";
$lang["monobook_navigation_location"] = "Si oui, utiliser la page-wiki suivante comme navigation:";

//custom copyright notice
$lang["monobook_copyright"]          = "Afficher note de copyright?";
$lang["monobook_copyright_default"]  = "Si oui, utiliser la note standard de copyright?";
$lang["monobook_copyright_location"] = "Sinon la note standard de copyright, utiliser la page-wiki suivante comme note de copyright:";

//search form
$lang["monobook_search"] = "Afficher le formulaire de recherche?";

//toolbox
$lang["monobook_toolbox"]               = "Afficher boîte à outils/ outils?";
$lang["monobook_toolbox_default"]       = "Si oui, utiliser la boîte à outils standard?";
$lang["monobook_toolbox_default_print"] = "Si la boîte à outils est utilisée, afficher la lien de version imprimée supplémentaire?";
$lang["monobook_toolbox_location"]      = "Sinon la boîte à outils standard, utiliser la page wiki suivante comme boîte à outils:";

//donation link/button
$lang["monobook_donate"]          = "Afficher le lien/ le bouton 'faire un don'?";
$lang["monobook_donate_default"]  = "Si oui, utiliser le but de don standard?";
$lang["monobook_donate_url"]      = "Sinon le but de don standard, utiliser la url suivante pour faire un don:";

//other stuff
$lang["monobook_mediamanager_embedded"] = "Affiché le média-manager encastré dans le mis en page commun?";
$lang["monobook_breadcrumbs_position"]  = "Position de la navigation breadcrumb (si activé):";
$lang["monobook_youarehere_position"]   = "Position de la navigation 'Vous-êtes ice' (si activé)";
$lang["monobook_cite_author"]           = "Citer dans l'article le nom de l'auteur utilisé':";
$lang["monobook_loaduserjs"]            = "Charger le fichier 'monobook/user/user.js'?";

