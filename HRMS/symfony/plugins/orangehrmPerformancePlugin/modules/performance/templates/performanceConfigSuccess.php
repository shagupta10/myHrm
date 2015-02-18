<?php use_javascripts_for_form($form) ?>
<?php use_stylesheets_for_form($form) ?>

<div class= "box">
	<div class= "head"> <h1><?php echo __('Start New Performance Cycle') ?></h1></div>
		<div class= "inner">
		    <?php include_partial('global/flash_messages', array('prefix' => 'performanceConfig')); ?>
			<form action="<?php echo url_for('performance/performanceConfig'); ?>" id="frmPerfConfig" method="post">
				<ol>
					<li>
						<label for="currentCcl">Current Cycle</label><input type="text" value="<?php echo $form->currentCycle; ?>" id="currentCcl"/>
					</li>
					<?php echo $form->render(); ?>
				</ol>
				 <p>
			           <input type="button" id="btnStart" value="<?php echo __("Start") ?>" name="btnStart"/>                    
			     </p>
			</form>
		</div>
</div>
<!-- Copy Review Confirmation box HTML: Begins for the Appraisal -->
<div class="modal hide" id="submitConfirmation"
	style="margin-top: -150px;">
	<div class="modal-header">
		<a class="close" data-dismiss="modal">×</a>
		<h3><?php echo __('SynerzipHRMS - Performance Cycle Saved'); ?></h3>
	</div>
	<div class="modal-body">
		<p><?php echo __('Do you want to Copy previous Performance Cycle Review?'); ?></p>
	</div>
	<div class="modal-footer">
		<input type="button" class="btn" data-dismiss="modal"
			id="dialogOkBtn" value="<?php echo __('Ok'); ?>" /> <input
			type="button" class="btn reset" data-dismiss="modal"
			value="<?php echo __('Cancel'); ?>" />
	</div>
</div>
<script type="text/javascript">
var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
var lang_required = '<?php echo __(ValidationMessages::REQUIRED); ?>';
var due_date = '<?php echo $form->dueDate ?>';
var today = '<?php echo $form->today ?>';
var isSave = '<?php echo $form->isSave ?>';

    $("#dialogOkBtn").click(function() {
    	window.location.href = '<?php echo url_for('performance/copyReview'); ?>';
    });
    $(document).ready(function(){
        if(isSave == 1){
            $('#submitConfirmation').modal();
        }
		$(".calendar").datepicker('disable');
		disableFormElements();
		checkForStartButton();
		$('#btnStart').click(function() {
			enableFormElements();
			$('#frmPerfConfig').submit();
			disableFormElements();
			checkForStartButton();
		});
		
		var validator = $("#frmPerfConfig").validate({
	        rules: {
	            'performanceConfig[from_date]' : {
		            required: true
	            },

	            'performanceConfig[to_date]' : {
	            	required: true
	            },
	            'performanceConfig[due_date]' : {
	            	required: true
	            }
	            },
	        messages: {
	            'performanceConfig[from_date]' : {
	            	required: lang_required
	            },

	            'performanceConfig[to_date]' : {
	            	required: lang_required
	            },
	            'performanceConfig[due_date]' : {
	            	required: lang_required
	            }
	        }
		});
	});

	function disableFormElements() {
		$("#frmPerfConfig :input").attr("disabled", true);
		$('#btnStart').attr("disabled", false);
	}

	function enableFormElements() {
		$("#frmPerfConfig :input").attr("disabled", false);
	}

	function checkForStartButton() {
		var dueDateArray = due_date.split("-");
		var todayArray = today.split("-");
		if(new Date(dueDateArray[0], dueDateArray[1]-1, dueDateArray[2]) >= new Date(todayArray[0], todayArray[1]-1, todayArray[2])) {
			$('#btnStart').attr("disabled", true);
		}
	}
	
</script>