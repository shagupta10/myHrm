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
                <div id="selfGoalsRating<?php echo $kpi->getId();?>" class="toolpos messages" style="display:none;"> </div>
                    <?php echo nl2br($kpi->getKpi()) ?>
                </td>
                <td >
                	<a href="#" id="<?php echo $kpi->getId();?>" onmouseover="selfGoalRatingDesc('<?php echo $kpi->getId(); ?>')"  onmouseout="hidefetchedRatings()">
                   	 <?php echo ($kpi->getMinRate() != '') ? $kpi->getMinRate() : '-' ?> - <?php echo ($kpi->getMaxRate() != '') ? $kpi->getMaxRate() : '-' ?>
                    </a>
                </td>
                <td >
                    <?php   if($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED) { 
                    			echo "";
                    		} else {
                    			echo nl2br($kpi->getSelfRate());
							} 
					?>
                </td>
                <td class="">
                	<?php if($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED) {
                 				echo "";
                 			} else {
                 				echo html_entity_decode(nl2br($kpi->getSelfComment()));
                 			} ?>
                </td>
                <?php if($isPrimaryReviewer){ ?>
				<td>
					<?php if($loggedEmpId == $reviewer->getReviewerId()){ ?>
                        <input type="hidden" name="max<?php echo $kpi->getId() ?>" id="max<?php echo $kpi->getId() ?>" value="<?php echo $kpi->getMaxRate() ?>" />
                        <input type="hidden" name="min<?php echo $kpi->getId() ?>" id="min<?php echo $kpi->getId() ?>" value="<?php echo $kpi->getMinRate() ?>" />
                        <input id="txtReviewerRate<?php echo $kpi->getId() ?>"   reviewer="<?php echo $reviewer->getReviewerId() ?>" name="txtReviewerRate[<?php echo $kpi->getId()."_".$reviewer->getId() ?>]" type="text"  class="smallInput" value="<?php echo trim($kpi->getRate()) ?>"  maxscale="<?php echo $kpi->getMaxRate() ?>" minscale="<?php echo $kpi->getMinRate() ?>" valiadate="1" maxlength='3'  <?php echo ((!$isReviwer) || $disableAll)? 'disabled' : ''; ?> />
                        <span class="validation-error"></span>
                    <?php }else{
                    		echo nl2br($kpi->getRate());
                    	 } ?>
                </td>
                <?php } ?>
                <td class="">
               	 <?php if($loggedEmpId == $reviewer->getReviewerId()){ ?>
                    <textarea id='txtReviewerComments' class="reviwerComment" reviewer="<?php echo $reviewer->getReviewerId() ?>" name='txtReviewerComments[<?php echo $kpi->getId()."_".$reviewer->getId() ?>]' rows="2" cols="40"  <?php echo ((!$isReviwer) || $disableAll) ? 'disabled' : ''; ?>><?php echo trim(html_entity_decode($kpi->getComment())); ?></textarea>
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