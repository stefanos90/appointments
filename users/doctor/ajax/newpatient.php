<?php
session_start();
include ('../../../lib/connect.php');
include ('../../../lib/class.php');


if (!isset($_SESSION["userData"])) {
	header('Location: ../../../index.php');
}
if (isset($_SESSION["userData"])){
	$userData=$_SESSION["userData"];
	if ($userData['role']!='doctor') {
		header('Location: ../../../index.php');
	}
	else if ($userData['role']=='doctor') {
		$user = new doctor($userData['username'],$conn); //all data is set for the current user doctor
		$msg=$sanitize=false; 
		/* create new appointment from doctor */
		if (isset($_GET['lName']) &&
			isset($_GET['fName']) &&
			isset($_GET['newUsername']) &&
			isset($_GET['password']) &&
			isset($_GET['email']) &&
			isset($_GET['bDate']) &&
			isset($_GET['phone']) &&
			isset($_GET['city']) &&
			isset($_GET['address']) ){
			//sinitizing user input	
			$newUsername= sanitize::username($_GET['newUsername'],$conn); 
			if ($newUsername!=$_GET['newUsername']) {
				$sanitize="Bad username";
				echo '<div class="alert alert-warning fade in">Bad Username</div>';
			}
			$lName= sanitize::text($_GET['lName'],$conn); 
			if ($lName!=$_GET['lName']) {
				$sanitize="Bad Last name";
				echo '<div class="alert alert-warning fade in">Bad Last name</div>';
			}
			$fName= sanitize::text($_GET['fName'],$conn); 
			if ($fName!=$_GET['fName']) {
				$sanitize="Bad First name";
				echo '<div class="alert alert-warning fade in">Bad First name</div>';
			}
			if (!sanitize::email($_GET['email'])) {
				$sanitize="Bad email";
				echo '<div class="alert alert-warning fade in">Not valid email</div>';
			}
			else{
				$email=$_GET['email'];
			}
			if (!sanitize::dDate($_GET['bDate'],$conn)) {
				$sanitize="Bad birth date";
				echo '<div class="alert alert-warning fade in">Invalid date, format must be YYY-MM-DD</div>';
			}
			elseif ($_GET['bDate']>date('Y-m-d')) {
				$sanitize="unborn";
				echo '<div class="alert alert-danger fade in">You are unborn</div>';
			}
			else{
				$bDate=$_GET['bDate'];
			}
			$address= sanitize::address($_GET['address'],$conn); 
			if ($address!=$_GET['address']) {
				$sanitize="Bad address";
				echo '<div class="alert alert-warning fade in">Bad street address</div>';
			}
			$city= sanitize::address($_GET['city'],$conn); 
			if ($city!=$_GET['city']) {
				$sanitize="Bad city";
				echo '<div class="alert alert-warning fade in">You entered an invalid city</div>';
			}
			if (!is_numeric($_GET['phone']) || strlen($_GET['phone'])<10 || strlen($_GET['phone'])>14) {
				$sanitize="Bad number";
				echo '<div class="alert alert-warning fade in">Bad phone number</div>';
			}
			else{
				$phone=$_GET['phone']; 
			}
			if (!$sanitize) { //if any errors, var sanitize has strings, so becomes true. (alternative => if sanitize==false )
				//register new patient
				$newPatient= new Register($conn);
				if ($newPatient->unique($newUsername,$email)) {
					//username and email are unique, register the user.
					$password = hash('sha256', $_GET["password"]);
					$regStatus = $newPatient->register($newUsername,$user->getusername(),$lName,$fName,$email,$bDate,$address,$city,$phone,$password);
					if ($regStatus==true) {
						echo '<div class="alert alert-success fade in">Successful registration</div>';
					}
					else{
						echo '<div class="alert alert-danger fade in">Oops! somthing is wrong '.$regStatus.'</div>';
					}
				}
				else{
					$msg="Username or email already in use";
					echo '<div class="alert alert-danger fade in">Username or email already in use</div>';
				}
			}
			else{
				//echo '<div class="alert alert-danger fade in">Try again</div>';
			}
		}
		else{
			echo '<div class="alert alert-danger fade in">No data entered </div>';
		}
		
	}
}

function newUSerRestricions(&$sanitize){
	//if not empty etc...
	if (!is_numeric($_GET['phone']) || strlen($_GET['phone'])<10 || strlen($_GET['phone'])>14) {
		$sanitize="Fill all the inputs";
		return false;
	}
	foreach ($_GET as $key => $value) {
		if (empty($_GET[$key]) || strlen($_GET[$key])<4 || is_null($_GET[$key]) || $_GET[$key]==" ") {
			$sanitize="Fill the ".$_GET[$key];
			return false;
		}
	}

	return true;
}

?> 
