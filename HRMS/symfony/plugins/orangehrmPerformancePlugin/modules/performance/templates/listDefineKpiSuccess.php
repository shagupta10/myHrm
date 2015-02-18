<style type="text/css">
	table
	{
		border-collapse:collapse;
	}
	table.table th, td
	{
		border: 1px solid black;
	}
</style>
<?php 	use_stylesheet(plugin_web_path('orangehrmPerformancePlugin', 'css/listDefineKpiSuccess.css'));
		use_javascript(plugin_web_path('orangehrmPerformancePlugin', 'js/listDefineKpiSuccess'));
?>
<div class="box searchForm toggableForm">

    <div id="formHeading" class="head"><h1><?php echo __("Search Key Performance Indicators") ?></h1></div>
    <div class="inner">

        <?php if (count($listJobTitle) == 0) : ?>
            <div class="message warning">
                <?php echo __("No Defined Job Titles") ?> 
                <a href="<?php echo url_for('admin/viewJobTitleList') ?>"><?php echo __("Define Now") ?></a>
                <a href="#" class="messageCloseButton"><?php echo __('Close'); ?></a>
            </div>
        <?php endif; ?>      

        <?php include_partial('global/form_errors', array('form' => $form)); ?>

        <form action="#" id="frmSearch" name="frmSearch" method="post">
            <input type="hidden" name="mode" value="search" >
            <fieldset>	
                <ol>
                    <li>
                        <label for="txtLocationCode"><?php echo __('Job Title') ?></label>
                        <select name="txtJobTitle" id="txtJobTitle" tabindex="1" >
                            <option value="all"><?php echo __('All') ?></option>
                            <?php foreach ($listJobTitle as $jobTitle) { ?>
                                <option value="<?php echo $jobTitle->getId() ?>" <?php
                            if (isset($searchJobTitle) && $jobTitle->getId() == $searchJobTitle->getId()) {
                                echo 'selected';
                            }
                                ?>><?php
                                    echo $jobTitle->getJobTitleName();
                                    if (!$jobTitle->getIsDeleted() == JobTitle::ACTIVE) {
                                        echo ' (' . __('Deleted') . ')';
                                    }
                                ?></option>
                            <?php } ?>
                        </select>
                    </li>
                </ol>
                <p>
                    <input type="button" class="searchbutton" id="searchBtn" value="<?php echo __("Search") ?>" name="_search" />
                </p>  
            </fieldset>
        </form>			
    </div>

    <a href="#" class="toggle tiptip" title="<?php echo __(CommonMessages::TOGGABLE_DEFAULT_MESSAGE); ?>">&gt;</a>

</div> <!-- end-of-searchKPI -->

