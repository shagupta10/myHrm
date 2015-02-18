<?php use_javascripts_for_form($form); ?>
<?php use_stylesheets_for_form($form); ?>
<?php use_javascript(plugin_web_path('orangehrmTrainingPlugin', 'js/showTrainingsSuccess')); ?>
<?php use_javascript(plugin_web_path('', 'js/stroll/js/stroll.min.js')); ?>
<?php use_javascript(plugin_web_path('', 'js/eventCal/js/jquery.eventCalendar.js')); ?>
<?php use_javascript(plugin_web_path('', 'js/eventCal/js/jquery.eventCalendar.min.js')); ?>
<?php use_stylesheet(plugin_web_path('', 'js/eventCal/css/eventCalendar.css')); ?>
<?php use_stylesheet(plugin_web_path('', 'js/eventCal/css/eventCalendar_theme_responsive.css')); ?>
<?php use_stylesheet(plugin_web_path('orangehrmTrainingPlugin', 'css/showTrainingsSuccess')); ?>
<?php use_stylesheet(plugin_web_path('', 'js/cssmenu/styles.css')); ?>
<?php use_javascript('jquery.blockUI.js')?>
<?php stylesheet_tag(theme_path('css/orangehrm.datepicker.css'));?>
<div class="box">
<input name="hdnPageNo" type="hidden" value="<?php echo isset($clues['pageNo']) ? $clues['pageNo'] : '' ?>">
<div class="inner">
	<table style="width: 100%">
		<tr>
			<td></td>
			<td>
				<form id = "frmShowTraining" method = "post" action = "<?php echo url_for('training/showTrainings'); ?>"> 
					<div id='cssmenu'>
						<ul>
						   <li><a href ='#' class = "filter">All</a></li>
						   <li><a href='#' class = "filter">Registered</a></li>
						   <li><a href='#' class = "filter">Upcoming</a></li>
						   <li class='last'><a href='#' class = "filter">Completed</a></li>
						</ul>
					</div>
				</form>
			</td>
		</tr>
		<tr>
			<td width="50" align="center">
				<div id="eventCal"  style="width: 80%;height: 500px;"></div>
			</td>
			<td width="50%">
			    <div class="top">
                    <?php
                    	$date = date('Y-m-d');
                        if ($pager->haveToPaginate()){
                        include_partial('global/paging_links', array('pager' => $pager, 'url' => url_for('training/showTrainings'), 'location' => 'top'));
                        }
                    ?>
                 </div>
				<div id="mainList">
                 	<ul class = "fly">
						<?php  
							$count = 0;
							if(count($trainings) > 0) {
								foreach ($trainings as $training) {
									$tid = $training->getId();
									if(in_array($tid, $form->trainingIds)){
										$btnClass = "unregButton";
									} else {
										$btnClass = "regButton";
									}
								?>
								<!-- Event card Starts here -->
								<li class = "<?php echo ($count%2 == 0) ? "odd" : "even" ?>">
									<font size = "2" color="#0E8EAB" face = "Calibri Light" style="font-weight: bold;"><?php echo set_datepicker_date_format($training[from_Date]). ' - '.set_datepicker_date_format($training[to_Date]) ?></font><br>
									<?php if($userObj->isAdmin()) {?> <!-- Link to edit event for admin user -->
										<a href = "<?php echo url_for('training/addTraining') ?>/id/<?php echo $tid ?>" style = "text-decoration: none;"><div style="padding-top: 9px;"><font style="font-weight: bold;" size = "4px"><?php echo $training->getTopic()?></font></div></a>
									<?php } else { ?>
										<div style="padding-top: 9px;"><font style="font-weight: bold;" size = "4px"><?php echo $training->getTopic()?></font></div>
									<?php }
										  if($date < $training[from_Date])  {?>
											<div style="float: right;"><a href="#" id = "rgBtn_<?php echo $tid ?>" class="<?php echo $btnClass ?>"></a></div>
									<?php } else {
												if($btnClass == "unregButton") {?>
												<div style="float: right;"><a href="#" class = "registeredBtn"></a></div>
										<?php	}
											}
										?>
									<table style = "margin-top: 10px;">
									<tr>
										<td>
											<div><a href="#" class = "viewDetails" id = "viewDetails_<?php echo $tid ?>">View Details</a></div>
										</td>
										<?php if($userObj->isAdmin()) {?>
										<td>
											<div><a href="#" style="margin-left: 13px;" class = "viewAttendance" id = "viewAttendance_<?php echo $tid ?>">Attendance</a></div>
										</td>
										<?php } ?>
									</tr>
									</table>
								</li>
								<!-- Event card ends here -->
							<?php $count++;
								$btnClass = "";
								}
							} else { ?>
								<li class = "even">
									<div style="padding-top: 9px;"><font style="font-weight: bold;" size = "4px">No records found.</font></div>
								</li>
							<?php  }
						?>
					</ul>
				</div>
			</td>
		</tr>
	</table>
</div>
</div>

<div class="modal hide large" id="viewDetailsBox">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('SynerzipHRMS - Training Details'); ?></h3>
    </div>
    <div class="modal-body">
		
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" data-dismiss="modal" id="dialogDeleteBtn" value="<?php echo __('Ok'); ?>" />
        <input type="button" class="btn reset" data-dismiss="modal" id ="regBoxBtn" value="<?php echo __('Register'); ?>" />
    </div>
</div>
<div id="domMessage" style="display:none;"> 
	We are processing your request.  Please be patient.
</div>

<script type = "text/javascript">
//<![CDATA[				
	var linkForReg = '<?php echo url_for('training/registerTraining') ?>';
	var trainingJSON = <?php echo str_replace('&#039;', "'", $form->getSubscribedTrainings()); ?>;
	var linkForDetails = '<?php echo url_for('training/getTrainingDetails') ?>';
	var linkForAttendance = '<?php echo url_for('training/trainingAttendance') ?>';
	var loadingImg = '<?php echo theme_path('images/ajax-loader.gif')?>';
	var filterValue = '<?php echo $prop['filter'] ?>';
//]]>
</script>