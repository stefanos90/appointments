<?php
session_start();
include ('../../lib/connect.php');
include ('../../lib/class.php');

if (!isset($_SESSION["userData"])) {
	header('Location: ../../index.php');
}
if (isset($_SESSION["userData"])){
	$userData=$_SESSION["userData"];
	if ($userData['role']!='doctor') {
		header('Location: ../../index.php');
	}
	else if ($userData['role']=='doctor') {
		$user = new doctor($userData['username'],$conn); //all data is set for the current user doctor
		$monthApps=$user->getMyApps(); 
		$counter=0;
		foreach ($monthApps as $key => $value) {
			$good[$counter]['id']=$counter+1;
			$good[$counter]['title']=$monthApps[$counter]['fName']." ".$monthApps[$counter]['lName'];
			$good[$counter]['start']=$monthApps[$counter]['date']."T".$monthApps[$counter]['time'];
			$good[$counter]['end']=$monthApps[$counter]['date']."T".$monthApps[$counter]['time'];
			$counter++;
		}
		//var_dump($good);
		$json_a=json_encode($good);
		
	}
}
else{
	header('HTTP/1.0 403 Forbidden');
	exit("<h1>Access Forbidden!</h1>");
}



?> 


<!DOCTYPE html>
<html>
<head>
	<title>Doctor - Appointments</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
	<script src="../../js/jquery.min.js"></script>
	<script src='../fullcalendar/lib/moment.min.js'></script>
	<script src="../../bootstrap/js/bootstrap.min.js"></script>

	<link rel='stylesheet' href='../fullcalendar/fullcalendar.css' />
	<script src='../fullcalendar/fullcalendar.js'></script>
	<script src='../fullcalendar/custom.js'></script>

	<link rel="stylesheet" href="css/doctor.css">
	<script type="text/javascript">var eventsDone = <?php echo $json_a;?></script>
	<script src='js/custom.js'></script>

</head>

<body >
<nav class="navbar navbar-inverse">
	<div class="container-fluid" >
		<div class="navbar-header">
		  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
		    <span class="icon-bar"></span>
		    <span class="icon-bar"></span>
		    <span class="icon-bar"></span>
		  </button>
		  <a class="navbar-brand" href="#">OnLine Doctor</a>
		</div>
		<div class="collapse navbar-collapse" id="myNavbar">
		  <ul class="nav navbar-nav">
		    <li class="active"><a href="home.php">Home</a></li>
				<li class="dropdown">
				  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Patients<span class="caret"></span></a>
				  <ul class="dropdown-menu">
				    <li><a href="#" data-toggle="modal" data-target="#newPatient"><span class="glyphicon glyphicon-plus"></span> Register</a></li>
		       		<li><a href="search.php"><span class="glyphicon glyphicon-search"></span> Search</a></li>
				    <li role="separator" class="divider"></li>
				    <li><a href="search.php"><span class="glyphicon glyphicon-envelope"></span> New Message</a></li>
				  </ul>
				</li>
				<li class="dropdown">
				  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Appointments<span class="caret"></span></a>
				  <ul class="dropdown-menu">
				    <li><a href="#" data-toggle="modal" data-target="#newApp"><span class="glyphicon glyphicon-plus"></span> Create New</a></li>
				    <li role="separator" class="divider"></li>
				    <li><a href="search.php"><span class="glyphicon glyphicon-eur"></span> View History</a></li>
				  </ul>
				</li>
				<li><a href="search.php">Requests</a></li>
				<li><a href="search.php">Contacts</a></li>
		  </ul>
		  <ul class="nav navbar-nav navbar-right">
		    <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo $user->getFullName();?> <span class="caret"></span></a>
		      <ul class="dropdown-menu">
		      	<li><a href="#"><span class="glyphicon glyphicon-list-alt"></span> Notifications</a></li>
		      	<li role="separator" class="divider"></li>
		      	<li><a href="#"><span class="glyphicon glyphicon-user"></span> Profile</a></li>
		      	<li><a href="#"><span class="glyphicon glyphicon-wrench"></span> Settings</a></li>
		        <li role="separator" class="divider"></li>
		        <li><a href="#"><span class="glyphicon glyphicon-off"></span> Log out</a></li>

		      </ul>
		    </li>
		</div>
	</div>
