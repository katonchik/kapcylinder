<?php
    if(isset($_POST['submit']))
	{
		echo '<div id="waitMsg">' . $lang['wait'] . '</div>';
		$email = htmlspecialchars($_POST['email_to']);
		if(!$email) 
		{
			$errormsg = $lang['please_enter_email'];
		}
		else
		{
			echo '<div class="checkpoint">Getting player details...</div>';
			$player = getPlayerByEmail($mysqli, $email);
			echo '<div class="checkpoint">Player details retrieved...</div>';
			if(!$player)
			{
				$errormsg = $lang['email_not_found'];
				$replace_email_form_with_login_link = 1;
			}
			elseif($player['regtype'] == "vk")
			{
				$errormsg = $lang['you_registered_with_vk'];
				$replace_email_form_with_login_link = 1;
			}
			elseif($player['regtype'] == "fb")
			{
				$errormsg = $lang['you_registered_with_fb'];
			}
			elseif($player['regtype'] == "pw")
			{
				echo '<div class="checkpoint">Sending email...</div>';
				$salt = generateSalt();
				$query = "UPDATE players SET salt='$salt' WHERE email='$email'";
				$result = $mysqli->query($query);
				if (!$result)
				{
					$errormsg = "oops... this is an error";
				}
				else
				{
					$query = "SELECT id FROM players WHERE email='$email'";
					$result = $mysqli->query($query);
					$row = $result->fetch_assoc();
					$player_id = $row['id'];
					$saltlink = SITE_URL . "/resetpass/?p=$player_id&amp;s=$salt";
					$email_body = $lang['forgotpass_email_body'] . " " . $saltlink;
					$email_subject = $lang['forgotpass_email_subj'];
					$is_sent = mail_utf8($email, 
						$email_subject, 
						$email_body, 
						$settings['from_email'], 
						$lang['tournament_name']);
					if($is_sent)
					{
						$successmsg = $lang['forgotpass_email_sent'];
					}
					else
					{
						$errormsg = $lang['forgotpass_email_failed'];
					}
				}
			}
			else //$player['regtype'] not set
			{
				$errormsg = "UNKNOWN ERROR";
			}
		}
	}

	echo formatMessages($errormsg, $successmsg);

	if(isset($replace_email_form_with_login_link) && $replace_email_form_with_login_link)
	{
		echo internaLink("login", $lang['log_in']);
	}
	elseif(!isset($_POST['submit']) || $errormsg)
	{
?>

<form name="forgot_pass" method="post" action="<?php echo SITE_URL; ?>/forgotpass/">
<strong><?php echo $lang['enter_email']; ?> : </strong>
<input name="email_to" type="text" id="mail_to" size="25">
<input type="submit" name="submit" value="Submit">
</form>

<?php
	}
?>
<script>
	document.getElementById('waitMsg').style.display = 'none';
<?php /*//this javascript is mainly for debugging
	var checkpoints = document.getElementsByClassName('checkpoint');
	for (var i = 0; i < checkpoints.length; ++i) {
		var item = checkpoints[i];  
		item.style.display = 'none';
	}
*/ ?>

</script>