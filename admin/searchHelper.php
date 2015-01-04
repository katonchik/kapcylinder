<?php
	$page_access_level = 2;
	include("include/admin_includes.inc.php");

	$searchStr = urldecode($_POST['searchStr']);
//	echo $searchStr;
	$query = "SELECT id FROM players WHERE name LIKE '%{$searchStr}%' LIMIT 1";
	$result = $mysqli->query($query);
	if($result)
	{
		$row = $result->fetch_assoc();
		echo $row['id'];
	}
/*	
	else
	{
		echo "Nothing found: " . $query;
	}
*/
?>