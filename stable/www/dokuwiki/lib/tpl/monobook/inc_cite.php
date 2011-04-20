<?php

/**
 * Content for the citation page
 *
 * This file will be imported by the "main.php".
 *
 *
 * LICENSE: This file is open source software (OSS) and may be copied under
 *          certain conditions. See COPYING file for details or try to contact
 *          the author(s) of this file in doubt.
 *
 * @license GPLv2 (http://www.gnu.org/licenses/gpl2.html)
 * @author Andreas Haerter <andreas.haerter@dev.mail-node.com>
 * @link http://www.dokuwiki.org/template:monobook
 * @link http://www.dokuwiki.org/devel:templates
 */

//check if we are running within the DokuWiki environment
if (!defined("DOKU_INC")){
    die();
}

//detect rev
$rev = (int)$INFO["rev"];
if ($rev < 1){
    $rev = (int)$INFO["lastmod"];
}

//set permanent URL
$permurl = DOKU_URL.DOKU_SCRIPT."?id=".getID()."&rev=".$rev;

?>
<h1><a name="bibliographic_details" id="bibliographic_details"><?php echo hsc($lang["monobook_cite_bibdetailsfor"]); ?> &quot;<?php tpl_pagetitle(); ?>&quot;</a></h1>
<div class="level2">
  <ul>
    <li class="level1">
      <div class="li"><?php echo hsc($lang["monobook_cite_pagename"]); ?>: <?php tpl_pagetitle(); ?></div>
    </li>
    <li class="level1">
      <div class="li"><?php echo hsc($lang["monobook_cite_author"]); ?>: <?php echo tpl_getConf("monobook_cite_author"); ?></div>
    </li>
    <li class="level1">
      <div class="li"><?php echo hsc($lang["monobook_cite_publisher"]); ?>: <?php echo hsc($conf["title"]); ?>.</div>
    </li>
    <li class="level1">
      <div class="li"><?php echo hsc($lang["monobook_cite_dateofrev"]); ?>: <?php echo gmdate("j F Y H:i T", $rev); ?></div>
    </li>
    <li class="level1">
      <div class="li"><?php echo hsc($lang["monobook_cite_dateretrieved"]); ?>: <?php echo gmdate("j F Y H:i T"); ?></div>
    </li>
    <li class="level1">
      <div class="li"><?php echo hsc($lang["monobook_cite_permurl"]); ?>: <a href="<?php echo hsc($permurl); ?>" title="<?php echo hsc($permurl); ?>"><?php echo hsc($permurl); ?></a></div>
    </li>
    <li class="level1">
      <div class="li"><?php echo hsc($lang["monobook_cite_pageversionid"]); ?>: <?php echo hsc($rev); ?></div>
    </li>
  </ul>
  <p>
    <?php echo hsc($lang["monobook_cite_checkstandards"]); ?>
  </p>
</div>


<h2><a name="citation_styles_for" id="citation_styles_for"><?php echo hsc($lang["monobook_cite_citationstyles"]); ?> &quot;<?php tpl_pagetitle(); ?>&quot;</a></h2>

<h3><a name="apa_style" id="apa_style">APA</a></h3>
<div class="level3">
  <p>
     <?php echo tpl_getConf("monobook_cite_author"); ?> (<?php echo gmdate("Y", $rev); ?>). <?php tpl_pagetitle(); ?>. <?php echo hsc($conf["title"]); ?>. <?php echo hsc($lang["monobook_cite_retrieved"])." ".gmdate("H:i, j F, Y")." ".hsc($lang["monobook_cite_from"]); ?> <a href="<?php echo hsc($permurl); ?>" title="<?php echo hsc($permurl); ?>"><?php echo hsc($permurl); ?></a>.
  </p>
</div>

<h3><a name="mla_style" id="mla_style">MLA</a></h3>
<div class="level3">
  <p>
     &ldquo;<?php tpl_pagetitle(); ?>.&rdquo; <u><?php echo hsc($conf["title"])?></u>. <?php echo gmdate("j M Y, H:i T", $rev); ?>. <?php echo gmdate("j M Y, H:i"); ?> &lt;<a href="<?php echo hsc($permurl); ?>" title="<?php echo hsc($permurl); ?>"><?php echo hsc($permurl); ?></a>&gt;.
  </p>
</div>

