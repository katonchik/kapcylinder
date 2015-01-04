<?php
	$page_access_level = 2;
	include("include/admin_includes.inc.php");

	$player_id = $_GET['player_id'];
	$basket = $_GET['basket'];
	$query = "UPDATE tournament_player SET basket=$basket WHERE player_id=$player_id AND tournament_id=$tournid";
	$result = $mysqli->query($query);
			
	$query = "SELECT * FROM players WHERE id=" . $player_id . " LIMIT 1";
	$result = $mysqli->query($query);
	if($result)
	{
		$row = $result->fetch_assoc();

//		$news_text = $row['name'] . " is now in basket " . $basket;
//		WriteLog($mysqli, BASKET, $news_text . " (by ${_SESSION['logged_admin']})", $player_id);
		$json_arr = array(
			'successful' => true,
			'msg' => $news_text,
		);
	}
	else
	{
//		$msg_text = "Failed to move player {$player_id} to basket {$basket}";
		$json_arr = array(
			'successful' => false,
			'msg' => $msg_text,
		);
	}


	echo json_encode($json_arr);

?>