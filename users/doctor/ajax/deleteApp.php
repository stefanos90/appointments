<?php
session_start();
include ('../../../lib/connect.php');
include ('../../../lib/class.php');

//localhost/appointments/users/doctor/ajax/newApp.php?lName=Tsaklidis&fName=Stefanos&date=2015-05-05&BirthDate=2000-10-01&time=15:15

function newUSerRestricions(&$sanitize){
	//if not empty etc...
	if (empty($_GET['id']) || !isset($_GET['id']) || strlen($_GET['id'])==0 || is_null($_GET['id']) || $_GET['id']==" " || !is_numeric($_GET['id'])) {
		$sanitize="Fill all the inputs";
		return false;
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
		if (isset($_GET['id']) && newUSerRestricions($sanitize)){ //check for valid input, not empty etc
			//sinitizing user input
			//$conn is passed for mysqli real escape
			$id = sanitize::numbers($_GET['id'],$conn); 	
				

			if (!$sanitize) { //if any errors, sanitize has strings, so becomes true.
				//check if appointment exists
				if ($user->checkAppExist($id)) {
					//app exists, delete it 
					if ($user->deleteApp($id)) {
						echo '<div class="alert alert-success fade in">Appointment deleted</div>';
					}
					else{
						echo '<div class="alert alert-success fade in">Error deleting appointment</div>';
					}
				}
				else{
					echo '<div class="alert alert-danger fade in">Appointment doesn\'t exist, refresh your page"</div>';
				}
			}
			else{
				exit($sanitize);
			}
		}
		else{
			exit('Please fill correct the all the inputs');
		}
	}
}
else{
	header('HTTP/1.0 403 Forbidden');
	exit("<h1>Access Forbidden!</h1>");
}

?> 