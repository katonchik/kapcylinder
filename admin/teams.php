<?php
	$page_access_level = 2;
	include("include/admin_includes.inc.php");

	//Add submitted team
	if(isset($_GET['action']) && isset($_GET['tid']))
	{
		$action = $_GET['action']; 
		$tid = $_GET['tid']; 
		if($action == 'delete')
		{
			//Delete teams from database:
			$query = "DELETE FROM hat_teams WHERE team_id=$tid";
			$result = $mysqli->query($query);
			if(!$result) 
			{
				$errormsg = "Failed to delete team";
			}
			else
			{
				$successmsg = "Team deleted";
			}
		}
	}

	if(isset($_POST['submit']))
	{
		$submit = $_POST['submit']; 
		$team_name = $_POST['team_name'];
		$color = $_POST['color'];
		$forum_link = $_POST['link'];
		if(!$team_name)
		{
			$errormsg = "The field is empty";
		}
		else
		{

			$query = "INSERT INTO hat_teams (team_name, color, link, tournament_id) values ('$team_name', '$color', '$forum_link', $tournid)";
			$result = $mysqli->query($query);
			if(!$result) 
			{
				$errormsg = "Failed to add team";
			}
			else
			{
				header("Location: teams.php");
				exit();
			}
		}
	}

	//Read db, draw team table
	$query = "SELECT * FROM hat_teams WHERE tournament_id = $tournid ORDER BY team_id";
	$result = $mysqli->query($query);
	include("include/header_admin.php");

?>
<form method="post" name="register" action="<?php echo $_SERVER['PHP_SELF']; ?>">
Add team: <input type="text" name="team_name" /><br />
Color: <input type="text" name="color" /><br />
Link to team's discussion: <input type="text" name="link" value="http://" /><br />
<input type="submit" name="submit" value="Add" />
</form>
<br />
<table class="listing">
	<tr>
		<th>№</th>
		<th>Team name</th>
		<th>Color</th>
		<th>Discussion link</th>
		<th>&nbsp;</th>
	</tr>
<?php
	if($result)
	{
		$counter=1;
		while ($row = $result->fetch_assoc())
		{
			echo "<tr>";
			echo "<td>" . $counter . "</td>";
			echo "<td>" . '<a href="edit_team.php?tid=' . $row['team_id'] . '">' . $row['team_name'] . "</a></td>";
			echo "<td>" . $row['color'] . "</td>";
			echo "<td>" . '<a href="' . $row['link'] . '" target="_blank">' . $row['link'] . "</a></td>";
			echo '<td align="center">' 
				. '<a href="edit_team.php?tid=' . $row['team_id'] . '"><img src="../images/edit.png" alt="Edit" /></a>'
				. '<a href="teams.php?action=delete&amp;tid=' . $row['team_id'] . '"><img src="../images/delete.png" alt=""/></a>' 
				. '</td>'; 
			echo "</tr>\n";
			$counter++;
		}
	}
?>

</table>
<br />
<?php
include("include/footer_admin.php");
?>