$(document).ready(function(){
	if(feedbackCount == 0) {
		$('#submitBtn').attr("disabled", true);
	}
	
	$('a.link').click(function(e){
		e.preventDefault();
		$('#editGoalContainer').show();
		$(this).parents('div#editButton').hide();
		$('#goals').hide();
	});

	$('#btnGoalSave').click(function(){
		var goalText = $('#editGoal').val()
		 $.ajax({
		        type: 'GET',
		        url: saveGoalsAjaxUrl,
		        data: {reviewId : reviewId, goals : goalText },
		        dataType: 'json',
		        success: function(data) {
		        	if(data != 0) {
						$('#editGoal').val(data);
						$('#goalText').html(data);
					}
		        	$('#editGoalContainer').hide();
					$('div#editButton').show();
					$('#goals').show();
			    }
		    });
	});
    
	//When click edit button
    $("#saveBtn").click(function() {
    	
        if(checkSubmit()){
            $('#saveMode').val('save');
            enableAll();
            showBlockUI();
            $('#frmSave').submit();
        }else{
        	setFocusOnError();
        }
    });
    
    //When Submit button click
    // Validations applied for self reviewer 
    $("#submitBtn").click(function() {
    	if(isRated()) {              
         	$('#submitConfirmation').modal();
        }else{
        	setFocusOnError();
        }
    });

    $("#dialogSubmitBtn").click(function(){
    	if(checkSubmit()){
    		showBlockUI();
            $('#saveMode').val('submit');
            $('#frmSave').submit();
        }else{
        	setFocusOnError();
        }
    });

    //When Reject Button click
    $("#rejectBtn").click(function() {
    	$('#rejectConfirmation').modal();
    });

    
    $("#dialogRejectBtn").click(function() {

       enableAll();
       showBlockUI();
        $('#saveMode').val('reject');
        $('#frmSave').submit();
    });

    $("#btnSubmitFeedback").click(function() {
    	var valid = true;          
    	if(!$("#reject_comments").val().trim()){
			 $("#reject_comments").addClass('validation-field');
			 $("#comments-reject").text("Required");
			 $("#btnSubmitFeedback").attr('disabled','disabled');
			 return valid;
        }else{
        	$("#btnSubmitFeedback").removeAttr('disabled');
        	enableAll();
        	showBlockUI();
            $('#saveMode').val('rejectComment');
            $('#frmSave').submit();
        }
    });

    $("#reject_comments").on("keyup",function() {
    	var valid = false;          
    	if(!$("#reject_comments").val().trim()){
			 $("#reject_comments").addClass('validation-field');
			 $("#comments-reject").text("Required");
			 $("#btnSubmitFeedback").attr('disabled','disabled');
			 return false;
        }else{
			 $("#reject_comments").removeClass('validation-field');
			 $("#comments-reject").text("");
			 $("#btnSubmitFeedback").removeAttr('disabled');
			 return true;
        }
    });

    //When Submit button click
    $("#approveBtn").click(function() {
    	enableAll();
    	showBlockUI();
        $('#saveMode').val('approve');
        $('#frmSave').submit();
    });

    // Back button
    $("#backBtn").click(function() {
        location.href = viewReviewUrl;
    });

    $.validator.addMethod("minmax", function(value, element) {

        if($('#validRate').val() == '1' )
            return true;
        else
            return false;
    });
   
    
    $( "input[name^=txtReviewerRate]" ).keyup(function() {
		calculateFinalRatings();
    });
    
    $( "input[name^=txtSelfRate]" ).keyup(function() {
		checkSubmit();
    });
});    

function setFocusOnError(){
 	var errorDiv = $('.validation-field:visible').first();
 	var scrollPos = errorDiv.offset().top;
 	$(window).scrollTop(scrollPos);
}
    
function clearErrors() {
        
    $("span.validation-error").each(function(){
        $(this).empty();
    });
    
    $("input.smallInput").each(function(){
    	$(this).removeClass('validation-field');
        $(this).removeClass('validation-error');
    });
    // Added code to clear all error messages 
     $("textarea#txtSelfComments").each(function(){
    	 $(this).removeClass('validation-field');
        $(this).removeClass('validation-error');
    });
}

//Check submit
function checkSubmit(){
    clearErrors();
    
    var valid = true ;
    
    $("input.smallInput").each(function() {
    	
    	if($.trim(this.value) == 'NA' || $.trim(this.value) == 'na' || $.trim(this.value) == 'N/A' || $.trim(this.value) == 'n/a'){
    		return valid;
    	}
    	
    	if(isNaN($.trim(this.value))) {
        	valid = false;                
            $(this).addClass('validation-field');
            $(this).next('span.validation-error').text(validationError);
            return valid;
        }
    	
        max	=	parseFloat($(this).attr('maxscale'));
        min =   parseFloat($(this).attr('minscale'));
        rate =  parseFloat(this.value) ;
        
        if (this.value != '' && isNaN(rate) ) {
            valid = false;                
            $(this).addClass('validation-field');
            $(this).next('span.validation-error').text(validationError);
        }
        
        if( !isNaN(max) || !isNaN(min)){
            
            if(this.value != '' && isNaN(rate) ){
                valid = false;
                $(this).addClass('validation-field');
                $(this).next('span.validation-error').text(validationError);
            }else if ($.trim(this.value).match(/[0-9]+\.[0-9]+$/) ) {
				valid = false;
                $(this).addClass('validation-field');
                $(this).next('span.validation-error').text(fractionError);
			} else {
                if( (rate > max) || (rate < min) ){
                    valid = false;                        
                    $(this).addClass('validation-field');
                    $(this).next('span.validation-error').text(fractionError);
                }
            }
        }
    });

    return valid ;
}

