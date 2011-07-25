<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

	require_once("../shared/common.php");
	session_cache_limiter(null);

	$tab = "admin";
	$nav = "member_fields";
	$focus_form_name = "newfieldform";
	$focus_form_field = "code";

	require_once(REL(__FILE__, "../functions/inputFuncs.php"));
	require_once(REL(__FILE__, "../shared/logincheck.php"));
	require_once(REL(__FILE__, "../shared/get_form_vars.php"));
	Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>T("Add custom member field")));

?>

<h3><?php echo T("Add custom member field"); ?></h3>
<br />
<form name="newfieldform" method="post" action="../admin/member_fields_new.php">
<fieldset>
<table class="primary">
	<tr>
		<th colspan="2" nowrap="yes" align="left">
		</th>
	</tr>
	<tr>
		<th nowrap="true" class="primary">
			<?php echo T("Code:"); ?>
		</th>
		<td valign="top" class="primary">
			<?php printInputText("code",40,40,$postVars,$pageErrors); ?>
		</td>
	</tr>
	<tr>
		<th nowrap="true" class="primary">
			<?php echo T("Description:"); ?>
		</th>
		<td valign="top" class="primary">
			<?php printInputText("description",40,40,$postVars,$pageErrors); ?>
		</td>
	</tr>
	<tr>
		<td align="center" colspan="2" class="primary">
			<input type="submit" value="<?php echo T("Submit"); ?>" class="button" />
			<input type="button" onclick="self.location='../admin/member_fields_list.php'" value="<?php echo T("Cancel"); ?>" class="button" />
		</td>
	</tr>
</table>
</fieldset>
</form>

<?php

	 ;