$(document).ready(function() {     
    function clearErrors() {
        $('#frmTerminateEmployement').validate().resetForm();
    }
         /* Type hint for Customer Name */
    var customerName = $('#job_customerName');

    if (customerName.val() == '' || customerName.val() == lang_typeHint) {
        customerName.val(lang_typeHint).addClass(hintClass);
    }
    customerName.one('focus', function() {
        if ($(this).hasClass(hintClass)) {
            $(this).val("");
            $(this).removeClass(hintClass);
        }
    });  
    
    //customer auto complete
    $(".formInputCustomer").autocomplete(customerList, {
        formatItem: function(item) {
            return $('<div/>').text(item.name).html();
        },
        formatResult: function(item) {
            return item.name;
        },       
        matchContains:true
    }).result(function(event, item) {
        $('#customerId').val(item.id);       
    });

    	  $("#job_project").tokenInput(function() {
          if($('#job_customerName').val()==''){
            $('#customerId').val(''); 
          }
          return urlForGetProjectList + $('#customerId').val();}
            , {
                prePopulate: existingEmpProjectList,
                tokenLimit: 10,
                preventDuplicates: true,
                disabled: true  
            });       
        $('#dialogCancel').click(function(){
            clearErrors();
        });     
        
        /* Form validation */        
        var list = new Array('#job_project');
        for(i=0; i < list.length; i++) {
            $(list[i]).attr("disabled", "disabled");
        }  
        $("#job_customerName").attr("disabled", "disabled");
        $("#btnSave").click(function() {           
                       
            if ( !readonlyFlag) {  
                //if user clicks on Edit make all fields editable                                     
                if($("#btnSave").attr('value') == edit) {
                    for(i=0; i < list.length; i++) {
                        $(list[i]).removeAttr("disabled");
                    }
                    $("#job_customerName").removeAttr("disabled");
                    $("#job_project").tokenInput("toggleDisabled");
                    
                    
                    $("#btnSave").attr('value', save);
                    
                    
                    return;
                }
                
                if($("#btnSave").attr('value') == save) {
                    
                    $("#frmEmpJobDetails").submit();
                }
            }
        });
        /* Hiding showing viewDetailsLink at loading */
        //showHideViewDetailsLink();       
        
    });
    