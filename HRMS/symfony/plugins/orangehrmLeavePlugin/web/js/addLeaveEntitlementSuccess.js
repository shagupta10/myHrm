    var abortEmployeeLoad = false;
    var matchingCount = false;
    
    function toggleFilters(show) {
        if (show) {
           $('ol#filter li:not(:first)').show();
        } else {
            $('ol#filter li:not(:first)').hide();
        }
    }


    function updateFilterMatches() {

        matchingCount = false;

        var params = '';

        $('ol#filter li:not(:first)').find('input,select').each(function(index, element) {
            var name = $(this).attr('name');
            name = name.replace('entitlements[filters][', '');
            name = name.replace(']', '');
            var value = $(this).val();

            params = params + '&' + name + '=' + value;
        });
        
        $.ajax({
            type: 'GET',
            url: getCountUrl,
            data: params,
            dataType: 'json',
            success: function(data) {
                filterMatchingEmployees = data;
                
                $('span#ajax_count').remove();
                var text = lang_matchesMany.replace('%count%', data);
                if (data == 1) {
                    text = lang_matchesOne;
                } else if (data == 0) {
                    text = lang_matchesNone;
                }

                matchingCount = data;
                $('ol#filter li:first').append('<span id="ajax_count">(' + text + ')</span>');
            }
        });
    }
    
    function fetchEmployees(offset) {
        var params = '';
        
        if (offset == 0) {
            abortEmployeeLoad = false;
            $('div#employee_list').html(''); 
            
            var progress = matchingCount ? '(0 / ' + matchingCount + ')' : '0';
            $('div#employee_loading').html(lang_Loading + '... ' + progress + ' ').show();
        }
        
        $('ol#filter li:not(:first)').find('input,select').each(function(index, element) {
            var name = $(this).attr('name');
            name = name.replace('entitlements[filters][', '');
            name = name.replace(']', '');
            var value = $(this).val();

            params = params + '&' + name + '=' + value;
        });
        params = params + '&offset=' + offset + '&lt=' + $('#entitlements_leave_type').val() + '&fd='+$('#date_from').val()+ '&td='+ $('#date_to').val()+'&ent='+$('#entitlements_entitlement').val();
        $.ajax({
            type: 'GET',
            url: getEmployeeUrl,
            data: params,
            dataType: 'json',
            success: function(results) {                
                var offset = parseInt(results.offset, 10);
                var pageSize = parseInt(results.pageSize, 10);
                var data = results.data;
                var count = data.length;
                var finishedLoading = true;
                
                if (offset == 0) {
                    if (count == 0) {
                        $('div#employee_list').html(lang_NoResultsFound);
                    } else {
                        $('div#employee_list').html("<table class='table'><tr><th>"+lang_employee+"</th><th>"+lang_old_entitlement+"</th><th>"+lang_new_entitlement+"</th></tr></table>");                    
                    }
                }                
                
                if (count > 0) {
                    var rows = $('div#employee_list table tr').length - 1;

                    var html = '';
                    for (var i = 0; i < count; i++) {
                        rows++;                        
                        var css = rows % 2 ? "even" : "odd";

                        var decodedName = $("<div/>").text(data[i][0]).html();
                        var oldValue = parseFloat(data[i][1]);
                        var newValue = parseFloat(data[i][2]);
                        
                        html = html + '<tr class="' + css + '"><td>'+decodedName+'</td><td>'+oldValue.toFixed(2)+'</td><td>'+newValue.toFixed(2)+'</td></tr>';
                    }

                    $('div#employee_list table.table').append(html);
                    
                    if ((count == pageSize)) {
                        finishedLoading = false;
                        
                        if (!abortEmployeeLoad) {                            
                            fetchEmployees(offset + pageSize);
                        }
                    }
                }
                
                if (finishedLoading) {
                    $('div#employee_loading').html('').hide();
                } else {
                    var progress = matchingCount ? '(' + rows + ' / ' + matchingCount + ') ' : rows;
                    $('div#employee_loading').html(lang_Loading + '... ' + progress + ' ');
                }
            }
        });        
    }

    function updateEmployeeList() {        
        fetchEmployees(0);
    }

    function showProgressDialog(message) {
        $('#buildAssignWait').text(message);
        $('#bulkAssignWaitDlg').modal();        
    }
    
    function calculateLeaveBalance123(){
        var joiningDateStr = $('#entitlements_employee_joiningDate').val();
        if(joiningDateStr != ''){
            var currentDate = new Date();

            var startOfFinancialYear = new Date(currentDate.getFullYear(),03,01);
            var endOfFinancialYear = new Date(currentDate.getFullYear()+1,02,31);

            //alert(startOfFinancialYear +" "+ endOfFinancialYear);
            var joiningDateValue = joiningDateStr.split('-');
            var joiningDate = new Date(joiningDateValue[0],(joiningDateValue[1]-1),joiningDateValue[2]);

            if(joiningDate <= startOfFinancialYear){
                startOfFinancialYear = new Date(currentDate.getFullYear()-1,03,01);
                endOfFinancialYear = new Date(currentDate.getFullYear(),02,31);
            }

            var balance = 0;
            var leaveType = $("#entitlements_leave_type").val();

            if((joiningDate >= startOfFinancialYear) && (joiningDate <= endOfFinancialYear)){
                //alert("InRange :"+joiningDate);
                var noOfMonth = 3;
                if(joiningDateValue[0] == startOfFinancialYear.getFullYear()){
                    noOfMonth += (12 - joiningDateValue[1]);
                }else{
                    noOfMonth -= joiningDateValue[1];
                }

                balance = getLeaveBalanceByLeaveType(leaveType,joiningDateValue[2],noOfMonth, true);
            }else{
                balance = getLeaveBalanceByLeaveType(leaveType,null,null, false);
            }

            $('#entitlements_entitlement').val(balance);
        }else{
            var empName = $('#entitlements_employee_empName').val();
            if(empName == '' || empName == 'Type for hints...' ){
                $('#noemployee').modal();
            }else{
                $('#nojoiningdate').modal();
            }
        }
    }

    /*Modified by sujata to seperate code in to getEntitlement().*/
    function calculateLeaveBalance(){
        var joiningDateStr = $('#entitlements_employee_joiningDate').val();
        if(joiningDateStr != ''){
            var balance = getEntitlement();
            $('#entitlements_entitlement').val(balance);
        }else{
            var empName = $('#entitlements_employee_empName').val();
            if(empName == '' || empName == 'Type for hints...' ){
                $('#noemployee').modal();
            }else{
                $('#nojoiningdate').modal();
            }
        }
    }
    /**
     * Get the leave balance by leave type
     * @param leaveType
     * @param joiningDate
     * @param noOfMonth
     * @param isInRange
     */
    function getLeaveBalanceByLeaveType(leaveType,joiningDate,noOfMonth, isInRange){
        var leaveBalance;
        if(leaveType == plDBId){//Paid Leave
            leaveBalance = isInRange ? calculatePaidLeave(joiningDate, noOfMonth) : getLeaveEntitlementDays(leaveType);
        }else if(leaveType == wfhDBId){ //WFH
            leaveBalance = isInRange ? calculateWFHBalance(joiningDate, noOfMonth) : getLeaveEntitlementDays(leaveType);
        }else{
        	leaveBalance = getLeaveEntitlementDays(leaveType);
        }
        return leaveBalance;
    }

    /**
     * Calculate paid leve balance
     * @param joiningDate
     * @param noOfMonth
     * @returns {Number}
     */
    function calculatePaidLeave(joiningDate, noOfMonth){
        if(joiningDate <=15){
            noOfMonth += 1;
        }

        var paidLeaveBalance = noOfMonth * 1.75;
        /*var fraction = paidLeaveBalance - Math.floor(paidLeaveBalance);
        //alert("Balance : " +paidLeaveBalance+" fraction " + fraction +" Ceil "+ Math.ceil(paidLeaveBalance)+" floor "+ Math.floor(paidLeaveBalance))
        if(fraction <= 0.5){
            paidLeaveBalance = Math.floor(paidLeaveBalance);
        }else {
            paidLeaveBalance = Math.ceil(paidLeaveBalance);
        }*/
        return paidLeaveBalance.toFixed(2);
    }

    /**
     * Calculate Work from home balance
     * @param joiningDate
     * @param noOfMonth
     * @returns {Number}
     */
    function calculateWFHBalance(joiningDate, noOfMonth){
        var wfhBalance;
        if(joiningDate <=15){
            noOfMonth += 1;
            wfhBalance = noOfMonth * 3;
        }else{
            wfhBalance = (noOfMonth * 3)+1;
        }

        return wfhBalance.toFixed(2);
    }

    $(document).ready(function() {
        
        if(mode == 'update'){
            $('#filter').hide();
        }

        if ($('#entitlements_filters_bulk_assign').is(':checked')) {
            toggleFilters(true);
            $('#entitlements_employee_empName').parent('li').hide();
            $('#linkCalculateBalance').parent('li').hide();
        } else {
            toggleFilters(false);
        }


        $('#btnSave').click(function() {
            if ($('#entitlements_filters_bulk_assign').is(':checked')) {

                if (filterMatchingEmployees == 0) {
                    $('#noselection').modal();
                } else {
                    var valid = $('#frmLeaveEntitlementAdd').valid();
                    if (valid) {
                        updateEmployeeList();

                        $('#preview').modal();
                    }
                }
            } else {
                if(!($('#entitlements_id').val() > 0)){
                    var valid = $('#frmLeaveEntitlementAdd').valid();
                        if (valid) {   
                            var params = '';

                            params = 'empId='+$('#entitlements_employee_empId').val()+'&lt=' + $('#entitlements_leave_type').val() + '&fd='+$('#date_from').val()+ '&td='+ $('#date_to').val()+'&ent='+$('#entitlements_entitlement').val();

                            $.ajax({
                                type: 'GET',
                                url: getEmployeeEntitlementUrl,
                                data: params,
                                dataType: 'json',
                                success: function(data) {                
                                    if( !isNaN(data[0]) && parseFloat(data[0])!=0 ){

                                        var oldValue = parseFloat(data[0]);
                                        var newValue = parseFloat(data[1]);

                                        $('ol#employee_entitlement_update').html(''); 
                                        var html = '<span>Existing Entitlement value '+ oldValue.toFixed(2)+' will be updated to '+ newValue.toFixed(2) +'</span>'
                                        $('ol#employee_entitlement_update').append(html);
                                        $('#employeeEntitlement').modal();
                                    }else{
                                        var loadingMsg = lang_PleaseWait + '...';
                                        showProgressDialog(loadingMsg);                                            
                                        $('#frmLeaveEntitlementAdd').submit();
                                    }

                                }
                            });

                        }

                    }else{
                        $('#frmLeaveEntitlementAdd').submit();
                    }
            }
        });

        $('#dialogConfirmBtn').click(function() {
            var loadingMsg = lang_BulkAssignPleaseWait.replace('%count%', matchingCount) + '...';
            showProgressDialog(loadingMsg);
            $('#frmLeaveEntitlementAdd').submit();
        });

        $('#bulkAssignCancelBtn').click(function() {
            abortEmployeeLoad = true;
        });

        $('#dialogUpdateEntitlementConfirmBtn').click(function() {
            var loadingMsg = lang_PleaseWait + '...';
            showProgressDialog(loadingMsg);
            $('#frmLeaveEntitlementAdd').submit();
        });

        $('#btnCancel').click(function() {
            window.location.href = listUrl;
        });
 
        $('#entitlements_filters_bulk_assign').click(function(){
            
            if ($('span#ajax_count').length == 0) {
                updateFilterMatches();
            }

            var checked = $(this).is(':checked');
            toggleFilters(checked);
            if (checked) {
                $('#entitlements_employee_empName').parent('li').hide();
                $('#linkCalculateBalance').parent('li').hide();
                $('#entitlements_entitlement').val(' ');
            } else {
                $('#entitlements_employee_empName').parent('li').show();
                $('#linkCalculateBalance').parent('li').show();
                $('span#ajax_count').remove();
            }
        });

        $('ol#filter li:not(:first)').find('input,select').change(function(){
           updateFilterMatches(); 
        });

    $.validator.addMethod("twoDecimals", function(value, element, params) {
        
        var isValid = false;

        var match = value.match(/^\$?([0-9]{1,3},([0-9]{3},)*[0-9]{3}|[0-9]+)(.[0-9][0-9])?$/);
        if(match) {
            isValid = true;
        }
        if (value == ""){
            isValid = true;
        }
        return isValid;
    });

    $.validator.addMethod("checkEntitlement", function(value, element, params) {
        var leaveType = $('#entitlements_leave_type').val();
        var leaveBalance;
        var leaveBalance = getLeaveEntitlementDays(leaveType);
        leaveBalance = parseFloat(parseFloat(leaveBalance).toFixed(2));
        value = parseFloat(parseFloat(value).toFixed(2));
        return (leaveBalance >= value)? true:false;
    });

        $('#frmLeaveEntitlementAdd').validate({
                ignore: [],
                rules: {
                    'entitlements[employee][empName]': {
                        required: function(element) {
                            return !$('#entitlements_filters_bulk_assign').is(':checked');
                        },
                        no_default_value: function(element) {
                            if ($('#entitlements_filters_bulk_assign').is(':checked')) {
                                return false;
                            } else {
                                return {
                                    defaults: $(element).data('typeHint')
                                }
                            }
                        }
                    },
                    'entitlements[leave_type]':{required: true },
                    'entitlements[date][from]': {
                        required: true,
                        valid_date: function() {
                            return {
                                required: true,
                                format:datepickerDateFormat,
                                displayFormat:displayDateFormat
                            }
                        }
                    },
                    'entitlements[date][to]': {
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
                    },
                    'entitlements[entitlement]': {
                        required: true,
                        number: true,
                        twoDecimals: true,
                        checkEntitlement: true,
                        remote: {
                            url: validEntitlemnetUrl,
                            data: {
                                id: $('#entitlements_id').val()
                            }
                        }
                    }

                },
                messages: {
                    'entitlements[employee][empName]':{
                        required:lang_required,
                        no_default_value:lang_required
                    },
                    'entitlements[leave_type]':{
                        required:lang_required
                    },
                    'entitlements[date_from]':{
                        required:lang_invalidDate,
                        valid_date: lang_invalidDate
                    },
                    'entitlements[date_to]':{
                        required:lang_invalidDate,
                        valid_date: lang_invalidDate ,
                        date_range: lang_dateError
                    },
                    'entitlements[entitlement]': {
                        required: lang_required,
                        number: lang_number,
                        remote : lang_valid_entitlement,
                        twoDecimals: lang_number,
                        checkEntitlement: lang_check_entitlement
                    }
            }

        });
        
    });
    /* Added by sujata to get entitlement default value */
    function getEmployeeJoiningDate(empNumber){
        $.ajax({
                type: 'GET',
                url: getEmployeeJoiningDateUrl,
                data: 'empNumber='+empNumber,
                dataType: 'json',
                success: function(data) {
                    $('#entitlements_employee_joiningDate').val(data);
                }
            });
    }
    /*Added by Sujata to call seperate fucntion for getting entitlement depending upon EmployeeName, LeaveType, Onload */
    function getEntitlement(){
        var date_from = $('#date_from').val();
        var date_to = $('#date_to').val();
        var startOfFinancialYear = new Date(date_from);
        var endOfFinancialYear = new Date(date_to);

        var joiningDateStr = $('#entitlements_employee_joiningDate').val();

        var leaveType = $("#entitlements_leave_type").val();
        var balance = 0;
        var currentDate = new Date();
        var joiningDateValue = joiningDateStr.split('-');

        var joiningDate = new Date(joiningDateValue[0],(joiningDateValue[1]-1),joiningDateValue[2]);
        if((joiningDate >= startOfFinancialYear) && (joiningDate <= endOfFinancialYear)){
            var noOfMonth = 3;
            if(joiningDateValue[0] == startOfFinancialYear.getFullYear()){
                noOfMonth += (12 - joiningDateValue[1]);
            }else{
                noOfMonth -= joiningDateValue[1];
            }

            balance = getLeaveBalanceByLeaveType(leaveType,joiningDateValue[2],noOfMonth, true);
        }else{
            balance = getLeaveBalanceByLeaveType(leaveType,null,null, false);
        }
        return balance;
    }

    function getLeaveEntitlementDays(entitlementId){
    	var leaveEntitled = leaveTypeEntitlementMapDB[entitlementId];
    	if(leaveEntitled && entitlementId == wfhDBId){//Return whole year entitlement for WFH i.e. 3 * 12 = 36
            leaveEntitled = leaveEntitled* 12;
           }
    	return leaveEntitled;
    }
