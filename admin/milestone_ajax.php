<?php
	$page_access_level = 2;
	include("include/admin_includes.inc.php");

	$milestone = $_GET['milestone'];
	if(isset($milestone))
	{
		$query = "UPDATE tournament SET milestone=$milestone WHERE tournament_id=$tournid";
		$result = $mysqli->query($query);
		if(!$result)
		{
			$msg = "Failed to change milestone: " . $mysqli->error;
			$success = false;
		}
		else
		{
			$msg = "Milestone changed to $milestone";
			$success = true;
		}
	}
	else
	{
		$msg = "Milestone undefined";
		$success = false;
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