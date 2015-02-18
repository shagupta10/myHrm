// This if statement is used for Chrome 
if (!('contains' in String.prototype)) String.prototype.contains = function (str, startIndex) {
    return -1 !== String.prototype.indexOf.call(this, str, startIndex);
};
function ohrmList_init() {

    $('#ohrmList_chkSelectAll').click(function() {
        
        var valueToSet = false;
        
        if ($(this).attr('checked') == 'checked') {
            valueToSet = true;
        }        
        $('table.table input[id^="ohrmList_chkSelectRecord_"]:not(:disabled)').attr('checked', valueToSet);
    });

    $('table.table input[id^="ohrmList_chkSelectRecord_"]').click(function() {
        var selectorCheckboxes = $('table.table input[id^="ohrmList_chkSelectRecord_"]');
        var isAllChecked = (selectorCheckboxes.size() == selectorCheckboxes.filter(':checked').size());
        $('#ohrmList_chkSelectAll').attr('checked', isAllChecked);
    });
};
//To Make as Generic for Pagination on each module
$('#frmList_ohrmListComponent #recordsPerPage_Limit').val(recordsPerpage);
$('#frmList_ohrmListComponent #recordsPerPage_LimitBottom').val(recordsPerpage);
var selectedoption;
$('#frmList_ohrmListComponent #recordsPerPage_Limit').change(function() {
	selectedoption = $('#frmList_ohrmListComponent #recordsPerPage_Limit').val(); 
	$('#frmList_ohrmListComponent #recordsPerPage_LimitBottom option:contains('+selectedoption+')').attr('selected', 'selected');
	genericPaginationJs();
});

$('#frmList_ohrmListComponent #recordsPerPage_LimitBottom').change(function() {
	selectedoption = $('#frmList_ohrmListComponent #recordsPerPage_LimitBottom').val(); 
	$('#frmList_ohrmListComponent #recordsPerPage_Limit option:contains('+selectedoption+')').attr('selected', 'selected');
	genericPaginationJs();
});

