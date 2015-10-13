<?php
define('debug', false);
class sanitize{
	
	function username($unsafe,$conn){
		//$unsafe=htmlspecialchars($unsafe);
		$unsafe=mysqli_real_escape_string($conn,$unsafe);
		$allowed='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVQXYZ0123456789';
	    $cleaned = "";
	    $pos = 0;
	    do {
	        $char = substr($unsafe, $pos, 1);
	        if(strspn($char, $allowed, 0, strlen($allowed)) > 0) {
	            $cleaned = $cleaned . $char;
	        }
	        $pos = $pos + 1;
	    }
	    while ($pos < strlen($unsafe));
	    return $cleaned;		
	}
	function numbers($unsafe,$conn){
		$unsafe=mysqli_real_escape_string($conn,$unsafe);
		$allowed='0123456789';
	    $cleaned = "";
	    $pos = 0;
	    do {
	        $char = substr($unsafe, $pos, 1);
	        if(strspn($char, $allowed, 0, strlen($allowed)) > 0) {
	            $cleaned = $cleaned . $char;
	        }
	        $pos = $pos + 1;
	    }
	    while ($pos < strlen($unsafe));
	    return $cleaned;

	}
	function text($unsafe,$conn){
		$unsafe=mysqli_real_escape_string($conn,$unsafe);
		$allowed='αβγδεζηθικλμνξοπρστυφχψωςάέήίόύώΎΆΈΉΊΌΏΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩabcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVQXYZ ';
	    $cleaned = "";
	    $pos = 0;
	    do {
	        $char = substr($unsafe, $pos, 1);
	        if(strspn($char, $allowed, 0, strlen($allowed)) > 0) {
	            $cleaned = $cleaned . $char;
	        }
	        $pos = $pos + 1;
	    }
	    while ($pos < strlen($unsafe));
	    return $cleaned;
	}

	function dDate($unsafe,$conn){
		$unsafe=mysqli_real_escape_string($conn,$unsafe);
		$test_date = $unsafe;

		$date = DateTime::createFromFormat('Y-m-d', $test_date);
		$date_errors = DateTime::getLastErrors();
		if ($date_errors['warning_count'] + $date_errors['error_count'] > 0) {
			debug ? var_dump($date_errors) : debug;
		    return false;
		}
		else return true;	    
	}
	function tTime($unsafe,$conn){
		$test_time = mysqli_real_escape_string($conn,$unsafe);

		$time = DateTime::createFromFormat('H:i', $test_time);
		$time_errors = DateTime::getLastErrors();
		if ($time_errors['warning_count'] + $time_errors['error_count'] > 0) {
			debug ? var_dump($time_errors) : debug;
		    return false; //if any error return false 
		}
		else return true;
	}
	function address($unsafe,$conn){
		$unsafe=mysqli_real_escape_string($conn,$unsafe);
		$allowed='αβγδεζηθικλμνξοπρστυφχψωςάέήίόύώΎΆΈΉΊΌΏΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩabcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVQXYZ,.0123456789 ';
	    $cleaned = "";
	    $pos = 0;
	    do {
	        $char = substr($unsafe, $pos, 1);
	        if(strspn($char, $allowed, 0, strlen($allowed)) > 0) {
	            $cleaned = $cleaned . $char;
	        }
	        $pos = $pos + 1;
	    }
	    while ($pos < strlen($unsafe));
	    return $cleaned;
	}
	function email($unsafe){
		if (!filter_var($unsafe, FILTER_VALIDATE_EMAIL)) {
		    return false;
		}
		else return true;
	}

}
class Register{
	protected
		$conn;
		/*
		$username,	//User's username
		$role,		//users role
		$dUsername, //the doctor who is registering the user
		$password,	
		$fName,		//First name
		$lName,		//Last name
		$bDate,		//birthdate
		$email,		//User's email
		$phone,		//user phone number
		$city,
		$address,
		$imgUrl;	//Image link
		*/
	function register($newUsername,$dUsername,$lName,$fName,$email,$bDate,$address,$city,$phone,$password){
		$sql = "INSERT INTO `users` (`username`, `role`, `active`, `dUsername`, `password`, `fName`, `lName`, `bDate`, `email`, `phone`, `city`, `addr`, `imgUrl`)
					VALUES ('$newUsername',
							'patient',
							'0',
							'$dUsername',
							'$password',
							'$fName', 
							'$lName',
							'$bDate',
							'$email',
							'$phone',
							'$city',
							'$address',
							'no_avatar.jpg');";
		$result = $this->conn->query($sql);
		if ( mysqli_affected_rows($this->conn)==1) {
			return true;
		}
		else{
			return mysqli_affected_rows($this->conn);
		}
	}
	function unique($usernameToTest,$emaiToTest){
		$sql = "SELECT `username`,`email` FROM `users` WHERE (`username`='$usernameToTest' OR `email`='$emaiToTest') LIMIT 1 ";
		$result = $this->conn->query($sql);
		if ($result->num_rows==1){
			return false;
		}
		else if ($result->num_rows==0){
			return true;
		}
	}

