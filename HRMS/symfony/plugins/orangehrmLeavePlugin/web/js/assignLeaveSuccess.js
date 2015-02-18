$(document).ready(function() {
        
    leaveBalanceNegative = false;
    lastLeaveBalance = 0.0;
    
    if (haveLeaveTypes) {
        showTimeControls(false);
        
        // Auto complete
        $("#assignleave_txtEmployee_empName").autocomplete(employees_assignleave_txtEmployee, {
            formatItem: function(item) {
                return $('<div/>').text(item.name).html();
            },
            formatResult: function(item) { 
                return item.name
            }              
            ,
            matchContains:true
        }).result(function(event, item) {
            $('#assignleave_txtEmployee_empId').val(item.id);
            $('#assignleave_txtEmployee_joiningDate').val(item.joiningDate);
            setEmployeeWorkshift(item.id);
            updateLeaveBalance();
        }
        );
    
        updateLeaveBalance();
        
        $('#assignleave_txtFromDate').change(function() {
            fromDateBlur($(this).val());
            updateLeaveBalance();
        });
        
        $('#assignleave_txtToDate').change(function() {
            toDateBlur($(this).val());
            updateLeaveBalance();
        });          
        
        //Show From if same date
        if(trim($("#assignleave_txtFromDate").val()) != displayDateFormat && trim($("#assignleave_txtToDate").val()) != displayDateFormat){
            if( trim($("#assignleave_txtFromDate").val()) == trim($("#assignleave_txtToDate").val()) && trim($("#assignleave_txtFromDate").val()) != '') {
                showTimeControls(true);
            }
        }
        
        // Fetch and display available leave when leave type is changed
        $('#assignleave_txtLeaveType').change(function() {
            updateLeaveBalance();
        });
        
        $("#assignleave_txtFromTime").datepicker({
            onClose: function() {
                $("#assignleave_txtFromTime").valid();
            }
        });     
        
        
        
        //Validation
        $("#frmLeaveApply").validate({
            rules: {
                'assignleave[txtEmployee][empName]':{
                    required: true,
                    validEmployeeName: true,
                    onkeyup: false
                },
                'assignleave[txtLeaveType]':{
                    required: true
                },
                'assignleave[txtFromDate]': {
                    required: true,
                    valid_date: function() {
                        return {
                            required: true,
                            format:datepickerDateFormat,
                            displayFormat:displayDateFormat
                        }
                    }
                },
                'assignleave[txtToDate]': {
                    required: true,
                    valid_date: function() {
                        return {
                            required: true,
                            format:datepickerDateFormat,
                            displayFormat:displayDateFormat
                        }
                    },
                    date_range: function() {
                        return {
                            format:datepickerDateFormat,
                            displayFormat:displayDateFormat,
                            fromDate:$("#assignleave_txtFromDate").val()
                        }
                    }
                },
                'assignleave[txtComment]': {
                    maxlength: 250
                }
            },
            messages: {
                'assignleave[txtEmployee][empName]':{
                    required:lang_Required,
                    validEmployeeName: lang_validEmployee
                },
                'assignleave[txtLeaveType]':{
                    required:lang_Required
                },
                'assignleave[txtFromDate]':{
                    required:lang_invalidDate,
                    valid_date: lang_invalidDate
                },
                'assignleave[txtToDate]':{
                    required:lang_invalidDate,
                    valid_date: lang_invalidDate ,
                    date_range: lang_dateError
                },
                'assignleave[txtComment]':{
                    maxlength: lang_CommentLengthExceeded
                }
            },                  
        });
        
        $.validator.addMethod("validTotalTime", function(value, element) {
            var valid = true;
            var fromdate = $('#assignleave_txtFromDate').val();
            var todate = $('#assignleave_txtToDate').val();
            
            if (fromdate == todate) {
                             
                if (value == '') {
                    valid = false;
                }
            }
            
            return valid;
        });
        
        $.validator.addMethod("validWorkShift", function(value, element) {
            var valid = true;
            var fromdate = $('#assignleave_txtFromDate').val();
            var todate = $('#assignleave_txtToDate').val();
            
            if (fromdate == todate) {
                var totalTime = getTotalTime();
                var workShift = $('#assignleave_txtEmpWorkShift').val();
                if (parseFloat(totalTime) > parseFloat(workShift)) {
                    valid = false;
                }

            }
            
            return valid;            
        });
        
        $.validator.addMethod("validToTime", function(value, element) {
            var valid = true;
            
            var fromdate = $('#assignleave_txtFromDate').val();
            var todate = $('#assignleave_txtToDate').val();
            
            if (fromdate == todate) {
                var totalTime = getTotalTime();
                if (parseFloat(totalTime) <= 0) {
                    valid = false;
                }

            }
            
            return valid;  
        });
        
        $.validator.addMethod("validEmployeeName", function(value, element) { 
            return employeeAutoFill('assignleave_txtEmployee_empName', 'assignleave_txtEmployee_empId', employees_assignleave_txtEmployee);  
            

        });
        
       $("#assignleave_txtEmployee_empName").result(function(event, item) {
            $("#assignleave_txtEmployee_empName").valid();
        });
        
        $('#confirmOkButton').click(function(event) {
            $("#frmLeaveApply").get(0).submit();
        });
        
        //Click Submit button
       $('#assignBtn').click(function(event) {
            event.preventDefault();
            if($('#assignleave_txtFromDate').val() == displayDateFormat ){
                $('#assignleave_txtFromDate').val("");
            }
            if($('#assignleave_txtToDate').val() == displayDateFormat ){
                $('#assignleave_txtToDate').val("");
            }
            $('#frmLeaveApply').submit();
        });
        
        $("#assignleave_txtEmployee_empName").change(function(){
            autoFill('assignleave_txtEmployee_empName', 'assignleave_txtEmployee_empId', employees_assignleave_txtEmployee);
            updateLeaveBalance();
        });
        
        function autoFill(selector, filler, data) {
            $("#" + filler).val("");
            $.each(data, function(index, item){
                if(item.name.toLowerCase() == $("#" + selector).val().toLowerCase()) {
                    $("#" + filler).val(item.id);
                    return true;
                }
            });
        }    
    }
});
    
