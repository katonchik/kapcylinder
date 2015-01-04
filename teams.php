<?php
	if($settings['enable_autolotting'])
	{
		include("include/teamsauto.php");
	}
	else
	{
		include("include/teamlist.php");
	}

?>
