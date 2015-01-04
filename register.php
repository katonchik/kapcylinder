<?php

	if(isset($_POST['submit'])) 
	{
		if (isset($_POST['phone']) && $_POST['phone'] == "123456") {exit;}
		$email = $_POST['email']; 
		$fb_user = 		isset($_SESSION['fb_user'])		? $_SESSION['fb_user']	:'NULL';
		$uid = 			isset($_SESSION['uid'])			? $_SESSION['uid']		:'NULL';
		$photo = 		isset($_SESSION['photo']) 		? $_SESSION['photo'] 	: ''; 
		$photo_rec = 	isset($_SESSION['photo_rec']) 	? $_SESSION['photo_rec']: '';
		$password = 	isset($_POST['password']) 		? $_POST['password'] 	: ''; 
		$confirm = 		isset($_POST['confirm']) 		? $_POST['confirm'] 	: ''; 
		$name = $mysqli->real_escape_string($_POST['name']); 
		if($settings['format_phones'])
		{
			$phone = formatPhone($_POST['phone']);
		}
		else
		{
			$phone = formatPhoneBy($_POST['phone']);
		}
		$sex = $_POST['sex']; 
		$tsize = $_POST['tsize']; 
		$club = $mysqli->real_escape_string($_POST['club']);
		$isnotlocal = (isset($_POST['isnotlocal']) ? $_POST['isnotlocal'] : ''); 
		if(!isset($_POST['city']) || $_POST['city'] == "")
		{
			$city = $settings['host_city'];
		}
		else
		{
			$city = $mysqli->real_escape_string($_POST['city']); 
		}
		$arrival = 			isset($_POST['arrival']) 		? $_POST['arrival'] 	: ''; 
		$departure = 		isset($_POST['departure']) 		? $_POST['departure'] 	: ''; 
		$accommodation = 	isset($_POST['accommodation']) 	? 1 : 0;
		$wannabe_captain = 	isset($_POST['wannabe_captain'])? 1 : 0;
		$food = 			isset($_POST['food']) 			? 1 : 0;
		$level = 			isset($_POST['level']) 			? 1 : 0;
		$email_news = 		isset($_POST['email_news']) 	? 1 : 0;
		$email_reg_news = 	isset($_POST['email_reg_news']) ? 1 : 0;
		$maybe = 			isset($_POST['maybe']) 			? 1 : 0;
		$errormsg = "";

		if(!validateEmail($email))
//		if($errormsg_email = validateEmail($email))
		{
			$errormsg .= $lang['bad_email_address'] . "<br />";
			//$errormsg .= $errormsg_email . "<br />";
		}
		
		if(!isEmailUnique($email, $mysqli))
		{
			$errormsg .= $lang['email_not_unique'] . "<br />";
		}

		if(!isset($_SESSION['uid']) && !isset($_SESSION['fb_user']))
		{
			if(!validatePassword($password) || !validatePassword($confirm))
			{
				$errormsg .= $lang['bad_password'] . "<br />";
			}

			if($password != $confirm)
			{
				$errormsg .= $lang['passwords_dont_match'] . "<br />";
			}
		}
		
		if($name == "")
		{
			$errormsg .= $lang['name_not_specified'] . "<br />";
		}

		if($phone == "")
		{
			$errormsg .= $lang['bad_phone_number'] . "<br />";
		}
	/*	
		if($vk == "" && $settings['make_vk_required'])
		{
			$errormsg .= $lang['bad_url'] . "<br />";
		}
		else if(strpos($vk, "vk") === 0)
		{
			$vk = "http://" . $vk;
		}
		else if(strpos($vk, "http") !== 0)
		{
			$vk = "http://vk.com/" . $vk;
		}
		$vk = str_replace("http://vk.com", "http://vk.com", $vk);
	*/
		if($club == "No Club")
		{
			$club = $lang['noclub'];
		}
		$otherclub = $_POST['otherclub'];
		if($club == "" && $otherclub == "")
		{
			$errormsg .= $lang['club_not_specified'] . "<br />";
		}
		else if($club == "")
		{
			$club = $otherclub;		
		}
		if($isnotlocal && $city == "")
		{
			$errormsg .= $lang['city_not_specified'] . "<br />";
		}
		
		//$experience = $_POST['experience'];

		if($errormsg == "")
		{

			$passwd = $password ? md5($password) : '';
			$participation = ($maybe == 1 ? 2 : 3);
			if($settings['milestone'] == 3)
			{
				$participation = 4;
			}			
			$query = "INSERT INTO players (email, password, name, sex, club, city, phone, email_news, uid, fb_user, photo, photo_rec) values ('$email', '$passwd', '$name', '$sex', '$club', '$city', '$phone', $email_news, $uid, $fb_user, '$photo', '$photo_rec')";
			$result = $mysqli->query($query);
			if($result)
			{
				$player_id = $mysqli->insert_id;
				$query = "INSERT INTO tournament_player (tournament_id, player_id, participation, arrival, departure, accommodation, wannabe_captain, tsize, level) values ($tournid, $player_id, $participation, '$arrival', '$departure', $accommodation, $wannabe_captain, '$tsize', $level)";
				$result = $mysqli->query($query);
			}

			if (!$result)
			{
				$errormsg = $lang['registration_error'];
				//echo "<!--" . $query . mysql_error() . "-->"; 
				//$errormsg = "$query<br />" . mysql_error();
			}
			else
			{
				$admin_message = 'На ' . internaLink("playerlist", "турнир") . ' зарегистрировался ' . $name . '. Клуб: ' . $club . '. Город: ' . $city . '. ' . internaLink("admin", "Проверить");
				$admin_subject = $name;
				mail_utf8($settings['admin_email'], $admin_subject, $admin_message, $settings['from_email'], $lang['tournament_name']);

				$player_message = $lang['welcome_email_body'];
				$player_subject = $lang['welcome_email_subj'];
				mail_utf8($email, 
					$player_subject, 
					$player_message, 
					$settings['from_email'], 
					$lang['tournament_name']);

				if($accommodation)
				{
					$accommodation_email_body = $lang['accommodation_email_body'];
					$accommodation_subject = $lang['accommodation_email_subject'];
					mail_utf8($settings['accommodation_email'], 
						$accommodation_subject, 
						$accommodation_email_body, 
						$settings['from_email'], 
						$lang['tournament_name']);
				}
				
				$playerLink = makePlayerLink($player_id, $name, $uid, $fb_user, $photo);
				$news_text = $lang[$sex . '_registered'] . " " . $playerLink . " (" . $city . ").";
				WriteNews($mysqli, $news_text, $player_id);
				WriteLog($mysqli, REGISTRATION, $news_text, $player_id);
							
				
				$_SESSION['logged_user'] = $email;
				header("Location: " . SITE_URL . "/home?msg=registered");
				exit();
			}
		}
	}

	$login_status = isLoggedIn();
	
	if($login_status) // Registered and logged in
	{
		header("Location: " . SITE_URL . "/home/");
		exit();						
	}
	elseif($login_status === "") // Not registered in the system yet, but logged in through a social network
	{
		$is_social = true;
		if(isset($_SESSION['uid']))
		{
			$uid = $_SESSION['uid'];
		}
		$email 		= $_SESSION['email'];
		$name 		= $_SESSION['name'];
		$photo 		= $_SESSION['photo'];
		$photo_rec 	= $_SESSION['photo_rec'];
		$sex 		= $_SESSION['sex'];
	}
	else //come through the 'register' link or hit page accidentally
	{
		//no social networks
		$club="";
		$is_social = false;
	}

	include("include/header.php");
	
	$clublist = buildClubOptions($settings['expected_clubs'], $club);
	
	$clublist_arr = array_map('trim',explode(",", $settings['expected_clubs']));
	if(!in_array($club, $clublist_arr) && $club != '')
	{
		$otherclub = $club;
		$otherclub_display = "block";
	}
	else
	{
		unset($otherclub);
		$otherclub_display = "none";
	}

	$hide_menu = true;

	if($settings['milestone'] == 3)
	{
		echo('<div id="notice">' . $lang['registration_closed'] . '</div>');
	}
	echo formatMessages($errormsg, $successmsg);
	
