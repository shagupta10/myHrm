 $(document).ready(function() {
        //Auto complete
		$("#empDir_employee_name").autocomplete(employees, {
	        formatItem: function(item) {
	            return item.name;
	        },
	        matchContains:true
	    }).result(function(event, item) {
	       
	    });
	    
	    $("#empDir_project_name").autocomplete(customerList, {
	        formatItem: function(item) {
	            return $('<div/>').text(item.name).html();
	        },
	        formatResult: function(item) {
	            return item.name
	        }  
	        ,matchContains:true
	    }).result(function(event, item) {
	    	
	    } );
	    
	    $("#empDir_employee_name").val('');

        $('#searchBtn').click(function() {
            $("#empDir_isSubmitted").val('yes');
            $('#search_form input.inputFormatHint').val('');
            $('#search_form input.ac_loading').val('');
            $('#search_form').submit();
        });

        $('#resetBtn').click(function(){
            $("#empDir_isSubmitted").val('yes');
            $("#empDir_employee_name").val('');
            $("#empDir_project_name").val('');
            $("#empDir_id").val('');
            $("#empDir_skills").val('0');
            $("#empDir_membership").val('0');
            $("#empDir_job_title").val('0');
            $('#search_form').submit();
        });
        
    }); //ready