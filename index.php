<?php
session_start();
include ('lib/connect.php');
include ('lib/class.php');
if (isset($_SESSION["userData"])){
	$userData=$_SESSION["userData"];
	if ($userData['role']!='doctor') {
		header('Location: ../../index.php');
	}
	else if ($userData['role']=='doctor') {
		header('Location: users/doctor/home.php');
	}
}
$msg=false;
if (isset($_POST["submit"]) && $_POST["submit"]=="login") { 
		$username = sanitize::username($_POST["username"],$conn);
		if($username==$_POST["username"]){ //condition ? do_if_true : do_if_false
			//user entered allowed chars
			$password = hash('sha256', $_POST["password"]);
			$sql = "SELECT `username`,`password`,`role` FROM `users` WHERE `username` = '$username' LIMIT 1";
			$result = $conn->query($sql);
			if ($result->num_rows == 1){
				$row = $result->fetch_assoc();
				if ( ($row["username"]==$username) && ( $row["password"]==$password) ){
					//good log in with correct entry
					//now check who has loged in
					if ($row["role"]=='doctor') {
						$userData = array(
							'username' => $username,
							'role'=>'doctor',
						 );
						$_SESSION['userData']=$userData;
						header('Location: users/doctor/home.php');
					}
					else if ($row["role"]=='patient') {
						header('Location: users/patient/home.php');
					}
					else if ($row["role"]=='sec') {
						header('Location: users/sec/home.php');
					}
				}
				else{
					$msg='Wrong password';
				}
			}
			else if($result->num_rows == 0){
				//there is not such username
				$msg='Wrong username';
			}
			else{
				//other case, exit.
				exit('Error:L01');
			}
		}
		else{
			//user entered not allowed chars
			$msg="Bad username";
		}

	
}

?> 
<!DOCTYPE html>
<html>
<head>
	<title>Appointments</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="bootstrap/css/customBoot.css">
	<script src="js/jquery.min.js"></script>
	<script src="bootstrap/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="css/index.css">
</head>

<body>

<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">Appointments</a>
    </div>
    <div>
      <ul class="nav navbar-nav navbar-right">
        <li><a href="#"><span class="glyphicon glyphicon-user"></span> Sign Up</a></li>
      </ul>
    </div>
  </div>
</nav>
	<div class="container" >
		<div class="page-header">
		  <h1>Central Appointments Page</h1>
		  <div class="well well-sm">Doctor Smith Appointments</div>
		</div>
		<?php 
			echo $msg!=false ? "<div class='alert alert-warning'>".$msg."</div>" : $msg;
		?>

		<form method="POST" class='formDefault' role="form"  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
			<input type="text"  class="form-control" name="username" placeholder="username" value=""><br>
			<input type="password"  class="form-control" name="password" placeholder="password" value=""><br>
	        <input type="submit" class="btn btn-primary" name="submit" value="login">
		</form>
	</div>

</body>


</html>

