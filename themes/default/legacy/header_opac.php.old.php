<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
	require_once(REL(__FILE__, "../../model/Members.php"));
	require_once(REL(__FILE__, "header_top.php"));
?>
<!--div id="banner">
<div id="logo">
	<img src="logo.png" /-->
	<?php
//		if (Settings::get('library_image_url') != "") {
//			echo "<img alt=\"".H(Settings::get('library_name'))."\" src=\"".H(Settings::get('library_image_url'))."\" border=\"0\" />";
//		}
	?>
<!--/div>
<div id="library_name"-->
	<?php
//		if (Settings::get('use_image_flg') == 'N') {
//			echo H(Settings::get('library_name'));
//		}
	?>
<!--/div>
<div id="library_hours"><?php echo H(Settings::get('library_hours')) ?></div>
<div id="library_phone"><?php echo H(Settings::get('library_phone')) ?></div>
</div-->

<div id="sidebar">
	<h3 class="staff_head">
			<?php echo T("%library%:<br />OPAC Interface", array('library'=>H(Settings::get('library_name')))) ?>
	</h3>
	<div id="library_hours"><?php echo T(Settings::get('library_hours')) ?></div>
	<div id="library_phone"><?php echo H(Settings::get('library_phone')) ?></div>
 	<hr width="95%" />

<?php Nav::display($nav); ?>

	<!--div id="sidebar_login"-->
<?php
/*
		$mbr = NULL;
		if (isset($_SESSION['authMbrid'])) {
			$members = new Members;
			$mbr = $members->maybeGetOne($_SESSION['authMbrid']);
		}

		if ($mbr) {
			echo 'Hello, '.H($mbr['first_name']).' (<a href="../opac/logout.php">logout</a>)';
		} else {
*/
?>
			<!--form action="../opac/login.php" method="POST">
				<!--div class="login_id">Login ID: <input name="login" /></div>
				<!--div class="password">Password: <input type="password" name="password" /></div-->
				<!--input class="button" type="submit" value="Log in" /-->
				<!--a class="button" href="../opac/register.php">Register</a-->
			<!--/form-->
<?php //} ?>

<?php// if ($nav == "request") { ?>
 			<!--hr width="95%" />
 			<iframe src="../shared/calendar.php" height="100%" width="95%" frameborder="0">
			<p>The calendar cannot be displayed with your current browser configuration.</p>
 			</iframe-->
<?php// } ?>
	<!--/div-->
	
	<div id="footer">
		<a href="http://obiblio.sourceforge.net/">
			<img src="../images/powered_by_openbiblio.gif" width="125" height="44" border="0" />
		</a>
		<br />
		Powered by OpenBiblio version <?php echo H(OBIB_CODE_VERSION);?><br />
		OpenBiblio is free software, copyright by its authors.<br />
		Get <a href="../COPYRIGHT.html">more information</a>.
	</div>
</div>

<!-- **************************************************************************************
		 * beginning of main body
		 **************************************************************************************-->
<div id="content">
	<?php
		if (isset($_GET['msg'])) {
			echo "<div class=\"msg\">".H($_GET['msg'])."</div>";
		}