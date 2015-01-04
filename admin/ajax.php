<?php
	//This file is part of the sorting tool
	include("include/auth.inc.php");
	if(!$_POST['data'])
	{
		die("Invalid query");
	}
	else
	{
		include("../settings.php");
		include('../include/functions.inc.php');
		$mysqli=dbconnect($host, $db_name, $db_username, $db_password);

		$json_string = str_replace("\\", "", $_POST['data']);
		$players = json_decode($json_string);

		$order = 1;
		foreach($players as $player)
		{
			$player_details = explode("::", $player);
			$player_id = $player_details[0];
			$player_team = $player_details[1];

			if($player_team == $prev_player_team)
			{
				$order++;
			}
			else
			{
				$order = 1;
			}

			$query = "UPDATE tournament_player SET hat_team = $player_team, experience = $order WHERE player_id = $player_id; ";
			$result = $mysqli->query($query);

			if($result)
			{
				echo "Update successful\r\n";
			}
			else
			{
				echo $mysqli->errno . ": " . $mysqli->error . "\n";
			}

			$prev_player_team = $player_team;
		}

	}
?>