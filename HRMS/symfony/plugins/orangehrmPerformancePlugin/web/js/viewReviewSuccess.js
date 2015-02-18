$(document).ready(function() {
	$.each(disabledReviews, function(i, item) {
	    $("#ohrmList_chkSelectRecord_" + item).attr('disabled', 'disabled');
	    $("#btnEdit-" + item).hide();
	});
    $('.editLink, .saveLink, .cclLink').click(function(e){
        e.preventDefault();
    });
    $('#editReview').attr('disabled', 'disabled');
    $('#approveReviews').attr('disabled', 'disabled');
    $('#deleteReview').attr('disabled','disabled'); 
    if(loggedAdmin || loggedReviewer) {
        /* Auto completion of employees */
        $("#txtEmpName").autocomplete(empdata, {
            formatItem: function(item) {
                return $('<div/>').text(item.name).html();
            },
            formatResult: function(item) {
                return item.name
            }, matchContains:"word"
        }).result(function(event, item) {
            $('#hdnEmpId').val(item.id);
        });
        
        /* Auto completion of reviewers */
        $("#txtReviewerName").autocomplete(empdata, {
            formatItem: function(item) {
                return $('<div/>').text(item.name).html();
            },
            formatResult: function(item) {
                return item.name
            }, matchContains:"word"
        }).result(function(event, item) {
            $('#hdnReviewerId').val(item.id);
        });
        
        /* Auto completion of Projects */
        $("#txtProjectName").autocomplete(projectData, {
            formatItem: function(item) {
                return $('<div/>').text(item.name).html();
            },
            formatResult: function(item) {
                return item.name
            }, matchContains:"word"
        }).result(function(event, item) {
            $('#hdnCustomerId').val(item.id);
        });
	}
    
    /* Clearing auto-fill fields */
    $("#txtEmpName").click(function(){ $(this).attr({ value: '' }); $("#hdnEmpId").attr({ value: '0' }); });
    $("#txtReviewerName").click(function(){ $(this).attr({ value: '' }); $("#hdnReviewerId").attr({ value: '0' }); });
    $("#txtProjectName").click(function(){ $(this).attr({ value: '' }); $("#hdnCustomerId").attr({ value: '0' }); });
    
    
    /* Search button */
    $('#searchButton').click(function(){
    	$('#pageNo').val(1);
    	$('#mode').val('new');
        $('#frmSearch').submit();
        
    });
    // Clear button
    $('#clearBtn').click(function(){
    	$('#pageNo').val(1);
        $('#date_from').val('');
        $('#date_to').val('');
        $('#txtJobTitleCode').val('0');
        $('#txtState').val('0');
        $('#txtSubDivisionId').val('0');
        $('#directReview').val('');
        if(loggedAdmin || loggedReviewer) {
            $('#txtEmpName').val('');
            $('#txtEmpName').attr("placeholder", "Type for hints...");
            $('#hdnEmpId').val('0');
            $('#txtProjectName').val('');
            $('#txtProjectName').attr("placeholder", "Type for hints...");
            $('#hdnCustomerId').val('0');
        }
        if(loggedAdmin) {
            $('#txtReviewerName').val('');
            $('#txtReviewerName').attr("placeholder", "Type for hints...");
            $('#hdnReviewerId').val('0');
        }
        
         $('#frmSearch').submit();
        
    });
    
    /* Add button */    
    $('#addReview').click(function(){
        window.location.href = saveReviewUrl;
    });

    /* Edit button */
    $('#editReview').click(function(){
        var reviews = $(":checkbox[name='chkSelectRow[]']:checked").size();
        
        if (reviews < 1) {
            // message to show click at least one
        } else if (reviews == 1) {
        	var reviewId = $(":checkbox[name='chkSelectRow[]']:checked").val();
            var url = saveReviewUrl + '?reviewId=' + reviewId;
            window.location.href = url;
        } else {
            // message to show click one review
        }
    });
    
    /*Added by sujata*/        
    $("#ohrmList_chkSelectAll").change(function() {
        if($(":checkbox").length == 1) {
            $('#deleteReview').attr('disabled','disabled');
        }
        else {
            if($("#ohrmList_chkSelectAll").is(':checked') && $(":checkbox[name='chkSelectRow[]']:checked") && $(":checkbox[name='chkSelectRow[]']:checked:not(:disabled)")) {
                $('#deleteReview').removeAttr('disabled');
                $('#approveReviews').removeAttr('disabled');
            } else {
                $('#deleteReview').attr('disabled','disabled');
                $('#approveReviews').attr('disabled','disabled');
                $('#editReview').attr('disabled', 'disabled');
            }
        }
    });
    
    $(':checkbox[name="chkSelectRow[]"]').change(function() {
    	if(!($(this).attr('checked'))) {
            $('#ohrmList_chkSelectAll').removeAttr('disabled');
        }
        if($(":checkbox[name='chkSelectRow[]']:checked")) {
        	if($(":checkbox[name='chkSelectRow[]']:checked").size() > 1 || $(":checkbox[name='chkSelectRow[]']:checked").size() == 0) {
	            $('#editReview').attr('disabled', 'disabled');
	        }else{
            	$('#editReview').removeAttr('disabled');
			}
        	if($(":checkbox[name='chkSelectRow[]']:checked").size() == 0) {
        		$('#approveReviews').attr('disabled', 'disabled');
                $('#deleteReview').attr('disabled', 'disabled');
	        }else{
	        	$('#approveReviews').removeAttr('disabled');
	        	$('#deleteReview').removeAttr('disabled');
	        }
        } else {
            $('#approveReviews').attr('disabled', 'disabled');
            $('#editReview').attr('disabled', 'disabled');
            $('#deleteReview').attr('disabled', 'disabled');
        }
    });
    
    /* Delete confirmation controls: Begin */    
    $('#dialogDeleteBtn').click(function() {
   		document.frmList_ohrmListComponent.action = deleteUrl;
        document.frmList_ohrmListComponent.submit();
    });
    /* Delete confirmation controls: End */
    
    $('#exportReviews').click(function(){
        $('#frmList_ohrmListComponent').attr('action', exportReviewsUrl);
        $('#frmList_ohrmListComponent').submit();
    });

    $('#approveReviews').click(function(){
        $('#frmList_ohrmListComponent').attr('action', approveReviewsUrl);
        $('#frmList_ohrmListComponent').submit();
    });
    
    
    
}); // ready():Ends