function updateLeaveBalance() {
    var leaveType = $('#assignleave_txtLeaveType').val();
    var empId = $('#assignleave_txtEmployee_empId').val();
    var startDate = $('#assignleave_txtFromDate').val();
    var endDate =  $('#assignleave_txtToDate').val();
    $('#assignleave_leaveBalance').text('--');
    $('#leaveBalance_details_link').remove();
    var joiningDateStr = $('#assignleave_txtEmployee_joiningDate').val();
    var currentDate = new Date();        
    var startOfFinancialYear = new Date(currentDate.getFullYear(),03,01);
    var endOfFinancialYear = new Date(currentDate.getFullYear()+1,02,31);
    
    var joiningDateValue = joiningDateStr.split('-');
    var joiningDate = new Date(joiningDateValue[0],(joiningDateValue[1]-1),joiningDateValue[2]);
    
    $('#popup_emp_name').text($('#assignleave_txtEmployee_empName').val());
    $('#popup_leave_type').text($('#assignleave_txtLeaveType option:selected').text()); 
    $('#multiperiod_emp_name').text($('#assignleave_txtEmployee_empName').val());
    $('#multiperiod_leave_type').text($('#assignleave_txtLeaveType option:selected').text());

    if (leaveType != "" || empId != "") {
        $('#assignleave_leaveBalance').text('').addClass('loading_message');   
        $('#assignBtn').attr("disabled", "disabled");
        $.ajax({
            type: 'GET',
            url: leaveBalanceUrl,
            data: 'leaveType=' + leaveType+'&empNumber=' + empId + '&startDate=' + startDate + '&endDate=' + endDate,
            dataType: 'json',
            success: function(data) {
                //Added by sujata
                if((joiningDate >= startOfFinancialYear) && (joiningDate <= endOfFinancialYear)){                              
                    if(joiningDate.getFullYear() == startOfFinancialYear.getFullYear()){
                        var diff = currentDate.getMonth() - joiningDate.getMonth();    
                    }else{
                        var diff = (12 - joiningDate.getMonth()) + currentDate.getMonth();
                    }                         
                    var elapsedMonth = (diff > 0)? diff : 0; 
                }else{  
                    if(currentDate.getMonth() > startOfFinancialYear.getMonth() ){
                        var elapsedMonth = currentDate.getMonth() - 3;
                    }else if(currentDate.getMonth() < startOfFinancialYear.getMonth() ){
                        var elapsedMonth = 12 - ( 3 - currentDate.getMonth());
                    }else{
                        var elapsedMonth = 0;
                    }                            
                }
                if (data.multiperiod == true) {
                    
                    var leavePeriods = data.data;
                    var leavePeriodCount = leavePeriods.length;
                    
                    var linkTxt = data.negative ? lang_BalanceNotSufficient : lang_details;
                    leaveBalanceNegative = data.negative;
                    
                    var balanceTxt = leavePeriodCount == 1 ? leavePeriods[0].balance.balance.toFixed(2) : '';
                    var linkCss = data.negative ? ' class="error" ' : "";
                    
                    if(leaveType != 2){
                    	$('#assignleave_leaveBalance').text(balanceTxt)
                        .append('<a href="#multiperiod_balance" data-toggle="modal" id="leaveBalance_details_link"' + linkCss + '>' + 
                            linkTxt + '</a>');
                      }
                    else{
                    	var wfhBal = leavePeriods[0].balance.WFHBalForMonth;   
                    	$('#assignleave_leaveBalance').text(balanceTxt)
                        .append('<a href="#multiperiod_balance" data-toggle="modal" id="leaveBalance_details_link"' + linkCss + '>' + 
                            linkTxt + '</a> WFH Balance For Current Month :'+wfhBal);   
                    	$('div.head').empty();                	
                    	$('div.head').append('<div class="message warning" id="divMessageBar" generated="true">'+ 'Work From Home Balance for Current Month : '+ 
                    			wfhBal + "  (LIMIT : " + whfMonthlyLimit+" Days Per Month)."+
                    	        "<a class='messageCloseButton' href='#'>"+'CLOSE'+"</a>" +  '</div>');
                      }

                    var html = '';
                    
                    var rows = 0;
                    for (var i = 0; i < leavePeriodCount; i++) {
                        var leavePeriod = leavePeriods[i];
                        var days = leavePeriod['days'];
                        var leavePeriodFirstRow = true;                        

                        for (var leaveDate in days) {
                            if (days.hasOwnProperty(leaveDate)) {
                                var leaveDateDetails = days[leaveDate];
                                
                                rows++;                        
                                var css = rows % 2 ? "even" : "odd";                                
                                
                                var thisLeavePeriod = leavePeriod['period'];
                                var leavePeriodTxt = '';
                                var leavePeriodInitialBalance = '';
                                
                                if (leavePeriodFirstRow) {
                                    leavePeriodTxt = thisLeavePeriod[0] + ' - ' + thisLeavePeriod[1];
                                    leavePeriodInitialBalance = leavePeriod.balance.balance.toFixed(2);
                                    leavePeriodFirstRow = false;                                    
                                }
                                
                                var balanceValue = leaveDateDetails.balance === false ? leaveDateDetails.desc : leaveDateDetails.balance.toFixed(2);
                                
                                html += '<tr class="' + css + '"><td>' + leavePeriodTxt + '</td><td class="right">' + leavePeriodInitialBalance +
                                    '</td><td>' + leaveDate + '</td><td class="right">' + balanceValue + '</td></tr>';                                
                            }
                        }                    
                        
                        $('div#multiperiod_balance table.table tbody').html('').append(html);
                    }
                    
                } else {
                    var balance = data.balance;
                    var asAtDate = data.asAtDate;
                    var balanceDays = balance.balance;
                    if(leaveType == 2){ 
                        if(elapsedMonth > 0 ){
                            var leaves = elapsedMonth * 3;
                            elapsedDays =(leaves > balance.used)? leaves - balance.used : balance.used - leaves;
                        }    
                        balanceDays =(balanceDays > elapsedDays)? balanceDays - elapsedDays:0;        
                        $('#divElapsed').show();
                        $('#balance_elapsed').text(Number(elapsedDays).toFixed(2));
                        $('#balance_total').text(balanceDays.toFixed(2));
                    }else{
                        $('#divElapsed').hide(); 
                        $('#balance_total').text(balanceDays.toFixed(2));
                    }
                    if(leaveType != 2){
                    	$('#assignleave_leaveBalance').text(balanceDays.toFixed(2))
                        .append('<a href="#balance_details" data-toggle="modal" id="leaveBalance_details_link">' + 
                            lang_details+'</a>');
                       }else{
                    	var wfhBal = balance.WFHBalForMonth;
                    	$('#assignleave_leaveBalance').text(balanceDays.toFixed(2))
                         .append('<a href="#balance_details" data-toggle="modal" id="leaveBalance_details_link">' + 
                            lang_details+'</a>  WFH Balance For Current Month :'+wfhBal);
                    	$('div.head').empty();
                    	$('div.head').append('<div class="message warning" id="divMessageBar" generated="true">'+ 'Work From Home Balance for Current Month : '+ 
                    			wfhBal + "  (LIMIT : " + whfMonthlyLimit+" Days Per Month)."+
                    	        "<a class='messageCloseButton' href='#'>"+'CLOSE'+"</a>" +  '</div>');
                       }
                    $('#balance_as_of').text(asAtDate);
                    $('#balance_entitled').text(Number(balance.entitled).toFixed(2));
                    $('#balance_taken').text(Number(balance.taken).toFixed(2));
                    $('#balance_scheduled').text(Number(balance.scheduled).toFixed(2));
                    $('#balance_pending').text(Number(balance.pending).toFixed(2));
                    $('#balance_adjustment').text(Number(balance.adjustment).toFixed(2));
                    if(Number(balance.adjustment) == 0 ){
                        $('#container-adjustment').hide();
                    }
                    
                    leaveBalanceNegative = false;
                    lastLeaveBalance = balanceDays;                    
                }
                
                $('#assignleave_leaveBalance').removeClass('loading_message');   
                $('#assignBtn').removeAttr("disabled");
            }
        });
    }
}
        
