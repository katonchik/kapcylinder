<?php
	$page_access_level = 1;
	include("include/admin_includes.inc.php");

	$player_id = $_GET['player_id'];
	$paid = isset($_GET['paid']) ? $_GET['paid'] : 'n';
	switch($paid)
	{
		case "y" :
			$int_value = "1";
			$log_msg = "has paid";
			$idprefix = "y";
			$linkText = "Yes";
			break;
		case "n" :
			$int_value = "0";
			$log_msg = "marked as NOT PAID";
			$idprefix = "n";
			$linkText = "No";
			break;
	}
	$query = "UPDATE tournament_player SET paid=$int_value WHERE player_id=$player_id AND tournament_id=$tournid";
	$result = $mysqli->query($query);
			
	if($result)
	{
		$query = "SELECT * FROM players WHERE id=$player_id LIMIT 1";
		$result = $mysqli->query($query);
		$row = $result->fetch_assoc();

		$log_text = $row['name'] . " " . $log_msg;
		WriteLog($mysqli, PAYMENT, $log_text . " (by ${_SESSION['logged_admin']})", $player_id);
		
		$json_arr = array(
			'successful' => true
		);
		echo json_encode($json_arr);
	}
	else
	{
		echo $query;
	}

?>