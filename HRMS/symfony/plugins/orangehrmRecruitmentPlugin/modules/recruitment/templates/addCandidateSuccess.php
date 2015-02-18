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
<?php use_javascripts_for_form($form) ?>
<?php use_stylesheets_for_form($form) ?>
<style type="text/css">
	strong{
	 font-weight:bold;
	}

</style>
<?php use_stylesheet(plugin_web_path('orangehrmRecruitmentPlugin', 'css/addCandidateSuccess.css')); ?>
<?php use_javascript(plugin_web_path('orangehrmRecruitmentPlugin', 'js/addCandidateSuccess')); ?>
<?php use_javascript(plugin_web_path('', 'js/ckeditor/ckeditor')); ?>

<?php $title = ($candidateId > 0) ? __('Candidate') : __('Add Candidate'); ?>
<?php
$allVacancylist[] = array("id" => "", "name" => __('-- Select --'));
$allowedVacancylist[] = array("id" => "", "name" => __('-- Select --'));
$allowedVacancylistWithClosedVacancies[] = array("id" => "", "name" => __('-- Select --'));
$allowedVacancyIdArray[] = array();
$closedVacancyIdArray[] = array();
foreach ($jobVacancyList as $vacancy) {
    $newVacancyId = $vacancy['id'];
    $newVacancyName = ($vacancy['status'] == JobVacancy::CLOSED) ? $vacancy['name'] . " (" . __('Closed') . ")" : $vacancy['name'];
    $allVacancylist[] = array("id" => $newVacancyId, "name" => $newVacancyName);
    if (in_array($vacancy['id'], $form->allowedVacancyList)) {
        $allowedVacancylistWithClosedVacancies[] = array("id" => $newVacancyId, "name" => $newVacancyName);
        $allowedVacancyIdArray[] = $newVacancyId;
        if ($vacancy['status'] == JobVacancy::ACTIVE) {
            $allowedVacancylist[] = array("id" => $newVacancyId, "name" => $newVacancyName);
        } else {
            $closedVacancyIdArray[] = $newVacancyId;
        }
    }
}
$isConsultant = false;
if ($_SESSION ['isConsultant']){
	$isConsultant = true;
}
?>
<style type="text/css">
.actionDrpDown {
    width: 170px;
    margin:1px 10px 0 0px;
}    
.fileSelector span.validation-error {
	font-size: 12px;
	//display: block;
	position: absolute;
	top: 30px;
	left: 150px;
	margin-top: 6px;
}
</style>
<div class="box" id="addCandidate">

    <div class="head"><h1 id="addCandidateHeading"><?php echo $title; ?></h1></div>
    <div class="inner">
        <?php include_partial('global/flash_messages', array('prefix' => 'addcandidate')); ?>
        <form name="frmAddCandidate" id="frmAddCandidate" method="post" action="<?php echo url_for('recruitment/addCandidate?id=' . $candidateId); ?>" enctype="multipart/form-data">

            <?php echo $form['_csrf_token']; ?>
	     <?php echo $form["referralId"]->render(); ?>
            <fieldset>
                <ol>
                	
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

                    <li>
                        <?php echo $form['email']->renderLabel(__('E-mail') . ' <em>*</em>'); ?>
                        <?php if($isConsultant){?>
                            <input type="hidden" name="addCandidate[email]" value="<?php echo $form->emailToDisplay; ?>" class="formInputText valid" id="addCandidate_email">
                            <input type="text" value="<?php echo $form->emailToDisplay; ?>" class="formInputText" disabled="disabled">
                        <?php }else{
                        	echo $form['email']->render(array("class" => "formInputText")); 
                        }?>
                        <div style='float:left; padding-left:10px;padding-right:10px'>
                            <?php echo $form['alternateEmail']->renderLabel(__('Alternate E-mail'), array("class " => "alternateEmail")); ?>
                            <?php echo $form['alternateEmail']->render(array("class" => "contactNo")); ?>
                        </div>
                    </li>
                    <li>
                        <?php echo $form['contactNo']->renderLabel(__('Contact No'). ' <em>*</em>', array("class " => "contactNoLable")); ?>
                        <?php if($isConsultant){?>
                            <input type="hidden" name="addCandidate[contactNo]" value="<?php echo $form->contactNoToDisplay; ?>" class="contactNo valid" id="addCandidate_contactNo">
                            <input type="text" value="<?php echo $form->contactNoToDisplay; ?>" class="formInputText" disabled="disabled">
                        <?php }else{
                        	echo $form['contactNo']->render(array("class" => "formInputText")); 
                        }?>
                        <div style='float:left; padding-left:10px;padding-right:10px'>
                            <?php echo $form['alternateNumber']->renderLabel(__('Alternate Contact No'), array("class " => "alternateNumber")); ?>
                            <?php echo $form['alternateNumber']->render(array("class" => "contactNo")); ?>
                        </div>
                     </li>
                </ol>
                <ol>
                    <li  class="line">
                         <?php 
                        /* Modified By: Shagupta Faras
                         * Modified On: 16-07-2014
                         * DESC: Displayed multiple vacancies with there action status with add more functionality
                         */
                        
                        if ($candidateId > 0) : ?>
                           <?php $candidateVacancy = $actionForm->candidate->getJobCandidateVacancy();
                            $obj=$actionForm->getCandidateService()->getCandidateVacancy($candidateId);
                            $positiveFlowAction=array(
                                PluginJobCandidateVacancy::RECRUITMENT_CANDIDATE_STATUS_APPLICATION_INITIATED,
                                PluginJobCandidateVacancy::RECRUITMENT_CANDIDATE_STATUS_INTERVIEW_SCHEDULED,
                                PluginJobCandidateVacancy::RECRUITMENT_CANDIDATE_STATUS_INTERVIEW_PASSED,
                                PluginJobCandidateVacancy::RECRUITMENT_CANDIDATE_STATUS_JOB_OFFERED,
                                PluginJobCandidateVacancy::RECRUITMENT_CANDIDATE_STATUS_HIRED
                            );
                           $objInterviewScheduleStatus=Doctrine::getTable('JobCandidateVacancy')->findByCandidateIdAndStatus($candidateId,'INTERVIEW SCHEDULED')->toArray();                       
                           $objInterviewPassStatus=Doctrine::getTable('JobCandidateVacancy')->createQuery()->where('candidateId=?',$candidateId)->andWhereIn('status',$positiveFlowAction)->fetchArray();
                           
                           $i = 0; 
                          ?><label class="vacancyDrpLabel" for="addCandidate_vacancy">Job Vacancy<span class="required">*</span></label>
                        <table class="vacancyTbl"><tr>
                            <th style="width:33%;">Vacancy</th>
                            <?php if(!$isConsultant)?>
                            <th>Action</th>
                            <th>Status</th>
                            </tr>
                          <?php 
                           foreach($obj as $candidateVacancy){ ?>
                        
                            <?php if ($candidateVacancy->getVacancyId() > 0) : ?>      
                            
                           <!-- <li id="actionPane" style="float:left;width:100%;"> -->
                            <tr><td>
                            <label class="select-static"> <?php echo $candidateVacancy->getJobVacancy()->getName();  ?></label>
                             </td>
                                
                            <?php                           
                             $widgetName = $candidateVacancy->getId();
                             $candidate_status = $candidateVacancy->getCandidateStatus();
                             if($isConsultant){
                              echo "<td>";
                              echo $actionForm[$widgetName]->render(array("class" => "disabledActionDrpDown", "disabled"=>"true"));
                              echo '</td><td><span class="status" style="font-weight: bold">'.__(ucwords(strtolower($candidate_status))).'</span></td>';
                             }else{
                               if(count($objInterviewPassStatus)==0) {
	                              echo "<td>";
	                              echo $actionForm[$widgetName]->render(array("class" => "actionDrpDown"));                              
	                              echo '</td><td><span class="status" style="font-weight: bold">'.__(ucwords(strtolower($candidate_status))).'</span></td>';
	                           }else
	                            {    
	                            if(($objInterviewPassStatus[0]['id']==$candidateVacancy->getId()) || (($candidate_status==PluginJobCandidateVacancy::RECRUITMENT_CANDIDATE_STATUS_APPLICATION_INITIATED) && count($objInterviewScheduleStatus)>0)) {
	                              echo "<td>";
	                              echo $actionForm[$widgetName]->render(array("class" => "actionDrpDown")); 
	                              echo '</td><td><span class="status" style="font-weight: bold">'.__(ucwords(strtolower($candidate_status))).'</span></td>';                             
	                             } else {
	                              echo "<td>";                 
	                              echo $actionForm[$widgetName]->render(array("class" => "disabledActionDrpDown", "disabled"=>"true"));                                   
	                              echo '</td><td><span class="status" style="font-weight: bold">'.__(ucwords(strtolower($candidate_status))).'</span><label style="float:right;width:175px;">[Hold for other candidature(s)]</label></td>';                             
	                              }   
	                            }   
                            }
                            ?> </tr>
                            <?php $i++; ?>
                           <!-- </li>-->
                            <?php endif;
                            
                           }//foreach?>
                        <?php endif; ?>    </table>               
                    </li>   
                    <!-- Add More Functionality -->
                    <div id="widContainer">
                      
                    </div>
                    <?php 
                    /* Only Hiring manager & admin can add more vacacnies */
                    if(count($vacancyList)>1 && !$isConsultant){ ?>
                    <li>
                        <h3><label for="addButton">&nbsp;</label><a href="#" id="addButton" class= "links" >Add More Vacancies</a></h3>
                    </li>
                    <?php }?>
                    <!-- Resume block : Begins -->

                    <li  class = "fileSelector">    

                        <?php
                        if ($form->attachment == "") {
                            echo $form['resume']->renderLabel(__('Resume'. ' <span class="required">*</span>'), array("class " => "resume"));
                            echo $form['resume']->render();
                            //echo "<label class=\"fieldHelpBottom\">" . __(CommonMessages::FILE_LABEL_DOC) . "</label>";
                            echo '<div style ="float:left;padding-top:7px;margin-left:10px;">' . __(CommonMessages::FILE_LABEL_DOC) . '</div>';
                                                        
                        } else {
                            $attachment = $form->attachment;
                            $linkHtml = "<div id=\"fileLink\"><a target=\"_blank\" class=\"fileLink\" href=\"";
                            $linkHtml .= url_for('recruitment/viewCandidateAttachment?attachId=' . $attachment->getId());
                            $linkHtml .= "\">{$attachment->getFileName()}</a></div>";

                            echo $form['resumeUpdate']->renderLabel(__('Resume'));
                            echo $linkHtml;
                            echo "<li class=\"radio noLabel\" id=\"radio\">";
                            echo $form['resumeUpdate']->render(array("class" => "fileEditOptions"));
                            echo "</li>";
                            echo "<li id=\"fileUploadSection\" class=\"noLabel\">";
                            echo $form['resume']->renderLabel(' ');
                            echo $form['resume']->render(array("class " => "duplexBox"));
                            echo "<label class=\"fieldHelpBottom\">" . __(CommonMessages::FILE_LABEL_DOC) . "</label>";
                            echo "</li>";
                        }
                        ?>
                    </li>

                    <!-- Resume block : Ends -->
				</ol>
				<ol>
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
                    <?php if(!$onlyInterviewer){  ?>
					<div id="divCtcDetails">
	                <li>
                        <?php echo $form['currentCtc']->renderLabel(__('Current CTC (LPA)', array("class " => "currentCtc"))); ?>
                        <?php echo $form['currentCtc']->render(array("class" => "formInputText")); ?>
					<div style='float:left; padding-left:10px;padding-right:10px'>
                        <?php echo $form['expectedCtc']->renderLabel(__('Expected CTC (LPA)'), array("class " => "expectedCtc")); ?>
                        <?php echo $form['expectedCtc']->render(array("class" => "formInputText")); ?>
					</div>
					</li>
					</div>
                    <?php } ?>
    	          <li>
                    <?php echo $form['noticePeriod']->renderLabel(__('Notice Period (In Days)'), array("class " => "noticePeriod")); ?>
                    <?php echo $form['noticePeriod']->render(array("class" => "formInputText")); ?> 
	                 <div style='float:left; padding-left:10px;padding-right:10px'>
	                    <?php echo $form['expectedDoj']->renderLabel(__('Expected Date of Joining'), array("class " => "expectedDoj")); ?>
	                    <?php echo $form['expectedDoj']->render(array("class" => "formDateInput")); ?>
	                </div>
                </li>
         		<li>
                    <?php echo $form['educationGap']->renderLabel(__('Education Gap (in years)'), array("class " => "educationGap")); ?>
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
							<strong>#Tech</strong>=( Tech skills <strong><u>Example</u></strong> Spring Java, C++ in the project), <strong>Duration</strong> = (project duration  <strong><u>Example</u></strong> : 6 months, 1 year), 
							<strong>Project 2</strong>--(Project Domain), <strong>#Role</strong> = (current role  <strong><u>Example</u></strong>: Tech lead, Software developer, Analyst), 
							<strong>#Tech</strong>= (Tech skills  <strong><u>Example</u></strong>: Spring Java, C++ in the project),
							<strong>Duration</strong> = (project duration  <strong><u>Example</u></strong>: 6 months, 1 year),</br>
							Mention at least 2-3  projects.
	                   </p>
					</div>
                 </li>
              </ol>
              <ol>
                
                <li class="largeTextBox">
                <div class="microresume">
                    <?php echo $form['microResume']->renderLabel(__('Short Resume Summary'), array("class " => "microResume")); ?>
                    <div style='float:left;'>
                    	<?php echo $form['microResume']->render(array("class" => "formInputText ckeditor", "cols" => 35, "rows" => 4, "style"=>"height:150px;width:400px")); ?>
                    </div>
                    <div style='margin-left:10px; float:left; align:center;padding:10px;border:1px solid;border-color:#000000;border-radius:25px;height:150px; width:500px'>
	                   <p style="color:#000000"> Provide short summary of your resume. Use following format -</p>
	                   <p style="color:#000000;line-height:150%">
	                   	Loc- {Location}, {Degree} Total Exp. - {exp} Yrs, Relv. Exp.-{exp} yrs,<br/>
						Tech Skills- {skills}, <br/>
						Project details- 1. {Name of the recent project): ( Key skills used), 2. (Name of the project ): (key skills used), 3. (Project name): (key skills). Mention at least 2-3 projects.<br/>
						Current CTC- {current CTC} LPA, Exp CTC- {expected CTC} LPA, N.P.-{notice period} days <br/>
	                   </p>
					</div>
					</div>
                </li>
                
                <li>
                 <?php if(!$isConsultant){?>
                 <div class="microresume">
                 	<label for = "generateShortResume">&nbsp;</label>
                 	<!-- <input id = "generateShortResume" type="button" value="Generate ShortResume"/> -->
                 	<a href="#" id = "generateShortResume" class = "link" onclick = "createMicroresume()" style="float:top; font-weight:bold">Generate Short-summary</a>
                  </div><?php }?>
                </li>
              </ol>
              <ol>
                <li class="largeTextBox">
                    <?php echo $form['comment']->renderLabel(__('Notes (If any)'), array("class " => "comment")); ?>
                    <?php echo $form['comment']->render(array("class" => "formInputText", "cols" => 35, "rows" => 4, "style"=>"width:300px;height:50px")); ?>
                </li>
                <li>
                    <?php echo $form['appliedDate']->renderLabel(__('Date of Application'), array("class " => "appDate")); ?>
                    <?php if($isConsultant){?>
                    	<input id="addCandidate_appliedDate" type="hidden" name="addCandidate[appliedDate]" value="<?php echo $form->doaToDisplay ?>" class="formDateInput calendar hasDatepicker valid">
                    	<input type="text" value="<?php echo $form->doaToDisplay ?>" class="formDateInput" disabled="disabled">
                   <?php  }else {
                          echo $form['appliedDate']->render(array("class" => "formDateInput"));
                    } ?>
                </li>
			    <li> 
			    	 <?php echo $form['referralName']->renderLabel(__('Referred By')); ?>
                     <?php if($isConsultant){?>
                     	<input type="hidden" name="addCandidate[referralName]" value="<?php echo $form->refferedBYToDisplay ?>" class="formInputText ac_input valid" maxlength="90" id="addCandidate_referralName" >
                     	<input type="text" value="<?php echo $form->refferedBYToDisplay ?>" class="formInputText ac_input valid" disabled="disabled">
                     <?php }else{
                     	echo $form['referralName']->render(array("class" => "formInputText", "maxlength" => 90)); 
                     }?>
			    </li>
			    <li class="required new">
                    <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                </li>
            </ol>
            <p>
                <?php if ($edit): ?>
                    <input type="button" id="btnSave" value="<?php echo __("Save"); ?>"/>
                <?php endif; ?>
                <?php if ($candidateId > 0): ?>
                    <input type="button" id="btnBack" value="<?php echo __("Back"); ?>"/>
                <?php endif; ?>
            </p>
            <input type="hidden" name="hdnFlag" id="hdnFlag"/>
            </fieldset>
        </form>
    </div>

