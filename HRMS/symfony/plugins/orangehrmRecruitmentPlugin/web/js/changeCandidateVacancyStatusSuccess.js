$(document).ready(function() {
	$('#cancelBtn').click(function(){
		if($("#cancelBtn").attr('value') == lang_back) {
			window.location.replace(cancelBtnUrl+'?id='+candidateId);
		}
		if($("#cancelBtn").attr('value') == lang_cancel) {
			window.location.replace(cancelUrl+'?id='+historyId);
		}
	});
	
	if(selectedAction == passAction || selectedAction == failAction || flag){
		$("#actionBtn").removeClass('savebutton').addClass('newSaveBtn');
		$(".radio").show();
		$(".textcomments").show();
	  }else {
		 $(".radio").hide();
		 $(".textcomments").hide();
	   }
	
	$('#actionBtn').click(function(){
		if(selectedAction == passAction || selectedAction == failAction){
			if($( "#frmCandidateVacancyStatus" ).valid()){
				$('#frmCandidateVacancyStatus').attr({
					action:linkForchangeCandidateVacancyStatus+"?candidateVacancyId="+candidateVacancyId+'&selectedAction='+selectedAction
					});
					$('#frmCandidateVacancyStatus').submit();
			}
		}else{
				$('#frmCandidateVacancyStatus').attr({
				action:linkForchangeCandidateVacancyStatus+"?candidateVacancyId="+candidateVacancyId+'&selectedAction='+selectedAction
				});
				$('#frmCandidateVacancyStatus').submit();
		}
    });
	
	$('#btnSave').click(function() {
		if($("#btnSave").attr('value') == lang_edit) {
			$(".formInputText").removeAttr("disabled");
			$(".formInputTextComments").removeAttr("disabled");
			$(".checkattitude").removeAttr("disabled");
			$(".checksoftskill").removeAttr("disabled");
			$(".checkcommunication").removeAttr("disabled");
			$("#btnSave").attr('value', lang_save);
			$("#cancelBtn").attr('value', lang_cancel);
			return;
		}
            
		if($("#btnSave").attr('value') == lang_save) {
			$('#frmCandidateVacancyStatus').attr({
				action:linkForchangeCandidateVacancyStatus+"?id="+historyId
			});
			$('#frmCandidateVacancyStatus').submit();
		}
	});
	
	var validator = $('#frmCandidateVacancyStatus').validate({

        rules: {
            'candidateVacancyStatus[optSoftSkill]' : {
                required:true,
            },
            
            'candidateVacancyStatus[optAttitude]' : {
            	required:true,
            },
            
            'candidateVacancyStatus[optCommunication]' : {
            	required:true,
            },
            
            'candidateVacancyStatus[notes]' : {
            	required:true,
            }
           },
        messages: {
            'jobInterview[optSoftSkill]' : {
                required: lang_softskill
            },

            'jobInterview[optAttitude]' : {
                required: lang_attitude
            },
            
            'jobInterview[optCommunication]' : {
                required: lang_communication
            },
            
            'jobInterview[notes]' : {
                required: lang_notes
            }
         }
    });
    
});