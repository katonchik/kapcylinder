<?php
	if($filter == "auto")
	{
		$where = "WHERE player_id!=0";
		$css_class = "autonewsitem";
	}
	else
	{
		$where = "WHERE player_id=0";
		$css_class = "orgnewsitem";
	}
	$query = "SELECT * FROM news $where ORDER BY news_id DESC";// LIMIT $news_on_page";
	$result = $mysqli->query($query) or die("error");

	$news_arr = array();
	if($result)
	{
		while ($row = $result->fetch_assoc()) 
		{
?>
		<div class="<?php echo $css_class; ?>">
			<div class="newsdate"><?php echo $row['date']; ?></div>
			<div class="newstext"><?php echo parse_wikilinks($row['news_text']); ?></div>
		</div>
<?php 
		}
	}
	else
	{
		echo "No news yet!";
	}
?>
