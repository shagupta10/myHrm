var nextId = 0;
var toDisable = new Array();
var item = 0;
var vacancyId;

$(document).ready(function() {
    //validation starts
	$.validator.addMethod('date_range_comp', function(value, element, params) {

		var valid = false;
		var fromDate = $.trim(value);
		var toDate = $.trim(currentDate);
		var format = datepickerDateFormat;

		if(fromDate == format || toDate == format || fromDate == "" || toDate =="") {
			valid = true;
		}else{
			var parsedFromDate = $.datepicker.parseDate(format, fromDate);
			var parsedToDate = $.datepicker.parseDate(format, toDate);
			if(parsedFromDate <= parsedToDate){
				valid = true;
			}
		}
		return valid;
	});
	
	$.validator.addMethod('date_range_future_comp', function(value, element, params) {

		var valid = false;
		var fromDate = $.trim(value);
		var toDate = $.trim(currentDate);
		var format = datepickerDateFormat;
		var format2 = "yyyy-mm-dd";

		if(fromDate == format || fromDate == format2 || toDate == format || fromDate == "" || toDate =="") {
			valid = true;
		}else{
			var parsedFromDate = $.datepicker.parseDate(format, fromDate);
			var parsedToDate = $.datepicker.parseDate(format, toDate);
			if(parsedFromDate >= parsedToDate){
				valid = true;
			}
		}
		return valid;
	});
	
	$.validator.addMethod("isAlpha", function(value, element, params) {
		var isAlpha=true;
		for(var i=0;i<value.length;i++)
		{
			if (!value[i].match(/[a-zA-Z]/))
			{
				isAlpha=false;
				break;
			}
		}   
		return isAlpha;
	});


    var validator = $("#frmAddCandidate").validate({

        rules: {
            'addCandidate[firstName]' : {
            	isAlpha: true,
            	required: {
                    depends:function(){
                        $(this).val($.trim($(this).val()));
						$(this).val($(this).val().replace(/\s+/g, ''));
                        return true;
                    }
                },
                maxlength:30
            },

            'addCandidate[middleName]' : {
                maxlength:30
            },

            'addCandidate[lastName]' : {
            	isAlpha: true,
            	required: {
                    depends:function(){
                        $(this).val($.trim($(this).val()));
						$(this).val($(this).val().replace(/\s+/g, ''));
                        return true;
                    }
                },
                maxlength:30
            },
            'addCandidate[email]' : {
                required:true,
                email:true,
                maxlength:100,
                onkeyup: 'if_invalid'
            },
            'addCandidate[alternateEmail]' : {
                email:true,
                maxlength:100,
                onkeyup: 'if_invalid'
            },

            'addCandidate[contactNo]': {
                required: true,
            	number: true,
                maxlength:10,
                minlength:10,
            },
            'addCandidate[alternateNumber]': {
            	number: true,
                maxlength:10,
                minlength:10,
            },
            'addCandidate[resume]': {
            	required: true
            },
            'addCandidate[keyWords]': {
                maxlength:250
            },
            
            'addCandidate[educationDetailDegree]' : {
                maxlength:100
            },
            
            'addCandidate[educationDetailSpec]' : {
                maxlength:100
            },
            
            'addCandidate[educationDetailPerc]' : {
                number:true,
                maxlength:10
            },
            
            'addCandidate[comment]': {
                maxlength:250
            },
            'addCandidate[appliedDate]' : {
                valid_date: function() {
                    return {
                        format:datepickerDateFormat,
                        required:false,
                        displayFormat:displayDateFormat

                    }
                },
                    date_range_comp: true
                },
                'addCandidate[currentCtc]' : {
                    number: true,
                    maxlength:30
                },
                'addCandidate[expectedCtc]' : {
                    number: true,
                    maxlength:30
                },
                'addCandidate[noticePeriod]' : {
                    number: true,
                    maxlength:4
                },
                'addCandidate[originalLocation]' : {
                     maxlength:200
                 },
                 'addCandidate[expectedDoj]': {
                	 valid_date: function() {
                     return {
                         format:datepickerDateFormat,
                         required:false,
                         displayFormat:displayDateFormat

                     }
                 }
                },
                'addCandidate[visaStatus]' : {
                    maxlength:100
                },
                'addCandidate[vacancy]' : {
                    required:true
                },
                'addCandidate[totalExperience]' : {
                    number:true,
                    maxlength:4,
                },
                'addCandidate[relevantExperience]' : {
                    number:true,
                    maxlength:4
                },
                'addCandidate[educationGap]' : {
                    number:true,
                    maxlength: 4
                },
                'addCandidate[workGap]' : {
                    digits:true,
                    maxlength: 3
                },
                'addCandidate[keySkills]' : {
                    maxlength:200
                },
                'addCandidate[currentCompany]' : {
                    maxlength:50
                },
                'addCandidate[designation]' : {
                	maxlength:30,
                },
                'addCandidate[referralName]' : {
                 required:true
                },
                'addCandidate[preferredLocation]' : {
                maxlength:200
                },
                'addCandidate[communicationSkills]' : {
                maxlength:200
                }
            },
        messages: {
            'addCandidate[firstName]' : {
            	isAlpha: lang_validFirstName,
                required: lang_firstNameRequired,
                maxlength: lang_tooLargeInput
            },

            'addCandidate[middleName]' : {
                maxlength: lang_tooLargeInput
            },
            'addCandidate[lastName]' : {
            	isAlpha: lang_validLastName,
                required: lang_lastNameRequired,
                maxlength: lang_tooLargeInput
            },

            'addCandidate[contactNo]': {
            	phone: lang_validPhoneNo,
                required: lang_contactNoRequired,
                maxlength:lang_noMoreThan10
            },
            'addCandidate[alternateNumber]': {
            	phone: lang_validPhoneNo,
                maxlength:lang_noMoreThan10
            },
            'addCandidate[resume]': {
            	required: lang_emailRequired
            },
            'addCandidate[email]' : {
                required: lang_emailRequired,
                email: lang_validEmail,
                maxlength: lang_noMoreThan100
                
            },
            'addCandidate[alternateEmail]' : {
                email: lang_validEmail,
                maxlength: lang_noMoreThan100
                
            },

            'addCandidate[keyWords]': {
                maxlength:lang_noMoreThan250
            },
            'addCandidate[educationDetailDegree]' : {
                maxlength: lang_noMoreThan10
            },
            
            'addCandidate[educationDetailPerc]' : {
                maxlength: lang_noMoreThan10,
                number:lang_validNo
            },
            
            'addCandidate[educationDetailSpec]' : {
                maxlength: lang_noMoreThan100
            },
            
            'addCandidate[comment]' :{
                maxlength:lang_noMoreThan250
            },
            'addCandidate[appliedDate]' : {
                valid_date: lang_validDateMsg,
                date_range_comp:lang_dateValidation
            },
            'addCandidate[currentCtc]' : {
                maxlength: lang_tooLargeInput,
                number:lang_validNo
            },
            'addCandidate[expectedCtc]' : {
                maxlength: lang_tooLargeInput,
                number:lang_validNo
            },
            'addCandidate[noticePeriod]' : {
                maxlength:lang_noMoreThan4,
                number:lang_validNo
            },
             'addCandidate[originalLocation]' : {
                 maxlength:lang_noMoreThan250
             },
             'addCandidate[expectedDoj]': {
            	 valid_date: lang_validDateMsg
            },
            'addCandidate[visaStatus]' : {
            	maxlength: lang_noMoreThan100
            },
            'addCandidate[vacancy]' : {
                required:lang_vacancyRequired
            },
            'addCandidate[totalExperience]' : {
            	number:lang_validNo,
            	maxlength: lang_noMoreThan4
            },
            'addCandidate[relevantExperience]' : {
            	number:lang_validNo,
            	maxlength: lang_noMoreThan4
            },
            'addCandidate[educationGap]' : {
            	number:lang_validNo,
            	maxlength: lang_noMoreThan4
            },
            'addCandidate[workGap]' : {
            	digits:lang_validNo,
            	maxlength: lang_noMoreThan3
            },
             'addCandidate[keySkills]' : {
              	 maxlength: lang_noMoreThan200
            },
            'addCandidate[currentCompany]' : {
                  maxlength:lang_noMoreThan50
            },
            'addCandidate[designation]' : {
            	 maxlength: lang_tooLargeInput
             },
             
           'addCandidate[referralName]' : {
                 required:lang_firstNameRequired
            },

            'addCandidate[preferredLocation]' : {
                maxlength:lang_noMoreThan200
            },
            'addCandidate[communicationSkills]' : {
               maxlength:lang_noMoreThan200
            }
        }

    });
    
    vacancyId = $('#addCandidate_vacancy').val();
    if(candidateStatus != activeStatus) {
        $("#btnSave").attr('disabled', 'disabled');
    }
    
    $('a.link').click(function(e)
    		{
    		    e.preventDefault();
    });

	//Auto complete
	$("#addCandidate_referralName").autocomplete(employees, {
        formatItem: function(item) {
            return item.name;
        },
        matchContains:true
    }).result(function(event, item) {
        $("#addCandidate_referralId").val(item.id);
    });
    
    $(".addText").live('click', function(){
       
        if($("#btnSave").attr('value') == lang_edit){
        
        }else{
            if((allowedVacancylist.length -1) > nextId){
                buildVacancyDrpDwn("", "show allowed vacancies", true);
                newId = /\d+(?:\.\d+)?/.exec(this.id);
                $("#removeButton"+newId).css("padding-left", "195px");
                $("#addButton"+newId).hide();
                if((allowedVacancylist.length -1) <= nextId){
                    $("#addButton"+(nextId-1)).hide();
                    $("#removeButton"+(nextId-1)).css("padding-left", "195px");
                }
            }
        }
    });

    $('.removeText').live('click', function(){

        result = /\d+(?:\.\d+)?/.exec(this.id);
        if(vacancyString.trim() != "" && result < vacancyList.length){
            if($("#btnSave").attr('value') == lang_edit){
            } else{
                $('#deleteConfirmation').modal();
            }
        }
        else{
            $('#jobDropDown'+result).remove();
            validate();
            $("#addButton"+($('.vacancyDrop').length-1)).show();
            $("#removeButton"+($('.vacancyDrop').length-1)).css("padding-left", "128px");
            if(result == $('.vacancyDrop').length-1){
                $("#addButton"+(nextId-1)).show();
                $("#removeButton"+(nextId-1)).css("padding-left", "128px");
            }
            nextId--;
        }
    });

    $('#btnSave').click(function() {
            var vFlag=validateMultipleVacancy();
            if($("#frmAddCandidate").valid() && vFlag) {
                $('#addCandidate_keyWords.inputFormatHint').val('');
                getVacancy();
                if(candidateId != "") {
                    if($('#addCandidate_vacancy').val() != vacancyId && vacancyId != "") {
                        $('#deleteConfirmationForSave').modal();
                    } else {
                    	$('#btnSave').attr('value',"Processing..");
                		$('#btnSave').attr('disabled','disabled');
                        $('form#frmAddCandidate').submit();
                    }
                } else {
                	$('#btnSave').attr('value',"Processing..");
            		$('#btnSave').attr('disabled','disabled');
                    $('form#frmAddCandidate').submit();
                }
           
        }
        if(!vFlag) {
           $("#validate_0").css("display","block");
        }
        

    });
   
    $("input.fileEditOptions").click(function () {
        if(attachment != "" && !$('#addCandidate_resumeUpdate_3').attr("checked")){
            $('#addCandidate_resume').val("");
        }
        if ($('#addCandidate_resumeUpdate_3').attr("checked")) {
            $('#fileUploadSection').show();
        } else {
            $('#fileUploadSection').hide();
        }
    });

    if ($("#addCandidate_keyWords").val() == '') {
        $("#addCandidate_keyWords").val(lang_commaSeparated)
        .addClass("inputFormatHint");
    }

    $("#addCandidate_keyWords").one('focus', function() {

        if ($(this).hasClass("inputFormatHint")) {
            $(this).val("");
            $(this).removeClass("inputFormatHint");
        }
    });
    
    if(candidateId != ""){
    	 $('#addCandidateHeading').text(lang_editCandidateTitle);
          $('.actionDrpDown').removeAttr("disabled");
          $(".vacancyDrop").each(function(){
              if($.inArray($(this).attr('id'), toDisable) > -1){
                  $(this).attr('disabled', 'disabled');
              }
          });
          $('#radio').show();
          $('#addCandidate_resumeUpdate_1').attr('checked', 'checked');
          $('#fileUploadSection').hide();
    } 

    $('.actionDrpDown').change(function(){
        var id = $(this).attr('id');
        var idList = id.split("_")
        var candidateVacancyId = idList[1];
        var selectedAction = $(this).val();
        var url = changeStatusUrl;
        if(selectedAction == interviewAction || selectedAction == interviewAction2){            
            url = interviewUrl;
        }
        if(selectedAction == removeAction){
            url = interviewUrl;
        }
        window.location.replace(url+'?candidateVacancyId='+candidateVacancyId+'&selectedAction='+selectedAction);
    });

    $('#btnBack').click(function(){
        if($("#btnBack").attr('value') == lang_cancel){
            window.location.replace(cancelBtnUrl+'?id='+candidateId);
        }else{
            window.location.replace(backBtnUrl+'?candidateId='+candidateId);
        }
    });

    $('#addCandidate_vacancy').change(function(){
    	if(vacList!=null) {
    		for(var i=0; i<vacList.length; i++) {
    			if(vacList[i]['vacId']==$(this).val())
    				if(vacList[i]['flagForResume']==1) {
    					$('.microresume').show();
    					$('#hdnFlag').val("show");
    				} else {
    					$('.microresume').hide();
    					$('#hdnFlag').val("hide");
    				}
    		}
    	}
    		$('#actionPane').hide();
    		if( $('#addCandidate_vacancy').val() == vacancyId) {
    			$('#actionPane').show();
    		}
    });

    $('#dialogSaveButton').click(function() {
        $('form#frmAddCandidate').submit();
    });
    
    $('#dialogCancelButton').click(function() {
        $('#addCandidate_vacancy').val(vacancyId);
        $('#actionPane').show();
    });

    $('.vacancyDrop').change(function(){
        toRemove = /\d+(?:\.\d+)?/.exec(this.id)
        $("#"+toRemove).hide();
    });
    
    $('#generateShortResume').click(function() {
        var resumeStr = '';
        	resumeStr = "Loc- "+$('#addCandidate_originalLocation').val()+", "+$('#addCandidate_educationDetailDegree').val()+" in "+$('#addCandidate_educationDetailSpec').val()+", Total Exp.- "+$('#addCandidate_totalExperience').val()+" Yrs, Relv. Exp.- "+$('#addCandidate_relevantExperience').val()+" Yrs.\n"+
			    "Skills- "+$('#addCandidate_keySkills').val()+"\n"+
				"Project details- "+$('#addCandidate_projectDetails').val()+"\n"+ 
				"Current CTC- "+$('#addCandidate_currentCtc').val()+" LPA, Exp CTC- "+$('#addCandidate_expectedCtc').val()+" LPA, N.P.- "+$('#addCandidate_noticePeriod').val()+" Days \n";
        	$('#addCandidate_microResume').text(resumeStr);
    });
    
    if(vacList!=null) {
		for(var i=0; i<vacList.length; i++) {
			if(vacList[i]['vacId']==vacancyId)
				if(vacList[i]['flagForResume']==1) {
					$('.microresume').show();
					$('#hdnFlag').val("show");
				} else {
					$('.microresume').hide();
					$('#hdnFlag').val("hide");
				}
		}
	}
    
});

