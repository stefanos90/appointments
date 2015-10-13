<?php
session_start();
include ('../../../lib/connect.php');
include ('../../../lib/class.php');


if (!isset($_SESSION["userData"])) {
	header('Location: ../index.php');
}
if (isset($_SESSION["userData"])){
	if (isset($_GET['logOut']) && is_numeric($_GET['logOut']) && $_GET['logOut']==1) {
		session_destroy();
		header('Location: ../index.php');
	}
}
else{
	header('HTTP/1.0 403 Forbidden');
	exit("<h1>Access Forbidden!</h1>");
}

?> 