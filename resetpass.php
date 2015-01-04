<?php
	include("include/fe_includes.inc.php");
	
	if(isset($_POST['submit']))
	{
		$player_id = intval($_POST['player_id']);
		$db_salt = getSaltFromDb($mysqli, $player_id);
		if($_POST['password'] != $_POST['confirm'])
		{
			$errormsg = "Passwords don't match.";
		}
		elseif(!$db_salt)
		{
			$errormsg = "DB ERROR.....";
		}
		elseif($db_salt != $_SESSION['salt'])
		{
			$errormsg = "VALIDATION ERROR.....";
		}
		else
		{
			$password = md5($_POST['password']);
			$query = "UPDATE players SET password = '$password' WHERE id = $player_id";

			$result = $mysqli->query($query);
			if (!$result)
			{
				$errormsg = "Failed to reset password.";
			}
			else
			{
				header("Location: " . SITE_URL . "/login/passreset/");
				exit();
			}
		}
		
	}
	//echo formatMessages($errormsg, $successmsg);
	
	
	$requestParams = getRequestParams();
    if (!$errormsg && $requestParams['p'] && $requestParams['s'])
	{
		$player_id = $requestParams['p'];
		$salt = $requestParams['s'];
		
		$query = "SELECT * FROM players WHERE id=$player_id";
		$result = $mysqli->query($query);
		if (!$result)
		{
			$errormsg = $lang['hack_attempt'] . "?";
		}
		elseif($result->num_rows == 0)
		{
			$errormsg = $lang['hack_attempt'] . "???";
		}
		else
		{
			$row = $result->fetch_assoc();
			if($salt != $row['salt'])
			{
				$errormsg = $lang['hack_attempt'];
			}
			else
			{
				$_SESSION['salt'] = $salt;
			}
		}
	}

	include("include/header.php");
	
	
	echo formatMessages($errormsg, $successmsg);
	
	if(!$errormsg && $requestParams['p'] && $requestParams['s'])
	{
		//show change password form
?>


<form name="forgot_pass" method="post" action="<?php echo SITE_URL; ?>/resetpass/">
<input type="hidden" name="player_id" value="<?php echo $player_id; ?>" />
<strong><?php echo $lang['new_password']; ?> : </strong>
<input name="password" type="password" id="password" size="25"><br />
<strong><?php echo $lang['confirm_new_password']; ?> : </strong>
<input name="confirm" type="password" id="confirm" size="25"><br />
<input type="submit" name="submit" value="Submit">
</form>

<?php
	}
	
	include("include/footer.php");
?>