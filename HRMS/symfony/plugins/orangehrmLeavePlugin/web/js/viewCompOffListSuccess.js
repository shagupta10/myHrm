$(document).ready(function() {
    
    $('#btnSearch').click(function() {
        $('#frmFilterLeave input.inputFormatHint').val('');
        $('#frmFilterLeave input.ac_loading').val('');
        $('#frmFilterLeave').submit();
    });


    $('#btnReset').click(function(event) {        
        window.location = resetUrl;
        event.preventDefault();
        return false;
    });
    
    $('select.select_action').bind("change",function() {
        $('div#noActionsSelectedWarning').remove();
    });
});