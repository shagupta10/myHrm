<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
* all the essential functionalities required for any enterprise.
* Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
*
* OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
* the GNU General Public License as published by the Free Software Foundation; either
* version 2 of the License, or (at your option) any later version.
*
* OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License along with this program;
* if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
* Boston, MA  02110-1301, USA
*
*/
?>
<?php use_javascripts_for_form($form) ?>
<?php use_stylesheets_for_form($form) ?>
<div class= "box">
	<div class= "head"> <h1><?php echo __('Add Feedback') ?></h1></div>
	<div class= "inner">
	<?php include_partial('global/flash_messages', array('prefix' => 'addFeedbackTop')); ?>
	<?php include_partial('global/flash_messages', array('prefix' => 'addFeedback')); ?>
	<form method="post" action="<?php echo url_for('performance/addFeedback'); ?>" name= "frmAddFeedback" id = "frmAddFeedback">
		<?php echo $form['_csrf_token']; ?>
		<?php echo $form["empNumber"]->render(); ?>
		<h3><em>Note: Please ensure to give constructive and actionable feedback.</em></h3><br />
		<ol>
			<li>
					<?php echo $form['empName']->renderLabel('Employee Name'. ' <span class="required">*</span>');?>
					<?php echo $form['empName']->render();?>
			</li>
			<li>
					<?php echo $form['pros']->renderLabel('Strong Points');?>
					<?php echo $form['pros']->render(array("style"=>"width:400px;height:150px"));?>
			</li>
			<li class="largeTextBox">
					<?php echo $form['cons']->renderLabel('Weak Points'. ' <span class="required">*</span>');?>
					<?php echo $form['cons']->render(array("style"=>"width:400px;height:150px"));?>
			</li>
		    <li>
                	<label for="performanceCycle"><?php echo __('Current Cycle'); ?></label>
                	 <?php
            			$currentCycle = $form->getCurrentCycle();
            			echo __(set_datepicker_date_format($currentCycle->getPeriodFrom()) .' - '.set_datepicker_date_format($currentCycle->getPeriodTo()));
            		 ?>
	        </li>
			<li>		
					<?php echo $form['flag']->renderLabel('Anonymous Feedback');//Review as Anonymous?>
					<?php echo $form['flag']->render();?>
					<div id ="hlpTxt">
					       [When you give feedback for ABC, only ABC's lead will be able to see your name. If you want FURTHER anonymity, check this checkbox.]
					</div>
			</li>
			<li class="required new">
                    <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
            </li>
		</ol>
		 <p>
            <input type="button" id="btnSaveDraft" value="<?php echo __("Draft") ?>" name="btnSaveDraft" style="display: none;"/>
            <input type="button" id="btnSave" value="<?php echo __("Submit") ?>" name="btnSave" style="display: none;"/>
            <input type="button" id="btnDraftSave" value="<?php echo __("Submit") ?>" name="btnDraftSave" style="display: none;"/>
            <input type="button" class ="delete" id="btnDiscard" value="<?php echo __("Discard") ?>" name="btnDiscard" style="display: none;"/>
            <input type="button" class ="reset" id="btnBack" value="<?php echo __("Back") ?>" name="btnBack" style="display: none;"/>                      
	     </p>
	     <input type = "hidden" name = "hdnAction" id = "hdnAction"/>
	     <input type = "hidden" name = "hdnId" id = "hdnId"/>
	</form>
	</div>
</div> 

<!-- Confirmation box HTML: Begins -->
<div class="modal hide" id="deleteConfirmation">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('SynerzipHRMS - Confirmation Required'); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo __('Are you sure to discard feedback?'); ?></p>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" data-dismiss="modal" id="dialogDiscardBtn" value="<?php echo __('Ok'); ?>" />
        <input type="button" class="btn reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>" />
    </div>
</div>
<!-- Confirmation box HTML: Ends -->

<script type="text/javascript">
//<![CDATA[
	var employees = <?php echo str_replace('&#039;', "'", $form->getEmployeesAsJson()) ?> ;
	var feedbackId = '<?php echo $feedback; ?>';
	var isViewOnly = '<?php echo $isViewOnly; ?>';
	var isSubmitted = '<?php echo $isSubmitted; ?>';
	var isDeleted = '<?php echo $IsDeleted; ?>';
	var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
	var currentDate = '<?php echo set_datepicker_date_format(date("Y-m-d")); ?>';
	var lang_dateValidation = "<?php echo __("Should be less than current date"); ?>";
	var lang_validDateMsg = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
	var lang_required = '<?php echo __(ValidationMessages::REQUIRED); ?>';
	var employeesArray = eval(employees);
	var lang_invalidName = "<?php echo __("Enter Valid Employee Name."); ?>";
	var lang_dateError = '<?php echo __("To date should be after from date") ?>';
	var lang_typeForHints = '<?php echo __("Type for hints") . "..."; ?>';
	var isNotEndofCycle = <?php echo json_encode($endOfCycle); ?>;
//]]>

$("#addFeedback_empName").autocomplete(employees, {
    formatItem: function(item) {
        return $('<div/>').text(item.name).html();
    },
    formatResult: function(item) {
        return item.name
    },  
    matchContains:true
}).result(function(event, item) {
    $('#addFeedback_empNumber').val(item.num);
    $('#addFeedback_empName').valid();
});

$('#btnSave').click(function(){
	$('#hdnAction').val('save');
	if($('#frmAddFeedback').valid()){
		$('form#frmAddFeedback').attr('action','<?php url_for('performance/addFeedback')?>');
		enableall();
		$('form#frmAddFeedback').submit();
		disableall();
	}
});

$('#btnDiscard').click(function(){
	$('#hdnAction').val('discard');
	if($('#frmAddFeedback').valid()){
		$('form#frmAddFeedback').attr('action','<?php url_for('performance/addFeedback')?>'+'?id='+feedbackId);
		$('#deleteConfirmation').modal();
	}
});

$('#btnDraftSave').click(function(){
	$('#hdnAction').val('saveDraft');
	if($('#frmAddFeedback').valid()){
		$('form#frmAddFeedback').attr('action','<?php url_for('performance/addFeedback')?>'+'?id='+feedbackId);
		enableall();
		$('form#frmAddFeedback').submit();
		disableall();
	}
});

$('#btnBack').click(function(){
	var backBtnUrl = '<?php echo url_for('performance/viewMyFeedback'); ?>'+'?id='+feedbackId;
	window.location.replace(backBtnUrl);
});

$('#btnSaveDraft').click(function(){
	$('#hdnAction').val('draft');
	if($('#btnSaveDraft').val()=='Draft') {
		if(feedbackId!=-1) {
			$('form#frmAddFeedback').attr('action','<?php url_for('performance/addFeedback')?>'+'?id='+feedbackId);
		}
		enableall();
		$('form#frmAddFeedback').submit();
		disableall();
	}
	if($('#btnSaveDraft').val()=='Edit') {
		$('#btnSaveDraft').val('Draft');
		$('#btnDraftSave').show();
		$('#addFeedback_pros').removeAttr('disabled');
		$('#addFeedback_cons').removeAttr('disabled');
		$('#addFeedback_flag').removeAttr('disabled');
		$(".calendar").datepicker('enable');
		if(isNotEndofCycle == true) {
			$('#btnSaveDraft').val('Edit');
			$('#btnSaveDraft').hide();
			$('#btnDraftSave').show();
		}
	}
});

$('#dialogDiscardBtn').click(function(){
	enableall();
	$('form#frmAddFeedback').submit();
	disableall();
});

$(document).ready(function() {
    if(feedbackId!=-1) {
        $('#hdnId').val(feedbackId);
    	$('#addFeedback_empName').attr('disabled','disabled');
		$('#addFeedback_pros').attr('disabled','disabled');
		$('#addFeedback_cons').attr('disabled','disabled');
		$('#addFeedback_flag').attr('disabled','disabled');
		$('#btnBack').show();
		if(isSubmitted == 0) {
			$('#btnSaveDraft').val('Edit');
			$('#btnSaveDraft').show();
			$('#btnDraftSave').show();
			$('#btnDiscard').show();
			if(isDeleted == 0) {
				$('#btnDiscard').show();
			}
		} else {
			if(isNotEndofCycle && isViewOnly != '1' ) {
				$('#btnSaveDraft').val('Edit');
				$('#btnSaveDraft').show();
				$('#btnDraftSave').hide();
			}
		}
    } else {
        //$('#addFeedback_flag').attr('checked','checked');
    	$('#btnSave').show();
		$('#btnSaveDraft').show();
    }
});
//-------------------- validators 

var validator = $("#frmAddFeedback").validate({
	 rules: {
	 	'addFeedback[empName]': {
		 	required: true,
	 		nameValidation: lang_invalidName
	 		},
 		'addFeedback[cons]': {
		 	required: true,
	 		},
	 },
	 messages: {
		'addFeedback[empName]': {
			required: lang_required,
			nameValidation: lang_invalidName
			},
		'addFeedback[cons]': {
			required: lang_required,
			},
	 }
});

$.validator.addMethod("nameValidation", function(value, element, params) {
    var temp = false;
    var empCount = employeesArray.length;
    if ($('#addFeedback_empName').hasClass("inputFormatHint")) {
        temp = true
    }

    else if ($('#addFeedback_empName').val() == "") {
        $('#addFeedback_empNumber').val("");
        temp = true;
    }
    else{
        var i;
        for (i=0; i < empCount; i++) {
            canName = $.trim($('#addFeedback_empName').val()).toLowerCase();
            arrayName = employeesArray[i].name.toLowerCase();

            if (canName == arrayName) {
                $('#addFeedback_empNumber').val(employeesArray[i].num);
                temp = true
                break;
            }
        }
    }
    return temp;
});



function enableall() {
	$('#addFeedback_pros').removeAttr('disabled');
	$('#addFeedback_cons').removeAttr('disabled');
	$('#addFeedback_flag').removeAttr('disabled');
}

function disableall() {
	$('#addFeedback_pros').attr('disabled','disabled');
	$('#addFeedback_cons').attr('disabled','disabled');
	$('#addFeedback_flag').attr('disabled');

}

</script>
