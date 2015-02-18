<?php
stylesheet_tag(theme_path('css/orangehrm.datepicker.css'));
use_javascript('orangehrm.datepicker.js');
?>
<?php use_javascript('jquery.blockUI.js')?>
<style type="text/css">
.messages{
	    display: none;
        position: absolute;
        width: auto;
        padding: 10px;
        background: #000000;
        color: #EEEEEE;
        /* background: #eeeeee;
        color: #000000; */
        border: 2px solid #1a1a1a;
        font-size: 100%;
}
.bgColorDesc{
		background-color: #C4C4C4;
}
input[type="radio"]:checked + span
{
    font-weight: bolder;
}
form ol li select {
 	width:280px
 }
</style>
<div class="box" >
        
    <div class="head"><h1><?php echo __("Copy Performance Review") ?></h1></div>

    <div class="inner">

       
        
        <?php include_partial('global/flash_messages'); ?>

        <form id="copyReviewFrm" name="copyReviewFrm" method="post">

            <?php echo $form['_csrf_token']; ?>
                
            <fieldset>
                <ol>
					<li>
						<label for="copy_allEmployees">All Employees</label>
						<input type="checkbox" name="chkAllEmployees" id="chkAllEmployees">
					</li>
                    <li id="employee">
                    	<label for="txtEmployee"><?php echo __('Employee') ?></label>
                         <?php
                			$employeeWidget = new ohrmWidgetEmployeeNameAutoFill(array('loadingMethod'=>'ajax'));
                			echo $employeeWidget->render('txtEmployee');
                		 ?>
	                </li>
            
                    <li>
                    	<label for="performanceCycle"><?php echo __('Copy From'); ?></label>
                    	 <?php
                			$performanceCycleFrom = new ohrmWidgetPerformancePeriod();
                			echo $performanceCycleFrom->render('performanceCycleFrom','','style="width:300px"');
                		 ?>
	                </li>
               		<li>
                		<label for="performanceCycle"><?php echo __('Current Cycle'); ?></label>
	                	 <?php
	            			echo __(set_datepicker_date_format($performanceCycle->getPeriodFrom()) .' - '.set_datepicker_date_format($performanceCycle->getPeriodTo()));
	            		 ?>
	        		</li>
                 
                    <li> 
                        <label for="copyData"><?php echo __('Copy') ?></label>
                        <ol style="border:none;">
	                        <li style="width:auto;margin:2px">
	                        	<input type="radio" name="copyData" value="1" style="width:auto;" checked> <label for="objectiveData"><?php echo __("Only Objective Data") ?></label>
	                        </li>
	                        <li style="text-align:left;width:auto;margin:2px">
	                        	<input type="radio" name="copyData" value="2" style="width:auto;"><?php echo __("All Data") ?>
	                        </li>
                        </ol>
                    </li>
                    
                    <li>
                    	<label for="emailNotification"><?php echo __('Send email notication') ?></label>
                    	<input type="checkbox" name="emailNotification" id="emailNotification">
                    </li>
                </ol>
                <p>
                    <input type="button" id="copyBtn" value="<?php echo __('Copy') ?>" tabindex="3" />
                    <input type="button" class="reset" id="resetBtn" value="<?php echo __('Reset') ?>" tabindex="4" />
                </p>

            </fieldset>  
                
        </form>            
    </div>
</div>

<!-- Alert box HTML: Begins -->
<div class="modal hide" id="confirmation">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('SynerzipHRM - Error'); ?></h3>
    </div>
    <div class="modal-body">
        <p id="errorMessage"></p>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" data-dismiss="modal" id="okBtn" value="<?php echo __('Ok'); ?>" />
    </div>
</div>
<!-- Alert box HTML: Ends -->

<div id="domMessage" style="display:none;"> 
    We are processing your request.  Please be patient.
</div> 

 <script type="text/javascript">
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var displayDateFormat = '<?php echo str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())); ?>';
    
	$(document).ready(function() {

		$('#chkAllEmployees').click(function(){
	        if(this.checked){
	        	$("#employee").hide();
	        	$("#txtEmployee_empId").val(' ');
	        	$("#txtEmployee_empName").val(' ');
	        }else{
	        	$("#employee").show();
	        }
    			
	    });
			
	    // when click Save button
	    $('#copyBtn').click(function(){
	    	var valid = true;
	    	html = "<h3>Correct following error(s)</h3><br />";
	    	
	    	if($("#date_from").val() == '' && $("#date_to").val() == ''){
    			 valid = false;
    			 html += " <p>Select <strong>Performance cycle </strong>to copy</p>";
	    	}
	    	
	    	
	    	if(valid){
    			$.blockUI({ 
					message: $('#domMessage'),
		    		css: { 
		                border: 'none', 
		                padding: '15px', 
		                backgroundColor: '#000', 
		                '-webkit-border-radius': '10px', 
		                '-moz-border-radius': '10px', 
		                opacity: .5, 
		                color: '#fff' 
		            }
				});
	    		$('#copyReviewFrm').submit();	
	    	}else{
	    		 $('#errorMessage').html(html);
                 $('#confirmation').modal()
	    	}
	        
	    });
	
	    // when click reset button
	    $('#resetBtn').click(function(){
	        $("label.error").each(function(i){
	            $(this).remove();
	        });
	        document.forms[0].reset('');
	    });
	
	});
	
	 function showError(){
        $('#errorMessage').html('Loading...');
    	$('#ratingDialog').modal();
    }
		
     
</script>
