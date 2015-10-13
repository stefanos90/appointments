<?php
session_start();
include ('../../lib/connect.php');
include ('../../lib/class.php');
//"protect" folder

if (!isset($_SESSION["userData"])) {
	header('Location: ../../index.php');
}
if (isset($_SESSION["userData"])){
	$userData=$_SESSION["userData"];
	if ($userData['role']!='doctor') {
		header('Location: ../../index.php');
	}
	else if ($userData['role']=='doctor') {
		header('Location: home.php');
	}
	else{
		header('HTTP/1.0 403 Forbidden');
		exit("<h1>Access Forbidden!</h1>");
	}
}

?> 