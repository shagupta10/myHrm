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
<?php use_javascript(plugin_web_path('orangehrmRecruitmentPlugin', 'js/viewCandidatesSuccess')); ?>
<style type="text/css">
.messages {
	display: none;
	position: absolute;
	width: auto;
	padding: 10px;
	background: #000000;
	color: #EEEEEE;
	border: 2px solid #1a1a1a;
	font-size: 100%;
}

.bgColorDesc {
	background-color: #C4C4C4;
}

input[type="radio"]:checked+span {
	font-weight: bolder;
}
</style>
<script type="text/javascript"> var recordsPerpage = '<?php echo $getRecordsLimit; ?>'; </script>
<div class="box searchForm toggableForm" id="srchCandidates">
	<div class="head">
		<h1><?php echo __('Candidates'); ?></h1>
	</div>
    <?php if($form->isEssUser()) { ?>
    <div class="inner">

		<form name="frmSrchCandidates" id="frmSrchCandidates" method="post"
			action="<?php echo url_for('recruitment/viewCandidates?recordsPerPage_Limit='.$getRecordsLimit); ?>">

			<fieldset>

				<ol>
                    <?php echo $form->render(); ?>
                    <?php include_component('core', 'ohrmPluginPannel', array('location' => 'listing_layout_navigation_bar_1')); ?>
                </ol>

				<p>
					<input type="button" id="btnSrch"
						value="<?php echo __("Search") ?>" name="btnSrch" /> <input
						type="button" class="reset" id="btnRst"
						value="<?php echo __("Reset") ?>" name="btnSrch" />
				</p>
			</fieldset>
		</form>
	</div>
    <?php } ?>
    <a href="#" class="toggle tiptip"
		title="<?php echo __(CommonMessages::TOGGABLE_DEFAULT_MESSAGE); ?>">&gt;</a>
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
		<input type="button" class="btn" data-dismiss="modal"
			id="dialogDeleteBtn" value="<?php echo __('Ok'); ?>" /> <input
			type="button" class="btn reset" data-dismiss="modal"
			value="<?php echo __('Cancel'); ?>" />
	</div>
</div>
<!-- Confirmation box HTML: Ends -->

<!-- build reject - Confirmation box HTML: Begins -->
<div class="modal hide" id="bulkRejectBox">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">×</a>
		<h3><?php echo __('SynerzipHRMS - Confirmation Required'); ?></h3>
	</div>
	<div class="modal-body">
		<p><?php echo __('Bulk Reject ?'); ?></p>
	</div>
	<div class="modal-footer">
		<input type="button" class="btn" data-dismiss="modal"
			id="dialogDeleteBtn1" value="<?php echo __('Ok'); ?>" /> <input
			type="button" class="btn reset" data-dismiss="modal"
			value="<?php echo __('Cancel'); ?>" />
	</div>
</div>
<!-- Confirmation box HTML: Ends -->

<!-- changeVacancy - Confirmation box HTML: Begins -->
<div class="modal hide large" id="changeVacacnyBox">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">×</a>
		<h3><?php echo __('SynerzipHRMS - Select Vacancy'); ?></h3>
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
		<input type="button" class="btn" data-dismiss="modal"
			id="dialogDeleteBtn2" value="<?php echo __('Ok'); ?>" /> <input
			type="button" class="btn reset" data-dismiss="modal"
			value="<?php echo __('Cancel'); ?>" />
	</div>
</div>
<!-- Confirmation box HTML: Ends -->

<!-- JD dialog -->
<div class="modal hide large" id="descDialog">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">×</a>
		<div id="header">
			<h3><?php echo __('Loading'); ?></h3>
		</div>
	</div>
	<div class="modal-body">
		<p>
		<div id="description">
			<span><?php echo __('Loading') . '...';?></span>
		</div>
		</p>
	</div>
	<div class="modal-footer">
		<input type="button" class="btn reset" data-dismiss="modal"
			id="ratingCancel" value="<?php echo __('Close'); ?>" />
	</div>
</div>
<!-- end of Rating dialog-->
<div id="domMessage" style="display: none;">We are processing your
	request. Please be patient.</div>

<form name="frmHiddenParam" id="frmHiddenParam" method="post"
	action="<?php echo url_for('recruitment/viewCandidates?recordsPerPage_Limit='.$getRecordsLimit); ?>">
	<input type="hidden" name="pageNo" id="pageNo"	value="<?php //echo $form->pageNo; ?>" /> 
	<input type="hidden" name="hdnAction" id="hdnAction" value="search" />
