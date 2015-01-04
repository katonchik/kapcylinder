<?php
	$where2 = ($src=="auto"?"AND player_id!=0":"AND player_id=0"); 
	//Read db, draw team table
	$query = "SELECT * FROM news WHERE tournament_id = $tournid $where2 ORDER BY news_id DESC";
	$result = $mysqli->query($query);

?>
<table class="listing">
	<tr>
		<th>Date</th>
		<th>News</th>
		<th>&nbsp;</th>
	</tr>
<?php
	if($result)
	{
		while ($row = $result->fetch_assoc())
		{
			echo "\t<tr" . ((!isset($row['is_published']) || $row['is_published']) ? ' class="active"' : ' class="inactive"') . ">";
			echo "<td>" . $row['date'] . "</td>";
			echo '<td class="news_text">' . parse_wikilinks($row['news_text']) . "</td>";
			echo '<td><a href="edit_news.php?nid=' . $row['news_id'] . '" title="Edit"><img src="../images/edit.png" alt="Edit" /></a>';
			if(isset($row['is_published']) && $row['is_published'])
			{
				echo '<a class="publish" id="y_' . $row['news_id'] . '" href="#" title="Unpublish"><img src="../images/unpublish.png" alt="Unpublish"/></a>';
			}
			else
			{
				echo '<a class="publish" id="n_' . $row['news_id'] . '" href="#" title="Publish"><img src="../images/publish.png" alt="Publish"/></a>';
			}

				
//				. '<a class="publish" id="' . ($row['publish'] ? 'y_' : 'n_') . $row['news_id'] . '" href="#" title="' . ($row['publish'] ? 'Unpublish' : 'Publish') . '"><img src="../images/' . ($row['publish'] ? 'unpublish' : 'publish') . '.png" alt="' . ($row['publish'] ? 'Unpublish' : 'Publish') . '"/></a>'
			echo '<a href="news.php?action=delete&amp;nid=' . $row['news_id'] . '" title="Delete"><img src="../images/delete.png" alt="Delete"/></a>' 
				. "</td>"; 
			echo "</tr>\n";
		}
	}
?>

</table>
