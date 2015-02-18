var countArray = new Array();
$(document).ready(function() {
   var counter = 1;
  
   //First hide all the hiring manager
   for(var i = 1; i <= numberOfHiringManagers; i++){
       $('#hiringManager_'+i).hide();
   }
   
   //apply JS token-input to consultants field
   if(existingConsultants == null) {
	   $("#addJobVacancy_consultants").tokenInput(consultants, {
	       prePopulate: false,
	       preventDuplicates: true,
	       disabled: false,
	       required: false 
	   });
   } else {
	   // prepopolate consultants for vacancy if it has existing consultants
	   $("#addJobVacancy_consultants").tokenInput(consultants, {
	       prePopulate: existingConsultants,
	       preventDuplicates: true,
	       disabled: false,
	       required: false 
	   });
   }
   
   if(existingProjects == null) {
	   $("#addJobVacancy_projects").tokenInput(projectsList, {
	       prePopulate: false,
	       tokenLimit: 1,
	       preventDuplicates: true,
	       disabled: false,
	       required: false 
	   });
   } else {
	   // prepopolate projects for vacancy if it has existing consultants
	   $("#addJobVacancy_projects").tokenInput(projectsList, {
	       prePopulate: existingProjects,
	       tokenLimit: 1,
	       preventDuplicates: true,
	       disabled: false,
	       required: false 
	   });
   }
   
   
  //Auto complete
    $(".formInputHiringManager").autocomplete(hiringManagers, {
        formatItem: function(item) {
            return item.name;
        },
        matchContains:true
    }).result(function(event, item) {
        validateInterviewerNames();
    });
    
    if(vacancyId>0){
        var noOfHiringManagers = $('#addJobVacancy_selectedHiringManagerList').val();
        for(var i=1; i<=noOfHiringManagers; i++){
            $('#hiringManager_'+(i)).show();
        }
        counter = noOfHiringManagers;
    }else{
    	$('#hiringManager_'+(counter)).show();
    }
    
    $("#addButton").live('click', function(){
    	counter++;
        
    	if(counter == 10){
            $("#addButton").hide();
        }     
        
        $('#hiringManager_'+counter).show();
        if ($('#addJobVacancy_hiringManager_'+counter).val() == '' || $('#addJobVacancy_hiringManager_'+counter).val() == lang_typeHint) {
            $('#addJobVacancy_hiringManager_'+counter).addClass("inputFormatHint").val(lang_typeHint);
        }
    });
    
    $('.removeText').live('click', function(){
    	 counter--;
        var result = /\d+(?:\.\d+)?/.exec(this.id);
        $('#hiringManager_'+result).hide();
        $('#addJobVacancy_hiringManager_'+result).val("");
        countArray.push(result);
        if(counter < 10){
            $("#addButton").show();
        }
        validateInterviewerNames();
        $(this).prev().removeClass('error');
        $(this).next().empty();
        $(this).next().hide();
    });
    
       
    $('.formInputHiringManager').each(function(){
        if($(this).parent().css('display') != 'none') {
            if ($(this).val() == '' || $(this).val() == lang_typeHint) {
                $(this).addClass("inputFormatHint").val(lang_typeHint);
            }
        }
    });
   
    $('.formInputHiringManager').one('focus', function() {
        if ($(this).hasClass("inputFormatHint")) {
            $(this).val("");
            $(this).removeClass("inputFormatHint");
        }

    });
    
    
    if($("#btnSave").attr('value') == lang_edit) {
    	
    	$(".formInputHiringManager").attr('disabled', 'disabled');
        $("#addJobVacancy_jobTitle").attr('disabled', 'disabled');
        $("#addJobVacancy_name").attr('disabled', 'disabled');
        $("#addJobVacancy_noOfPositions").attr('disabled', 'disabled');
        $("#addJobVacancy_description").attr('disabled', 'disabled');
        $("#addJobVacancy_experience").attr('disabled', 'disabled');
        $("#addJobVacancy_keySkills").attr('disabled', 'disabled');
        $("#addJobVacancy_goodToHaveSkills").attr('disabled', 'disabled');
        $("#addJobVacancy_status").attr('disabled', 'disabled');
        $("#addJobVacancy_publishedInFeed").attr('disabled', 'disabled');
        $("#addJobVacancy_flagResume").attr('disabled', 'disabled');
        $("#addJobVacancy_consultants").tokenInput("toggleDisabled");
        $("#addJobVacancy_projects").tokenInput("toggleDisabled");
        $("#addJobVacancy_urgent").attr('disabled', 'disabled');
        $(".removeText").hide();
        $("#addButton").hide();
    }
      
    $('#btnSave').click(function() {

        $('#addJobVacancy_vacancyId').val(vacancyId);

        //if user clicks on Edit make all fields editable
        if($("#btnSave").attr('value') == lang_edit) {
        	$(".formInputHiringManager").removeAttr("disabled");
            $("#addJobVacancy_jobTitle").removeAttr("disabled");
            $("#addJobVacancy_name").removeAttr("disabled");
            $("#addJobVacancy_hiringManager").removeAttr("disabled");
            $("#addJobVacancy_hiringManager1").removeAttr("disabled");
            $("#addJobVacancy_noOfPositions").removeAttr("disabled");
            $("#addJobVacancy_description").removeAttr("disabled");
            $("#addJobVacancy_experience").removeAttr("disabled");
            $("#addJobVacancy_keySkills").removeAttr("disabled");
            $("#addJobVacancy_goodToHaveSkills").removeAttr("disabled");
            $("#addJobVacancy_status").removeAttr("disabled");
            $("#addJobVacancy_publishedInFeed").removeAttr("disabled");
            $("#addJobVacancy_flagResume").removeAttr("disabled");
            $("#addJobVacancy_consultants").tokenInput("toggleDisabled");
            $("#addJobVacancy_projects").tokenInput("toggleDisabled");
            $("#addJobVacancy_urgent").removeAttr("disabled");
            $(".removeText").show();
            if(counter != 10){
            	$("#addButton").show();
            }
            $("#btnSave").attr('value', lang_save);
            $("#btnBack").attr('value', lang_cancel);
            return;
        }
        if($("#btnSave").attr('value') == lang_save) {
        	if($("#addJobVacancy_publishedInFeed").is(':checked') && !($("#addJobVacancy_status").is(':checked'))) {
        		alert("Can't publish vacancy while being closed");
        	} else {
        		if(validateInterviewerNames()){
        		if(isValidForm()) {
        			removeTypeHints();
        			validateInterviewers(); 
        			$('form#frmAddJobVacancy').attr({
                    action:linkForAddJobVacancy+"?Id="+vacancyId
                });
                $('#frmAddJobVacancy').submit();
        		}
        	   }
        	}
        }
		
    });

    $('#btnBack').click(function(){
        if($("#btnBack").attr('value') == lang_back) {
            window.location.replace(backBtnUrl+'?vacancyId='+vacancyId);
        }
        if($("#btnBack").attr('value') == lang_cancel) {
            window.location.replace(backCancelUrl+'?Id='+vacancyId);
        }
    });

});
function validateInterviewers(){

    var empCount = employeeList.length;
    var empIdList = new Array();
    var j = 0;
    $('.formInputHiringManager').each(function(){
        element = $(this);
        inputName = $.trim(element.val()).toLowerCase();
        if(inputName != ""){
            var i;
            for (i=0; i < empCount; i++) {
                arrayName = employeeList[i].name.toLowerCase();

                if (inputName == arrayName) {
                    empIdList[j] = employeeList[i].id;
                    j++;
                    break;
                }
            }
        }
    });
    $('#addJobVacancy_selectedHiringManagerList').val(empIdList);
}
function removeTypeHints() {
    $('.formInputHiringManager').each(function(){
        if($(this).val() == lang_typeHint) {
            $(this).val("");
        }
    });
}

