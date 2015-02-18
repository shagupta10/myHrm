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
    		<td><div id = "goals">
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
				</div></td>
				<td><a href="#" onmouseover="selfGoalRatingDesc(1)" onmouseout="hidefetchedRatings()">1 - 5</a>
				<div id="selfGoalsRating1" class="messages" style="display:none;"> </div></td>
				<td><?php if($loggedEmpId == $performanceReview->getEmployeeId()){ ?>
					<input id="txtSelfGoalsRating"  name="txtSelfGoalsRating" type="text" class="smallInput" maxscale="5" minscale="1" valiadate="1" maxlength='3' <?php echo ($isReviwer || $isHrAdmin || $disableAll) ? 'disabled' : ''; ?> value="<?php echo trim($performanceReview->getReviewRatings()->getSelfGoalsRate())?>"/>
					<span class="validation-error"></span>
				<?php }else {
               			if($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED) {
                        	echo "";
                        } else {
                        	echo trim($performanceReview->getReviewRatings()->getSelfGoalsRate());
                        }
            	} ?>
				</td>
				<td><?php if($loggedEmpId == $performanceReview->getEmployeeId()){ ?>
                    <textarea id='txtSelfGoalsComment'  name='txtSelfGoalsComment' class="selfComments reviwerComment" cols="50" rows="6" <?php echo ($isReviwer || $isHrAdmin || $disableAll) ? 'disabled' : ''; ?> ><?php echo trim(html_entity_decode($performanceReview->getReviewRatings()->getSelfGoalsComment())); ?></textarea>
                    <span class="validation-error"></span>
                <?php }else{ 
                	if($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED) {
                		echo "";
                	} else {
                		echo html_entity_decode(nl2br($performanceReview->getReviewRatings()->getSelfGoalsComment()));
                	}
                } ?>
        	</td>
    	</tr>
    </tbody>
</table>