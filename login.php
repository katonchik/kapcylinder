<?php

	if(isLoggedIn())
	{
		header("Location: " . SITE_URL . "/home/");
		exit();
	}

function curl_get_contents($url)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);

    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}

	require_once("include/facebook.php");

	$facebook = new Facebook(array(
	  'appId'  => $fb_app_id,
	  'secret' => $fb_app_secret,
	));

	$params = array(
	  //'response_type' => 'code',
	  //'client_id' => $fb_app_id,
	  'redirect_uri' => SITE_URL . "/login/fb/",
	);

	$fb_user = $facebook->getUser();
	//$fb_loginUrl = $facebook->getLoginUrl($params);
	//echo "<!--fb_loginUrl: [{$fb_loginUrl}]; fb_user: [{$fb_user}]; -->";

	if ($fb_user) 
	{
		try
		{
			// Proceed knowing you have a logged in user who's authenticated.
			$fb_user_profile = $facebook->api('/me');
			$fb_name = $fb_user_profile['name'];
		}
		catch (FacebookApiException $e) 
		{
			//Limited support for Facebook: can't retrieve name and other details to pre-fill the form
		}
	}

	$requestParams = getRequestParams();
	
	if(isset($_POST['submit'])) //if authenticated through login and password (no social networks)
	{
		$email = $mysqli->real_escape_string($_POST['email']);
		$password = $mysqli->real_escape_string($_POST['password']);

		//Get login and pass from db
		$query = "SELECT * FROM players WHERE email='$email' LIMIT 1";
		$result = $mysqli->query($query);
		if(!$result || $result->num_rows == 0)//if login is in the database
		{
			$errormsg = "Login failed";
		}
		else
		{
			$row = $result->fetch_assoc();
			if(md5($password) == $row['password']) 
			{
				$_SESSION['logged_user'] = $email;
				$location = SITE_URL . "/home/";
			}
			else
			{
				$errormsg = $lang['bad_password'];
			}
		}
	}	
	elseif($filter == "vk") //this is returned by VK authentication as defined in VK application settings
	{
		$requestParams = getRequestParams();
		$uid  = $requestParams['uid'];
		$hash = $requestParams['hash'];
		$first_name = $requestParams['first_name'];
		$last_name = $requestParams['last_name'];
		$photo = $requestParams['photo'];
		$photo_rec = $requestParams['photo_rec'];
		if(isVkAuthorized($app_id, $security_key, $uid, $hash))
		{
			$query = "SELECT * FROM players WHERE uid=$uid";
			$result = $mysqli->query($query);
			if ($result->num_rows > 0)
			{
				$row=$result->fetch_assoc();
				$_SESSION['logged_user'] = $row['email'];
				$location = SITE_URL . "/home/";
			}
			else
			{
				$_SESSION['uid'] = $uid;
				$_SESSION['hash'] = $uid;
				$_SESSION['name'] = urldecode($requestParams['first_name'] . " " . $requestParams['last_name']);
				$_SESSION['photo'] = $requestParams['photo'];
				$_SESSION['photo_rec'] = $requestParams['photo_rec']; //маленькое
				//$location = SITE_URL . "/home/" . $_SERVER['QUERY_STRING']; //Not sure what this was for?
				$location = SITE_URL . "/register/";
			}
		}
	}	
	//elseif(isset($requestParams['code']))//facebook - after authentication
	elseif($filter == "fb")//facebook - after authentication
	{
		if (!$fb_user) //Added 01.01.2015
		{
			$requestParams = getRequestParams();
			$code = $requestParams["code"];
			$my_url = SITE_URL . "/login/fb/";

			if(empty($code)) {
				$_SESSION['state'] = md5(uniqid(rand(), TRUE)); //CSRF protection
				$dialog_url = "http://www.facebook.com/dialog/oauth?client_id=" 
				. $fb_app_id . "&redirect_uri=" . urlencode($my_url) . "&state="
				. $_SESSION['state'];

				echo("<script> top.location.href='" . $dialog_url . "'</script>");
			}

			$token_url = "https://graph.facebook.com/oauth/access_token?"
			. "client_id=" . $fb_app_id . "&redirect_uri=" . urlencode($my_url)
			. "&client_secret=" . $fb_app_secret . "&code=" . $code;
			$response = @curl_get_contents($token_url);
			$params = null;
			parse_str($response, $params);

			$graph_url = "https://graph.facebook.com/me?access_token=" 
			. $params['access_token'];

			$user = json_decode(curl_get_contents($graph_url));
			//echo print_r($user);
			$fb_user = $user->id;
			//echo $fb_user;
		
		}
		// end Added 01.01.2015
		
		$query = "SELECT * FROM players WHERE fb_user=$fb_user";
		$result = $mysqli->query($query);
		if ($result->num_rows > 0) //user already registered
		{
			$row=$result->fetch_assoc();
			$_SESSION['logged_user'] = $row['email'];
			$location = SITE_URL . "/home/";
		}
		else
		{
			$_SESSION['fb_user'] = $fb_user;
			if(isset($fb_user_profile))
			{
				$_SESSION['name'] = $fb_user_profile['name'];
				$_SESSION['sex'] = $fb_user_profile['gender']=='male'?'m':'f';
			}
			else //Added 01.01.2015
			{
				$_SESSION['name'] = $user->name;
				$_SESSION['email'] = $user->email;
				$_SESSION['sex'] = $user->gender=='male'?'m':'f';
			}
			$_SESSION['photo'] = "https://graph.facebook.com/" . $fb_user . "/picture";
			$_SESSION['photo_rec'] = "https://graph.facebook.com/" . $fb_user . "/picture";
			//$location = SITE_URL . "/register/" . $_SERVER['QUERY_STRING'];
			$location = SITE_URL . "/register/";
		}

	}


	if(isset($location))
	{
		header("Location: $location");
		exit();
	}


	$loginUrl = $facebook->getLoginUrl($params);
	
	
	include("include/header.php");

	echo "<!-- fb_user = [{$fb_user}] -->";
	
	if(isset($filter) && $filter == 'passreset')
	{
		$successmsg = "Password reset. Please log in with the new password.";
	}

	echo formatMessages($errormsg, $successmsg);
	
