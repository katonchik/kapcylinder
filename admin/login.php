<?php
	include("../settings.php");
	include("../lang/$hl.lang");
	include("../include/functions.inc.php");
	 
	if(isset($_POST['submit']))
	{
		$submit = $_POST['submit'];
		$mysqli=dbconnect($host, $db_name, $db_username, $db_password);
		$login = $mysqli->real_escape_string($_POST['login']);
		$password = $mysqli->real_escape_string($_POST['password']);

		//Get login and pass from db
		$query = "SELECT * FROM users WHERE username='".$login."' LIMIT 1";
		$result = $mysqli->query($query);
		if(!$result) //if failed to execute query
		{
			echo "Error";
		}
		elseif($result->num_rows > 0)//if login is in the database
		{
			$row = $result->fetch_assoc();
			if(md5($password) == $row['password']) 
			{
				session_start();
				$_SESSION['logged_admin'] = $login;
				$_SESSION['access_level'] = $row['access_level'];
				header("Location: index.php");
				exit();
			}
		}
		echo "Authorization failed ";
	}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $lang['admin_backend']; ?></title>
<LINK href="style.css" rel="stylesheet" type="text/css">
</head>
<body>

<form method="post" name="login" action=''>
<?php echo $lang['admin_login']; ?>: <input type="text" name="login" size='15' value=""> <br />
<?php echo $lang['admin_password']; ?>: <input type="password" name="password" value=""> <br />
<input type="submit" name="submit" value="<?php echo $lang['admin_enter']; ?>"> <br />
</form>
</body>
</html>