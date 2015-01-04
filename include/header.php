<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title><?php echo $settings['tournament_name']; ?></title>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL; ?>/css/bootstrap.min.css" media="screen">
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL; ?>/_css/general.css" />
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL; ?>/themes/<?php echo $settings['theme'] ? $settings['theme'] : 'default'; ?>/default.css" />
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL; ?>/_css/wide.css" media="(min-width: 1600px)" />
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL; ?>/_css/normal.css" media="(max-width: 1600px) and (min-width: 1100px)" />
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL; ?>/_css/narrow.css" media="(max-width: 1100px)" />
<link rel="stylesheet" type="text/css" href="<?php echo SITE_URL; ?>/include/pictures.css" />
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL; ?>/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL; ?>/validation.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL; ?>/js/jquery.hoverIntent.minified.js"></script>
<script type="text/javascript" src="<?php echo SITE_URL; ?>/js/pics.js"></script>

<?php
switch($page)
{
	case "home":
		echo '<!--[if lt IE 7]> <link rel="stylesheet" type="text/css" media="all" href="css/ie6.css?v=1" /> <![endif]-->';

		echo '<link rel="stylesheet" href="' . SITE_URL . '/jqwidgets/styles/jqx.base.css" type="text/css"/>';
		echo '<script type="text/javascript" src="' . SITE_URL . '/jqwidgets/jqxcore.js"></script>';
		echo '<script type="text/javascript" src="' . SITE_URL . '/jqwidgets/jqxbuttons.js"></script>';
		echo '<script type="text/javascript" src="' . SITE_URL . '/jqwidgets/jqxscrollbar.js"></script>';
		echo '<script type="text/javascript" src="' . SITE_URL . '/jqwidgets/jqxlistbox.js"></script>';
		echo '<script type="text/javascript" src="' . SITE_URL . '/jqwidgets/jqxcombobox.js"></script>';

		echo '<script type="text/javascript" src="' . SITE_URL . '/js/home.js"></script>';
		echo '<script type="text/javascript" src="' . SITE_URL . '/js/popup_form.js"></script>';
		echo '<script type="text/javascript" src="' . SITE_URL . '/js/popup_submit.js"></script>';
		echo '<link href="' . SITE_URL . '/css/editpopup.css" rel="stylesheet" type="text/css" />';
		break;
	case "login":
		echo '<!-- Put this script tag to the <head> of your page -->' . "\n";
		echo '<script type="text/javascript" src="http://userapi.com/js/api/openapi.js?45"></script>' . "\n";
		echo '<script type="text/javascript">' . "\n";
		echo 'VK.init({apiId: ' . $app_id . '})' . "\n";
		echo '</script>' . "\n";
		break;
	}
?>

<!--[if !IE 7]>
	<style type="text/css">
		#wrapper {display:table;height:100%}
	</style>
<![endif]-->


</head>


<body>
<div id="wrapper">
<header>
	<?php if($settings['milestone'] > 0 && $settings['milestone'] < 4) { ?>
		<div id="profile_menu">
		<?php if(isLoggedIn() === false) { ?>
			<?php echo '<b>' . internaLink("login", $lang['login']) . '</b>'; ?>
		<?php } elseif(isLoggedIn()) { ?>
			<?php echo internaLink("home", $_SESSION['logged_user']); ?> |
			<?php echo internaLink("logout", $lang['log_out']); ?>
		<?php } else { //only vk?>
			<?php echo internaLink("logout", $lang['log_out']); ?>
		<?php } ?>
		</div><!--end profile_menu-->
	<?php } ?>



	<nav>
		<?php
			if(!isset($hide_menu))
			{
				$pages_level1 = $fs->get_children(1);
				$pagename = "";
				foreach($pages_level1 as $menupage)
				{
					if($menupage['is_active'])
					{
						if($pagename)
						{
							echo " | ";
						}

						$pagename = $menupage['nm'];
						$pageurl = $menupage['page_url'];
						if($settings['milestone'] == 4 && $menupage['page_url'] == "announcement")
						{
							if($pages_level1[1]['page_url'] == 'results')
							{
								$pagename = $pages_level1[1]['nm']; //dirty hack
							}
						}
						echo makeMenuItem($pageurl, $pagename);
					}
				}



			} //end if(!$hide_menu)
		?>

	</nav><!--end menu-->
	</header>


	<main>
        <div id="content">

			<div id="fb-root"></div>

            <div class="column" id="left">
				<p>&nbsp;</p>
<?php if($page == "login") { 
		echo '<script type="text/javascript" src="' . SITE_URL . '/js/facebook.js"></script>';
} ?>

				
<?php if($page == "login") { ?>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  //js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=<?php echo $fb_app_id; ?>";
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=<?php echo $fb_app_id; ?>&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<?php } ?>
				</div>

			<?php if($page_data['layout'] == 2) { ?>

            <aside>
				<div id="newsblock" class="infobox">
					<?php
						$newsCount=$settings['news_on_page'];
						$src="org";
						include("include/newsblock.inc.php");
					?>
					<div id="orgnews_link"><?php echo internaLink("allnews/org", $lang['allnews']); ?></div>
					<hr />
					<?php
						$newsCount=$settings['autonews_on_page'];
						$src="auto";
						include("include/newsblock.inc.php");
					?>
					<div id="autonews_link"><?php echo internaLink("allnews/auto", $lang['allnews']); ?></div>
				</div>
			</aside>
			<?php } ?>

            <div class="column" id="center">

			<div id="mainblock" class="infobox">

