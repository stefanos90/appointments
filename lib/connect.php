<?php
date_default_timezone_set('Europe/Athens'); 
$DBServer = 'localhost'; 
$DBUser   = 'AppManage';
$DBPass   = 'qwerty';
$DBName   = 'AppManage';  
$conn = new mysqli($DBServer, $DBUser, $DBPass, $DBName);
$conn->set_charset("utf8");
if ($conn->connect_error) trigger_error('Database connection failed: '. $conn->connect_error, E_USER_ERROR);
?>