<?php

	if (isLoggedIn())
	{
		$player = getThisPlayer($mysqli, $tournid);
		$email 		= $player['email'];
		$name 		= $player['name'];
		$phone 		= $player['phone'];
		$sex 		= $player['sex'];
		$email_news	= $player['email_news'];
		$city 		= $player['city'];
		$club 		= $player['club'];
		$like 		= $player['like'];
		$dislike	= $player['dislike'];
		$arrival	= $player['arrival'];
		$departure	= $player['departure'];

		//$email_reg_news = $player['email_reg_news'];
		//$participation = $player['participation'];
		//$paid = $player['paid'];
		//$accommodation = $player['accommodation'];
		//$accommodation_note = $player['accommodation_note'];
		
		if(isset($player['lunches']))
		{
			if($player['lunches'])
			{
				$lunches_day2 = $player['lunches'] % 10;
				$lunches_day1 = ($player['lunches'] - $lunches_day2) / 10;
			}
			else
			{
				$lunches_day1 = $lunches_day2 = 0;
			}
		}	
		if(isset($player['hat_team']) && $settings['milestone'] == 3)
		{
			$teammates = "<div>\n";
			$teammates_query = "SELECT * FROM players, tournament_player WHERE 
				players.id = tournament_player.player_id AND
				tournament_player.tournament_id = $tournid AND
				hat_team = ${player['hat_team']} 
				ORDER BY basket";
			$result = $mysqli->query($teammates_query);
			while($teammate=$result->fetch_assoc())
			{
				$teammates .= "<div>" . makePlayerLink($teammate['id'], $teammate['name'], $teammate['uid'], $teammate['fb_user'], $teammate['photo'], "./") . "</div>\n";
			}
			$teammates .= "</div>";
		}
	
		
		//Block participation
		$participation_link_no = '<span class="vbutton">' . $lang['participation_link_no'] . '</span>';
		$participation_link_maybe = '<span class="vbutton">' . $lang['participation_link_maybe'] . '</span>';
		$participation_link_yes = '<span class="vbutton">' . $lang['participation_link_yes'] . '</span>';
		
		//Block division
		$division_link_first = '<span class="vbutton">' . $lang['division_link_first'] . '</span>';
		$division_link_second = '<span class="vbutton">' . $lang['division_link_second'] . '</span>';

		//Block tshirt
		$tshirt_link_xs 	= '<span class="vbutton">' . $lang['xs'] . '</span>';
		$tshirt_link_s 		= '<span class="vbutton">' . $lang['s'] . '</span>';
		$tshirt_link_m 		= '<span class="vbutton">' . $lang['msize'] . '</span>';
		$tshirt_link_l 		= '<span class="vbutton">' . $lang['l'] . '</span>';
		$tshirt_link_xl 	= '<span class="vbutton">' . $lang['xl'] . '</span>';
		$tshirt_link_xxl 	= '<span class="vbutton">' . $lang['xxl'] . '</span>';
		$tshirt_link_myt 	= '<span class="vbutton">' . $lang['myt'] . '</span>';

		//Block lunches
		$lunches_link_menu0 = '<span class="vbutton">' . $lang['lunches_menu0'] . '</span>';
		$lunches_link_menu1 = '<span class="vbutton">' . $lang['lunches_menu1'] . '</span>';
		$lunches_link_menu2 = '<span class="vbutton">' . $lang['lunches_menu2'] . '</span>';
		
		
		if(isset($player['tsize']))
		{
			switch($player['tsize'])
			{
				case "myt":
					$tshirt_message = $lang['tshirt_myt'];
					$hide_button_tshirt = "tshirt_myt";
					break;
				case "xs":
					$tshirt_message = $lang['tshirt_xs'];
					$hide_button_tshirt = "tshirt_xs";
					break;
				case "s":
					$tshirt_message = $lang['tshirt_s'];
					$hide_button_tshirt = "tshirt_s";
					break;
				case "m":
					$tshirt_message = $lang['tshirt_m'];
					$hide_button_tshirt = "tshirt_m";
					break;
				case "l":
					$tshirt_message = $lang['tshirt_l'];
					$hide_button_tshirt = "tshirt_l";
					break;
				case "xl":
					$tshirt_message = $lang['tshirt_xl'];
					$hide_button_tshirt = "tshirt_xl";
					break;
				case "xxl":
					$tshirt_message = $lang['tshirt_xxl'];
					$hide_button_tshirt = "tshirt_xxl";
					break;
				default:
					$tshirt_message = $lang['tshirt_unset'];
					unset($hide_button_tshirt);
			}
		}
		else
		{
			$tshirt_message = $lang['tshirt_unset'];
			unset($hide_button_tshirt);
		}


		if(!isset($player['level']))
		{
			$division_message = $lang['division_please_select'];
		}
		elseif($player['level'])
		{
			$division_message = $lang['division_second'];
			$hide_button_division = "division_second";
		}
		else
		{
			$division_message = $lang['division_first'];
			$hide_button_division = "division_first";
		}
		
		if(isset($player['participation']))
		{
			switch($player['participation'])
			{
				case NO:
					$participation_message = $lang['participate_no'];
					$hide_button = "no";
					break;
				case MAYBE:
					$participation_message = $lang['participate_maybe'];
					$hide_button = "maybe";
					break;
				case YES:
					$participation_message = $lang['participate_yes'];
					$hide_button = "yes";
					break;
				case WAITING:
					$participation_message = $lang['participate_waiting'];
					$hide_all_buttons = true;
					break;
				default:
					$participation_message = $lang['participate_unset'];
					break;
			}
		}
		else
		{
			$participation_message = $lang['participate_unset'];
			unset($hide_button);
		}

		if(isset($player['hat_team']))
		{
			$query = "SELECT * FROM hat_teams WHERE tournament_id = $tournid AND team_id=${player['hat_team']}";
			$result = $mysqli->query($query);
			if(!$result)
			{
				//die("Critical Error!!!");
				echo mysql_error();
			}
			$team = $result->fetch_assoc();
			//Block Team
			$team_name = $team['team_name'];
			$color = $team['color'];
			$link = $team['link'];
			$team_msg = $lang['team_name_msg'] . ' <a href="' . $link . '" target="_blank">' . $team_name . '</a>'; 
			if($color)
			{
				$team_color = $lang['team_color_msg'] . ": " . $color . ".";
			}
		}
		
		
		//Block payment
		if(isset($player['paid']) && $player['paid'])
		{
			$paid_message = $lang['you_have_paid'];
		}
		else
		{
			$paid_message = $lang['you_havent_paid'];
		}
			
		//Block Accommodation
		if(isset($player['accommodation']) && $player['accommodation'])
		{
			$accommodation_message = $lang['you_need_accommodation'];
			$accommodation_link = $lang['decline_accommodation'];
		}
		else
		{
			$accommodation_message = $lang['you_dont_need_accommodation'];
			$accommodation_link = $lang['request_accommodation'];
		}

		//Block Lunches
		if(!isset($player['lunches']))
		{
			$lunches_message = $lang['lunches_please_select'];
		}
		else
		{
			$lunches = $player['lunches'];
			$day2 = $lunches % 10;
			$day1 = ($lunches - $day2) / 10;
		
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
		
		if(isset($_REQUEST['err']) && isset($lang['error' . $_REQUEST['err']]))
		{
			$errormsg = $lang['error' . $_REQUEST['err']];
		}
		
		$requestParams = getRequestParams();

		if(isset($requestParams['msg']) && $requestParams['msg'] == 'registered')
		{
			$successmsg = $lang['registered_msg'];
		}
	}


		

	if (isLoggedIn()==="" || !isLoggedIn())
	{
		echo '<div style="margin:50px; font-size:150%; font-weight: bold;">' . internaLink("login", $lang['please_log_in']) . '</div>';
	}
	else

	{
	
?>

	<div style="float:right; padding: 30px 0;">
		<?php if($player['photo']) { ?>
		<div id="player_photo"><img src="<?php echo $player['photo']; ?>" /></div>
		<?php } ?>

		<script>
			//document.getElementsByClassName("infobox")[0].style.opacity = null;
		</script>
		<div id="account_links">
			<ul>
			<?php /*
				<li><a href="#" onclick="openForm('changeContacts');return false;"><?php echo $lang['change_contact_details']; ?> javascript</a></li>
				<li><a href="#" class="openForm" id="changeContacts_link"><?php echo $lang['change_contact_details']; ?> jquery</a></li>
			*/?>

				<li><a href="#" class="openForm" id="changeContacts_link"><?php echo $lang['change_contact_details']; ?></a></li>
				<?php if(isset($player['password']) && $player['password'] != '') { ?>
				<li><a href="#" onclick="openForm('changePassword');return false;"><?php echo $lang['change_password']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div style="overlay:auto;">
		<div id="player_name"><?php echo $player['name'];?> (<?php echo $_SESSION['logged_user'];?>)</div>
		<div class="blockname"><?php echo $lang['participation_blockname']; ?></div>
		<div id="question_participation">
			<p class="question"><?php echo $participation_message; ?></p>
		<?php if(!isset($hide_all_buttons)) { ?>	
			<p class="answer">
		<?php if($settings['milestone'] == 2 || $settings['milestone'] == 3 ){ ?>
				<a href="#" class="yes<?php if(isset($hide_button) && $hide_button == "yes") echo ' choosed'; ?>"><?php echo $participation_link_yes; ?></a>
		<?php } ?>
		<?php if($settings['milestone'] == 2) { ?>
				<a href="#" class="maybe<?php if(isset($hide_button) && $hide_button == "maybe") echo ' choosed'; ?>"><?php echo $participation_link_maybe; ?></a>
		<?php } ?>
				<a href="#" class="no<?php if(isset($hide_button) && $hide_button == "no") echo ' choosed'; ?>"><?php echo $participation_link_no; ?></a>
			</p>
		<?php } ?>
		</div>

		<?php if(isset($player['participation']) && $player['participation'] > NO) 
		{
			$divdisplay = "block";
		}
		else
		{
			$divdisplay = "none";
		}
		?>
		
		<div id="ifRegistered" style="display: <?php echo $divdisplay;?>">
		
			<?php if($settings['milestone'] == 3 AND isset($player['hat_team']) AND $player['hat_team'] != 0) { ?>
				<div class="blockname"><?php echo $lang['team_blockname']; ?></div>
				<div>
					<?php echo $team_msg; ?><br />
					<?php if(isset($team_color)) echo $team_color; ?>
				</div>
				<?php 
				if(isset($teammates))
				{
					echo '<div>' . $lang['in_your_team'] . '</div>';
					echo $teammates; 
				} 
				?>
			<?php } ?>

		
			<?php if($settings['milestone'] == 2 && ($settings['enable_likes'] || $settings['enable_dislikes'])) { ?>
				<div class="blockname"><?php echo $lang['autolotting_blockname']; ?></div>
				<div class="note"><?php echo $lang['autolotting_note']; ?></div>
				<div id="preference_message"></div>
				<?php
				$players_query = "SELECT * FROM players, tournament_player WHERE 
					players.id = tournament_player.player_id AND tournament_player.participation = " . YES 
					. " AND tournament_player.tournament_id = $tournid 
					ORDER BY name";
				$result = $mysqli->query($players_query);
				$likeOptions = '<select id="like" name="like"><option></option>';
				$dislikeOptions = '<select id="dislike" name="dislike"><option></option>';
				while($prefPlayer=$result->fetch_assoc())
				{
					if($prefPlayer['id'] != $player['id'])
					{
						$likeOptions .= '<option value="' . $prefPlayer['id'] . '"' . ($like == $prefPlayer['id'] ? ' selected' : '') . '>' . $prefPlayer['name'] . '</option>' . "</div>\n";
						$dislikeOptions .= '<option value="' . $prefPlayer['id'] . '"' . ($dislike == $prefPlayer['id'] ? ' selected' : '') . '>' . $prefPlayer['name'] . '</option>' . "</div>\n";
					}
				}
				$likeOptions .= '</select>';
				$dislikeOptions .= '</select>';
				
				if($settings['enable_likes'])
				{
					echo '<div class="preference">' . $lang['autolotting_like'] . "<br />" . $likeOptions . '<br /></div>';
				}
				if($settings['enable_dislikes'])
				{
					echo '<div class="preference">' . $lang['autolotting_dislike'] . "<br />" . $dislikeOptions . '<br /></div>';
				}
				
			} ?>
		
		
		
			<div class="blockname"><?php echo $lang['fee_blockname']; ?></div>
			<div><?php echo $paid_message; ?></div>

			<?php if($city != $settings['host_city']) { ?>
				<div id="guests"<?php echo(isset($player['participation'])?"":' style="display:none;"'); ?>>
					<div class="blockname"><?php echo $lang['tickets']; ?></div>
					<div style="display: inline;">
						<?php echo $lang['arrival']; ?>: <input type="time" id="arrival" value="<?php echo $arrival; ?>" />
					</div>
					<div id="timesMessage" style="display: inline;"> </div>
					<div>
						<?php echo $lang['departure']; ?>: <input type="time" id="departure" value="<?php echo $departure; ?>" />
						<input type="button" id="saveTimes" value="<?php echo $lang['save']; ?>" />
					</div>
				
					<?php if($settings['offer_accommodation']) { ?>
						<div class="blockname"><?php echo $lang['accommodation_blockname']; ?></div>
						<div id="question_accommodation">
							<p class="question"><?php echo $accommodation_message; ?></p>
							<p class="answer"><a href="#" class="no"><?php echo $accommodation_link; ?></a></p>
						</div>
						<?php if(isset($accommodation_note)) { ?>
							<div id="accommodation_note"><?php echo $lang['your_accommodation'] . ": " . $accommodation_note; ?></div>
						<?php } ?>
					<?php } ?>
				</div>				
			<?php } ?>

			<?php if(isset($settings['enable_easylvl']) && $settings['enable_easylvl']) { ?>
				<div class="blockname"><?php echo $lang['division_blockname']; ?></div>
				<div id="question_division">
					<p class="question"><?php echo $division_message; ?></p>
				<?php if(!isset($hide_all_buttons) && $settings['milestone'] == 2) { ?>	
					<p class="answer">
						<a href="#" class="division_first<?php if(isset($hide_button_division) && $hide_button_division == "division_first") echo ' choosed'; ?>"><?php echo $division_link_first; ?></a>
						<a href="#" class="division_second<?php if(isset($hide_button_division) && $hide_button_division == "division_second") echo ' choosed'; ?>"><?php echo $division_link_second; ?></a>
					</p>
				<?php } ?>
				</div>
			<?php } ?>
		

			<?php if(isset($settings['offer_jerseys']) && $settings['offer_jerseys']) { ?>
				<div class="blockname"><?php echo $lang['tshirt_blockname']; ?></div>
				<div id="question_tshirt">
					<p class="question"><?php echo $tshirt_message; ?></p>
				<?php if(!isset($hide_all_buttons)) { ?>	
					<p class="answer">
						<a href="#" class="tshirt_xs<?php if(isset($hide_button_tshirt) && $hide_button_tshirt == "tshirt_xs") echo ' choosed'; ?>"><?php echo $tshirt_link_xs; ?></a>
						<a href="#" class="tshirt_s<?php if(isset($hide_button_tshirt) && $hide_button_tshirt == "tshirt_s") echo ' choosed'; ?>"><?php echo $tshirt_link_s; ?></a>
						<a href="#" class="tshirt_m<?php if(isset($hide_button_tshirt) && $hide_button_tshirt == "tshirt_m") echo ' choosed'; ?>"><?php echo $tshirt_link_m; ?></a>
						<a href="#" class="tshirt_l<?php if(isset($hide_button_tshirt) && $hide_button_tshirt == "tshirt_l") echo ' choosed'; ?>"><?php echo $tshirt_link_l; ?></a>
						<a href="#" class="tshirt_xl<?php if(isset($hide_button_tshirt) && $hide_button_tshirt == "tshirt_xl") echo ' choosed'; ?>"><?php echo $tshirt_link_xl; ?></a>
						<a href="#" class="tshirt_xxl<?php if(isset($hide_button_tshirt) && $hide_button_tshirt == "tshirt_xxl") echo ' choosed'; ?>"><?php echo $tshirt_link_xxl; ?></a>
						<a href="#" class="tshirt_myt<?php if(isset($hide_button_tshirt) && $hide_button_tshirt == "tshirt_myt") echo ' choosed'; ?>"><?php echo $tshirt_link_myt; ?></a>
					</p>
				<?php } ?>
				</div>
			<?php } ?>
			
			<?php if(isset($settings['offer_lunches']) && $settings['offer_lunches']) { ?>
				<div class="blockname"><?php echo $lang['lunches_blockname']; ?></div>
				<div id="question_lunches">
				<p class="question"><?php echo $lunches_message; ?></p>
					<p class="answer"><?php echo $lang['lunches_day1']; ?>
						<a href="#" class="day1menu0<?php if(isset($lunches_day1) && $lunches_day1 === 0) echo ' choosed'; ?>"><?php echo $lunches_link_menu0; ?></a>
						<a href="#" class="day1menu1<?php if(isset($lunches_day1) && $lunches_day1 == 1) echo ' choosed'; ?>"><?php echo $lunches_link_menu1; ?></a>
						<a href="#" class="day1menu2<?php if(isset($lunches_day1) && $lunches_day1 == 2) echo ' choosed'; ?>"><?php echo $lunches_link_menu2; ?></a>
					</p>
					<p class="answer"><?php echo $lang['lunches_day2']; ?>
						<a href="#" class="day2menu0<?php if(isset($lunches_day2) && $lunches_day2 === 0) echo ' choosed'; ?>"><?php echo $lunches_link_menu0; ?></a>
						<a href="#" class="day2menu1<?php if(isset($lunches_day2) && $lunches_day2 == 1) echo ' choosed'; ?>"><?php echo $lunches_link_menu1; ?></a>
						<a href="#" class="day2menu2<?php if(isset($lunches_day2) && $lunches_day2 == 2) echo ' choosed'; ?>"><?php echo $lunches_link_menu2; ?></a>
					</p>
				</div>
			<?php } ?>
		</div> <!--end divdisplay-->
	</div><!--end main column-->


	
	
	
	
<div id="changePassword" class="lightbox">
	<form name="change_password" method="post">
		<input type="hidden" name="player_id" value="<?php echo $player['id']; ?>" />
		<strong><?php echo $lang['new_password']; ?> : </strong>
		<input name="password" type="password" id="password" size="25"/><br />
		<strong><?php echo $lang['confirm_new_password']; ?> : </strong>
		<input name="confirm" type="password" id="confirm" size="25"/><br />
		<input type="button" name="submit" class="submit" value="<?php echo $lang['save']; ?>" />
	</form>
</div>

<div id="changeContacts" class="lightbox">
	<form method="post" name="register" onsubmit="return validateForm();">
		<label><b><?php echo $lang['email']; ?> *:</b></label> <input type="email" id="email" name="email" value="<?php if(isset($email)) echo $email; ?>" /><br />
		<?php if(0==1){ ?>
		<b><?php echo $lang['photo']; ?> :</b> <input type="text" id="photo" name="photo" value="<?php echo $photo; ?>" /><br />
		<b><?php echo $lang['thumb']; ?> :</b><input type="text" id="photo_rec" name="photo_rec" value="<?php echo $photo_rec; ?>" /><br />
		<?php } ?>
		<label><b><?php echo $lang['name']; ?> *:</b></label> <input type="text" id="name" name="name" value="<?php if(isset($name)) echo $name; ?>" /><br />
		<label><b><?php echo $lang['phone']; ?> *:</b></label> <input type="text" id="phone" name="phone" value="<?php if(isset($phone)) echo $phone; ?>" /><br />
		<label><?php echo $lang['sex']; ?>:</label> <select name="sex">
		<option value="m"<?php echo (isset($sex) && $sex=='m'?' selected':''); ?>><?php echo $lang['m']; ?></option>
		<option value="f"<?php echo (isset($sex) && $sex=='f'?' selected':''); ?>><?php echo $lang['f']; ?></option>
		</select><br />

		<?php $cityList = buildCityList($mysqli, $tournid); ?>


		 <script type="text/javascript">

            $(document).ready(function () {

            	var selectedCity = "<?php echo $city; ?>";
				<?php
				$cities_array_js = json_encode($cityList);
				echo "var cities = ". $cities_array_js . ";\n";
				?>
                // Create a jqxComboBox
                $("#jqx_city").jqxComboBox({ source: cities, width: '220px', height: '25px' });

                var myCity = $("#jqx_city").jqxComboBox('getItemByValue', selectedCity);
                $("#jqx_city").jqxComboBox('selectIndex', myCity.index);

                // bind to 'select' event.
                $('#jqx_city').bind('select', function (event) {
                    var args = event.args;
                    var item = $('#jqx_city').jqxComboBox('getItem', args.index);
                });
            
            	var selectedClub = "<?php echo $club; ?>";
				<?php
				$clubList = array_map('trim',explode(",", $settings['expected_clubs']));
				$clubList[] = $lang['noclub'];
				$clubs_array_js = json_encode($clubList);
				echo "var clubs = ". $clubs_array_js . ";\n";
				?>
                // Create a jqxComboBox
                $("#jqx_club").jqxComboBox({ source: clubs, width: '220px', height: '25px' });

                var myClub = $("#jqx_club").jqxComboBox('getItemByValue', selectedClub);
                $("#jqx_club").jqxComboBox('selectIndex', myClub.index);

                // bind to 'select' event.
                $('#jqx_club').bind('select', function (event) {
                    var args = event.args;
                    var item = $('#jqx_club').jqxComboBox('getItem', args.index);
                });

            });
        </script>

			<label><?php echo $lang['city']; ?>:</label>
        	<div id='jqx_city'></div>
			<br />
			<label><?php echo $lang['club']; ?>:</label>
        	<div id='jqx_club'></div>


		<br />
		<?php echo $lang['email_news']; ?>: <input type="checkbox" id="email_news" name="email_news" value="1" <?php echo ($player['email_news'] ? 'checked="checked" ' : ''); ?>/><br />
		<?php /* ?>
		<?php echo $lang['email_reg_news']; ?>: <input type="checkbox" id="email_reg_news" name="email_reg_news" value="1" <?php echo ($email_reg_news ? 'checked="checked" ' : ''); ?>/><br />
		<?php */ ?>
		<?php /* ?>
		<input type="submit" name="submit" id="mySubmit" class="submit" value="<?php echo $lang['save']; ?>" onclick="javascript: submitForm(); return false;" />
		<?php */ ?>
		<input type="button" name="submit" class="submit" value="<?php echo $lang['save']; ?>" />
	</form>
</div>

<?php } ?>