<div class="box noHeader" id="search-results">



    <form action="<?php echo url_for('performance/deleteDefineKpi') ?>" name="frmList" id="frmList" method="post">

        <?php echo $form['_csrf_token']; ?>

        <div id="tableWrapper">



            <div class="inner">

                <div class="top">

                    <?php
                    if ($pager->haveToPaginate()) {
						echo '<div class="paginginline">';
						echo '<ul class="paging top">
							<select id="recordsPerPage_Limit" name="recordsPerPage_Limit">
							<option value="10">10</option>
							<option value="20">20</option>
							<option value="30">30</option>
							<option value="40">40</option>
							<option value="50">50</option>
							<option value="60">60</option>
							<option value="70">70</option>
							<option value="80">80</option>
							<option value="90">90</option>
							<option value="100">100</option>
						</select> </ul>';
                        include_partial('global/paging_links_js', array('pager' => $pager, 'url' => url_for('performance/listDefineKpi'), 'location' => 'top'));
                        echo '</div>';
                    }
                    ?>
                     <input type="button" class="" id="addKpiBut" value="<?php echo __('Add') ?>" tabindex="2"  /> 
                    <?php if ($hasKpi) { ?>
                        <input type="button" class="delete"  id="deleteKpiBut"
                               value="<?php echo __('Delete') ?>" tabindex="3" />
                         <input type="button" class=""  id="copyKpiBut"
                               value="<?php echo __('Copy') ?>" tabindex="4" />  
                           <?php } ?>
                </div>

                <?php include_partial('global/flash_messages'); ?>

                <table class="table hover">
                    <thead>
                        <tr>
                           <!-- <th style="width:2%" class="tdcheckbox">
                                <input type="checkbox"  name="allCheck" value="" id="allCheck" />
                            </th>  -->
                            <th> 
                                <?php echo __('Key Performance Indicator Title') ?>
                            </th> 
                            <th> 
                                <?php echo __('Key Performance Indicator Description') ?>
                            </th> 
                            <th>
                                <?php echo __('Job Title') ?>
                            </th>
                            <th> 
                                <?php echo __('Rating') ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$hasKpi) { ?>
                            <tr>
                                <td></td>
                                <td><?php echo __(TopLevelMessages::NO_RECORDS_FOUND); ?></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        <?php } else { ?>
                            <?php
                            $row = 0;
                            foreach ($kpiList as $kpi) {
                                $cssClass = ($row % 2) ? 'even' : 'odd';
                                $row = $row + 1;
                                ?>
                                <tr class="<?php echo $cssClass ?>">

                               <!--  <td class="tdcheckbox">
                                        <input type='checkbox' class='innercheckbox' name='chkKpiID[]' id="chkLoc" value='<?php //echo $kpi->getId() ?>' />
                                    </td> --> 
                                    <td class="">
                                        <a href="<?php echo url_for('performance/updateKpi?id=' . $kpi->getId()) ?>"><?php echo $kpi->getKpiTitle() ?></a>
                                    </td>
                                    <td class="">
                                        <?php echo nl2br($kpi->getDesc()) ?>
                                        <div id="msgtooltip<?php echo $kpi->getId() ?>"  class="msgposition messages" style="display: none;">
                                     </div>
                                    </td>
                                    <td class="">
                                        <?php echo $kpi->getJobTitle()->getJobTitleName(); ?>
                                        
                                    </td>
                                    <td class="">
                                     <a href="#" id=<?php echo $kpi->getId(); ?> onmouseover="showRatingDesc(this)"  onmouseout="hidefetchedRatings()">
                                        <?php echo ($kpi->getRateMin() != '') ? $kpi->getRateMin() : '-' ?> - <?php echo ($kpi->getRateMax() != '') ? $kpi->getRateMax() : '-' ?>
                                     </a>
                                    </td>
                                 
                                </tr>

                                <?php
                            }
                        }
                        ?>

                    </tbody>

                </table> 
				<div class="bottom">
                <?php
                if ($pager->haveToPaginate()) {
					echo '<div class="paginginline">';
					echo '<ul class="paging bottom">
					<select id="recordsPerPage_LimitBottom" name="recordsPerPage_LimitBottom">
						<option value="10">10</option>
						<option value="20">20</option>
						<option value="30">30</option>
						<option value="40">40</option>
						<option value="50">50</option>
						<option value="60">60</option>
						<option value="70">70</option>
						<option value="80">80</option>
						<option value="90">90</option>
						<option value="100">100</option>
					</select> </ul>';
                    include_partial('global/paging_links_js', array('pager' => $pager, 'url' => url_for('performance/listDefineKpi'), 'location' => 'bottom'));
                    echo '</div>';
                }
                ?>                        
			</div>
            </div>


        </div>

    </form>

</div>
<!-- Rating dialog -->

<form name="frmHiddenParam" id="frmHiddenParam" method="post"
	action="<?php echo url_for('performance/listDefineKpi?recordsPerPage_Limit='.$recordsPerPageLimit); ?>">
	<input type="hidden" name="pageNo" id="pageNo"	value="" /> 
	<input type="hidden" name="hdnAction" id="hdnAction" value="search" />
</form>
<!-- end of Rating dialog-->
<script type="text/javascript">
	var addKpiURL = '<?php echo url_for('performance/saveKpi'); ?>';
	var copyKpiURL = '<?php echo url_for('performance/copyKpi'); ?>';
	var getKpiUrl = '<?php echo url_for('performance/getKpiRatings'); ?>';
	var lang_deleteWarningMsg = '<?php echo __(TopLevelMessages::SELECT_RECORDS); ?>';
	var lang_ValidateMsg = '<?php echo __(ValidationMessages::REQUIRED); ?>';
	var actionURL = '<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>';
	var recordsPerPage = '<?php echo $recordsPerPageLimit; ?>';
	function submitPage(pageNo) {
		document.frmHiddenParam.pageNo.value = pageNo;
		document.frmHiddenParam.hdnAction.value = 'paging';
		document.getElementById('frmHiddenParam').submit();
		}
</script>
