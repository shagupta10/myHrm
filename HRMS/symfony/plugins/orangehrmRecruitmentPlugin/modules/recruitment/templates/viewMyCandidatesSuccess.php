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
<?php use_javascript('jquery.blockUI.js')?>
<?php use_javascript(plugin_web_path('orangehrmRecruitmentPlugin', 'js/viewMyCandidatesSuccess')); ?>
<style type = "text/css">
.bgColorDesc{
		background-color: #D0D0D0;
}
input[type="radio"]:checked + span
{
    font-weight: bolder;
}
</style>
<script type="text/javascript"> var recordsPerpage = '<?php echo $recordsLimits; ?>'; </script>
<div class="box searchForm toggableForm" id="srchCandidates">
    <div class="head">
        <h1><?php echo __('My Referred Candidates'); ?></h1>
    </div>
    <div class="inner">
        <form name="frmSrchCandidates" id="frmSrchCandidates" method="post" action="<?php echo url_for('recruitment/viewMyCandidates'); ?>">
            <fieldset>
                <ol>
                    <?php echo $form->render(); ?>
                    <?php include_component('core', 'ohrmPluginPannel', array('location' => 'listing_layout_navigation_bar_1')); ?>
                </ol>
                 <input type="hidden" name="pageNo" id="pageNo" value="" />
    			<input type="hidden" name="hdnAction" id="hdnAction" value="search" />            
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


<!-- changeVacancy - Confirmation box HTML: Begins -->
<div class="modal hide large" id="changeVacacnyBox">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('SynerzipHRMS - Confirmation Required'); ?></h3>
    </div>
    <div class="modal-body">
		<?php 
			foreach($vacancyListBox as $value => $vacName) {
				echo '<div id ="row_"'.$value.'" class = "row" style = "width:auto;"><input type = "radio" name = "vacancyToChange" class = "division" id = "rdo_'.$value.'" value = '.$value.'><span>'.$vacName.
				'</span></div><div class = "descr" id = "jd_'.$value.'" style="display:none;"><hr>'.nl2br($vacancyDesc[$value]).'<hr></div>
 				<div id = "hideBtn_'.$value.'" class = "hideBtn" style="display:none;font-weight:bold;"><a href="#" class="links">[Hide]</a></div></br></br>';
			}
		?>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" data-dismiss="modal" id="dialogDeleteBtn2" value="<?php echo __('Ok'); ?>" />
        <input type="button" class="btn reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>" />
    </div>
</div>
<!-- Confirmation box HTML: Ends -->
<div id="domMessage" style="display:none;"> 
    We are sending your request to Admin. Please be patient.
</div> 
<script type="text/javascript">
//<![CDATA[
				var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
                var lang_validDateMsg = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
                var candidates = <?php echo str_replace('&#039;', "'", $form->getCandidateListAsJson()) ?> ;
                var employees = <?php echo str_replace('&#039;', "'", $form->getEmpListInCandidate()) ?> ;
                var statusMapping = <?php echo str_replace('&#039;', "'", $form->getStatusMapping()) ?> ;
                var statusMappingArray = eval(statusMapping);
                var addCandidateUrl = '<?php echo url_for('recruitment/addCandidate'); ?>';
                var lang_all = '<?php echo __("All") ?>';
                var lang_dateError = '<?php echo __("To date should be after from date") ?>';
                var lang_helpText = '<?php echo __("NOTE : To request Change Vacancy, Please select candidates with status as Application Initiated, Screening & Rejected") ?>';
                var candidatesArray = eval(candidates);
                var lang_enterValidName = '<?php echo __(ValidationMessages::INVALID) ?>';
                var lang_typeForHints = '<?php echo __("Type for hints") . "..."; ?>';
                var lang_enterCommaSeparatedWords = '<?php echo __("Enter comma separated words") . "..."; ?>';
                var changeVacancyUrl = '<?php echo url_for('recruitment/requestToChangeVacancy'); ?>';
    //]]>
                function submitPage(pageNo) {
                	document.frmSrchCandidates.pageNo.value = pageNo;
                	document.frmSrchCandidates.hdnAction.value = 'paging';
                	$('#candidateSearch_candidateName.inputFormatHint').val('');
            		$("#frmSrchCandidates").submit();
                	}
</script>

