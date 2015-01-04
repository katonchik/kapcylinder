<?php
	$page_access_level = 2;
	include("include/admin_includes.inc.php");

	$eventDate 	= $_GET['eventDate'];
	$action 	= $_GET['action'];
	if($action == 'add')
	{
		$eventName 	= $_GET['eventName'];
		$query = "INSERT INTO calendar (date, eventName, is_unavailable, tournament_id) values ('{$eventDate}', '{$eventName}', 1, {$tournid})";
	}
	else
	{
		$query = "DELETE FROM calendar WHERE tournament_id={$tournid} AND date='{$eventDate}' AND isUnavailable=1";
	}
	$result = $mysqli->query($query);
	if($result)
	{
		$json_arr = array(
			'successful' => true,
			'msg' => 'success',
		);
	}
	else
	{
		$json_arr = array(
			'successful' => false,
			'msg' => 'failed: ' . $mysqli->error . " " . $query,
		);
	}
	
	echo json_encode($json_arr);

?>