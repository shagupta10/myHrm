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
<?php use_javascript(plugin_web_path('orangehrmPerformancePlugin', 'js/viewReviewSuccess')); ?>
<script type="text/javascript">
var empdata = <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson()) ?>;
var disabledReviews = <?php echo $form->getApprovedReviewsListAsJson(); ?>;
var projectData = <?php echo $form->getProjectListAsJson(); ?>;
var loggedAdmin = <?php echo (isset($loggedAdmin) && !empty($loggedAdmin))? $loggedAdmin:0;?>;
var loggedReviewer = <?php echo (isset($loggedReviewer) && !empty($loggedReviewer))? $loggedReviewer:0; ?>;
var deleteUrl = "<?php echo url_for('performance/deleteReview'); ?>";
var exportReviewsUrl = "<?php echo url_for('performance/exportReviews'); ?>";
var approveReviewsUrl = "<?php echo url_for('performance/approveReviews'); ?>";
var saveReviewUrl = '<?php echo url_for('performance/saveReview'); ?>'; 
var recordsPerpage = '<?php echo $recordsPerPage; ?>';
    function populateTokenInput(id, json) {
    	$("#txtRevHdn-"+id).tokenInput(empdata, {
            prePopulate: json,
            tokenLimit: 6,
            preventDuplicates: true,
            theme: "facebook",
            disabled: false  
        });
    }
    
    function populateEmployeeNumber(reviewId, empNum) {
    	$('#previewersList-'+reviewId+' div#spanEmpNum-'+reviewId).text(empNum);
    }
    
    function populateTokenInputPrimary(id, json) {
    	$("#ptxtRevHdn-"+id).show();
    	$("#ptxtRevHdn-"+id).tokenInput(empdata, {
            prePopulate: json,
            tokenLimit: 1,
            preventDuplicates: true,
            theme: "facebook",
            disabled: false,
            required: true	  
        });
    }
    
    function saveReviewers(reviewId) {
        var reviewerIds = $('#txtRevHdn-'+reviewId).val();
        var reviewerId = $('#ptxtRevHdn-'+reviewId).val();
    
        if(reviewerId =='' || reviewerId ==' '){
        	$('#previewersListHdn-'+reviewId+' span').addClass('validation-error').show();
        	$('#previewersListHdn-'+reviewId+' span').show();
            return;
        } else {
        	$('div[id^=previewersListHdn-]').find('span.errorContainer').removeClass('validation-error').hide();
        }
        var empNum = $('#previewersList-'+reviewId+' div#spanEmpNum-'+reviewId).text();
    	var reviewersArray = reviewerIds.split(",");
    	
        for(i=0; i < reviewersArray.length ; i++) {
    		if(reviewerId == reviewersArray[i]) {
    			alert('Error: Duplicate Reviewer Names.\n\nPlease Enter Valid Reviewers');
    			return;
    		}
    		
    		if(empNum == reviewersArray[i]){
    			alert('Error : Reviewer name should not be same as Employee name.\n\nPlease Enter Valid Reviewers.');
    			return;
    		}
        }
        if(empNum == reviewerId) {
       		alert('Error : Reviewer name should not be same as Employee name.\n\nPlease Enter Valid Reviewers.');
    		return;
       	}
       	
        $.ajax({
            type: 'GET',
            url: '<?php echo url_for('performance/updateReviewers'); ?>',
            data: { review : reviewId , reviewers : reviewerIds, reviewer : reviewerId },
            dataType: 'json',
            success: function(data) {
    			var primaryReviewer = data.shift();
    			
                var namestring='';
              	$('#txtContainer-'+reviewId).html('<input type = "text" id = "txtRevHdn-'+reviewId+'" />');
              	$('#ptxtContainer-'+reviewId).html('<input type = "text" id = "ptxtRevHdn-'+reviewId+'" />');
                for(i = 0; i < data.length; i++) {
                	namestring = namestring + data[i].name;
                    if(i < (data.length-1)) {
                    	namestring = namestring + ', ';
                    }
                } 
                
                $('#reviewersList-'+reviewId+' span').html(namestring); 
            	$("#txtRevHdn-"+reviewId).tokenInput(empdata, {
                    prePopulate: data,
                    tokenLimit: 6,
                    preventDuplicates: true,
                    theme: "facebook",
                    disabled: false  
                });
    
            	$('#previewersList-'+reviewId+' span').html(primaryReviewer[0].name); 
            	$("#ptxtRevHdn-"+reviewId).tokenInput(empdata, {
                    prePopulate: primaryReviewer,
                    tokenLimit: 1,
                    preventDuplicates: false,
                    theme: "facebook",
                    disabled: false,
                    required: true
                });
            	$('div[id^=previewersListHdn-]').find('span.errorContainer').removeClass('validation-error').hide();
    	    	$('#reviewersList-'+reviewId).show();
    	       	$('#reviewersListHdn-'+reviewId).hide();
    	       	$('#previewersList-'+reviewId).show();
    	       	$('#previewersListHdn-'+reviewId).hide();
    	    }
        });
    }
    
    
    
