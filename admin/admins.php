<?php
	$page_access_level = 1;
	include("include/admin_includes.inc.php");


	if(isset($_GET['action']) && isset($_GET['pid']))
	{
		$action = $_GET['action']; 
		$pid = $_GET['pid']; 
		if($action == 'delete')
		{
			//Delete users from database:
			$query = "DELETE FROM users WHERE id=$pid";
			$result = $mysqli->query($query);
			if(!$result) 
			{
				$errormsg = "Failed to delete admin.";			
			}
			else
			{
				$successmsg = "Admin deleted.";
			}
		}
	}
		
	$query = "SELECT * FROM users";
	$result = $mysqli->query($query);
	include("include/header_admin.php");
	
	if(!$access_granted)
	{
		echo "Access denied.";
	}
	else
	{
	
		$access_levels = array("", "Super Admin", "Regular Admin", "Accommodation Org");

?>

<table class="listing">
	<tr> 
		<th>username</th>
		<th>access_level</th>
		<th>&nbsp;</th>
	</tr>
<?php
while ($row = $result->fetch_assoc()) 
{
	$admin_access_level = $row['access_level'];
	echo "<tr>";
	echo '<td>' . $row['username'] . '</td>';
	echo "<td>" . $access_levels[$admin_access_level] . "</td>";
	echo '<td align="center">' 
		. '<a href="edit_admin.php?id=' . $row['id'] . '"><img src="../images/edit.png" alt="Edit" /></a>'
		. '<a href="admins.php?action=delete&amp;pid=' . $row['id'] . '"><img src="../images/delete.png" alt=""/></a>' . "</td>"; 
	echo "</tr>";
}
?>
</table>
<br />

<a href="add_admin.php">Add administrator</a>

<?php
}
include("include/footer_admin.php");
?>