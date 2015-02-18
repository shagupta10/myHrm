<?php use_javascripts_for_form($form) ?>
<?php use_stylesheets_for_form($form) ?>
<?php use_javascript(plugin_web_path('orangehrmTrainingPlugin', 'js/addTrainerSuccess')); ?>
<div id= "trainerFrm">
	<div class="box">
		<div class="head"><h1 id="addTrainerHeading">Add External Trainer</h1></div>
	    <div class="inner">
	        <form name="frmAddTrainer" id="frmAddTrainer" method="post" action="<?php echo url_for('training/addTrainer'); ?>">
	            <fieldset>
	              <ol>
	            	<?php echo $form->render(); ?>
	              </ol>
	              <p>
	                   	<input type="button" id="btnSave" value="<?php echo __("Save") ?>" name="btnSave" />
	                    <input type="button" class="reset" id="btnCcl" value="<?php echo __("Cancel") ?>" name="btnCcl" />                    
	              </p>
	            </fieldset>
	        </form>
	    </div>
	</div>
</div>
<div class="box">
	<?php include_partial('global/flash_messages', array('prefix' => 'addTrainer')); ?>
</div>
<div id="customerList" class="">
     <?php  include_component('core', 'ohrmList', array()); ?>
</div>
<script type = "text/javascript">
	//<![CDATA[
	var trainerInfoUrl = '<?php echo url_for('training/trainerInfo')?>'+'?id=';
	var lang_required = 'This is a required field.';
	var lang_number = 'Please enter a valid number.';
	//]]>
</script>