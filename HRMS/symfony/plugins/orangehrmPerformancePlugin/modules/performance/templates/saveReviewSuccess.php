<?php use_javascripts_for_form($form); ?>
<?php use_stylesheets_for_form($form); ?>

<?php
$dateFormat = get_datepicker_date_format($sf_user->getDateFormat());  
$displayDateFormat = str_replace('yy', 'yyyy', $dateFormat);
?>
<div class="box">

    <div class="head">
        <h1 id="formHeading">
            <?php echo isset($reviewId) ? __('Edit Performance Review') : __('Add Performance Review'); ?>
        </h1>
    </div>

    <div class="inner">
        
        <?php include_partial('global/flash_messages'); ?>

        <?php if (isset($noKpiDefined)) { ?>
            <div class = "message warning">
                <?php
                echo __('No Key Performance Indicators were found for the job title of this employee') . " " .
                '<a href="#" id="defineKpi">' . __("Define Now") . '</a>';
                ?>
                <a href="#" class="messageCloseButton"><?php echo __('Close'); ?></a>
            </div>
        <?php } ?>
        <form action="#" id="frmSave" name="frmSave" class="content_inner" method="post">
            <fieldset>
                <ol>
                    <?php echo $form['_csrf_token']; ?>
					<?php echo $form["reviewId"]->render(); ?>
                    <li>
							<?php echo $form['employeeName']->renderLabel('Employee Name'. ' <span class="required">*</span>');?>
							<?php echo $form['employeeName']->render();?>
					</li>

					<li class="largeTextBox" >
							<?php echo $form['reviewer']->renderLabel('Primary Reviewer');?>
						<div style='float:left;'>
							<?php echo $form['reviewer']->render();?>
						</div>
					</li>
					<li class="largeTextBox">
							<?php echo $form['reviewers']->renderLabel('Secondary Reviewers');?>
						<div style='float:left;'>
							<?php echo $form['reviewers']->render();?>
						</div>
					</li>
					<li>
	                		<label for="performanceCycle"><?php echo __('Current Cycle'); ?></label>
		                	 <?php
		            			$currentCycle = $form->getCurrentCycle();
		            			echo __(set_datepicker_date_format($currentCycle['from']) .' - '.set_datepicker_date_format($currentCycle['to']));
		            		 ?>
					</li>
				   
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>
                <p>
                    <input type="button" class="" id="saveBtn" value="<?php echo __('Save') ?>" tabindex="6" />
                    <input type="button" class="reset" id="resetBtn" 
                        value="<?php if (isset($reviewId)) echo __('Reset'); else echo __('Clear'); ?>" tabindex="7" />
                </p>
            </fieldset>
        </form>
    </div> <!-- inner -->

</div> <!-- Box -->

