$(document).ready(function() {
	$("#uploaded").hide();
    if(candidateId > 0) {
        $(".formInputText").attr('disabled', 'disabled');
        $(".formInput").attr('disabled', 'disabled');
        $(".formDateInput").attr('disabled', 'disabled');
        $(".contactNo").attr('disabled', 'disabled');
        $(".keyWords").attr('disabled', 'disabled');
        $(".formSelect").attr('disabled', 'disabled');
        $("#cvHelp").hide();
        $("#uploaded").show();
        $("#btnSave").hide();
    }	
	var isCollapse = false;
	$("#txtArea").attr('disabled', 'disabled');
	$("#txtArea").hide();

	$('#extend').click(function(){
		if(!isCollapse){
			$("#txtArea").show();
			isCollapse = true;
			$('#extend').text('[-]');
		} else {
			$("#txtArea").hide();
			isCollapse = false;
			$('#extend').text('[+]');
		}
	});
        
	$('#btnSave').click(function() {
           
		if($("#frmAddCandidate").valid()){ 
			$('#addCandidate_vacancyList').val(vacancyId);
			$('#addCandidate_keyWords.inputFormatHint').val('');
			$('form#frmAddCandidate').attr({
				action:linkForApplyVacancy+"?id="+vacancyId
			});                         
                        if(($('#addCandidate_id').val().length!=0) && ($('#duplicate_status').val().length>0)) {                             
                             $('#linkConfirmation').modal();
                             return false;
			}
                        $('#btnSave').attr('value',"Processing..");
        		$('#btnSave').attr('disabled','disabled..');                       
			$('form#frmAddCandidate').submit();
		}
	});
        

    $('#backLink').click(function(){
        window.location.replace(linkForViewJobs);
    });
	if ($("#addCandidate_keyWords").val() == '') {
		$("#addCandidate_keyWords").val(lang_commaSeparated).addClass("inputFormatHint");
	}

	$("#addCandidate_keyWords").one('focus', function() {

		if ($(this).hasClass("inputFormatHint")) {
			$(this).val("");
			$(this).removeClass("inputFormatHint");
		}
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
				maxlength:100

			},
			'addCandidate[alternateEmail]' : {
                email:true,
                maxlength:100,
                onkeyup: 'if_invalid'
            },
            'addCandidate[alternateNumber]': {
            	number: true,
                maxlength:10,
                minlength:10
            },

			'addCandidate[contactNo]': {
				required:true,
				number: true,
				maxlength:10,
                                minlength:10
			},

			'addCandidate[resume]' : {
				required:true
			},

			'addCandidate[keyWords]': {
				maxlength:250
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
            'addCandidate[totalExperience]' : {
            	number:true,
                maxlength:4
            },
            'addCandidate[relevantExperience]' : {
            	number:true,
                maxlength:4
            },
            'addCandidate[currentCtc]' : {
                 number: true,
                 maxlength:30
             },
             'addCandidate[expectedCtc]' : {
                 number: true,
                 maxlength:30
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
             'addCandidate[keySkills]' : {
             	 maxlength:200,
             },
             'addCandidate[currentCompany]' : {
                 maxlength:50,
            },
             'addCandidate[designation]' : {
                 maxlength: 30
            },
            'addCandidate[educationGap]' : {
                 number:true,
                 maxlength: 4
             },
             'addCandidate[workGap]' : {
            	 digits:true,
                 maxlength: 4
             },
             'addCandidate[preferredLocation]' : {
                maxlength:200
            },
            'addCandidate[communicationSkills]' : {
                maxlength:200
             },
            'addCandidate[projectDetails]' : {
                maxlength:10000
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

			
            'addCandidate[email]' : {
				required: lang_emailRequired,
				email: lang_validEmail,
				maxlength: lang_noMoreThan100
			},
			'addCandidate[alternateEmail]' : {
              
                email: lang_validEmail,
                maxlength: lang_noMoreThan100
                
            },
             'addCandidate[alternateNumber]': {
            	phone: lang_validPhoneNo,
                maxlength:lang_noMoreThan10
            },
            
            
            'addCandidate[contactNo]': {
            	required: lang_lastNameRequired,
				phone: lang_validPhoneNo,
				maxlength:lang_noMoreThan10,                                
			},

			'addCandidate[resume]' : {
				required:lang_resumeRequired
			},

			'addCandidate[keyWords]': {
				maxlength:lang_noMoreThan250
			},
			'addCandidate[noticePeriod]' : {
				maxlength:lang_noMoreThan4,
                number:lang_validNo
            },
             'addCandidate[originalLocation]' : {
             	 maxlength: lang_tooLargeInput
             },
             'addCandidate[expectedDoj]': {
            	 valid_date: lang_validDateMsg
            },
            'addCandidate[visaStatus]' : {
            	maxlength: lang_noMoreThan100
            },
            'addCandidate[totalExperience]' : {
            	number:lang_validNo,
            	maxlength: lang_noMoreThan4
            },
            'addCandidate[relevantExperience]' : {
            	number:lang_validNo,
            	maxlength: lang_noMoreThan4
            },
            'addCandidate[currentCtc]' : {
                 maxlength: lang_tooLargeInput,
                 number:lang_validNo
            },
            'addCandidate[expectedCtc]' : {
                 maxlength: lang_tooLargeInput,
                 number:lang_validNo
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
            'addCandidate[keySkills]' : {
                 maxlength: lang_noMoreThan200
            },
            'addCandidate[currentCompany]' : {
                 maxlength:lang_noMoreThan50
            },
            'addCandidate[designation]' : {
                 maxlength: lang_tooLargeInput
            },
             'addCandidate[educationGap]' : {
            	number:lang_validNo,
            	maxlength: lang_noMoreThan4
            },
            'addCandidate[workGap]' : {
            	digits:lang_validNo,
            	maxlength: lang_noMoreThan4
            },
            'addCandidate[preferredLocation]' : {
                maxlength:lang_noMoreThan200
            },
            'addCandidate[communicationSkills]' : {
                maxlength:lang_noMoreThan200
            },
            'addCandidate[projectDetails]' : {
                 maxlength:lang_noMoreThan1000
            }
		}
	});
        
        
        
         $('#dialogLinkBtn').click(function() {
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
    	document.frmAddCandidate.submit();
    });
     
});