</div>

<?php if ($candidateId > 0) : ?>
    <?php $existingVacancyList = $actionForm->candidate->getJobCandidateVacancy(); ?>
    <?php include_component('core', 'ohrmList', $parmetersForListCompoment); ?>
<?php endif; ?>

<!-- Confirmation box - delete HTML: Begins -->
<div class="modal hide" id="deleteConfirmation">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('SynerzipHRM - Confirmation Required'); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo __(CommonMessages::DELETE_CONFIRMATION); ?></p>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" data-dismiss="modal" id="dialogDeleteBtn" value="<?php echo __('Ok'); ?>" />
        <input type="button" class="btn reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>" />
    </div>
</div>
<!-- Confirmation box HTML: Ends -->

<!-- Confirmation box - remove vacancies & save HTML: Begins -->
<div class="modal hide" id="deleteConfirmationForSave">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('SynerzipHRM - Confirmation Required'); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo __("This action will remove previous vacancy"); ?></p>
        <br>
        <p><?php echo __('Remove?'); ?></p>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" data-dismiss="modal" id="dialogSaveButton" value="<?php echo __('Ok'); ?>" />
        <input type="button" class="btn reset" data-dismiss="modal" id="dialogCancelButton" value="<?php echo __('Cancel'); ?>" />
    </div>
