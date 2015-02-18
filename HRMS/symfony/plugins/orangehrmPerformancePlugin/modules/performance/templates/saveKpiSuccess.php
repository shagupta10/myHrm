<div class="box" >
    
    <div class="head"><h1><?php echo __("Add Key Performance Indicator") ?></h1></div>
    
	<div class="inner">
        
        <?php if(count($listJobTitle) == 0) : ?>
            <div class="message warning">
                <?php echo __("No Defined Job Titles") ?> 
                <a href="<?php echo url_for('admin/viewJobTitleList') ?>"><?php echo __("Define Now") ?></a>
                <a href="#" class="messageCloseButton"><?php echo __('Close');?></a>
            </div>
        <?php endif; ?>        
        
        <?php include_partial('global/flash_messages'); ?>
        
	    <form id="frmSave" method="post">

        <?php echo $form['_csrf_token']; ?>
              
                <fieldset>
                    
                    <ol>
                        
                        <li>
                            <label for="txtLocationCode"><?php echo __('Job Title'.' <em>*</em>')?></label>
                             <select name="txtJobTitle" id="txtJobTitle" tabindex="1" >
                     	       <option value=""><?php echo '--'.__('Select').'--'?></option>
	                           <?php foreach($listJobTitle as $jobTitle){?>
	                     	   <option value="<?php echo $jobTitle->getId()?>"><?php echo $jobTitle->getJobTitleName() ?></option>
	                           <?php }?>
                             </select>
                        </li>
                        <li>
                            <label for="txtPerformanceTitle"><?php echo __('Key Performance Indicator Title'.' <em>*</em>')?></label>
                            <input id="txtPerformanceTitle"  name="txtPerformanceTitle" type="text"  tabindex="2" />
             		    </li>
                        <li class="largeTextBox"> 
                            <label for="txtDescription"><?php echo __('Key Performance Indicator Description')?></label>
                            <textarea id='txtDescription' name='txtDescription' rows="4" cols="40" tabindex="3"></textarea>
                        </li>
                        
                        <li>
                            <label for="txtMinRate"><?php echo __('Minimum Rating')?></label>
                            <input id="txtMinRate"  name="txtMinRate" type="text" value="<?php echo $defaultRate['min']?>" tabindex="4" />
             		    </li>
                        
                        <li>
                            <label for="txtMaxRate"><?php echo __('Maximum Rating')?></label>
                            <input id="txtMaxRate"  name="txtMaxRate" type="text" value="<?php echo $defaultRate['max']?>" tabindex="5" />
             		    </li>
             		    <li>
                            <label for="txtRatingDescription"><?php echo __('Rating Description')?></label>
                            <a href="#" id="addRating" onclick="addRatingDetails()">Add Rating Detail</a>
             		    </li>
             		    <?php for ($i = 1; $i <= $form->numberOfRatings; $i++) {
                            ?>
                            <li id="<?php echo "ratingDescription_" . $i ?>" name="ratingDescription" style="display: none;">
                          	  <label id="<?php echo "lblRatingDescription_" . $i ?>" for="<?php echo "lblRatingDescription_" . $i ?>" align="center">Rating : 0</label>
                           	  <textarea id='<?php echo "txtRatingDescription_" . $i ?>' name='txtRatingDescription[<?php echo $i ?>]' rows="2" cols="20"></textarea>
             		   		</li>
                        <?php } ?>
             		   
             		    <li>
                            <label for="chkDefaultScale"><?php echo __('Make Default Scale')?></label>
                            <input type="checkbox"  name="chkDefaultScale" id="chkDefaultScale" tabindex="6"  value="1"></input>
             		    </li> 
                        <li class="required">
                            <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                        </li>
                            
                    </ol>
                        
                    <p>
                        <input type="button" id="saveBtn" value="<?php echo __('Save')?>" tabindex="7" />
                        <input type="button" class="reset" id="resetBtn" value="<?php echo __('Reset')?>" tabindex="8" />
                    </p>
                        
                </fieldset>    
                            
            </form>            
 	</div>
</div>

<!-- end of comment dialog-->

	 <script type="text/javascript">

		$(document).ready(function() {

			//Validate the form
			 $("#frmSave").validate({
				
				 rules: {
				 	txtJobTitle: { required: true },
				 	txtPerformanceTitle: { required: true ,maxlength: 100},
				 	txtMinRate: { number: true,minmax:true,maxlength: 5},
				 	txtMaxRate: { number: true,minmax:true ,maxlength: 5}
			 	 },
			 	 messages: {
			 		txtJobTitle: '<?php echo __(ValidationMessages::REQUIRED); ?>', 
			 		txtPerformanceTitle:{ 
			 			required:'<?php echo __(ValidationMessages::REQUIRED); ?>',
			 			maxlength:"<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 100))?>"
			 		},
			 		txtMinRate:{ 
				 		number:"<?php echo __("Should be a number")?>",
				 		minmax:"<?php echo __("Minimum Rating should be less than Maximum Rating")?>",
				 		maxlength:"<?php echo __("Should be less than %number% digits", array('%number%' => 5))?>"
			 		},
			 		txtMaxRate: {
				 		number:"<?php echo __("Should be a number")?>", 
				 		minmax:"<?php echo __("Minimum Rating should be less than Maximum Rating")?>",
				 		maxlength:"<?php echo __("Should be less than %number% digits", array('%number%' => 5))?>"
				 		}
			 	 }
			 });

			//Add custom function to validator
				$.validator.addMethod("minmax", function(value, element) {
					if( $('#txtMinRate').val() !='' && $('#txtMaxRate').val() !='')
				    	return ((parseFloat($('#txtMinRate').val())) < (parseFloat($('#txtMaxRate').val())));
					else
						return true;
				});

			// when click Save button 
				$('#saveBtn').click(function(){
					$('#frmSave').submit();
				});

				// when click reset button 
				$('#resetBtn').click(function(){
               $("label.error").each(function(i){
                  $(this).remove();
               });
					document.forms[0].reset('');
				});

		 });
		 
		 function addRatingDetails(){
		 	$("li[name='ratingDescription']" ).hide();
		 	var minRate = $('#txtMinRate').val();
		 	var maxRate = $('#txtMaxRate').val();
				 
		 	if(minRate == ''){
		 		alert("Minimum Rating not defined");
		 		return false;
		 	}
		 	
		 	if (maxRate <= minRate){
		 		alert('Minimum Rating should be less than Maximum Rating');
		 		return false;
		 	}
		 	var diff = maxRate - minRate;
			for(var i=1; i<= (diff+1);i++){
				$("#lblRatingDescription_"+i).html("Rating : "+ minRate);	
				$("#ratingDescription_"+i).show();
				minRate++;
			}
		 }

		
		
	</script>
