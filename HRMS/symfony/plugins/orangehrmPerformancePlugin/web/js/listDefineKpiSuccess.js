$(document).ready(function() {
		var selectedoption;
		$('#frmList #recordsPerPage_Limit').val(recordsPerPage);
		$('#frmList #recordsPerPage_LimitBottom').val(recordsPerPage);
		var selectedoption;
		$('#frmList #recordsPerPage_Limit').change(function() {
			selectedoption = $('#frmList #recordsPerPage_Limit').val(); 
			$('#frmList #recordsPerPage_LimitBottom option:contains('+selectedoption+')').attr('selected', 'selected');
			KpiListFormSubmit();
		});
		$('#frmList #recordsPerPage_LimitBottom').change(function() {
			selectedoption = $('#frmList #recordsPerPage_LimitBottom').val(); 
			$('#frmList #recordsPerPage_Limit option:contains('+selectedoption+')').attr('selected', 'selected');
			KpiListFormSubmit();
		});
		function KpiListFormSubmit(){
			if(actionURL.indexOf("recordsPerPage_Limit") >= 0 ){
				actionURL = actionURL.substring(0,actionURL.lastIndexOf("/"));
				myURL = actionURL+"/"+selectedoption;
				$('#frmList').attr('action', myURL);
			}else{
				$('#frmList').attr('action', actionURL);
			}
			$('#frmList').submit();
		}
		// Search Kpi 
        $('#searchBtn').click(function(){
            $('#frmSearch').submit();
        });
        $('#addKpiBut').hide();
        // Add Kpi button
        $('#addKpiBut').click(function(){
            location.href = addKpiURL;
        });
        $('#copyKpiBut').hide();
        // Copy kpi button
        $('#copyKpiBut').click(function(){
            location.href = copyKpiURL;
        });
        $('#deleteKpiBut').hide();
        // Delete KPI 
        $('#deleteKpiBut').click(function(){
            if($('.innercheckbox').is(':checked'))
            {
                $('#frmList').submit();
            }else
            {
					
                showError('messageBalloon_warning',lang_deleteWarningMsg );
            }
        });

        // Validate search form 
        $("#frmSearch").validate({
					
            rules: {
                txtJobTitle: { required: true }
            },
            messages: {
                txtJobTitle: lang_ValidateMsg
            }
        });

        // When Click Main Tick box
        $("#allCheck").click(function() {
            if ($('#allCheck').attr('checked')) {
                $('.innercheckbox').attr('checked', true);
            } else {
                $('.innercheckbox').attr('checked', false);
            }

            toggleDeleteButton();
        });

        $('.innercheckbox').click(function() {
            if(!($(this).attr('checked'))) {
                $('#allCheck').attr('checked', false);
            }
            
            toggleDeleteButton();
        });
        
        toggleDeleteButton();
				
    });
    
    function toggleDeleteButton() {
        $('#deleteKpiBut').attr('disabled', $('.innercheckbox:checked').size() == 0);
    }

    function showError(errorType,message) {
        var html	=	"<div id='"+errorType+"' class='"+errorType+"' >"+message+"</div>";
        $("#errorContainer").html(html);
        $("#errorContainer").show();
    }
    
    function showRatingDesc(element){
    	$('#msgtooltip'+element.id).html('Loading...');
    	 $('#msgtooltip'+element.id).show();
    	fetchRatings(element.id);
    }
    
   
	function fetchRatings(kpiId) {
	    params = 'kpiId=' + kpiId;
	    $.ajax({
	        type: 'GET',
	        url: getKpiUrl,
	        data: params,
	        dataType: 'json',
	        success: function(data) {   
	            var count = data.length;
	            var html = '';
	            var rows = 0;
	            $('#msgtooltip'+kpiId).html('');
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
	                    		rate = "- Unacceptable Performance"
	                    		break;
	                    	case '2':
	                    		rate = "- Satisfactory performance <br />(Needs improvement)"
	                    		break;
	                    	case '3':
	                    		rate = "- Good <br />(Meets expectations)"
	                    		break;
	                    	case '4':
	                    		rate = "- Very Good <br />(Sometimes exceeds expectations)"
	                    		break;
	                    	case '5':
	                    		rate = "- Outstanding"
	                    		break;
	                    }
	                    html = html + '<tr class="' + css + '"><td>'+data[i]['rate']+ ' '+rate+'</td></tr>';
	                }
	                html = html + '</table>';
	            } else {
				   html = "No Rating description found";
	            }
	            $('#msgtooltip'+kpiId).append(html);
		     }
	    });
	}
	
	function hidefetchedRatings() {
		 $('.messages').hide();
	 }
	    
	