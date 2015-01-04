<h3 class="page_title"><?php echo $lang['games']; ?></h3>

<div id="mainblock_inside">
	<?php if(isset($settings['setka']) && $settings['setka'] != "") { ?> 
	<img src="<?php echo $settings['setka']; ?>" />
	<?php } else echo $lang['schedule_coming_soon']; ?>
</div>