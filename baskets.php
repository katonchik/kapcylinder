<?php
		$players = getTournamentPlayers($mysqli, $tournid, " AND participation=3 ORDER BY basket ASC");
		if(count($players) < 1)
		{
			echo $lang['baskets_not_created'];//"Baskets not created yet.";
		}
		else
		{
			//Determine the height of a team block
			$query2 = "SELECT MAX(tp.b) as b FROM (SELECT COUNT(basket) AS b FROM tournament_player WHERE participation =3 GROUP BY basket) tp";
			$result2 = $mysqli->query($query2);
			$row2 = $result2->fetch_assoc();
			$max_players = $row2['b'];
			$team_block_height = 23 * $max_players + 67;
		
			$previous_basket = -1;
			$counter = 0;
			$waiting_list = "";
			echo '<div id="basket_page">';
			foreach($players as $row) 
			{
			//	echo "<br /><br />" . print_r($row) . "<br />";
				
				$current_basket = $row['basket'];

				if($previous_basket!=$current_basket && $previous_basket == 0)
				{
					$waiting_list .= "</ol></div>\r\n";
				}
				if($previous_basket!=$current_basket && $previous_basket > 0)
				{
					echo "</ol></div>\r\n";
				}
				
				if($previous_basket!=$current_basket && $current_basket == 0)
				{
					$waiting_list .= '<div class="frontend_team" style="min-height:' . $team_block_height . 'px;"><h3 class="heading">' . $lang['waiting_list'] . '</h3>' . "\r\n<ol>\r\n";
				}
				if($previous_basket!=$current_basket && $current_basket > 0)
				{
					echo '<div class="frontend_team" style="min-height:' . $team_block_height . 'px;"><h3 class="heading">' . $lang['basket'] . " " . $row['basket'] . "</h3>\r\n<ol>\r\n";
				}
				if($current_basket == 0)
				{
					//$waiting_list .= "\t" . '<li class="' . $row['sex']	. '">' . makePlayerLink($row['id'], $row['name'], $row['uid'], $row['photo'], "./") . ($row['paid']?' (опл.)':'<span style="color:red"> (не опл.)</span>') . "</li>\r\n";
					$waiting_list .= "\t" . '<li class="' . $row['sex']	. '">' . makePlayerLink($row['id'], $row['name'], $row['uid'], row['fb_user'], $row['photo']) . "</li>\r\n";
				}
				else
				{
					//echo "\t" . '<li class="' . $row['sex']	. '">' . makePlayerLink($row['id'], $row['name'], $row['uid'], $row['photo'], "./") . ($row['paid']?' (опл.)':'<span style="color:red"> (не опл.)</span>') . "</li>\r\n";
					echo "\t" . '<li class="' . $row['sex']	. '">' . makePlayerLink($row['id'], $row['name'], $row['uid'], row['fb_user'], $row['photo']) . "</li>\r\n";
				}
				$previous_basket = $current_basket;
				$counter++;
			}

			echo "</ol></div>\r\n";
			if(isset($waiting_list))
			{
				echo $waiting_list;
			}
			//echo '<div style="height: 460px;"></div>' . "<p>Всього $counter чол.</p>";
			echo '</div>';
		}
?>
