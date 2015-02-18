$(document).ready(function(){
	var scheduleCount = $('.selectColumn').length;
	var attendeesCount = $('.selectRow').length;
	var checkedSchCount = 0;
	var checkedAttCount = 0;
	
	// get todays date
	var month = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
	var d = new Date();
	var today = d.getDate()+"-"+(month[d.getMonth()])+"-"+d.getFullYear();
	displaySavedValues();
	// on click submit
	$('.confirm').click(function(){
		if($(this).val() === 'Submit'){
			$('.modal').removeClass('hide');
			$('#fade').removeClass('hide');
		}
		else{
			$('#frmAttendance').attr("action", $('#frmAttendance').attr("action")+"/id/"+$('#trainingAttendance_trainingId').val());
			$('#frmAttendance').submit();
		}
	});
	
	$('#printAttendance').click(function() {
		$('#frmAttendance').attr('action', linkForExport+'?id='+$('#trainingAttendance_trainingId').val());
        $('#frmAttendance').submit();
        $('#frmAttendance').attr('action', linkForAttendance);
	});

	// temperory function till we get values from backend.
	/*$('#show').click(function(){
		displaySavedValues();
	})*/

	// select all
	$(".selectAllCheckbox").change(function() {
		if($(this).is(":checked")) {
			$("input[select-all~='all-checkboxes']").attr('checked', true);
		}else{
			$("input[select-all~='all-checkboxes']").attr('checked', false);
		}
	});
	// select whole column
	$(".selectColumn").change(function() {
		var schID = $(this).attr('id');
		var str = schID.split('_');
		var colNum = str[1];
		if($(this).is(":checked")) {
			$('.'+schID).attr('checked', true);
			// to check whether all the checkboxes are checked for corresponding attendees 
			updateCheckboxData(null,colNum);
			checkForFullMatrix();
			
		}else{
			$('.'+schID).attr('checked', false);
			$(".selectRow").attr('checked', false);
			$(".selectAllCheckbox").attr('checked', false);
		}
	});
	//select whole row
	$(".selectRow").change(function() {
		var attID = $(this).attr('id');
		var str = attID.split('_');
		var rowNum = str[1];
		if($(this).is(":checked")) {
			$("input[attID~='"+attID+"']").attr('checked', true);
			// to check whether all the checkboxes are checked for corresponding schedules 
			updateCheckboxData(rowNum,null);
			checkForFullMatrix();
		}else{
			$("input[attID~='"+attID+"']").attr('checked', false);
			$(".selectColumn").attr('checked', false);
			$(".selectAllCheckbox").attr('checked', false);
		}
		
	});
	// select each checkbox
	$(".child-checkbox").change(function() {
		var id = $(this).attr('id');
		var str = id.split('_');
		var attID = str[1];
		var schID = str[3];
		updateCheckboxData(attID,schID);
		checkForFullMatrix();
	});

	// find todays date
	$('.date').each(function(){
		var date = $.trim($(this)[0].firstChild.data);
		if(date==today){
			$(this).addClass('today');
			$(this).css('background','#b6b6b4');
		}
	});
	
	// highlight todays schedule column
	var todaySchedule = $('.today').find('input').attr('id');
	$('.'+todaySchedule).each(function(){
		$(this).parent().css('background','white');
	});

	// submit points directly if today is the last day of training schedule.
	var lastDate = $.trim($($('.date')[$('.date').length-1])[0].firstChild.data);
	if(lastDate == today)
	{
		$('#btnSubmit').val('Submit');
	}
	
	// disable all future schedules
	/*var headers = $('.today').nextAll().find('input').attr('disabled','disabled').each(function(){
		$('.'+$(this).attr('id')).each(function(){
			$(this).attr('disabled','disabled');
		});
	});*/
	
	function updateCheckboxData(row,col){
		// calculate both
		if(row!=null && col!=null){
			calculateChecked(row,col);
		}
		// calculate row
		else if(row!=null && col==null){
			// calculate for each col
			$('.selectColumn').each(function(index){
				var id = $(this).attr('id');
				var str = id.split('_');
				var schID = str[1];
				calculateChecked(row,schID);
			});
		}
		// calculate col
		else if(row==null && col!=null){
			// calculate for each row
			$('.selectRow').each(function(index){
				var id = $(this).attr('id');
				var str = id.split('_');
				var attID = str[1];
				calculateChecked(attID,col);
			});
		}
	}
	
	function calculateChecked(attID,schID){
		// to calculate the total schedule checked count
		checkedSchCount = $('.sch_'+schID+':checked').length;
		if(attendeesCount == checkedSchCount){
			$("#sch_"+schID).attr('checked',true);
		}else{
			$("#sch_"+schID).attr('checked',false);
		}

		// to calculate the total attendees checked count
		checkedAttCount = $("input[attID~='att_"+attID+"']"+":checked").length;
		if(scheduleCount == checkedAttCount){
			$("#att_"+attID).attr('checked',true);
		}else{
			$("#att_"+attID).attr('checked',false);
		}
	}

	function checkForFullMatrix() {
		var rowCount = 0;
		var colCount = 0;
		$('.selectColumn').each(function(){
			if($(this).is(":checked"))
				colCount++;
		});
		$('.selectRow').each(function(){
			if($(this).is(":checked"))
				rowCount++;
		});

		if((rowCount == attendeesCount) && (colCount == scheduleCount)){
			$(".selectAllCheckbox").attr('checked',true);
		}
		else{
			$(".selectAllCheckbox").attr('checked',false);
		}
	}

	// temperory function until we get data from backend
	function displaySavedValues() {
		// suppose these are values to be saved (from backend)
		for(var i=0;i<attendanceArray.length;i++)
		{
			$('#'+attendanceArray[i]).attr('checked',true);
		}
	}

	// confirmation box event listeners
	$('.yes').click(function(){
		$.ajax({
	        type: 'POST',
	        url: savePointsUrl,
	        success: function(data) { 
	            $('#description').html('Points saved');
	            $('.modal-footer').addClass('hide');
	            $('#frmAttendance').submit();
		     }
	    });
	});

	$('.no').click(function(){
		$('.modal').addClass('hide');
		$('#fade').addClass('hide');
	});
	
	$('.close').click(function(){
		$('.modal').addClass('hide');
		$('#fade').addClass('hide');
	});
	
	$("#copyToClipboard").zclip({
		path: flashPath,
		copy: function(){return attendeeEmails;},
		beforeCopy: function () { },
		afterCopy: function () {
			alert('Emails are Copied to clipboard !');
		}
	});
});