<script type="text/javascript">
//<![CDATA[                
    var invalid_input = 'Invalid Input';
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var displayDateFormat = '<?php echo str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())); ?>';
    var lang_dateError = '<?php echo __("To date should be after from date") ?>';
    var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, 
            array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
    var required_message = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var valid_emp = '<?php echo __(ValidationMessages::INVALID); ?>';
    var dueDateMessage = '<?php echo __("Due date should be after from date"); ?>';
    var employeeList = <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson()) ?> ;
    var existingReviewersList = <?php echo str_replace('&#039;', "'", $form->existingReviewerListAsJSON) ?> ;
    var existingPrimaryReviewers = <?php echo str_replace('&#039;', "'", $form->existingPrimaryReviewerAsJSON) ?> ;
  //]]>
    $(document).ready(function() {
        if($('#txtPeriodFromDate-0').val() == ""){
            $('#txtPeriodFromDate-0').val(displayDateFormat)
        }
        if($('#txtPeriodToDate-0').val() == ""){
            $('#txtPeriodToDate-0').val(displayDateFormat)
        }
        if($('#txtDueDate-0').val() == ""){
            $('#txtDueDate-0').val(displayDateFormat)
        }

        $("#saveReview_reviewer").tokenInput(employeeList, {
             prePopulate: existingPrimaryReviewers,
             tokenLimit: 1,
             preventDuplicates: true,
             disabled: false,
             required: true 
         });
         
         $("#saveReview_reviewers").tokenInput(employeeList, {
            prePopulate: existingReviewersList,
            tokenLimit: 6,
            preventDuplicates: true,
            disabled: false  
        });
         
        $('#processing').html('');
        
        // Save button
        $('#saveBtn').click(function(){
            	$('#frmSave').submit();
        });
        
        // Clear button
        $('#resetBtn').click(function(){
            if($("#resetBtn").attr('value') == 'Clear') {
                validator.resetForm();
                $('#saveReview_reviewers').tokenInput("clear");
                $('#saveReview_reviewer').tokenInput("clear");
            } else { //reset part
                validator.resetForm();
                $('#saveReview_reviewers').tokenInput("clear");
                $('#saveReview_reviewer').tokenInput("clear");
            }            
        });    
        
        $.validator.addMethod("validEmployeeName", function(value, element) {                 
            return autoFill('saveReview_employeeName_empName', 'saveReview_employeeName_empId', employees_saveReview_employeeName);
        });
        
        $.validator.addMethod("validReviewerName", function(value, element) {                 
            return autoFill('saveReview_reviewerName_empName', 'saveReview_reviewerName_empId', employees_saveReview_reviewerName);
        });
        
        $("#saveReview_employeeName_empName").result(function(event, item) {
            $(this).valid();
        });
        
        $("#saveReview_reviewerName_empName").result(function(event, item) {
            $(this).valid();
        });        

        function autoFill(selector, filler, data) {
            $("#" + filler).val("");
            var valid = false;
            $.each(data, function(index, item){
                if(item.name.toLowerCase() == $("#" + selector).val().toLowerCase()) {
                    $("#" + filler).val(item.id);
                    valid = true;
                }
            });
            return valid;
        }
	
        $.validator.addMethod("checkReviewers", function(value, element, params) {
 			var reviewer = trim($('#saveReview_reviewer').val());
 			var reviewers = trim($('#saveReview_reviewers').val());
 			var temp = true;
 			if(reviewers!="" && reviewer!="" && reviewer !=null && reviewers != null) {
	 			reviewersArray = reviewers.split(",");
 				if(reviewer.length > 0 && reviewersArray.length > 0) {
 		 			for(i=0; i < reviewersArray.length; i++) {
 		 	 			if(reviewersArray[i] == reviewer) {
 							temp = false;
 							break;
 		 	 			}
 		 			}
 	 			}
        	}
 			return temp;
        });

        $.validator.addMethod("isEmployee", function(value, element, params) {
        	temp = true;
        	var emp = trim($('#saveReview_employeeName_empId').val());
        	if(value!="" && value!=null  && emp != "" && emp !=null) {
        		reviewersArray = value.split(",");
        		for(i=0; i < reviewersArray.length; i++) {
		 	 			if(reviewersArray[i] == emp) {
							return false;
		 	 			}
		 		}
        	}
 			return temp;
        });
        
            /* Validation */
            var validator = $('#frmSave').validate({
            	ignore: "",
                rules: {
                    'saveReview[employeeName][empName]' : {
                        required: true,
                        validEmployeeName: true,
                        onkeyup: false
                    },
                    'saveReview[reviewer]' : {
                    	checkReviewers: true,
                    	isEmployee: true,
                        required: true,
                    },
                    'saveReview[reviewers]' : {
                    	checkReviewers: true,
                    	isEmployee: true,
                    },
                    
                },  
                messages: {
                    'saveReview[employeeName][empName]' : {
                        required: required_message,
                        validEmployeeName: valid_emp
                    },
                    'saveReview[reviewer]' : {
                    	checkReviewers: 'Duplicate Reviewer',
                    	isEmployee: 'Reviewer should not be same as Employee',
                        required: required_message,
                        //validReviewerName: valid_emp
                    },
	                'saveReview[reviewers]' : {
	                	checkReviewers: 'Duplicate Reviewer',
	                	isEmployee: 'Reviewer should not be same as Employee'
	                }
                } 
            });
            
            // defineKpi link click
            $('#defineKpi').click(function(){
                location.href = "<?php echo url_for('performance/saveKpi'); ?>";
            });
    }); // ready():Ends
</script>