</form>
<!-- Candidate History Modal: Begins -->
<div class="modal hide large" id="candidatehistoryBox">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">�</a>
        <h3><?php echo __('SynerzipHRMS - Candidate History'); ?></h3> 
    </div>
    <div class="modal-body">
		<table class="table">
		<thead><tr><th>Performed date</th><th>Description</th><th>Notes</th><th>Details</th> </tr>
		</thead>
		<tbody id="tbodyhistory" >
		</tbody>
		</table>
		<span id="spanmsg" style="margin-left: 315px; margin-top: 50px;  font-size: 25px;"></span>
		</div>
</div>
<!-- Candidate History Modal: Ends -->

<script type="text/javascript">
function candidateHistory(id)
{
	var id = id;
	var arrayofid = id.split("_"); 
	id = arrayofid[1];
	$('#spanmsg').html('Loading ...');
//Ajax call to get Candidate history
 var ajaxUrl = '<?php echo sfContext::getInstance()->getRequest()->getUriPrefix().sfContext::getInstance()->getRequest()->getRelativeUrlRoot().'/index.php/recruitment/getCandidateHistory?id=' ?>';
 $('#tbodyhistory').html("");
 $.ajax({
     type: 'POST',
     url: ajaxUrl + id,
     dataType: 'text',
     success: function(data) { 
         $('#spanmsg').html('');
         if (data == 'false'){
         $('#tbodyhistory').html('No Data Found!!');
         }
         else
         {
         $('#tbodyhistory').html(data);
         }
      }
 });
}

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
                var vacancyListUrl = '<?php echo url_for('recruitment/getVacancyListForJobTitleJson'); ?>';
                var hiringManagerListUrlForJobTitle = '<?php echo url_for('recruitment/getHiringManagerListJson?jobTitle='); ?>';
                var hiringManagerListUrlForVacancyId = '<?php echo url_for('recruitment/getHiringManagerListJson?vacancyId='); ?>';
                var addCandidateUrl = '<?php echo url_for('recruitment/addCandidate'); ?>';
                var lang_all = '<?php echo __("All") ?>';
                var lang_dateError = '<?php echo __("To date should be after from date") ?>';
                var lang_helpText = '<?php echo __("Click on a candidate to perform actions") ?>';
                var candidatesArray = eval(candidates);
                var lang_enterValidName = '<?php echo __(ValidationMessages::INVALID) ?>';
                var lang_typeForHints = '<?php echo __("Type for hints") . "..."; ?>';
                var lang_enterCommaSeparatedWords = '<?php echo __("Enter comma separated words") . "..."; ?>';
                var allowedCandidateListToDelete = <?php echo json_encode($form->allowedCandidateListToDelete); ?>;
                var deleteUrl = '<?php echo url_for('recruitment/deleteCandidateVacancies'); ?>';
                var rejectUrl = '<?php echo url_for('recruitment/bulkRejectCandidate'); ?>';
                var changeVacancyUrl = '<?php echo url_for('recruitment/changeCandidateVacancies'); ?>';
    //]]>

 function getVacancyDescription(id){
                   $('#description').html('Loading...');
                   $('#header').html('Loading...');
                	getDescription(id);
                	$('#descDialog').modal();
 }

 function getDescription(id) {
	    params = 'vacId=' + id;
	    
	    $.ajax({
	        type: 'GET',
	        url: '<?php echo url_for('recruitment/getJobDescription'); ?>',
	        data: params,
	        dataType: 'json',
	        success: function(data) {   
	            var html = '';
	            var rows = 0;
	            $('#description').html('');
	           
	                html = html + data[0]['description'];
					var headerMsg = data[0]['name'];
	            $('#description').append(html);
	            $('#header').html(headerMsg);
		     }
	    });
 }
  
  
      

 function getinterviewTime(element) {
	 //alert(element.id);
	 $('#msg'+element.id).html('Loading...');
	 $('#msg'+element.id).show();
	 getInterViewDetails(element.id);
 }

 function getInterViewDetails(id) {
	    params = 'candidateId=' + id;
	    
	    $.ajax({
	        type: 'GET',
	        url: '<?php echo url_for('recruitment/getInterviewTime'); ?>',
	        data: params,
	        dataType: 'json',
	        success: function(data) { 
	            var html = '';
	            var rows = 0;
	           $('#msg'+id).html('');
	           html = html + '<b style="font-weight:bold;">Interview Name : </b>' +data[0]['name']+
	           '<br><b style="font-weight:bold;">Date : </b>'+data[0]['date']+
	           '<br><b style="font-weight:bold;">Time : </b>'+data[0]['time'];;
	           $('#msg'+id).append(html);
		     }
	    });
	}

   

 function hideinterviewTime(element) {
	 $('.messages').hide();
 }
</script>

