<?php
	$page_access_level = 1;
	include("include/admin_includes.inc.php");

	if($access_granted)
	{
		
		if(isset($_GET['filter']))
		{
			$filter = intval($_GET['filter']);
		}
		
		$query = "SELECT l.*, p.id, p.name FROM log AS l LEFT JOIN players AS p ON p.id = l.player_id WHERE l.tournament_id=$tournid";
		if(isset($filter) && $filter > 0)
		{
			$query .= " AND p.id = $filter";
		}
		$query .= " ORDER BY time DESC";
		$result = $mysqli->query($query);
		if(!$result) 
		{
			$errormsg = "DB ERRROR";
		}
		elseif($result->num_rows == 0)
		{
			$errormsg = "Event log is empty";
		}
	}
		
		include("include/header_admin.php");
		
	if($access_granted)
	{
		if(!isset($errormsg))
		{
	?>
		<table id="log" cellspacing="0">
	<?php
		while ($row = $result->fetch_assoc()) 
		{
	?>
			<tr>
				<td width="140"><?php echo $row['time']; ?></td>
				<td width="620"><?php echo $row['text']; ?></td>
				<td width="200"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?filter=<?php echo $row['id']; ?>"><?php echo $row['name']; ?></a></td>
			</tr>
	<?php } ?>
		</table>
	<br />
	<?php
		} //end while loop
	} //end if access
	else
	{
		echo "<div class=\"errorMsg\">Access denied!</div>";
	}

include("include/footer_admin.php");
?>