<?php
	$page_access_level = 1;
	include("include/admin_includes.inc.php");
	
	if($access_granted)
	{
		if(isset($_POST['submit']))
		{
			if(isset($_POST['css']))
			{
				//Handle uploads
				$allowedExts = array("css");
				$extension = end(explode(".", $_FILES["file"]["name"]));
				if ($_FILES["file"]["size"] < 20000	&& in_array($extension, $allowedExts))
				{
					if ($_FILES["file"]["error"] > 0)
					{
						$errormsg = "Return Code: " . $_FILES["file"]["error"] . "<br>";
					}
					else
					{
						if($_POST['css'] == "main")
						{
//							$success = move_uploaded_file($_FILES["file"]["tmp_name"],	"../_css/" . $_FILES["file"]["name"]);
							$success = move_uploaded_file($_FILES["file"]["tmp_name"],	"../_css/general.css");
						}
						else if($_POST['css'] == "theme")
						{
//							$success = move_uploaded_file($_FILES["file"]["tmp_name"],	"../themes/$theme/" . $_FILES["file"]["name"]);
							$success = move_uploaded_file($_FILES["file"]["tmp_name"],	"../themes/$theme/default.css");
						}
						else if($_POST['css'] == "admin")
						{
//							$success = move_uploaded_file($_FILES["file"]["tmp_name"],	"_css/" . $_FILES["file"]["name"]);
							$success = move_uploaded_file($_FILES["file"]["tmp_name"],	"_css/admin.css");
						}

						if($success)
						{
							$successmsg = "Upload successful";
						}
						else
						{
							$errormsg = "Upload failed";
						}
					}
				}
				else
				{
					$errormsg="Invalid file";
				}
			}
			else if(isset($_POST['lang']))
			{
				$allowedExts = array("lang");
				$extension = end(explode(".", $_FILES["file"]["name"]));
				if ($_FILES["file"]["size"] < 20000	&& in_array($extension, $allowedExts))
				{
					if ($_FILES["file"]["error"] > 0)
					{
						$errormsg = "Return Code: " . $_FILES["file"]["error"] . "<br>";
					}
					else
					{
						$success = move_uploaded_file($_FILES["file"]["tmp_name"],	"../lang/" . $_FILES["file"]["name"]);
						if($success)
						{
							$successmsg = "Upload successful";
						}
						else
						{
							$errormsg = "Upload failed";
						}
					}
				}
				else
				{
					$errormsg="Invalid file";
				}
			}
			else if(isset($_POST['imgs']))
			{
				$allowedExts = array("jpg","png","gif");
				$filename_parts = explode(".", $_FILES["file"]["name"]);
				$base = $filename_parts[0];
				$extension = $filename_parts[1];
				$errormsg = "";

				if($_POST['imgs']=="logo" && ($base!="logo" || !in_array($extension, $allowedExts)))
				{
					$errormsg .= "Invalid filename.  Must be logo.gif or logo.jpg or logo.png. ";
				}
				elseif($_POST['imgs']=="hdrbkg" && ($base!="hdrbkg" || !in_array($extension, $allowedExts)))
				{
					$errormsg .= "Invalid filename.  Must be hdrbkg.gif or hdrbkg.jpg or hdrbkg.png. ";
				}
				elseif($_POST['imgs']=="mainbkg" && ($base!="mainbkg" || !in_array($extension, $allowedExts)))
				{
					$errormsg .= "Invalid filename.  Must be mainbkg.gif or mainbkg.jpg or mainbkg.png. ";
				}
				
				if ($_FILES["file"]["size"] > 200000)
				{
					$errormsg .= "The file size is too big (limit 200000 bytes).";
				}

				if ($_FILES["file"]["error"] > 0)
				{
					$errormsg .= "Return Code: " . $_FILES["file"]["error"] . "<br>";
				}
				
				if($errormsg == "")
				{
					$success = move_uploaded_file($_FILES["file"]["tmp_name"],	"../images/" . $_FILES["file"]["name"]);
					if($success)
					{
						$successmsg = "Upload successful";
					}
					else
					{
						$errormsg = "Upload failed";
					}
				}

			}//end of post imgs
		} //end of if submit
		//In either case browse all files to show them on the page
		if ($handle = opendir('../lang')) 
		{
			$lang_files = array();
			while (false !== ($entry = readdir($handle)))
			{
				if($entry != "." && $entry != "..")
				{
					$lang_files[] = $entry;
				}
			}
			closedir($handle);
		}
	}//end of access
	else
	{
		$errormsg = "Access denied";
	}

	include("include/header_admin.php");
	
	if($access_granted)
	{
?>
<h3>Images</h3>
<form name="upload_css" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
<label for="file">Logo:</label>
<input type="file" name="file" id="file">
<input type="hidden" name="imgs" value="logo"> (allowed filename: logo.*)<br />
<input type="submit" name="submit" value="Submit">
</form>
<br />

<form name="upload_css" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
<label for="file">Header background:</label>
<input type="file" name="file" id="file">
<input type="hidden" name="imgs" value="hdrbkg"> (allowed filename: hdrbkg.*)<br />
<input type="submit" name="submit" value="Submit">
</form>
<br />

<form name="upload_css" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
<label for="file">Main background:</label>
<input type="file" name="file" id="file">
<input type="hidden" name="imgs" value="mainbkg"> (allowed filename: mainbkg.*)<br />
<input type="submit" name="submit" value="Submit">
</form>
<br />

<h3>CSS</h3>
<a href="../_css/general.css">Main CSS</a><br />
<form name="upload_css" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
<label for="file">Upload modified CSS file:</label>
<input type="file" name="file" id="file">
<input type="hidden" name="css" value="main"><br />
<input type="submit" name="submit" value="Submit">
</form>
<br />

<a href="../themes/<?php echo $theme; ?>/default.css">Default theme css</a><br />
<form name="upload_css" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
<label for="file">Upload modified CSS file:</label>
<input type="file" name="file" id="file">
<input type="hidden" name="css" value="theme"><br />
<input type="submit" name="submit" value="Submit">
</form>
<br />

<a href="../admin/_css/admin.css">Admin css</a><br />
<form name="upload_css" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
<label for="file">Upload modified CSS file:</label>
<input type="file" name="file" id="file">
<input type="hidden" name="css" value="admin"><br />
<input type="submit" name="submit" value="Submit">
</form>
<br />

<h3>Language files</h3>
<?php
foreach($lang_files as $lang_file)
{
	echo '<a href="../lang/' . $lang_file . '">' . $lang_file . '</a><br />';
}
?>
<form name="upload_lang" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
<label for="file">Upload modified lang file:</label>
<input type="file" name="file" id="file">
<input type="hidden" name="lang" value="1"><br />
<input type="submit" name="submit" value="Submit">
</form>


<?php
}
include("include/footer_admin.php");
?>