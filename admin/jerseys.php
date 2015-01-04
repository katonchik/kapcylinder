<?php
	$page_access_level = 2;
	include("include/admin_includes.inc.php");

	/*
	$xtra = " AND lunches>0 ";

	$order_key = "id";
	if(isset($_GET['orderby']))
	{
		$order_key = $_GET['orderby'];
	}
	
	$direction = "ASC";
	$dir = "a";
	if(isset($_GET['dir']))
	{
		$dir = $_GET['dir'];
		if($dir == "a")
		{
			$direction = "ASC";
		}
		else
		{
			$direction = "DESC";
		}
	}
	*/
	
	$xtra = " ORDER BY hat_team, tsize";
	
	$players = getTournamentPlayers($mysqli, $tournid, $xtra, 1);
	//echo print_r($players);

	include("include/header_admin.php");
?>
	<table class="listing" id="players">
	<tr>
		<th>№</th>
		<th>Name</th>
		<th><a href="<?php echo $_SERVER['PHP_SELF'];?>?orderby=club&amp;dir=<?php echo ($dir=="a"?'d':'a');?>">Club</a></th>
		<th><a href="<?php echo $_SERVER['PHP_SELF'];?>?orderby=city&amp;dir=<?php echo ($dir=="a"?'d':'a');?>">City</a></th>
		<th>Phone</th>
		<th>Team</th>
		<th>Size</th>
	</tr>
<?php
if(count($players)>0)
{
	$counter=1;
	$totals = array('xx'=>0, 'xs'=>0, 's'=>0, 'm'=>0, 'l'=>0, 'xl'=>0, 'xxl'=>0, 'myt'=>0);
	foreach($players as $player)
	{
		if(isset($prev_team) && $prev_team != $player['hat_team'])
		{
			echo "<tr>";
			echo "<td colspan=6>" . $prev_team_name . " (" . $prev_team_color . ") </td>";
			echo "<td>";
			foreach($totals as $size => $size_total)
			{
				if($size_total>0)
				{
					echo("$size - $size_total<br />");
				}
			}
			echo "</td>";
			echo "</tr>";
			$totals = array('xx'=>0, 'xs'=>0, 's'=>0, 'm'=>0, 'l'=>0, 'xl'=>0, 'xxl'=>0, 'myt'=>0);
		}
		echo "<tr>";
		echo "<td>" . $counter . "</td>";
		echo '<td>' . makePlayerLink($player['id'], $player['name'], $player['uid'], $player['fb_user'], $player['photo']) . "</td>\n";
		echo "<td>" . $player['club'] . "</td>";
		echo "<td>" . $player['city'] . "</td>";
		echo "<td>" . $player['phone'] . "</td>";
		echo "<td>" . $player['team_name'] . "</td>";
		echo "<td>" . $player['tsize'] . "</td>";
		echo "</tr>";
		$tsize = $player['tsize'];
		$totals[$tsize]++;
		$counter++;
		$prev_team = $player['hat_team'];
		$prev_team_name = $player['team_name'];
		$prev_team_color = $player['color'];
	}
	echo "<tr>";
	echo "<td colspan=6>" . $prev_team_name . " (" . $prev_team_color . ") </td>";
	echo "<td>";
	foreach($totals as $size => $size_total)
	{
		if($size_total>0)
		{
			echo("$size - $size_total<br />");
		}
	}
	echo "</td>";
	echo "</tr>";
	unset($totals);



	
}
?>
</table>


<br />
<?php
include("include/footer_admin.php");
?>