</div>
<!-- Confirmation box remove vacancies & save HTML: Ends -->

                                <script type="text/javascript">
                                    //<![CDATA[
                                    var employees = <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson()) ?> ;
                   					var employeesArray = eval(employees);
                   					var candidates = <?php echo json_encode($form->getAllCandidateList()); ?>;
    								var candidateList =eval(candidates);
    								var vacancies = <?php echo json_encode($form->getActiveVacancyListForMicroResume()); ?>;
    								var vacList =eval(vacancies);
                                    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
                                    var lang_firstNameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
                                    var lang_lastNameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
                                    var lang_emailRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
                                    var lang_contactNoRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
                                    var lang_validDateMsg = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
                                    var lang_validEmail = '<?php echo __(ValidationMessages::EMAIL_INVALID); ?>';
                                    var lang_refferalRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
                                    var list = <?php echo json_encode($allVacancylist); ?>;
                                    var allowedVacancylistWithClosedVacancies = <?php echo json_encode($allowedVacancylistWithClosedVacancies); ?>;
                                    var allowedVacancylist = <?php echo json_encode($allowedVacancylist); ?>;
                                    var allowedVacancyIdArray = <?php echo json_encode($allowedVacancyIdArray); ?>;
                                    var closedVacancyIdArray = <?php echo json_encode($closedVacancyIdArray); ?>;
                                    var lang_identical_rows = "<?php echo __('Cannot assign same vacancy twice'); ?>";
                                    var lang_tooLargeInput = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 30)); ?>";
                                    var lang_commaSeparated = "<?php echo __('Enter comma separated words').'...'; ?>";
                                    var currentDate = '<?php echo set_datepicker_date_format(date("Y-m-d")); ?>';
                                    var lang_dateValidation = "<?php echo __("Should be less than current date"); ?>";
                                    var lang_validPhoneNo = "<?php echo __(ValidationMessages::TP_NUMBER_INVALID); ?>";
                                    var lang_futureDateValidation = "<?php echo __("Should be greater than current date"); ?>";
                                    var lang_noMoreThan250 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>";
                                    var lang_noMoreThan100 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 100)); ?>";
                                    var lang_noMoreThan200 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 200)); ?>";
                                    var lang_noMoreThan2000 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 2000)); ?>";
                                    var lang_noMoreThan50 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 50)); ?>";
                                    var lang_noMoreThan4 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 4)); ?>";
                                    var lang_noMoreThan3 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 3)); ?>";
                                      var lang_noMoreThan10 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 10)); ?>";
                                    var lang_edit = "<?php echo __("Edit"); ?>";
                                    var lang_save = "<?php echo __("Save"); ?>";
                                    var lang_cancel = "<?php echo __("Cancel"); ?>";
                                    var candidateId = "<?php echo $candidateId; ?>";
                                    var attachment = "<?php echo $form->attachment; ?>";
                                    var changeStatusUrl = '<?php echo url_for('recruitment/changeCandidateVacancyStatus?'); ?>';
                                    var backBtnUrl = '<?php echo url_for('recruitment/viewCandidates?'); ?>';
                                    var cancelBtnUrl = '<?php echo url_for('recruitment/addCandidate?'); ?>';
                                    var interviewUrl = '<?php echo url_for('recruitment/jobInterview?'); ?>';
                                    var interviewAction = '<?php echo WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_INTERVIEW; ?>';
                                    var interviewAction2 = '<?php echo WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_2ND_INTERVIEW; ?>';
                                    var removeAction = '<?php echo JobCandidateVacancy::REMOVE; ?>';
                                    var lang_remove =  '<?php echo __("Remove"); ?>';
                                    var lang_editCandidateTitle = "<?php echo __('Edit Candidate'); ?>";
                                    var editRights = "<?php echo $edit; ?>";
                                    var activeStatus = "<?php echo JobCandidate::ACTIVE; ?>";
                                    var candidateStatus = "<?php echo $candidateStatus; ?>";
                                    var invalidFile = "<?php echo $invalidFile; ?>";
                                    var lang_typeForHints = '<?php echo __("Type for hints") . "..."; ?>';
                                    var lang_emailExistmsg = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
                                    //var lang_currentCtcRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
                                    //var lang_expectedCtcRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
                                   // var lang_noticePeriodRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
                                    var lang_noMoreThan1 = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 4)); ?>";
                                	var lang_validNo = '<?php echo __(ValidationMessages::VALID_NUMBER); ?>';
                                	var session_is_admin = '<?php echo $_SESSION['isAdmin']; ?>';
                                	var session_is_hiring_mnger = '<?php echo $_SESSION['isHiringManager']  ?>';
                                	var lang_vacancyRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
                                	var lang_validFirstName = '<?php echo __(ValidationMessages::ALPHA_CHARACTER, array('%name%' => "First Name")); ?>';
                                	var lang_validLastName = '<?php echo __(ValidationMessages::ALPHA_CHARACTER, array('%name%' => "Last Name")); ?>';
                                	var vacancyOptions='<?php echo html_entity_decode($vacancyOptions);?>';
                                	
                                	 	
	                        	 	 var projectDetail_editor =  CKEDITOR.replace( 'addCandidate_projectDetails', {
																	toolbar: [
																		[ 'Bold', 'Italic', 'Underline','-','Link', 'Unlink' ],
																	]
									 							});
										 
									 var microresume_editor = CKEDITOR.replace( 'addCandidate_microResume', {
																toolbar: [
																	[ 'Bold', 'Italic','Underline', '-', 'Link', 'Unlink' ],
																]
															  });
										
	                            	 if(session_is_admin=='Yes' || session_is_hiring_mnger=='1')
	                            	 {
	                                	document.getElementById('divCtcDetails').style.display="block";
	                            	 }
                                	
                                	//function to create resume
                                	function createMicroresume(){
									var	resumeStr = "Loc- "+$('#addCandidate_originalLocation').val()+", "+$('#addCandidate_educationDetailDegree').val()+" in "+$('#addCandidate_educationDetailSpec').val()+", Total Exp.- "+$('#addCandidate_totalExperience').val()+" Yrs, Relv. Exp.- "+$('#addCandidate_relevantExperience').val()+" Yrs.\n"+
										    "Skills- "+$('#addCandidate_keySkills').val()+"\n"+
											"Project details- "+projectDetail_editor.getData()+"\n"+ 
											"Current CTC- "+$('#addCandidate_currentCtc').val()+" LPA, Exp CTC- "+$('#addCandidate_expectedCtc').val()+" LPA, N.P.- "+$('#addCandidate_noticePeriod').val()+" Days \n";
								    	microresume_editor.setData(resumeStr)
									}
                                    
								    
$(document).ready(function() {
 var addedVacancy=<?php  if(empty($form->addedVacancy))echo "null"; else echo $form->addedVacancy;?>;
 var actionForm='<?php echo json_encode($actionForm);?>';
 
    });
</script>
