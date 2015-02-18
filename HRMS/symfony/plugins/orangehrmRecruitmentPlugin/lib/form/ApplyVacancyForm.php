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
class ApplyVacancyForm extends BaseForm {

    private $candidateService;
    private $recruitmentAttachmentService;
    public $attachment;
    public $candidateId;
    private $vacancyService;
    private $allowedFileTypes = array(
        "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        "doc" => "application/msword",
        "doc" => "application/x-msword",
        "doc" => "application/vnd.ms-office",
        "odt" => "application/vnd.oasis.opendocument.text",
        "pdf" => "application/pdf",
        "pdf" => "application/x-pdf",
        "rtf" => "application/rtf",
        "rtf" => "text/rtf",
        "txt" => "text/plain"
    );
    
    public function getVacancyService() {
    	if (is_null($this->vacancyService)) {
    		$this->vacancyService = new VacancyService();
    		$this->vacancyService->setVacancyDao(new VacancyDao());
    	}
    	return $this->vacancyService;
    }

    /**
     *
     * @return <type>
     */
    public function getCandidateService() {
        if (is_null($this->candidateService)) {
            $this->candidateService = new CandidateService();
            $this->candidateService->setCandidateDao(new CandidateDao());
        }
        return $this->candidateService;
    }

    /**
     *
     * @return <type>
     */
    public function getRecruitmentAttachmentService() {
        if (is_null($this->recruitmentAttachmentService)) {
            $this->recruitmentAttachmentService = new RecruitmentAttachmentService();
            $this->recruitmentAttachmentService->setRecruitmentAttachmentDao(new RecruitmentAttachmentDao());
        }
        return $this->recruitmentAttachmentService;
    }
    
    public function getAllCandidateList(){
    	$candidateArray = array();
    	$candidateList = $this->getCandidateService()->getAllCandidateList();
    	foreach($candidateList as $candidate){
    		$candidateArray[] = array('candidateId' => $candidate['id'], 'contactNumber' => $candidate['contactNumber'], 'email' => $candidate['email'],'candidateName' => trim(trim($candidate["firstName"]." ".trim($candidate["lastName"]))));
    	}
    	return $candidateArray;
    }