function buildVacancyDrpDwn(vacancyId, mode, removeBtn) {
    if(nextId < 5){
        var newjobDropDown = $(document.createElement('div')).attr("id", 'jobDropDown' + nextId);
        $('#jobDropDown' + nextId).addClass('jobDropDown');
        htmlTxt =  '<label><?php echo __(Job Vacancy); ?></label>' +
            '<select  id="jobDropDown' + nextId +'"'+' onchange="validate()"'+' class="vacancyDrop"'+'>'+buildVacancyList(vacancyId, mode)+'</select>'+
            '<span '+'class="addText"'+ 'id="addButton'+nextId+'">'+'Add another'+'</span>'
        if(removeBtn){
            htmlTxt += '<span '+'class="removeText"'+ 'id="removeButton'+nextId+'">'+lang_remove+'</span>'
        }else{
            toDisable[item] = "jobDropDown"+nextId;
        }
        newjobDropDown.after().html(htmlTxt);
        nextId++;
        newjobDropDown.appendTo("#textBoxesGroup");
    }

}

function buildVacancyList(vacancyId, mode){

    var listArray = new Array();
    if(mode == "show all vacancies"){
        listArray = list;
    }
    if(mode == "show allowed vacancies"){
        listArray = allowedVacancylist;
    }
    if(mode == "show with closed vacancies"){
        listArray = allowedVacancylistWithClosedVacancies;
    }

    var numOptions = listArray.length;
    var optionHtml = "";
    for (var i = 0; i < numOptions; i++) {

        if(listArray[i].id == vacancyId){
            optionHtml += '<option selected="selected" value="' + listArray[i].id + '">' + listArray[i].name + '</option>';
        }else{
            optionHtml += '<option value="' + listArray[i].id + '">' + listArray[i].name + '</option>';
        }
    }
    return optionHtml;
}

