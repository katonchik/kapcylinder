<?php
	$page_access_level = 2;
	include("include/admin_includes.inc.php");

	$action 	= $_GET['action'];

	switch($action)
	{
		case 'addItem' :
			$taskName 	= $_GET['taskName'];
			$query = "INSERT INTO checklist (task_name, tournament_id) values ('{$taskName}', {$tournid})";
			$result = $mysqli->query($query);
			$itemID = $mysqli->insert_id;
			if($result)
			{
				$json_arr = array(
					'successful' => true,
					'itemID' => $itemID,
					'msg' => 'success',
				);
			}
			else
			{
				$json_arr = array(
					'successful' => false,
					'msg' => $mysqli->error . " " . $query,
				);
			}
			break;
		case 'getItems':
			$query = "SELECT * FROM checklist WHERE tournament_id = {$tournid}";
			$result = $mysqli->query($query);
			if($result)
			{
				$itemRows = array();
				while($row = $result->fetch_assoc())
				{
					$itemRows[] = $row;
				}
				$json_arr = array(
					'successful' => true,
					'itemsData' => $itemRows,
					'msg' => 'success',
				);
			}
			else
			{
				$json_arr = array(
					'successful' => false,
					'msg' => $mysqli->error . " " . $query,
				);
			}
			break;
		case 'delete':
			$query = "DELETE FROM calendar WHERE tournament_id={$tournid} AND date='{$eventDate}' AND isUnavailable=1";
			break;
	}

	echo json_encode($json_arr);

?>