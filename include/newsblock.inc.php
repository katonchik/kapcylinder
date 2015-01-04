<?php
	if($src=="org")
	{
		$query = "SELECT * FROM news WHERE is_published=1 AND player_id=0 AND tournament_id = $tournid ORDER BY news_id DESC LIMIT $newsCount";		
		$css_class = "orgnewsitem";
	}
	else
	{
		$query = "SELECT * FROM news WHERE tournament_id = $tournid AND news_id in (SELECT MAX(news_id) FROM news WHERE is_published=1 AND player_id!=0 GROUP BY player_id) ORDER BY news_id DESC LIMIT $newsCount";
		$css_class = "autonewsitem";
	}
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

