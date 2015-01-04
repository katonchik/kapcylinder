<?php
	include("include/auth.inc.php");
	include("../settings.php");
	include("../lang/$hl.lang");
	include("../include/functions.inc.php");
	include("../include/constants.inc.php");
	include("../include/Player.class.php");

	$mysqli=dbconnect($host, $db_name, $db_username, $db_password);
	$settings = GetSettings($mysqli);
	$tournid = getUpcomingTournamentId($mysqli);

?>