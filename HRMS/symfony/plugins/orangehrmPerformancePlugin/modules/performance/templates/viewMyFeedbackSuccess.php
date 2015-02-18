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
<?php use_javascripts_for_form($form); ?>
<?php use_stylesheets_for_form($form); ?>
<?php use_javascript('orangehrm.datepicker.js')?>
<?php use_javascript('jquery.blockUI.js')?>
<?php use_javascript(plugin_web_path('orangehrmPerformancePlugin', 'js/viewMyFeedbackSuccess')); ?>
<script type="text/javascript"> var recordsPerpage = '<?php echo $recordsPerPage; ?>'; </script>
<div class="box searchForm toggableForm" id="srchCandidates">
    <div class="head">
        <h1><?php echo __('View Feedback'); ?></h1>
    </div>

    <div class="inner">
        <form name="frmSrchFeedback" id="frmSrchFeedback" method="post" action="<?php echo url_for('performance/viewMyFeedback?recordsPerPage_Limit='.$recordsPerPage); ?>">
            <fieldset>
              
                <ol>
                    <?php echo $form->render(); ?>
                </ol>
                            
                <p>
                    <input type="button" id="btnSrch" value="<?php echo __("Search") ?>" name="btnSrch" />
                    <input type="button" class="reset" id="btnRst" value="<?php echo __("Reset") ?>" name="btnSrch" />                    
                </p>
            </fieldset>            
        </form>
    </div>
    <a href="#" class="toggle tiptip" title="<?php echo __(CommonMessages::TOGGABLE_DEFAULT_MESSAGE); ?>">&gt;</a>
</div>
<?php  include_component('core', 'ohrmList', $parmetersForListCompoment); ?>

<form name="frmHiddenParam" id="frmHiddenParam" method="post" action="<?php echo url_for('performance/viewMyFeedback?recordsPerPage_Limit='.$recordsPerPage); ?>">
    <input type="hidden" name="pageNo" id="pageNo" value="<?php echo $form->pageNo;        ?>" />
    <input type="hidden" name="hdnAction" id="hdnAction" value="search" />
</form>

<!-- Confirmation box HTML: Begins -->
<div class="modal hide" id="deleteConfModal">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo __('OrangeHRM - Confirmation Required'); ?></h3>
  </div>
  <div class="modal-body">
    <p><?php echo __(CommonMessages::DELETE_CONFIRMATION); ?></p>
  </div>
  <div class="modal-footer">
    <input type="button" class="btn" data-dismiss="modal" id="dialogDeleteBtn" value="<?php echo __('Ok'); ?>" />
    <input type="button" class="btn reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>" />
  </div>
</div>
<!-- Confirmation box HTML: Ends -->
<script type="text/javascript">

function submitPage(pageNo) {

document.frmHiddenParam.pageNo.value = pageNo;
document.frmHiddenParam.hdnAction.value = 'paging';
document.getElementById('frmHiddenParam').submit();

}

//<![CDATA[
	var employees = <?php echo str_replace('&#039;', "'", $form->getReviewedEmployeesAsJson()) ?> ; //reviewedemployees
	var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
	var currentDate = '<?php echo set_datepicker_date_format(date("Y-m-d")); ?>';
	var lang_dateValidation = "<?php echo __("Should be less than current date"); ?>";
    var lang_validDateMsg = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
	var lang_required = '<?php echo __(ValidationMessages::REQUIRED); ?>';
	var employeesArray = eval(employees);
	var lang_invalidName = "<?php echo __("Enter Valid Employee Name."); ?>";
	var lang_dateError = '<?php echo __("To date should be after from date") ?>';
	var lang_typeForHints = '<?php echo __("Type for hints") . "..."; ?>';
	var fromdate;
	var deleteFeedbackUrl = '<?php echo url_for('performance/deleteMyFeedback'); ?>';
	var myFeedbackList = <?php echo str_replace('&#039;', "'", $form->getMyFeedbacks()) ?>  ;
//]]>
</script>