function showTimeControls(show) {
        
    var timeControlIds = ['assignleave_leaveDuration'];
        
    $.each(timeControlIds, function(index, value) {
            
        if (show) {
            $('#' + value).parent('li').show();
        } else {
            $('#' + value).parent('li').hide();
        }
    });
}
    
function showTimepaneFromDate(theDate, datepickerDateFormat){
    var Todate = trim($("#assignleave_txtToDate").val());
    if(Todate == datepickerDateFormat) {
        $("#assignleave_txtFromDate").val(theDate);
        $("#assignleave_txtToDate").val(theDate);
    } else{
        showTimeControls((Todate == theDate));
    }
    $("#assignleave_txtFromDate").valid();
    $("#assignleave_txtToDate").valid();
}
    
function showTimepaneToDate(theDate){
    var fromDate    =    trim($("#assignleave_txtFromDate").val());
        
    showTimeControls((fromDate == theDate));
        
    $("#assignleave_txtFromDate").valid();
    $("#assignleave_txtToDate").valid();
}
    

    
function getTotalTime() {
    var total = 0;
    var difference = $('#assignleave_leaveDuration').val();
    var floatDeference    =    parseFloat(difference/3600000) ;
    total = Math.round(floatDeference*Math.pow(10,2))/Math.pow(10,2);
        
    return total;        
}
    
