<?php
	$page_access_level = 1;
	include("include/admin_includes.inc.php");
	include("include/header_admin.php");
	
	if($access_granted)
	{
		include("../include/teamsauto.php");
	}
	else
	{
		echo "<div class=\"errorMsg\">Access denied!</div>";
	}	
	include("include/footer_admin.php");

?>