?>


<form method="post" name="register" onsubmit="return validateForm();" id="regform">

<div id="org_quest">

<?php if($settings['milestone'] == 2) { ?>
	<input type="checkbox" id="maybe" name="maybe" value="1" <?php echo (isset($maybe) ? 'checked="checked" ' : ''); ?>/> <?php echo $lang['maybe']; ?><br />
<?php } ?>

<input type="checkbox" id="email_news" name="email_news" value="1" <?php echo ((isset($email_news) || !isset($_POST['submit'])) ? 'checked="checked" ' : ''); ?>/> <?php echo $lang['email_news']; ?> <br />
<?php /* ?>
	<input type="checkbox" id="email_reg_news" name="email_reg_news" value="1" <?php echo ($email_reg_news ? 'checked="checked" ' : ''); ?>/> <?php echo $lang['email_reg_news']; ?> <br />
<?php */ ?>
<?php if(isset($settings['enable_easylvl']) && $settings['enable_easylvl']) { ?>
	<input type="checkbox" id="level" name="level" value="1" <?php echo (isset($level) ? 'checked="checked" ' : ''); ?>/> <?php echo $lang['level']; ?> <br />
<?php } ?>
<input type="checkbox" id="wannabe_captain" name="wannabe_captain" value="1" <?php echo (isset($wannabe_captain) ? 'checked="checked" ' : ''); ?>/> <?php echo $lang['wannabe_captain']; ?> <br />
</div>

