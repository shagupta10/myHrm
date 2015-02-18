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
            <div id="msgtooltip<?php echo $kpi->getId() ?>"  class="msgposition messages" style="display: none;">
                 </div>
                <?php echo nl2br($kpi->getKpi()) ?>
            </td>
            <td >
            	<a href="#" id="<?php echo $kpi->getId();?>" onmouseover="showRatingDesc(this)"  onmouseout="hidefetchedRatings()">
                	<?php echo ($kpi->getMinRate() != '') ? $kpi->getMinRate() : '-' ?> - <?php echo ($kpi->getMaxRate() != '') ? $kpi->getMaxRate() : '-' ?>
                </a>
            </td>
            <td>
            	<?php if($loggedEmpId == $performanceReview->getEmployeeId()){ ?>
                    <input type="hidden" name="max<?php echo $kpi->getId() ?>" id="max<?php echo $kpi->getId() ?>" value="<?php echo $kpi->getMaxRate() ?>" />
                    <input type="hidden" name="min<?php echo $kpi->getId() ?>" id="min<?php echo $kpi->getId() ?>" value="<?php echo $kpi->getMinRate() ?>" />
                    <input id="txtSelfRate<?php echo $kpi->getId() ?>"  name="txtSelfRate[<?php echo $kpi->getId() ?>]" type="text"  class="smallInput"  value="<?php echo trim($kpi->getSelfRate()) ?>"  maxscale="<?php echo $kpi->getMaxRate() ?>" minscale="<?php echo $kpi->getMinRate() ?>" valiadate="1" maxlength='3' <?php echo ($isReviwer || $isHrAdmin || $disableAll) ? 'disabled' : ''; ?>/>
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
                    <textarea id='txtSelfComments'  name='txtSelfComments[<?php echo $kpi->getId() ?>]' class="selfComments reviwerComment" rows="2" cols="40" <?php echo ($isReviwer || $isHrAdmin || $disableAll) ? 'disabled' : ''; ?> ><?php echo trim(html_entity_decode($kpi->getSelfComment())); ?></textarea>
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
