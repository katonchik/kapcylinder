<?php
	include("include/fe_includes.inc.php");

	$uid = $_GET['uid'];

	$query = "SELECT photo FROM players WHERE id=$uid";
	$result = $mysqli->query($query);
	$row=$result->fetch_assoc();
	$img_url = $row['photo'];
//	echo '<img src="' . $img_url . '" />';

	echo '<div class="personPopupResult"><img src="' . $img_url . '" style="max-width: 200px;" /></div>';
?>