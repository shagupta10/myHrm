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
    		<td style="width: 30%">List only remarkable achievements, such as - independent delivery of a major component, a technical/process suggestion/initiative which has made considerable improvement in your project, distinctive appreciation received for you work, certification earned relevant for your job role etc. Accomplishments should be evidence based and/or quantifiable.</td>
			<td><a href="#" onmouseover="selfGoalRatingDesc(2)" onmouseout="hidefetchedRatings()">1 - 5</a>
			<div id="selfGoalsRating2" class="messages" style="display:none;"> </div></td>
			<td><?php if($loggedEmpId == $performanceReview->getEmployeeId()){ ?>
				<input id="txtSelfMajorAccomplishmentRating"  name="txtSelfMajorAccomplishmentRating" type="text" class="smallInput" maxscale="5" minscale="1" valiadate="1" maxlength='3' <?php echo ($isReviwer || $isHrAdmin || $disableAll) ? 'disabled' : ''; ?> value="<?php echo trim($performanceReview->getReviewRatings()->getSelfAccomplishmentRate())?>"/>
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
				<textarea id='txtSelfMajorAccomplishmentComment'  name='txtSelfMajorAccomplishmentComment' class="selfComments reviwerComment" cols="50" rows="6" <?php echo ($isReviwer || $isHrAdmin || $disableAll) ? 'disabled' : ''; ?> ><?php echo trim(html_entity_decode($performanceReview->getReviewRatings()->getSelfAccomplishmentComment())); ?></textarea>
	        	<span class="validation-error"></span>
            <?php }else{ 
            	if($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED) {
            		echo "";
            	} else {
            	  echo html_entity_decode(nl2br($performanceReview->getReviewRatings()->getSelfAccomplishmentComment()));
            	}
            } ?>
        	</td>
    	</tr>
    </tbody>
</table>