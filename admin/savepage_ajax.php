<?php

	$page_access_level = 2;
	include("include/admin_includes.inc.php");


	$pageid = $_POST['pageid'];
	$pagename = $mysqli->real_escape_string($_POST['pagename']);
	$query = "UPDATE tree_data"
		. " SET nm='"			. $pagename 
		. "', page_url='" 		. $mysqli->real_escape_string($_POST['pageurl']) 
		. "', page_content='" 	. $mysqli->real_escape_string($_POST['pagecontent']) 
		. "', layout=" 			. (int)$_POST['layout'] 
		. ", is_active=" 		. (int)$_POST['is_active'] 
		. " WHERE id=" . $_POST['pageid'];
	$result = $mysqli->query($query);
	if(!$result)
	{
		$success = false;
		$msg = $mysqli->error . " :: " . $query;
	}
	else
	{
		$success = true;
		$msg = "Page '" . $pagename . "' updated!";
	}

	$json_arr = array(
		'successful' => $success,
		'msg' => $msg
	);
	echo json_encode($json_arr);

?>