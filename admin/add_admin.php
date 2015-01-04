<?php
	$page_access_level = 1;
	include("include/admin_includes.inc.php");

	if(isset($_POST['submit'])) 
	{

		$username = $_POST['username'];
		$password = $_POST['password'];
		$access_level = $_POST['access_level']; 
		if (!$username || !$password)
		{
			$errormsg = "All fields must be filled";
		}
		else
		{
			$password = md5($password);	
			
			$query = "INSERT INTO users (username, password, access_level) values ('$username', '$password', '$access_level')";
			$result = $mysqli->query($query);
			if ($result)
			{
				header("Location: admins.php");//redirect to admin list page
				exit();
			}
			else
			{
				$errormsg = "Failed to add user";
			}
		}
		
			
	}
	include("include/header_admin.php");
		
	if(!$access_granted)
	{
		echo "Access denied.";
	}
	else
	{
		
?>

<form method="post">
	Username: <input type="text" name="username" value="" /> <br />
	Password: <input type="password" name="password" value="" /> <br />
	Access level: 
<select name="access level">
	<option value="1">Super Admin</option>
	<option value="2">Regular Admin</option>
	<option value="3">Accommodation Org</option> 
</select>
<br />
	<input type="submit" name="submit" value="Ok">
</form>
<br />
* Super Admin has access to the settings page and can manage other admins.<br />
Regular admin only has access to teams, players, news and mass mail.
<br />
<?php
	}
	include("include/footer_admin.php");
?>