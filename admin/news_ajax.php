<?php
	$page_access_level = 2;
	include("include/admin_includes.inc.php");

	$news_id = $_GET['news_id'];
	$status = $_GET['status'];
	switch($status)
	{
		case "n" :
			$int_value = 1;
			$title = "Unpublish";
			$idprefix = "y";
			$imgFile = "unpublish.png";
			$rowClass = "active";
			break;
		case "y" :
			$int_value = 0;
			$title = "Publish";
			$idprefix = "n";
			$imgFile = "publish.png";
			$rowClass = "inactive";
			break;
	}
	$query = "UPDATE news SET is_published=$int_value WHERE news_id=$news_id";
	$result = $mysqli->query($query);
			
	if($result)
	{
		$json_arr = array(
			'successful' => true,
			'imgFile' => $imgFile,
			'idprefix' => $idprefix,
			'rowClass' => $rowClass,
			'title' => $title,
		);
		echo json_encode($json_arr);
	}
	else
	{
		echo $query;
	}

?>