$(document).ready(function() { 
		$('#recordsPerPage_Limit').val(lbr_RecordsLimit);
		$('#recordsPerPage_LimitBottom').val(lbr_RecordsLimit);
		$('#frmLeaveBalanceReport #leave_balance_LBR_recordsPer_Page_Limit').val(lbr_RecordsLimit);
        $('a.total').live('click', function(){
            
        });
        var selectedoption;
        $('#recordsPerPage_Limit').change(function() {
        	selectedoption = $('#recordsPerPage_Limit').val();
    		$('#recordsPerPage_LimitBottom option:contains('+selectedoption+')').attr('selected', 'selected');
    		formSubmit();
        });
        $('#recordsPerPage_LimitBottom').change(function() {
        	selectedoption = $('#recordsPerPage_LimitBottom').val();
    		$('#recordsPerPage_Limit option:contains('+selectedoption+')').attr('selected', 'selected');
    		formSubmit();
        });
        function formSubmit(){
        	$('#frmLeaveBalanceReport #leave_balance_LBR_recordsPer_Page_Limit').val(selectedoption);
            $('#frmLeaveBalanceReport').attr('action', currentURL).submit(); 
        }
        $('#report-results table.table thead.fixedHeader tr:first').hide();
        
        $('#viewBtn').click(function() {       
            $('#frmLeaveBalanceReport input.inputFormatHint').val('');
            $('#frmLeaveBalanceReport input.ac_loading').val('');        
            $('#frmLeaveBalanceReport').submit();
        });
        
        $("#leave_balance_report_type").change(function() {          
            toggleReportType();
        });
        
        $('#frmLeaveBalanceReport').validate({
                rules: {
                    'leave_balance[employee][empName]': {
                        required: function(element) {
                            return $("#leave_balance_report_type").val() == employeeReport;
                        },
                        no_default_value: function(element) {
                            return {
                                defaults: $(element).data('typeHint')
                            }
                        }
                    },
                    'leave_balance[leave_type]':{required: function(element) {
                            return $("#leave_balance_report_type").val() == employeeReport;
                        } 
                    },
                    'leave_balance[date][from]': {
                        required: true,
                        valid_date: function() {
                            return {
                                required: true,                                
                                format:datepickerDateFormat,
                                displayFormat:displayDateFormat
                            }
                        }
                    },
                    'leave_balance[date][to]': {
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
                                fromDate:$("#date_from").val()
                            }
                        }
                    }
                    
                },
                messages: {
                    'leave_balance[employee][empName]':{
                        required: lang_required,
                        no_default_value: lang_required
                    },
                    'leave_balance[leave_type]':{
                        required: lang_required
                    },
                    'leave_balance[date][from]':{
                        required: lang_invalidDate,
                        valid_date: lang_invalidDate
                    },
                    'leave_balance[date][to]':{
                        required: lang_invalidDate,
                        valid_date: lang_invalidDate ,
                        date_range: lang_dateError
                    }                  
            }
        });        
    });