    public function configure() {

        $this->candidateId = $this->getOption('candidateId');
        $attachmentList = $this->attachment;
        if (count($attachmentList) > 0) {
            $this->attachment = $attachmentList[0];
        }

        //creating widgets
        $this->setWidgets(array(
            'id' => new sfWidgetFormInputHidden(),
            'firstName' => new sfWidgetFormInputText(),
            'middleName' => new sfWidgetFormInputText(),
            'lastName' => new sfWidgetFormInputText(),
            'email' => new sfWidgetFormInputText(),
            'contactNo' => new sfWidgetFormInputText(),
            'resume' => new sfWidgetFormInputFileEditable(array('edit_mode' => false, 'with_delete' => false, 'file_src' => '')),
            'keyWords' => new sfWidgetFormInputText(),
            'comment' => new sfWidgetFormTextArea(),
            'vacancyList' => new sfWidgetFormInputHidden(),
       		 'noticePeriod' => new sfWidgetFormInputText(),
            'expectedDoj' => new ohrmWidgetDatePicker(array(), array('id' => 'addCandidate_expectedDoj')),
            'visaStatus' => new sfWidgetFormInputText(),
            'preferredLocation' => new sfWidgetFormInputText(),
            'originalLocation' => new sfWidgetFormInputText(),
            'educationDetailDegree' => new sfWidgetFormInputText(),
            'educationDetailSpec' => new sfWidgetFormInputText(),
            'educationDetailPerc'=> new sfWidgetFormInputText(),
            'totalExperience' => new sfWidgetFormInputText(),
            'relevantExperience' => new sfWidgetFormInputText(),
            'currentCompany' => new sfWidgetFormInputText(),
			'designation' => new sfWidgetFormInputText(),
            'appliedDate' => new ohrmWidgetDatePicker(array(), array('id' => 'addCandidate_appliedDate')),
        	'currentCtc' => new sfWidgetFormInputText(),
            'expectedCtc' => new sfWidgetFormInputText(),
            'projectDetails' => new sfWidgetFormTextArea(),
            'keySkills' => new sfWidgetFormInputText(),
            'communicationSkills' => new sfWidgetFormInputText(),
            'educationGap' => new sfWidgetFormInputText(),
            'workGap' => new sfWidgetFormInputText(),
            'stability' => new sfWidgetFormSelect(
				array(
					'choices' => array(
						'' => 'Select',
						'Good' => 'Good',
						'Medium' => 'Medium',
						'Bad' => 'Bad')
				)),
			'employmentType' => new sfWidgetFormSelect(
					array(
						'choices' => array(
							'' => 'Select',
							'Full time' => 'Full time',
							'Part time' => 'Part time')
					)),
             'alternateEmail' => new sfWidgetFormInputText(),
             'alternateNumber' => new sfWidgetFormInputText(),
             'microResume' => new sfWidgetFormTextArea()
        ));
		
        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
        $this->setValidators(array(
            'id' => new sfValidatorNumber(array('required' => false)),
            'firstName' => new sfValidatorString(array('required' => true, 'max_length' => 35)),
            'middleName' => new sfValidatorString(array('required' => false, 'max_length' => 35)),
            'lastName' => new sfValidatorString(array('required' => true, 'max_length' => 35)),
            'email' => new sfValidatorEmail(array('required' => true, 'max_length' => 100)),
            'contactNo' => new sfValidatorNumber(array('required' => true,  'max'=>9999999999)),
            'resume' => new sfValidatorFile(array('required' => false, 'max_size' => 1024000, 'validated_file_class' => 'orangehrmValidatedFile')),
            'keyWords' => new sfValidatorString(array('required' => false, 'max_length' => 255)),
            'comment' => new sfValidatorString(array('required' => false)),
            'vacancyList' => new sfValidatorString(array('required' => true)),
        	'noticePeriod' => new sfValidatorNumber(array('required' => false)),
        	'expectedDoj' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false),
                    array('invalid' => 'Date format should be ' . $inputDatePattern)),
        	'originalLocation' => new sfValidatorString(array('required' => false)),
            'visaStatus' => new sfValidatorString(array('required' => false)),
            'microResume' => new sfValidatorString(array('required' => false)),
            'preferredLocation' => new sfValidatorString(array('required' => false)),
            'educationDetailDegree' => new sfValidatorString(array('required' => false, 'max_length' => 100)),
            'educationDetailSpec' => new sfValidatorString(array('required' => false, 'max_length' => 100)),
            'educationDetailPerc' => new sfValidatorNumber(array('required' => false, 'min' => 0,'max'=>100)),
            'totalExperience' => new sfValidatorNumber(array('required' => false)),
            'relevantExperience' => new sfValidatorNumber(array('required' => false)),
            'currentCompany' => new sfValidatorString(array('required' => false)),
            'designation' => new sfValidatorString(array('required' => false)),
            'stability' => new sfValidatorString(array('required' => false, 'max_length' => 10)),
            'appliedDate' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false),
                    array('invalid' => 'Date format should be ' . $inputDatePattern)),
            'currentCtc' => new sfValidatorString(array('required' => false)),
        	'expectedCtc' => new sfValidatorString(array('required' => false)),
        	'projectDetails' => new sfValidatorString(array('required' => false, 'max_length' => 10000)),        
            'keySkills' => new sfValidatorString(array('required' => false)),
            'communicationSkills' => new sfValidatorString(array('required' => false)),
            'educationGap' => new sfValidatorNumber(array('required' => false)),
            'workGap' => new sfValidatorNumber(array('required' => false)),
           	'employmentType' => new sfValidatorString(array('required' => false)),
           	'alternateEmail' => new sfValidatorEmail(array('required' => false, 'max_length' => 100, 'trim' => true)),
            'alternateNumber' =>new sfValidatorNumber(array('required' => false,  'max'=>9999999999)),
        ));

        $this->widgetSchema->setNameFormat('addCandidate[%s]');
        $this->widgetSchema['appliedDate']->setAttribute();
        $this->setDefault('appliedDate', set_datepicker_date_format(date('Y-m-d')));

        if (!empty($this->candidateId)) {
            $candidate = $this->getCandidateService()->getCandidateById($this->candidateId);
            $this->setDefault('firstName', $candidate->getFirstName());
            $this->setDefault('middleName', $candidate->getMiddleName());
            $this->setDefault('lastName', $candidate->getLastName());
            $this->setDefault('email', $candidate->getEmail());
            $this->setDefault('contactNo', $candidate->getContactNumber());
            $this->attachment = $candidate->getJobCandidateAttachment();
            $this->setDefault('keyWords', $candidate->getKeywords());
            $this->setDefault('comment', $candidate->getComment());
            $candidateVacancyList = $candidate->getJobCandidateVacancy();
            $vacancyList = array();
            foreach ($candidateVacancyList as $candidateVacancy) {
                $vacancyList[] = $candidateVacancy->getVacancyId();
            }
            $this->setDefault('vacancyList', implode("_", $vacancyList));
            $this->setDefault('expectedDoj', $candidate->getExpectedDoj());
        	$this->setDefault('noticePeriod', $candidate->getNoticePeriod());
        	$this->setDefault('visaStatus', $candidate->getVisaStatus());
        	$this->setDefault('originalLocation', $candidate->getOriginalLocation());
        	
        	$this->setDefault('microResume', $candidate->getMicroResume()); 
       		$this->setDefault('preferredLocation', $candidate->getPreferredLocation());
    		$this->setDefault('educationDetailDegree',$candidate->getEducationDetailDegree());
    		$this->setDefault('educationDetailSpec',$candidate->getEducationDetailSpec());
    		$this->setDefault('educationDetailPerc',$candidate->getEducationDetailPerc());
    		$this->setDefault('totalExperience',$candidate->getTotalExperience());
    		$this->setDefault('relevantExperience',$candidate->getRelevantExperience());
    		$this->setDefault('currentCompany',$candidate->getCurrentCompany());
    		$this->setDefault('designation',$candidate->getDesignation());
    		$this->setDefault('stability',$candidate->getStability());
    		$this->setDefault('appliedDate', set_datepicker_date_format($candidate->getDateOfApplication()));
    		$this->setDefault('currentCtc', $candidate->getCurrentCtc());        
            $this->setDefault('expectedCtc', $candidate->getExpectedCtc());
            $this->setDefault('projectDetails',$candidate->getProjectDetails());
    		$this->setDefault('keySkills',$candidate->getKeySkills());
    		$this->setDefault('communicationSkills',$candidate->getCommunicationSkills());
    		$this->setDefault('educationGap',$candidate->getEducationGap());
    		$this->setDefault('workGap',$candidate->getWorkGap());
    		$this->setDefault('employmentType',$candidate->getEmploymentType());
    		$this->setDefault('alternateEmail',$candidate->getAlternateEmail());
    		$this->setDefault('alternateNumber',$candidate->getAlternateNumber());
        }else{
        	$projectTemplate = "<p>&nbsp;<strong>Project 1</strong>- &nbsp; &nbsp; &nbsp; ,&nbsp;<strong>#Role</strong>&nbsp;= &nbsp; &nbsp; &nbsp; &nbsp;,&nbsp;" .
        			"<strong>#Tech</strong>= &nbsp; &nbsp; &nbsp; &nbsp;,&nbsp;<strong>#Duration</strong>&nbsp;= &nbsp; &nbsp; &nbsp;<br></p><p>&nbsp;" .
        			"<strong>Project 2</strong>- &nbsp; &nbsp; &nbsp; ,&nbsp;<strong>#Role</strong>&nbsp;= &nbsp; &nbsp; &nbsp; &nbsp;,&nbsp;" .
        			"<strong>#Tech</strong>= &nbsp; &nbsp; &nbsp; &nbsp;,&nbsp;<strong>#Duration</strong>&nbsp;= &nbsp; &nbsp; &nbsp;<br></p>" .
        			"<p>&nbsp;<strong>Project 3</strong>- &nbsp; &nbsp; &nbsp; ,&nbsp;<strong>#Role</strong>&nbsp;= &nbsp; &nbsp; &nbsp; &nbsp;,&nbsp;" .
        			"<strong>#Tech</strong>= &nbsp; &nbsp; &nbsp; &nbsp;,&nbsp;<strong>#Duration</strong>&nbsp;= &nbsp; &nbsp; &nbsp;<br></p>";
        	$this->setDefault('projectDetails',$projectTemplate);
        }
    }

    public function save() {
        $file = $this->getValue('resume');
        
          if($this->getValue('id')>0)
                {
                  $existingCandidateId=$this->getValue('id');
                  //$candidate=Doctrine::getTable('JobCandidate')->findOneById($existingCandidateId);
                  $candidate=$this->getCandidateService()->getCandidateById($existingCandidateId);
                  $resumObj=$this->getCandidateService()->getCandidateAttachment($candidate->getId());   
                  if(!is_null($resumObj))
                  {
                   $resume =$resumObj;
                  }
                  else 
                  $resume = new JobCandidateAttachment();
                  
                } else {                    
                  $candidate = new JobCandidate();
                  $resume = new JobCandidateAttachment();
                }  
        
        $vacnacyId = $this->getValue('vacancyList');
        $resultArray = array();
       
         /* Added By : Shagupta Faras
             * Added On : 22-07-2014
             * DESC: Agenet can apply for multiple vacancies for one candidate
             */
        
       
           /* if($this->checkForDuplicateCandidate()) {
        	$resultArray = array();
        	$resultArray['messageType'] = 'warning';
        	$resultArray['message'] = __("Duplicate Candidate");
        	return $resultArray;
            }*/
        
          if(count($file)>0) {  
        if (!($this->isValidResume($file))) {
            $resultArray['messageType'] = 'warning';
            $resultArray['message'] = __(TopLevelMessages::FILE_TYPE_SAVE_FAILURE);
            return $resultArray;
            }
            
          }    
          
            $this->candidateId = $this->_getNewlySavedCandidateId($candidate,$vacnacyId);
            $resultArray['candidateId'] = $this->candidateId;
                 if(count($file)>0) {
            $resumeId = $this->_saveResume($file, $resume, $this->candidateId);
        }

        
         $candidateVacancyObj=Doctrine::getTable('JobCandidateVacancy')->createQuery()->where('candidate_id='.$this->candidateId)->andWhere('vacancy_id='.$vacnacyId)->execute();  
         
        if($candidateVacancyObj->count() > 0) {
         $resultArray['messageType'] = 'warning';
            $resultArray['message'] ='Vacancy :'. __(ValidationMessages::ALREADY_EXISTS);
            return $resultArray;   
        }
        else
        $this->_saveCandidateVacancies($vacnacyId, $this->candidateId);
        
        //Now send mail to HR admin and Hiring manager
        $empNumber = sfContext::getInstance()->getUser()->getEmployeeNumber();
        $addCandidateMailer = new AddCandidateMailer($empNumber, $this->candidateId, $vacnacyId);
	    $addCandidateMailer->send();
	    
        return $resultArray;
    }
    
    /**
     *@desc Return true if candidate is duplicate
     * 
     * @return <boolean>
     */
    public function checkForDuplicateCandidate() {
    	$isDuplicate = false;
    	$name = trim($this->getValue('firstName'))." ".trim($this->getValue('lastName'));
    	$email = trim($this->getValue('email'));
    	$contact = trim($this->getValue('contactNo'));
    	$candidateList = $this->getCandidateService()->getAllCandidateList();
    	$date = date('Y-m-d', strtotime("-6 Months"));
    	foreach ($candidateList as $candidate) {
	    	//if((strtolower($name) == strtolower($candidate->getFirstName())." ".strtolower($candidate->getLastName())) && ((strtolower($email) == strtolower($candidate->getEmail())) || (strtolower($contact) == strtolower($candidate->getContactNumber())))) {
            if((strtolower($email) == strtolower($candidate->getEmail()))) {
    		    			if(strtotime($date) < strtotime($candidate->getDateOfApplication())) {
    				$isDuplicate = true;
    			} else {
    				$candidate->isDeleted = JobCandidate::IS_DELETED;
    				$cv = $candidate->getJobCandidateVacancy();
    				$this->getCandidateService()->saveCandidate($candidate);
    				$candidateHistory = new CandidateHistory();
    				$candidateHistory->setCandidateId($candidate->getId());
    				$candidateHistory->setVacancyId($cv->getVacancyId());
    				$candidateHistory->setAction(WorkflowStateMachine::RECRUITMENT_CANDIDATE_ACTION_DELETE);
    				$candidateHistory->setCandidateVacancyName($cv->getVacancyName());
    				$candidateHistory->setPerformedBy(sfContext::getInstance()->getUser()->getEmployeeNumber());
    				$candidateHistory->setPerformedDate(date('Y-m-d') . " " . date('H:i:s'));
    				$candidateHistory->setNote("Duplicate candidate");
    				$this->getCandidateService()->saveCandidateHistory($candidateHistory);
    			}
    		}
    	}
    	return $isDuplicate;
    }

    /**
     *
     * @param <type> $candidate
     * @return <type>
     */
    private function _getNewlySavedCandidateId($candidate,$vacnacyId) {
		$flag = $this->getVacancyMicroResumeFlagById($vacnacyId);
        $candidate->firstName = trim($this->getValue('firstName'));
        $candidate->middleName = trim($this->getValue('middleName'));
        $candidate->lastName = trim($this->getValue('lastName'));
        $candidate->email = trim($this->getValue('email'));
        $candidate->noticePeriod = (integer)$this->getValue('noticePeriod');
        $candidate->expectedDoj = $this->getValue('expectedDoj');
        $candidate->visaStatus = $this->getValue('visaStatus');
        $candidate->originalLocation = $this->getValue('originalLocation');
        $candidate->comment = $this->getValue('comment');
        $candidate->contactNumber = $this->getValue('contactNo');
        $candidate->keywords = $this->getValue('keyWords');
        $date = date('Y-m-d');
        $candidate->dateOfApplication = $date . " " . date('H:i:s');
        $candidate->status = JobCandidate::ACTIVE;
        $candidate->modeOfApplication = JobCandidate::MODE_OF_APPLICATION_ONLINE;
        $candidate->addedPerson = sfContext::getInstance()->getUser()->getEmployeeNumber();
        $candidate->preferredLocation = $this->getValue('preferredLocation');
        $candidate->educationDetailDegree = $this->getValue('educationDetailDegree');
        $candidate->educationDetailSpec = $this->getValue('educationDetailSpec');
        $candidate->educationDetailPerc = $this->getValue('educationDetailPerc');
        $candidate->totalExperience = $this->getValue('totalExperience');
        $candidate->relevantExperience = $this->getValue('relevantExperience');
        $candidate->currentCompany = $this->getValue('currentCompany');
        $candidate->designation = $this->getValue('designation');
        $candidate->stability = $this->getValue('stability');
        $candidate->currentCtc = (float)$this->getValue('currentCtc');
        $candidate->expectedCtc = (float)$this->getValue('expectedCtc');
        $candidate->projectDetails = $this->getValue('projectDetails');
		$candidate->keySkills = $this->getValue('keySkills');
		$candidate->communicationSkills = $this->getValue('communicationSkills');
		$candidate->educationGap = $this->getValue('educationGap');
        $candidate->workGap = $this->getValue('workGap');
        $candidate->employmentType = $this->getValue('employmentType');
        $candidate->alternateNumber = $this->getValue('alternateNumber');
        $candidate->alternateEmail = $this->getValue('alternateEmail');
        $expectedDojVal = $this->getValue('expectedDoj');
 
        
        if(($expectedDojVal == "" || $expectedDojVal=="yyyy-mm-dd" || $expectedDojVal==null) && ($this->candidateId != null))
        {
        	$candidate->expectedDoj = "0000-00-00";
        }
        
    	if ($this->getValue('appliedDate') == "") {
            $candidate->dateOfApplication = date('Y-m-d');
        } else {
            $candidate->dateOfApplication = $this->getValue('appliedDate');
        }
    	
    	if($flag==JobVacancy::SHOW_MICRORESUME){
	    	$candidate->microResume = $this->_generateMicroResume($this->getValue('microResume'), $candidate);
    	}
    	
        $candidateService = $this->getCandidateService();
        $candidateService->saveCandidate($candidate);
        $candidateId = $candidate->getId();
        return $candidateId;
    }
    
    /**
     * generate micro resume if not provided by user
     */
    private function _generateMicroResume($microresume, $candidate){
    	$candidateShortResume = $microresume;
	    if(empty($candidateShortResume)){
		    $candidateShortResume = "Loc- ". $candidate->originalLocation.", ".$candidate->educationDetailDegree." in ".$candidate->educationDetailSpec.", Total Exp.- ".$candidate->totalExperience." Yrs, Relv. Exp.- ".$candidate->relevantExperience." Yrs.\n".
			    "Skills- ".$candidate->keySkills."\n".
				"Project details- ".$candidate->projectDetails."\n". 
				"Current CTC- ".$candidate->currentCtc." LPA, Exp CTC- ".$candidate->expectedCtc." LPA, N.P.- ".$candidate->noticePeriod." Days \n";
	    }
	    return $candidateShortResume;
    }

    /**
     *
     * @param sfValidatedFile $file
     * @return <type> 
     */
    protected function isValidResume($file) {

        $validFile = false;

        $mimeTypes = array_values($this->allowedFileTypes);
        $originalName = $file->getOriginalName();

        if (($file instanceof orangehrmValidatedFile) && $originalName != "") {

            $fileType = $file->getType();

            if (!empty($fileType) && in_array($fileType, $mimeTypes)) {
                $validFile = true;
            } else {
                $fileType = $this->guessTypeFromFileExtension($originalName);

                if (!empty($fileType)) {
                    $file->setType($fileType);
                    $validFile = true;
                }
            }
        }

        return $validFile;
    }

    /**
     *
     * @param <type> $vacnacyId
     * @param <type> $candidateId 
     */
    protected function _saveCandidateVacancies($vacnacyId, $candidateId) {
        if (!empty($vacnacyId)) {
            $flag = $this->getVacancyMicroResumeFlagById($vacnacyId);
            $candidateVacancy = new JobCandidateVacancy();
            $candidateVacancy->candidateId = $candidateId;
            $candidateVacancy->vacancyId = $vacnacyId;
            $candidateVacancy->status = "SCREENING";
            $candidateVacancy->appliedDate = date('Y-m-d');
            $candidateService = $this->getCandidateService();
            $candidateService->saveCandidateVacancy($candidateVacancy);
            $history = new CandidateHistory();
            $history->candidateId = $candidateId;
            $history->action = WorkflowStateMachine::RECRUITMENT_CANDIDATE_ACTION_APPLY;
            $history->performedDate = $candidateVacancy->appliedDate . " " . date('H:i:s');
            $history->performedBy = sfContext::getInstance()->getUser()->getEmployeeNumber();
            $history->vacancyId = $vacnacyId;
            $history->candidateVacancyName = $candidateVacancy->getVacancyName();
            $this->getCandidateService()->saveCandidateHistory($history);
        }
    }

    /**
     *
     * @param <type> $file
     * @param <type> $resume
     * @param <type> $candidateId
     * @return <type>
     */
    private function _saveResume($file, $resume, $candidateId) {

        $tempName = $file->getTempName();
        $resume->fileContent = file_get_contents($tempName);
        $resume->fileName = $file->getOriginalName();
        $resume->fileType = $file->getType();
        $resume->fileSize = $file->getSize();
        $resume->fileSize = $file->getSize();
        $resume->candidateId = $candidateId;

        $recruitmentAttachmentService = $this->getRecruitmentAttachmentService();
        $recruitmentAttachmentService->saveCandidateAttachment($resume);

        $this->attachment = $resume;
    }

    /**
     * Guess the file mime type from the file extension
     *
     * @param  string $file  The absolute path of a file
     *
     * @return string The mime type of the file (null if not guessable)
     */
    public function guessTypeFromFileExtension($file) {

        $mimeType = null;

        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if (isset($this->allowedFileTypes[$extension])) {
            $mimeType = $this->allowedFileTypes[$extension];
        }

        return $mimeType;
    }

    /**
     *
     * @return JobCandidateAttachment
     */
    public function getResume() {
        return $this->attachment;
    }
    
    public function getVacancyMicroResumeFlagById($vacId) {
    	$list = array();
    	$vacancyProperties = array('id', 'hiringManagerId','flagForResume');
    	$activeVacancyList = $this->getVacancyService()->getVacancyPropertyList($vacancyProperties, JobVacancy::ACTIVE);
    	foreach ($activeVacancyList as $vacancy) {
    			if($vacId == $vacancy['id'])
    			{
    				$flag = $vacancy['flagForResume'];
    				return $flag;
    			}
    	}
    }
    

}

?>
