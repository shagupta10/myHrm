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
class AddCandidateForm extends BaseForm {

  	private $employeeService;
    private $vacancyService;
    private $candidateService;
    public $attachment;
    public $candidateId;
    private $recruitmentAttachmentService;
    private $addedBy;
    private $addedHistory;
    private $removedHistory;
    public $allowedVacancyList;
    public $empNumber;
    private $isAdmin;
    private $isRecruitmentManager;
    private $referredBy;
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
    public $addedVacancy;
    public $emailToDisplay;
    public $contactNoToDisplay;
    public $refferedBYToDisplay;
    public $doaToDisplay;
    private $isConsultant;
    const CONTRACT_KEEP = 1;
    const CONTRACT_DELETE = 2;
    const CONTRACT_UPLOAD = 3;

    /**
     * Get VacancyService
     * @returns VacncyService
     */
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

    public function getInterviewService() {
        if (is_null($this->interviewService)) {
            $this->interviewService = new JobInterviewService();
            $this->interviewService->setJobInterviewDao(new JobInterviewDao());
        }
        return $this->interviewService;
    }
    
     /**
     * Get VacancyService
     * @returns VacncyService
     */
    public function getEmployeeService() {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }
    

    /**
     *
     */
    public function configure() {

        $this->candidateId = $this->getOption('candidateId');
        $this->allowedVacancyList = $this->getOption('allowedVacancyList');
        $this->empNumber = $this->getOption('empNumber');
        $this->isAdmin = $this->getOption('isAdmin');
        $this->isRecruitmentManager = $this->getOption('isRecruitmentManager');
        $this->isConsultant = $this->getOption('isConsultant');
        $attachmentList = $this->attachment;
        if (count($attachmentList) > 0) {
            $this->attachment = $attachmentList[0];
        }
        $vacancyList = $this->getActiveVacancyList();
        if ($this->candidateId != null) {
            $candidateVacancyList = $this->getCandidateService()->getCandidateById($this->candidateId)->getJobCandidateVacancy();            
            $vacancy = $candidateVacancyList->getJobVacancy();
            if ($vacancy->getStatus() == JobVacancy::CLOSED) {
                $vacancyList[$vacancy->getId()] = $vacancy->getVacancyName();
            } elseif ($vacancy->getStatus() == JobVacancy::ACTIVE) {
                $vacancyList[$vacancy->getId()] = $vacancy->getName();
            }
        }

        $resumeUpdateChoices = array(self::CONTRACT_KEEP => __('Keep Current'),
            self::CONTRACT_DELETE => __('Delete Current'),
            self::CONTRACT_UPLOAD => __('Replace Current'));

        // creating widgets
        $this->setWidgets(array(
            'firstName' => new sfWidgetFormInputText(),
            'middleName' => new sfWidgetFormInputText(),
            'lastName' => new sfWidgetFormInputText(),
            'email' => new sfWidgetFormInputText(),
            'contactNo' => new sfWidgetFormInputText(),
            'resume' => new sfWidgetFormInputFileEditable(
                    array('edit_mode' => false,
                        'with_delete' => false,
                        'file_src' => '')),
            'keyWords' => new sfWidgetFormInputText(),
            'comment' => new sfWidgetFormTextArea(),
            'currentCtc' => new sfWidgetFormInputText(),
            'expectedCtc' => new sfWidgetFormInputText(),
            'noticePeriod' => new sfWidgetFormInputText(),
            'originalLocation' => new sfWidgetFormInputText(),
            'expectedDoj' => new ohrmWidgetDatePicker(array(), array('id' => 'addCandidate_expectedDoj')),
            'visaStatus' => new sfWidgetFormInputText(),
            'appliedDate' => new ohrmWidgetDatePicker(array(), array('id' => 'addCandidate_appliedDate')),
            'vacancy' => new sfWidgetFormSelect(array('choices' => $vacancyList)),
            'resumeUpdate' => new sfWidgetFormChoice(array('expanded' => true, 'choices' => $resumeUpdateChoices)),
            'referralName'=> new sfWidgetFormInputText(),
            'referralId'=> new sfWidgetFormInputHidden(),
            'microResume' => new sfWidgetFormTextArea(),
            'preferredLocation' => new sfWidgetFormInputText(),
            'educationDetailDegree' => new sfWidgetFormInputText(),
            'educationDetailSpec' => new sfWidgetFormInputText(),
            'educationDetailPerc'=> new sfWidgetFormInputText(),
            'totalExperience' => new sfWidgetFormInputText(),
            'relevantExperience' => new sfWidgetFormInputText(),
            'currentCompany' => new sfWidgetFormInputText(),
			'designation' => new sfWidgetFormInputText(),
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
 
        ));

        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();

        $this->setValidators(array(
            'firstName' => new sfValidatorString(array('required' => true, 'max_length' => 35)),
            'middleName' => new sfValidatorString(array('required' => false, 'max_length' => 35)),
            'lastName' => new sfValidatorString(array('required' => true, 'max_length' => 35)),
            'email' => new sfValidatorEmail(array('required' => true, 'max_length' => 100, 'trim' => true)),
            'contactNo' => new sfValidatorNumber(array('required' => true,  'max'=>9999999999)),
            'resume' => new sfValidatorFile(array('required' => false, 'max_size' => 1024000,
            'validated_file_class' => 'orangehrmValidatedFile')),
            'keyWords' => new sfValidatorString(array('required' => false, 'max_length' => 255)),
            'comment' => new sfValidatorString(array('required' => false)),
       		'currentCtc' => new sfValidatorString(array('required' => false)),
        	'expectedCtc' => new sfValidatorString(array('required' => false)),
        	'noticePeriod' => new sfValidatorNumber(array('required' => false)),
            'appliedDate' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false),
                    array('invalid' => 'Date format should be ' . $inputDatePattern)),
            'expectedDoj' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false),
                    array('invalid' => 'Date format should be ' . $inputDatePattern)),
        	'originalLocation' => new sfValidatorString(array('required' => false)),
            'visaStatus' => new sfValidatorString(array('required' => false)),
            'vacancy' => new sfValidatorString(array('required' => true)),
            'resumeUpdate' => new sfValidatorString(array('required' => false)),
            'referralName'=> new sfValidatorString(array('required' => true)),
            'referralId' => new sfValidatorNumber(array('required' => false, 'min' => 0)),
            'microResume' => new sfValidatorString(array('required' => false)),
            'preferredLocation' => new sfValidatorString(array('required' => false)),
            'educationDetailDegree' => new sfValidatorString(array('required' => false, 'max_length' => 100)),
            'educationDetailSpec' => new sfValidatorString(array('required' => false, 'max_length' => 35)),
            'educationDetailPerc' => new sfValidatorNumber(array('required' => false,'max' => 100)),
            'totalExperience' => new sfValidatorNumber(array('required' => false)),
            'relevantExperience' => new sfValidatorNumber(array('required' => false)),
            'currentCompany' => new sfValidatorString(array('required' => false)),
            'designation' => new sfValidatorString(array('required' => false)),
            'stability' => new sfValidatorString(array('required' => false, 'max_length' => 10)),
            'projectDetails' => new sfValidatorString(array('required' => false)),        
            'keySkills' => new sfValidatorString(array('required' => false)),
            'communicationSkills' => new sfValidatorString(array('required' => false)),
            'educationGap' => new sfValidatorNumber(array('required' => false)),
            'workGap' => new sfValidatorNumber(array('required' => false)),
           	'employmentType' => new sfValidatorString(array('required' => false)),
           	'alternateEmail' => new sfValidatorEmail(array('required' => false, 'max_length' => 100, 'trim' => true)),
            'alternateNumber' => new sfValidatorNumber(array('required' => false,  'max'=>9999999999)),
            
        ));

        $this->widgetSchema->setNameFormat('addCandidate[%s]');
        $this->widgetSchema['appliedDate']->setAttribute();
        $this->setDefault('appliedDate', set_datepicker_date_format(date('Y-m-d')));
        
		$referralEmp = $this->getEmployeeService()->getEmployee(sfContext::getInstance()->getUser()->getEmployeeNumber());
        $referralName = trim(trim($referralEmp['firstName']) . ' ' . $referralEmp['lastName']);
        $this->setDefault('referralId', sfContext::getInstance()->getUser()->getEmployeeNumber());
        $this->setDefault('referralName', $referralName);
        
        if ($this->candidateId != null) {
            $this->setDefaultValues($this->candidateId);
            $this->setValidator('vacancy',new sfValidatorString(array('required' => false)));
        }
    }

    private function setDefaultValues($candidateId) {

        $candidate = $this->getCandidateService()->getCandidateById($candidateId);
        $this->setDefault('firstName', $candidate->getFirstName());
        $this->setDefault('middleName', $candidate->getMiddleName());
        $this->setDefault('lastName', $candidate->getLastName());
        $this->setDefault('email', $candidate->getEmail());
        $this->setDefault('contactNo', $candidate->getContactNumber());
        $this->attachment = $candidate->getJobCandidateAttachment();
        $this->setDefault('keyWords', $candidate->getKeywords());
        $this->setDefault('comment', $candidate->getComment());  
        $this->setDefault('currentCtc', $candidate->getCurrentCtc());        
        $this->setDefault('expectedCtc', $candidate->getExpectedCtc());
        $this->setDefault('noticePeriod', $candidate->getNoticePeriod());
        $this->setDefault('appliedDate', set_datepicker_date_format($candidate->getDateOfApplication()));
        $this->setDefault('visaStatus', $candidate->getVisaStatus());
        $this->setDefault('originalLocation', $candidate->getOriginalLocation());
        $this->setDefault('microResume', nl2br($candidate->getMicroResume())); 
        $this->setDefault('preferredLocation', $candidate->getPreferredLocation());
    	$this->setDefault('educationDetailDegree',$candidate->getEducationDetailDegree());
    	$this->setDefault('educationDetailSpec',$candidate->getEducationDetailSpec());
    	$this->setDefault('educationDetailPerc',$candidate->getEducationDetailPerc());
    	$this->setDefault('totalExperience',$candidate->getTotalExperience());
    	$this->setDefault('relevantExperience',$candidate->getRelevantExperience());
    	$this->setDefault('currentCompany',$candidate->getCurrentCompany());
    	$this->setDefault('designation',$candidate->getDesignation());
    	$this->setDefault('stability',$candidate->getStability());
    	$this->setDefault('projectDetails',$candidate->getProjectDetails());
    	$this->setDefault('keySkills',$candidate->getKeySkills());
    	$this->setDefault('communicationSkills',$candidate->getCommunicationSkills());
    	$this->setDefault('educationGap',$candidate->getEducationGap());
    	$this->setDefault('workGap',$candidate->getWorkGap());
    	$this->setDefault('employmentType',$candidate->getEmploymentType());
    	$this->setDefault('alternateEmail',$candidate->getAlternateEmail());
    	$this->setDefault('alternateNumber',$candidate->getAlternateNumber());
    	        
        if($candidate->getExpectedDoj() == "0000-00-00")
        {
        	$xpectedDoj = 'yyyy-mm-dd';
        }else 
        {
        	$xpectedDoj = $candidate->getExpectedDoj();
        }
        $this->setDefault('expectedDoj', set_datepicker_date_format($xpectedDoj));
        $candidateVacancyListObj=Doctrine::getTable('JobCandidateVacancy')->createQuery()->where('candidate_id='.$candidate->getId())->execute();     
        /* Added By: Shagupta Faras 
         * DESC : To get multiple vacancies
         */        
        $vacArray=array();
        foreach ($candidateVacancyListObj as $candidateVacancyList) {
        $defaultVacancy = ($candidateVacancyList->getVacancyId() == "") ? "" : $candidateVacancyList->getVacancyId();
        $vacArray[]=$defaultVacancy;
        }        
        $this->setDefault('vacancy', $vacArray);
        $addedVacancy= array();
        foreach($vacArray as $key=>$value)
        $addedVacancy[$key]= array('vacancy'=>$value);
        $this->addedVacancy=json_encode($addedVacancy);        
        $referralID = $candidate->getAddedPerson();
        $employee = $this->getEmployeeService()->getEmployee($referralID);
        $referralName = trim(trim($employee['firstName'] ) . ' ' . $employee['lastName']);
        $this->setDefault('referralId', $referralID);
        $this->setDefault('referralName', $referralName);
        $this->emailToDisplay = $candidate->getEmail();
        $this->contactNoToDisplay = $candidate->getContactNumber();
        $this->refferedBYToDisplay = $referralName;
        $this->doaToDisplay = set_datepicker_date_format($candidate->getDateOfApplication());
    }

    public function getActiveVacancyList() {
        $list = array("" => "-- " . __('Select') . " --");
        $vacancyProperties = array('name', 'id', 'hiringManagerId');
        $activeVacancyList = $this->getVacancyService()->getVacancyPropertyList($vacancyProperties, JobVacancy::ACTIVE);
        foreach ($activeVacancyList as $vacancy) {
            $vacancyId = $vacancy['id'];
            if (in_array($vacancyId, $this->allowedVacancyList) && ($vacancy['hiringManagerId'] == $this->empNumber || $this->isAdmin || $this->isRecruitmentManager )) {
                $list[$vacancyId] = $vacancy['name'];
             }
        }
        return $list;
    }
    
    public function getActiveVacancyListForMicroResume() {
    	$list = array();
    	$vacancyProperties = array('name', 'id', 'hiringManagerId','flagForResume');
    	$activeVacancyList = $this->getVacancyService()->getVacancyPropertyList($vacancyProperties, JobVacancy::ACTIVE);
    	foreach ($activeVacancyList as $vacancy) {
    		$vacancyId = $vacancy['id'];
    		if (in_array($vacancyId, $this->allowedVacancyList) && ($vacancy['hiringManagerId'] == $this->empNumber || $this->isAdmin || $this->isRecruitmentManager )) {
    		    $list1['vacId'] = $vacancy['id'];
    			$list1['flagForResume'] = $vacancy['flagForResume'];  
    			array_push($list, $list1);
    		}
    	}
    	return $list;
    } 

    /**
     *
     * @return string
     */
    public function save($vacancy=null) {
    	
    	$logger = Logger::getLogger('AddCandidateForm.save');
        $file = $this->getValue('resume');
        $resumeUpdate = $this->getValue('resumeUpdate');
        $resume = new JobCandidateAttachment();
        $resumeId = "";
        $candidate = new JobCandidate();
        $email = trim($this->getValue('email'));
        $contactNumber = trim($this->getValue('contactNo'));    
        $candidateID=empty($this->candidateId) ? null : $this->candidateId;
        $objCheckEmail=$this->getCandidateService()->getCandidateByEmail($email,$candidateID); 
        if(!empty($objCheckEmail)){
            $resultArray = array();
            $resultArray['messageType'] = 'warning';
            $resultArray['message'] = __("Duplicate Candidate: (".$email.") email id is already exists");
            return $resultArray;
        }
        $objCheckNumber=$this->getCandidateService()->getCandidateByContactNumber($contactNumber,$candidateID); 
            if(!empty($objCheckNumber)){
            $resultArray = array();
            $resultArray['messageType'] = 'warning';
            $resultArray['message'] = __("Duplicate Candidate: (".$contactNumber.") Contact Number already exists");
            return $resultArray;
        }
       
        if(empty($vacancy))      
        $vacancy = $this->getValue('vacancy');
        $empNumber = sfContext::getInstance()->getUser()->getEmployeeNumber();
        if ($empNumber == 0) {
            $empNumber = null;
        }
        $this->addedBy = $empNumber;
      
        if (!empty($file)) {
            if (!($this->isValidResume($file))) {
                $resultArray['messageType'] = 'warning';
                $resultArray['message'] = __(TopLevelMessages::FILE_TYPE_SAVE_FAILURE);
                $logger->error($resultArray['message']);
                return $resultArray;
            }
        }
        if ($this->candidateId != null) {
            $candidate = $this->getCandidateService()->getCandidateById($this->candidateId);
            $storedResume = $candidate->getJobCandidateAttachment();
            if ($storedResume != "") {
                $resume = $storedResume;
            }
            /* Modified By : Shagupta Faras
             * Modified On : 14-07-2014
             * DESC: Save multiple vacancies
             */
            $candidateVacancyObj=Doctrine::getTable('JobCandidateVacancy')->createQuery()->where('candidate_id='.$candidate->getId())->execute();     
            // $candidateVacancyListObj = $candidate->getJobCandidateVacancy();       
            foreach($candidateVacancyObj as $candidateVacancy) {
            $id = $candidateVacancy->getVacancyId();
            if (!empty($id)) {
              if(!in_array($id,$vacancy)) {     
                foreach($vacancy as $key=>$vacancyid) { 
                 if(!$this->getCandidateService()->isExistCandidateVacancy($candidate,$vacancyid)) {
                  $this->_saveCandidateVacancies($vacancyid, $this->candidateId);
                  }
                 }//foreach
               } else {
                    foreach($vacancy as $key=>$vacancyid) {
                      if(!$this->getCandidateService()->isExistCandidateVacancy($candidate,$vacancyid)) {
                      $this->_saveCandidateVacancies($vacancyid, $this->candidateId);
                      }
                    }//foreach     
                }
            } else {
                //$this->_saveCandidateVacancies($id, $this->candidateId);
                foreach($vacancy as $key=>$vacancyid) { 
                 if(!$this->getCandidateService()->isExistCandidateVacancy($candidate,$vacancyid)) {
                  $this->_saveCandidateVacancies($vacancyid, $this->candidateId);  
                 }//if
                }//foreach
            }//else if (!empty($id))
         }//foreach 
        } //if ($this->candidateId != null)

        if ($resumeUpdate == self::CONTRACT_DELETE) {
            $resume->delete();
        }
      
        $resultArray = array();
        $candidateId = $this->_getNewlySavedCandidateId($candidate);
        if(is_array($candidateId) && isset($candidateId['messageType'])) {
            $logger->error($candidateId['messageType']);
        	return $candidateId; //send message type array
        }
        $resultArray['candidateId'] = $candidateId;
        if (!empty($file)) {
            $resumeId = $this->_saveResume($file, $resume, $candidateId);
        }
        if (!empty($this->addedHistory)) {
            $this->getCandidateService()->saveCandidateHistory($this->addedHistory);
        }
        if ($this->candidateId == "") {
        //$this->_saveCandidateVacancies($vacancy, $candidateId);
         foreach($vacancy as $key=>$vacancyid) {                          
            $this->_saveCandidateVacancies($vacancyid, $candidateId);          
        }

        }
       
        if (!empty($this->removedHistory)) {
            $this->getCandidateService()->saveCandidateHistory($this->removedHistory);
        }
        
        //Now send mail to HR admin and Hiring manager
        if (empty($this->candidateId)) {
            foreach($vacancy as $key=>$vacancyid){
	        $addCandidateMailer = new AddCandidateMailer($empNumber, $candidateId, $vacancyid);
	        $addCandidateMailer->send();
            }
        }
       
        $logger->info('Candidate saved');
        return $resultArray;
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
    }

    /**
     *
     * @param <type> $candidate
     * @return <type>
     */
    private function _getNewlySavedCandidateId($candidate) {

        $candidate->firstName = trim($this->getValue('firstName'));
        $candidate->middleName = trim($this->getValue('middleName'));
        $candidate->lastName = trim($this->getValue('lastName'));
        if(!$this->isConsultant){
        	$candidate->email = trim($this->getValue('email'));
        	$candidate->contactNumber = $this->getValue('contactNo');
        }
        $candidate->comment = $this->getValue('comment');
        $candidate->currentCtc = (float)$this->getValue('currentCtc');
        $candidate->expectedCtc = (float)$this->getValue('expectedCtc');
        $candidate->noticePeriod = (integer)$this->getValue('noticePeriod');  
        $candidate->originalLocation = $this->getValue('originalLocation');
        $candidate->expectedDoj = $this->getValue('expectedDoj');
        $candidate->visaStatus = $this->getValue('visaStatus');  
        $candidate->keywords = $this->getValue('keyWords');
        $candidate->addedPerson = $this->getValue('referralId');
        $candidate->preferredLocation = $this->getValue('preferredLocation');
        $candidate->educationDetailDegree = $this->getValue('educationDetailDegree');
        $candidate->educationDetailSpec = $this->getValue('educationDetailSpec');
        $candidate->educationDetailPerc = $this->getValue('educationDetailPerc');
        $candidate->totalExperience = $this->getValue('totalExperience');
        $candidate->relevantExperience = $this->getValue('relevantExperience');
        $candidate->currentCompany = $this->getValue('currentCompany');
        $candidate->designation = $this->getValue('designation');
        $candidate->stability = $this->getValue('stability');
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
        
        $candidate->status = JobCandidate::ACTIVE;
        $candidate->modeOfApplication = JobCandidate::MODE_OF_APPLICATION_MANUAL;
		
        $candidateService = $this->getCandidateService();
        if ($this->candidateId != null) {
        	// In case of update candidate, don't generate microresume.
        	$candidate->microResume = $this->getValue('microResume');
            $candidateService->updateCandidate($candidate);
        } else {
        	//Generate micro resume for new added candidate, if not provided
        	if($this->getOption('flag') == 'show')
        		$candidate->microResume = $this->_generateMicroResume($this->getValue('microResume'),$candidate);
        	else
        		$candidate->microResume = "";
        	
        	if($this->checkForDuplicateCandidate()) {
        		$resultArray = array();
        		$resultArray['messageType'] = 'warning';
                $resultArray['message'] = __("Duplicate Candidate");
        		return $resultArray;
        	}
        	$candidateService->saveCandidate($candidate);
            $this->addedHistory = new CandidateHistory();
            $this->addedHistory->candidateId = $candidate->getId();
            $this->addedHistory->action = WorkflowStateMachine::RECRUITMENT_CANDIDATE_ACTION_ADD;
            $this->addedHistory->performedBy = $this->addedBy;
            $date = date('Y-m-d');
            $this->addedHistory->performedDate = $date . " " . date('H:i:s');
        }
        $candidateId = $candidate->getId();
        return $candidateId;
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
	    	if((strtolower($name) == strtolower($candidate->getFirstName())." ".strtolower($candidate->getLastName())) && ((strtolower($email) == strtolower($candidate->getEmail())) || (strtolower($contact) == strtolower($candidate->getContactNumber())))) {
	    		if(strtotime($date) < strtotime($candidate->getDateOfApplication())) {
	    			$isDuplicate = true;
	    		} else {
	    			$candidate->isDeleted = JobCandidate::IS_DELETED;
	    			$cv = $candidate->getJobCandidateVacancy(); // retrieve JobCandidateVacancy Before deleteing Candidate.
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
     * @param <type> $vacnacyArray
     * @param <type> $candidateId
     */
    private function _saveCandidateVacancies($vacnacy, $candidateId) {

        if ($vacnacy != null) {
            $candidateVacancy = new JobCandidateVacancy();
            $candidateVacancy->candidateId = $candidateId;
            $candidateVacancy->vacancyId = $vacnacy;
            $candidateVacancy->status = "SCREENING";
            if ($this->getValue('appliedDate') == "") {
                $candidateVacancy->appliedDate = date('Y-m-d');
            } else {
                $candidateVacancy->appliedDate = $this->getValue('appliedDate');
            }
            $candidateService = $this->getCandidateService();
            $candidateService->saveCandidateVacancy($candidateVacancy);
            $history = new CandidateHistory();
            $history->candidateId = $candidateId;
            $history->action = WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_ATTACH_VACANCY;
            $history->vacancyId = $candidateVacancy->getVacancyId();
            $history->performedBy = $this->addedBy;
            $date = date('Y-m-d');
            $history->performedDate = $date . " " . date('H:i:s');
            $history->candidateVacancyName = $candidateVacancy->getVacancyName();
            $this->getCandidateService()->saveCandidateHistory($history);
        }
    }

    /**
     *
     * @return JobCandidateAttachment
     */
    public function getResume() {
        return $this->attachment;
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
    
    public function getEmployeeListAsJson() {
        $jsonArray = array();
       
        $properties = array("empNumber","firstName", "middleName", "lastName", "termination_id");
        $employeeList = $this->getEmployeeService()->getEmployeePropertyList($properties, 'lastName', 'ASC', true);

        foreach ($employeeList as $employee) {
            $empNumber = $employee['empNumber'];
            //$name = trim(trim($employee['firstName'] . ' ' . $employee['middleName'],' ') . ' ' . $employee['lastName']);
            $name = trim(trim($employee['firstName']) . ' ' . trim($employee['lastName']));
        
            $jsonArray[] = array('name' => $name, 'id' => $empNumber);
        }
        $jsonString = json_encode($jsonArray);
        return $jsonString;
    }
    
     public function getAllCandidateList(){
    	$candidateArray = array();
    	$candidateList = $this->getCandidateService()->getAllCandidateList();
    	foreach($candidateList as $candidate){
    			$candidateArray[] = array('candidateId' => $candidate['id'], 'contactNumber' => $candidate['contactNumber'], 'email' => $candidate['email'],'candidateName' => trim(trim($candidate["firstName"]." ".trim($candidate["lastName"]))));
    	}
    	return $candidateArray;
    }
}

