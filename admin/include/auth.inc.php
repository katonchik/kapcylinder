<?php
session_start();
if (!isset($_SESSION['logged_admin']))
{
	header("Location: login.php");
	exit;
}

if ($page_access_level >= $_SESSION['access_level'])
{
	$access_granted = true;
}

?>