function validate(){
    var flag = validateVacancy();
    if(!flag) {
        $('#btnSave').attr('disabled', 'disabled');
        $('#vacancyError').attr('class', "vacancyErr");
    }
    else{
        $('#btnSave').removeAttr('disabled');
    }

}

function getVacancy() {

    var strID = "";
    
    $('.vacancyDrop').each(function() {
        if(!isEmpty($(this).val())) {
            strID = strID + $(this).val() + "_";
        }
    });
    
    $('#addCandidate_vacancyList').val(strID);

}

function validateVacancy(){

    var flag = true;
    $(".messageBalloon_success").remove();
    $(".messageBalloon_failure").remove()
    $('#vacancyError').removeAttr('class');
    $('#vacancyError').html("");
    
    var errorStyle = "background-color:#FFDFDF;";
    var normalStyle = "background-color:#FFFFFF;";
    var vacancyArray = new Array();
    var errorElements = new Array();
    var index = 0;
    var num = 0;

    $('.vacancyDrop').each(function(){
        element = $(this);
        $(element).attr('style', normalStyle);
        vacancyArray[index] = $(element);
        index++;
    });

    for(var i=0; i<vacancyArray.length; i++){
        var currentElement = vacancyArray[i];
        for(var j=1+i; j<vacancyArray.length; j++){
            if(currentElement.val()!=""){
                if(currentElement.val() == vacancyArray[j].val() ){
                    errorElements[num] = currentElement;
                    errorElements[++num] = vacancyArray[j];
                    num++;
                    $('#vacancyError').html(lang_identical_rows);
                    flag = false;
                }
            }
        }
        for(var k=0; k<errorElements.length; k++){
            errorElements[k].attr('style', errorStyle);
        }
    }
    return flag;
}
/* Added By: Shagupta Faras
 * Added On: 17-07-2014
 * DESC: validation for multiple vacancies
 */