?>

<div>
	<div style="float:right; width: 40%; overflow: auto; min-width: 280px; overflow: auto;">
		<h3 class="heading"><?php echo $lang['without_vk']; ?></h3>
		<div id="login_form">
			<form name="login_form" method="post" action="<?php echo SITE_URL; ?>/login/">
			<?php echo $lang['email']; ?> <input type="text" name="email" /><br />
			<?php echo $lang['password']; ?> <input type="password" name="password" /><br />
			<input type="submit" name="submit" value="<?php echo $lang['log_in']; ?>" /> <?php echo internaLink("forgotpass", $lang['forgot_password']); ?> <br />
			<br />
			<?php echo internaLink("register", $lang['first_time']); ?>
			</form>	
		</div>
	</div>
	<div style="float:left; width: 30%; min-width: 205px; min-height: 200px;">
		<h3 class="heading"><?php echo $lang['with_vk']; ?></h3>
		<!-- Put this div tag to the place, where Auth block will be -->
		<div id="vk_auth"></div>
		<script type="text/javascript">
		VK.Widgets.Auth("vk_auth", {width: "200px", authUrl: '<?php echo SITE_URL;?>/login/vk/?'});
		</script>
	</div>

	<div style="float:left; width: 30%;">
		<h3 class="heading">Facebook</h3>
		<?php /* not working
		<fb:login-button scope="public_profile,email" onlogin="checkLoginState();">
</fb:login-button>
	<div class="fb-login-button" data-max-rows="1" data-size="xlarge" data-show-faces="false" data-auto-logout-link="false"></div>
		*/ ?>
		<a href="<?php echo $loginUrl; ?>"><?php echo (isset($fb_name) ? $lang['log_in_as'] . " " . $fb_name : '<img src="' . SITE_URL . '/images/facebook.png" />'); ?></a>
	</div>
	
</div>

