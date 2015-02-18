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
    		<td style="width: 30%">List only remarkable achievements, such as - independent delivery of a major component, a technical/process suggestion/initiative which has made considerable improvement in your project, distinctive appreciation received for you work, certification earned relevant for your job role etc. Accomplishments should be evidence based and/or quantifiable.</td>
			<td><a href="#" onmouseover="selfGoalRatingDesc(5)" onmouseout="hidefetchedRatings()">1 - 5</a>
				<div id="selfGoalsRating5" class="messages" style="display:none;"> </div></td>
			<td><?php if($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED) { 
        			echo "";
        		} else {
					echo trim($performanceReview->getReviewRatings()->getSelfAccomplishmentRate());
				} ?>
            </td>
            <td>
            	<?php if($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED) {
            		echo "";
            	} else {
            		echo html_entity_decode(nl2br($performanceReview->getReviewRatings()->getSelfAccomplishmentComment()));
            	} ?>
        	</td>
        	<?php if($reviewer->isPrimaryReviewer()){ ?>
            <td><?php if($loggedEmpId == $reviewer->getReviewerId()){ ?>
            	<input id="txtMajorAccomplishmentRating_<?php echo $reviewer->getReviewerId(); ?>"  name="txtMajorAccomplishmentRating_<?php echo $reviewer->getReviewerId(); ?>" type="text" class="smallInput ratingInput" maxscale="5" minscale="1" valiadate="1" maxlength='3' <?php echo ((!$isReviwer) || $disableAll) ? 'disabled' : ''; ?> value="<?php echo trim($reviewer->getReviewRatings()->getAccomplishmentRate())?>"/>
	        	<span class="validation-error"></span>
	        	<?php }else{
           			echo trim($reviewer->getReviewRatings()->getAccomplishmentRate());
                } ?>
            </td>
            <?php } ?>
            <td><?php if($loggedEmpId == $reviewer->getReviewerId()){ ?>
            	<textarea id='txtMajorAccomplishmentComment_<?php echo $reviewer->getReviewerId(); ?>' class="reviwerComment" name='txtMajorAccomplishmentComment_<?php echo $reviewer->getReviewerId(); ?>' rows="6" cols="50"  <?php echo ((!$isReviwer) || $disableAll) ? 'disabled' : ''; ?> ><?php echo trim(html_entity_decode($reviewer->getReviewRatings()->getAccomplishmentComment())); ?></textarea>
	    		<span class="validation-error"></span>
	    	<?php }else{
           			echo trim($reviewer->getReviewRatings()->getAccomplishmentComment());
                } ?>
	    	</td>
    	</tr>
    </tbody>
</table>