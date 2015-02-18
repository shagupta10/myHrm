<?php
/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */
?>
<?php use_stylesheet(plugin_web_path('orangehrmRecruitmentPlugin', 'css/addCandidateSuccess.css')); ?>
<?php use_javascript(plugin_web_path('orangehrmRecruitmentPlugin', 'js/applyVacancySuccess')); ?>
<?php use_javascript(plugin_web_path('', 'js/ckeditor/ckeditor')); ?>
<?php use_javascript('jquery.blockUI.js')?>


<style type="text/css">
    #content {
        padding-top: 0;
    }
    
    strong{
	 font-weight:bold;
	}
</style>

<div id="addCandidate" class="box">
		<div align="right">
	    	Welcome  <?php echo $_SESSION['fname']?> &nbsp;&nbsp;&nbsp; <a href="<?php echo url_for('auth/logout'); ?>"><?php echo __('Logout'); ?></a>
	    </div>
	    <br>

        <div class="head"><h1 id="addCandidateHeading"><?php echo __("Apply for") . " " . $name; ?></h1></div>
        
        <?php include_component('core', 'ohrmPluginPannel', array('location' => 'add_layout_after_main_heading_1')) ?>
        
        <div class="inner">
            
        <?php include_partial('global/flash_messages', array('prefix' => 'applyVacancy')); ?>
            <!-- Confirmation box HTML: Begins -->
<div class="modal hide" id="linkConfirmation">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('SynerzipHRMS - Confirmation Required'); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo __(CommonMessages::ADD_MULTIPLE_VACANCY_CONFIRMATION); ?></p>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" data-dismiss="modal" id="dialogLinkBtn" value="<?php echo __('Yes'); ?>" />
        <input type="button" class="btn reset" data-dismiss="modal" value="<?php echo __('No'); ?>" />
    </div>
</div>
<!-- Confirmation box HTML: Ends -->
        
        <form name="frmAddCandidate" id="frmAddCandidate" method="post" enctype="multipart/form-data">

            <fieldset>
                
            <?php echo $form['_csrf_token']; ?>
            <?php echo $form["vacancyList"]->render(); ?>

            <ol>
                
                <li>
                    <label><?php echo __('Description'); ?> <span  id="extend">[+]</span></label>
                    <div id="txtArea" style="width:100%;margin-left: 150px">
                        <?php echo nl2br($description) ?>
                    </div>
                </li>
                <?php echo $form['id']->render(array("value"=>$candidateId)); ?>
            <li>
                <?php echo $form['email']->renderLabel(__('Email'). ' <span class="required">*</span>'); ?>
                <?php echo $form['email']->render(array("class" => "formInputText")); ?>
                <div style='float:left; padding-left:10px;padding-right:10px'>
                        <?php echo $form['alternateEmail']->renderLabel(__('Alternate E-mail'), array("class " => "alternateEmail")); ?>
                        <?php echo $form['alternateEmail']->render(array("class" => "contactNo")); ?>
                      </div>
            </li>
             <li>
                <?php echo $form['contactNo']->renderLabel(__('Contact No'. ' <em>*</em>'), array("class " => "contactNoLable")); ?>
                <?php echo $form['contactNo']->render(array("class" => "contactNo")); ?>
                <div style='float:left; padding-left:10px;padding-right:10px'>
                        <?php echo $form['alternateNumber']->renderLabel(__('Alternate Contact No'), array("class " => "alternateNumber")); ?>
                        <?php echo $form['alternateNumber']->render(array("class" => "contactNo")); ?>
                </div>
            </li>
            </ol><ol>
              <li class="line nameContainer">

                    <label class="hasTopFieldHelp"><?php echo __('Full Name'); ?></label>
                    <ol class="fieldsInLine">
                        <li>
                            <div class="fieldDescription"><em>*</em> <?php echo __('First Name'); ?></div>
                            <?php echo $form['firstName']->render(array("class" => "formInputText", "maxlength" => 35)); ?>
                        </li>
                        <li>
                            <div class="fieldDescription"><?php echo __('Middle Name'); ?></div>
                             <?php echo $form['middleName']->render(array("class" => "formInputText", "maxlength" => 35)); ?>
                        </li>
                        <li>
                            <div class="fieldDescription"><em>*</em> <?php echo __('Last Name'); ?></div>
                            <?php echo $form['lastName']->render(array("class" => "formInputText", "maxlength" => 35)); ?>
                        </li>
                        <li class="fieldHelpContainer" style="width: 40%;" ><?php echo '<div style="float:left;padding-top:20px;">' . __(CommonMessages::NAME_VALIDATION) . '</div>'; ?></li>
                    </ol>                        

                </li>                
                
            <?php include_component('core', 'ohrmPluginPannel', array('location' => 'add_layout_after_main_heading_2')) ?>
            
