<!DOCTYPE html>
<html lang="ru">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $lang['admin_backend'] . " - " . $settings['tournament_name']; ?></title>
<?php
	$scriptName = basename($_SERVER['SCRIPT_NAME']);
	echo '<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
			<script type="text/javascript" src="../js/jquery.hoverIntent.minified.js"></script>
			<script type="text/javascript" src="../js/pics.js"></script>
			
			<link href="../include/pictures.css" rel="stylesheet" type="text/css" />
	';

	switch($scriptName)
	{
		case "pages.php":
			echo '
			<meta name="viewport" content="width=device-width" />
			<script src="tree/dist/jstree.min.js"></script>
			<link rel="stylesheet" href="' . SITE_URL . '/admin/tree/dist/themes/default/style.min.css" />
			<style>
				#container { min-width:320px; margin:0px auto 0 auto; background:white; border-radius:0px; padding:0px; overflow:hidden; }
				#tree { float:left; min-width:319px; border-right:1px solid silver; overflow:auto; padding:0px 0; }
				#data { margin-left:320px; }
				#data textarea { margin:0; height:100%; background:white; display:block; }
				#data, #code { font: normal normal normal 12px/18px Consolas, monospace !important; }
				#data .content { padding: 0px 50px; }
				#data .content textarea { border: 1px solid lightgray; line-height: auto; padding: 5px;}
				#data .content checkbox { border: 1px solid lightgray; width: 17px;}
			</style>

			<!-- TinyMCE -->
				<script type="text/javascript" src="js/tiny_mce/tiny_mce.js"></script>
				<script type="text/javascript">
					tinyMCE.init({
						mode : "textareas",
						theme : "advanced",
						plugins : "preview",
						//theme_advanced_buttons1: "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontsizeselect,|,sub,sup,|,bullist,numlist,|,outdent,indent",
						//theme_advanced_buttons2: "cut,copy,paste,pastetext,|,undo,redo,|,link,unlink,image,code,emotions,|,removeformat,visualaid,|,insertdate,inserttime,preview,fullscreen",
						//theme_advanced_buttons3: "",
						// Example content CSS (should be your site CSS)
						content_css : "' . SITE_URL . '/_css/general.css, ' . SITE_URL . '/themes/default/default.css",
					});

					function ajaxLoad() {
						var ed = tinyMCE.get("pagecontent");

						// Do you ajax call here, window.setTimeout fakes ajax call
						ed.setProgressState(1); // Show progress
						window.setTimeout(function() {
							ed.setProgressState(0); // Hide progress
							ed.setContent("HTML content that got passed from server.");
						}, 3000);
					}

					function ajaxSave() {
						var ed = tinyMCE.get("pagecontent");

						// Do you ajax call here, window.setTimeout fakes ajax call
						ed.setProgressState(1); // Show progress
						window.setTimeout(function() {
							ed.setProgressState(0); // Hide progress
							alert(ed.getContent());
						}, 3000);
					}
				</script>
				<!-- /TinyMCE -->
			';
			break;
		case "news.php":
        	include("include/header_jquery_news.inc.php");
        	break;
		case "players.php":
			include("include/header_jquery_paid.inc.php");
			include("include/header_jquery_admin.inc.php");
			include("include/header_jquery_basket.inc.php");
			break;
		case "accommodation.php":
			echo '
				<script type="text/javascript" src="js/jquery.jeditable.mini.js"></script>
				<script type="text/javascript" src="accommodation.js"></script>
			';
			include("include/header_jquery_accommodation.inc.php");
			break;
		case "sorting.php": //you sure we need to call an older version of jquery? Maybe switch on the new one up above?
			echo '
				<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
				<script type="text/javascript" src="js/jquery.json-2.2.min.js"></script>
				<script type="text/javascript" src="sorting.js"></script>
			';
			break;
		case "edit_player.php":
			echo '
				<link rel="stylesheet" href="_css/avatar.css" type="text/css" />
				<script src="js/cropbox.js"></script>
			';
			break;
		case 'milestones.php':
			echo '
				<link rel="stylesheet" href="../jqwidgets/styles/jqx.base.css" type="text/css" />
				<script type="text/javascript" src="../jqwidgets/jqxcore.js"></script>
				<script type="text/javascript" src="../jqwidgets/jqxslider.js"></script>
				<script type="text/javascript" src="../jqwidgets/jqxbuttons.js"></script>
				<script type="text/javascript" src="../jqwidgets/jqxscrollbar.js"></script>
				<script type="text/javascript" src="../jqwidgets/jqxpanel.js"></script>
				<script type="text/javascript" src="js/slider.js"></script>
			';
			break;
		case 'baskets.php':
			echo '
				<link type="text/css" rel="stylesheet" href="_css/baskets.css" />
				<script type="text/javascript" src="js/baskets.js"></script>
			';
			break;
		case 'calendar.php':
			echo '
				<link type="text/css" rel="stylesheet" href="_css/calendar.css" />
			';
			break;
		case 'checklist.php':
			echo '
				<link type="text/css" rel="stylesheet" href="_css/checklist.css" />
			';
			break;
	}


