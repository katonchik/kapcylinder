<?php
	$page_access_level = 1;
	include("include/admin_includes.inc.php");
	include("include/header_admin.php");

	if($access_granted)
	{
		
		$xtra = " AND (participation = " . YES . " OR participation = " . MAYBE . ") ORDER BY name";
		$playersArr = getTournamentPlayers($mysqli, $tournid, $xtra);

		$players = array();
		foreach($playersArr as $playerArr)
		{
			$playerID = $playerArr['id'];
			$player = new Player($playerArr);
			$players[$playerID] = $player;
		}

		echo '<table class="listing"><tr><th>Name</th>';
		if($settings['enable_likes'])
		{
			echo '<th>Likes</th>';
		}
		if($settings['enable_dislikes'])
		{
			echo '<th>Dislikes</th>';
		}
		if($settings['enable_likes'])
		{
			echo '<th>Liked by</th>';
		}
		if($settings['enable_dislikes'])
		{
			echo '<th>Disliked by</th>';
		}
		echo '</tr>';
		foreach($players as $player)
		{
			$likeID = $player->like;
			$dislikeID = $player->dislike;

			//echo "<!--" . print_r($player) . "<br />-->";
			echo '<tr><td class="player-link">' . $player->makeAdminLink().'</td>';
			if($settings['enable_likes'])
			{
				echo '<td class="player-link">' . ($likeID ? $players[$likeID]->makeAdminLink() : '') . '</td>';
			}
			if($settings['enable_dislikes'])
			{
				echo '<td class="player-link">' . ($dislikeID ? $players[$dislikeID]->makeAdminLink() : '') . '</td>';
			}
			if($settings['enable_likes'])
			{
				$likeCount = $player->getLikeCount($mysqli, $tournid);
				$class = "hilite" . floor($likeCount / 2);
				echo "<td class=\"{$class}\">" . $likeCount . '</td>';
			}
			if($settings['enable_dislikes'])
			{
				$dislikeCount = $player->getDislikeCount($mysqli, $tournid);
				$class = "hilite" . floor($dislikeCount / 3);
				echo "<td class=\"{$class}\">" . $dislikeCount . '</td>';
			}
			echo '</tr>';
		}
		echo "</table>";
	
	}
	else
	{
		echo "<div class=\"errorMsg\">Access denied!</div>";
	}
	
	include("include/footer_admin.php");

?>