function validateInterviewerNames(){
	var flag = true;
	var errorClass = "validation-error";
    var interviewerNameArray = new Array();
    var errorElements = new Array();
    var index = 0;
    var num = 0;

    $('.formInputHiringManager').each(function(){
        element = $(this);
        $(element).removeClass(errorClass);
        var ParantId = $(element).parent('li').attr('id');
        $("#"+ParantId).find('span.'+errorClass).remove();
        if((element.val() != "") && (element.val() != lang_typeHint)){
            interviewerNameArray[index] = $(element);
            index++;
        }
    });

    if(interviewerNameArray.length > 0) {
        for(var i=0; i<interviewerNameArray.length; i++){        
            var currentElement = interviewerNameArray[i];
        
            for(var j=0; j<interviewerNameArray.length; j++){
                if(currentElement.val() == interviewerNameArray[j].val() && currentElement.attr('id') != interviewerNameArray[j].attr('id')){
                    errorElements[num] = currentElement;
                    errorElements[++num] = interviewerNameArray[j];
                    num++;
                    interviewerNameArray[j].after('<span class="validation-error">'+lang_identical_rows+'</span>');
                    flag = false;
                }
            }
        
            for(var k=0; k<errorElements.length; k++){
                errorElements[k].addClass(errorClass);
            }
        }
    }
    return flag;
}

