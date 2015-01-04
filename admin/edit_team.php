<?php
	$page_access_level = 2;
	include("include/admin_includes.inc.php");

	if(!isset($_POST['submit']))
	{
		$tid = $_GET['tid'];
		$query = "SELECT * FROM hat_teams WHERE team_id=$tid";	
		$result = $mysqli->query($query);
		if(!$result)
		{
			die("Nothing selected from DB");
			exit();
		}
		$row = $result->fetch_assoc();
	}
	else
	{
		$team_name = $_POST['team_name'];
		$color = $_POST['color'];
		$forum_link = $_POST['link'];
		$tid = $_POST['tid'];
	
		$query = "UPDATE hat_teams SET team_name='$team_name', color='$color', link='$forum_link' WHERE team_id=$tid";
		$result = $mysqli->query($query);
		if($result)
		{
			$log_text = $_SESSION['logged_admin'] . " updated team " . $team_name;
			WriteLog($mysqli, TEAMS, $log_text);
			header("Location: teams.php");
			exit();
		}
		$errormsg = "Failed to update team name";
	}

include("include/header_admin.php");

?>
<form method="post" name="register">
Team name: <input type="text" name="team_name" value="<?php echo $row['team_name']; ?>" /><br />
Color: <input type="text" name="color" value="<?php echo $row['color']; ?>" /><br />
Link: <input type="text" name="link" value="<?php echo $row['link']; ?>" /><br />
<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
<input type="submit" name="submit" value="Save">
</form>

<br />
<?php
include("include/footer_admin.php");
?>