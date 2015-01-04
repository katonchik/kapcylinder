<?php

	include("include/fe_includes.inc.php");

	if(!$email=isLoggedIn())
	{
		die();
	}

	//$player = getThisPlayer($mysqli, $tournid);

	//$player_id = $player['id'];

	$requestParams = getRequestParams($mysqli);

	$action_id = $requestParams['action_id'];

	switch($action_id)
	{
		case "changePassword":
			$password = $requestParams['password'];
			$confirm = $requestParams['confirm'];

			if($password != $confirm)
			{
				$successful = false;
				$msgText = $lang['passwords_dont_match'];
			}
			elseif(!validatePassword($password))
			{
				$successful = false;
				$msgText = $lang['bad_password_register'];
			}
			else
			{
				$password = md5($password);
				$query = "UPDATE players SET password = '$password' WHERE email = '$email'";
				$result = $mysqli->query($query);
				if (!$result)
				{
					$successful = false;
					$msgText = $lang['password_change_failed'];
				}
				else
				{
					$successful = true;
					$msgText = $lang['password_change_successful'];
				}
			}
			$debug_msg = "";
			$json_arr = array(
				'successful' => $successful,
				'msgText' => $msgText,
				'debugMsg' => $debug_msg,
			);
			echo json_encode($json_arr);

			break;
		case "changeContacts":
			$auth_email = $email;
			$email = $requestParams['email'];
	//		$photo = $_POST['photo'];
	//		$photo_rec = $_POST['photo_rec'];
			$name = $requestParams['name'];
			if($settings['format_phones'])
			{
				$phone = formatPhone($requestParams['phone']);
			}
			else
			{
				$phone = formatPhoneBy($requestParams['phone']);
			}
			$sex = $requestParams['sex'];
			$isnotlocal = (isset($requestParams['isnotlocal']) ? 1 : 0 );
			$city = isset($requestParams['city'])?$requestParams['city']:$settings['host_city'];
			$email_news = (isset($requestParams['email_news']) ? 1 : 0);
//			$email_reg_news = ($_POST['email_reg_news'] ? 1 : 0);
			$errormsg = "";

			if(!validateEmail($email))
			{
				$errormsg .= $lang['bad_email_address'] . " " . $email . "<br />";
			}


			if($name == "")
			{
				$errormsg .= $lang['name_not_specified'] . "<br />";
			}

			if($phone == "")
			{
				$errormsg .= $lang['bad_phone_number'] . "<br />";
			}

			if(!isset($requestParams['club']) || $requestParams['club'] == "" || $requestParams['club'] == "No Club")
			{
				$club = $lang['noclub'];
			}
			else
			{
				$club = $requestParams['club'];
			}

			if($isnotlocal && $city == "")
			{
				$errormsg .= $lang['city_not_specified'] . "<br />";
			}

			if($errormsg == "")
			{

				if($city == "")
				{
					$city = $settings['host_city'];
				}

				$query = "UPDATE players SET "
					. "email = '$email', "
					. "sex = '$sex', "
					. "club = '$club', "
					. "city = '$city', "
					. "name = '$name', "
					. "phone = '$phone', "
					. "email_news = $email_news "
	//				. "email_reg_news = $email_reg_news "
	//				. "photo = '$photo', "
		//			. "photo_rec = '$photo_rec' "
					. "WHERE email = '$auth_email'";
				$result = $mysqli->query($query);
				if (!$result)
				{
					//TODO: give safe message
					//$errormsg = $query . "<br />" . mysql_error();
					$errormsg = "Failed to update. Please contact administrator.";
					$log_text = "$name failed to update contact details: " . $mysqli->error;
					WriteLog($mysqli, CONTACTS, $log_text, $auth_email);
				}
				else
				{
					$successmsg = $lang['changes_saved'];
					$log_text = "$name updated contact details";
					WriteLog($mysqli, CONTACTS, $log_text, $auth_email);
				}
			}
			if($errormsg)
			{
				$successful = false;
				$msgText = $errormsg;
			}
			else
			{
				$successful = true;
				$msgText = $successmsg;
			}
			$debug_msg = "";
			$json_arr = array(
				'successful' => $successful,
				'msgText' => $msgText,
				'debugMsg' => $debug_msg,
			);
			echo json_encode($json_arr);


			break;
	}


?>