	function __construct($connection){
		$this->conn=$connection;
	}
	
}
class User{
	protected
		$role, 		//Doctor, patien or secratery 
		$loged,		//Bool, if user is loged right now
		$username,	//User's username
		$email,		//User's email
		$fName,		//First name
		$lName,		//Last name
		$imgUrl,	//Image link
		$phone,		//user phone number
		$conn, 		//connection for sql queries pased from constructor
		$appointments = array(), 		//User active appointments
		$todayAppointments = array(), 	//User TODAY appointments
		$messages = array();	 		//User messages to read

	function setRole($type){
		$this->role=$type;
	}
	function getRole(){
		return $this->role;
	}
	function setLoged(){
		$this->loged=true;
	}
	function getLoged(){
		return $this->loged;
	}
	function setusername($username){
		$this->username=$username;
	}
	function getusername(){
		return $this->username;
	}
	function setEmail($email){
		$this->email=$email;
	}
	function getEmail(){
		return $this->email;
	}
	function setfName($fname){
		$this->fName=$fname;
	}
	function getfName(){
		return $this->fName;
	}
	function setlName($lname){
		$this->lName=$lname;
	}
	function getlName(){
		return $this->lName;
	}
	function setimgUrl($link){
		$this->imgUrl=$link;
	}
	function getimgUrl(){
		return $this->imgUrl;
	}
	function setPhone($number){
		$this->phone=$number;
	}
	function getPhone(){
		return $this->phone;
	}
	function getFullName(){
		return $this->lName." ".$this->fName;
	}
	function getAppointmets($date,$username){
		//query the appointments table for specific user
		return $this->appointments;
	}
	function getMyApps(){
		//query the appointments table for current month and current user
		$today=date('Y-m')."-%"; //wildcard for the full month
		$sql = "SELECT `id`,`fName`,`lName`,`time`,`date`
					FROM `users` JOIN `appointments`
					ON appointments.pusername=users.username
					WHERE appointments.dUsername='$this->username'
					AND `date` LIKE '$today'AND `approved`='1' AND `completed`='0'
					ORDER BY `time` ASC";
		$result = $this->conn->query($sql);
		if ($result->num_rows>0){
			while ($row = $result->fetch_assoc()) {
				$this->todayAppointments[]=$row;
			}
			return $this->todayAppointments;
		}
		else{
			return false;
		}

	}
	function sendMsg($text,$to){
		//insert into messages
		//from $this->username
		//to $to
	}
	function getMessage (){
		//select from messages where username=$this->username
	}

	function __construct($sUsername,$connection){ //username from session
		$this->conn=$connection;
		$sql = "SELECT * FROM `users` WHERE `username` = '$sUsername' LIMIT 1";
		$result = $this->conn->query($sql);
		if ($result->num_rows == 1){
			$row = $result->fetch_assoc();
			$this->setRole($row['role']);
			$this->setLoged();
			$this->setusername($row['username']);
			$this->setEmail($row['email']);
			$this->setfName($row['fName']);
			$this->setlName($row['lName']);
			$this->setimgUrl($row['imgUrl']);
			$this->setPhone($row['phone']);
		}
	}

}

class doctor extends User{
	var $appsToAccept = array();

