<?php
	$page_access_level = 2;
	include("include/admin_includes.inc.php");

	$player_id = $_GET['player_id'];
	$participation = $_GET['participation'];
	switch($participation)
	{
		case "no" :
			$int_value = 1;
			break;
		case "maybe" :
			$int_value = 2;
			break;
		case "yes" :
			$int_value = 3;
			break;
	}
	$query = "UPDATE tournament_player SET participation=$int_value WHERE player_id=$player_id AND tournament_id=$tournid";
	$result = $mysqli->query($query);
			
	$query = "SELECT * FROM players WHERE id=$player_id LIMIT 1";
	$result = $mysqli->query($query);
	if(!$result)
	{
		die("Critical Error!!!");
	}
	$row = $result->fetch_assoc();

	$news_text_key = $row['sex'] . "_said_" . $participation;
	$news_text = $row['name'] . " " . $lang[$news_text_key];
	WriteNews($mysqli, $news_text, $player_id);
	WriteLog($mysqli, REGISTRATION, $news_text . " (by ${_SESSION['logged_admin']})", $player_id);
			
	$json_arr = array(
		'successful' => true,
	);

	echo json_encode($json_arr);

?>