<?php
	$page_access_level = 2;
	include("include/admin_includes.inc.php");

	$playerId = $_POST['playerId'];
	$croppedSrc = $_POST['croppedSrc'];
	$query = "UPDATE players SET photo_cropped='$croppedSrc' WHERE id=$playerId";
	$result = $mysqli->query($query);
	if(!$result)
	{
		$msg = "Failed to save avatar: " . $mysqli->error;
		$success = false;
	}
	else
	{
		$msg = "Avatar updated";
		$success = true;
	}
	//$row = $result->fetch_assoc();

	//$news_text = $row['name'] . " is now in basket " . $basket;
	//WriteLog($mysqli, BASKET, $news_text . " (by ${_SESSION['logged_admin']})", $player_id);
			
	$json_arr = array(
		'successful' => $success,
		'msgText' => $msg,
	);

	echo json_encode($json_arr);

?>