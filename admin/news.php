<?php
	$page_access_level = 2;
	include("include/admin_includes.inc.php");

	//Add submitted team
	if(isset($_GET['action']) && isset($_GET['nid']))
	{
		$action = $_GET['action']; 
		$nid = $_GET['nid']; 
		if($action == 'delete')
		{
			//Delete teams from database:
			$query = "DELETE FROM news WHERE news_id=$nid";
			$result = $mysqli->query($query);
			if(!$result) 
			{
				$errormsg = "Failed to delete news";				
			}
			else
			{
				$successmsg = "News deleted";
			}
		}
	}

	if(isset($_POST['submit']))
	{
		$submit = $_POST['submit']; 
		$news_text = $mysqli->real_escape_string($_POST['news_text']);
		if(!$news_text)
		{
			$errormsg = "The field is empty";
		}
		else
		{
			$query = "INSERT INTO news (date, news_text, is_published, player_id, tournament_id) values (CURDATE(), '$news_text', 1, 0, $tournid)";
			$result = $mysqli->query($query);
			if(!$result) 
			{
				$errormsg = "Failed to add news: " . $query;
			}
			else
			{
				header("Location: news.php");
				exit();		
			}
		}
	}

	
	include("include/header_admin.php");

?>
<form method="post" name="register" action="<?php echo $_SERVER['PHP_SELF']; ?>">
News Text: <input type="text" name="news_text" style="width:600px;" />
<input type="submit" name="submit" value="Add" />
<br />
<i>For internal links, wiki format is supported: [[page|LinkText]]. E.g.: Заказываем [[lunches|обеды на турнир]]</i><br />

</form>
<br />

<p>Disabling news removes them from the cover page.  Deleting news permanently removes them from the entire system.</p>
<br />
<p>News from administrator:</p>
<?php $src="org"; ?>
<?php include("include/news_list.inc.php");?>
<br />
<p>Auto-generated news:</p>
<?php $src="auto"; ?>
<?php include("include/news_list.inc.php");?>

<br />
<?php
include("include/footer_admin.php");
?>