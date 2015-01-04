<?php
	$page_access_level = 1;
	include("include/admin_includes.inc.php");
	include("include/header_admin.php");

	if(!$access_granted)
	{
		echo "<div class=\"errorMsg\">Access denied!</div>";
	}
	else
	{
		$players = getTournamentPlayers($mysqli, $tournid, ' AND participation = ' . YES . ' ORDER BY name ASC');
		$baskets = array();
		foreach($players as $player)
		{
			$player_id = $player['id'];
			$basket = $player['basket'];
			//echo "<!-- {$player['name']} in basket {$basket} -->\r\n";
			
			$playerDiv = "\t" . '<div id="player' . $player_id . '" data-playerID="' . $player_id . '" class="player ' . $player['sex'] . '" draggable="true">' . $player['name'] . "</div>\r\n";

			if(!isset($baskets[$basket]) || !$baskets[$basket])
			{
				$baskets[$basket] = array();
			}
			//array_push($baskets[$basket], $playerDiv);
			if(is_string($baskets[$basket]))
			{
				echo $baskets[$basket] . "<br />";
			}
			else
			{
				$baskets[$basket][] = $playerDiv;
			}
			
		}
		//echo "<!-- " . print_r($baskets[5]) . " -->\r\n";
	?>

	<!-- BEGIN distributor -->
	<div id="distributor">

	<?php
		$basketCount = $settings['number_of_baskets'];
		//echo "Total {$basketCount} baskets<br/ >\r\n";
		for($i=0; $i<=$basketCount; $i++)
		{
			echo "<!-- Basket {$i} -->\r\n";
			if($i == 0)
			{
				$playerListClass = "pool";
			}
			else
			{
				$playerListClass = "basket";
			}
			echo '<div id="basket' . $i . '" data-basket="' . $i . '" class="playerList ' . $playerListClass .'">' . "\r\n";
			if($i > 0)
			{
				echo "\t" . '<div class="basket_legend">' . $lang['basket'] . " " . $i . "</div>\r\n";
			}
			$basket = $baskets[$i];
			foreach($basket as $player)
			{
				echo $player;
			}
			echo "</div>\r\n";
		}
	?>

	</div>
	<!-- END distributor -->

<?php
	}

	include("include/footer_admin.php");
?>