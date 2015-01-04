<?php
	$page_access_level = 2;
	include("include/admin_includes.inc.php");

	$player_id = $_GET['player_id'];
	$accommodation = $_GET['accommodation'];
	switch($accommodation)
	{
		case "n" :
			$int_value = 1;
			$log_msg = "needs accommodation";
			$idprefix = "y";
			$linkText = "Нада!";
			break;
		case "y" :
			$int_value = 0;
			$log_msg = "does not need accommodation";
			$idprefix = "n";
			$linkText = "Не нада!";
			break;
	}
	$query = "UPDATE tournament_player SET accommodation=$int_value WHERE player_id=$player_id AND tournament_id=$tournid";
	$result = $mysqli->query($query);
			
	if($result)
	{
		$query = "SELECT * FROM players WHERE id=$player_id LIMIT 1";
		$result = $mysqli->query($query);
		$row = $result->fetch_assoc();

		$log_text = $row['name'] . " " . $log_msg;
		WriteLog($mysqli, ACCOMMODATION, $log_text . " (by ${_SESSION['logged_admin']})", $player_id);
				
		$json_arr = array(
			'successful' => true,
			'linkText' => $linkText,
			'idprefix' => $idprefix,
		);
		echo json_encode($json_arr);
	}
	else
	{
		echo $query;
	}

?>