<h3><a name="mhra_style" id="mhra_style">MHRA</a></h3>
<div class="level3">
  <p>
    <?php echo tpl_getConf("monobook_cite_author");?>,
    &lsquo;<?php tpl_pagetitle(); ?>&rsquo;,
    <?php echo hsc($conf["title"])?>,
    <?php echo gmdate("j F Y, H:i T", $rev); ?>,
    &lt;<a href="<?php echo hsc($permurl); ?>" title="<?php echo hsc($permurl); ?>"><?php echo hsc($permurl); ?></a>&gt; [<?php echo hsc($lang["monobook_cite_accessed"])." ".gmdate("j F Y"); ?>]
  </p>
</div>

<h3><a name="chicago_style" id="chicago_style">Chicago</a></h3>
<div class="level3">
  <p>
    <?php echo tpl_getConf("monobook_cite_author");?>,
    &ldquo;<?php tpl_pagetitle(); ?>,
    &rdquo; <?php echo hsc($conf["title"]); ?>,
    <a href="<?php echo hsc($permurl); ?>" title="<?php echo hsc($permurl); ?>"><?php echo hsc($permurl); ?></a> (<?php echo hsc($lang["monobook_cite_accessed"])." ".gmdate("F j, Y"); ?>).
  </p>
</div>

<h3><a name="cbe_cse_style" id="cbe_cse_style">CBE/CSE</a></h3>
<div class="level3">
  <p>
    <?php echo tpl_getConf("monobook_cite_author");?>.
    <?php tpl_pagetitle(); ?> [Internet].
    <?php echo hsc($conf["title"])?>; <?php echo date("Y M j, H:i T", $rev); ?> [<?php echo hsc($lang["monobook_cite_cited"])." ".gmdate("Y M j"); ?>].
    <?php echo hsc($lang["monobook_cite_availableat"]); ?>: <a href="<?php echo hsc($permurl); ?>" title="<?php echo hsc($permurl); ?>"><?php echo hsc($permurl); ?></a>.
  </p>
</div>

<h3><a name="bluebook_style" id="bluebook_style">Bluebook</a></h3>
<div class="level3">
  <p>
    <?php tpl_pagetitle(); ?>,
    <a href="<?php echo hsc($permurl); ?>" title="<?php echo hsc($permurl); ?>"><?php echo hsc($permurl); ?></a> (<?php echo hsc($lang["monobook_lastvisited"])." ".gmdate("F j, Y"); ?>).
  </p>
</div>

<h3><a name="ama_style" id="ama_style">AMA</a></h3>
<div class="level3">
  <p>
    <?php echo tpl_getConf("monobook_cite_author");?>.
    <?php tpl_pagetitle(); ?>.
    <?php echo hsc($conf["title"])?>.
    <?php echo gmdate("F j, Y, H:i T", $rev); ?>.
    <?php echo hsc($lang["monobook_cite_availableat"]); ?>: <a href="<?php echo hsc($permurl); ?>" title="<?php echo hsc($permurl); ?>"><?php echo hsc($permurl); ?></a>.
    <?php echo hsc($lang["monobook_cite_accessed"])." ".gmdate("F j, Y"); ?>.
  </p>
</div>

<h3><a name="bibtex_entry" id="bibtex_entry">BibTeX</a></h3>
<div class="level3">
  <pre>
 @misc{ wiki:xxx,
   author = &quot;<?php echo tpl_getConf("monobook_cite_author"); ?>&quot;,
   title = &quot;<?php tpl_pagetitle(); ?> --- <?php echo hsc($conf["title"]); ?>&quot;,
   year = &quot;<?php echo gmdate("Y", $rev); ?>&quot;,
   url = &quot;<?php echo hsc($permurl); ?>&quot;,
   note = &quot;[Online; accessed <?php echo gmdate("j-F-Y"); ?>]&quot;
 }
  </pre>
  <p>
    <?php echo hsc($lang["monobook_cite_latexusepackagehint"]); ?>:
  </p>
  <pre>
 @misc{ wiki:xxx,
   author = &quot;<?php echo tpl_getConf("monobook_cite_author"); ?>&quot;,
   title = &quot;<?php tpl_pagetitle(); ?> --- <?php echo hsc($conf["title"]); ?>&quot;,
   year = &quot;<?php echo gmdate("Y", $rev); ?>&quot;,
   url = &quot;\url{<?php echo hsc($permurl); ?>}&quot;,
   note = &quot;[Online; accessed <?php echo gmdate("j-F-Y"); ?>]&quot;
 }
  </pre>
</div>