function validateMultipleVacancy()
{
    var element=$('#widContainer select').first();    
    var errorStyle = "validation-error";
    var normalStyle = "background-color:#FFFFFF;";
    var count =$(element).val();
    if(count=='') 
    {
     element.addClass(errorStyle);
     $(element).parent('li').append('<span id="validate_0" generated="true" class="validation-error">Required</span>');              
     return false;
    }
    else
    {
     element.attr('style', normalStyle);
     $(element).parent('li').remove('span');
     return true;
    }
 }
/* For Vacancy */
/* Added By : Shagupta Faras
 * Added On : 17-07-2014
 * DESC: Add more Functionality
 */
$(document).ready(function() {
 //var addedVacancy=<?php  if(empty($form->addedVacancy))echo "null"; else echo $form->addedVacancy;?>;
 //   var actionForm='<?php echo json_encode($actionForm);?>';
                                	 	   
    var i=0;
    var widDiv = $('#widContainer');
    if(candidateId!='') {
    /*$.each(addedVacancy, function(i,item) {
    setWidgets(item.vacancy, item.status, window.i);
    window.i++;
    });*/
    } else {  
    setWidgets('', '', window.i);
    window.i++;
    }
    
    $.fn.serializeObject = function() {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };
    /* Added By : Shagupta Faras
     * Added On: 17-07-2014
     */
 $("#addButton").live('click', function(){
    var widDiv = $('#widContainer');
    var i = $("#widContainer li").length;	
    stringHTML =  '<select class="vacancyDrp" id="addCandidate_vacancy'+i+'" name="addCandidate[vacancy]['+i+']" onSelect="validateWithExistingSelect('+i+');" onChange="getVacancyStatus('+i+');">'+vacancyOptions+'</select>';
    stringHTML += '<span id="validate_'+i+'" class=""></span>';

    stringHTML += '<div id="row_status'+i+'" style="float:left; padding-left:10px;padding-right:10px">';
    stringHTML += '<input type="text" id="addCandidate_status'+i+'" name="addCandidate[status]['+i+']" disabled="disabled" value="" placeholder="Status" />';
    stringHTML += '</div>';         
				
    stringHTML += '<div style="float:left; padding-left:10px;padding-right:10px;padding-top:7px">';
    stringHTML += '<a href="#" class = "linkss" id = "rmvBtn_'+i+'">Remove</>';
    stringHTML += '</div>';
    stringHTML += '</li>';

    stringHTML = '<li style="width:100%" id ="rowBox_'+i+'" style = "display:none"><label >&nbsp;</label>'+ stringHTML;
    $(stringHTML).appendTo(widDiv);
    $('#rowBox_'+i).show('slow');
    $('#validate_'+i).show('slow');
    window.i++;
    return false;
 });
 $('a.links').click(function(e) {
		    e.preventDefault();
 });
 $("div").on('click', 'a.linkss', function(e) {
    e.preventDefault();
    var id = parseInt(this.id.substring(7));
    $('#rowBox_'+id).remove();
 });
 function setWidgets(vacancy , status, i) {
    var widDiv = $('#widContainer');
    if(typeof(vacancy)==='undefined') vacancy = "";
    if(typeof(status)==='undefined') status = "";

    stringHTML =  '<select class="vacancyDrp" id="addCandidate_vacancy'+i+'" name="addCandidate[vacancy]['+i+']" onChange="getVacancyStatus('+i+');">'+vacancyOptions+'</select>';
    stringHTML += '<span id="validate_'+i+'" class=""></span>';

    stringHTML += '<div id="row_status'+i+'" style="float:left; padding-left:10px;padding-right:10px">';
    stringHTML += '<input type="text" id="addCandidate_status'+i+'" name="addCandidate[status]['+i+']" disabled="disabled" value="" placeholder="Status" />';
    stringHTML += '</div>';

    stringHTML += '<div style="float:left; padding-left:10px;padding-right:10px;padding-top:8px">';
    stringHTML += '<a href="#" class = "linkss" id = "rmvBtn_'+i+'">Remove</>';
    stringHTML += '</div>';
    stringHTML += '</li>';

    if($.trim($('#widContainer').html())=='') {
    stringHTML = '<li style="width:100%" id ="rowBox_'+i+'" style = "display:none"><label >Job Vacancy<span class="required">*</span></label>'+ stringHTML;
                } else {
    stringHTML = '<li style="width:100%" id ="rowBox_'+i+'" style = "display:none"><label >&nbsp;</label>'+ stringHTML;
                }
    $(stringHTML).appendTo(widDiv);
    $('#addCandidate_vacancy'+i).val(vacancy);
    $('#addCandidate_status'+i).val(status);
    $('#rowBox_'+i).show('slow');
    $('#validate_'+i).show('slow');
  }
	

});
/* Added By : Shagupta Faras
 * 
 */
