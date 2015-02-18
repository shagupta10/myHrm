<?php use_javascripts_for_form($form) ?>
<?php use_stylesheets_for_form($form) ?>
<?php echo javascript_include_tag(plugin_web_path('orangehrmPimPlugin', 'js/addEmployeeSuccess')); ?>

<div class="box">

<?php if (isset($credentialMessage)) { ?>

<div class="message warning">
    <?php echo __(CommonMessages::CREDENTIALS_REQUIRED) ?> 
</div>

<?php } else { ?>

    <div class="head">
        <h1><?php echo __('Add Employee'); ?></h1>
    </div>

    <div class="inner" id="addEmployeeTbl">
        <?php include_partial('global/flash_messages'); ?>        
        <form id="frmAddEmp" method="post" action="<?php echo url_for('pim/addEmployee'); ?>" enctype="multipart/form-data">
            <fieldset>
                <ol>
                	<?php echo $form['_csrf_token']; ?>
                	<?php echo $form['empNumber']->render(); ?>
                    <?php //echo $form->render(); ?>
                    <li>
                        <?php echo $form['employeeId']->renderLabel(__('Employee ID') . ' <em>*</em>'); ?>
                        <?php echo $form['employeeId']->render(array("class" => "formInputText")); ?>
                        <div style='float:left; padding-left:10px;padding-right:10px'>
                        	<?php echo $form['jobTitle']->renderLabel(__('job Title') . ' <em>*</em>'); ?>
                        	<?php echo $form['jobTitle']->render(array("class" => "formInputText")); ?>
                        </div>	
                    </li>
                    
                    <li class="line nameContainer">
                        <label class="hasTopFieldHelp"><?php echo __('Full Name'); ?></label>
                        <ol class="fieldsInLine">
                            <li>
                                <div class="fieldDescription"><em>*</em> <?php echo __('First Name'); ?></div>
                                <?php echo $form['firstName']->render(); ?>
                            </li>
                            <li>
                                <div class="fieldDescription"><?php echo __('Middle Name'); ?></div>
                                 <?php echo $form['middleName']->render(); ?>
                            </li>
                            <li>
                                <div class="fieldDescription"> <?php echo __('Last Name'); ?></div>
                                <?php echo $form['lastName']->render(); ?>
                            </li>
                        </ol>                        
                    </li>
                    
                    <li class="line nameContainer">
                        <label class="hasTopFieldHelp"><?php echo __('Contacts'); ?></label>
                        <ol class="fieldsInLine">
                            <li>
                                <div class="fieldDescription"><?php echo __('E-Mail'); ?></div>
                                <?php echo $form['otherEmail']->render(); ?>
                            </li>
                            <li>
                                <div class="fieldDescription"><?php echo __('Contact Number'); ?></div>
                                 <?php echo $form['contactNo']->render(); ?>
                            </li>
                        </ol>                        
                    </li>
                 </ol>
                 
                 <ol>
                   <li class="line nameContainer">
                        <label class="hasTopFieldHelp"><?php echo __('Address'); ?></label>
                        <ol class="fieldsInLine">
                            <li>
                                <div class="fieldDescription"> <?php echo __('Address Street 1'); ?></div>
                                <?php echo $form['addStreetOne']->render(); ?>
                            </li>
                            <li>
                                <div class="fieldDescription"><?php echo __('Address Street 2'); ?></div>
                                 <?php echo $form['addStreetTwo']->render(); ?>
                            </li>
                            <li>
                                <div class="fieldDescription"> <?php echo __('City'); ?></div>
                                <?php echo $form['city']->render(); ?>
                            </li>
                        </ol>                        
                    </li>
                    
                    <li class="line nameContainer">
                        <label class="hasTopFieldHelp"><?php echo __(''); ?></label>
                        <ol class="fieldsInLine">
                            <li>
                                <div class="fieldDescription"> <?php echo __('State / Province'); ?></div>
                                <?php echo $form['state']->render(); ?>
                            </li>
                            <li>
                                <div class="fieldDescription"><?php echo __('Zipcode'); ?></div>
                                 <?php echo $form['zipcode']->render();?>
                            </li>
                            <li>
                                <div class="fieldDescription"> <?php echo __('Country'); ?></div>
                                <?php echo $form['country']->render(); ?>
                            </li>
                        </ol>                        
                    </li>
                  </ol>
                  
                  <ol>
                    <li>
                        <?php echo $form['optGender']->renderLabel(__('Gender') . ' <em>*</em>'); ?>
                        <?php echo $form['optGender']->render(); ?>
                        <div style='float:left; padding-left:10px;padding-right:10px'>
                        	<?php echo $form['cmbMarital']->renderLabel(__('Marital Status') . ' <em>*</em>'); ?>
                        	<?php echo $form['cmbMarital']->render(); ?>
                        </div>	
                    </li>
                    
                    <li>
                        <?php echo $form['dateofjoining']->renderLabel(__('Date of joining' . '<em>*</em>')); ?>
                        <?php echo $form['dateofjoining']->render(); ?>
                    </li>
                                        
                    <li>
                        <?php echo $form['emp_status']->renderLabel(__('Employment Status' . '<em>*</em>')); ?>
                        <?php echo $form['emp_status']->render(); ?>
                    </li>
                    
                    <li>
                        <?php echo $form['photofile']->renderLabel(__('Photograph')); ?>
                        <?php echo $form['photofile']->render(); ?>
                    </li>
                    
                    <li>
                        <?php echo $form['chkLogin']->renderLabel(__('Create Login Details')); ?>
                        <?php echo $form['chkLogin']->render(); ?>
                    </li>
                    
                    <li class = "loginSection">
                    	<?php echo $form['user_name']->renderLabel(__('Username') . '<em>*</em>'); ?>
                        <?php echo $form['user_name']->render(); ?>
                    </li>
                    
                    <li class = "loginSection">
                        <?php echo $form['user_password']->renderLabel(__('Password') . '<em>*</em>'); ?>
                        <?php echo $form['user_password']->render(); ?>
                    </li>
                    
                    <li class = "loginSection">
                        <?php echo $form['re_password']->renderLabel(__('Confirm Password' . '<em>*</em>')); ?>
                        <?php echo $form['re_password']->render(); ?>
                    </li>
                    
                    <li class = "loginSection">
                        <?php echo $form['status']->renderLabel(__('Status') . '<em>*</em>'); ?>
                        <?php echo $form['status']->render(); ?>
                    </li>
                    
                </ol>
                <p>
                    <input type="button" class="" id="btnSave" value="<?php echo __("Save"); ?>"  />
                </p>
            </fieldset>
        </form>
    </div>

<?php } ?>
    
