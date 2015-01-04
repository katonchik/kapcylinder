<?php
	$page_access_level = 2;
	include("include/admin_includes.inc.php");

	if(!$_POST['id'])
	{
		die("Invalid query");
	}
	else
	{
		$element = explode("_", $_POST['id']);
		$player_id = $element[1];
		$value = $_POST['value'];
		
		$query = "UPDATE tournament_player SET accommodation_note = '$value' WHERE player_id = $player_id AND tournament_id=$tournid";
		$result = $mysqli->query($query);
		if($result)
		{
			echo $value;
			$log_category = ACCOMMODATION;
			$query = "SELECT name FROM players WHERE id = $player_id; ";
			$result = $mysqli->query($query);
			$row = $result->fetch_assoc();		
			$log_text = $row['name'] . " will be accommodated at $value (by ${_SESSION['logged_admin']}).";
			WriteLog($mysqli, $log_category, $log_text, $player_id);
		}
		else
		{
			echo "Failed to save :(";
		}
	}
?>