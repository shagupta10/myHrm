$(document).ready(function(){
	 $("#viewMyFeedback_empName").autocomplete(employees, {
	        formatItem: function(item) {
	            return $('<div/>').text(item.name).html();
	        },
	        formatResult: function(item) {
	            return item.name
	        },  
	        matchContains:true
	    }).result(function(event, item) {
	    	$("#viewMyFeedback_empNumber").val(item.id);
	        $("#viewMyFeedback_empName").valid();
	 });

	 $('#btnSrch').click(function(){
		$('#viewMyFeedback_empName.inputFormatHint').val('');
		fromdate = $('#viewMyFeedback_fromDate').val();
		if($('#frmSrchFeedback').valid()) {
			$('form#frmSrchFeedback').submit();
		}
     });

	 $('#btnRst').click(function(){
			$('#viewMyFeedback_empName').val('');
			$('#date_from').val('');
            $('#date_to').val('');
            $('#viewMyFeedback_empNumber').val('');
			$('form#frmSrchFeedback').submit();
	 });

     $(':checkbox[name*="chkSelectRow[]"]').each(function() {
        var id = $(this).attr('id');
        var temp = id.split("_");
        if(($.inArray(temp[2], myFeedbackList)) == -1){
            $(this).attr('disabled', 'disabled');
        }
	 });

	 if ($("#viewMyFeedback_empName").val() == '') {
	        $("#viewMyFeedback_empName").val(lang_typeForHints)
	        .addClass("inputFormatHint");
	    }
	    
     $("#viewMyFeedback_empName").one('focus', function() {
        if ($(this).hasClass("inputFormatHint")) {
            $(this).val("");
            $(this).removeClass("inputFormatHint");
        }
     });

     $("#viewMyFeedback_empName").click(function() {
         if ($(this).hasClass("inputFormatHint")) {
             $(this).val("");
             $(this).removeClass("inputFormatHint");
         }
     });
     /*Added by sujata*/
     $('#btnDelete').attr('disabled','disabled');
     $("#ohrmList_chkSelectAll").click(function() {
         if($(":checkbox").length == 1) {
             $('#btnDelete').attr('disabled','disabled');
         }
         else {
             if($("#ohrmList_chkSelectAll").is(':checked')) {
                 $('#btnDelete').removeAttr('disabled');
             } else {
                 $('#btnDelete').attr('disabled','disabled');
             }
         }
     });
     
     $(':checkbox[name*="chkSelectRow[]"]').click(function() {
         if($(':checkbox[name*="chkSelectRow[]"]').is(':checked')) {
             $('#btnDelete').removeAttr('disabled');
         } else {
             $('#btnDelete').attr('disabled','disabled');
         }
     });
     
     /* Delete confirmation controls: Begin */
     $('#dialogDeleteBtn').click(function() {
    	 document.frmList_ohrmListComponent.action = deleteFeedbackUrl;
         document.frmList_ohrmListComponent.submit();
     });
     /* Delete confirmation controls: End */
var validator = $("#frmSrchFeedback").validate({
	 rules: {
         'viewMyFeedback[empName]' : {
        	 nameValidation: true
         },
      
         'viewMyFeedback[from]' : {
             valid_date: function() {
                 return {
                     format:datepickerDateFormat,
                     required:false,
                     displayFormat:displayDateFormat
                 }
             }
         },
         'viewMyFeedback[to]' : {
             valid_date: function() {
                 return {
                     format:datepickerDateFormat,
                     required:false,
                     displayFormat:displayDateFormat
                 }
             },
             date_range: function() {
                 return {
                     format:datepickerDateFormat,
                     displayFormat:displayDateFormat,
                     fromDate:fromdate
                 }
             }
         }
         
     },
     messages: {
         'viewMyFeedback[empName]' : {
        	 nameValidation: lang_invalidName
         },
     
         'viewMyFeedback[from]' : {
             valid_date: lang_validDateMsg
         },
         'viewMyFeedback[to]' : {
             valid_date: lang_validDateMsg,
             date_range: lang_dateError
         }  
     }
});

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

$.validator.addMethod("nameValidation", function(value, element, params) {
    var temp = false;
    if ($('#viewMyFeedback_empName').hasClass("inputFormatHint")) {
        temp = true
    }

    else if ($('#viewMyFeedback_empName').val() == "") {
        $('#viewMyFeedback_empNumber').val("");
        temp = true;
    }
    else{
        var i;
        if(employeesArray !== null && employeesArray !== undefined){
        	var empCount = employeesArray.length;
	        for (i=0; i < empCount; i++) {
	            canName = $.trim($('#viewMyFeedback_empName').val()).toLowerCase();
	            arrayName = employeesArray[i].name.toLowerCase();
	            if (canName == arrayName) {
	                $('#viewMyFeedback_empNumber').val(employeesArray[i].id);
	                temp = true
	                break;
	            }
	        }
		}
    }
    return temp;
});

$.validator.addMethod("greaterThan", function(value, element, params) {
	if($(params).val()=="") {
		return true;
	}
		    if (!/Invalid|NaN/.test(new Date(value))) {
		        return new Date(value) > new Date($(params).val());
		      }
		      return isNaN(value) && isNaN($(params).val()) || (Number(value) > Number($(params).val())); 
});
});