<!-- end of Rating dialog-->
<div id="domMessage" style="display:none;"> 
    We are processing your request.  Please be patient.
</div>
           
            
           
            
        </ol>
        <ol>    
            
            <li class="fieldHelpContainer">
            <?php if ($candidateId == "") : ?>
            
                <?php echo $form['resume']->renderLabel(__('Resume') . ' <span class="required">*</span>');
                
                
                ?>
                <?php echo $form['resume']->render(array("class " => "duplexBox")); ?>
                <?php echo '<div style ="float:left;padding-top:5px;margin-left:10px;">' . __(CommonMessages::FILE_LABEL_DOC) . '</div>'; ?>
            
            <?php else : ?>
                
                <?php echo $form['resume']->renderLabel(__('Resume')); ?>
                <?php echo "<span class=\"fileLink\">".__('Uploaded')."</span>"; ?>
            
            <?php endif; ?>
            </li>
            <li class="line nameContainer">
            	<label class="hasTopFieldHelp"><?php echo __('Education Details'); ?></label>
                <ol class="fieldsInLine">
                	<li>
                		<div class="fieldDescription"> <?php echo __('Highest Qualification'); ?></div>
                    	<?php echo $form['educationDetailDegree']->render(array("class" => "formInputText")); ?>
                    </li>
                    <li>
                    	<div class="fieldDescription"><?php echo __('Spcialization'); ?></div>
                    	<?php echo $form['educationDetailSpec']->render(array("class" => "formInputText")); ?>
                    </li>
                    <li>
                    	<div class="fieldDescription"><?php echo __('Percentage'); ?></div>
                   	    <?php echo $form['educationDetailPerc']->render(array("class" => "formInputText")); ?>
                	</li>
                </ol>
            </li>
     		<li>
                <?php echo $form['keyWords']->renderLabel(__('Keywords'), array("class " => "keywrd")); ?>
                <?php echo $form['keyWords']->render(array("class" => "keyWords")); ?>
                <div style='float:left; padding-left:10px;padding-right:10px'>
                    <?php echo $form['keySkills']->renderLabel(__('Key Skills'), array("class " => "keySkills")); ?>
                    <?php echo $form['keySkills']->render(array("class" => "formInputText")); ?>
                </div>
            </li>
           <li>
                <?php echo $form['currentCompany']->renderLabel(__('Current Company'), array("class " => "currentCompany")); ?>
                <?php echo $form['currentCompany']->render(array("class" => "formInputText")); ?>
			<div style='float:left; padding-left:10px;padding-right:10px'>
                <?php echo $form['designation']->renderLabel(__('Current Designation'), array("class " => "designation")); ?>
                <?php echo $form['designation']->render(array("class" => "formInputText")); ?>
			</div>
			</li>
            <li>
                <?php echo $form['totalExperience']->renderLabel(__('Total Experience(in years)'), array("class " => "totalExperience")); ?>
                <?php echo $form['totalExperience']->render(array("class" => "formInputText")); ?>
				<div style='float:left; padding-left:10px;padding-right:10px'>
                    <?php echo $form['relevantExperience']->renderLabel(__('Relevant Experience (in years)'), array("class " => "relevantExperience")); ?>
                    <?php echo $form['relevantExperience']->render(array("class" => "formInputText")); ?>
				</div>
			</li>
            <li>
                <?php echo $form['currentCtc']->renderLabel(__('Current CTC (LPA)', array("class " => "currentCtc"))); ?>
                <?php echo $form['currentCtc']->render(array("class" => "formInputText")); ?>
			<div style='float:left; padding-left:10px;padding-right:10px'>
                <?php echo $form['expectedCtc']->renderLabel(__('Expected CTC (LPA)'), array("class " => "expectedCtc")); ?>
                <?php echo $form['expectedCtc']->render(array("class" => "formInputText")); ?>
			</div>
			</li>
                
                <li>
                    <?php echo $form['noticePeriod']->renderLabel(__('Notice Period (In Days)'), array("class " => "noticePeriod")); ?>
                    <?php echo $form['noticePeriod']->render(array("class" => "formInputText")); ?> 
                 <div style='float:left; padding-left:10px;padding-right:10px'>
                    <?php echo $form['expectedDoj']->renderLabel(__('Expected Date of Joining'), array("class " => "expectedDoj")); ?>
                    <?php echo $form['expectedDoj']->render(array("class" => "formDateInput")); ?>
                </div>
                </li>
         		<li>
                    <?php echo $form['educationGap']->renderLabel(__('Education Gap'), array("class " => "educationGap")); ?>
                	<?php echo $form['educationGap']->render(array("class" => "formInputText")); ?>
				<div style='float:left; padding-left:10px;padding-right:10px'>
                    <?php echo $form['workGap']->renderLabel(__('Work Gap (in Months)'), array("class " => "workGap")); ?>
                    <?php echo $form['workGap']->render(array("class" => "formInputText")); ?>
				</div>
				</li>
		        <li>
                    <?php echo $form['originalLocation']->renderLabel(__('Current Location'), array("class " => "originalLocation")); ?>
                	<?php echo $form['originalLocation']->render(array("class" => "formInputText")); ?>  
				<div style='float:left; padding-left:10px;padding-right:10px'>
                    <?php echo $form['preferredLocation']->renderLabel(__('Preferred Location'), array("class " => "preferredLocation")); ?>
                    <?php echo $form['preferredLocation']->render(array("class" => "formInputText")); ?>
				</div>
				</li>  
				<li>
	                <?php echo $form['communicationSkills']->renderLabel(__('Communication Skills'), array("class " => "communicationSkills")); ?>
	                <?php echo $form['communicationSkills']->render(array("class" => "formInputText")); ?>
                <div style='float:left; padding-left:10px;padding-right:10px'>
                    <?php echo $form['visaStatus']->renderLabel(__('Visa Status'), array("class " => "visaStatus")); ?>
                    <?php echo $form['visaStatus']->render(array("class" => "formInputText")); ?>
                </div>
                </li>                
                <li>
                    <?php echo $form['stability']->renderLabel(__('Stability'), array("class " => "stability")); ?>
                	<?php echo $form['stability']->render(array("class" => "formSelect")); ?>
				<div style='float:left; padding-left:10px;padding-right:10px'>
                   <?php echo $form['employmentType']->renderLabel(__('Employment Type'), array("class " => "employmentType")); ?>
                   <?php echo $form['employmentType']->render(array("class" => "formSelect")); ?>            
				</div>
				</li>
             </ol>
              <ol>
			    <li class="largeTextBox">
                     <?php echo $form['projectDetails']->renderLabel(__('Project Details'), array("class " => "projectDetails")); ?>
                     <div style='float:left;'>
                     	<?php echo $form['projectDetails']->render(array("class" => "formInputText ckeditor", "cols" => 35, "rows" => 4, "style"=>"height:150px;width:400px")); ?>
                     </div>
                     <div style='margin-left:10px; float:left; align:center;padding:10px;border:1px solid;border-color:#000000;
                     border-radius:25px;height:150px; width:500px'>
	                   <p style="color:#000000"> Provide one or more recent project details with skills. Use following format-</p>
	                   <p style="color:#000000;line-height:150%">
							Project details- <strong>Project 1</strong>-(Project Domain), <strong>#Role</strong> = ( Current role <strong><u>Example</u></strong>: Tech lead, Software developer, Analyst),
							<strong>#Tech</strong>=( Tech skills <strong><u>Example</u></strong> Spring Java, C++ in the project), <strong>#Duration</strong> = (project duration  <strong><u>Example</u></strong> : 6 months, 1 year), 
							<strong>Project 2</strong>--(Project Domain), <strong>#Role</strong> = (current role  <strong><u>Example</u></strong>: Tech lead, Software developer, Analyst), 
							<strong>#Tech</strong>= (Tech skills  <strong><u>Example</u></strong>: Spring Java, C++ in the project),
							<strong>#Duration</strong> = (project duration  <strong><u>Example</u></strong>: 6 months, 1 year),</br>
							Mention at least 2-3  projects (Length should be less than 1000 characters).
	                   </p>
					</div>
                 </li>
              </ol>
              <ol>
             <ol style="display:none">
                <li class="largeTextBox">
                    <?php echo $form['microResume']->renderLabel(__('Short Resume Summary'), array("class " => "microResume")); ?>
                    <?php echo $form['microResume']->render(array("class" => "formInputText", "cols" => 35, "rows" => 4, "style"=>"height:120px;width:400px")); ?>
                    <div style='margin-left:10px; float:left; align:center;padding:10px;border:1px solid;border-color:#000000;border-radius:25px;height:120px; width:400px'>
	                   <p style="color:#000000"> Provide short summary of your resume. Use following format -</p>
	                   <p style="color:#000000;line-height:150%">
	                   	Loc- {Location}, {Degree} Total Exp. - {exp} Yrs, Relv. Exp.-{exp} yrs,<br/>
						Tech Skills- {skills}, <br/>
						Project details- 1. {Name of the recent project): ( Key skills used), 2. (Name of the project ): (key skills used), 3. (Project name): (key skills).  Mention at least 2-3 projects.<br/>
						Current CTC- {current CTC} LPA, Exp CTC- {expected CTC} LPA, N.P.-{notice period} days <br/>
	                   </p>
					</div>
                </li>
              </ol>
              <ol>
                <li class="largeTextBox">
                    <?php echo $form['comment']->renderLabel(__('Notes (If any)'), array("class " => "comment")); ?>
                    <?php echo $form['comment']->render(array("class" => "formInputText", "cols" => 35, "rows" => 4, "style"=>"width:300px;height:50px")); ?>
                </li>
                <li>
                    <?php echo $form['appliedDate']->renderLabel(__('Date of Application'), array("class " => "appDate")); ?>
                    <?php echo $form['appliedDate']->render(array("class" => "formDateInput")); ?>
                </li>
			    <li class="required new">
                    <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                </li>
            </ol>
            
            <p><input id="duplicate_status" type="hidden" value="">
                <input type="button" class="savebutton" name="btnSave" id="btnSave" value="<?php echo __("Submit"); ?>" /> <a id="backLink" href="<?php echo url_for('recruitmentApply/jobs') ?>"><?php echo __("Back to Job List"); ?></a>
            </p>
            
            </fieldset>

        </form>
        
        </div> <!-- inner -->
        
    </div>