</script>
<div class="box searchForm toggableForm">     

    <div class="head">
        <h1><?php echo __('Search Performance Reviews')?></h1>
    </div>
  
    <div class="inner">
        <form action="<?php echo url_for('performance/viewReview?recordsPerPage_Limit='.$recordsPerPage); ?>" id="frmSearch" name="frmSearch" method="post">
            <input type="hidden" name="mode" value="search" >
            <input type="hidden" name="pageNo" id="pageNo" value="<?php echo $clues['pageNo'];?>" />
            <input type="hidden" name="txtSortOrder" id="txtSortOrder" value="<?php echo $clues['Order'];?>" />
            <input type="hidden" name="txtSortField" id="txtSortField" value="<?php echo $clues['sortBy'];?>" />
            <input type="hidden" name="hdnAction" id="hdnAction" value="search" />
            <?php echo $form['_csrf_token']; ?>
            <fieldset>
                <ol>                        
                    <li>
                		<label for="performanceCycle"><?php echo __('Performance Cycle'); ?></label>
                    	 <?php
                			$performanceCycle = new ohrmWidgetPerformancePeriod();
                			echo $performanceCycle->render('performanceCycle',array('from' => $clues['from'],'to' => $clues['to']),'style="width:300px"');
                		 ?>
                	</li>

                    <li>
                        <label for="txtJobTitleCode"><?php echo __('Job Title') ?></label>
                        <select id="txtJobTitleCode" name="txtJobTitleCode" class="formSelect" tabindex="3">
                            <option value="0"><?php echo __('All') ?></option>
                            <?php
                            foreach ($jobList as $job) {
                                if ($job->getId() == $clues['jobCode']) {
                                    $selected = ' selected';
                                } else {
                                    $selected = '';
                                }
                                $jobName = $job->getJobTitleName();
                                if ($job->getIsDeleted() == JobTitle::DELETED) {
                                    $jobName = $jobName . ' (' . __('Deleted') . ')';
                                }
                                echo "<option value=\"" . $job->getId() . "\"" . $selected . ">" . $jobName . "</option>\n";
                            }
                            ?>
                        </select>
                    </li>
                    
                   <li>
                    <label for="txtState"><?php echo __('Status') ?></label>
                    <select id="txtState" name="txtState" class="formSelect" tabindex="4">
                        <option value="0"><?php echo __('All') ?></option>
                        <?php
                        foreach (PerformanceReview::$performanceStatusList as $key => $status) {
                            if ($key == $clues['state']) {
                                $selected = ' selected';
                            } else {
                                $selected = '';
                            }
                            echo "<option value=\"" . $key . "\"" . $selected . ">" . $status . "</option>\n";
                        }
                        ?>
                    </select>
                </li>

                <li>
                    <label for="txtSubDivisionId"><?php echo __('Sub Division') ?></label>
                    <select id="txtSubDivisionId" name="txtSubDivisionId" class="formSelect" tabindex="5">
                        <option value="0"><?php echo __('All') ?></option>
                        <?php
                        foreach ($tree as $node) {
                            if ($node->getId() != 1) {
                                if ($node->getId() == $clues['divisionId']) {
                                    $selected = ' selected';
                                } else {
                                    $selected = '';
                                }
                                echo "<option value=\"" . $node->getId() . "\"" . $selected . ">" . str_repeat('&nbsp;&nbsp;', $node['level'] - 1) . $node['name'] . "</option>\n";
                            }
                        }
                        ?>
                    </select>
                </li>
                    
                    <?php if ($loggedAdmin || $loggedReviewer) { ?>
                     <li>
                        <label for="txtProjectName"><?php echo __('Project') ?></label>
                        <input id="txtProjectName"  name="txtProjectName" type="text" class="formInputText" placeholder="Type for hints..."
                               value="<?php echo isset($clues['projectName']) ? $clues['projectName'] : ''?>" tabindex="5" 
                                   onblur="autoFillProject('txtProjectName', 'hdnCustomerId');"/>
                        <input type="hidden" name="hdnCustomerId" id="hdnCustomerId" 
                               value="<?php echo isset($clues['customerId']) ? $clues['customerId'] : '0' ?>">
                    </li>
                    <li>
                        <label for="txtEmpName"><?php echo __('Employee') ?></label>
                        <input id="txtEmpName" name="txtEmpName" type="text" placeholder="Type for hints..."
                               value="<?php echo isset($clues['empName']) ? $clues['empName'] : ''?>"
                               tabindex="6" onblur="autoFill('txtEmpName', 'hdnEmpId');"/>
                        <input type="hidden" name="hdnEmpId" id="hdnEmpId" 
                               value="<?php echo isset($clues['empId']) ? $clues['empId'] : '0' ?>">
                    </li>
                    
                    <?php } // $loggedAdmin || $loggedReviewer:Ends    ?>
                        
                    <?php if ($loggedAdmin) { ?>
                    <li>
                        <label for="txtReviewerName"><?php echo __('Reviewer') ?></label>
                        <input id="txtReviewerName"  name="txtReviewerName" type="text" class="formInputText" placeholder="Type for hints..."
                               value="<?php echo isset($clues['reviewerName']) ? $clues['reviewerName'] : ''?>" tabindex="7" 
                                   onblur="autoFill('txtReviewerName', 'hdnReviewerId');"/>
                        <input type="hidden" name="hdnReviewerId" id="hdnReviewerId" 
                               value="<?php echo isset($clues['reviewerId']) ? $clues['reviewerId'] : '0' ?>">
                    </li>
                    <?php } // $loggedAdmin:Ends    ?>
					<li>
						<div style="display:inline">
							<?php if($clues['directReview'] == 'direct') { ?>
								<input type="checkbox" style="margin: 26px 2px" checked id="directReview" name="directReview" value="direct">
							<?php } else { ?>
								<input type="checkbox" style="margin: 26px 2px" id="directReview" name="directReview" value="direct">
							<?php } ?>
							<label for="checkReviewerDirect" style="margin: 26px 0 0 18px"><?php echo __('Direct Reportees') ?></label>							
						</div>
					</li>
                   
                </ol>
                <p>
                    <input type="button" class="" id="searchButton" value="<?php echo __("Search") ?>" tabindex="7"/>
                    <input type="button" class="reset" id="clearBtn" value="<?php echo __('Clear') ?>" tabindex="8"/>
                </p> 
            </fieldset>
        </form>
    </div> <!-- Inner:Ends -->
    
    <a href="javascript:void(0);" class="toggle tiptip" title="<?php echo __(CommonMessages::TOGGABLE_DEFAULT_MESSAGE); ?>">&gt;</a>

</div> <!-- box:Ends -->

<?php include_component('core', 'ohrmList', $parmetersForListCompoment); ?> 
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
    document.frmSearch.pageNo.value = pageNo;
    document.frmSearch.hdnAction.value = 'paging';
    document.getElementById('frmSearch').submit();

}
</script>