<?php

/**
 * Default tab configuration of the "monobook" DokuWiki template
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
  If you want to add some own tabs, have a look at the README of this template
  and "/user/tabs.php". You have been warned! 
 *****************************************************************************/


//check if we are running within the DokuWiki environment
if (!defined("DOKU_INC")){
    die();
}


//note: The tabs will be rendered in the order they were defined. Means: first
//      tab will be rendered first, last tab will be rendered at last.


//article tab
//ATTENTION: "ca-nstab-main" is used as css id selector!
if (substr(getID(), 0, strlen("wiki:user:")) !== "wiki:user:"){
    $_monobook_tabs["ca-nstab-main"]["text"] = $lang["monobook_tab_article"];
}else{
    $_monobook_tabs["ca-nstab-main"]["text"] = $lang["monobook_tab_userpage"];
}
$_monobook_tabs["ca-nstab-main"]["accesskey"] = "V";
if ($monobook_context !== "discuss"){ //$monobook_context was defined within main.php
    $_monobook_tabs["ca-nstab-main"]["wiki"]  = ":".getID();
    $_monobook_tabs["ca-nstab-main"]["class"] = "selected";
}else{
    $_monobook_tabs["ca-nstab-main"]["wiki"]  = ":".substr(getID(), strlen(tpl_getConf("monobook_discuss_ns"))-1);
}


//discussion tab
//ATTENTION: "ca-talk" is used as css id selector!
if (tpl_getConf("monobook_discuss")){
    $_monobook_tabs["ca-talk"]["text"] = $lang["monobook_tab_discussion"];
    if ($monobook_context === "discuss"){ //$monobook_context was defined within main.php
        $_monobook_tabs["ca-talk"]["wiki"]  = ":".getID();
        $_monobook_tabs["ca-talk"]["class"] = "selected";
    }else{
        $_monobook_tabs["ca-talk"]["wiki"] = tpl_getConf("monobook_discuss_ns").getID();
    }
}


//edit/create/show source tab
//ATTENTION: "ca-edit" is used as css id selector!
$_monobook_tabs["ca-edit"]["href"]      = wl(getID(), array("do" => "edit", "rev" => (int)$rev), false, "&");
$_monobook_tabs["ca-edit"]["accesskey"] = "E";
if ($ACT === "edit"){ //$ACT comes from DokuWiki core
    $_monobook_tabs["ca-edit"]["class"] = "selected";
}
if (!empty($INFO["writable"])){ //$INFO comes from DokuWiki core
    if (!empty($INFO["draft"])){
        $_monobook_tabs["ca-edit"]["href"] = wl(getID(), array("do" => "draft", "rev" => (int)$rev), false, "&");
        $_monobook_tabs["ca-edit"]["text"] = $lang["btn_draft"]; //language comes from DokuWiki core
    }else{
        if(!empty($INFO["exists"])){
            $_monobook_tabs["ca-edit"]["text"] = $lang["btn_edit"]; //language comes from DokuWiki core
        }else{
            $_monobook_tabs["ca-edit"]["text"] = $lang["btn_create"]; //language comes from DokuWiki core
        }
    }
}else{
    $_monobook_tabs["ca-edit"]["text"]      = $lang["btn_source"]; //language comes from DokuWiki core
    $_monobook_tabs["ca-edit"]["accesskey"] = "E";
}


//old versions/revisions tab
//ATTENTION: "ca-history" is used as css id selector!
$_monobook_tabs["ca-history"]["text"]      = $lang["btn_revs"]; //language comes from DokuWiki core
$_monobook_tabs["ca-history"]["href"]      = wl(getID(), array("do" => "revisions"), false, "&");
$_monobook_tabs["ca-history"]["accesskey"] = "O";
if ($ACT === "revisions"){ //$ACT comes from DokuWiki core
    $_monobook_tabs["ca-history"]["class"] = "selected";
}


//(un)subscribe tab
//ATTENTION: "ca-watch" is used as css id selector!
if (!empty($conf["useacl"]) &&
    !empty($conf["subscribers"]) &&
    !empty($loginname)){ //$loginname was defined within main.php
    if (empty($INFO["subscribed"])){ //$INFO comes from DokuWiki core
        $_monobook_tabs["ca-watch"]["href"] = wl(getID(), array("do" => "subscribe"), false, "&");
        $_monobook_tabs["ca-watch"]["text"] = $lang["btn_subscribe"]; //language comes from DokuWiki core
    }else{
        $_monobook_tabs["ca-watch"]["href"] = wl(getID(), array("do" => "unsubscribe"), false, "&");
        $_monobook_tabs["ca-watch"]["text"] = $lang["btn_unsubscribe"]; //language comes from DokuWiki core
    }
}



/******************************************************************************
 ********************************  ATTENTION  *********************************
         DO NOT MODIFY THIS FILE, IT WILL NOT BE PRESERVED ON UPDATES!        
 ****************************************************************************** 
  If you want to add some own tabs, have a look at the README of this template
  and "/user/tabs.php". You have been warned! 
 *****************************************************************************/

