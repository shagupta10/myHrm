$(document).ready(function() {
	$('#candidateSearch_recordsPer_Page_Limit').val(recordsPerpage);
	$('#dialogDeleteBtn2').attr('disabled','disabled');
	$('#changeVacancy').attr('disabled', 'disabled');
	$(':checkbox[name*="chkSelectRow[]"]').attr('checked',false);
    $("#helpText").append("<span id='helpMessage'></span>");
    if($("#helpMessage").text() == ""){
        $("#helpMessage").text(lang_helpText);
    }
    if(statusMappingArray.length != null) {
    	
    	for(i=0; i<statusMappingArray.length; i++) {
    		if(statusMappingArray[i].statusName != 'Screening' && statusMappingArray[i].statusName != 'Application Initiated' && statusMappingArray[i].statusName != 'Rejected') {
    			$('#ohrmList_chkSelectRecord_'+statusMappingArray[i].id+'_'+statusMappingArray[i].vacancyId).attr('disabled',true);
    		}
    	}
    }
    //Auto complete
    $("#candidateSearch_candidateName").autocomplete(candidates, {
        formatItem: function(item) {
            return $('<div/>').text(item.name).html();
        },
        formatResult: function(item) {
            return item.name
        },  
        matchContains:true
    }).result(function(event, item) {
        $("#candidateSearch_candidateName").valid();
    });
    $('a.links').click(function(e) {
    		    e.preventDefault();
    });
    $('.division').click(function (){
    	var id = $(this).attr('id');
    	var temp = id.split("_");
    	$('#dialogDeleteBtn2').removeAttr('disabled');
    	$("#frmList_ohrmListComponent").attr("action", changeVacancyUrl+'?id='+$('input[name=vacancyToChange]:checked').val());
    	$('.descr').removeClass('bgColorDesc');
    	$('#jd_'+temp[1]).show();
    	$('#hideBtn_'+temp[1]).show();
    	$('#jd_'+temp[1]).addClass('bgColorDesc');
    });
    
    $('.hideBtn').click(function (){
    	var id = $(this).attr('id');
    	var temp = id.split("_");
    	$('#jd_'+temp[1]).hide();
    	$('#hideBtn_'+temp[1]).hide();
    });
    

    $('#btnSrch').click(function() {
        $('#candidateSearch_candidateName.inputFormatHint').val('');
        
        $('#frmSrchCandidates').submit();
   
    });
    
    $('#btnRst').click(function() {
        $('#frmSrchCandidates').get(0).reset();
        $('#candidateSearch_jobVacancy').val("");
        $('#candidateSearch_status').val("");
        $('#candidateSearch_candidateName').val("");
        $('#candidateSearch_selectedCandidate').val("");
        $('#candidateSearch_fromDate').val("");
        $('#candidateSearch_toDate').val("");
        $('#frmSrchCandidates').submit();
    });


    if ($("#candidateSearch_candidateName").val() == '') {
        $("#candidateSearch_candidateName").val(lang_typeForHints)
        .addClass("inputFormatHint");
    }
    


    $("#candidateSearch_candidateName").one('focus', function() {

        if ($(this).hasClass("inputFormatHint")) {
            $(this).val("");
            $(this).removeClass("inputFormatHint");
        }
    });


    $("#candidateSearch_candidateName").click(function() {

        if ($(this).hasClass("inputFormatHint")) {
            $(this).val("");
            $(this).removeClass("inputFormatHint");
        }
    });
    //for list
    $("#ohrmList_chkSelectAll").click(function() {
        if($(":checkbox").length == 1) {
            $('#changeVacancy').attr('disabled', 'disabled');
        }
        else {
            if($("#ohrmList_chkSelectAll").is(':checked')) {
                $('#changeVacancy').removeAttr('disabled', 'disabled');
            } else {
                $('#changeVacancy').attr('disabled', 'disabled');
            }
        }
    });
    
    $('#dialogDeleteBtn2').click(function() {
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
    
    $(':checkbox[name*="chkSelectRow[]"]').click(function() {
        if($(':checkbox[name*="chkSelectRow[]"]').is(':checked')) {
            $('#changeVacancy').removeAttr('disabled', 'disabled');
        } else {
            $('#changeVacancy').attr('disabled', 'disabled');
        }
    });
    
    $('#frmList_ohrmListComponent').attr('name','frmList_ohrmListComponent');
    $('#candidateSearch_jobVacancy').change(function() {
        var jobTitle = $('#candidateSearch_jobTitle').val();
        var vacancyId = $('#candidateSearch_jobVacancy').val();
        var url = hiringManagerListUrlForVacancyId + vacancyId;
        getHiringManagerListJson(url);
        if(vacancyId == ""){
            url = hiringManagerListUrlForJobTitle + jobTitle;
            getHiringManagerListJson(url);
        }
    });

    var fromdate = $('#candidateSearch_fromDate').val();
    $.validator.addMethod("canNameValidation", function(value, element, params) {
        var temp = false;
        var canCount = candidatesArray.length;
        if ($('#candidateSearch_candidateName').hasClass("inputFormatHint")) {
            temp = true;
        }

        else if ($('#candidateSearch_candidateName').val() == "") {
            $('#candidateSearch_selectedCandidate').val("");
            temp = true;
        }
        else{
            var i;
            for (i=0; i < canCount; i++) {
                canName = $.trim($('#candidateSearch_candidateName').val()).toLowerCase();
                arrayName = candidatesArray[i].name.toLowerCase();

                if (canName == arrayName) {
                    $('#candidateSearch_selectedCandidate').val(candidatesArray[i].id);
                    temp = true;
                    break;
                }
            }
        }
        return temp;
    });
    
    var validator = $("#frmSrchCandidates").validate({

        rules: {
            'candidateSearch[candidateName]' : {
                canNameValidation: true
            },

        },
        messages: {
            'candidateSearch[candidateName]' : {
                canNameValidation: lang_enterValidName
            },
 

        }
    });

});

function addCandidate(){
    window.location.replace(addCandidateUrl);
}




function getVacancyListJson(vcUrl, para){
    $.getJSON(vcUrl, function(data) {

        var numOptions = 0;
        if(data != null){
            numOptions = data.length;
        }
        var optionHtml = '<option value="">'+lang_all+'</option>';

        for (var i = 0; i < numOptions; i++) {

            if(data[i].id == para){
                optionHtml += '<option selected="selected" value="' + data[i].id + '">' + data[i].name + '</option>';
            }
            else{
                optionHtml += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
            }
        }
        $("#candidateSearch_jobVacancy").html(optionHtml);
    })
}
