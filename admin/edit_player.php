<?php
	$page_access_level = 2;
	include("include/admin_includes.inc.php");

	if(isset($_POST['submit'])) 
	{
		$name = $mysqli->real_escape_string($_POST['name']);
		$club = $mysqli->real_escape_string($_POST['club']);
		$city = $mysqli->real_escape_string($_POST['city']);
		if($settings['format_phones'])
		{
			$phone = formatPhone($_POST['phone']);
		}
		else
		{
			$phone = formatPhoneBy($_POST['phone']);
		}
		$photo 			= $_POST['photo'];
		$sex 			= $_POST['sex'];
		$player_id 		= $_POST['id'];
		$email_news 	= (isset($_POST['email_news']) ? 1 : 0);
		$blacklisted 	= (isset($_POST['blacklisted']) ? 1 : 0);
		$uid 			= ($_POST['uid']?$_POST['uid']:'NULL');
		$fb_user 		= ($_POST['fb_user']?$_POST['fb_user']:'NULL');
	
		$query = "UPDATE players SET name='$name', club='$club', uid=$uid, fb_user=$fb_user, city='$city', sex='$sex', phone='$phone', photo='$photo', email_news=$email_news, blacklisted=$blacklisted WHERE id=$player_id";
		$result = $mysqli->query($query);
		if($result)
		{
			$log_text = $_SESSION['logged_admin'] . " updated profile for $name ";
			WriteLog($mysqli, CONTACTS, $log_text, $player_id);
			$successmsg = $log_text;
		}
		else
		{
			$errormsg = "Failed to update player $query<br />" . $mysqli->error;
		}
	}
	else
	{
		$player_id = $_GET['id'];
	}
	$query = "SELECT * FROM players WHERE id=$player_id";	
	$result = $mysqli->query($query);
	if(!$result)
	{
		die("Nothing selected from DB");
		exit();
	}
	$row = $result->fetch_assoc();
	$name = $row['name'];
	$phone = $row['phone'];
	$sex = $row['sex'];
	$email_news = $row['email_news'];
	$blacklisted = $row['blacklisted'];
	$club = $row['club'];
	$photo = $row['photo'];
	$photo_cropped = $row['photo_cropped'];
	$city = $row['city'];
	$uid = $row['uid'];	
	$fb_user = $row['fb_user'];	
	

include("include/header_admin.php");

?>
<?php if(isset($photo)) { ?>





<div class="cropper" data-playerId="<?php echo $player_id; ?>" data-lgImgSrc="<?php echo $photo; ?>">
<!--<img src="<?php echo $row['photo']; ?>" />-->
	<div class="imageBox">
		<div class="thumbBox"></div>
		<div class="spinner" style="display: none">Loading...</div>
	</div>
	<div class="cropper__actions">
		<input type="file" id="file" style="float:left; width: 250px">
		<input type="button" id="btnCrop" value="Crop" style="float: right">
		<input type="button" id="btnZoomIn" value="  +  " style="float: right">
		<input type="button" id="btnZoomOut" value="  -  " style="float: right">
	</div>
	<div class="cropper__thumbContainer">
<?php 
if(isset($photo_cropped)) 
{
	echo "<img src=\"{$photo_cropped}\" id=\"croppedImg\" />\r\n";
} 
?>
	</div>
	<div class="cropper__thumbControls">
		<div id="saveResultMsg"></div>
		<input type="button" id="btnSaveCropped" value="Save" style="visibility:hidden;" />
	</div>
</div>


<script type="text/javascript"  src="<?php echo SITE_URL; ?>/admin/js/avatar.js"></script>

<?php } //end if is set row photo?>

	<form method="post" name="register">
		Player name: <input type="text" name="name" value="<?php echo $name; ?>" /><br />
		Gender: <input type="radio" name="sex" value="m"<?php echo ($sex == 'm' ? ' checked="checked"' : '')?>> Male <input type="radio" name="sex" value="f"<?php echo ($sex == 'f' ? ' checked' : '')?>> Female<br />
		Club: <input type="text" name="club" value="<?php echo $club; ?>" /><br />
		City: <input type="text" name="city" value="<?php echo $city; ?>" /><br />
		Phone number: <input type="text" name="phone" value="<?php echo $phone; ?>" /><br />
		Photo link: <input type="text" name="photo" value="<?php echo $photo; ?>" /><br />
		VK uid: <input type="text" name="uid" value="<?php echo $uid; ?>" /><br />
		FB uid: <input type="text" name="fb_user" value="<?php echo $fb_user; ?>" /><br />
		<?php echo $lang['email_news']; ?>: <input type="checkbox" id="email_news" name="email_news" value="1" <?php echo ($email_news ? 'checked="checked" ' : ''); ?>/><br />
		Blacklisted? <input type="checkbox" id="blacklisted" name="blacklisted" value="1" <?php echo ($blacklisted ? 'checked="checked" ' : ''); ?>/><br />
		<input type="hidden" name="id" value="<?php echo $player_id; ?>" /><br />
		<input type="submit" name="submit" value="Save" />
	</form>

<br />
<?php
include("include/footer_admin.php");
?>