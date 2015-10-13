$(document).ready(function() {


$( "#logOut" ).click(function() {
  $.get("../logOut.php", { logOut:'1' }, function(returnedData, status){
		window.location.href ='../../index.php';
	});

});

$("#newApp").on('hidden.bs.modal', function () {
	$('#lName').val("");
	$('#fName').val("");
	$('#date').val("");
	$('#BirthDate').val("");
	$('#time').val("Time");
	$('#newAppFeed').html("");
});
$("#newPatient").on('hidden.bs.modal', function () {
	$('#NP_lName').val("");
	$('#NP_fName').val("");
	$('#NP_newUsername').val("");
	$('#NP_password').val("");
	$('#NP_email').val("");
	$('#NP_bDate').val("");
	$('#NP_phone').val("");
	$('#NP_city').val("");
	$('#NP_address').val("");
	$('#newPatientFeed').html("");
});



});




$(function(){
    $('#newApp').on('submit', function(e){
        e.preventDefault();
        var lName= $('#lName').val();
		var fName= $('#fName').val();
		var date= $('#date1').val();
		var BirthDate= $('#BirthDate').val();
		var time= $('#time').val();
		$.get("ajax/newApp.php", { lName:lName,fName:fName,BirthDate:BirthDate,date:date,time:time }, function(returnedData, status){
			$('#newAppFeed').html(returnedData);	
			//console.log(returnedData);
			//alert(returnedData);
			$('#appCancel').html('Close');	newPatCancel	
		});
    });
});




$(function(){
    $('#newPatient').on('submit', function(e){
        e.preventDefault();
        $('#ajax').css({"visibility":"visible"});

		var NP_lName = 		$('#NP_lName').val();
		var NP_fName = 		$('#NP_fName').val();
		var NP_newUsername = $('#NP_newUsername').val();
		var NP_password = 	$('#NP_password').val();
		var NP_email = 		$('#NP_email').val();
		var NP_bDate = 		$('#NP_bDate').val();
		var NP_phone = 		$('#NP_phone').val();
		var NP_city = 		$('#NP_city').val();
		var NP_address = 	$('#NP_address').val();

		$.get("ajax/newpatient.php", {
			lName:NP_lName,
			fName:NP_fName,
			newUsername:NP_newUsername,
			password:NP_password,
			email:NP_email,
			bDate:NP_bDate,
			phone:NP_phone,
			city:NP_city,
			address:NP_address
		},
		function(returnedData, status){
			$('#newPatientFeed img').css({"visibility":"hidden"});
			$('#newPatientFeed').html(returnedData);	
			$('#newPatCancel').html('Close');		
			setTimeout(function(){
			  //console.log("runed!");
			  $('#newPatientFeed .alert').fadeOut(2000);
			}, 5000);

		});
    });
});

$(function() {  
//new appointment date 
	$( "#date1" ).datepicker( {
		todayBtn:'linked',
		format: "yyyy-mm-dd",
	    weekStart: 1,
	    daysOfWeekDisabled: "0,6"
	});
});

$(function() {   
//new appointment patient bdate (validation) 
	$( "#BirthDate" ).datepicker({
		format: "yyyy-mm-dd",
	    weekStart: 1,

	});
});

$(function() {   
//patient registration birthdate  
	$( "#NP_bDate" ).datepicker({
		format: "yyyy-mm-dd",
	    weekStart: 1,

	});
});


