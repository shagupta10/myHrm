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
    		<td><div id = "goals_<?php echo $reviewer->getReviewerId(); ?>">
				<strong>Last 6 month Goals:<br/>
				<div id="goalText_<?php echo $reviewer->getReviewerId(); ?>">
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
				<div id="editGoalContainer_<?php echo $reviewer->getReviewerId(); ?>" style = "display: none;">
				<Strong>Enter Goals:</Strong><br/>
				<textarea id='editGoal_<?php echo $reviewer->getReviewerId(); ?>' name='editGoal_<?php echo $reviewer->getReviewerId(); ?>' class="formTextArea" rows="6" cols="50"><?php echo trim($performanceReview->getPreviousObjective()); ?></textarea></br>
				<input type="button" id ="btnGoalSave_<?php echo $reviewer->getReviewerId(); ?>" value ="save" onclick="saveGoals('<?php echo $reviewer->getReviewerId(); ?>')" />
				</div>
				<?php if($loggedEmpId == $reviewer->getReviewerId() && $isPrimaryReviewer && $performanceReview->getState() < PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED) { ?>
				    <div id="editButton_<?php echo $reviewer->getReviewerId(); ?>" >
				    	<a href="javascript:void(0)" onclick="showEditGoals('<?php echo $reviewer->getReviewerId();?>')">Edit</a>
				    </div>
				<?php } ?></td>
			<td><a href="#" onmouseover="selfGoalRatingDesc(4)" onmouseout="hidefetchedRatings()">1 - 5</a>
				<div id="selfGoalsRating4" class="messages" style="display:none;"> </div></td>
			<td><?php if($performanceReview->getState() != PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED) { 
            			echo "";
            		} else {
            			echo trim($performanceReview->getReviewRatings()->getSelfGoalsRate());
					} ?>
            </td>
            <td>
            	<?php if($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED) {
            		echo "";
            	} else {
            		echo html_entity_decode(nl2br($performanceReview->getReviewRatings()->getSelfGoalsComment()));
            	} ?>
        	</td>
        	<?php if($reviewer->isPrimaryReviewer()){ ?>
            <td>
            	<?php if($loggedEmpId == $reviewer->getReviewerId()){ ?>
	            	<input id="txtGoalsRating_<?php echo $reviewer->getReviewerId(); ?>"  name="txtGoalsRating_<?php echo $reviewer->getReviewerId(); ?>" type="text" class="smallInput ratingInput" maxscale="5" minscale="1" valiadate="1" maxlength='3' <?php echo ((!$isReviwer) || $disableAll) ? 'disabled' : ''; ?> value="<?php echo trim($reviewer->getReviewRatings()->getGoalsRate())?>"/>
		            <span class="validation-error"></span>
	            <?php }else{
                    	echo nl2br($reviewer->getReviewRatings()->getGoalsRate());
                } ?>
            </td>
            <?php } ?>
            <td>
            <?php if($loggedEmpId == $reviewer->getReviewerId()){ ?>
            	<textarea id='txtGoalsComment_<?php echo $reviewer->getReviewerId(); ?>' class="reviwerComment" name='txtGoalsComment_<?php echo $reviewer->getReviewerId(); ?>' cols="50" rows="6" <?php echo ((!$isReviwer) || $disableAll) ? 'disabled' : ''; ?>><?php echo trim(html_entity_decode($reviewer->getReviewRatings()->getGoalsComment())); ?></textarea>
	        	<span class="validation-error"></span>
	        	<?php }else{
                    	echo trim(html_entity_decode($reviewer->getReviewRatings()->getGoalsComment()));
                 } ?>
	        </td>
    	</tr>
    </tbody>
</table>