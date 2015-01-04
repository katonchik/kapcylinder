<?php
	$page_access_level = 2;
	include("include/admin_includes.inc.php");

	$roster_html_0 = "";
	$playerlist = array();
	$xtra = " AND participation=" . YES . " ORDER BY basket ASC";
	$players = getTournamentPlayers($mysqli, $tournid, $xtra);
	if(count($players)>0)
	{
		foreach($players as $player)
		{
			$player_id = $player['id'];
			$playerlist[$player_id] = array('name'=>$player['name'], 'team_id'=>$player['hat_team'], 'sex'=>$player['sex']);
			if($player['hat_team'] == 0)
			{
				$basket = (isset($player['basket']) ? $player['basket'] : '-');
				$roster_html_0 .= "\t" . '<li id="p' . $player_id . '" class="player ' . $player['sex'] . '">' . "[$basket] " . $player['name'] . "</li>\r\n";
			}
		}
	}

	$team_boxes = "";
	$query = "SELECT * FROM hat_teams WHERE tournament_id = $tournid ORDER BY team_id";
	$result = $mysqli->query($query);
	while($row = $result->fetch_assoc())
	{
		$team_id = $row['team_id'];
		$team_name = $row['team_name'];

		$team_boxes .= '<div class="team_outer"><div class="teamname">' . $team_name . "</div>\r\n" . '<ul id="t' . $team_id . '" class="team">' . "\r\n";
		$roster_html = "";
		foreach($playerlist as $player_id => $player_data)
		{
			if($team_id == $player_data['team_id'])
			{
				$roster_html .= "\t" . '<li id="p' . $player_id . '" class="player ' . $player_data['sex'] . '">'  . $player_data['name'] . "</li>\r\n";
			}
		}
		$team_boxes .= $roster_html . "\r\n";
		$team_boxes .= "</ul></div>\r\n";
	}


	$xtras = '<LINK href="sorting.css" rel="stylesheet" type="text/css">';
	include("include/header_admin.php");
?>

<div id="global">

  <form>
  <input id="submit" type="submit" name="submit" value="Save" />
  </form>

<div class="team_outer">
<div class="teamname">To draw</div>
<ul id="t0" class='team'>
<?php echo $roster_html_0; ?>
</ul>
</div>

  <div id="teambar">
<?php echo $team_boxes; ?>
  </div>
</div>

</body>
</html>