?>

<?php if(isset($xtras)) echo $xtras; // see an example in sorting.php ?>
<link href="_css/admin.css" rel="stylesheet" type="text/css" />
<style media="handheld">
.handheld{
	font-size: largest;
}
</style>
</head>
<body>
<div id="wrapper">
	<header>
		<nav>
			<a href="../" target="_blank">На морду</a>  |
			<a href="index.php"><?php echo $lang['admin_players'];?></a> |
		<?php if ($_SESSION['access_level'] == 1) { ?>
			<a href="log.php"><?php echo $lang['admin_log'];?></a> |
			<a href="baskets.php"><?php echo $lang['baskets'];?></a> |
			<?php if(isset($settings['enable_likes']) || $settings['enable_dislikes']) { ?>
				<a href="likes.php"><?php echo $lang['admin_likes'];?></a> |
			<?php } ?>
			<?php if(isset($settings['enable_autolotting']) && $settings['enable_autolotting']) { ?>
				<a href="lotting.php"><?php echo $lang['admin_autolotting'];?></a> |
			<?php } else { ?>
				<a href="sorting.php"><?php echo $lang['admin_draw'];?></a> |
			<?php } ?>
		<?php } ?>
			<a href="teams.php"><?php echo $lang['admin_teams'];?></a> |
			<a href="news.php"><?php echo $lang['admin_news'];?></a> |
			<a href="massmail.php"><?php echo $lang['admin_massmail'];?></a> |
			<a href="milestones.php"><?php echo $lang['admin_milestones'];?></a> |
			<a href="checklist.php">Checklist</a> |
		<?php if(isset($settings['offer_accommodation']) && $settings['offer_accommodation']) { ?>
			<a href="accommodation.php"><?php echo $lang['admin_accommodation'];?></a> |
		<?php } ?>
		<?php if(isset($settings['offer_lunches']) && $settings['offer_lunches']) { ?>
			<a href="lunches.php"><?php echo $lang['lunches'];?></a> |
		<?php } ?>
		<?php if ($_SESSION['access_level'] == 1) { ?>
			<!--<a href="files.php"><?php echo $lang['admin_files'];?></a> |-->
			<a href="pages.php"><?php echo $lang['admin_pages'];?></a> |
			<a href="settings.php"><?php echo $lang['admin_settings'];?></a> |
			<a href="admins.php"><?php echo $lang['admin_admins'];?></a> |
		<?php } ?>
		<!--	&nbsp; &nbsp; 
			<a href="../" target="_blank">На морду</a>
		-->
			&nbsp; &nbsp; 
			<a href="logout.php"><?php echo $lang['admin_logout'];?></a>
			
			<br />

		</nav>
	</header>
	<main>
		<div id="pageTitle" data-page="admin" />

		<div class="playerSearch">
			<input type="text" 	id="searchName" class="playerSearch__name" />
			<input type="image" id="searchBttn" class="playerSearch__bttn" src="../images/search.png" alt="Search player" />
		</div>

<?php
	if(isset($errormsg))
	{
		echo '<div id="errorMsg">' . $errormsg . '</div>';
	}
	if(isset($successmsg))
	{
		echo '<div id="successMsg">' . $successmsg . '</div>';
	}
?>