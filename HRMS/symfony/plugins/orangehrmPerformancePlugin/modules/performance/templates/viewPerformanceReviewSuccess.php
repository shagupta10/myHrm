<?php use_stylesheet(plugin_web_path('orangehrmPerformancePlugin', 'css/viewPerformanceReviewSuccess'))?>
<?php use_javascript(plugin_web_path('orangehrmPerformancePlugin', 'js/viewPerformanceReviewSuccess'))?>

<div class="box miniList" id="performanceReviewcontentContainer">

    <div class="head" id="formHeading" >
        <h1><?php echo __("Performance Review") ?></h1>
    </div>

    <div class="inner">
     <form action="#" id="frmSave" class="content_inner" method="post">
            <?php echo $form['_csrf_token']; ?>
            <fieldset>
                <ol>
                    <li>
                        <label><?php echo __("Employee") ?></label>
                        <label class="line"><?php echo $performanceReview->getEmployee()->getFirstName() ?> 
                            <?php echo $performanceReview->getEmployee()->getLastName() ?></label>
                    </li>
                    <li>
                        <label><?php echo __("Job Title") ?></label>
                        <label class="line"><?php echo $performanceReview->getJobTitle()->getJobTitleName(); ?> </label>
                    </li>
                    <li>
                        <label><?php echo __("Reviewer") ?></label>
                        <label class="line"><?php echo $reviewerNames ?></label>
                    </li>
                    <li>
                        <label><?php echo __("Review Period") ?></label>
                        <label class="line"><?php echo set_datepicker_date_format($performanceReview->getPeriodFrom()) ?>-<?php echo set_datepicker_date_format($performanceReview->getPeriodTo()) ?></label>
                    </li>
                    <li>
                        <label><?php echo __("Status") ?></label>
                        <label class="line"><?php echo __($performanceReview->getTextStatus()) ?> </label>
                    </li>
                     <?php	if ($isHrAdmin || $isReviwer) { ?>
                     <li>
                        <label><?php echo __("360 Feedback") ?></label>
                        <label class="line">
                        	<?php if(count($selfMultiFeedbackList) > 0) { ?>
                        		<a href="<?php echo url_for('performance/viewMultiSourceFeedback?eid='.$performanceReview->getEmployeeId()); ?>" target="_blank">
                                       <?php echo (set_datepicker_date_format($performanceReview->getPeriodFrom()). " - ". set_datepicker_date_format($performanceReview->getPeriodTo()) )?>
                                </a>
                        	<?php } else {
                        		echo __('No Feedback');
                        	 } ?>
                        </label>
                    </li>
                    <?php } ?>
                    
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
            </fieldset>
            
            <input type="hidden" name="validRate" id="validRate" value="1" />
           
  			<div id="tab-container" class='tab-container'>
  			
			 <ul class='etabs'>
			   <li class='tab'><a href="#selfReview">Self Review</a></li>
			   <?php
				foreach ($performanceReview->getReviewers() as $reviewer) {
			   ?>
			   <li class='tab'>
			   	<a href="#<?php echo $reviewer->getReviewerId() ?>-<?php echo $reviewer->getId(); ?>"><?php echo $reviewer->getReviewer()->getFirstAndLastNames(); ?><?php if ($reviewer->isPrimaryReviewer()){?> * <?php } ?> </a>
			   	
			   </li>
			   <?php } ?>
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
	                    <th scope="col"><?php echo __("Self Apprisal Comments") ?></th>
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
	                        	<div id="msgtooltip<?php echo $kpi->getId() ?>"  class="msgposition messages" style="display: none;"></div>
	                            <?php echo nl2br($kpi->getKpi()) ?>
	                        </td>
	                        <td >
	                        	<a href="#" id="<?php echo $kpi->getId();?>" onmouseover="showRatingDesc(this)"  onmouseout="hidefetchedRatings()">
	                        	<!--<a href="#" onClick="showRatingDesc(<?php //echo $kpi->getId(); ?>)">-->
	                            	<?php echo ($kpi->getMinRate() != '') ? $kpi->getMinRate() : '-' ?> - <?php echo ($kpi->getMaxRate() != '') ? $kpi->getMaxRate() : '-' ?>
	                            </a>
	                        </td>
	                        <td>
	                        	<?php echo nl2br($kpi->getSelfRate()); ?>
	                        </td>
	                        <td class="">
		                        <?php echo html_entity_decode(nl2br($kpi->getSelfComment())); ?> 
	                        </td>
							
	                      </tr>
	                 <?php $i++;
	                	}
	                ?>
	                </tbody>
    			</table>
	      	</div>
      		
	        <?php
				foreach ($performanceReview->getReviewers() as $reviewer) {
					  $reviewerKpiList = $reviewer->getKpiList();
					  $isPrimaryReviewer = $reviewer->isPrimaryReviewer();
			?>               
			 <div id="<?php echo $reviewer->getReviewerId() ?>-<?php echo $reviewer->getId() ?>">
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
	                        <th scope="col"><?php echo __("Self Apprisal Comments") ?></th>
	                        <th scope="col" ><?php echo __("Reviewer Rating") ?></th>
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
	                            	<div id="selfGoalsRating<?php echo $kpi->getId() ?>"  class="msgposition messages" style="display: none;"></div>
	                                <?php echo nl2br($kpi->getKpi()) ?>
	                            </td>
	                            <td >
	                            	<a href="#" id="<?php echo $kpi->getId();?>" onmouseover="selfGoalRatingDesc('<?php echo $kpi->getId(); ?>')"  onmouseout="hidefetchedRatings()">
	                            	<!--<a href="#" onClick="showRatingDesc(<?php echo $kpi->getId(); ?>)">-->
	                               	 <?php echo ($kpi->getMinRate() != '') ? $kpi->getMinRate() : '-' ?> - <?php echo ($kpi->getMaxRate() != '') ? $kpi->getMaxRate() : '-' ?>
	                                </a>
	                            </td>
	                            <td >
	                                <?php echo nl2br($kpi->getSelfRate()) ?>
	                            </td>
	                            <td class="">
	                            	<?php echo html_entity_decode(nl2br($kpi->getSelfComment())) ?>
	                            </td>
	                            <td><?php echo nl2br($kpi->getRate()); ?></td>
	                            
	                            <td class=""><?php echo html_entity_decode(nl2br($kpi->getComment())) ?></td>
	                          </tr>
	                     <?php $i++;
	                    	}
	                    ?>
	                    </tbody>
            		</table>
            	</div>
            	<?php } ?>
           </div>
           		
           		<!-- accomplishments box starts here -->
				<div class="box minilist remove-margin">
					<div class="head">
						<h1><?php echo __(" Accomplishments") ?></h1>
					</div>
					<div class="inner">
						<table class="table" style="width: 100%">
							<tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
								<td width="50%" style="text-align: left"><?php echo __("Major Accomplishments") ?><br />
									<strong>[<em>To be filled by Employee only</em>]
								</strong></td>
								
							</tr>
			            	<?php $i++; ?>
			            	<tr	class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
								<td width="50%">
								<?php echo trim(nl2br($performanceReview->getMajorAccomplishments())); ?>
								</td>
								
							</tr>
			            	<?php $i++; ?>
			            </table>
					</div>
					<!-- end of accomplishments inner  -->
					</div>
				<!-- end of accomplishments box -->
				
				<!-- reviewer's feedback starts here -->
				<div class="box minilist remove-margin">
					<div class="head">
						<h1><?php echo __(" Reviewer's feedback") ?></h1>
					</div>
					<div class="inner">
						<table class="table" style="width: 100%">
							<tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
								<td width="50%" style="text-align: left"><?php echo __("Strong Points of Employee") ?><br />
									<strong>[<em>To be filled by Reviewer only</em>]
								</strong></td>
								<td width="50%" style="text-align: left"><?php echo __("Noticed improvement") ?><br />
									<strong>[<em>To be filled by Reviewer only</em>]
								</strong></td>
							</tr>
				            	<?php $i++; ?>
				            	<tr	class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
								<td width="50%"><?php echo trim(nl2br($performanceReview->getStrongPoints())); ?></td>
								<td width="50%"><?php echo trim(nl2br($performanceReview->getNoticedImprovements())); ?></td>
							</tr>
				            	<?php $i++; ?>
				            	<tr	class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
								<td width="50%" style="text-align: left"><?php echo __("Final Grade") ?></td>
								<td width="50%"><?php echo trim($performanceReview->getFinalRating());?> 
								</td>
							</tr>
						</table>

					</div>
					<!-- reviewer's feedback inner end -->
				</div>
				<!-- reviewer's feedback end -->
				
            	<!-- goals box starts here -->
				<div class="box minilist remove-margin">
					<div class="head">
						<h1><?php echo __(" Goals") ?></h1>
					</div>
					<div class="inner">

						<table class="table" style="width: 100%">
							<tbody> 
		            		<?php $i++; ?>              
		                    <tr	class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
									<td width="50%" style="text-align: left">
										<div id="goals">
											<strong>Last 6 month Goals:<br />
												<div id="goalText">
				                        	<?php
												$previousGoal = $performanceReview->getPreviousObjective ();
												if (! empty ( $previousGoal )) {
													echo nl2br ( $performanceReview->getPreviousObjective () );
												} else {
														echo __ ( '[Not Defined]' );
													}?>
											</div>
											</strong>
										</div>
								  </td>
									<td width="50%">
		                        	<?php echo __("Performance Summary - Against last 6 month goals") ?> <strong>[<em>To
												be filled by Reviewer only</em>]
									<?php echo trim(nl2br($performanceReview->getReviewSummary())); ?>
									</td>
								</tr>
							</tbody>
						</table>

					</div>
					<!-- end of goals inner -->
				</div>
				<!-- end of goals box -->
				
				<!-- goals setting starts here -->
				<div class="box minilist remove-margin">
					<div class="head">
						<h1><?php echo __(" Goal setting for upcoming review period") ?></h1>
					</div>
					<div class="inner">

						<table class="table" style="width: 100%">
							<tbody>

								<tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
									<td width="50%" style="text-align: left"><?php echo __("Objectives for Next 6 months ")?>
			                        	<strong>[<em>To be filled by Reviewer only</em>]
									</strong></td>
									<td width="50%"><?php echo trim(nl2br($performanceReview->getObjective())); ?></td>
								</tr>

							</tbody>
						</table>

					</div>
					<!-- end of goal settings inner -->
				</div>
				<!-- end of goal settings box -->
				
				
				<!-- feedback starts here -->
				<div class="box minilist remove-margin">
					<div class="head">
						<h1><?php echo __(" Feedback") ?></h1>
					</div>
					<div class="inner">

						<table class="table" style="width: 100%">
							<tbody>
								<tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
									<td width="50%" style="text-align: left"><?php echo __("Employee Feedback (aspirations, grievances etc.) ") ?><strong>[<em>To
												be filled by Employee only</em>]
									</strong></td>
									<td width="50%"><?php echo trim(nl2br($performanceReview->getEmployeeFeedback())); ?></td>
								</tr>
			                </tbody>
						</table>

					</div>
					<!-- end of feedback inner -->
				</div>
				<!-- end of feedback box -->
			
     </form> 
	 <?php  echo include_component('performance', 'attachments', array('id' => $performanceReview->getId(),'mode'=> 'view'));   ?>
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


<script type="text/javascript">
    var getKpiRatingsUrl = '<?php echo url_for('performance/getKpiRatings'); ?>';
	$(document).ready(function(){
    	
    	<?php if($isReviwer) { ?>
    		$('#tab-container').easytabs('select', '<?php echo '#'.$loggedEmpId."-".$reviewReviewerId ?>');
    	<?php }else { ?>
    		$('#tab-container').easytabs('select', '#selfReview');
    	<?php }?>
    });
</script>