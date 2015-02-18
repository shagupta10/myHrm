	$(document).ready(function() {
		$('#trainerFrm').hide();
		$('#trainer_enabled').attr('checked',true);
		$('#btn_add').click(function() {
			 $('#trainerFrm').show();
		});
		$('#btnCcl').click(function() {
			 validator.resetForm();
			 $('#trainer_id').attr('value','');
			 $('#trainer_firstName').attr('value','');
			 $('#trainer_lastName').attr('value','');
			 $('#trainer_details').attr('checked',true);
			 $('#addTrainerHeading').html('Add External Trainer');
			 $('#trainerFrm').hide();
		});
		$('#btnSave').click(function() {
			if($('#frmAddTrainer').valid()) {
				if($('#btnSave').attr('value') == 'Update') {
					$('#btnSave').attr('value', 'Updating...');
					$('#btnSave').attr('disabled', 'disabled');
				} else {
					$('#btnSave').attr('value', 'Saving...');
					$('#btnSave').attr('disabled', 'disabled');
				}
				$('#frmAddTrainer').submit();
			}
		});
		$('a[href="javascript:"]').click(function(){
	        var row = $(this).closest("tr");
	        var Id = row.find('input').val();
	        var url = trainerInfoUrl+Id;
	        getTrainingTypeInfo(url);
		});
			
		var validator = $("#frmAddTrainer").validate({
			 rules: {
			 	'trainer[firstName]': {
				 	required: true,
			 		},
		 		'trainer[lastName]': {
				 	required: false,
			 		},
			 },
			 messages: {
				'trainer[firstName]': {
					required: lang_required,
					},
				'trainer[lastName]': {
					required: lang_required,
					},
			 } 
		});
	});

	 function getTrainingTypeInfo(url) {
	    $.getJSON(url, function(data) {
	        $('#trainer_id').val(data.id);
	        $('#trainer_firstName').val(data.firstName);
	        $('#trainer_lastName').val(data.lastName);
	        $('#trainer_details').val(data.details);
	        $('#btnSave').val('Update');
	        $('#addTrainerHeading').html('Update Trainer Info');
	        $('#trainerFrm').show();
	    });
	}

	