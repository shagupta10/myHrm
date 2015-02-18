$(document).ready(function() {
    
    if (ldapInstalled == 'true') {
        $("#password_required").hide();
        $("#rePassword_required").hide();
    }    

    $("#chkLogin").attr("checked", true);

    $("#addEmployeeTbl td div:empty").remove();
    $("#addEmployeeTbl td:empty").remove();
    
    $('#photofile').after('<label class="fieldHelpBottom">'+fieldHelpBottom+'</label>');

    if(createUserAccount == 0) {
        //hiding login section by default
        $(".loginSection").hide();
        $("#chkLogin").removeAttr("checked");
    }

    //default edit button behavior
    $("#btnSave").click(function() {
        $("#frmAddEmp").submit();
    });

    $("#chkLogin").click(function() {
        $(".loginSection").hide();

        $("#user_name").val("");
        $("#user_password").val("");
        $("#re_password").val("");
        $("#status").val("Enabled");

        if($("#chkLogin").is(':checked')) {
            $(".loginSection").show();
        }
    });
    
    $.validator.addMethod("uniqueEmail", function(value, element, params) {
		var isUnique = true;
		if(employeeList!=null)
		{
		var employeesCount = employeeList.length;
		employeeEmail = $.trim($('#otherEmail').val()).toLowerCase();
		for (var i=0; i < employeesCount; i++) {
			if(employeeEmail != '') {
				if(employeeList[i].email) {
					email = employeeList[i].email.toLowerCase();
					if (employeeEmail == email) {
						isUnique = false
						break;
					}
				}
			}
		}
		}
		return isUnique;
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
        //form validation
    $("#frmAddEmp").validate({
        rules: {
            'firstName': {required: true, maxlength:100,isAlpha: true },
            'middleName': {maxlength:100 },
            'lastName': {maxlength:100 },
            'addStreetOne': {maxlength:100 },
            'addStreetOne': {maxlength:100 },
            'city': {maxlength:100 },
            'state': {maxlength:100 },
            'zipcode': {maxlength:100 },
            'user_name': { validateLoginName: true, onkeyup: 'if_invalid'},
            'user_password': {validatePassword: true, onkeyup: 'if_invalid'},
            're_password': {validateReCheckPassword: true, onkeyup: 'if_invalid'},
            'status': {validateStatusRequired: true },
            'location': {required: true },
            'otherEmail': {email: true, maxlength: 100, uniqueEmail: true},
            'contactNo': {phone: true, maxlength: 50},
            'dateofjoining': {
           	 valid_date: function() {
                return {
                    format:datepickerDateFormat,
                    required:true,
                    displayFormat:displayDateFormat

                }
            }
           },
            'cmbMarital': {required: true},
            'optGender': {required: true},
            'employeeId': {required: true},
            'jobTitle': {required: true},
            'emp_status': {required: true}
        },
        messages: {
            'firstName': { required: lang_firstNameRequired, isAlpha: lang_validFirstName },
            'middleName': {maxlength:lang_noMoreThan100 },
            'lastName': {maxlength:lang_noMoreThan100 },
            'addStreetOne': {maxlength:lang_noMoreThan100 },
            'addStreetOne': {maxlength:lang_noMoreThan100 },
            'city': {maxlength:lang_noMoreThan100 },
            'state': {maxlength:lang_noMoreThan100 },
            'zipcode': {maxlength:lang_noMoreThan20 },
            'user_name': { validateLoginName: lang_userNameRequired },
            'user_password': {validatePassword: lang_passwordRequired},
            're_password': {validateReCheckPassword: lang_unMatchingPassword},
            'status': {validateStatusRequired: lang_statusRequired },
            'location': {required: lang_locationRequired },
            'otherEmail': {email: lang_validEmail , maxlength: lang_noMoreThan100, uniqueEmail: lang_alreadyExists},
            'contactNo': {phone: lang_validPhoneNo , maxlength: lang_noMoreThan50},
            'dateofjoining': {
           	 	valid_date: lang_validDateMsg,
           	 	required: lang_firstNameRequired
            },
            'cmbMarital': {required: lang_firstNameRequired},
            'optGender': {required: lang_firstNameRequired},
            'employeeId': {required: lang_firstNameRequired},
            'jobTitle': {required: lang_firstNameRequired},
            'emp_status': {required: lang_firstNameRequired}
        }
    });

    $.validator.addMethod("validateLoginName", function(value, element) {
        if($("#chkLogin").is(':checked') && !(ldapInstalled == 'true')) {
            if(value.length < 5) {
                return false;
            }
        } else if ($("#chkLogin").is(':checked') && (ldapInstalled == 'true')) {
            if(value.length < 1) {
                return false;
            }
		}
        return true;
    });

    $.validator.addMethod("validatePassword", function(value, element) {
        if($("#chkLogin").is(':checked') && !(ldapInstalled == 'true')) {
            if(value.length < 4) {
                return false;
            }
        }
        return true;
    });

    $.validator.addMethod("validateReCheckPassword", function(value, element) {
        if($("#chkLogin").is(':checked')) {
            if(value != $("#user_password").val()) {
                return false;
            }
        }
        return true;
    });

    $.validator.addMethod("validateStatusRequired", function(value, element) {
        if($("#chkLogin").is(':checked')) {
            if(value == "") {
                return false;
            }
        }
        return true;
    });

    $("#btnCancel").click(function(){
       navigateUrl("viewEmployeeList");
    });
});