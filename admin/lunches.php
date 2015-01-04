<?php
	$page_access_level = 2;
	include("include/admin_includes.inc.php");


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
	
	$xtra .= " ORDER BY $order_key $direction";

	$xtra = " AND lunches>0 ORDER BY lunches";
	$players = getTournamentPlayers($mysqli, $tournid, $xtra);

	include("include/header_admin.php");
?>
	<table class="listing" id="players">
	<tr>
		<th>№</th>
		<th>Name</th>
		<th><a href="<?php echo $_SERVER['PHP_SELF'];?>?orderby=club&amp;dir=<?php echo ($dir=="a"?'d':'a');?>">Club</a></th>
		<th><a href="<?php echo $_SERVER['PHP_SELF'];?>?orderby=city&amp;dir=<?php echo ($dir=="a"?'d':'a');?>">City</a></th>
		<th>Phone</th>
		<th>Day 1</th>
		<th>Day 2</th>
	</tr>
<?php
if(count($players)>0)
{
	$counter=1;
	$day1_menu1_total = 0;
	$day1_menu2_total = 0;
	$day2_menu1_total = 0;
	$day2_menu2_total = 0;
	foreach($players as $player)
	{
		$lunches = $player['lunches'];
		$day2 = $lunches % 10;
		$day1 = ($lunches - $day2) / 10;
		
		echo "<tr>";
		echo "<td>" . $counter . "</td>";
		echo '<td>' . makePlayerLink($player['id'], $player['name'], $player['uid'], $player['fb_user'], $player['photo']) . "</td>\n";
		echo "<td>" . $player['club'] . "</td>";
		echo "<td>" . $player['city'] . "</td>";
		echo "<td>" . $player['phone'] . "</td>";
		echo "<td>" . $lang['lunches_menu' . $day1] . "</td>";
		echo "<td>" . $lang['lunches_menu' . $day2] . "</td>";
		echo "</tr>";
		
		if($day1 == 1) $day1_menu1_total++;
		if($day1 == 2) $day1_menu2_total++;
		if($day2 == 1) $day2_menu1_total++;
		if($day2 == 2) $day2_menu2_total++;
		$counter++;

	}
	echo '<tr>';
	echo '<td colspan="5"><strong>' . $lang['lunches_total'] . '</strong></td>';
	echo '<td><strong>' . $lang['lunches_menu1'] . ' - ' . $day1_menu1_total . "<br />"
		. $lang['lunches_menu2'] . ' - ' . $day1_menu2_total . "</strong></td>";
	echo '<td><strong>' . $lang['lunches_menu1'] . ' - ' . $day2_menu1_total . "<br />"
		. $lang['lunches_menu2'] . ' - ' . $day2_menu2_total . "</strong></td>";
	echo "</tr>";
	
}
?>
</table>


<br />
<?php
include("include/footer_admin.php");
?>