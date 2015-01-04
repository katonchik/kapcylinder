<?php
	$page_access_level = 2;
	include("include/admin_includes.inc.php");

	include("include/header_admin.php");

?>
	<div id="calendar" class="calendar"></div>
	<script src="<?php echo SITE_URL; ?>/admin/locale/uk-ua.js"></script>
	<script src="<?php echo SITE_URL; ?>/admin/js/calendar.js"></script>
	<script>
		var myCalendar = new Calendar(document.getElementById("calendar"), 'uk-ua');
	</script>

<?php
	include("include/footer_admin.php");
?>