function isValidForm(){
    $.validator.addMethod("hiringManagerNameValidation", function(value, element, params) {
        var temp = false;
        var hmCount = hiringManagersArray.length;

        var i;
        for (i=0; i < hmCount; i++) {
            hmName = $.trim($('#'+element.id).val()).toLowerCase();
            arrayName = hiringManagersArray[i].name.toLowerCase();
            if (hmName == arrayName) {
                $('#addJobVacancy_hiringManagerId').val(hiringManagersArray[i].id);
                temp = true
                break;
            }
        }
        return temp;
    });
  
    $.validator.addMethod("uniqueName", function(value, element, params) {
        var temp = true;
        var currentVacancy;
        var id = parseInt(vacancyId,10);
        var vcCount = vacancyNameList.length;
        for (var j=0; j < vcCount; j++) {
            if(id == vacancyNameList[j].id){
                currentVacancy = j;
            }
        }
        var i;
        vcName = $.trim($('#addJobVacancy_name').val()).toLowerCase();
        for (i=0; i < vcCount; i++) {

            arrayName = vacancyNameList[i].name.toLowerCase();
            if (vcName == arrayName) {
                temp = false
                break;
            }
        }
        if(currentVacancy != null){
            if(vcName == vacancyNameList[currentVacancy].name.toLowerCase()){
                temp = true;
            }
        }
		
        return temp;
    });

    $.validator.addMethod("integer", function(value, element, params) {
        value = $('#addJobVacancy_noOfPositions').val();
        return (value =="" ||(value == parseInt(value, 10)));
    });

    var validator = $("#frmAddJobVacancy").validate({

        rules: {
            'addJobVacancy[jobTitle]' : {
                required:true
            },
            'addJobVacancy[name]' : {
                uniqueName:true,
                required:true
            },
            'addJobVacancy[noOfPositions]' : {
                required:false,
                integer: true,
                min: 0,
                max: 99
            },
            'addJobVacancy[hiringManager_1]' : {
                hiringManagerNameValidation: true
            },
            'addJobVacancy[hiringManager_2]' : {
            	hiringManagerNameValidation: true
            },
            'addJobVacancy[hiringManager_3]' : {
                hiringManagerNameValidation: true
            },
            'addJobVacancy[hiringManager_4]' : {
            	hiringManagerNameValidation: true
            },
            'addJobVacancy[hiringManager_5]' : {
                hiringManagerNameValidation: true
            },
            'addJobVacancy[hiringManager_6]' : {
            	hiringManagerNameValidation: true
            },
            'addJobVacancy[hiringManager_7]' : {
                hiringManagerNameValidation: true
            },
            'addJobVacancy[hiringManager_8]' : {
            	hiringManagerNameValidation: true
            },
            'addJobVacancy[hiringManager_9]' : {
                hiringManagerNameValidation: true
            },
            'addJobVacancy[hiringManager_10]' : {
            	hiringManagerNameValidation: true
            },            
            'addJobVacancy[description]' : {
                maxlength:40000
            },
            'addJobVacancy[experience]' : {
                required:true,
                maxlength:30
            },
            'addJobVacancy[keySkills]' : {
                required:true
            },
            'addJobVacancy[goodToHaveSkills]' : {
                required:true
            }

        },
        messages: {
            'addJobVacancy[jobTitle]' : {
                required:lang_jobTitleRequired
            },
            'addJobVacancy[name]' : {
                uniqueName:lang_nameExistmsg,
                required:lang_vacancyNameRequired
            },
            'addJobVacancy[noOfPositions]' : {
                integer: lang_negativeAmount,
                min: lang_negativeAmount,
                max: lang_tooLargeAmount
            },
            'addJobVacancy[hiringManager_1]' : {
                hiringManagerNameValidation: lang_enterAValidEmployeeName
            },
            'addJobVacancy[hiringManager_2]' : {
            	hiringManagerNameValidation: lang_enterAValidEmployeeName
            },
            'addJobVacancy[hiringManager_3]' : {
                hiringManagerNameValidation: lang_enterAValidEmployeeName
            },
            'addJobVacancy[hiringManager_4]' : {
            	hiringManagerNameValidation: lang_enterAValidEmployeeName
            },
            'addJobVacancy[hiringManager_5]' : {
                hiringManagerNameValidation: lang_enterAValidEmployeeName
            },
            'addJobVacancy[hiringManager_6]' : {
            	hiringManagerNameValidation: lang_enterAValidEmployeeName
            },
            'addJobVacancy[hiringManager_7]' : {
                hiringManagerNameValidation: lang_enterAValidEmployeeName
            },
            'addJobVacancy[hiringManager_8]' : {
            	hiringManagerNameValidation: lang_enterAValidEmployeeName
            },
            'addJobVacancy[hiringManager_9]' : {
                hiringManagerNameValidation: lang_enterAValidEmployeeName
            },
            'addJobVacancy[hiringManager_10]' : {
            	hiringManagerNameValidation: lang_enterAValidEmployeeName
            },
            'addJobVacancy[description]' : {
                maxlength: lang_descriptionLength
            }
        }
    });
    return true;
}