<div id="org_inputs">
<label><b><?php echo $lang['email']; ?> *:</b></label> <input type="email" id="email" name="email" value="<?php if(isset($email)) echo $email; ?>" /><br />
<?php if(!$is_social) { ?>
<label><b><?php echo $lang['password']; ?> *:</b></label> <input type="password" id="password" name="password" value="<?php if(isset($password)) echo $password; ?>" /><br />
<label><b><?php echo $lang['confirm']; ?> *:</b></label> <input type="password" id="confirm" name="confirm" value="<?php if(isset($confirm)) echo $confirm; ?>" /><br />
<?php } ?>
<label><b><?php echo $lang['name']; ?> *:</b></label> <input type="text" id="name" name="name" value="<?php if(isset($name)) echo $name; ?>" /><br />
<label><b><?php echo $lang['phone']; ?> *:</b></label> <input type="tel" id="phone" name="phone" value="<?php if(isset($phone)) echo $phone; ?>" /><br />
<label><?php echo $lang['sex']; ?>:</label> <select name="sex">
<option value="m"<?php echo (isset($sex) && $sex=='m'?' selected':''); ?>><?php echo $lang['m']; ?></option>
<option value="f"<?php echo (isset($sex) && $sex=='f'?' selected':''); ?>><?php echo $lang['f']; ?></option>
</select><br />

<?php if(isset($settings['offer_jerseys']) && $settings['offer_jerseys']) { ?>
<label><?php echo $lang['wanttsize']; ?>:</label> <select name="tsize">
<option value="xx"<?php echo (!isset($tsize)?' selected':''); ?>><?php echo $lang['choose_tsize']; ?></option>
<option value="xs"<?php echo (isset($tsize) && $tsize=='xs'?' selected':''); ?>><?php echo $lang['xs']; ?></option>
<option value="s"<?php echo (isset($tsize) && $tsize=='s'?' selected':''); ?>><?php echo $lang['s']; ?></option>
<option value="m"<?php echo (isset($tsize) && $tsize=='m'?' selected':''); ?>><?php echo $lang['msize']; ?></option>
<option value="l"<?php echo (isset($tsize) && $tsize=='l'?' selected':''); ?>><?php echo $lang['l']; ?></option>
<option value="xl"<?php echo (isset($tsize) && $tsize=='xl'?' selected':''); ?>><?php echo $lang['xl']; ?></option>
<option value="xxl"<?php echo (isset($tsize) && $tsize=='xxl'?' selected':''); ?>><?php echo $lang['xxl']; ?></option>
<option value="myt"<?php echo (isset($tsize) && $tsize=='myt'?' selected':''); ?>><?php echo $lang['myt']; ?></option>
</select><br />
<?php } ?>

<input type="checkbox" id="isnotlocal" name="isnotlocal" onchange="javascript: toggleShowCity();" value="1" <?php echo (isset($isnotlocal) ? 'checked="checked" ' : ''); ?>/> <?php echo $lang['notlocal']; ?>
<div id="otherCity" style="display:<?php echo (isset($isnotlocal) ? 'block' : 'none'); ?>;">
	<label><?php echo $lang['city']; ?>:</label> <input type="text" name="city" value="<?php if(isset($city)) echo $city; ?>" /><br />
	<label><?php echo $lang['arrival']; ?>:</label> <input type="time" name="arrival" value="<?php if(isset($arrival)) echo $arrival; ?>" /><br />
	<label><?php echo $lang['departure']; ?>:</label> <input type="time" name="departure" value="<?php if(isset($departure)) echo $departure; ?>" /><br />
	<?php if($settings['offer_accommodation']) { ?>
		<input type="checkbox" id="accommodation" name="accommodation" value="1" <?php echo (isset($accommodation) ? 'checked="checked" ' : ''); ?>/> <?php echo $lang['accommodation']; ?>
	<?php } ?>
</div>
<br />
<label><?php echo $lang['club']; ?>:</label>
<select name="club" id="club" type="text" onchange="javascript: toggleShowClub();">
<option value="No Club"<?php if(!isset($club) || $club == 'No Club') echo ' selected="selected"'; ?>><?php echo $lang['noclub']; ?></option>
<?php echo $clublist; ?>
<option value=""><?php echo $lang['anotherclub']; ?></option>
</select><br />
<div id="Club_name" style="display:<?php echo $otherclub_display; ?>"><label><?php echo $lang['clubname']; ?>: </label><input type="text" id="otherclub" name="otherclub" value="<?php if(isset($otherclub)) echo $otherclub; ?>" /></div>
</div>

<input type="submit" name="submit" value="<?php echo $lang['register']; ?>">

</form>