function genericPaginationJs(){
	if(currentURL.contains("leave")){
		if(currentURL.contains("viewMyLeaveList") || currentURL.contains("viewLeaveList")){
			$('#leaveList_recordsPer_Page').val(selectedoption);
			var autoCompleteField = $('#leaveList_txtEmployee_empName');
		    if ((autoCompleteField.val() === lang_typeHint) ||
		        autoCompleteField.hasClass('ac_loading') || 
		        autoCompleteField.hasClass('inputFormatHint')) {
		        $('#leaveList_txtEmployee_empName').val('');
		    }
		    $('#frmList_ohrmListComponent #onChange').val("1");
		    document.getElementById('frmFilterLeave').submit();
		}
		
		if(currentURL.contains("viewHolidayList")){
			$('#holidayList_recordsPer_Page_Limit').val(selectedoption);
			$('#frmList_ohrmListComponent #onChange').val("1");
			$("#frmHolidaySearch").submit();
		}
	}
	
	if(currentURL.contains("pim") && currentURL.contains("viewEmployeeList")){	
    	$('#search_form #empsearch_recordsPer_Page_Limit').val(selectedoption);
    	$('#search_form input.inputFormatHint').val('');
        $('#search_form input.ac_loading').val('');
        $("#empsearch_isSubmitted").val('yes');
        $('#frmList_ohrmListComponent').attr('action',currentURL);       
        $('#frmList_ohrmListComponent #onChange').val("1");
        document.getElementById('search_form').submit();
	}
	
	if(currentURL.contains("pim") && currentURL.contains("viewDirectory")){
		$('#search_form #empDir_recordsPer_Page_Limit').val(selectedoption);
		$('#search_form input.inputFormatHint').val('');
        $('#search_form input.ac_loading').val('');
        $("#empDir_isSubmitted").val('yes');
        document.getElementById('search_form').submit();
	}
	
	if(currentURL.contains("recruitment")){
		if(currentURL.contains("viewMyCandidates")){
			$('#candidateSearch_recordsPer_Page_Limit').val(selectedoption);
			$('#candidateSearch_candidateName.inputFormatHint').val('');
			$('#frmList_ohrmListComponent #onChange').val("1");
			$("#frmSrchCandidates").submit();
		}
		if(currentURL.contains("viewCandidates")){
			$('#frmList_ohrmListComponent #onChange').val("1");
			$("#frmList_ohrmListComponent").submit();
		}
		if(currentURL.contains("viewJobVacancy")){
			$('#vacancySearch_recordsPer_Page_Limit').val(selectedoption);
			$('#frmSrchJobVacancy').submit();
		}
		if(currentURL.contains("viewJobRequests")){
			$('#frmList_ohrmListComponent #onChange').val("1");
			$("#frmList_ohrmListComponent").submit();
		}
	}
	
	if(currentURL.contains("performance")){
		if(currentURL.contains("viewMyFeedback")){
			if(currentURL.contains("recordsPerPage_Limit") ){
				currentURL = currentURL.substring(0,currentURL.lastIndexOf("/"));
				myURL = currentURL+"/"+selectedoption;
				$('#frmList_ohrmListComponent').attr('action', myURL);
			}else{
				$('#frmList_ohrmListComponent').attr('action', currentURL);
			}
			$('#frmList_ohrmListComponent #onChange').val("1");
			$("#frmList_ohrmListComponent").submit();
		}
		if(currentURL.contains("viewReview")){
			$("#frmList_ohrmListComponent").submit();
		}
	}
	if(currentURL.contains("attendance")){
		if(currentURL.contains("viewAttendanceRecord")){
			$('#reportForm #attendance_recordsPerPageLimit').val(selectedoption);
			$('#reportForm').submit();
		}
		
	}
	
	if(currentURL.contains("admin")){
            
               if(currentURL.contains("viewProjects")){
                   if(currentURL.contains("recordsPerPage_Limit") ){
				currentURL = currentURL.substring(0,currentURL.lastIndexOf("/"));
				myURL = currentURL+"/"+selectedoption;
				$('#frmList_ohrmListComponent').attr('action', myURL);
			}else{
				$('#frmList_ohrmListComponent').attr('action', currentURL);
			}
                        $('#frmList_ohrmListComponent #onChange').val("1");
			$("#frmList_ohrmListComponent").submit();                   
		}
		
		if(currentURL.contains("viewSystemUsers")){
			$('#searchSystemUser_recordsPer_PageLimits').val(selectedoption);
			$('#search_form').attr('action', currentURL);
			$('#search_form input.inputFormatHint').val('');
	        document.getElementById('search_form').submit();
		}
		
		if(currentURL.contains("viewJobTitleList") || currentURL.contains("viewPayGrades") || currentURL.contains("employmentStatus") || currentURL.contains("jobCategory") || currentURL.contains("workShift")){
			if(currentURL.contains("recordsPerPage_Limit") ){
				currentURL = currentURL.substring(0,currentURL.lastIndexOf("/"));
				myURL = currentURL+"/"+selectedoption;
				$('#frmList_ohrmListComponent').attr('action', myURL);
			}else{
				$('#frmList_ohrmListComponent').attr('action', currentURL);
			}
			$("#frmList_ohrmListComponent").submit();
		}
	}        	
}
/**
 * Used in pagination links
 * TODO: Rename with a proper method once paging_links_js partial is replaced
 */
function submitPage(pageNumber) {
    var baseUrl = location.href;
    var urlSuffix = '';
    
    if (baseUrl.match(/index\.php\/\w{1,}$/)) {
        baseUrl += '/index/';
    }

    if (baseUrl.match(/pageNo\/\d{1,}/)) {
        baseUrl = baseUrl.replace(/pageNo\/\d{1,}/, 'pageNo/' + pageNumber);
    } else {
        urlSuffix = (baseUrl.match(/\/$/) ? '' : '/') + 'pageNo/' + pageNumber;
    }
    
    location.href = baseUrl + urlSuffix;
}
