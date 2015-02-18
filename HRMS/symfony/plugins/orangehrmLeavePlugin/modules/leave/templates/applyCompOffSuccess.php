<?php
use_javascripts_for_form($form);
use_stylesheets_for_form($form);
use_stylesheet(plugin_web_path('orangehrmLeavePlugin', 'css/assignLeaveSuccess.css'));
?>

<div class="box" id="add-CompOff">
    <div class="head">
        <h1><?php echo __('Apply CompOff') ?></h1>
    </div>
    <div class="inner">
        <?php include_partial('global/flash_messages'); ?>
              
        
        <form id="frmAddCompoff" name="frmAddCompoff" method="post" action="">
            <fieldset>                
                <ol>
                    <?php echo $form->render(); ?>
                    <li class="required new">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>                      
                </ol>
                <p>
                	 <input type="submit" id="applyBtn" value="<?php echo __("Add CompOff") ?>"/>
                </p>                
            </fieldset>
            
        </form>
                   
    </div> <!-- inner -->
    
</div> <!-- Add CompOff -->

<script type="text/javascript">
    //<![CDATA[
    	 //Click Submit button
        $('#applyBtn').click(function(){
            $('#frmAddCompoff').submit();
        });
        
        //Validation
        $("#frmAddCompoff").validate({
            rules: {
                'addCompOff[numberOfDays]':{required: true, number:true },
                'addCompOff[txtComment]': {required: true, maxlength: 250},
            },
            messages: {
                'addCompOff[numberOfDays]':{
                    required:'<?php echo __(ValidationMessages::REQUIRED); ?>'
                },
                'addCompOff[txtComment]':{
                	required:'<?php echo __(ValidationMessages::REQUIRED); ?>',
                    maxlength:"<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>"
                }
            }
        });
        
        $.validator.addMethod("number", function(value, element) {
    	    return (numbers(element));
    	});
    
    
    //]]>
</script>