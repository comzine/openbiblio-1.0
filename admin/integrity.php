<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "admin";
  $nav = "integrity";

  require_once(REL(__FILE__, "../shared/logincheck.php"));

  Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));
?>
<h3><?php echo T("Check Database Integrity"); ?></h3>

<p><?php echo T('integrityMsg');?></p>

<form method="post" action="../admin/integrity_check.php">
<input type="submit" class="button" value="<?php echo T("Check Now"); ?>" />
</form>

<?php

  Page::footer();