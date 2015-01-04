<?php
	$page_access_level = 1;
	include("include/admin_includes.inc.php");
	
	$settingsArray = array(
		'tournament_name'=>'str',
		'tournament_description'=>'str',
		'host_city'=>'str',
		'expected_clubs'=>'str',
		'number_of_baskets'=>'int',
		'format_phones'=>'flag',
		'slots'=>'int',

		//Tournament features
		'enable_series'=>'flag',
		'enable_easylvl'=>'flag',
		'enable_autolotting'=>'flag',
		'enable_likes'=>'flag',
		'enable_dislikes'=>'flag',
		
		//Tournament offerings
		'offer_accommodation'=>'flag',
		'offer_lunches'=>'flag',
		'offer_jerseys'=>'flag',

		//Emails
		'admin_email'=>'str',
		'from_email'=>'str',
		'accommodation_email'=>'str',

		//Page tuning
		'theme'=>'str',
		'news_on_page'=>'int',
		'autonews_on_page'=>'int',
		'show_avatars'=>'flag',
		'show_gender'=>'flag',
		'show_social'=>'flag'
	);
		
		
		
	
	if(isset($_POST['submit']) && $access_granted)
	{
		if(isset($_POST['reset_all']) && $_POST['reset_all'] != "")
		{
			$query = "INSERT INTO tournament (";
			foreach($settingsArray as $key=>$value)
			{
				$query .= $key . ", ";
			}
			$query .= "is_upcoming) values (";
			foreach($settingsArray as $fieldName=>$fieldType)
			{
				switch($fieldType)
				{
					case "str":
						$query .= "'" . $mysqli->real_escape_string($_POST[$fieldName]) . "', "; 
						break;
					case "int":
						$query .= intval($_POST[$fieldName]) . ", ";
						break;
					case "flag":
						$query .= ($_POST[$fieldName]?'1':'0') . ", ";
						break;
				}
			}
			$query .= "1)";
			$action = "inserting";
		}
		elseif(isset($_POST['tournament_id']) && $_POST['tournament_id'] != "")
		{
			$query = "UPDATE tournament SET ";
			foreach($settingsArray as $fieldName=>$fieldType)
			{
				switch($fieldType)
				{
					case "str":
						$query .= "$fieldName='" . $mysqli->real_escape_string($_POST[$fieldName]) . "', "; 
						break;
					case "int":
						$query .= "$fieldName=" . intval($_POST[$fieldName]) . ", ";
						break;
					case "flag":
						$query .= "$fieldName=" . ((isset($_POST[$fieldName]) && $_POST[$fieldName]) ? '1' : '0') . ", ";
						break;
				}
			
			}
			$query = substr($query, 0, -2);
			$query .= " WHERE tournament_id=" 	. $_POST['tournament_id'];
			$action = "inserting";
		}
		$result = $mysqli->query($query);
		if(!$result) 
		{
			$errormsg = "Error {$action} tournament [{$_POST['tournament_id']}] : " . $mysqli->error . "<br />" . $query;
		}
		else
		{			
			if(isset($_POST['reset_all']))
			{
				if(isset($_POST['tournament_id']) && $_POST['tournament_id'] != "")
				{
					//change old is_upcoming to 0
					$query = "UPDATE tournament SET is_upcoming=0 WHERE tournament_id = " . $_POST['tournament_id'] . " AND is_upcoming=1";
					$result = $mysqli->query($query);
					if(!$result)
					{
						$errormsg .= "ERROR2" . $mysqli->error;
					}
				}

				$query = "TRUNCATE TABLE news;";
				$result = $mysqli->query($query);
				if(!$result)
				{
					$errormsg .= "ERROR4" . $mysqli->error;
				}
				$query = "TRUNCATE TABLE hat_teams;";
				$result = $mysqli->query($query);
				if(!$result)
				{
					$errormsg .= "ERROR5" . $mysqli->error;
				}
				$query = "TRUNCATE TABLE log;";
				$result = $mysqli->query($query);
				if(!$result)
				{
					$errormsg .= "ERROR6" . $mysqli->error;
				}
			}
			if(!isset($errormsg))
			{
				$successmsg = "Success!";
			}
		}
		
	}

	if($access_granted)
	{
		if(isset($errormsg))
		{
			foreach($settingsArray as $fieldName=>$fieldType)
			{
				$settings[$fieldName] = $_POST[$fieldName];
			}
		}
		else
		{
			$query = "SELECT * FROM tournament WHERE is_upcoming = 1 LIMIT 1";
			$result = $mysqli->query($query);
			$settings = $result->fetch_assoc();
			if(!isset($settings['setka']) || $settings['setka'] == '')
			{
				$settings['setka'] = 'http://';
			}
		}
	}
	else
	{
		$errormsg = "Access denied!";
	}

	include("include/header_admin.php");
	
	if($access_granted)
	{
?>


<form method="post" name="settings" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div style="margin: 20px 0px;">
<?php if(isset($_GET['mode']) && $_GET['mode'] == 'reset') { ?>
	Make this a new tournament? <input type="checkbox" name="reset_all" value="1" /> <font color="red">(Careful! all team and player data will be lost!!!)</font>
<?php } else { ?>
	<i>To enable to option of creating a new tournament, add "?mode=reset" to the page URL.  The option is disabled by default for security reasons.</i>
<?php } ?>
</div>
<input type="hidden" name="tournament_id" value="<?php echo $settings['tournament_id']; ?>" />
<h3>General Settings</h3>
<div>Tournament Name: <input type="text" name="tournament_name" value="<?php echo $settings['tournament_name']; ?>" /> (Normally includes year or order number, e.g. 'ЗЧХ 2012')</div>
<div>Tournament Description (Currently not used): <input type="text" name="tournament_description" value="<?php echo $settings['tournament_description']; ?>" /></div>
<div>Host City: <input type="text" name="host_city" value="<?php echo $settings['host_city']; ?>" /></div>
<div>Expected Clubs (comma separated): <input type="text" name="expected_clubs" value="<?php echo $settings['expected_clubs']; ?>" /></div>
<div>Number of baskets: <?php echo makeNumericSelect("number_of_baskets", 2, 9, $settings['number_of_baskets']); ?></div>
<div>Format phone numbers? <input type="checkbox" name="format_phones" value="1" <?php echo ($settings['format_phones'] ? 'checked="checked" ' : ''); ?>/> (This brings phone numbers to the international standard [(NNN) NNN NNNN]. Not recommended for Belarus.)</div>
<div>Slots (Currently not used): <?php echo makeNumericSelect("slots", 20, 99, $settings['slots']); ?></div>
<br />

<h3>Tournament features</h3>
<div>Enable series? <input type="checkbox" name="enable_series" value="1" <?php echo ($settings['enable_series'] ? 'checked="checked" ' : ''); ?>/> (Check if the tournament is a league that will span several rounds.)</div>
<div>Enable easy level? <input type="checkbox" name="enable_easylvl" value="1" <?php echo ($settings['enable_easylvl'] ? 'checked="checked" ' : ''); ?>/> (Check if the tournament will have a division for beginners.)</div>
<div>Enable autolotting? <input type="checkbox" name="enable_autolotting" value="1" <?php echo ($settings['enable_autolotting'] ? 'checked="checked" ' : ''); ?>/> (Automatically create teams based on player matching preferences. You can check this when you have distributed players by baskets.)</div>
<div>Enable likes? <input type="checkbox" name="enable_likes" value="1" <?php echo ($settings['enable_likes'] ? 'checked="checked" ' : ''); ?>/> (Players can specify who they want on their team.)</div>
<div>Enable dislikes? <input type="checkbox" name="enable_dislikes" value="1" <?php echo ($settings['enable_dislikes'] ? 'checked="checked" ' : ''); ?>/> (Players can specify who they DO NOT want on their team.)</div>
<br />

<h3>Tournament offerings</h3>
<div>Offer accommodation? <input type="checkbox" name="offer_accommodation" value="1" <?php echo ($settings['offer_accommodation'] ? 'checked="checked" ' : ''); ?>/> (Check if it's a two day tournament and you are expecting players from other cities.)</div>
<div>Offer lunches? <input type="checkbox" name="offer_lunches" value="1" <?php echo ($settings['offer_lunches'] ? 'checked="checked" ' : ''); ?>/> (Check to allow collecting lunch orders.)</div>
<div>Offer jerseys? <input type="checkbox" name="offer_jerseys" value="1" <?php echo ($settings['offer_jerseys'] ? 'checked="checked" ' : ''); ?>/> (Check if you will be giving out T-Shirts to participants.)</div>
<br />

<h3>Notification Emails</h3>
<div>Admin email: <input type="text" name="admin_email" value="<?php echo $settings['admin_email']; ?>" /></div>
<div>From email: <input type="text" name="from_email" value="<?php echo $settings['from_email']; ?>" /> (This address must be in the web site domain)</div>
<div>Accommodation email: <input type="text" name="accommodation_email" value="<?php echo $settings['accommodation_email']; ?>" /></div>
<br />

<h3>Page tuning</h3>
<div>Theme: <?php echo makeThemeSelect("theme", $settings['theme']); ?> (You can add your own theme by creating a new directory in '/themes/'.)</div>
<div>News on the front page: <?php echo makeNumericSelect("news_on_page", 3, 20, $settings['news_on_page']); ?> (News created by admins.)</div>
<div>Autonews on the front page: <?php echo makeNumericSelect("autonews_on_page", 3, 20, $settings['autonews_on_page']); ?> (News automatically generated by the site based on user activity.)</div>
<div>Show avatars? <input type="checkbox" name="show_avatars" value="1" <?php echo ($settings['show_avatars'] ? 'checked="checked" ' : ''); ?>/> (Check to show player avatars on the player listing page. Don't enable if you don't manually crop player photos.)</div>
<div>Show gender? <input type="checkbox" name="show_gender" value="1" <?php echo ($settings['show_gender'] ? 'checked="checked" ' : ''); ?>/> (This will show gender column on the player listing page.)</div>
<div>Show social links? <input type="checkbox" name="show_social" value="1" <?php echo ($settings['show_social'] ? 'checked="checked" ' : ''); ?>/> (This will show social links column on the player listing page.)</div>


<!--
<div>Broadcast embed code:<br /><textarea name="broadcast_embed_code" style="width:999px;height:120px;"><?php echo $settings['broadcast_embed_code']; ?></textarea> </div>
<div>Schedule: <input type="text" name="setka" value="<?php echo $settings['setka']; ?>" style="width:400px;" /> (This must be an image URL.)</div>
-->

<br />
<br />
<input type="submit" name="submit" value="Submit" />
</form>


<?php
}
include("include/footer_admin.php");
?>