<?php
/*
 * Created on 14-Jan-2014
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
?>
<?php use_javascripts_for_form($form); ?>
<?php use_stylesheets_for_form($form); ?>
<?php use_javascript('jquery.blockUI.js')?>
<?php use_javascript(plugin_web_path('orangehrmRecruitmentPlugin', 'js/viewJobRequestsSuccess')); ?>
<style type="text/css">
.messages{
	    display: none;
        position: absolute;
        width: auto;
        padding: 10px;
        background: #000000;
        color: #EEEEEE;
        /* background: #eeeeee;
        color: #000000; */
        border: 2px solid #1a1a1a;
        font-size: 100%;
}
.bgColorDesc{
		background-color: #C4C4C4;
}
input[type="radio"]:checked + span
{
    font-weight: bolder;
}
</style>
<script type="text/javascript"> var recordsPerpage = '<?php echo $getRecordsLimit; ?>'; </script>
<div class="box searchForm toggableForm" id="srchCandidates">
    <div class="head">
        <h1><?php echo __('Request Tracker'); ?></h1>
    </div>
    <div class="inner">
        <form name="frmSrchCandidates" id="frmSrchCandidates" method="post" action="<?php echo url_for('recruitment/viewJobRequests?recordsPerPage_Limit='.$getRecordsLimit); ?>">
            <fieldset>
                <ol>
                    <?php echo $form->render(); ?>
                    <?php include_component('core', 'ohrmPluginPannel', array('location' => 'listing_layout_navigation_bar_1')); ?>
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
<?php include_component('core', 'ohrmList', $parmetersForListCompoment); ?>


<!-- Confirmation box HTML: Begins -->
<div class="modal hide" id="deleteConfirmation">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('SynerzipHRMS - Confirmation Required'); ?></h3>
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

<!-- build reject - Confirmation box HTML: Begins -->
<div class="modal hide" id="requestRejectBox">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('SynerzipHRMS - Confirmation Required'); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo __('Reject Request(s) ?'); ?></p>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" data-dismiss="modal" id="dialogRejectBtn" value="<?php echo __('Ok'); ?>" />
        <input type="button" class="btn reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>" />
    </div>
</div>
<!-- Confirmation box HTML: Ends -->


<div id="domMessage" style="display:none;"> 
    We are processing your request.  Please be patient.
</div> 

<form name="frmHiddenParam" id="frmHiddenParam" method="post" action="<?php echo url_for('recruitment/viewJobRequests?recordsPerPage_Limit='.$getRecordsLimit); ?>">
    <input type="hidden" name="pageNo" id="pageNo" value="<?php //echo $form->pageNo;        ?> " />
    <input type="hidden" name="hdnAction" id="hdnAction" value="search" />
</form>


<script type="text/javascript">
	function submitPage(pageNo) {
		document.frmHiddenParam.pageNo.value = pageNo;
		document.frmHiddenParam.hdnAction.value = 'paging';
		document.getElementById('frmHiddenParam').submit();
	}
	
	//<![CDATA[
	var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var lang_validDateMsg = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
    var candidates = <?php echo str_replace('&#039;', "'", $form->getCandidateListAsJson()) ?> ;
    var employees = <?php echo str_replace('&#039;', "'", $form->getEmpListInCandidate()) ?> ;
    var lang_dateError = '<?php echo __("To date should be after from date") ?>';
    var lang_helpText = '<?php echo __("Click on a candidate to perform actions") ?>';
    var candidatesArray = eval(candidates);
    var lang_enterValidName = '<?php echo __(ValidationMessages::INVALID) ?>';
    var lang_typeForHints = '<?php echo __("Type for hints") . "..."; ?>';
    var lang_enterCommaSeparatedWords = '<?php echo __("Enter comma separated words") . "..."; ?>';
    var deleteUrl = '<?php echo url_for('recruitment/deleteCandidateVacancies'); ?>';
    var rejectUrl = '<?php echo url_for('recruitment/bulkRejectCandidate'); ?>';
    var changeVacancyUrl = '<?php echo url_for('recruitment/changeCandidateVacancies'); ?>';
</script>
