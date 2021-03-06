<?php

function dbconnect($host, $db_name, $db_username, $db_password)
{
	$mysqli = new mysqli($host, $db_username, $db_password, $db_name);
	if (mysqli_connect_error()) {
		die('Connect Error (' . mysqli_connect_errno() . ') '
				. mysqli_connect_error());
	}
	if (!$mysqli->set_charset("utf8")) {
		$debug = "Error loading character set utf8: " . $mysqli->error;
	} else {
		$debug = "Current character set: " . $mysqli->character_set_name();
	}
	return $mysqli;
}


function formatPhone($num) 
{ 
    $num = preg_replace('/[^0-9]/', '', $num); 

    $len = strlen($num); 
    if($len == 7) 
        $num = preg_replace('/([0-9]{3})([0-9]{4})/', '$1 $2', $num); 
    elseif($len == 10) 
        $num = preg_replace('/([0-9]{3})([0-9]{3})([0-9]{4})/', '($1) $2 $3', $num); 
    elseif($len == 12) 
        $num = preg_replace('/([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{4})/', '($2) $3 $4', $num); 

    return $num; 
} 

function formatPhoneBy($num) 
{ 
	
	$num = preg_replace('/[^0-9]/', '', $num); 
    $len = strlen($num); 
    if($len == 7) //Local
        $num = preg_replace('/([0-9]{3})([0-9]{4})/', '$1 $2', $num); 
    elseif($len == 9) //Belarus
	{	
        $num = preg_replace('/([0-9]{2})([0-9]{3})([0-9]{4})/', '375 $1 $2 $3', $num);
		$num = "+" . $num;
    }
	elseif($len == 10 && (strpos($num, "5") === 0 || strpos($num, "0") === 0)) //Belarus
	{	
        $num = preg_replace('/([0-9]{1})([0-9]{2})([0-9]{3})([0-9]{4})/', '375 $2 $3 $4', $num);
		$num = "+" . $num;
	}
    elseif($len == 10) //International undefined
	{
        $num = preg_replace('/([0-9]{3})([0-9]{3})([0-9]{4})/', '($1) $2 $3', $num); 
	}
    elseif($len == 11 && (strpos($num, "80") === 0 || strpos($num, "85") === 0)) //Belarus
	{
        $num = preg_replace('/([0-9]{2})([0-9]{2})([0-9]{3})([0-9]{4})/', '375 $2 $3 $4', $num);
		$num = "+" . $num;
	}

    elseif($len == 11 && (strpos($num, "7") === 0 || strpos($num, "1") === 0)) //Russia
	{
        $num = preg_replace('/([0-9]{1})([0-9]{3})([0-9]{3})([0-9]{4})/', '7 $2 $3 $4', $num);
		$num = "+" . $num;
	}
    elseif($len == 12 && (strpos($num, "375") === 0 || strpos($num, "380") === 0)) //Ukraine, Belarus
	{
		$num = preg_replace('/([0-9]{3})([0-9]{2})([0-9]{3})([0-9]{4})/', '$1 $2 $3 $4', $num);
		$num = "+" . $num;
	}
    elseif($len == 12) //International
	{
		$num = preg_replace('/([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{4})/', '$1 $2 $3 $4', $num);
		$num = "+" . $num;
	}

	return $num; 
} 

/**
Validate an email address.
Provide email address (raw input)
Returns true if the email address has the email 
address format and the domain exists.
*/
function validateEmail($email)
{
   $isValid = true;
   $atIndex = strrpos($email, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
      $isValid = false;
   }
   else
   {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
         $debug = "local part length exceeded";
         $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
         $debug = "domain part length exceeded";
         $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
         $debug = "local part starts or ends with '.'";
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
         $debug = "local part has two consecutive dots";
         $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
         $debug = "character not valid in domain part";
         $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
         $debug = "domain part has two consecutive dots";
         $isValid = false;
      }
      else if(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                 str_replace("\\\\","",$local)))
      {
         $debug = "character not valid in local part unless local part is quoted";
         if (!preg_match('/^"(\\\\"|[^"])+"$/',
             str_replace("\\\\","",$local)))
         {
            $isValid = false;
         }
      }
      if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
      {
      //   $debug = "domain not found in DNS";
      //   $isValid = false;
      }
   }
   //return $debug;
   return $isValid;
}


