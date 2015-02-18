<?php use_javascripts_for_form($form); ?>
<?php use_stylesheets_for_form($form); ?>
<?php use_javascript('jquery.blockUI.js')?>
<?php use_javascript(plugin_web_path('', 'js/ckeditor/ckeditor')); ?>
<?php use_javascript(plugin_web_path('orangehrmTrainingPlugin', 'js/addTrainingSuccess')); ?>
<?php use_stylesheet(plugin_web_path('orangehrmTrainingPlugin', 'css/addTrainingSuccess')); ?>
<?php use_javascript(plugin_web_path('', 'js/timepicker/jquery.plugin.js')); ?>
<?php use_javascript(plugin_web_path('', 'js/timepicker/jquery.timeentry.js')); ?>
<div id= "trainingFrm">
	<div class="box">
	    <?php include_partial('global/flash_messages', array('prefix' => 'addTraining')); ?>
		<div class="head"><h1 id="addTrainingTHeading">Add Training</h1></div>
	    <div class="inner">
	            <form name="frmAddTraining" id="frmAddTraining" method="post" action="<?php echo url_for('training/addTraining'); ?>">
		            <?php echo $form['_csrf_token']; ?>
		            <?php echo $form['id']; ?>
		            <?php echo $form['trainingDates']; ?>
		            <fieldset>
		              <ol>
		                    <li>
									<?php echo $form['topic']->renderLabel('Topic'. ' <span class="required">*</span>');?>
									<?php echo $form['topic']->render();?>
							</li>
							<li class="largeTextBox">
									<?php echo $form['trainingDesc']->renderLabel('Description');?>
									<div style='float:left;'>
										<?php echo $form['trainingDesc']->render(array("style"=>"width:400px;height:150px"));?>
									</div>
							</li>
							<li>
									<?php echo $form['attendeePoint']->renderLabel('Attendee Points'. ' <span class="required">*</span>');?>
									<?php echo $form['attendeePoint']->render();?>
							</li>
							<li>
									<?php echo $form['trainerPoint']->renderLabel('Trainer Points'. ' <span class="required">*</span>');?>
									<?php echo $form['trainerPoint']->render();?>
							</li>
							
							<div id="widContainer">
							    
							</div>
							
							<li>
								<h3><label for="addButton">&nbsp;</label><a href="#" id="addButton" class= "links">Add Schedule Details</a></h3>
								<span class = "redSpan" for = "addButton" id="addButtonSpan"></span>
							</li>
							<li>
									<?php echo $form['totalHours']->renderLabel('Hours');?>
									<?php echo $form['totalHours']->render();?>
							</li>
							<li class="largeTextBox">
									<?php echo $form['trainer']->renderLabel('Trainers'. ' <span class="required">*</span>');?>
									<?php echo $form['trainer']->render();?>
							</li>
							<li>
									<?php echo $form['location']->renderLabel('Location');?>
									<?php echo $form['location']->render();?>
							</li>
							<li>
									<?php echo $form['isPublished']->renderLabel('Is Published');?>
									<?php echo $form['isPublished']->render();?>
							</li>
							<li class="required new">
				                    <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
				            </li>
		              </ol>
		              <p>
		                   	<input type="button" id="btnSave" value="<?php echo __("Save") ?>" name="btnAdd" />
		                    <input type="button" class="reset" id="btnCcl" value="<?php echo __("Cancel") ?>" name="btnCcl" />                    
		              </p>
		            </fieldset>
		        </form>
	    </div>
	</div>
</div>
<div id="domMessage" style="display: none;">We are processing your
	request. Please be patient.
</div>
<script type = "text/javascript">
//<![CDATA[
    var lang_typeHintForTopic = '<?php echo __("Topic") . "..."; ?>';
    var lang_typeHintForDesc = '<?php echo __("Description") . "..."; ?>'; 
	var trainingId = '<?php echo $form->trainingId ?>';
	var trainerList = <?php echo str_replace('&#039;', "'", $form->getTrainers()) ?> ;
	var lang_required = '<?php echo __(ValidationMessages::REQUIRED); ?>';
	var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
	var displayDateFormat = '<?php echo str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())); ?>';
	var errorForInvalidFormat='<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
	var desc_editor =  CKEDITOR.replace('training[trainingDesc]', {height: 240, width: 700});
	var prePopulatedTrainers = <?php echo str_replace('&#039;', "'", $form->prePopulatedTrainers) ?> ;
	var lang_number = 'Please enter valid number.';
    var scheduleDetails = <?php echo str_replace('&#039;', "'", $form->prePopulatedScheduleDetails) ?> ;
    var details = <?php echo $form->prePopulatedScheduleDetails ?> ;
	var i = 0;
	//]]>
</script>