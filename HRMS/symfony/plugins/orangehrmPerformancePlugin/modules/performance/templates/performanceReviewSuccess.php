<?php use_javascript('jquery.blockUI.js')?>
<?php use_stylesheet(plugin_web_path('orangehrmPerformancePlugin', 'css/performanceReviewSuccess'))?>
<?php use_javascript(plugin_web_path('orangehrmPerformancePlugin', 'js/performanceReviewSuccess'))?>
<?php
	$disableAll = false;
	$showButton = true;

	if($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED){
		$disableAll = true;
		if(!$isHrAdmin) {
			$showButton = false;
		}
	} else if($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_APPROVED){
		$disableAll = true;
		$showButton = false;
	} else if($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_BEING_REVIWED) {
		if($isSelfReview) {
			$disableAll = true;
			$showButton = false;
		}
	} 
	
	if(($isHrAdmin && $isSelfReview && ($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED)) || (($performanceReview->getState() < PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED) && !$isHrAdmin && !$isSelfReview && !$isReviwer) ) {
		$showButton = false;
	}
	
	/* if($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED && !$isHrAdmin) {
		$showButton = false;
		$disableAll = true;
	} */
    
	if($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_REJECTED && !$isReviwer ) {
		$showButton = false;
		$disableAll = true;
	}
	$deletedReviewers = array();
		foreach($performanceReview->getAllReviewers() as $rev) {
			if($rev->getIsDeleted() == PerformanceReviewReviewer::IS_DELETED) {
				$temp = array();
				$temp[0] = $rev->getId();
				$temp[1] = $rev->getReviewerId();
				array_push($deletedReviewers, $temp);
			}
		}
?>
<script type="text/javascript">
var isPrimary   = <?php if ($isPrimary) echo $isPrimary; else echo 'false'; ?>;
var isSelfReview = <?php if ($isSelfReview) echo 'true'; else echo 'false'; ?>;
var isReviwer	= '<?php echo $isReviwer; ?>';
var feedbackCount = <?php echo $feedbackCount;  ?>;
var loggedEmpId = <?php echo $loggedEmpId;?>;
var reviewId = '<?php echo $performanceReview->getId() ?>';
var validationError = '<?php echo __('Should be a number or NA, N/A, na, n/a if not applicable'); ?>';
var fractionError = '<?php echo __('Fractions are not allowed.'); ?>';
var blankError = '<?php echo __('Should not be blank'); ?>';
var lengthExceedMsg1 = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 2000)) ?>';
var lengthExceedMsg2 = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)) ?>';
var rangeError = '<?php echo __('Should be within Min and Max rating'); ?>';
var getKpiRatingsUrl = '<?php echo url_for('performance/getKpiRatings'); ?>';
var saveGoalsAjaxUrl = '<?php echo url_for('performance/saveGoalsAjax'); ?>';
var viewReviewUrl = '<?php echo url_for('performance/viewReview'); ?>';

