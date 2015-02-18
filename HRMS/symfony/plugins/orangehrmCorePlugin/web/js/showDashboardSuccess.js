$( document ).ready(function(){
	var pholiday = 1;
	if(parseInt(consultant) == 1 ){
		//Start of AjaxCall for Agency Candidate Reffered
		$.ajax({
		     type: 'POST',
		     url: ajaxUrlagency,
		     dataType: 'text',
		     success: function(data1) {
			     if(data1 == 'false')
			     {
				     $('#agencySpan').html('<h2> No entry found!!! </h2>');
				     $('#task-list-group-panel-container-time1').hide();
				 }
			     else
			     {
				     $('#divagencySpan').hide();
				     $('#agencydata').html(data1);
			     }
			     }
		 }); 
	}else {
		if(parseInt(hiringmanager) == 1 && parseInt(interviewer) == 1){
			$.ajax({
			     type: 'POST',
			     url: ajaxUrlinterviewsInterviewer,
			     dataType: 'text',
			     success: function(data) {
				     if (data == 'false')
				    	$('#divpanel-3').hide();
					 else
				    	 $('#interviewList').html(data); 
			         }
			 });
			
			$.ajax({
			     type: 'POST',
			     url: ajaxUrlfeedbackInterviewer,
			     dataType: 'text',
			     success: function(data) {
			    	 if (data == 'false' || data == ''){
				    	 $('#pendingfeedback').hide();
					 }
				     else{
				    	 
				    	 $('#pendingFeedbackList').html(data);
				     }
			        }
			 });
		}else
		if(parseInt(hiringmanager) == 1)
		{
			//Start of AjaxCall for Current Interview List  
			$.ajax({
			     type: 'POST',
			     url: ajaxUrlinterviewsHiringMgr,
			     dataType: 'text',
			     success: function(data) {
				     if (data == 'false')
				    	$('#divpanel-3').hide();
					 else
				    	 $('#interviewList').html(data); 
			         }
			 });
			
			$.ajax({
			     type: 'POST',
			     url: ajaxUrlfeedbackHiringMgr,
			     dataType: 'text',
			     success: function(data) {
			    	 if (data == 'false' || data == ''){
				    	 $('#pendingfeedback').hide();
					 }
				     else{
				    	 
				    	 $('#pendingFeedbackList').html(data);
				     }
			        }
			 });
		}else{
			if (parseInt(interviewer) == 1)
			{
				$.ajax({
				     type: 'POST',
				     url: ajaxUrlinterviewsInterviewer,
				     dataType: 'text',
				     success: function(data) {
					     if (data == 'false')
					    	$('#divpanel-3').hide();
						 else
					    	 $('#interviewList').html(data); 
				         }
				 });
				
				$.ajax({
				     type: 'POST',
				     url: ajaxUrlfeedbackInterviewer,
				     dataType: 'text',
				     success: function(data) {
				    	 if (data == 'false' || data == ''){
					    	 $('#pendingfeedback').hide();
						 }
					     else{
					    	 
					    	 $('#pendingFeedbackList').html(data);
					     }
				        }
				 });
			}else{
			$('#divpanel-3').hide();
			$('#pendingfeedback').hide();
			}
		}
	// script for Calender
	var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();
		$('#calendar').fullCalendar({
			events: eventObj,
			theme: true,
			weekMode: 'variable',
			slotEventOverlap:false,
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},		
			editable: true,
			eventLimit: 2,
			eventRender: function(event, element) {
		        $(element).find(".fc-time").remove();
		    },
		    height: $(window).height() * 0.7
			
		});
	// end of script for Calender
	 var arr=["tdid4","tdid5","tdid6","tdid7"];
	  for(var i=1;i<5;i++)
	 	$('#th'+i).hide(); 
		$("tbody td").each(function(){
			 if(arr.indexOf($(this).attr('id')) > -1)
			 { $(this).hide(); }
			}); 
		
		// if today nobody's birthday then hide the BirthDay panel or fieldset
		if (empCount == 0){ $("#Birthday").hide(); }
		
	  $('#interviewList').html('');
	  
	//Start of AjaxCall for Public Holidays
	
	$.ajax({
	     type: 'POST',
	     url: Urlpholiday,
	     dataType: 'text',
	     async: false,
	     success: function(data) {
		     if (data == 'false'){
		    	 $('#panel-rightpholiday').hide();
		    	  pholiday = 0;
		     }			      
		     else
	         	$('#publicholidays').html(data); 
	         }
	 }); 
	
	//if Nothing in to upcoming event then hide the whole right upcoming event panel
	if(empCount == 0 && pholiday == 0){  $("#eventpanel").hide(); }
	
	// Start of AjaxCall for Upcoming Trainings
	/*$.ajax({
	     type: 'POST',
	     url: Urltraining,
	     dataType: 'text',
	     success: function(data) {
		     if (data == 'false')
			      $('#panel-training').hide();
		     else
	         	$('#publictraining').html(data); 
	         }
	 }); */
	}//end of main else part
	
	});