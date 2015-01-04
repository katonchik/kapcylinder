<?php
	session_start();
	unset($_SESSION['logged_admin']);
	unset($_SESSION['access_level']);
	header("Location: login.php");
	exit();
?>