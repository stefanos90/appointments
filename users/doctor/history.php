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
		
		if (isset($_POST['daterange'])) {
			$date=$_POST['daterange'];
			//10 chars from begening
			if (sanitize::dDate(substr($date, 0, 10),$conn)) {
				$start=substr($date, 0, 10);
			}
			else{
				$start=date('Y-m-d');
			}
			if (sanitize::dDate(substr($date,-10),$conn)) {
				$end=substr($date,-10);
			}
			else{
				$end=date('Y-m-d');
			}
			//prepare data for chart
			if ($dates=$user->viewHistory($start,$end)) {//get appointment dates (distinct)
				foreach ($dates as $key => $value) {
					//ask db for every date
					$dateVisits[]=$user->countDates($value);
				}
			}
			else{
				$ready[]=null;
			}
		}
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
	<link rel="stylesheet" href="../../bootstrap/css/customBoot.css">
	<script src="../../js/jquery.min.js"></script>
	<script src="../../bootstrap/js/bootstrap.min.js"></script>
	<script src='../fullcalendar/lib/moment.min.js'></script>

	<script type="text/javascript" src="js/daterangepicker/daterangepicker.js"></script>
	<link rel="stylesheet" type="text/css" href="js/daterangepicker/daterangepicker.css" />
	<script src='js/daterangepicker/settings.js'></script>

	<link rel="stylesheet" href="css/doctor.css">
	<script src='js/chart/chart.js'></script>
	<script type="text/javascript">
		var dates=<?php if (isset($dates)) {echo json_encode($dates);} else echo "['No Dates']";
			
		?>;
		var dateVisits=<?php if (isset($dateVisits)) {echo json_encode($dateVisits);} else echo "['0']";
		?>;
	</script>
	<script src='js/chart/settings.js'></script>


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
				<li ><a href="home.php">Home</a></li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Patients<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="#" data-toggle="modal" data-target="#newPatient"><span class="glyphicon glyphicon-plus"></span> Register</a></li>
						<li role="separator" class="divider"></li>
						<li><a href="search.php"><span class="glyphicon glyphicon-envelope"></span> New Message</a></li>
					</ul>
				</li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Appointments<span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="#" data-toggle="modal" data-target="#newApp"><span class="glyphicon glyphicon-plus"></span> Create New</a></li>
						<li role="separator" class="divider"></li>
						<li class="active"><a href="history.php"><span class="glyphicon glyphicon-eur"></span> View History</a></li>
					</ul>
				</li>
				<li><a href="search.php">Requests</a></li>
				<li><a href="search.php">Contacts</a></li>
				<div class="col-sm-3 col-md-3 pull-right">

				</div>
			</ul>
			<ul class="nav navbar-nav navbar-right ">
				<li>
					<form class="navbar-form" method='GET' action='<?php echo htmlspecialchars("search.php");?>' role="search">
						<div class="input-group ">
							<input type="text" class="form-control" placeholder="Search patient or 'all'" name="srch" id="srch-term">
							<div class="input-group-btn">
							<button class="btn btn-default" type="submit"><i class="glyphicon glyphicon-search"></i></button>
						</div>
						</div>
					</form>
				</li>
				<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo $user->getFullName();?> <span class="caret"></span></a>
					<ul class="dropdown-menu">
						<li><a href="#"><span class="glyphicon glyphicon-list-alt"></span> Notifications</a></li>
						<li role="separator" class="divider"></li>
						<li><a href="#"><span class="glyphicon glyphicon-user"></span> Profile</a></li>
						<li><a href="#"><span class="glyphicon glyphicon-wrench"></span> Settings</a></li>
						<li role="separator" class="divider"></li>
						<li><a href="#" id='logOut'><span class="glyphicon glyphicon-off"></span> Log out</a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</nav>

	<div class="container-fluid" >
		<div class="row">
			<div class="col-sm-12 col-md-12">
				<ul class="breadcrumb home" >
					<li><a href="home.php">Home</a></li>
					<li class='active'>History</li>
				</ul>
				<hr>
				<div class="panel panel-primary statistics center-block text-center">
					<div class="panel-heading"><span>Select date range to view</span></div>
					<div class="panel-body">
						<form class="form-inline " action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>' method='POST' role="form">
							<div class="form-group">
								<input type="text" class="form-control" id="daterange" name='daterange' placeholder="Select date range" value="" style='max-width: 185px;'>
								
								<button type="submit" class="btn btn-primary">View</button>
							</div>
						</form>
					</div>
					<div class="panel-footer" id='showCount'>
						
					</div>
				</div>	
			</div>
			<div class="col-sm-12 col-md-12 ">
				<?php 
				if (isset($dateVisits) && isset($dates)) {
					echo '<canvas id="visits" class="center-block" width="900" height="400"></canvas><br>';
				}
				else echo '<div class="center-block text-center alert alert-warning fade in">Nothing found on that date</div>';

				?>			
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
									<input class='form-control' pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" title="Date must be YYYY-MM-DD" required type="text" id="BirthDate" placeholder="Patient Birth Date" value=""><br>
									<i class="form-control-feedback glyphicon glyphicon-user" style='color: rgb(194, 0, 0);'></i>
								</div>
								<div class="form-group has-feedback" style='margin: 0px;'>
									<input id="date1"  class='form-control' pattern="(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))" title="Date must be YYYY-MM-DD" required type="text" placeholder="Appointment Date" value=""><br>
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
								<hr>
						    	<div class='right'>
							    	<input class="btn btn-success odom-submit" type="submit" name="submit" value="Save">
							    	<button type="button" class="btn btn-danger" id='appCancel' data-dismiss="modal">Cancel</button>
							    </div>
							</form>	
						</div>
					    
				    </div>
				  </div>
				</div>

				<div id="newPatient" class="modal fade" role="dialog">
					<div class="modal-dialog modal-lg">
				    <!-- Modal content-->
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title ">Register New Patient</h4>
							</div>
							<div class="modal-body ">
								<form class="form-inline" id='info' role='form' action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
									<div class="form-group-sm" >
										<input class='form-control' type="text" id="NP_lName" placeholder="Last Name" required value="">
										<input class='form-control' type="text" id="NP_fName" placeholder="First Name" required value="">
										<input class='form-control' type="text" id="NP_bDate" placeholder="Birth Date" required value="">
										<hr>
									</div>
									<div class="form-group-sm" >
										<input class='form-control' type="text" id="NP_newUsername" placeholder="username" required value="">
										<input class='form-control' type="password" id="NP_password" placeholder="Password" required value="">
										<input class='form-control' type="email" id="NP_email" placeholder="email" required value="">
										<hr>
									</div>
									<div class="form-group-sm" >
										<input class='form-control' type="text" id="NP_phone" placeholder="phone" required value="">
										<input class='form-control' type="text" id="NP_city" placeholder="city" required value="">
										<input class='form-control' type="text" id="NP_address" placeholder="address" required value="">
										<hr>
									</div>
								 	<div id='newPatientFeed'>
								    	<img id="ajax" src="ajax/loading.gif">
									</div>
									<div class='right'>
										<input class="btn btn-success odom-submit" id='register' type="submit" name="submit" value="Register" style='margin-bottom:0px;'>
										<button type="button" class="btn btn-danger" id='newPatCancel' data-dismiss="modal">Cancel</button>
									</div>
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