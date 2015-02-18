<?php use_javascripts_for_form($form) ?>
<?php use_stylesheets_for_form($form) ?>
<?php use_stylesheet(plugin_web_path('orangehrmTrainingPlugin', 'css/trainingAttendanceSuccess')); ?>
<?php use_javascript(plugin_web_path('orangehrmTrainingPlugin', 'js/trainingAttendanceSuccess')); ?>
<!-- <script type="text/javascript" src="http://www.steamdev.com/zclip/js/jquery.zclip.min.js"></script> -->
<?php use_javascript(plugin_web_path('', 'js/zclip/jquery.zclip.min.js')); ?>

<div class="box">
	<div class="head"><h1 id="addTrainerHeading">Training Attendance</h1></div>
    <div class="inner">
    <?php include_partial('global/flash_messages', array('prefix' => 'trainingAttendance')); ?>
        <form name="frmAddTrainer" id="frmAddTrainer" method="post" action="<?php echo url_for('training/addTrainer'); ?>">
            <fieldset>
              <ol>
              	<?php echo $form->render(); ?>
              </ol>
              <p>
                   	<input type="button" id="show" value="<?php echo __("Show") ?>" name="btnSave" />
                    <input type="button" class="reset" id="btnCcl" value="<?php echo __("Clear") ?>" name="btnCcl" />                    
              </p>
            </fieldset>
        </form>
    </div>
</div>
<div class="box">
	<div class="head"><h1>Attendance-Sheet</h1></div>
	<div class="inner">
	<form name="frmAttendance" id="frmAttendance" class="enable-scrolling" method="post" action="<?php echo url_for('training/trainingAttendance'); ?>">
	<div class = "top">
			<input type="button" class = "submit" id = "printAttendance" value = "Download"/>
			<input type="button" class = "submit" id = "copyToClipboard" value = "Copy Emails"/>
	</div>
        <table border="1" class = "attTabler">
        	<tr>
        	<!-- First row for session dates starts here -->
	        	<td>Attendees<input type="checkbox" class="selectAllCheckbox" name='all["checked"]'/></td>
	        	<?php
	        	foreach($form->trainingSchedule as $scheduleHeader) {
	        		$schHeaderID = 'sch_'.$scheduleHeader->getId();
				?>
				<td class="date">
					<?php echo date('d-M-Y', strtotime($scheduleHeader->getSessionDate())) ?>
					<!--  schedule headings -->
					<input type="checkbox" class="selectColumn" id="<?php echo $schHeaderID ?>" name='col["<?php echo $schHeaderID ?>"]' select-all="all-checkboxes"/>
				</td>
				<?php } ?>
				<td>Points</td>
        	</tr>
        	<!-- First row for session dates ends here -->
        	<?php foreach ($form->trainingAttendees as $attendees) { ?>
        	<tr>
	        	<td>
		        	<?php echo $attendees->getEmployee()->getFirstAndLastNames();
		        		$attID = 'att_'.$attendees->getEmpNumber() ?>
		        	<input type="checkbox" class="selectRow" id="<?php echo $attID?>" name='row["<?php echo $attID ?>"]'  select-all="all-checkboxes"/>
	        	</td>
	        	<?php foreach ($form->trainingSchedule as $schedule) {
	        		$schID = 'sch_'.$schedule->getId();
	        		$chkBoxID = $attID.'_'.$schID;
	        	?>
        		<td>
        			<input type="checkbox" id="<?php echo $chkBoxID ?>" class="<?php echo $schID ?> child-checkbox" attID="<?php echo $attID ?>" select-all="all-checkboxes" 
        			name='group["<?php echo $schID ?>"]["<?php echo $attID ?>"]'/>
        		</td>
	        	<?php }?>
	        	<td><input type="button" class = "pointButton" value ="Submit" id = "points_<?php echo $attID ?>"></td>
        	</tr>
            <?php }?>
        </table>
        <br><br>
        <button class="confirm" id="btnSubmit" value="<?php echo __("Save") ?>"></button>
    </form>
    </div>
</div>

<div class="modal hide small in" id="confirmDialog">
  <div class="modal-header align-center">
    <a class="close" data-dismiss="modal">×</a>
    <div id="header">
    <h3><?php echo __('SynerzipHRMS - Confirmation Required'); ?></h3></div>
  </div>
  <div class="modal-body align-center">
    
     <div id="description">  
         <span><?php echo __("After Submit data cannot be changed.<br>(Points will be added automatically)<br><br>Do you want to continue?");?></span>
     </div>
   
  </div>
  <div class="modal-footer align-center">
    <input type="button" class="btn yes" data-dismiss="modal" value="<?php echo __('Yes'); ?>"/>
    <input type="button" class="btn no" data-dismiss="modal" value="<?php echo __('No'); ?>"/>
  </div>
</div>

<div id="fade" class="modal-backdrop hide"></div>
<script type="text/javascript">
	var savePointsUrl = '<?php echo url_for('training/saveTrainingPoints'); ?>';
	var attendanceArray = <?php echo $form->getTrainingAttendanceforWidget(); ?>;
	var linkForExport = '<?php echo url_for('training/exportAttendance'); ?>';
	var linkForAttendance = '<?php echo url_for('training/trainingAttendance'); ?>';
	var attendeeEmails = '<?php echo $form->emails; ?>';
	var flashPath = '<?php echo plugin_web_path('', 'js/zclip/ZeroClipboard.swf')?>';
</script>