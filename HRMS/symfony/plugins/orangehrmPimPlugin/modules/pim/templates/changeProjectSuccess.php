<?php use_javascripts_for_form($form); ?>
<?php use_stylesheets_for_form($form); ?>
<?php use_javascript(plugin_web_path('orangehrmPimPlugin', 'js/changeProjectSuccess')); ?>
<script type="text/javascript">
    //<![CDATA[
    //we write javascript related stuff here, but if the logic gets lengthy should use a seperate js file
    var edit = "<?php echo __("Edit"); ?>";
    var save = "<?php echo __("Save"); ?>";
    var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>'
    var lang_startDateAfterEndDate = "<?php echo __('End date should be after start date'); ?>";
    var lang_View_Details =  "<?php echo __('View Details'); ?>";
    var lang_Hide_Details =  "<?php echo __('Hide Details'); ?>";
    var lang_max_char_terminated_reason =  "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>";
    var lang_max_char_terminated_note =  "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>";
    var lang_terminatedReasonRequired = "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var lang_activateEmployement = "<?php echo __("Activate Employment"); ?>";
    var lang_terminateEmployement = "<?php echo __("Terminate Employment"); ?>";
    var lang_editTerminateEmployement = "<?php echo __("Edit Employment Termination"); ?>";
    var activateEmployementUrl = '<?php echo url_for('pim/activateEmployement?empNumber=' . $empNumber); ?>';
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var customerList = <?php echo str_replace('&#039;', "'", $form->getCustomerListAsJson()) ?> ;
    var projectList = <?php echo str_replace('&#039;', "'", $form->getProjectListAsJson()) ?> ;
    var existingEmpCustomerList = <?php echo str_replace('&#039;', "'", $form->getExistingEmpCustomerListAsJson()) ?> ;
    var existingEmpProjectList = <?php echo str_replace('&#039;', "'", $form->getExistingEmpProjectListAsJson()) ?> ;
    var fileModified = 0;
    var hintClass = 'inputFormatHint';
    var lang_typeHint = '<?php echo __("Type for hints") . "..."; ?>';
    var urlForGetProjectList = '<?php echo url_for("admin/getProjectListJson?customerId="); ?>';
    var firstPart = '<?php echo url_for('admin/viewJobSpec?attachId='); ?>';
    var notDefinedLabel = '<?php echo __('Not Defined'); ?>';
    
    //]]>
</script>


<div class="box pimPane" id="">
    
    <?php include_partial('pimLeftMenu', array('empNumber' => $empNumber, 'form' => $form));?>
    
    <div class="">
        <div class="head">
            <h1><?php echo __('Job'); ?></h1>
        </div> <!-- head -->
        
        <div class="inner">
            <?php if ($allowTerminate || $allowActivate || $jobInformationPermission->canRead()) : ?>
            
            <?php include_partial('global/flash_messages', array('prefix' => 'jobdetails')); ?>
            
            <form id="frmEmpJobDetails" method="post" enctype="multipart/form-data" action="<?php echo url_for('pim/changeProject'); ?>">
                <fieldset>
                    <?php if ($jobInformationPermission->canRead()) : ?>
                    <?php echo $form['_csrf_token']; ?>
                    <?php echo $form['emp_number']->render(); ?>
                    <ol>
                       <li>
                        <?php echo $form['customerName']->renderLabel(__('Customer Name')); ?>
                        <?php echo $form['customerName']->render(array("class" => "formInputCustomer", "maxlength" => 52)); ?>
                           <input type="hidden" id="customerId" name="customerId" value="">
                       </li>                        
                        <li>
                            <?php echo $form['project']->renderLabel(__('Project')); ?>
                            <?php echo $form['project']->render(array("class" => "formInputText")); ?>
                        </li>
                        
                       
                        
                    </ol>
                    <?php endif; ?>
                    
                    <p>
                        <?php if ($jobInformationPermission->canUpdate()) : ?>
                        <input type="button" class="" id="btnSave" value="<?php echo __("Edit"); ?>" />
                        <?php endif; ?>                      
                    </p>
                </fieldset>
            </form>
            
            <?php else : ?>
            <div><?php echo __(CommonMessages::DONT_HAVE_ACCESS); ?></div>
            <?php endif; ?>
        </div> <!-- inner -->
    </div>
    
    <?php 
    //echo include_component('pim', 'customFields', array('empNumber' => $empNumber, 'screen' => CustomField::SCREEN_JOB)); 
    echo include_component('pim', 'reporty', array('empNumber' => $empNumber, 'screen' => EmployeeAttachment::SCREEN_JOB)); 
    ?>
    
</div>
<script type="text/javascript">
    //<![CDATA[
   var readonlyFlag = 0;
    <?php if (!$jobInformationPermission->canUpdate()) { ?>
        readonlyFlag = 1;
    <?php } ?>
    //]]>
</script>