	function newApp($bDate,$date,$time,$lName,$fName){
	/**
		returning cases
		1: appointment saved
		2: there is appointment on that date and time
		3: user has appointmenton that day 
		4: there is no such user
	*/
		$sql = "SELECT `username` FROM `users` WHERE (`bDate` = '$bDate' AND `lName`='$lName' AND `fName`='$fName' AND `role`='patient' AND `dUsername`='$this->username') LIMIT 1";
		$result = $this->conn->query($sql);
		if ($result->num_rows == 1){
			//user exists
			$row = $result->fetch_assoc();
			$pUsername=$row['username'];

			$sql="SELECT `id` FROM `appointments` WHERE (`date`='$date' AND `pUsername`='$pUsername' AND `dUsername`='$this->username') ";
			$result = $this->conn->query($sql);
			if ($result->num_rows ==1){
				//user has appointmenton that day 
				return 3;
			}
			$sql="SELECT `id` FROM `appointments` WHERE (`date`='$date' AND `time`='$time' AND `dUsername`='$this->username') ";
			$result = $this->conn->query($sql);
			if ($result->num_rows == 0){
				//there is no appointment on that time and date from the current doctor
				//save the appointment 
				$sql = "INSERT INTO `appointments` (`dUsername`, `pUsername`, `date`, `time`, `completed`, `approved`)
						VALUES ('$this->username', '$pUsername','$date','$time','0','1');";
				$result = $this->conn->query($sql);
				if ( mysqli_affected_rows($this->conn)==1) {
					//appointment saved
					return 1;
				}
				else{
					//error, app not saved
					return mysqli_affected_rows($this->conn);
				}
			}
			else if ($result->num_rows >0){
				//there is appointment on that date and time
				return 2;
			}
		}
		else{
			//there is no such user
			return 4;
		}
	}

	function getAppsToAccept($FullDate){

	}

	function acceptApp($FullDate,$username){

	}
	function denyApp($FullDate,$username){

	}
	function checkAppExist($id){
		$sql="SELECT `id` FROM `appointments` where `id`='$id' AND `dUsername`='$this->username'";
		$result = $this->conn->query($sql);
		if ($result->num_rows==1){
			return true;
		}
		else{
			return false;
		}

	}
	function deleteApp($id){
		if ($this->checkAppExist($id)) {
			$sql="DELETE FROM `appointments` WHERE `id`='$id' AND `dUsername`='$this->username'";
			$result = $this->conn->query($sql);
			if ( mysqli_affected_rows($this->conn)==1) {
				return true;
			}
			else{
				return false;
			}
		}
	}
	function completeApp($id){
		if ($this->checkAppExist($id)) {
			$sql="UPDATE `appointments` SET `completed` = '1' WHERE `dUsername` = '$this->username' AND `id` = '$id'";
			$result = $this->conn->query($sql);
			if ( mysqli_affected_rows($this->conn)==1) {
				return true;
			}
			else{
				return false;
			}
		}
	}
	
	function searchPatient($sTerm){
		if ($sTerm=='all') {
			$sql="SELECT `lName`,`fName`,`imgUrl`,`regDate`,`bDate` FROM `users` WHERE `role`='patient' AND `dUsername`='$this->username' AND `lName` LIKE '%' ";
		}
		else{
			$sql="SELECT `lName`,`fName`,`imgUrl`,`regDate`,`bDate` FROM `users` WHERE `role`='patient' AND `dUsername`='$this->username' AND `lName` LIKE '$sTerm' ";
		}
		$result = $this->conn->query($sql);
		if ($result->num_rows>0){
			while ($row = $result->fetch_assoc()) {
				$found[]=$row;
			}
			return $found;
		}
		else return false;
	}
	function viewHistory($start,$end){
		$sql="SELECT DISTINCT `date` FROM `appointments` where `completed`='1' AND (`date` >= '$start' AND `date` <='$end' ) AND `dUsername`='$this->username'";
		$result = $this->conn->query($sql);
		if ($result->num_rows>0){
			while ($row = $result->fetch_assoc()) {
				$dates[]=$row['date'];
			}
			return $dates;
		}
		else return false;
	}
	function countDates($date){
		$sql="SELECT COUNT(`date`) FROM `appointments` where `completed`='1' AND `date` = '$date' AND `dUsername`='$this->username'";
		$result = $this->conn->query($sql);
		if ($result->num_rows>0){
			$row = $result->fetch_assoc();
			return $row['COUNT(`date`)'];
		}
		else return false;
	}

} 

class patient extends User{
	var $active;
	function requestApp($FullDate){ //new request for appointment date

	}
	function getActive(){			//query the user table for active

	}

}

class secratery extends User{
	function newApp($username,$date,$time){
		//username for which patient
		//date is the day, time is the time
		echo "$username,$date,$time";
	}
}

?> 

