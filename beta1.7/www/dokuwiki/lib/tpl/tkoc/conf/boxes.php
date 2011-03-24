<?php

/**
 * Default box configuration of the "monobook" DokuWiki template
 *
 *
 * LICENSE: This file is open source software (OSS) and may be copied under
 *          certain conditions. See COPYING file for details or try to contact
 *          the author(s) of this file in doubt.
 *
 * @license GPLv2 (http://www.gnu.org/licenses/gpl2.html)
 * @author Andreas Haerter <andreas.haerter@dev.mail-node.com>
 * @link http://www.dokuwiki.org/template:monobook
 * @link http://www.dokuwiki.org/devel:configuration
 */



/******************************************************************************
 ********************************  ATTENTION  *********************************
         DO NOT MODIFY THIS FILE, IT WILL NOT BE PRESERVED ON UPDATES!        
 ****************************************************************************** 
  If you want to add some own boxes, have a look at the README of this
  template and "/user/boxes.php". You have been warned! 
 *****************************************************************************/


//check if we are running within the DokuWiki environment
if (!defined("DOKU_INC")){
    die();
}


//note: The boxes will be rendered in the order they were defined. Means:
//      first box will be rendered first, last box will be rendered at last.

//toolbox
if (tpl_getConf("monobook_toolbox")){
    //headline
    $_monobook_boxes["p-tb"]["headline"] = $lang["monobook_bar_toolbox"];
    
    //content
    if (tpl_getConf("monobook_toolbox_default")){
        //define default, predefined toolbox
        $_monobook_boxes["p-tb"]["xhtml"] =  "        <ul>\n"
                                            ."          <li id=\"tb-whatlinkshere\"><a href=\"".wl(getID(), array("do" => "backlink"))."\" rel=\"nofollow\">".hsc($lang["monobook_toolbxdef_whatlinkshere"])."</a></li>\n" //we might use tpl_actionlink("backlink", "", "", hsc($lang["monobook_toolbxdef_whatlinkshere"]), true), but it would be the only toolbox link where this is possible... therefor I don't use it to be consistent
                                            ."          <li id=\"tb-upload\"><a href=\"".DOKU_BASE."lib/exe/mediamanager.php?ns=".getNS(getID())."\" rel=\"nofollow\">".hsc($lang["monobook_toolbxdef_upload"])."</a></li>\n"
                                            ."          <li id=\"tb-special\"><a href=\"".wl("", array("do" => "index"))."\" rel=\"nofollow\">".hsc($lang["monobook_toolbxdef_siteindex"])."</a></li>\n";
        //add link to a printable version? this is not needed in every case (and
        //therefore configurable) cause the print stylesheets are used automatically
        //by common browsers if the user wants to print. but often users are
        //searching for a print version instead of using the browser's printing
        //preview...
        if (tpl_getConf("monobook_toolbox_default_print")){
            $_monobook_boxes["p-tb"]["xhtml"] .= "          <li id=\"tb-print\"><a href=\"".wl(getID(), array("rev" =>(int)$rev, "mddo" => "print"))."\" rel=\"nofollow\">".hsc($lang["monobook_toolbxdef_print"])."</a></li>\n";
        }
        $_monobook_boxes["p-tb"]["xhtml"] .= "          <li id=\"tb-permanent\"><a href=\"".wl(getID(), array("rev" =>(int)$rev))."\" rel=\"nofollow\">".hsc($lang["monobook_toolboxdef_permanent"])."</a></li>\n"
                                            ."          <li id=\"tb-cite\"><a href=\"".wl(getID(), array("rev" =>(int)$rev, "mddo" => "cite"))."\" rel=\"nofollow\">".hsc($lang["monobook_toolboxdef_cite"])."</a></li>\n"
                                            ."        </ul>";
    }else{
        //we have to use a custom toolbox
        if (empty($conf["useacl"]) ||
            auth_quickaclcheck(trim(tpl_getConf("monobook_toolbox_location"), ":"))){ //current user got access?
            //get the rendered content of the defined wiki article to use as
            //custom toolbox
            $interim = tpl_include_page(tpl_getConf("monobook_toolbox_location"), false);
            if ($interim === "" ||
                $interim === false){
                //add creation/edit link if the defined page got no content
                $_monobook_boxes["p-tb"]["xhtml"] =  "<li>[&#160;".html_wikilink(tpl_getConf("monobook_toolbox_location"), hsc($lang["monobook_fillplaceholder"]." (".tpl_getConf("monobook_toolbox_location").")"), null)."&#160;]<br /></li>";
            }else{
                //add the rendered page content
                $_monobook_boxes["p-tb"]["xhtml"] =  $interim."\n";
            }
        }else{
            //we are not allowed to show the content of the defined wiki
            //article to use as custom sitenotice.
            $_monobook_boxes["p-tb"]["xhtml"] = hsc($lang["monobook_accessdenied"])." (".tpl_getConf("monobook_toolbox_location").")";
        }
    }
}


/******************************************************************************
 ********************************  ATTENTION  *********************************
         DO NOT MODIFY THIS FILE, IT WILL NOT BE PRESERVED ON UPDATES!        
 ****************************************************************************** 
  If you want to add some own boxes, have a look at the README of this
  template and "/user/boxes.php". You have been warned! 
 *****************************************************************************/

