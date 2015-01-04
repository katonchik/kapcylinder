<?php
	//session_start();
	unset($_SESSION['logged_user']);
	unset($_SESSION['uid']);
	unset($_SESSION['fb_user']);
	unset($_SESSION['photo']);
	unset($_SESSION['photo_rec']);
	unset($_SESSION['name']);
	header("Location: " . SITE_URL);
	exit();
?>