function fromDateBlur(date) {
    var singleDayLeaveRequest = false;
    var fromDateValue = trim(date);
    if (fromDateValue != displayDateFormat && fromDateValue != "") {
        var toDateValue = trim($("#assignleave_txtToDate").val());
        if (validateDate(fromDateValue, datepickerDateFormat)) {
            if (fromDateValue == toDateValue) {
                singleDayLeaveRequest = true;
            }

            if (!validateDate(toDateValue, datepickerDateFormat)) {
                $('#assignleave_txtToDate').val(fromDateValue);
                singleDayLeaveRequest = true;
            }
        }
    }

    showTimeControls(singleDayLeaveRequest);
}
    
function toDateBlur(date) {
    var singleDayLeaveRequest = false;
    var toDateValue = trim(date);
    if (toDateValue != displayDateFormat && toDateValue != "") {
        var fromDateValue = trim($("#assignleave_txtFromDate").val());

        if (validateDate(fromDateValue, datepickerDateFormat) && validateDate(toDateValue, datepickerDateFormat)) {
            singleDayLeaveRequest = (fromDateValue == toDateValue);
        }
    }

    showTimeControls(singleDayLeaveRequest);
}
    
function setEmployeeWorkshift(empNumber) {
        
    $.ajax({
        url: "getWorkshiftAjax",
        data: "empNumber="+empNumber,
        dataType: 'json',
        success: function(data){
            $('#assignleave_txtEmpWorkShift').val(data.workshift);
        }
    });
        
}    

function employeeAutoFill(selector, filler, data) {
        $("#" + filler).val("");
        var valid = false;
        $.each(data, function(index, item){
            if(item.name.toLowerCase() == $("#" + selector).val().toLowerCase()) {
                $("#" + filler).val(item.id);
                valid = true;
            }
        });
        return valid;
    }
