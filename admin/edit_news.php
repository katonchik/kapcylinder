<?php
	$page_access_level = 2;
	include("include/admin_includes.inc.php");

	if(isset($_POST['submit']))
	{
		$news_text = $mysqli->real_escape_string($_POST['news_text']);
		$nid = $_POST['nid'];
	
		$query = "UPDATE news SET news_text='$news_text' WHERE news_id=$nid";
		$result = $mysqli->query($query);
		if($result)
		{
			header("Location: news.php");
			exit();
		}
		$errormsg = "Failed to update news";
	}
	else
	{
		$nid = $_GET['nid'];
		$query = "SELECT * FROM news WHERE news_id=$nid";	
		$result = $mysqli->query($query);
		if(!$result)
		{
			die("Nothing selected from DB");
			exit();
		}
		$row = $result->fetch_assoc();
	}

include("include/header_admin.php");

?>
<form method="post">
News text: <input type="text" name="news_text" value="<?php echo htmlspecialchars($row['news_text']); ?>" style="width:600px;" /><br />
<input type="hidden" name="nid" value="<?php echo $nid; ?>" />
<input type="submit" name="submit" value="Save">
<br />
<i>For internal links, wiki format is supported: [[a|b]] is labelled "b", but links to page "a" in the front end. E.g.: Заказываем [[lunches|обеды на турнир]]</i><br />

</form>

<br />
<?php
include("include/footer_admin.php");
?>