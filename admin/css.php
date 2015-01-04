<?php
	$page_access_level = 1;
	include("include/admin_includes.inc.php");
	
	if(isset($_POST['submit']))
	{
		if($access_granted)
		{
			$css_content = $_POST['css_content'];
			$success = file_put_contents('../_css/content.css', $css_content);
			if(!$success)
			{
				$errormsg = "Failed to save css";
			}
			else
			{
				$successmsg = "css saved";
			}
		}
		else
		{
			$errormsg = "Access denied!";
		}
	}

	include("include/header_admin.php");

	if($access_granted)
	{
		$css_content = file_get_contents('../_css/content.css');

?>
<form method="post" name="css" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<textarea name="css_content" id="css_content"><?php echo stripslashes($css_content); ?></textarea><br />
<input type="submit" name="submit" value="Submit" />
</form>


<?php
	}
	else
	{
		$errormsg = "Access denied!";
	}
	include("include/footer_admin.php");
?>