<script type="text/javascript">
    //<![CDATA[
    var description	= '<?php $description; ?>';
    var vacancyId	= '<?php echo $vacancyId; ?>';
    var candidateId	= '<?php echo ($candidateId !="") ? $candidateId : 0;?>';
    var candidates = <?php echo json_encode($form->getAllCandidateList()); ?>;
    var candidateList = eval(candidates);
    var lang_firstNameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_lastNameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_emailRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_validEmail = '<?php echo __(ValidationMessages::EMAIL_INVALID); ?>';
    var lang_tooLargeInput = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 30)); ?>";
    var lang_commaSeparated = "<?php echo __("Enter comma separated words") . '...'; ?>";
    var lang_validPhoneNo = "<?php echo __(ValidationMessages::TP_NUMBER_INVALID); ?>";
    var lang_noMoreThan250 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>";
    var lang_noMoreThan200 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 200)); ?>";
    var lang_noMoreThan2000 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 2000)); ?>";
    var lang_noMoreThan50 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 50)); ?>";
    var lang_noMoreThan4 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 4)); ?>";
    var lang_noMoreThan10 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 10)); ?>";
    var lang_noMoreThan300 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 300)); ?>";
    var lang_noMoreThan1000 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 1000)); ?>";
    var lang_resumeRequired = "<?php echo __(ValidationMessages::REQUIRED); ?>";
    var linkForApplyVacancy = "<?php echo url_for('recruitmentApply/applyVacancy'); ?>";
    var linkForViewJobs = "<?php echo url_for('recruitmentApply/viewJobs'); ?>";
    var lang_back = "<?php echo __("Go to Job Page")?>";
    var lang_emailExistmsg = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
    var lang_noMoreThan100 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 100)); ?>";
    var lang_futureDateValidation = "<?php echo __("Should be greater than current date"); ?>";
    var lang_noMoreThan1 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 4)); ?>";
    var lang_validNo = '<?php echo __(ValidationMessages::VALID_NUMBER); ?>';
    var lang_validDateMsg = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
    var currentDate = '<?php echo set_datepicker_date_format(date("Y-m-d")); ?>';
	var lang_validFirstName = '<?php echo __(ValidationMessages::ALPHA_CHARACTER, array('%name%' => "First Name")); ?>';
	var lang_validLastName = '<?php echo __(ValidationMessages::ALPHA_CHARACTER, array('%name%' => "Last Name")); ?>';
	 
	 $(document).ready(function() { 	
	 	 CKEDITOR.replace( 'addCandidate_projectDetails', {
			toolbar: [
				[ 'Bold', 'Italic', 'Underline','-','Link', 'Unlink' ],
				[ 'FontSize', 'TextColor', 'BGColor' ]
			]
		 });
       /* Added By : Shagupta Faras
        * Added On : 22-07-2014
        * DESC: Agenet can apply for multiple vacancies for one candidate
        */   
		
	$('#addCandidate_email').focusout(function(){
            
            var candidateEmail=trim($(this).val());
            
           waitForRespose();
           if(candidateEmail!='')
           {                
               getCandidateDetail('email',candidateEmail);
           }
           else
           {
               
                setResumeRule();
                var resume_obj=$("#addCandidate_resume").parent('li');
                if(resume_obj.length>0)
                 resume_obj.html(' <label for="addCandidate_resume">Resume <span class="required">*</span></label><input type="file" id="addCandidate_resume" class="duplexBox" name="addCandidate[resume]">                <div style="float:left;padding-top:5px;margin-left:10px;">Accepts .docx, .doc, .odt, .pdf, .rtf, .txt up to 1MB</div>            ');
                else
                 $("#fileLink").parent('li').html(' <label for="addCandidate_resume">Resume <span class="required">*</span></label><input type="file" id="addCandidate_resume" class="duplexBox" name="addCandidate[resume]">                <div style="float:left;padding-top:5px;margin-left:10px;">Accepts .docx, .doc, .odt, .pdf, .rtf, .txt up to 1MB</div>            ');
             
                $.unblockUI();
                
           }
	 });
     $('#addCandidate_contactNo').focusout(function(){
            
            var contactNo=trim($(this).val());
            
           
           waitForRespose();
           if(contactNo!='')
           {               
               getCandidateDetail('contactNo',contactNo);
               //$("#addCandidate_email").val(prev_email);
           }
           else
           {
               
                setResumeRule();
                var resume_obj=$("#addCandidate_resume").parent('li');
                if(resume_obj.length>0)
                 resume_obj.html(' <label for="addCandidate_resume">Resume <span class="required">*</span></label><input type="file" id="addCandidate_resume" class="duplexBox" name="addCandidate[resume]">                <div style="float:left;padding-top:5px;margin-left:10px;">Accepts .docx, .doc, .odt, .pdf, .rtf, .txt up to 1MB</div>            ');
                else
                 $("#fileLink").parent('li').html(' <label for="addCandidate_resume">Resume <span class="required">*</span></label><input type="file" id="addCandidate_resume" class="duplexBox" name="addCandidate[resume]">                <div style="float:left;padding-top:5px;margin-left:10px;">Accepts .docx, .doc, .odt, .pdf, .rtf, .txt up to 1MB</div>            ');
             
                $.unblockUI();
           }
	 });
	 });
    function getCandidateDetail(passObj,objVal) {
        if(passObj=='email')
	    params = 'email=' + objVal;
        else
        {
           if(passObj=='contactNo')
            params = 'contactNo=' + objVal;
        }
        var prev_email=trim($('#addCandidate_email').val());
        var prev_contact=trim($('#addCandidate_contactNo').val());
	    $.ajax({
	        type: 'GET',
	        url: '<?php echo url_for('recruitmentApply/getCandidateDetail'); ?>',
	        data: params,
	        dataType: 'json',
	        success: function(data) {   
	            var html = '';
	            var rows = 0;                   
                    if(data.length != 0){
                    /*$("#addCandidate_firstName").val(data['firstName']);
                    $("#addCandidate_middleName").val(data['middleName']);
                    $("#addCandidate_lastName").val(data['lastName']);
                    $("#addCandidate_contactNo").val(data['contactNumber']);*/ 
            
            if(data.duplicate === true)
            {
                var duplicateString="<div class=\"message warning fadable\" >Duplicate Candidate<a class=\"messageCloseButton\" href=\"#\">Close</a></div>";
                $(".inner").prepend(duplicateString); 
                 setTimeout(function(){
                $("div.fadable").fadeOut("slow", function () {
                    $("div.fadable").remove();
                });
            }, 2000);
                $.unblockUI();
                $('#frmAddCandidate').reset();
                $("#addCandidate_email").val('');
                $("#addCandidate_contactNo").val('');
           }
           else
           {
                 
                           
                     $.each( data, function( i, value ) {
                       if($("#addCandidate_"+i).length>0){  
                           $("#addCandidate_"+i).val(value);   
                       }
                       if(i=='attachment' && value!='')
                       {
                           if($("#addCandidate_resume").parent('li').children('li').length>0)
                              $("#addCandidate_resume").parent('li').children('li').remove();
                              
                           $("#addCandidate_resume").parent('li').append(value);
                       }
                       if(i=='contactNumber')
                       {
                            $("#addCandidate_contactNo").val(value);   
                       }

                     });
                        if(data.duplicate === false){
                            
                        $("#duplicate_status").val("1");
                    }else
                    {
                        $("#duplicate_status").val("");
                    }
                   
                             setResumeRule();
                             $.unblockUI();
                }//duplicate   
                    
                }
                else
                 { 
                   $('#frmAddCandidate').reset(passObj); 
                   $("#addCandidate_email").val(prev_email);
                   $("#addCandidate_contactNo").val(prev_contact);
                     setResumeRule();
                    $.unblockUI();
                 }   
		}
	    });
            return true;
    }
