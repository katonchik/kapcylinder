<?php
	$page_access_level = 2;
	include("include/admin_includes.inc.php");
	
	if(isset($_POST['test'])) 
	{
		$email_result = mail_utf8($_POST['address'], 
			"Test", 
			"Test went ok", 
			$settings['from_email'], 
			$settings['tournament_name']);
			
		if(!$email_result)
		{
			$errormsg .= "Email failed";
		}
		else
		{
			$successmsg = "Check mailbox for the test message";
		}
	}
	elseif(isset($_POST['submit'])) 
	{
		$subj = $_POST['subj'];
		$body = $_POST['body'];
		$target_group = $_POST['target_group'];

		$errormsg = "";
		if(!$subj)
		{
			$errormsg .= "no subj<br />";
		}
		if(!$body)
		{
			$errormsg .= "no body<br />";
		}
		$xtra = "";
		
		if(!$errormsg)
		{
			
			if(!isset($_POST['force_all']))
			{
				$xtra = " AND email_news=1";
			}
			
			switch($target_group)
			{
				case 'unpaid' :
					$xtra .= " AND paid=0";
					break;
				case 'unconfirmed':
					$xtra .= " AND participation = " . MAYBE;
					break;
				case 'waitlist':
					$xtra .= " AND participation = " . WAITING;
					break;
				case 'unregistered':
					$xtra = -1;
					break;
				case 'all' :
				default:
					$xtra .= " AND participation IS NOT NULL ";
					break;
			}
			
			$players = getTournamentPlayers($mysqli, $tournid, $xtra);
			
			if(!$players)
			{
				$errormsg = "DB ERROR";
			}
			else
			{
				$total_count = 0;
				$success_count = 0;
				
				foreach($players as $player)
				{
					$email_result = mail_utf8($player['email'], 
						$subj, 
						$body, 
						$settings['from_email'], 
						$settings['tournament_name']);
					$total_count++;
					if(!$email_result)
					{
						$errormsg .= $player['name'] . " &lt;" . $player['email'] . "&gt;<br />";
					}
					else
					{
						$success_count++;
					}
				}
				$stats = "Message sent to $success_count recipients out of $total_count. ";
				if($errormsg)
				{
					$errormsg = $stats . "Failed to send message to the following recipients:<br />" . $errormsg;
				}
				else
				{
					$successmsg = $stats;
				}
				
				$log_text = "${_SESSION['logged_admin']} sent massmail to {$target_group} (sent to {$success_count} recipients out of {$total_count}): <b>{$subj}</b><br />{$body}";
				WriteLog($mysqli, MASSMAIL, $log_text);
				
			}
		}
	}

include("include/header_admin.php");

?>
<form method="post" name="massmail">
Subject: <br />
<input type="text" name="subj" value="<?php if(isset($subj)) echo $subj; ?>"><br />
Message Text: <br />
<textarea name="body" id="email_body"><?php if(isset($body)) echo $body; ?></textarea><br />
<select name="target_group">
	<option value="all">All who have registered</option>
	<option value="unpaid">Those who haven't paid</option>
	<option value="unconfirmed">Those who haven't confirmed</option>
	<option value="waitlist">Waiting list</option>
	<option value="unregistered">Those who haven't registered</option>
</select><br />
<input type="checkbox" name="force_all" value="1" <?php if(isset($force_all)) echo 'checked="checked"'; ?> /> Send to all (if unchecked, will send only to those players who chose to be notified)<br />
<input type="submit" name="submit" value="Send">
</form>

<br />
<br />
<h4>Test</h4>
<form method="post" name="massmail">
Address: <br />
<input type="text" name="address">
<input type="submit" name="test" value="Send Test"> (This will send a test message to the specified address to check if email is configured correctly.)
</form>

<?php
include("include/footer_admin.php");
?>