function isEmailUnique($email, $mysqli)
{
	$query = "SELECT id FROM players WHERE email='$email'"; 
	$result = $mysqli->query($query);
	if($result && $result->num_rows == 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}


function GetSettings($mysqli)
{
	$query = "SELECT * FROM tournament WHERE is_upcoming=1 LIMIT 1"; 
	$result = $mysqli->query($query);
	return $result->fetch_assoc();
}


function WriteNews($mysqli, $news_text, $player_id=0)
{
	$tournid = getUpcomingTournamentId($mysqli);
	$news_text = $mysqli->real_escape_string($news_text);
	$query = "SELECT news_id, player_id FROM news WHERE tournament_id = $tournid ORDER BY news_id DESC LIMIT 1";
	$result = $mysqli->query($query);
	$row = $result->fetch_assoc();
	$last_player_id = $row['player_id'];
	if($player_id != 0 && $player_id == $last_player_id)
	{
		$query = "UPDATE news SET date=CURDATE(), news_text = '$news_text' WHERE news_id = " . $row['news_id'];
	}
	else
	{
		$query = "INSERT INTO news (date, news_text, player_id, tournament_id) values (CURDATE(), '$news_text', $player_id, $tournid)"; 
	}
	return $mysqli->query($query);
}

function WriteLog($mysqli, $category, $text, $player_id=0)
{
	$tournid = getUpcomingTournamentId($mysqli);
	$text = $mysqli->real_escape_string($text);
	if(is_numeric($player_id))
	{
		$query = "INSERT INTO log (time, category, text, player_id, tournament_id) values (NOW(), $category, '$text', $player_id, $tournid)"; 
	}
	else
	{
		$query = "INSERT INTO log (time, category, text, player_id, tournament_id) values (NOW(), $category, '$text', (SELECT id FROM players WHERE email='$player_id'), $tournid)"; 		
	}
	return $mysqli->query($query);
}


function validatePassword($password)
{
	if( 
		ctype_alnum($password) // numbers & digits only 
		&& strlen($password)>4 // at least 5 chars 
		&& strlen($password)<21 // at most 20 chars 
		)
	{ 
		return true; 
	}
	else
	{ 
		return false;
	} 
}

function MakeFromLine($s_email,$s_name)
{
    $s_line = "";
    if (!empty($s_email))
        $s_line .= $s_email." ";
    if (!empty($s_name))
        $s_line .= "(=?utf-8?B?".base64_encode($s_name)."?=)";
    return ($s_line);
} 

function mail_utf8($to, $subject = '(No subject)', $message = '', $from_email = '', $tournament_name = 'хет') 
{ 
	$from = MakeFromLine($from_email,$tournament_name);
	$message = wordwrap($message, 70);
	$headers = "From:" . $from . "\r\n";
	$headers .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	return mail($to, '=?UTF-8?B?'.base64_encode($subject).'?=', $message, $headers); 
} 

function isLoggedIn()
{
	if (isset($_SESSION['logged_user']))
	{
		return $_SESSION['logged_user'];
	}
	elseif (isset($_SESSION['uid']))
	{
		return "";
	}
	elseif (isset($_SESSION['fb_user']))
	{
		return "";
	}
	return false;
}

function getRequestParams($mysqli="")
{
	$request_params = array();
	$params_arr=explode("?", $_SERVER['REQUEST_URI']);
	$requests_str = $params_arr[count($params_arr)-1];
	$params_arr=explode("&", $requests_str);
	foreach($params_arr as $param)
	{
		$pair = explode("=", $param);
		$key = $pair[0];
		if(count($pair)==2)
		{
			$value = $pair[1];
		}
		else
		{
			array_shift($pair);
			$value = implode("=", $pair);
		}
		if($mysqli)
		{
			$request_params[$key] = $mysqli->real_escape_string(urldecode($value));
		}
		else
		{
			$request_params[$key] = urldecode($value);
		}
	}
	return $request_params;
}

function isVkAuthorized($app_id, $security_key, $uid, $hash)
{
	$str = md5($app_id . $uid . $security_key);
	if($str == $hash)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function emailExists($mysqli, $email)
{
	$query = "SELECT id FROM players WHERE email='$email'"; 
	$result = $mysqli->query($query);
	if($result && $result->num_rows > 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function generateSalt() 
{
	return substr(md5(uniqid(rand(), true)), 0, 12);
}

function getSaltFromDb($mysqli, $player_id)
{
	$query = "SELECT * FROM players WHERE id=$player_id";
	$result = $mysqli->query($query);
	if (!$result || $result->num_rows == 0)
	{
		return false;
	}
	else
	{
		$row = $result->fetch_assoc();
		return $row['salt'];
	}
}

function buildClubOptions($clubs_csv, $selected_club = "")
{
	$clublist_arr = array_map('trim',explode(",", $clubs_csv));
	$cluboptions="";
	foreach($clublist_arr as $clubname)
	{
		$cluboptions .= '<option value="' . $clubname . '"';
		if($selected_club && $clubname == $selected_club)
		{
			$cluboptions .= ' selected="selected"';
		}
		$cluboptions .='>' . $clubname . '</option>';
	}
	return $cluboptions;
}

function buildCityOptions($mysqli, $tournid, $selected_city="")
{
	$options = "";
	$selectedFlag = "";
	$query = "SELECT DISTINCT(city) AS city FROM players p, tournament_player tp WHERE tp.tournament_id = $tournid AND p.id=tp.player_id";
	$result = $mysqli->query($query);
	while($row=$result->fetch_assoc())
	{
		if($selected_city == $row['city'])
		{
			$selectedFlag .= ' selected="selected"';
		}

		$options.='<option value="' . $row['city'] . '"' . $selectedFlag.'>' . $row['city'] . '</option>' . "\n";
		$selectedFlag = "";
	}
	return $options;
}

function buildCityList($mysqli, $tournid)
{
	$city_array = array();
	$query = "SELECT DISTINCT(city) AS city FROM players p, tournament_player tp WHERE tp.tournament_id = $tournid AND p.id=tp.player_id";
	$result = $mysqli->query($query);
	while($row=$result->fetch_assoc())
	{
		$city_array[] = $row['city'];
	}
	return $city_array;
}

function makePlayerLink($id, $name, $vk_id, $fb_user="", $photo_url, $jspath = "./")
{
	$name=stripslashes($name);
	if(isset($vk_id) && $vk_id && $vk_id != 'NULL')
	{
		return '<a class="personPopupTrigger" href="http://vk.com/id' . $vk_id . '" rel="' . $jspath . ','. $id . '" target="_blank">' . $name . '</a>';
	}
	else if(isset($fb_user) && $fb_user != "")
	{
		return '<a class="personPopupTrigger" href="https://facebook.com/' . $fb_user . '" rel="' . $jspath .','. $id . '" target="_blank">' . $name . '</a>';
	}
	else if(isset($photo_url) && $photo_url != "")
	{
		return '<a class="personPopupTrigger" href="#" rel="' . $jspath .','. $id . '" target="_blank">' . $name . '</a>';
	}
	else
	{
		return $name;
	}
}

function makeNumericSelect($name, $low, $high, $selected)
{
	$select = "<select name=\"$name\">\r\n";
	foreach(range($low, $high) as $option) {
	   $select .= "<option value=\"$option\"" . ($option==$selected?' selected':'') . ">$option</option>\r\n";
	}
	$select .= "</select>\r\n";
	return $select;
}

function getUpcomingTournamentId($mysqli)
{
	$query = "SELECT tournament_id FROM tournament WHERE is_upcoming=1 LIMIT 1";
	$result = $mysqli->query($query);
	$row = $result->fetch_assoc();
	return $row['tournament_id'];
}

function getThisPlayer($mysqli, $tournid="")
{
	if($tournid == "")
	{
		$tournid = getUpcomingTournamentId($mysqli);
	}
	$email = $_SESSION['logged_user'];
	$query = "SELECT p.*, tp.* FROM players p 
		LEFT JOIN tournament_player AS tp ON p.id=tp.player_id AND tp.tournament_id = $tournid 
		WHERE p.email='$email' LIMIT 1";
	$result = $mysqli->query($query);
	if($result->num_rows > 0)
	{
		$row = $result->fetch_assoc();
		return $row;
	}
	return false;
}

function getPlayerByEmail($mysqli, $email)
{
	$query = "SELECT * FROM players	WHERE email='$email' LIMIT 1";
	$result = $mysqli->query($query);
	if($result->num_rows > 0)
	{
		//echo '<div class="checkpoint">' . $result->num_rows . ' players found...</div>';
		$row = $result->fetch_assoc();
		if($row['uid'])
		{
			$row['regtype'] = "vk";
		}
		elseif($row['fb_user'])
		{
			$row['regtype'] = "fb";
		}
		elseif($row['password'])
		{
			$row['regtype'] = "pw";
		}
		
		return $row;
	}
	return false;
}

function getTournamentPlayers($mysqli, $tournid="", $xtra="", $ext="")
{
	if($tournid == "")
	{
		$tournid = getUpcomingTournamentId($mysqli);
	}
	$players = array();

	if($xtra == -1) //get players that haven't signed up for the upcoming tournament
	{
		$query = "SELECT p.* FROM players p WHERE p.id NOT IN (SELECT tp.player_id FROM tournament_player tp WHERE tp.tournament_id = $tournid)";
	}
	elseif($xtra == "blacklist") //get blacklisted players
	{
		$query = "SELECT p.* FROM players p WHERE blacklisted=1";
	}
	elseif(!$ext)
	{
		$query = "SELECT p.* , tp.* FROM players p, tournament_player tp 
			WHERE p.id = tp.player_id AND tp.tournament_id = $tournid" . $xtra;
	}
	else
	{
		$query = "SELECT p.* , tp.*, ht.* FROM players p, tournament_player tp, hat_teams ht 
			WHERE p.id = tp.player_id AND tp.tournament_id = $tournid AND tp.hat_team = ht.team_id" . $xtra;
	}
			
	$result = $mysqli->query($query);
	if(!$result)
	{
		return false;
	}
	while ($row = $result->fetch_assoc()) 
	{
		$players[] = $row;
	}
	return $players;
}

function getPlayer($mysqli, $player_id, $tournid="")
{
	if($tournid == "")
	{
		$tournid = getUpcomingTournamentId($mysqli);
	}
	$query = "SELECT p.*, tp.* FROM players p 
		LEFT JOIN tournament_player AS tp ON p.id=tp.player_id AND tp.tournament_id = $tournid 
		WHERE p.id=$player_id LIMIT 1";
	$result = $mysqli->query($query);
	if($result->num_rows > 0)
	{
		$row = $result->fetch_assoc();
		return $row;
	}
	return false;
}

function formatMessages($errormsg="", $successmsg="")
{
	$messages = "";
	if($errormsg != "")
	{
		$messages .= '<div id="errorMsg">' . $errormsg . '</div>';
	}
	if($successmsg != "")
	{
		$messages .= '<div id="successMsg">' . $successmsg . '</div>';
	}
	return $messages;
}

//[[a|b]] is labelled "b" on this page but links to page "a".
function parse_tags($match) {
    $text = $match[1];
	$link_arr = explode("|", $text);
    $page_id = $link_arr[0];
	$anchor = $link_arr[1];
    return internaLink($page_id, $anchor);
}

function parse_wikilinks($str) 
{
	return preg_replace_callback('/\[\[(.*?)\]\]/', 'parse_tags', $str);
}

function internaLink($page_id, $anchor)
{
	if(!$page_id || $page_id == 'announcement' || $page_id == 'index')
	{
		return '<a href="' . SITE_URL . '">' . $anchor . '</a>';
	}
	else
	{
		return '<a href="' . SITE_URL . '/' . $page_id . '/">' . $anchor . '</a>';
	}
}

function makeMenuItem($page_id, $text)
{
	if(isset($_REQUEST['params']))
	{
		$params_arr=explode("/", $_REQUEST['params']);
		$page = $params_arr[0] == 'index'?'announcement':$params_arr[0];
	}
	else
	{
		$page = "announcement";
	}

	if($page != $page_id) 
	{
		return internaLink($page_id, $text);
	}
	return $text;
}

function getJerseySizeText($size, $lang)
{
	switch ($size) {
		case "myt":
			return $lang['ownt'];
		case "xs":
			return $lang['xs'];
		case "s":
			return $lang['s'];
		case "m":
			return $lang['msize'];
		case "l":
			return $lang['l'];
		case "xl":
			return $lang['xl'];
		case "xxl":
			return $lang['xxl'];
		default:
			return "?";
	}			
}

function makeThemeSelect($selectName, $selectedTheme)
{
	//Relative to /admin/
	$themesArray = scandir("../themes/");
	$select = '<select name="' . $selectName . '">';
	foreach($themesArray as $theme)
	{
		if(is_dir('../themes/' . $theme) && $theme != "." && $theme != "..")
		{
			$selectedFlag = "";
			if($theme == $selectedTheme)
			{
				$selectedFlag = " selected";
			}
			$select .= '<option value="' . $theme . '"' . $selectedFlag . '>' . $theme . '</option>';
		}
	}
	$select .= "</select>";
	return $select;
}

?>