<?php
	$page_access_level = 2;
	include("include/admin_includes.inc.php");
	
	$xtra = " AND accommodation=1 ";
	if(isset($_GET['show']))
	{
		if($_GET['show'] == 'homeless')
		{
			$xtra .= "AND (accommodation_note='' OR accommodation_note IS NULL)";
		}
		$url_filter = "&amp;show=" . $_GET['show'];
	}
	elseif(isset($_GET['host']))
	{
		$host = $mysqli->real_escape_string(urldecode($_GET['host']));
		$xtra .= " AND accommodation_note='$host'";
	}
	else
	{
		$url_filter = "";
	}
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
	$players = getTournamentPlayers($mysqli, $tournid, $xtra);

	include("include/header_admin.php");
?>
	<a href="<?php echo $_SERVER['PHP_SELF']; ?>?show=homeless">Homeless</a> |
	<a href="<?php echo $_SERVER['PHP_SELF']; ?>?show=all">All</a><br /><br />

	<table class="listing" id="players">
	<tr>
		<th>№</th>
		<th>Name</th>
		<th>Sex</th>
		<th><a href="<?php echo $_SERVER['PHP_SELF'];?>?orderby=club&amp;dir=<?php echo ($dir=="a"?'d':'a');?><?php echo $url_filter; ?>">Club</a></th>
		<th><a href="<?php echo $_SERVER['PHP_SELF'];?>?orderby=city&amp;dir=<?php echo ($dir=="a"?'d':'a');?><?php echo $url_filter; ?>">City</a></th>
		<th>Phone</th>
		<th>Email</th>
		<th>Жильё?</th>
		<th>Куда определяем?</th>
	</tr>
<?php
if(count($players)>0)
{
	$counter=1;
	foreach($players as $player)
	{
		echo "<tr>";
		echo "<td>" . $counter . "</td>";
		echo '<td>' . makePlayerLink($player['id'], $player['name'], $player['uid'], $player['fb_user'], $player['photo']) . "</td>\n";
		echo '<td><img src="../_images/sex_' . ($player['sex'] == 'f' ? 'f' : 'm') . '.png" title="' . ($player['sex'] == 'f' ? $lang['f'] : $lang['m']) . '"></td>';
		echo "<td>" . $player['club'] . "</td>";
		echo "<td>" . $player['city'] . "</td>";
		echo "<td>" . $player['phone'] . "</td>";
		echo "<td>" . $player['email'] . "</td>";
		echo '<td class="accommodation_info">'
			. '<a href="#" id="' . ($player['accommodation'] ? "y" : "n") . '_' . $player['id'] .'">' 
			. ($player['accommodation']?'Нада!':'Не нада!') . '</a></td>';


	//	echo "<td>" . ($player['accommodation'] ? "Нада!" : "") . "</td>";
		echo '<td><div class="edit" style="display:inline" id="note_' . $player['id'] . '">' . ($player['accommodation_note'] ? $player['accommodation_note'] : 'Click to add a note')  . '</div>'
			. ($player['accommodation_note']?' <a href="' . $_SERVER['PHP_SELF'] . '?host=' . urlencode($player['accommodation_note']) . '">&gt;&gt;</a>':'');
		echo "</tr>";
		$counter++;

	}
}
?>
</table>


<br />
<?php
include("include/footer_admin.php");
?>