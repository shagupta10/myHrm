$(document).ready(function(){
	$("#viewTraining_trainer").tokenInput(trainerList, {
        prePopulate: prepopuldatedTrainerList,
        tokenLimit: 10,
        theme: "facebook",
        preventDuplicates: true,
        disabled: false,
        required: false 
    });

	$("#viewTraining_attendees").tokenInput(attendeesList, {
        prePopulate: prepopuldatedAttendeesList,
        tokenLimit: 10,
        theme: "facebook",
        preventDuplicates: true,
        disabled: false,
        required: false 
    });
	
	$('#btnSrch').click(function() {
		$('form#frmViewTraining').submit();
	});

	$('#btnAdd').click(function() {
		$('#frmList_ohrmListComponent').attr('action', addTrainingURL);
		$('#frmList_ohrmListComponent').submit();
	});

	$('#btnDelete').click(function() {
		$('#frmList_ohrmListComponent').attr('action', addTrainingURL);
		$('#frmList_ohrmListComponent').submit();
	});

	$('#frmList_ohrmListComponent').attr('name','frmList_ohrmListComponent');
	
	$('#btnRst').click(function() {
		$('#viewTraining_trainer').tokenInput("clear");
		$('#viewTraining_trainingName').val('');
		$('#viewTraining_attendees').tokenInput("clear");
		$('#frmViewTraining').submit();
	});
});