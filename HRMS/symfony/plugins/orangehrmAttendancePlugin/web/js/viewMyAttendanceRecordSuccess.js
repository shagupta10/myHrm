/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function()
{
	$("#recordsTable").hide();
    if(flag){
        var parsedFromDate = $.datepicker.parseDate(datepickerDateFormat, $("#attendance_fromDate").val());
        var parsedToDate = $.datepicker.parseDate(datepickerDateFormat, $("#attendance_toDate").val());
        getRelatedAttendanceRecords(employeeId,actionRecorder,$.datepicker.formatDate("yy-mm-dd", parsedFromDate),$.datepicker.formatDate("yy-mm-dd", parsedToDate));
    }

    $('#showButton').click(function() {
        var isValidDate= validateInputDate();
        $("#reportForm").valid();
        if(isValidDate){
        	var parsedFromDate = $.datepicker.parseDate(datepickerDateFormat, $("#attendance_fromDate").val());
            var parsedToDate = $.datepicker.parseDate(datepickerDateFormat, $("#attendance_toDate").val());
            getRelatedAttendanceRecords(employeeId,actionRecorder,$.datepicker.formatDate("yy-mm-dd", parsedFromDate),$.datepicker.formatDate("yy-mm-dd", parsedToDate));
        }  
    });       
        
    function validateInputDate(){
        errFlag = false;
        $(".messageBalloon_success").remove();
        $('#validationMsg').removeAttr('class');
        $('#validationMsg').html("");
        $("#attendance_fromDate").removeAttr('style');
        $("#attendance_toDate").removeAttr('style');
        var errorStyle = "background-color:#FFDFDF;";
        var date=$("#attendance_fromDate").val();
        var toDate=$("#attendance_toDate").val();
        if(!validateDate(date, datepickerDateFormat)){
            $('#validationMsg').attr('class', "message warning");
            $('#validationMsg').html(errorForInvalidFormat);
            $("#attendance_fromDate").attr('style', errorStyle);
            errFlag = true;
        }
        if(!validateDate(toDate, datepickerDateFormat)){
            $('#validationMsg').attr('class', "message warning");
            $('#validationMsg').html(errorForInvalidFormat);
            $("#attendance_toDate").attr('style', errorStyle);
            errFlag = true;
        }   
        return !errFlag ;
    }

    var validator = $("#reportForm").validate({
        rules: {
            'attendance[dateRange][from]' : {
                required: true, 
                date_range_comp: true
            },
		    'attendance[dateRange][to]' : {
		        required: true, 
		        date_range_comp: true,
		        date_range: function() {
                    return {
                        format:datepickerDateFormat,
                        displayFormat:displayDateFormat,
                        fromDate:$('#attendance_fromDate').val()
                    }
                }
		    }
        },
        messages: {
            'attendance[dateRange][from]' : {
                required: lang_NameRequired,
                date_range_comp: lang_dateValidation
            },
            'attendance[dateRange][to]' : {
                required: lang_NameRequired,
                date_range_comp: lang_dateValidation,
                date_range: lang_dateError
            }
        }
    });
    
    function getRelatedAttendanceRecords(employeeId,actionRecorder,fromDate,toDate){
    $.post(
        linkForGetRecords,
        {
            employeeId: employeeId,
            fromDate: fromDate,
            toDate: toDate,
            actionRecorder:actionRecorder
        },
        
        function(data, textStatus) {
                      
            if( data != ''){
                $("#recordsTable").show();
                $('#recordsTable1').html(data);    
            }
                    
        });
                    
    return false;
        
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
		if(parsedFromDate < parsedToDate){
			valid = true;
		}
	}
	return valid;
});

$.validator.addMethod('date_range_validation', function(value, element, params) {
	var valid = false;
	var fromDate = $.trim(value);
	var toDate = $.trim(currentDate);
	var format = datepickerDateFormat;

	if(fromDate == format || toDate == format || fromDate == "" || toDate =="") {
		valid = true;
	}else{
		var parsedFromDate = $.datepicker.parseDate(format, fromDate);
		var parsedToDate = $.datepicker.parseDate(format, toDate);
		if(parsedFromDate < parsedToDate){
			valid = true;
		}
	}
	return valid;
});

String.prototype.isValidDate = function() {
    var IsoDateRe = new RegExp("^([0-9]{4})-([0-9]{2})-([0-9]{2})$");
    var matches = IsoDateRe.exec(this);
    if (!matches) return false;
  

    var composedDate = new Date(matches[1], (matches[2] - 1), matches[3]);

    return ((composedDate.getMonth() == (matches[2] - 1)) &&
        (composedDate.getDate() == matches[3]) &&
        (composedDate.getFullYear() == matches[1]));

}