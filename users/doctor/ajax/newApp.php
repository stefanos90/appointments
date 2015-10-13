<?php
session_start();
include ('../../../lib/connect.php');
include ('../../../lib/class.php');

//localhost/appointments/users/doctor/ajax/newApp.php?lName=Tsaklidis&fName=Stefanos&date=2015-05-05&BirthDate=2000-10-01&time=15:15

function newUSerRestricions(&$sanitize){
	//if not empty etc...
	foreach ($_GET as $key => $value) {
		if (empty($_GET[$key]) || !isset($_GET[$key]) || strlen($_GET[$key])<4 || is_null($_GET[$key]) || $_GET[$key]==" ") {
			$sanitize="Fill all the inputs";
			return false;
		}
	}
	return true;
}
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
		$todayApps=$user->getMyApps(); 
		$msg=$sanitize=false; 
		/* create new appointment from doctor */
		if (isset($_GET['lName']) && 
			isset($_GET['fName']) &&
			isset($_GET['date']) && 
			isset($_GET['BirthDate']) && 
			isset($_GET['time']) && newUSerRestricions($sanitize)){ //check for valid input, not empty etc
			//sinitizing user input
			//$conn is passed for mysqli real escape
			$lName 	= sanitize::text($_GET['lName'],$conn); 	
			$fName 	= sanitize::text($_GET['fName'],$conn); 

			$lName!=$_GET['lName'] ? $sanitize="Wrong first name format" : $sanitize;
			$fName!=$_GET['fName'] ? $sanitize="Wrong last name format" : $sanitize;
			// sanitize dDate && tText returns false if the format is wrong
			// for debuging set debug=true
			!sanitize::dDate($_GET['date'],$conn) ? $sanitize="Date must be YYYY-MM-DD" : $date = $_GET['date']; 
			!sanitize::dDate($_GET['BirthDate'],$conn) ? $sanitize="Date must be YYYY-MM-DD" : $bDate = $_GET['BirthDate']; 
			!sanitize::tTime($_GET['time'],$conn) ? $sanitize="Time must be 24h (15:30)" : $time = $_GET['time']; 

			//check for past date
			
			if (isset($date)) {
				$today=date('Y-m-d');
				$date<$today ? $sanitize = "You can't create appointment for past date" : $sanitize;
			}
				

			if (!$sanitize) { //if any errors, sanitize has strings, so becomes true.
				$appMsg=$user->newApp($bDate,$date,$time,$lName,$fName);
				//NewApp returns false on success
				if ($appMsg==1) {
					echo '<div class="alert alert-success fade in">Appointment successfully saved</div>';
				}
				else if ($appMsg==2) {
					echo '<div class="alert alert-danger fade in">Date is reserved</div>';;
				}
				else if ($appMsg==3) {
					echo '<div class="alert alert-warning fade in">User has already an appointment on that day</div>';
				}
				else if ($appMsg==4){
					echo '<div class="alert alert-warning fade in">User is not registerd</div>';
				}
				else{
					echo '<div class="alert alert-danger fade in">Appointment creation failed |'.$appMsg.'|</div>';
				}
			}
			else{
				echo '<div class="alert alert-danger fade in">'.$sanitize.'</div>';
			}
		}
		else{
			exit('<div class="alert alert-warning fade in">Please fill correct the all the inputs<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></div>');
		}
	}
}
else{
	header('HTTP/1.0 403 Forbidden');
	exit("<h1>Access Forbidden!</h1>");
}

?> 