<?php

/**
 * Italian language for the Config Manager
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
 * @author Luigi Micco <l.micco@tiscali.it>
 * @link http://www.dokuwiki.org/template:monodoku
 * @link http://www.dokuwiki.org/config:lang
 * @link http://www.dokuwiki.org/devel:configuration
 */


//check if we are running within the DokuWiki environment
if (!defined("DOKU_INC")){
    die();
}

//discussion pages
$lang["monodoku_discuss"]    = "Usare linguetta discussioni?";
$lang["monodoku_discuss_ns"] = "Se si, usa questo ':namespace:' come radice per le discussioni:";

//site notice
$lang["monodoku_sitenotice"]          = "Mostra annunci generali?";
$lang["monodoku_sitenotice_location"] = "Se si, usa la seguente pagina wiki come annuncio:";

//navigation
$lang["monodoku_navigation"]          = "Mostra pannello di navigazione?";
$lang["monodoku_navigation_location"] = "Se si, usa la seguente pagina wiki come pannello di navigazione:";

//custom copyright notice
$lang["monodoku_copyright"]          = "Mostra avviso di copyright?";
$lang["monodoku_copyright_default"]  = "Se si, usa l'avviso di copyright predefinito?";
$lang["monodoku_copyright_location"] = "Se non usi il predefinito, usa la seguente pagina wiki come avviso di copyright:";

//search form
$lang["monodoku_search"] = "Mostra casella di ricerca?";

//toolbox
$lang["monodoku_toolbox"]               = "Mostra pannello strumenti?";
$lang["monodoku_toolbox_default"]       = "Se si, usa il pannello predefinito?";
$lang["monodoku_toolbox_default_print"] = "Se utilizzi il pannello predefinito, mostra link per versione stampabile?";
$lang["monodoku_toolbox_location"]      = "Se non usi il predefinito, usa la seguente pagina wiki come pannello degli strumenti:";

//donation link/button
$lang["monodoku_donate"]          = "Mostra link/pulsante per le donazioni?";
$lang["monodoku_donate_default"]  = "Se si, usa l'indirizzo URL predefinito?";
$lang["monodoku_donate_url"]      = "Se non predefinito, usa il seguente indirizzo URL per le donazioni:";

//other stuff
$lang["monobook_mediamanager_embedded"] = "Visualizzare mediamanager incluso nello schema dello stile?";
$lang["monodoku_breadcrumbs_position"]  = "Posizione del pannello breadcrumb (se abilitato):";
$lang["monodoku_youarehere_position"]   = "Posizione del pannello 'Tu sei qui' (se abilitato):";
$lang["monodoku_cite_author"]           = "Nome autore in 'Cita questo articolo':";
$lang["monodoku_loaduserjs"]            = "Carica 'monodoku/user/user.js'?";

