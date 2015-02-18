<table name="kpiForm" class="table"  >
	<thead>
        <tr>
            <th scope="col"><?php echo __("Description") ?></th>
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
    	<tr class="odd">
    		<td><?php if(count($selfMultiFeedbackList) > 0) { ?>
        		<a href="<?php echo url_for('performance/viewMultiSourceFeedback?eid='.$performanceReview->getEmployeeId().'&reviewId='.$performanceReview->getId()); ?>" target="_blank">
                	<?php echo (set_datepicker_date_format($performanceReview->getPeriodFrom()). " - ". set_datepicker_date_format($performanceReview->getPeriodTo()) )?>
                </a>
        	<?php } else {
        		echo __('No Feedback Found');
        	 } ?></td>
			<td><a href="#" onmouseover="selfGoalRatingDesc(6)" onmouseout="hidefetchedRatings()">1 - 5</a>
				<div id="selfGoalsRating6" class="messages" style="display:none;"> </div></td>
			<td><?php if($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED) { 
        			echo "";
        		} else {
        			echo trim($performanceReview->getReviewRatings()->getSelf360FeedbackRate());
				} ?>
            </td>
            <td><?php if($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED) {
            		echo "";
            	} else {
            		echo html_entity_decode(nl2br($performanceReview->getReviewRatings()->getSelf360FeedbackComment()));
            	} ?>
        	</td>
        	<?php if($reviewer->isPrimaryReviewer()){ ?>
            <td><?php if($loggedEmpId == $reviewer->getReviewerId()){ ?>
            	<input id="txt360FeedbackRating_<?php echo $reviewer->getReviewerId(); ?>"  name="txt360FeedbackRating_<?php echo $reviewer->getReviewerId(); ?>" type="text" class="smallInput ratingInput" maxscale="5" minscale="1" valiadate="1" maxlength='3' <?php echo ((!$isReviwer) || $disableAll) ? 'disabled' : ''; ?> value="<?php echo trim($reviewer->getReviewRatings()->getFeedbackRate())?>"/>
			    <span class="validation-error"></span>
			    <?php }else{
           			echo trim($reviewer->getReviewRatings()->getFeedbackRate());
                } ?>
            </td>
            <?php } ?>
            <td><?php if($loggedEmpId == $reviewer->getReviewerId()){ ?>
            	<textarea id='txt360FeedbackComment_<?php echo $reviewer->getReviewerId(); ?>' rows="6" cols="50" class="reviwerComment" name='txt360FeedbackComment_<?php echo $reviewer->getReviewerId(); ?>' <?php echo ((!$isReviwer) || $disableAll) ? 'disabled' : ''; ?> ><?php echo trim(html_entity_decode($reviewer->getReviewRatings()->getFeedbackComment())); ?></textarea>
		    	<span class="validation-error"></span>
		    <?php }else{
           			echo trim($reviewer->getReviewRatings()->getFeedbackComment());
                } ?>
		    </td>
    	</tr>
    </tbody>
</table>