<table name="kpiForm" class="table"  >
	<thead>
        <tr>
            <th scope="col"><?php echo __("Description") ?></th>
            <th scope="col"><?php echo __("Rating") ?></th>
            <th scope="col"><?php echo __("Self Rating") ?></th>
            <th scope="col"><?php echo __("Self Review Comments") ?></th>
        </tr>
    </thead>
    <tbody>
    	<tr>
    		<td><p>Please rate yourself on your perception of what your team thinks of you.</p></td>
			<td><a href="#" onmouseover="selfGoalRatingDesc(3)" onmouseout="hidefetchedRatings()">1 - 5</a>
			<div id="selfGoalsRating3" class="messages" style="display:none;"> </div></td>
			<td><?php if($loggedEmpId == $performanceReview->getEmployeeId()){ ?>
			<input id="txtSelf360FeedbackRating"  name="txtSelf360FeedbackRating" type="text" class="smallInput" maxscale="5" minscale="1" valiadate="1" maxlength='3' <?php echo ($isReviwer || $isHrAdmin || $disableAll) ? 'disabled' : ''; ?> value="<?php echo trim($performanceReview->getReviewRatings()->getSelf360FeedbackRate())?>"/>
				<span class="validation-error"></span>
				<?php }else {
               			if($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED) {
                        	echo "";
                        } else {
                        	echo trim($performanceReview->getReviewRatings()->getSelfAccomplishmentRate());
                        }
            	} ?>
            </td>
			<td><?php if($loggedEmpId == $performanceReview->getEmployeeId()){ ?>
                <textarea id='txtSelf360FeedbackComment'  name='txtSelf360FeedbackComment' class="selfComments reviwerComment" rows="6" cols="50" <?php echo ($isReviwer || $isHrAdmin || $disableAll) ? 'disabled' : ''; ?> ><?php echo trim(html_entity_decode($performanceReview->getReviewRatings()->getSelf360FeedbackComment())); ?></textarea>
                <span class="validation-error"></span>
            <?php }else{
            	if($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED) {
            		echo "";
            	} else {
            		echo html_entity_decode(nl2br($performanceReview->getReviewRatings()->getSelf360FeedbackComment()));
            	}
            } ?>
        	</td>
    	</tr>
    </tbody>
</table>