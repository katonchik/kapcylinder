<?php
	$page_access_level = 2;
	include("include/admin_includes.inc.php");
	
	if($access_granted)
	{
		if(isset($_POST['submit']))
		{
			$query = "UPDATE tournament SET general_info='" . $mysqli->real_escape_string($_POST['general_info']) . "' WHERE tournament_id=" . $_POST['tournament_id'];
			$result = $mysqli->query($query);
			if(!$result) 
			{
				$announcement = $_POST['general_info'];
				$errormsg = "Failed to update announcement";
			}
			else
			{
				$successmsg = "Success!";
			}			
		}
		
		if(!isset($announcement))
		{			
			$query = "SELECT general_info FROM tournament WHERE tournament_id = $tournid";
			$result = $mysqli->query($query); 
			if(!$result)
			{
				$errormsg = "ERROR :(";
			}
			else
			{
				$row = $result->fetch_assoc();
				$announcement = $row['general_info'];
			}
		}
	}
	else
	{
		$errormsg = "Access denied.";
	}

	include("include/header_admin.php");
	
	if($access_granted)
	{
	
	
?>
<form method="post" name="settings" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type="hidden" name="tournament_id" value="<?php echo $settings['tournament_id']; ?>" />
General tournament info (will show on the front page): <br />
<textarea id="general_info" name="general_info">
<?php echo stripslashes($announcement); ?></textarea><br />
<input type="submit" name="submit" value="Submit" />
</form>


<?php
}
include("include/footer_admin.php");
?>