<?php
	$page_access_level = 2;
	include("include/admin_includes.inc.php");
	include("include/header_admin.php");
	
?>
	<div id="taskManager">
		<div id="addTask">
			<input type="text" id="addText" value="sometext" />
			<input type="button" value="add" id="addButton" />
		</div>

		<div id="activeList"></div>
		<div id="completedList"></div>
	</div>

	<script src="<?php echo SITE_URL; ?>/admin/js/checklist.js"></script>
	<script>
		var myChecklist = new CheckList();
	</script>

	
	<ul>
		<li>Бланки</li>
		<li>Роздруковки регламенту</li>
		<li>Роздруковки сітки</li>
		<li>Ручки</li>
		<li>Зажим</li>
		<li>Перекидалки</li>
		<li>Ігровий диск</li>
		<li>Малярська стрічка для розмітки</li>
		<li>Медальки</li>
		<li>Аптечка</li>
		<li>Кульки для сміття</li>
		<li>Туалетний папір</li>

		<li>Вода</li>
		<li>Чайник</li>
		<li>Чайні пакетики</li>
		<li>Цукор</li>
		<li>Стаканчики</li>
		<li>Палочки-мішалочки</li>
		<li>Печеньки</li>
</ul>

<?php
include("include/footer_admin.php");
?>