function waitForRespose(){
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
                
    	
    }
    jQuery.fn.reset = function (passObj) {
        var email=$("#addCandidate_email").val();
        var fname=$("#addCandidate_firstName").val();
        var mname=$("#addCandidate_middleName").val();
        var lname=$("#addCandidate_lastName").val();
        var contactNo=$("#addCandidate_contactNo").val();
  $(this).each (function() {   
        this.reset(); });
  $("#addCandidate_id").val('');
  if(passObj=='email')
   $("#addCandidate_email").val(email);
  else
   $("#addCandidate_email").val();
  if(passObj=='contactNo')
   $("#addCandidate_contactNo").val(contactNo);
  else
   $("#addCandidate_contactNo").val();
  $("#duplicate_status").val('');
  $("#addCandidate_resume").parent('li').children('li').remove();
 
}
function setResumeRule()
{
    if($("#addCandidate_id").val().length>0){
              $("#addCandidate_resume").rules("remove"); 
              $("#addCandidate_resume").rules("add",{required : false}); 
            }
            else {
                $("#addCandidate_resume").rules("remove"); 
              $("#addCandidate_resume").rules("add",{required : true}); 
            }
}
    $("div.fadable").delay(2000)
        .fadeOut("slow", function () {
            $("div.fadable").remove();
        }); 
</script>
