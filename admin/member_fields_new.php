<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");
  $tab = "admin";
  $nav = "member_fields";
  $restrictInDemo = true;
  require_once(REL(__FILE__, "../shared/logincheck.php"));

  require_once(REL(__FILE__, "../classes/Dm.php"));
  require_once(REL(__FILE__, "../classes/DmQuery.php"));
  require_once(REL(__FILE__, "../functions/errorFuncs.php"));

  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************

  if (count($_POST) == 0) {
    header("Location: ../admin/member_fields_new_form.php");
    exit();
  }

  #****************************************************************************
  #*  Validate data
  #****************************************************************************
  $pageErrors = array();
  if (!isset($_POST['code']) or !$_POST['code']) {
    $pageErrors['code'] = T("This is a required field.");
  }
  if (!isset($_POST['description']) or !$_POST['description']) {
    $pageErrors['description'] = T("This is a required field.");
  }
  if (!empty($pageErrors)) {
    $_SESSION["postVars"] = $_POST;
    $_SESSION["pageErrors"] = $pageErrors;
    header("Location: ../admin/member_fields_new_form.php");
    exit();
  }

  #**************************************************************************
  #*  Insert new domain table row
  #**************************************************************************
  $dm = new Dm();
  $dm->setCode($_POST["code"]);
  $dm->setDescription($_POST["description"]);
  $dmQ = new DmQuery();
  $dmQ->connect();
  $dmQ->insert("member_fields_dm",$dm);
  $dmQ->close();

  #**************************************************************************
  #*  Destroy form values and errors
  #**************************************************************************
  unset($_SESSION["postVars"]);
  unset($_SESSION["pageErrors"]);

  #**************************************************************************
  #*  Show success page
  #**************************************************************************
  Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

  echo T("Member field, %desc%, has been added.", array('desc'=>$dm->getDescription())).'<br /><br />';
  echo '<a href="../admin/member_fields_list.php">'.T("Return to member fields list").'</a>';

  Page::footer();