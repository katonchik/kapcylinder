<?php
	$page_access_level = 2;
	include("include/admin_includes.inc.php");

	$baskets = $settings['number_of_baskets'];

	if(isset($_GET['action']) && isset($_GET['pid']))
	{
		$action = $_GET['action'];
		$pid = $_GET['pid']; 

		if($action == 'delete')
		{
			//Delete players from the tournament:
			//$query = "DELETE FROM players WHERE id=$pid";
			$query = "DELETE FROM tournament_player WHERE player_id=$pid AND tournament_id=$tournid";
			//TODO: implement separate deletion of players from tournament and from the entire system 
			//(latter should not be normally used.  This will be done on a separate page.)
			$result = $mysqli->query($query);
			if(!$result) 
			{
				$errormsg = "Failed to delete player";
			}
			else
			{
				$successmsg = "Player deleted";
				$log_text = $_SESSION['logged_admin'] . " deleted " . $_GET['name'];
				WriteLog($mysqli, CONTACTS, $log_text, $pid);
			}
		}
	}

	$xtra = " AND";
	$mobile=0;
	if(isset($_GET['show']))
	{
		switch($_GET['show'])
		{
			case 'unpaid':
				$xtra .= " participation != " . NO . " AND paid=0";
				break;
			case 'paid':
				$xtra .= " paid=1";
				break;
			case 'no':
				$xtra .= " participation = " . NO;
				break;
			case 'maybe':
				$xtra .= " participation = " . MAYBE;
				break;
			case 'yes':
				$xtra .= " participation = " . YES;
				break;
			case 'waiting':
				$xtra .= " participation = " . WAITING;
				break;
			case 'guests':
				$xtra .= " city != '{$settings['host_city']}'";
				break;
			case 'old':
				$xtra = -1;
				break;
			case 'all':
			default:
				$xtra .= " participation != -1";
		}
		$url_filter = "&amp;show=" . $_GET['show'];
	}
	else
	{
		$xtra .= " participation != " . NO;
		$url_filter = "";
	}


	$order_key = "name";
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
echo "host city: " . $settings['host_city'] . "<br />";
	if($players===false)
	{
		echo "ERROR";
	}
	else
	{

?>

	<a href="<?php echo $_SERVER['PHP_SELF']; ?>?show=all">All</a> 
			&nbsp; &nbsp;  &nbsp;
	<a href="<?php echo $_SERVER['PHP_SELF']; ?>?show=paid">Paid</a> |
	<a href="<?php echo $_SERVER['PHP_SELF']; ?>?show=unpaid">Unpaid</a> 
			&nbsp; &nbsp;  &nbsp;
	<a href="<?php echo $_SERVER['PHP_SELF']; ?>?show=yes">Yes</a> |
<?php if($settings['milestone'] == 3) { ?>
	<a href="<?php echo $_SERVER['PHP_SELF']; ?>?show=waiting">Waiting</a>
<?php } else { ?> 
	<a href="<?php echo $_SERVER['PHP_SELF']; ?>?show=maybe">Maybe</a> |
	<a href="<?php echo $_SERVER['PHP_SELF']; ?>?show=no">No</a>
<?php } ?>
			&nbsp; &nbsp;  &nbsp;
	<a href="<?php echo $_SERVER['PHP_SELF']; ?>?show=guests">Guests</a>
			&nbsp; &nbsp;  &nbsp;
	<a href="<?php echo $_SERVER['PHP_SELF']; ?>?show=old">Previous Tournament</a>
	<br /><br />
	<table class="listing handheld" id="players">
	<tr>
		<th>№</th>
		<th><a href="<?php echo $_SERVER['PHP_SELF'];?>?orderby=name&amp;dir=<?php echo ($dir=="a"?'d':'a');?><?php echo $url_filter; ?>">Name</a></th>
		<th>Sex</th>
		<th><a href="<?php echo $_SERVER['PHP_SELF'];?>?orderby=club&amp;dir=<?php echo ($dir=="a"?'d':'a');?><?php echo $url_filter; ?>">Club</a></th>
		<th><a href="<?php echo $_SERVER['PHP_SELF'];?>?orderby=city&amp;dir=<?php echo ($dir=="a"?'d':'a');?><?php echo $url_filter; ?>">City</a></th>
<?php if(isset($_GET['show']) && ($_GET['show'] == "guests")) { ?>
		<th><a href="<?php echo $_SERVER['PHP_SELF'];?>?orderby=arrival&amp;dir=<?php echo ($dir=="a"?'d':'a');?><?php echo $url_filter; ?>">Arrivals</th>
		<th><a href="<?php echo $_SERVER['PHP_SELF'];?>?orderby=departure&amp;dir=<?php echo ($dir=="a"?'d':'a');?><?php echo $url_filter; ?>">Departures</th>
<?php } ?>
		<th>Phone</th>
<?php /*
		<th>Email</th>
*/ ?>

<?php if(!isset($_GET['show']) || $_GET['show'] != "old") { ?>
			<th width="100">Participation</th>
<?php /*
			<th width="<?php echo $baskets*11+13; ?>"><a href="<?php echo $_SERVER['PHP_SELF'];?>?orderby=basket&amp;dir=<?php echo ($dir=="a"?'d':'a');?><?php echo $url_filter; ?>">Basket</a></th>		
*/ ?>
	<?php if ($_SESSION['access_level'] == 1) { ?>
			<th>Paid?</th>
	<?php } ?>
<?php } ?>

<?php if ($_SESSION['access_level'] == 1) { ?>
		<th>&nbsp;</th>
<?php } ?>

	</tr>
<?php

if(count($players)>0)
{
	$counter=1;
	foreach($players as $player) 
	{
		$playerId = $player['id'];
		if(isset($_GET['show']) && $_GET['show'] == "old") {
			echo "\t" . '<tr>';
		} else {
			switch($player['participation'])
			{
				case NO:
					$participation = "No";
					break;
				case MAYBE:
					$participation = "Maybe";
					break;
				case YES:
					$participation = "Yes";
					break;
				case WAITING:
					$participation = "Waiting";
					break;
				default:
					$participation = "Unspecified";
					break;
			}
			
			$paymentClass = "";
			if(isset($_GET['show']) && ($_GET['show'] == 'unpaid' || $_GET['show'] == 'paid'))
			{
				$paymentClass = " payment";
			}
			echo "\t" . '<tr class="' . $participation . $paymentClass . '">';
		}
		echo "<td>" . $counter . "</td>";
		//echo '<td>' . makePlayerLink($player['id'], $player['name'], $player['uid'], $player['fb_user'], $player['photo']) . "</td>\n";
		echo "<td><a href=\"edit_player.php?id={$player['id']}\">{$player['name']}</a>\r\n";
//	if($mobile==0)	{	
		echo '<td><img src="../_images/sex_' . ($player['sex'] == 'f' ? 'f' : 'm') . '.png" title="' . ($player['sex'] == 'f' ? $lang['f'] : $lang['m']) . '" alt=""></td>';
		echo "<td>" . $player['club']. "</td>";
		echo "<td>" . $player['city'] . "</td>";
if(isset($_GET['show']) && ($_GET['show'] == "guests")) 
{
		echo "<td>" . $player['arrival'] . "</td>";
		echo "<td>" . $player['departure'] . "</td>";
}		
		echo '<td align="right">' . $player['phone'] . "</td>";
		//echo "<td>" . $player['email'] . "</td>";
if(!isset($_GET['show']) || $_GET['show'] != "old") 
{
			$basket = (isset($player['basket'])? $player['basket'] : "-");
			$paid_class = $player['paid'] == 1 ? 'haspaid' : 'notpaid';
			echo '<td class="participation">'
				. '<div class="participation_info" id="_participation_'.$playerId .'">' . $participation . '</div>'
				. '<div class="participation_controls" id="participation_'.$playerId .'">'
					. '<a href="#" id="yes_' . $playerId .'">Yes</a> '
					. '<a href="#" id="maybe_' . $playerId .'">Maybe</a> '
					. '<a href="#" id="no_' . $playerId .'">No</a>'
					. "</div></td>";
			$basket_list = "";
			for($i=1;$i<=$baskets;$i++)
			{
				$basket_list .= '<a href="#" id="b_' . $i . '_' . $playerId .'">' . $i . '</a> ';
			}
			//echo '<td class="basket"><div class="basket_info" id="_basket_'.$playerId .'">' . $basket . '</div><div class="basket_controls" id="basket_'.$playerId .'">' . $basket_list . "</div></td>";
	//	} //end not mobile
			if ($_SESSION['access_level'] == 1) 
			{
				
				echo '<td class="paid"><input type="checkbox" id="' . $playerId . '" value="1" title="Paid?"' . ($player['paid'] ? " checked" : "") . ' /></td>';
//					. '<a href="#" id="' . ($player['paid'] ? "y" : "n") . '_' . $player['id'] .'" title="Paid?">' 
//					. ($player['paid']?'Yes':'No') . '</a></td>';
			}
		}
		if ($_SESSION['access_level'] == 1) 
		{
			echo '<td align="center">';
			echo '<a href="edit_player.php?id=' . $player['id'] . '"><img src="../images/edit.png" alt="" /></a>';
			if(!isset($_GET['show']) || $_GET['show'] != "old") 
			{
				echo '<a href="players.php?action=delete&amp;pid=' . $player['id'] . '&amp;name=' . urlencode($player['name']) . '" onclick="return confirm(\'Are you sure?\');"><img src="../images/delete.png" width="16" height="16" alt="" /></a>';
			}
			echo "</td>\n";
		}
		echo "</tr>\n";
		$counter++;
	}
}
else
{
	echo '<tr><td colspan="11" style="padding: 10px;">Empty list</td></tr>';
}
?>
</table>
<br />
<?php
}
include("include/footer_admin.php");
?>