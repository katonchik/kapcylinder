<?php

function displayPlayerList($rows, $listName, $lang, $settings)
{
	$avatar_th = '';
	$gender_th = '';
	$jersey_th = '';
	$social_network_th = '';
	$status_th = '';
	
	$page = '';
	$listComment = '';
	if($listName == 'blacklist')
	{
		$page = 'blacklist';
//		$listName = $lang['blacklist'];
		$listName = '';
		$listComment = $lang['blacklist_comment'];
	}
	
	if(isset($settings['show_avatars']) && $settings['show_avatars']) 
		$avatar_th = '<th>&nbsp;</th>';
	if(isset($settings['show_gender']) && $settings['show_gender']) 
		$gender_th = '<th class="sex">&nbsp;</th>';
	if(isset($settings['offer_jerseys']) && $settings['offer_jerseys'] && $page != 'blacklist') 
		$jersey_th = '<th class="tshirt">' . $lang['tshirt'] .'</th>';
	if(isset($settings['show_social']) && $settings['show_social']) 
		$social_network_th = '<th class="soc">&nbsp;</th>';
	if($page != 'blacklist') 
		$status_th = '<th class="status">&nbsp;</th>';
	 
    echo <<<HTML

	<h3 class="page_title">{$listName}</h3>
	<p>{$listComment}</p>
	<table class="players" cellspacing="0">
		<tr>
			<th class="num">#</th>
			{$avatar_th}
			<th class="name">{$lang['name']}</th>
			<th class="club">{$lang['club']}</th>
			<th class="city">{$lang['city']}</th>
			{$gender_th}
			{$jersey_th}
			{$social_network_th}
			{$status_th}
		</tr>
HTML;

	$output = "";
	$counter = 1;
    foreach($rows as $row)
    {
		$playerLink 			= makePlayerLink($row['id'], $row['name'], $row['uid'], $row['fb_user'], $row['photo'], "./");
		
		$avatar_td = "";
		$gender_td = "";
		$jersey_td = "";
		$status_td = "";
		
		if(isset($settings['show_avatars']) && $settings['show_avatars']) 
		{
			$imgSrc = "";
			if($row['photo_cropped'])
			{
				$imgSrc = $row['photo_cropped'];
			} 
			elseif($row['photo_rec'])
			{
				$imgSrc = $row['photo_rec'];
			}
			
			if($imgSrc)
			{
				$avatarImg 			= '<img src="' . $imgSrc . '" alt="' . $row['name'] . '" class="avatar" />';
				$avatar_td			= '<td>' . $avatarImg . '</td>';
			}
			else
			{
				$avatar_td			= '<td>&nbsp;</td>';
			}
		}
			
		if(isset($settings['show_gender']) && $settings['show_gender']) 
		{
			$genderIconImg 		= '<img src="' . SITE_URL . '/_images/sex_' . ($row['sex'] == 'f' ? 'f' : 'm') . '.png" title="' . ($row['sex'] == 'f' ? $lang['f'] : $lang['m']) . '" />';
			$gender_td			= '<td style="text-align:center;">' . $genderIconImg . '</td>';
		}
			
		if(isset($settings['offer_jerseys']) && $settings['offer_jerseys'] && $page != 'blacklist') 
		{
			$jerseySizeText 	= getJerseySizeText($row['tsize'], $lang);
			$jerseyIconImg 		= '<img src="'.SITE_URL.'/_images/t-shirt_icon.png" />';
			$jerseySizeClass 	= ($row['paid'] && !$row['tunpaid']) ? 'tshirt-paid' : 'tshirt-unpaid';
			$jersey_td 			= '<td class="t-shirt">' . $jerseyIconImg . '<span class="' . $jerseySizeClass . '"><b>' . $jerseySizeText . '</b></span></td>';
		}

		if(isset($settings['show_social']) && $settings['show_social']) 
		{
			if($row['fb_user']) {
				$socUrl 	= 'https://www.facebook.com/' . $row['fb_user'];
				$socTarget 	= ' target="_blank"';
				$socImgUrl 	= SITE_URL . '/_images/fb_logo.png';
				$socTitle	= $lang['fb_page'];
			}
			elseif($row['uid']) {
				$socUrl 	= 'https://www.vk.com/id' . $row['uid'];
				$socTarget 	= ' target="_blank"';
				$socImgUrl 	= SITE_URL . '/_images/vk_logo.png';
				$socTitle	= $lang['fb_page'];
			}
			else {
				$socUrl 	= '#';
				$socTarget 	= '';
				$socImgUrl 	= SITE_URL . '/_images/vk_logo_off.png';
				$socTitle	= '';
				$social_network_td =  '<td class="vk">&nbsp</td>';
			}
			$social_network_td =  '<td class="vk"><a href="' . $socUrl .'"' . $socTarget . ' style="cursor: default"><img src="'. $socImgUrl . '" title="' . $socTitle . '" /></a></td>';
		}
		else
		{
			$social_network_td = "";
		}
		
		if($page == 'blacklist')
		{
			$status_td = "";
		} 
		elseif($row['paid'])
		{
			$paidClass = "color-paid";
			$paidText =  $lang['paid'];
			$status_td = '<td class="' . $paidClass . '">' . $paidText .'</td>';
		}
		else
		{
			$paidClass = "color-not-paid";
			$paidText =  $lang['not_paid'];
			$status_td = '<td class="' . $paidClass . '">' . $paidText .'</td>';
		}
		
		echo <<<HTML

			<tr class="{$row['sex']}">
				<td class="num">{$counter}</td>
				{$avatar_td}
				<td class="playername">{$playerLink}</td>
				<td class="club">{$row['club']}</td>
				<td class="city">{$row['city']}</td>
				{$gender_td}{$jersey_td}{$social_network_td}{$status_td}
			</tr>
HTML;
	$counter++;
	} //end foreach row
	echo "</table>\n";

}

if($page=="blacklist")
{
	$playersBlacklisted = getTournamentPlayers($mysqli, $tournid, "blacklist");
	displayPlayerList($playersBlacklisted, "blacklist", $lang, $settings);
}
else
{
	if($settings['enable_easylvl'])
	{
		$playersStandard = getTournamentPlayers($mysqli, $tournid, " AND participation=" . YES . " AND (level <> 1 OR level is NULL) ORDER BY paid DESC, id");
		displayPlayerList($playersStandard, $lang['confirmed'], $lang, $settings);

		$playersEasy = getTournamentPlayers($mysqli, $tournid, " AND participation=" . YES . " AND level = 1 ORDER BY paid DESC, id"); //paid DESC, 
		displayPlayerList($playersEasy, $lang['confirmed_easy'], $lang, $settings);
	}
	else
	{
		$playersStandard = getTournamentPlayers($mysqli, $tournid, " AND participation=" . YES . " ORDER BY paid DESC, id");
		displayPlayerList($playersStandard, $lang['confirmed'], $lang, $settings);
	}

	if($settings['milestone'] == 3)
	{
		$playersWaiting = getTournamentPlayers($mysqli, $tournid, " AND participation=" . WAITING . " ORDER BY level, id"); //waiting for free spots
		displayPlayerList($playersWaiting, $lang['waiting_list'], $lang, $settings);
	}
	else
	{
		$playersUnconfirmed = getTournamentPlayers($mysqli, $tournid, " AND participation=" . MAYBE . " ORDER BY level, id"); //have not confirmed participation
		displayPlayerList($playersUnconfirmed, $lang['unconfirmed'], $lang, $settings);
	}
}
	
?>