function autoFill(selector, filler) {
    jQuery.each(empdata, function(index, item){
        if(item.name == $("#" + selector).val()) {
            $("#" + filler).val(item.id);
            return true;
        }
    });
}
    
function autoFillProject(selector, filler) {
    jQuery.each(projectData, function(index, item){
        if(item.name == $("#" + selector).val()) {
            $("#" + filler).val(item.id);
            return true;
        }
    });
}

function showEditablebox(hdnId) {
   	$('div[id^=reviewersListHdn-]').hide();
   	$('div[id^=reviewersList-]').show();
	$('div[id^=previewersListHdn-]').hide();
   	$('div[id^=previewersList-]').show();
   	
   	$('#reviewersList-'+hdnId).hide();
   	$('#reviewersListHdn-'+hdnId).fadeIn("slow");
   	$('#previewersList-'+hdnId).hide();
   	$('#previewersListHdn-'+hdnId).fadeIn("slow");
   //	event.preventDefault();
}

function onClickCancel(hdnId){
	
	$('div[id^=previewersListHdn-]').find('span .errorContainer').removeClass('validation-error').hide();
	$('#reviewersList-'+hdnId).show();
   	$('#reviewersListHdn-'+hdnId).hide();
   	$('#previewersList-'+hdnId).show();
   	$('#previewersListHdn-'+hdnId).hide();
   	//event.preventDefault();
}
function fillAutoFields(autoFields, autoHidden) {
    //this is to make case insensitive
    for(x=0; x < autoFields.length; x++) {
        $("#" + autoHidden[x]).val(0);
        for(i=0; i < empdata.length; i++) {
            var data = empdata[i];
            var fieldValue = $("#" + autoFields[x]).val();
            fieldValue = fieldValue.toLowerCase();
            if((data.name).toLowerCase() == fieldValue) {
                $("#" + autoHidden[x]).val(data.id);
                break;
            }
        }
    }
}