function finalRatings(){
	calculateFinalRatings();
	event.preventDefault();
}

function calculateFinalRatings(){
	if(checkSubmit()){
		var ratings = 0;
    	var noOfKpis = 0;
    	$('input[name^=txtReviewerRate]').each(function(){
			if( 'N/A' != $.trim(this.value) && 'n/a' != $.trim(this.value) && 'NA' != $.trim(this.value) && 'na' != $.trim(this.value)){
				noOfKpis++;
				if ($.trim(this.value) != '') {
					ratings+= parseFloat(this.value);
				}
        	}    		
    	});
    	
    	var finalRatings = 0;
    	if(ratings != 0 && noOfKpis != 0){
    		finalRatings = ratings/noOfKpis;
    	}
    	document.getElementById('txtfinalRating').value = new Number(finalRatings).toFixed(2);
		document.getElementById('finalRating').value = new Number(finalRatings).toFixed(2);
	}
}

function showRatingDesc(element){
	$('#msgtooltip'+element.id).show();
    $('#msgtooltip'+element.id).html('');
	fetchRatings(element.id);
}
    
   
function fetchRatings(kpiId) {
    params = 'kpiId=' + kpiId;
    $.ajax({
        type: 'GET',
        url: getKpiRatingsUrl,
        data: params,
        dataType: 'json',
        success: function(data) {   
            var count = data.length;
            var html = '';
            var rows = 0;
            $('#msgtooltip'+ kpiId).html('');
            if (count > 0) {
                html = "<table class='table'><tr><th>Rating</th></tr>";
                for (var i = 0; i < count; i++) {
                    var css = "odd";
                    rows++;
                    if (rows % 2) {
                        css = "even";
                    }
                     var rate="";
                     switch (data[i]['rate']){
                    	case '1':
                    		rate = "- Unacceptable Performance";
                    		break;
                    	case '2':
                    		rate = "- Satisfactory performance <br />(Needs improvement)";
                    		break;
                    	case '3':
                    		rate = "- Good <br />(Meets expectations)";
                    		break;
                    	case '4':
                    		rate = "- Very Good <br />(Sometimes exceeds expectations)";
                    		break;
                    	case '5':
                    		rate = "- Outstanding";
                    		break;
                    }
                    html = html + '<tr class="' + css + '"><td>'+data[i]['rate']+ ' '+rate+'</td></tr>';
                }
                html = html + '</table>';
            } else {
			   html = "No Rating description found";
            }
            $('#msgtooltip'+ kpiId).append(html);
	     }
    });
}

function hidefetchedRatings() {
	 $('.messages').hide();
}

function selfGoalRatingDesc(id){
	$('#selfGoalsRating'+id).show();
	$('#selfGoalsRating'+id).html('');
	html = "<table class='table'><tr><th>Rating</th></tr>";
	html = html + "<tr class='odd'><td> 1 - Unacceptable Performance </td></tr>";
	html = html + "<tr class='even'><td>2 - Satisfactory performance <br />(Needs improvement) </td></tr>";
	html = html + "<tr class='odd'><td>3 - Good <br />(Meets expectations) </td></tr>";
	html = html + "<tr class='even'><td>4 - Very Good <br />(Sometimes exceeds expectations) </td></tr>";
	html = html + "<tr class='odd'><td>5 - Outstanding </td></tr>";
	html = html + "</table>";
	$('#selfGoalsRating'+id).append(html);
}

function enableAll(){
	$('.reviwerComment').removeAttr('disabled');
	$('.smallInput').removeAttr('disabled');
	$('.formTextArea').removeAttr('disabled');
}

function showBlockUI(){
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
}
function disableFieldsinTab(id , revId) {
	$('#'+revId+'-'+id+' textarea').attr('disabled','disabled');
    $('#'+revId+'-'+id+' input').attr('disabled','disabled');
}
 /* DESC: To fix HRMS-267
 */  
function isRated() {   
    var valid=true;
    $("input.smallInput").each(function() {

	if((this.id !== 'txtfinalRating') 
        && ($.trim(this.value))=='' && ($.trim(this.value))==0) {
        	valid = false;                
            $(this).addClass('validation-field');
            $(this).next('span.validation-error').text(blankError);                
    }
    });
    // added code to check comments for self reviewer
      if(isSelfReview){
        if(!isCommented()) 
        { valid=false;
        }
    }
    return valid;
}
function isCommented() {
    var valid=true;
    $("textarea#txtSelfComments").each(function() {
        if(($.trim(this.value))=='' && ($.trim(this.value))==0) {
            valid = false;
            $(this).addClass('validation-field');
            $(this).next('span.validation-error').text(blankError);
        }
    });
    return valid;
}
function validateReviewerComment() {

    var flag = true;

    $("textarea.reviwerComment").each(function() {
        if(this.value.length >= 2000 ){
            flag = false;
            $(this).addClass('validation-field');
            $(this).next('span.validation-error').text(lengthExceedMsg1);                    
        }
    });
    
    var mainComment = $('#txtMainComment');
    if (mainComment.val()!= undefined && mainComment.val().length > 250) {
        flag = false;
        mainComment.addClass('validation-field');
        mainComment.next('span.validation-error').text(lengthExceedMsg2);
    }
    return flag;
}