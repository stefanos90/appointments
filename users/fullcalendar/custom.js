var eventGlobal;
$(document).ready(function() {
	/*
	$('#trashIcon').click(function(){
		alert("Drop here an appointment to mark it as completed or press shift and drop in order to delete it");
	})*/

	
	$("#calendarPop").on('hidden.bs.modal', function () {
		$('#calendarValName').val("");
		$('#calendarValDate').val("");
		$('#calendarValTime').val("");
		$('#calendarAjax').html("");
		

	});

	
	$('#deleteApp').click(function(){
		if (confirm("Delete appointment PERMANENTLY ?") == true) {
			$.get("ajax/deleteApp.php", { id:eventGlobal.id }, function(returnedData, status){
				$('#calendarAjax').html($.trim(returnedData));
				if (htmlAway(returnedData)=='Appointment deleted') {
					$('#calendar').fullCalendar('removeEvents', eventGlobal.id);
					setTimeout(function(){
					  //console.log("runed!");
					  $('#calendarAjax .alert').fadeOut(2000);
					  $('#calendarPop').modal("hide")
					}, 2000);
				}
			});
		}
	});

	$('#completeApp').click(function(){
		if (confirm("Mark appointment as completed?") == true) {
			$.get("ajax/completeApp.php", { id:eventGlobal.id }, function(returnedData, status){
				$('#calendarAjax').html($.trim(returnedData));
				
				if (htmlAway(returnedData)=='Appointment marked as completed') {
					$('#trashIcon').attr('src','images/trashFull.png');
					$('#calendar').fullCalendar('removeEvents', eventGlobal.id);
					setTimeout(function(){
					  //console.log("runed!");
					  $('#calendarAjax .alert').fadeOut(2000);
					  $('#calendarPop').modal("hide");
					}, 2000);
					
					//$('#calendar').fullCalendar('updateEvent', event);
				}
			});
		}
	});

	$("#fullMonth").click(function(){
		$("#calendar").collapse('toggle');
	});

	$('#calendar').fullCalendar({
		eventMouseover: function(date, jsEvent, view) {
			$(this).css('background-color', 'rgb(231, 48, 48)');
		},
		eventMouseout: function(date, jsEvent, view) {
			$(this).css('background-color', 'rgb(162, 41, 41)');
		},
		eventClick: function(event, element) {

			$('#calendarValName').html(event.title);
			$('#calendarValDate').html(event.start.format('YYYY-MM-DD'));
			$('#calendarValTime').html(event.start.format('HH:mm'));

			$("#calendarPop").modal();
			eventGlobal=event;

	    	

	    },
		eventStartEditable:true,
		eventDurationEditable:true,
		defaultView: 'month',
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,basicWeek,agendaDay'
		},
		editable: true,
		droppable:true,
		eventDrop: function(event, delta, revertFunc, ui) {
			//console.log(event.title + " was dropped on " + event.start.format());
		   /* if (!confirm("Are you sure about this change?")) {
				revertFunc();
				//revertFunc is a function that, if called, reverts the event's start/end date to the values before the drag.
				//This is useful if an ajax call should fail.
			}*/
		},
		eventDragStop: function(event,jsEvent) {
			/*
			var trashEl = jQuery('#trash');
			var ofs = trashEl.offset();

			var x1 = ofs.left;
			var x2 = ofs.left + trashEl.outerWidth(true);
			var y1 = ofs.top;
			var y2 = ofs.top + trashEl.outerHeight(true);

			if (jsEvent.pageX >= x1 && jsEvent.pageX<= x2 &&
				jsEvent.pageY>= y1 && jsEvent.pageY <= y2) {
				if (jsEvent.shiftKey==true) {
					if (confirm("Permanently delete appointment "+event.title+" "+event.start.format('h:mm')+"?") == true) {
						$.get("ajax/deleteApp.php", { id:event.id }, function(returnedData, status){
							alert(returnedData);
							if ($.trim(returnedData)=='Appointment deleted') {
								$('#calendar').fullCalendar('removeEvents', event.id);
							}
						});
					}
				}
				else if(jsEvent.shiftKey==false){
					if (confirm("Mark appointment "+event.title+" "+event.start.format('h:mm')+"  as completed?") == true) {
						$.get("ajax/completeApp.php", { id:event.id }, function(returnedData, status){
							alert($.trim(returnedData));
							if ($.trim(returnedData)=='Appointment marked as completed') {
								$('#trashIcon').attr('src','images/trashFull.png');
								$('#calendar').fullCalendar('removeEvents', event.id);
								//$('#calendar').fullCalendar('updateEvent', event);
							}
						});
					}
				}
			}*/
		},
		editable: true,
		minTime:"10:00",
		maxTime:"17:00",
		height: "auto",
		weekends: false, // will hide Saturdays and Sundays
		firstDay: 1, //Mondey etc
		businessHours: {
			start: '10:00',
			end: '17:00', 
			dow: [ 1, 2, 3, 4, 5 ]
		},
		timezone: 'Europe/Athens',
		timeFormat: 'H(:mm)',
		allDay	: false,
		eventColor: 'rgb(162, 41, 41)',
		events: eventsDone,

	});
	

});

/*
$(document).ajaxStop(function(){
    window.location.reload();
});
*/

function htmlAway(dirty){
	var regex = /(<([^>]+)>)/ig
	return $.trim(dirty.replace(regex, ""));

}