function getVacancyStatus(index){
 if(validateWithExistingSelect(index)){
 var vacancy=$("#addCandidate_vacancy"+index).val();
 if(vacList!=null && vacancy!='') {
    for(var i=0; i<vacList.length; i++) {
      
      if(vacList[i]['vacId']==vacancy){
       
            if(vacList[i]['flagForResume']==1) {
             $("#addCandidate_status"+index).val("SCREENING");
             $("#addCandidate_status"+index).attr('disabled',true);
            } else {
            $("#addCandidate_status"+index).val("APPLICATION INITIATED");
            $("#addCandidate_status"+index).attr('disabled',true);
            }
        }
        
     
     }
   }
   else
   {
       $("#addCandidate_status"+index).val("");
   }
 }
}
/* Added By : Shagupta Faras
 * DESC: This validate with previously selected drop down value
 */
function validateWithExistingSelect(index)
{
    var vacancy=$("#addCandidate_vacancy"+index).val();
    var id="addCandidate_vacancy"+index;
    var widDiv = $('#widContainer');
    var elements=$('#widContainer').find("select");
    $.each(elements, function() {
        if($(this).attr('id')!=id && $(this).val()==vacancy && vacancy!='')
        {
            alert("Please select another vacancy, as it has been already selected");
            $("#addCandidate_vacancy"+index).val('');
            $("#addCandidate_status"+index).val("");
            return false;
        }
    });
    return true;     
}