</nav>

	<div class="container-fluid" >
		<div class="row">
			<div class="col-sm-12 col-md-12">
				<ul class="breadcrumb home" >
			        <li class="active"><a href="home.php">Home</a></li>
			        <li>Dates</li>
			    </ul>
			    <div id='calendar' ></div>
		    </div>
		 
		    <div class="col-sm-6 col-md-6" style="background-color: rgb(242, 242, 242);">
				<div id="newApp" class="modal fade" role="dialog">
				  <div class="modal-dialog">
				    <!-- Modal content-->
				    <div class="modal-content">
				      <div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
				        <h4 class="modal-title">Create New Appointment</h4>
				      </div>
				      <div class="modal-body">
						<form role="form" class='formDefault' id="newPatForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
							<input class='form-control' pattern=".{4,}[a-zA-Zα-ωΑ-Ωάέήίόύώ]*" title="Only characters allowed and length > 4" required type="text" id="lName" placeholder="Last Name" value=""><br>
							<input class='form-control' pattern=".{4,}[a-zA-Zα-ωΑ-Ωάέήίόύώ]*" title="Only characters allowed and length > 4" required type="text" id="fName" placeholder="First Name" value=""><br>
							<div class="form-group has-feedback" style='margin: 0px;'>
								<input class='form-control' pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" title="Date must be YYYY-MM-DD" required type="date" id="BirthDate" placeholder="Patient Birth Date" value=""><br>
								<i class="form-control-feedback glyphicon glyphicon-user" style='color: rgb(194, 0, 0);'></i>
							</div>
							
							<div class="form-group has-feedback" style='margin: 0px;'>
								<input class='form-control' pattern="(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))" title="Date must be YYYY-MM-DD" required type="date" id="date" placeholder="Appointment Date" value=""><br>
								<i class="form-control-feedback glyphicon glyphicon-calendar"></i>
							</div>
							<select class='form-control' id='time' >
							 	<option value="Time" disabled selected="selected">Time</option> 
								<option value="10:00">10:00</option>
								<option value="10:30">10:30</option>
								<option value="11:00">11:00</option>
								<option value="11:30">11:30</option>
								<option value="12:00">12:00</option>
								<option value="12:30">12:30</option>
								<option value="13:00">13:00</option>
								<option value="13:30">13:30</option>
								<option value="14:00">14:00</option>
								<option value="14:30">14:30</option>
								<option value="15:00">15:00</option>
							</select> <br>
						    <div id='newAppFeed'></div>
						</form>
				      </div>
				      <div class="modal-footer">
				      	<form>	
				      		<input class="btn btn-success odom-submit" type="submit" name="submit" value="Save">
				      		<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
				      	</form>
				      </div>			      
				    </div>
				  </div>
				</div>

				<div id="newPatient" class="modal fade" role="dialog">
				  <div class="modal-dialog">
				    <!-- Modal content-->
				    <div class="modal-content">
				      <div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal">&times;</button>
				        <h4 class="modal-title ">Register New Patient</h4>

				      </div>
				      <div class="modal-body ">
						<form class="form-inline" id='info' role='form' action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
							<div class="form-group-sm" >
								<input class='form-control' type="text" id="NP_lName" placeholder="Last Name" value="">
								<input class='form-control' type="text" id="NP_fName" placeholder="First Name" value="">
								<input class='form-control' type="date" id="NP_bDate" placeholder="Birth Date" value="">
								<hr>
							</div>
							<div class="form-group-sm" >
								<input class='form-control' type="text" id="NP_newUsername" placeholder="username" value="">
								<input class='form-control' type="password" id="NP_password" placeholder="Password" value="">
								<input class='form-control' type="email" id="NP_email" placeholder="email" value="">
								<hr>
							</div>
							<div class="form-group-sm" >
								<input class='form-control' type="text" id="NP_phone" placeholder="phone" value="">
								<input class='form-control' type="text" id="NP_city" placeholder="city" value="">
								<input class='form-control' type="text" id="NP_address" placeholder="address" value="">
								<hr>
							</div>
						    <div id='newPatientFeed'>
						    	<img id="ajax" src="ajax/loading.gif">
						    </div>
						</form> 
				      </div>
				      <div class="modal-footer">
				      	<form>
				      		<input class="btn btn-success odom-submit" id='register' type="submit" name="submit" value="Register">
				      		<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
				      	</form>  
				      	
				      </div>			      
				    </div>
				  </div>
				</div>

		    </div>
	  </div>		
	</div>
</body>

</html>