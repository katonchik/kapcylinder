<?php
	$page_access_level = 1;
	include("include/admin_includes.inc.php");
	

	if(isset($_POST['submit'])) 
	{
		$username = $_POST['username'];
		$access_level = $_POST['access_level']; 
		$id = $_POST['id'];
		
		if(!$_POST['password'])
		{
			$query = "UPDATE users SET username='$username', access_level=$access_level WHERE id=$id";
		}
		else
		{
			$password = md5($_POST['password']);  			
			$query = "UPDATE users SET username='$username', password='$password', access_level=$access_level WHERE id=$id";
		}
		$result = $mysqli->query($query);
		if (!$result)
		{
			die('Invalid query: ' . mysql_error() . "<br />" . $query);
		}

		header("Location: admins.php");//redirect to admin list page
		exit();
	}
	else
	{
		$id = $_GET['id'];
		if(empty($id)) 
		{
			header("Location: admins.php");//redirect to admin list page
			exit();
		}

		$query = "SELECT * FROM users WHERE id=$id";
		$result = $mysqli->query($query);
		$row = $result->fetch_assoc();

		include("include/header_admin.php");
		
		if(!$access_granted)
		{
			echo "Access denied.";
		}
		else
		{
	
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	Username: <input type="text" name="username" value="<?php echo $row['username']; ?>"/> <br />
	Password: <input type="password" name="password" value="" /> (Leave the field empty to keep password unchanged)<br />
	Access level:
<select name="access level">
	<option value="1"<?php if($row['access_level']==1) echo ' selected'; ?>>Super Admin</option>
	<option value="2"<?php if($row['access_level']==2) echo ' selected'; ?>>Regular Admin</option>
	<option value="3"<?php if($row['access_level']==3) echo ' selected'; ?>>Accommodation Org</option>
</select>
<input type="hidden" name="id" value="<?php echo $id; ?>">
<br />
	<input type="submit" name="submit" value="Ok">
</form>

<br />
* Super Admin has access to the settings page and can manage other admins.<br />
Regular admin only has access to teams, players, news and mass mail.
<br />
<?php
		}
	} //end else 
	include("include/footer_admin.php");
?>