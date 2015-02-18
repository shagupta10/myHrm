$(document).ready(function() {
    //Auto complete
    $("#jobRequests_candidateName").autocomplete(candidates, {
        formatItem: function(item) {
            return $('<div/>').text(item.name).html();
        },
        formatResult: function(item) {
            return item.name
        },  
        matchContains:true
    }).result(function(event, item) {
        $("#jobRequests_candidateName").valid();
        $("#jobRequests_candidateId").val(item.id);
    });
    
     $("#jobRequests_requesterName").autocomplete(employees, {
        formatItem: function(item) {
            return $('<div/>').text(item.name).html();
        },
        formatResult: function(item) {
            return item.name
        },
        matchContains:true
    }).result(function(event, item) {
        $("#jobRequests_requesterName").valid();
        $("#jobRequests_requesterId").val(item.id);
    });
    

    $('#btnSrch').click(function() {
        $('#jobRequests_candidateName.inputFormatHint').val('');
        $('#jobRequests_keywords.inputFormatHint').val('');
        if ($('#jobRequests_requesterName').val() == "") {
           $('#jobRequests_requesterId').val("");
        }
        $('#frmSrchCandidates').submit();
   
    });
    
    $('#btnRst').click(function() {
        $('#frmSrchCandidates').get(0).reset();
        $('#jobRequests_fromDate').val("");
        $('#jobRequests_toDate').val("");
        $('#jobRequests_status').val("");
        $('#jobRequests_candidateName').val("");
        $('#jobRequests_candidateId').val("");
         $("#jobRequests_requesterName").val("");
        $("#jobRequests_requesterId").val("");
        $('#frmSrchCandidates *[name^="additionalParams"]').val("");
        $('#frmSrchCandidates').submit();
    });


    if ($("#jobRequests_candidateName").val() == '') {
        $("#jobRequests_candidateName").val(lang_typeForHints)
        .addClass("inputFormatHint");
    }
    
    if ($("#jobRequests_requesterName").val() == '') {
        $("#jobRequests_requesterName").val(lang_typeForHints)
        .addClass("inputFormatHint");
    }

    $("#jobRequests_candidateName").one('focus', function() {

        if ($(this).hasClass("inputFormatHint")) {
            $(this).val("");
            $(this).removeClass("inputFormatHint");
        }
    });
    
   $("#jobRequests_requesterName").one('focus', function() {

        if ($(this).hasClass("inputFormatHint")) {
            $(this).val("");
            $(this).removeClass("inputFormatHint");
        }
    });
    
   

    $("#jobRequests_candidateName").click(function() {

        if ($(this).hasClass("inputFormatHint")) {
            $(this).val("");
            $(this).removeClass("inputFormatHint");
        }
    });
    
     $("#jobRequests_requesterName").click(function() {

        if ($(this).hasClass("inputFormatHint")) {
            $(this).val("");
            $(this).removeClass("inputFormatHint");
        }
    });

	 $('#btnDelete').click(function() {
		 $("#frmList_ohrmListComponent").attr("action", changeVacancyUrl+"?fromPage=jobRequest&actionName=delete");
	 });
	 
    $('#reject').click(function() {
    	$("#frmList_ohrmListComponent").attr("action", changeVacancyUrl+"?fromPage=jobRequest&actionName=reject");
    });
    
    $('#changeVacancy').click(function() {
    	$("#frmList_ohrmListComponent").attr("action", changeVacancyUrl+"?fromPage=jobRequest&actionName=changeVacancy");
    });

    $('#btnDelete').attr('disabled', 'disabled');
    $('#reject').attr('disabled', 'disabled');
    $('#changeVacancy').attr('disabled', 'disabled');

        
    $("#ohrmList_chkSelectAll").click(function() {
        if($(":checkbox").length == 1) {
            $('#btnDelete').attr('disabled','disabled');
            $('#reject').attr('disabled', 'disabled');
            $('#changeVacancy').attr('disabled', 'disabled');
        }
        else {
            if($("#ohrmList_chkSelectAll").is(':checked')) {
                $('#btnDelete').removeAttr('disabled');
                $('#reject').removeAttr('disabled', 'disabled');
                $('#changeVacancy').removeAttr('disabled', 'disabled');
            } else {
                $('#btnDelete').attr('disabled','disabled');
                $('#reject').attr('disabled', 'disabled');
                $('#changeVacancy').attr('disabled', 'disabled');
            }
        }
    });
    
    $(':checkbox[name*="chkSelectRow[]"]').click(function() {
        if($(':checkbox[name*="chkSelectRow[]"]').is(':checked')) {
            $('#btnDelete').removeAttr('disabled');
            $('#reject').removeAttr('disabled', 'disabled');
            $('#changeVacancy').removeAttr('disabled', 'disabled');
        } else {
            $('#btnDelete').attr('disabled','disabled');
            $('#reject').attr('disabled', 'disabled');
            $('#changeVacancy').attr('disabled', 'disabled');
        }
    });
    
    $('#dialogDeleteBtn,#dialogRejectBtn').click(function() {
    	$.blockUI({ 
    		message: $('#domMessage'),
    		css: { 
                border: 'none', 
                padding: '15px', 
                backgroundColor: '#000', 
                '-webkit-border-radius': '10px', 
                '-moz-border-radius': '10px', 
                opacity: .5, 
                color: '#fff' 
            }
    		});
    	document.frmList_ohrmListComponent.submit();
    });


    $('a.links').click(function(e) {
    		    e.preventDefault();
    });
})