$(document).ready(function(){
<?php if($isReviwer) { ?>
$('#tab-container').easytabs('select', '<?php echo '#'.$loggedEmpId.'-'.$reviewReviewerId ?>');
<?php }else { ?>
$('#tab-container').easytabs('select', '#selfReview');
<?php }?>
<?php foreach ($deletedReviewers as $del) {?>
disableFieldsinTab(<?php echo $del[0]; ?>, <?php echo $del[1]; ?>);
<?php }?>
});
</script>
<div class="box miniList" id="performanceReviewcontentContainer">

    <div class="head" id="formHeading" >
        <h1><?php echo __("Performance Review") ?></h1>
    </div>

    <div class="inner" style="background:none !important;">
		 <?php if(count($givenMultiFeedbackList) == 0) : ?>
            <div class="message warning">
               	<?php if($loggedEmpId == $performanceReview->getEmployeeId() || !$hasPeerEmployeeFeedback){ ?>
               		<a href="<?php echo url_for('performance/addFeedback') ?>"><?php echo __("Add") ?></a><?php echo __(" 360 review (atleast one peer) to Submit the Appraisal Form.") ?>
               	<?php } else { ?>
               		<?php echo __($performanceReview->getEmployee()->getFirstAndLastNames() ." need to add 360 review to Submit the self-review.") ?>
                    <!-- DESC: Message will display only reviewer not admin -->
                    <?php if($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED && (($isReviwer && !$isHrAdmin) ||($isHrAdmin && $isReviwer))) { ?>
                    <?php echo __("<br><br>Employee has not yet submitted the self-review, meanwhile you can draft your ratings/comments. Draft review ratings and comments are only visible to Reviewers and Admin.");?>
                    <?php } ?>
               	<?php } ?>
                    
            </div> <!-- message warning -->
        <?php endif; ?> 
        <?php include_partial('global/flash_messages'); ?>

        <form action="#" id="frmSave" class="content_inner" method="post">

            <?php echo $form['_csrf_token']; ?>
            <input type="hidden" name="id" id="id" value="<?php echo $performanceReview->getId() ?>"/>
            <input type="hidden" name="saveMode" id="saveMode" value="" />
            <!-- Review information box starts here -->
			<div class="box minilist remove-margin">
				<div class="head">
					<h1><?php echo __("Section 1 : Review Information") ?></h1>
				</div>
	            <div class="inner">
	                <ol class="remove-border">
	                    <li>
	                        <label><?php echo __("Employee") ?></label>
	                        <label class="line"><?php echo $performanceReview->getEmployee()->getFirstName() ?> 
	                            <?php echo $performanceReview->getEmployee()->getLastName() ?></label>
	                    </li>
	                    <li>
	                        <label><?php echo __("Job Title") ?></label>
	                        <label class="line"><?php echo $performanceReview->getJobTitle()->getJobTitleName(); ?> </label>
	                    </li>
	                    <?php if(!is_null($primaryReviewer)) { ?>
	                    <li>
	                        <label><?php echo __("Primary Reviewer") ?></label>
	                        <label class="line"><?php echo $primaryReviewer->getReviewer()->getFirstAndLastNames() ?></label>
	                    </li>
	                    <?php } ?>
	                    <li>
	                    <?php 
                        /* DESC: Displayed all reviewers  */
                        $secondary_reviewer_name='';
                        foreach($secondaryReviewer as $name) {
                          if($secondary_reviewer_name=='')
                          $secondary_reviewer_name= $name->getReviewer()->getFirstAndLastNames();
                          else
                          $secondary_reviewer_name.=", ".$name->getReviewer()->getFirstAndLastNames();
                        }
	                    ?>
	                        <label><?php echo __("Secondary Reviewer") ?></label>
	                        <label class="line"><?php echo $secondary_reviewer_name ?></label>
	                    </li>
	                    <li>
	                        <label><?php echo __("Review Period") ?></label>
	                        <label class="line"><?php echo set_datepicker_date_format($performanceReview->getPeriodFrom()) ?>-<?php echo set_datepicker_date_format($performanceReview->getPeriodTo()) ?></label>
	                    </li>
	                    <li>
	                        <label><?php echo __("Status") ?></label>
	                        <label class="line"><?php echo __($performanceReview->getTextStatus()) ?> </label>
	                    </li>
	                     <?php if(count($lastPerformanceList) > 0){?>
	                    	<li>
	                            <label><?php echo __("Last Performace Cycle") ?></label>
	                            <label class="line">
	                                <?php foreach ($lastPerformanceList as $lastPerformanceReview) { ?>
	                                	<a href="<?php echo url_for('performance/viewPerformanceReview?id='.$lastPerformanceReview->getId()); ?>" target="_blank">
	                                       <?php echo (set_datepicker_date_format($lastPerformanceReview->getPeriodFrom()). " - ". set_datepicker_date_format($lastPerformanceReview->getPeriodTo()) )?>
	                                    </a> <br /><br/>
	                                <?php } ?>
	                            </label>
	                        </li>
	                    <?php }?>
	                    <?php if (count($performanceReview->getPerformanceReviewComment()) > 0 && ($isReviwer || $isHrAdmin)) { ?>
	                        <li>
	                            <label><?php echo __("Notes") ?></label>
	                            <table class="table data">
	                                <tr>
	                                    <th style="width:20%"><?php echo __("Date") ?></th>
	                                    <th style="width:30%"><?php echo __("Employee") ?></th>
	                                    <th style="width:50%"><?php echo __("Comment") ?></th>
	                                </tr>
	                                <?php
	                                $i = 1;
	                                foreach ($performanceReview->getPerformanceReviewComment() as $comment) {
	                                    ?>
	                                    <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
	                                        <td ><?php echo set_datepicker_date_format($comment->getCreateDate()) ?></td>
	                                        <td ><?php echo ($comment->getEmployee()->getFullName() != '') ? $comment->getEmployee()->getFullName() : __('Admin') ?></td>
	                                        <td ><?php echo nl2br($comment->getComment())?></td>
	                                    </tr>
	                                    <?php
	                                    $i++;
	                                }
	                                ?>
	                            </table>
	                        </li>
	                    <?php } ?>
	                    
	                </ol>
	            </div> <!-- review information inner -->
            </div> <!-- Review information end-->
            
            
            <br/><br/>
            
            <input type="hidden" name="validRate" id="validRate" value="1" />
           
  			<div id="tab-container" class='tab-container'>
  			
	  			<!-- KPI's box starts here --> 
	  				<div class="head">
						<h1><?php echo __("Section 2 : Performance Evaluation") ?></h1>
					</div>
					<br/>
					<div class="head">
						<h1><?php echo __("2.1 KPI's") ?></h1>
					</div>
					
						<ul class='etabs'>
						   <li class='tab'><a href="#selfReview">Self Review</a></li>
						   <?php
						   	if($showReviewers){ 
				 foreach ($performanceReview->getAllReviewers() as $reviewer) {
                     
						   ?>
						   <li class='tab'>
			   	<a href="#<?php echo $reviewer->getReviewerId() ?>-<?php echo $reviewer->getId(); ?>"><?php echo $reviewer->getReviewer()->getFirstAndLastNames(); ?><?php if ($reviewer->isPrimaryReviewer() && (empty($reviewer->isDeleted) || !$reviewer->isDeleted)){?> * <?php } ?><?php if ($reviewer->isDeleted){?> [-] <?php } ?>  </a>
						   	
						   </li>
						   <?php }
						   	} ?>
					 	</ul>
					 	
					 	<div class='panel-container'>
			   				<div id="selfReview">
				   				 <div style="float:right">
				   				 	<p align="left">NA  - Not Applicable <br/></p>
				   				 </div>
					  			 <table name="kpiForm" class="table"  >
					             <thead>
					                <tr>
					                    <th scope="col"><?php echo __("Key Performance Indicator Title") ?></th>
					                    <th scope="col"><?php echo __("Key Performance Indicator Description") ?></th>
					                    <th scope="col"><?php echo __("Rating") ?></th>
					                    <th scope="col"><?php echo __("Self Rating") ?></th>
					                    <th scope="col"><?php echo __("Self Review Comments") ?></th>
					                </tr>
					            </thead>
				       			 <tbody>
					                <?php
					                $i = 1;
					                foreach ($performanceReview->getKpiList() as $kpi) {
					                    ?>
					                    <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
					                    	<td >
					                            <?php echo nl2br($kpi->getKpiTitle()) ?>
					                        </td>
					                        <td >
					                        <div id="msgtooltip<?php echo $kpi->getId(); ?>"  class="msgposition messages" style="display: none;">
                							 </div>
					                            <?php echo nl2br($kpi->getKpi()) ?>
					                        </td>
					                        <td >
					                        	<a href="#" id="<?php echo $kpi->getId(); ?>" onmouseover="showRatingDesc(this)" onmouseout="hidefetchedRatings()">
					                            	<?php echo ($kpi->getMinRate() != '') ? $kpi->getMinRate() : '-' ?> - <?php echo ($kpi->getMaxRate() != '') ? $kpi->getMaxRate() : '-' ?>
					                            </a>
					                        </td>
					                        <td>
					                        	<?php if($loggedEmpId == $performanceReview->getEmployeeId()){ ?>
						                            <input type="hidden" name="max<?php echo $kpi->getId() ?>" id="max<?php echo $kpi->getId() ?>" value="<?php echo $kpi->getMaxRate() ?>" />
						                            <input type="hidden" name="min<?php echo $kpi->getId() ?>" id="min<?php echo $kpi->getId() ?>" value="<?php echo $kpi->getMinRate() ?>" />
						                            <input id="txtSelfRate<?php echo $kpi->getId() ?>"  name="txtSelfRate[<?php echo $kpi->getId() ?>]" type="text"  class="smallInput" 
						                            value="<?php echo trim($kpi->getSelfRate()) ?>"  maxscale="<?php echo $kpi->getMaxRate() ?>" 
						                            minscale="<?php echo $kpi->getMinRate() ?>" valiadate="1" maxlength='3' <?php echo ($disableAll) ? 'disabled' : ''; ?>/>
						                            <span class="validation-error"></span>
					                            <?php }else {
                                                        if($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED) {
                                                                echo "";
                                                            } else {
                                                                echo nl2br($kpi->getSelfRate());
                                                            }
					                               } ?>
					                        </td>
					                        <td class="">
						                        <?php if($loggedEmpId == $performanceReview->getEmployeeId()){ ?>
						                            <textarea id='txtSelfComments' class="reviwerComment" name='txtSelfComments[<?php echo $kpi->getId() ?>]'
						                                      rows="2" cols="40"  <?php echo ($disableAll) ? 'disabled' : ''; ?>><?php echo trim(html_entity_decode($kpi->getSelfComment())); ?></textarea>
						                            <span class="validation-error"></span>
					                            <?php }else {
                                                        if($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED) {
                                                            echo "";
                                                        } else {
                                                            echo html_entity_decode(nl2br($kpi->getSelfComment()));
                                                        }
                                                   } ?>
					                        </td>
											
					                      </tr>
					                 <?php $i++;
					                	}
					                ?>
					                </tbody>
				    			</table>
	      	</div>
            <!-- Added By: Shagupta Faras
                 DESC: To display deleted reviewer's comment
            -->
					        <?php
					        if($showReviewers){
				foreach ($performanceReview->getAllReviewers() as $reviewer) {
									  $reviewerKpiList = $reviewer->getKpiList();
									  $isPrimaryReviewer = $reviewer->isPrimaryReviewer();
							?>               
							 	<div id="<?php echo $reviewer->getReviewerId() ?>-<?php echo $reviewer->getId(); ?>">
							 	 <div style="float:right">
				   				 	<p align="left">NA  - Not Applicable<br/></p>
				   				 </div> 
				           		 <table  name="kpiForm" class="table">
					                <thead>
					                    <tr>
					                        <th scope="col"><?php echo __("Key Performance Indicator Title") ?></th>
					                        <th scope="col"><?php echo __("Key Performance Indicator Description") ?></th>
					                        <th scope="col"><?php echo __("Rating") ?></th>
					                        <th scope="col"><?php echo __("Self Rating") ?></th>
					                        <th scope="col"><?php echo __("Self Review Comments") ?></th>
					                        <?php if($reviewer->isPrimaryReviewer()){ ?>
					                        <th scope="col" ><?php echo __("Reviewer Rating") ?></th>
					                        <?php } ?>
					                        <th scope="col" ><?php echo __("Reviewer Comments") ?></th>
					                    </tr>
					                </thead>
					                <tbody>
					                    <?php
					                    	$i = 1;
					                    	foreach ($reviewerKpiList as $kpi) {
					                       ?>
					                        <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
					                        	<td >
					                                <?php echo $kpi->getKpiTitle() ?>
					                            </td>
					                            <td >
					                            <div id="selfGoalsRating<?php echo $kpi->getId(); ?>" class="tipposition messages" style="display:none;"> </div>
					                                <?php echo nl2br($kpi->getKpi()) ?>
					                            </td>
					                            <td >
					                            	<a href="#" id="<?php echo $kpi->getId(); ?>" onmouseover="selfGoalRatingDesc('<?php echo $kpi->getId(); ?>')" onmouseout="hidefetchedRatings()">
					                               	 <?php echo ($kpi->getMinRate() != '') ? $kpi->getMinRate() : '-' ?> - <?php echo ($kpi->getMaxRate() != '') ? $kpi->getMaxRate() : '-' ?>
					                                </a>
					                            </td>
					                            <td >
					                                <?php   if($performanceReview->getState() != PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED) { 
					                                			echo nl2br($kpi->getSelfRate());
					                                		} else {
																echo "";
															} 
													?>
					                            </td>
					                            <td class="">
					                            	<?php if($performanceReview->getState() != PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED) {
					                             				echo html_entity_decode(nl2br($kpi->getSelfComment()));
					                             			} else {
					                             				echo "";
					                             			} ?>
					                            </td>
					                            <?php if($isPrimaryReviewer){ ?>
												<td>
													<?php if($loggedEmpId == $reviewer->getReviewerId()){ ?>
						                                <input type="hidden" name="max<?php echo $kpi->getId() ?>" id="max<?php echo $kpi->getId() ?>" value="<?php echo $kpi->getMaxRate() ?>" />
						                                <input type="hidden" name="min<?php echo $kpi->getId() ?>" id="min<?php echo $kpi->getId() ?>" value="<?php echo $kpi->getMinRate() ?>" />
						                                <input id="txtReviewerRate<?php echo $kpi->getId() ?>"   reviewer="<?php echo $reviewer->getReviewerId() ?>" 
						                                name="txtReviewerRate[<?php echo $kpi->getId()."_".$reviewer->getId() ?>]" type="text"  class="smallInput" 
						                                value="<?php echo trim($kpi->getRate()) ?>"  maxscale="<?php echo $kpi->getMaxRate() ?>" minscale="<?php echo $kpi->getMinRate() ?>" 
						                                valiadate="1" maxlength='3'  <?php echo ($disableAll) ? 'disabled' : ''; ?> />
						                                <span class="validation-error"></span>
					                                <?php }else{
					                                		echo nl2br($kpi->getRate());
					                                	 } ?>
					                            </td>
					                            <?php } ?>
					                            <td class="">
					                           	 <?php if($loggedEmpId == $reviewer->getReviewerId()){ ?>
					                                <textarea id='txtReviewerComments' class="reviwerComment" reviewer="<?php echo $reviewer->getReviewerId() ?>" name='txtReviewerComments[<?php echo $kpi->getId()."_".$reviewer->getId() ?>]'
					                                          rows="2" cols="40"  <?php echo ($disableAll) ? 'disabled' : ''; ?>><?php echo trim(html_entity_decode($kpi->getComment())); ?></textarea>
					                                <span class="validation-error"></span>
					                             <?php }else{ 
					                             			echo html_entity_decode(nl2br($kpi->getComment()));
					                            	   } ?>
					                            </td>
					                          </tr>
					                     <?php $i++;
					                    	}
					                    ?>
					                    </tbody>
				            		</table>
				            	</div>
				            	<?php } 
					        }            	?>
	           			</div> <!--  end of panel container -->
	           
				<!-- KPI's box ends here -->
				
				
				
				 <!-- goals box starts here -->
            	<div class="box minilist remove-margin">
					<div class="head">
						<h1><?php echo __("2.2 Goals") ?></h1>
					</div>
		            <div class="inner">
            	
		            	<table class="table" style="width:100%">
		            		<tbody> 
		            		<?php $i++; ?>              
		                    <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
		                        <td width="50%" style="text-align:left">
		                        	<div id = "goals">
			                        	<strong>Last 6 month Goals:<br/>
			                        	<div id="goalText">
				                        	<?php
				                        		$previousGoal = $performanceReview->getPreviousObjective(); 
				                        		 if(!empty($previousGoal)){
				                        		 	echo nl2br($performanceReview->getPreviousObjective());
				                        		}else{
				                        			echo __('[Not Defined]');
				                        		}
				                        	 ?>
				                        </div>
			                        	</strong>
		                        	</div>
		                        	<div id="editGoalContainer" style = "display: none;">
		                        		<Strong>Enter Goals:</Strong><br/>
 			                            <textarea id='editGoal' name='editGoal' class="formTextArea" rows="8" cols="50"><?php echo trim($performanceReview->getPreviousObjective()); ?></textarea></br>
 			                            <input type="button" id ="btnGoalSave" value ="save" />
			                        </div>
			                        <?php if($isPrimary && $performanceReview->getState() < PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED) { ?>
				                        <div id="editButton" >
				                        	<a href="#" class = "link">Edit</a>
				                        </div>
				                    <?php } ?>
		                        </td>
		                        <td width="50%">
		                        <?php echo __("Performance Summary - Against last 6 month goals") ?> <strong>[<em>To be filled by Reviewer only</em>]</strong><br />
		                            <textarea id='reviewSummary' name='reviewSummary' class="formTextArea" rows="8" cols="50" <?php echo ((!$isReviwer ) || $disableAll) ? 'disabled' : ''; ?> ><?php echo ($showReviewers) ? trim($performanceReview->getReviewSummary()) : ''; ?></textarea>
		                            <span class="validation-error"></span>
		                        </td>
		                    </tr>
		                    </tbody>
		            	</table>
            
            		</div> <!-- end of goals inner -->
            	</div> <!-- end of goals box -->
            	
           		
           		<!-- accomplishments box starts here -->
           		<div class="box minilist remove-margin">
					<div class="head">
						<h1><?php echo __("2.3 Accomplishments") ?></h1>
					</div>
		            <div class="inner">
			            <table class="table" style="width:100%">
			            	<tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
				            	<td width="50%"  style="text-align:left"><?php echo __("Major Accomplishments") ?><br/><strong>[<em>To be filled by Employee only</em>]</strong></td>
				            	<td width="50%"  style="text-align:left"><?php echo __("Feedback on accomplishments") ?><br/><strong>[<em>To be filled by Reviewer only</em>]</strong></td>
				            </tr>
			            	<?php $i++; ?>
			            	<tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
				            	<td width="50%">
				            		 <textarea id='major_accomplishments' name='major_accomplishments' class="formTextArea" rows="5" cols="50" <?php echo ($isReviwer || $isHrAdmin || $disableAll) ? 'disabled' : ''; ?>><?php echo ($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED && !$isSelfReview) ? "" : trim($performanceReview->getMajorAccomplishments()); ?></textarea>
			                         <span class="validation-error"></span>
				            	</td>
				            	<td width="50%">
				            		 <textarea id='feedback_on_accomplishments' name='feedback_on_accomplishments' class="formTextArea" rows="5" cols="50" <?php echo ((!$isReviwer) || $disableAll) ? 'disabled' : ''; ?> ><?php echo ($showReviewers) ? trim($performanceReview->getFeedbackOnAccomplishments()) : ''; ?></textarea>
			                         <span class="validation-error"></span>
				            	</td>
				            </tr>
			            	<?php $i++; ?>
			            </table>
		            </div> <!-- end of accomplishments inner  -->
	            </div> <!-- end of accomplishments box -->
            	
            	
            	<!-- 360 degree box starts here -->
            	
	            			
	            <?php	if ($isHrAdmin || $isReviwer) { ?>
	                <div class="box minilist remove-margin">
            			<div class="head">
							<h1><?php echo __("2.4 360 degree feedback") ?></h1>
						</div>
						<div class="inner">
							<table class="table" style="width:30%">
		            			<tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
		                        	<td><?php echo __("360 Feedback") ?></td>
			                        <td class="line">
			                        	<?php if(count($selfMultiFeedbackList) > 0) { ?>
			                        		<a href="<?php echo url_for('performance/viewMultiSourceFeedback?eid='.$performanceReview->getEmployeeId().'&reviewId='.$performanceReview->getId()); ?>" target="_blank">
			                                       <?php echo (set_datepicker_date_format($performanceReview->getPeriodFrom()). " - ". set_datepicker_date_format($performanceReview->getPeriodTo()) )?>
			                                </a>
			                        	<?php } else {
			                        		echo __('No Feedback Found');
			                        	 } ?>
			                        </td>
								</tr>
							</table>
						</div>
            		</div> <!-- end of 360 degree box -->
	           <?php } ?>
	                    
	                    
            	
            	<br /><br />
            	
            	<!-- reviewer's feedback starts here -->
            	<div class="box minilist remove-margin">
	            	<div class="head">
							<h1><?php echo __("Section 3 : Reviewer's feedback") ?></h1>
	            	</div>
	            	<div class="inner">
	            		<table class="table" style="width:100%">
	            				<tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
					            	<td width="50%" style="text-align:left"><?php echo __("Strong Points of Employee") ?><br/><strong>[<em>To be filled by Reviewer only</em>]</strong></td>
					            	<td width="50%" style="text-align:left"><?php echo __("Noticed improvement") ?><br/><strong>[<em>To be filled by Reviewer only</em>]</strong></td>
					            </tr>
				            	<?php $i++; ?>
				            	<tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
					            	<td width="50%">
					            		 <textarea id='strong_points' name='strong_points' class="formTextArea" rows="5" cols="50" <?php echo ((!$isReviwer) || $disableAll) ? 'disabled' : ''; ?> ><?php echo ($showReviewers) ? trim($performanceReview->getStrongPoints()) : ''; ?></textarea>
				                         <span class="validation-error"></span>
					            	</td>
					            	<td width="50%">
					            		 <textarea id='noticed_improvement' name='noticed_improvement' class="formTextArea" rows="5" cols="50" <?php echo ((!$isReviwer) || $disableAll) ? 'disabled' : ''; ?> ><?php echo ($showReviewers) ? trim($performanceReview->getNoticedImprovements()) : ''; ?></textarea>
				                         <span class="validation-error"></span>
					            	</td>
					            </tr>
				            	<?php $i++; ?>
				            	<tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
			                        <td width="50%" style="text-align:left"><?php echo __("Final Grade") ?></td>
			                        <td width="50%">
			                            <input id="txtfinalRating"  name="txtfinalRating" type="text" class="smallInput"
			                             value="<?php echo ($showReviewers && $isPrimaryReviwerCommented) ? trim($performanceReview->getFinalRating()) : ''; ?>"  disabled /> 
			                            <span class="validation-error"></span>
			                            <input type="hidden" name="finalRating" id="finalRating" value="<?php echo trim($performanceReview->getFinalRating())?>" />
			                        </td>
	                    		</tr>   
				          </table>
	            	
	            	</div><!-- reviewer's feedback inner end -->
            	</div> <!-- reviewer's feedback end -->
            	
            	
            	<br /><br />
            	
	                <!-- goals setting starts here -->
	                <div class="box minilist remove-margin">
						<div class="head">
							<h1><?php echo __("Section 4 : Goal setting for upcoming review period") ?></h1>
						</div>
		            	<div class="inner">
            	
			            	<table class="table" style="width:100%">
			            		<tbody> 
			                    
			                    <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
			                        <td width="50%" style="text-align:left"><?php echo __("Objectives for Next 6 months ") ?>
			                        	<strong>[<em>To be filled by Reviewer only</em>]</strong>
			                        </td>
			                        <td width="50%">
			                            <textarea id='objective' name='objective' class="formTextArea" rows="8" cols="50" <?php echo ((!$isReviwer) || $disableAll) ? 'disabled' : ''; ?> ><?php echo ($showReviewers) ? trim($performanceReview->getObjective()) : ''; ?></textarea>
			                            <span class="validation-error"></span>
			                        </td>
			                    </tr>
			                    
			                     </tbody>
			            	</table>
            
            			</div> <!-- end of goal settings inner -->
            	</div> <!-- end of goal settings box -->
            	<br /><br />
            	
            	<!-- feedback starts here -->
            	<div class="box minilist remove-margin">
					<div class="head">
						<h1><?php echo __("Section 5 : Feedback") ?></h1>
					</div>
		            <div class="inner">
            	
		            	<table class="table" style="width:100%">
		            		<tbody> 
			                    <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
			                        <td width="50%" style="text-align:left"><?php echo __("Employee Feedback (aspirations, grievances etc.) ") ?><strong>[<em>To be filled by Employee only</em>]</strong></td>
			                        <td width="50%">
			                            <textarea id='employee_feedback' name='employee_feedback' class="formTextArea" rows="6" cols="50" <?php echo ($isReviwer || $isHrAdmin || $disableAll) ? 'disabled' : ''; ?> ><?php echo ($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED && !$isSelfReview) ? "" : trim($performanceReview->getEmployeeFeedback()); ?></textarea>
			                            <span class="validation-error"></span>
			                        </td>
			                    </tr>
			                <?php
			            	 $i++;
				            	if (($isHrAdmin || $isReviwer) && $showButton) :
			                ?>  
			                   <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
			                        <td width="50%" style="text-align:left"><?php echo __("Notes ") ?><strong>[<em>Reviewer & Admin can add multiple notes. These notes not visible to employee</em>]</strong></td>
			                        <td width="50%">
                                        <!-- Fixed HRMS-280 -->
			                            <textarea id='txtNotes' name='txtNotes' class="formTextArea" rows="4" cols="50" <?php echo (!$isReviwer && !$isHrAdmin) ? 'disabled' : ''; ?>></textarea>
			                            <span class="validation-error"></span>
			                        </td>
			                    </tr>    
			                  <?php endif; ?>       
			                	
	            			</tbody>
		            	</table>
		            
            		</div> <!-- end of feedback inner -->
            	</div> <!-- end of feedback box -->
            	<br></br>
            	
            	<!-- goals accept/reject starts here -->
            	<?php if(($isSelfReview || $isHrAdmin) && ($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED)) {?>
	            	<div class="box minilist remove-margin">
						<div class="head">
							<h1><?php echo __("Section 6 : Discrepancy on Appraisal") ?></h1>
						</div>
			            <div class="inner">
	            	
			            	<table class="table" style="width:100%">
			            		<tbody> 
			            		<?php $i++; ?>              
			                    <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
			                        <td width="50%" style="text-align:left">
				                        <strong>Fill Reasons here </strong>&nbsp;[ Notification will be sent to HR ]<span class="required">*</span>
			                        </td>
			                        <td width="50%">
			                            <textarea id='reject_comments' name='reject_comments' class="formTextArea" rows="8" cols="50" <?php echo ($isHrAdmin)? 'disabled' : ''; ?>><?php echo ($showReviewers) ? trim($performanceReview->getRejectComments()) : ''; ?></textarea><span class="validation-error" id="comments-reject"></span></br>
			                            <?php if(!$isHrAdmin) { ?>
			                            	<input type = "button" value="<?php echo __("Submit") ?>" id = "btnSubmitFeedback"/>
			                            <?php } ?>
			                        </td>
			                    </tr>
			                    </tbody>
			            	</table>
	            
	            		</div> <!-- end of accept/reject inner -->
	            	</div> <!-- end of accept/reject box -->
            	<?php } ?>
            	<br/>
           
            
	            <p style="margin-top:10px">
	            	<?php if($showButton){ ?>
		                <?php if ((($performanceReview->getState() <= PerformanceReview::PERFORMANCE_REVIEW_STATUS_BEING_REVIWED && !($isSelfReview))
		                		|| $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_REJECTED) 
		                		|| ($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED && $isSelfReview)
		                		|| ($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED && $isHrAdmin)) { ?>
		                	<?php //if(!$isHrAdmin && $performanceReview->getState() != PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED && !$isReviwer) { ?>
		                    	<input type="button" class="" id="saveBtn" value="<?php echo __("Save Draft") ?>" title="This will save the changes you have made without going to the next step in workflow"  />
		                    <?php //}?>
		                <?php } ?>
		                <?php 
                            $showSubmitButton = False;
                            if ($isSelfReview 
                                && $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED) {
                                $showSubmitButton = True;
                            } else if (!$isSelfReview) {
                                if ($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_BEING_REVIWED
                                    || $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_REJECTED) {
                                    if (($isPrimary || ($isHrAdmin && $isPrimaryReviwerCommented))) {
                                        $showSubmitButton = True;
                                    }
                                }
                            }
                            if ($showSubmitButton) { ?>                        
		                      <input type="button" class="" title="Your changes will be saved and form will be moved to the next step in the workflow" id="submitBtn" value="<?php echo __("Submit") ?>"/>
                            <?php } ?>		              
		                <?php if ($isHrAdmin && $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED) { ?>
		                    <input type="button" class="delete" id="rejectBtn" value="<?php echo __("Reject") ?>"  />
		                <?php } ?>
		
		                <?php if ($isHrAdmin && ( $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED )) { ?>
		                    <input type="button" class="" id="approveBtn" value="<?php echo __("Approve") ?>"  />
		                <?php } ?>
					<?php } ?> 
	                <input type="button" class="reset" id="backBtn" value="<?php echo __("Back"); ?>" />
                     <!--                    
                     Modification DESC: To fix HRMS-267 [Admin & primary reviewer can only submit the apprisal form.]
                     -->
	                <?php 
                 $noteStr=='';                 
                 if ($isHrAdmin && ( $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED || $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_BEING_REVIWED || $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_REJECTED)) { ?>                 
                 <?php $noteStr.='Once submitted the reviewers will not be able to edit anything.';
                   } ?>
                 <?php if (($isHrAdmin) && ( $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED || $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_BEING_REVIWED || $performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_REJECTED) && !$isPrimaryReviwerCommented && $showButton) {
                        if(!empty($noteStr))
                        $noteStr.='<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Submit button will be available, only if rating is given for all KPIs';
                     }
                     if(!empty($noteStr)) { ?>
                <p><strong>[<em><u>Note:</u>&nbsp;<?php echo $noteStr;?></em>]</strong> </p>
                     <?php } ?>            
	           
	           
            </div> <!-- tab container -->
        </form> <!-- form -->
     <br />   
	 <?php
	 	 echo include_component('performance', 'attachments', array('id' => $performanceReview->getId()));
	 ?>
    </div> <!-- inner -->
	
</div> <!-- performanceReviewcontentContainer -->


<!-- Rating dialog -->
<div class="modal hide large" id="ratingDialog">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo __('Rating Description'); ?></h3>
  </div>
  <div class="modal-body">
    <p>
     <div id="ratings">  
         <span><?php echo __('Loading') . '...';?></span>
     </div>
    </p>
  </div>
  <div class="modal-footer">
    <input type="button" class="btn reset" data-dismiss="modal" id="ratingCancel" value="<?php echo __('Close'); ?>" />
  </div>
</div>
<!-- end of Rating dialog-->

<!-- Confirmation box - Submit HTML: Begins -->
<div class="modal hide" id="submitConfirmation">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('SynerzipHRM - Confirmation Required'); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo __('Your changes will be saved and appraisal form will be moved to the next step in the workflow.'); ?></p><br />
        <p><?php echo __('Note: This action cannot be undone.'); ?></p>
        <br>
        <p><?php echo __('Are you sure?'); ?></p>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" data-dismiss="modal" id="dialogSubmitBtn" value="<?php echo __('Yes'); ?>" />
        <input type="button" class="btn reset" data-dismiss="modal" value="<?php echo __('No'); ?>" />
    </div>
</div>

<div id="domMessage" style="display: none;">We are processing your
	request. Please be patient.</div>
<!-- Reject Confirmation box HTML: Begins for the Appraisal -->
<div class="modal hide" id="rejectConfirmation"
	style="margin-top: -150px;">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">�</a>
		<h3><?php echo __('SynerzipHRMS - Confirmation Required'); ?></h3>
	</div>
	<div class="modal-body">
		<p><?php echo __('Do you want to reject Appraisal?'); ?></p>
	</div>
	<div class="modal-footer">
		<input type="button" class="btn" data-dismiss="modal"
			id="dialogRejectBtn" value="<?php echo __('Ok'); ?>" /> <input
			type="button" class="btn reset" data-dismiss="modal"
			value="<?php echo __('Cancel'); ?>" />
	</div>
</div>
<!-- Reject Confirmation box HTML: Ends -->
