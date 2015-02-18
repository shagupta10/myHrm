var originalCertificationName = '';

$(document).ready(function() {
    
    executeLoadtimeActions();
    loadCheckboxBehavior();
    
    loadCancelButtonBehavior();
    loadDeleteButtonBehavior();
    
    $('#btnSave').click(function() {
        validateData();
        $('#frmSave').submit();
    });
	
});

$.validator.addMethod("uniqueName", function(value, element, params) {
    
    /* If in edit mode and original name (value at loading time), return true */
    if ($('#saveFormHeading').text() == lang_editFormHeading && $.trim($('#certification_name').val()) == originalCertificationName) {
        return true;
    }
    
    var temp = true;
    var currentCertification;
    var id = parseInt(id,10);
    var vcCount = certificationList.length;
    for (var j=0; j < vcCount; j++) {
        if(id == certificationList[j].id){
        	currentCertification = j;
        }
    }
    var i;
    certificationName = $.trim($('#certification_name').val()).toLowerCase();
    for (i=0; i < vcCount; i++) {

        arrayName = certificationList[i].name.toLowerCase();
        if (certificationName == arrayName) {
            temp = false
            break;
        }
    }
    if(currentCertification != null){
        if(certificationName == certificationList[currentCertification].name.toLowerCase()){
            temp = true;
        }
    }
	
    return temp;
});

function validateData() {
    
    $("#frmSave").validate({

        rules: {
            'certification[name]' : {
                required:true,
                maxlength: 120,
                uniqueName:true
            },
            'certification[description]' : {
                maxlength: 250
            }

        },
        messages: {
            'certification[name]' : {
                required: lang_nameIsRequired,
                uniqueName: lang_nameExists
            },
            'certification[description]' : {
                maxlength: lang_descLengthExceeded
            }

        }

    });
    
}

function executeLoadtimeActions() {
    
    $('#saveFormDiv').hide();
    
    $('table.data-table tbody tr:odd').addClass('odd');
    $('table.data-table tbody tr:even').addClass('even');
    
    if (recordsCount == 0) {
        $('#recordsListTable th.check').hide();
        $('#recordsListTable td.check').hide();
    }    
    
}

function loadCheckboxBehavior() {
    
    $("#checkAll").click(function(){
        if($("#checkAll:checked").attr('value') == 'on') {
            $(".checkboxAtch").attr('checked', 'checked');
        } else {
            $(".checkboxAtch").removeAttr('checked');
        }
    });

    $(".checkboxAtch").click(function() {
        
        $("#checkAll").removeAttr('checked');
        
        if(($(".checkboxAtch").length - 1) == $(".checkboxAtch:checked").length) {
            $("#checkAll").attr('checked', 'checked');
        }
        
        if ($(".checkboxAtch:checked").length > 0 && $(".checkboxAtch").length >1) {
            $('#btnDel,#btnDisapprove').removeAttr('disabled');
			
        } else {
            $('#btnDel,#btnDisapprove').attr('disabled', 'disabled');			
        }
        
    });    
    
}



function loadCancelButtonBehavior() {
    
    $("#btnCancel").click(function(){
        
        $('#saveFormDiv').hide();
        
        $('#recordsListTable th.check').show();
        $('#recordsListTable td.check').show();
        
        _addRecordLinks();

        $('#listActions').show();
        
        if (recordsCount == 0) {
            $('#recordsListTable th.check').hide();
            $('#recordsListTable td.check').hide();
        }         
        
    });
    
} 

function loadDeleteButtonBehavior() {   
    
    if ($(".checkboxAtch:checked").length == 0) {
        $('#btnDel,#btnDisapprove').attr('disabled', 'disabled');
    } 
    
    $('#btnDel,#btnDisapprove').click(function(){
        $('#frmList').submit();
    });
    
}

function _removeRecordLinks() {
    $('#recordsListTable tbody td.tdName a').each(function(index) {
        $(this).parent().text($(this).text());
    });
}

function _addRecordLinks() {
    $('#recordsListTable tbody td.tdName').wrapInner('<a href="#"/>');
}

function _clearErrorMessages() {    
    $('.errorHolder').each(function(){
        $(this).empty();
    });    
}