</div> <!-- Box -->    

<script type="text/javascript">
    //<![CDATA[
    //we write javascript related stuff here, but if the logic gets lengthy should use a seperate js file
    var employees = <?php echo json_encode($form->getEmployeeList()); ?>;
    var employeeList = eval(employees);
    var edit = "<?php echo __("Edit"); ?>";
    var save = "<?php echo __("Save"); ?>";
    var lang_firstNameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_lastNameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_userNameRequired = "<?php echo __("Should have at least %number% characters", array('%number%' => 5)); ?>";
    var lang_passwordRequired = "<?php echo __("Should have at least %number% characters", array('%number%' => 4)); ?>";
    var lang_unMatchingPassword = "<?php echo __("Passwords do not match"); ?>";
    var lang_statusRequired = "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var lang_locationRequired = "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var cancelNavigateUrl = "<?php echo public_path("../../index.php?menu_no_top=hr"); ?>";
    var createUserAccount = "<?php echo $createUserAccount; ?>";
    var ldapInstalled = '<?php echo ($sf_user->getAttribute('ldap.available')) ? 'true' : 'false'; ?>';
    var fieldHelpBottom = <?php echo '"' . __(CommonMessages::FILE_LABEL_IMAGE) . '. ' . __('Recommended dimensions: 200px X 200px') . '"'; ?>;
    var lang_validEmail = '<?php echo __(ValidationMessages::EMAIL_INVALID); ?>';
    var lang_validPhoneNo = "<?php echo __(ValidationMessages::TP_NUMBER_INVALID); ?>";
    var lang_validDateMsg = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';    //]]>
    var lang_noMoreThan100 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 100)); ?>";
    var lang_noMoreThan20 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 20)); ?>";
    var lang_noMoreThan50 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 50)); ?>";
    var lang_alreadyExists = "<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>";
    var lang_validFirstName = '<?php echo __(ValidationMessages::ALPHA_CHARACTER, array('%name%' => "First Name")); ?>';
	var lang_validLastName = '<?php echo __(ValidationMessages::ALPHA_CHARACTER, array('%name%' => "Last Name")); ?>';
    
</script>
