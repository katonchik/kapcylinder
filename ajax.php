<?php
	include("include/fe_includes.inc.php");

	if(!isLoggedIn())
	{
		die("Access denied");
	}

	$player = getThisPlayer($mysqli, $tournid);
	$email = $player['email'];
	$player_id = $player['id'];
	$playerLink = makePlayerLink($player['id'], $player['name'], $player['uid'], $player['fb_user'], $player['photo']);
	//echo "email=[$email]";
	//echo "playerid=[$player_id]";
	
	$requestParams = getRequestParams();

	$q_id = isset($requestParams['q_id']) ? $requestParams['q_id'] : '';
	$new_value = isset($requestParams['new_value']) ? $requestParams['new_value'] : '';
	$debug_msg = "";
	
	switch($q_id)
	{
		case "question_accommodation":
			$new_accommodation = ($player['accommodation'] == 0 ? 1 : 0);
			$query = "UPDATE tournament_player SET accommodation=$new_accommodation WHERE 
				player_id=$player_id AND
				tournament_id=$tournid";
			$result = $mysqli->query($query);
			
			$player = getThisPlayer($mysqli, $tournid);
		
			if($result)
			{
				if(!$player['accommodation'])
				{
					$accommodation_message = $lang['you_dont_need_accommodation'];
					$accommodation_link = $lang['request_accommodation'];
					$log_text = $playerLink . " does not need help with accommodation.";
				}
				else
				{
					$accommodation_message = $lang['you_need_accommodation'];
					$accommodation_link = $lang['decline_accommodation'];
					$log_text = $playerLink . " needs help with accommodation.";
				}
				$json_arr = array(
					'successful' => true,
					'msgText' => $accommodation_message,
					'linkText' => $accommodation_link,
					'debugMsg' => $debug_msg,
					'old_class' => 'yes',
					'new_class' => 'no',
				);
			}
			else
			{
				$log_text = $playerLink . " failed to update accommodation preferences.";
			}
			
			$log_category = ACCOMMODATION;
/*			
			$admin_email_subj = $row['name'] . " doesn't need help with accommodation.";
			$admin_email_body = $admin_email_subj;
			$admin_email_field = 'accommodation_email';
*/
			
			break;
		case "question_division":
			//$new_division = ($player['level'] == 0 ? 1 : 0);
			$new_division = 0;
			switch($new_value)
			{
				case "division_first" :
					$new_division = 0;
					break;
				case "division_second" :
					$new_division = 1;
					break;
			}
			$query = "UPDATE tournament_player SET level=$new_division WHERE 
				player_id=$player_id AND
				tournament_id=$tournid";
			$result = $mysqli->query($query);
			
			$player = getThisPlayer($mysqli, $tournid);
		
			if($result)
			{
				if(!$player['level'])
				{
					$division_message = $lang['division_first'];
					$division_link = $lang['division_link_second'];
					$hide_button_division = "division_first";
					$log_text = $playerLink . " has chosen first division.";
				}
				else
				{
					$division_message = $lang['division_second'];
					$division_link = $lang['division_link_first'];
					$hide_button_division = "division_second";
					$log_text = $playerLink . " has chosen second division.";
				}
				$json_arr = array(
					'successful' => true,
					'msgText' => $division_message,
					'linkText' => $division_link,
					'debugMsg' => $debug_msg,
	//				'old_class' => 'yes',
	//				'new_class' => 'no',
				);
			}
			else
			{
				$log_text = $playerLink . " failed to update division preferences.";
			}
			
			$log_category = REGISTRATION;

			break;	
		case "question_tshirt":
			$new_tsize = "xx";
			switch($new_value)
			{
				case "tshirt_myt":
					$new_tsize = "myt";
					break;
				case "tshirt_xs":
					$new_tsize = "xs";
					break;
				case "tshirt_s":
					$new_tsize = "s";
					break;			
				case "tshirt_m":
					$new_tsize = "m";
					break;			
				case "tshirt_l":
					$new_tsize = "l";
					break;
				case "tshirt_xl":
					$new_tsize = "xl";
					break;			
				case "tshirt_xxl":
					$new_tsize = "xxl";
					break;			
			}
			$query = "UPDATE tournament_player SET tsize='$new_tsize' WHERE 
				player_id=$player_id AND
				tournament_id=$tournid";
			$result = $mysqli->query($query);
			
			$player = getThisPlayer($mysqli, $tournid);

			switch($player['tsize'])
			{
				case "myt":
					$tshirt_message = $lang['tshirt_myt'];
					$tshirt_link = $lang['myt'];
					$hide_button_tshirt = "tshirt_myt";
					$news_text_key = "tshirt_myt";
					break;
				case "xs":
					$tshirt_message = $lang['tshirt_xs'];
					$tshirt_link = $lang['xs'];
					$hide_button_tshirt = "tshirt_xs";
					$news_text_key = "tshirt_xs";
					break;
				case "s":
					$tshirt_message = $lang['tshirt_s'];
					$tshirt_link = $lang['s'];
					$hide_button_tshirt = "tshirt_s";
					$news_text_key = "tshirt_s";
					break;
				case "m":
					$tshirt_message = $lang['tshirt_m'];
					$tshirt_link = $lang['m'];
					$hide_button_tshirt = "tshirt_m";
					$news_text_key = "tshirt_m";
					break;
				case "l":
					$tshirt_message = $lang['tshirt_l'];
					$tshirt_link = $lang['l'];
					$hide_button_tshirt = "tshirt_l";
					$news_text_key = "tshirt_l";
					break;
				case "xl":
					$tshirt_message = $lang['tshirt_xl'];
					$tshirt_link = $lang['xl'];
					$hide_button_tshirt = "tshirt_xl";
					$news_text_key = "tshirt_xl";
					break;
				case "xxl":
					$tshirt_message = $lang['tshirt_xxl'];
					$tshirt_link = $lang['xxl'];
					$hide_button_tshirt = "tshirt_xxl";
					$news_text_key = "tshirt_xl";
					break;
				default:
					$tshirt_message = $lang['tshirt_unset'];
					$tshirt_link = $lang['ownt'];
					unset($hide_button_tshirt);
			}
			

			$log_category = JERSEY;
			$log_text = $playerLink . " has chosen jersey size " . $tshirt_link;

			$json_arr = array(
				'successful' => true,
				'msgText' => $tshirt_message,
				'linkText' => $tshirt_link,
				'debugMsg' => $debug_msg,
			);
			
			break;				


		case "question_lunches":
			//example of new_value: day1menu1
			$day = substr($new_value, 3 ,1);
			$menu = substr($new_value, 8, 1);
			
			$lunches = $player['lunches'];
			if($lunches)
			{
				$day2 = $lunches % 10;
				$day1 = ($lunches - $day2) / 10;
			}
			else
			{
				$day1 = $day2 = 0;
			}
			
			if($day == 1)
			{
				$day1 = $menu;
			}
			elseif($day == 2)
			{
				$day2 = $menu;
			}
			
			$lunches = $day1*10 + $day2;
			
			$query = "UPDATE tournament_player SET lunches='$lunches' WHERE 
				player_id=$player_id AND
				tournament_id=$tournid";
			$result = $mysqli->query($query);
			
			if($result)
			{
				if(!$day1 && !$day2)
				{
					$lunches_message = $lang['lunches_none'];
				}
				elseif($day1 && $day2)
				{
					$lunches_message = $lang['lunches_both_days'];
				}
				elseif($day1 && !$day2)
				{
					$lunches_message = $lang['lunches_only_day1'];
				}
				elseif(!$day1 && $day2)
				{
					$lunches_message = $lang['lunches_only_day2'];
				}
			}
			
			$log_category = LUNCHES;
			$log_text = $playerLink . " updated lunch preferences.";

			$lunches_link = "lnk";
			
			$json_arr = array(
				'successful' => true,
				'msgText' => $lunches_message,
				'linkText' => $lunches_link,
				'debugMsg' => $debug_msg,
			);
		
			break;

		case "question_participation":
			switch($new_value)
			{
				case "no" :
					$int_value = 1;
					break;
				case "maybe" :
					$int_value = 2;
					break;
				case "yes" :
				default:
					$int_value = 3;
					break;
			}
			if(isset($player['participation'])) //changing participation option
			{
				$mode = "update";
				$query = "UPDATE tournament_player SET participation=$int_value WHERE player_id=$player_id AND tournament_id = $tournid";
			}
			else //setting participation option for the first time
			{
				$mode = "insert";
				$query = "INSERT INTO tournament_player (tournament_id, player_id, participation) values ($tournid, $player_id, $int_value)";			
			}
			$result = $mysqli->query($query);
			if(!$result)
			{
				echo mysql_error() . $query;
				exit();
			}
			
			$player = getThisPlayer($mysqli, $tournid);

			switch($player['participation'])
			{
				case NO:
					$participation_message = $lang['participate_no'];
					$hide_button = "no";
					$news_text_key = "no";
					break;
				case MAYBE:
					$participation_message = $lang['participate_maybe'];
					$hide_button = "maybe";
					$news_text_key = "maybe";
					break;
				case YES:
					$participation_message = $lang['participate_yes'];
					$hide_button = "yes";
					$news_text_key = "yes";
					break;
				default:
					$participation_message = $lang['participate_unset'];
					break;
			}
			
			
			if($mode == 'update')
			{

				$news_text_key = $player['sex'] . "_said_" . $news_text_key;
				$news_text = $playerLink . " " . $lang[$news_text_key];
			}
			else //if($mode=='insert')
			{
				$news_text = $lang[$player['sex'] . '_registered'] . " " . $playerLink . " (" . $player['city'] . ").";
			}
			//$news_text = $player['name'] . " " . $lang[$news_text_key];
			WriteNews($mysqli, $news_text, $player['id']);

			
			$log_category = REGISTRATION;
			$log_text = $news_text;
/*
			$admin_email_subj = $news_text;
			$admin_email_body = $news_text;
			$admin_email_field = 'admin_email';
*/
			
			$json_arr = array(
				'successful' => true,
				'msgText' => $participation_message,
				'debugMsg' => $debug_msg,
			);
		
			break;
			
		case "like":
			$new_name = urldecode($requestParams['new_name']);
			$int_value = intval($new_value);
			$query = "UPDATE tournament_player SET `like`=$int_value WHERE player_id=$player_id AND tournament_id = $tournid";
			$result = $mysqli->query($query);
			if($result)
			{
				if($int_value)
				{
					$log_text = $playerLink . " wants to play with " . $new_name;
				}
				else
				{
					$log_text = $playerLink . " removed the like preference.";
				}
				$json_arr = array(
					'successful' => true,
					'msgText' => $lang['autolotting_like_saved'] . " " . $new_name,
					'debugMsg' => $debug_msg,
				);
			}
			else
			{
				$log_text = $playerLink . " failed to update the like preference.";
				$json_arr = array(
					'successful' => false,
					'msgText' => $lang['autolotting_like_save_failed'],
					'debugMsg' => $debug_msg,
				);
			}
			$log_category = REGISTRATION;
			break;
			
		case "dislike":
			$new_name = urldecode($requestParams['new_name']);
			$int_value = intval($new_value);
			$query = "UPDATE tournament_player SET `dislike`=$int_value WHERE player_id=$player_id AND tournament_id = $tournid";
			$result = $mysqli->query($query);
			if($result)
			{
				if($int_value)
				{
					$log_text = $playerLink . " doesn't want to play with " . $new_name;
				}
				else
				{
					$log_text = $playerLink . " removed the dislike preference.";
				}
				$json_arr = array(
					'successful' => true,
					'msgText' => $lang['autolotting_dislike_saved'] . " " . $new_name,
					'debugMsg' => $debug_msg,
				);
			}
			else
			{
				$log_text = $playerLink . " failed to update the dislike preference.";
				$json_arr = array(
					'successful' => false,
					'msgText' => $lang['autolotting_dislike_save_failed'],
					'debugMsg' => $debug_msg,
				);
			}
			
			$log_category = REGISTRATION;
			break;
			
		case "saveTimes":
			$arrival = $requestParams['arrival'];
			$departure = $requestParams['departure'];
			$query = "UPDATE tournament_player SET `arrival`='$arrival', `departure`='$departure' WHERE player_id=$player_id AND tournament_id = $tournid";
			$result = $mysqli->query($query);
			if($result)
			{
				$log_text = $playerLink . " updated arrival to {$arrival} and departure to {$departure}.";
				$json_arr = array(
					'successful' => true,
					'msgText' => $lang['changes_saved'],
					'debugMsg' => $debug_msg,
				);
			}
			else
			{
				$log_text = $playerLink . " failed to update arrival and departure.";
				$json_arr = array(
					'successful' => false,
					'msgText' => $lang['changes_failed'],
					'debugMsg' => $debug_msg,
				);
			}
			
			$log_category = REGISTRATION;
			break;
			
			
	}

	echo json_encode($json_arr);

	if($log_text)
	{
		WriteLog($mysqli, $log_category, $log_text, $player_id);
	}
	



?>