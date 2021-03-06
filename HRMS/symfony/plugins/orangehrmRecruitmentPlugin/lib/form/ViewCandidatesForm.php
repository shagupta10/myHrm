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

/**
 * Form class for search candidates
 */
class viewCandidatesForm extends BaseForm {

    private $candidateService;
    private $vacancyService;
    private $allowedCandidateList;
    private $allowedVacancyList;
    public $allowedCandidateListToDelete;
    private $jobTitleService;

    public function getJobTitleService() {
        if (is_null($this->jobTitleService)) {
            $this->jobTitleService = new JobTitleService();
            $this->jobTitleService->setJobTitleDao(new JobTitleDao());
        }
        return $this->jobTitleService;
    }

    /**
     * Get CandidateService
     * @returns CandidateService
     */
    public function getCandidateService() {
        if (is_null($this->candidateService)) {
            $this->candidateService = new CandidateService();
            $this->candidateService->setCandidateDao(new CandidateDao());
        }
        return $this->candidateService;
    }

    /**
     * Set CandidateService
     * @param CandidateService $candidateService
     */
    public function setCandidateService(CandidateService $candidateService) {
        $this->candidateService = $candidateService;
    }

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
     * Set VacancyService
     * @param VacancyService $vacancyService
     */
    public function setVacancyService(VacancyService $vacancyService) {
        $this->vacancyService = $vacancyService;
    }

    /**
     *
     */
    public function configure() {

        $this->allowedCandidateList = $this->getOption('allowedCandidateList');
        $this->allowedVacancyList = $this->getOption('allowedVacancyList');
        $this->allowedCandidateListToDelete = $this->getOption('allowedCandidateListToDelete');
        $jobVacancyList = $this->getVacancyList();
        $modeOfApplication = array('' => __('All'), JobCandidate::MODE_OF_APPLICATION_MANUAL => __('Manual'), JobCandidate::MODE_OF_APPLICATION_ONLINE => __('Online'));
        $hiringManagerList = $this->getHiringManagersList();
        $jobTitleList = $this->getJobTitleList();
        $statusList = $this->getStatusList();
        $vacancyStatusList = $this->getVacancyStatusList();
        //creating widgets
        $this->setWidgets(array(
            'jobTitle' => new sfWidgetFormSelect(array('choices' => $jobTitleList)),
        	'vacancyStatus' => new sfWidgetFormSelect(array('choices' => $vacancyStatusList)),
            'jobVacancy' => new sfWidgetFormSelect(array('choices' => $jobVacancyList)),
            'hiringManager' => new sfWidgetFormSelect(array('choices' => $hiringManagerList)),
            'candidateName' => new sfWidgetFormInputText(),
            'referralName'=> new sfWidgetFormInputText(),
            'referralId'=> new sfWidgetFormInputHidden(),
            'selectedCandidate' => new sfWidgetFormInputHidden(),
            'keywords' => new sfWidgetFormInputText(),
            'dateApplication' => new ohrmWidgetFormDateRange(array(  
                    'from_date' => new ohrmWidgetDatePicker(array(), array('id' => 'candidateSearch_fromDate')),  
                    'to_date' => new ohrmWidgetDatePicker(array(), array('id' => 'candidateSearch_toDate')),
                    'from_label' => 'From',
                    'to_label' => 'To'
                )),
            'modeOfApplication' => new sfWidgetFormSelect(array('choices' => $modeOfApplication)),
            'status' => new sfWidgetFormSelect(array('multiple' => true, 'choices' => $statusList), array('style' => 'height:55px;')),
//            'fromDate' => new ohrmWidgetDatePicker(array(), array('id' => 'candidateSearch_fromDate')),
//            'toDate' => new ohrmWidgetDatePicker(array(), array('id' => 'candidateSearch_toDate'))
        ));

        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();

        //Setting validators
        $this->setValidators(array(
            'jobTitle' => new sfValidatorString(array('required' => false)),
        	'vacancyStatus' => new sfValidatorString(array('required' => false)),
            'jobVacancy' => new sfValidatorString(array('required' => false)),
            'hiringManager' => new sfValidatorString(array('required' => false)),
            'status' => new sfValidatorChoice(array('choices' => array_keys($statusList), 'required' => false, 'multiple' => true)),
            'candidateName' => new sfValidatorString(array('required' => false)),
            'referralName'=> new sfValidatorString(array('required' => false)),
            'referralId' => new sfValidatorNumber(array('required' => false, 'min' => 0)),
            'selectedCandidate' => new sfValidatorNumber(array('required' => false, 'min' => 0)),
            'keywords' => new sfValidatorString(array('required' => false)),
            'modeOfApplication' => new sfValidatorString(array('required' => false)),
            'dateApplication' => new sfValidatorDateRange(array(  
                'from_date' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false)),  
                'to_date' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false)),
                'required' => false
            ), array('invalid' => 'To date should be after from date')),
//            'fromDate' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false),
//                    array('invalid' => 'Date format should be ' . $inputDatePattern)),
//            'toDate' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false),
//                    array('invalid' => 'Date format should be ' . $inputDatePattern)),
        ));
        
        $usrObj = sfContext::getInstance()->getUser()->getAttribute('user');
	    if (!($usrObj->isAdmin() ||$usrObj->isRecruitmentManager())) {
		    $this->setDefault('status', "progress");
		    $this->setDefault('vacancyStatus', JobVacancy::ACTIVE);
	    }
        
        $this->widgetSchema->setNameFormat('candidateSearch[%s]');
        $this->getWidgetSchema()->setLabels($this->getFormLabels());

    }

    /**
     *
     * @param CandidateSearchParameters $searchParam
     * @return CandidateSearchParameters
     */
    public function getSearchParamsBindwithFormData(CandidateSearchParameters $searchParam) {
        
        $searchParam->setJobTitleCode($this->getValue('jobTitle'));
        $searchParam->setVacancyStatus($this->getValue('vacancyStatus'));
        $searchParam->setVacancyId($this->getValue('jobVacancy'));
        $searchParam->setHiringManagerId($this->getValue('hiringManager'));
        $searchParam->setStatus($this->getValue('status'));
        $searchParam->setCandidateId($this->getValue('selectedCandidate'));
        $searchParam->setModeOfApplication($this->getValue('modeOfApplication'));
        $dateApplication = $this->getValue('dateApplication');
        $searchParam->setFromDate($dateApplication['from']);
        $searchParam->setToDate($dateApplication['to']);
        $searchParam->setKeywords($this->getValue('keywords'));
        $searchParam->setCandidateName($this->getValue('candidateName'));
        $searchParam->setReferralId($this->getValue('referralId'));
        $searchParam->setReferralName($this->getValue('referralName'));
        return $searchParam;
    }

    /**
     *
     * @param CandidateSearchParameters $searchParam
     */
    public function setDefaultDataToWidgets(CandidateSearchParameters $searchParam) {

        $newSearchParam = new CandidateSearchParameters();

        $this->setDefault('jobTitle', $searchParam->getJobTitleCode());
        $this->setDefault('vacancyStatus', $searchParam->getVacancyStatus());
        $this->setDefault('jobVacancy', $searchParam->getVacancyId());
        $this->setDefault('hiringManager', $searchParam->getHiringManagerId());
        $this->setDefault('status', $searchParam->getStatus());
        $this->setDefault('selectedCandidate', $searchParam->getCandidateId());
        $this->setDefault('modeOfApplication', $searchParam->getModeOfApplication());

        $displayFromDate = ($searchParam->getFromDate() == $newSearchParam->getFromDate()) ? "" : $searchParam->getFromDate();
        $displayToDate = ($searchParam->getToDate() == $newSearchParam->getToDate()) ? "" : $searchParam->getToDate();

        $this->setDefault('from_date', ($displayFromDate));
        $this->setDefault('to_date', ($displayToDate));
        $this->setDefault('keywords', $searchParam->getKeywords());
        $this->setDefault('candidateName', $searchParam->getCandidateName());
        $this->setDefault('referralId', $searchParam->getEmpNumber());
        $this->setDefault('referralName',$searchParam->getReferralName());
    }
    
    public function isEssUser(){
    	//sfContext::getInstance()->getUser()->getEmployeeNumber();
	    $usrObj = sfContext::getInstance()->getUser()->getAttribute('user');
	    if (!($usrObj->isAdmin() || $usrObj->isHiringManager() || $usrObj->isInterviewer() || $usrObj->isRecruitmentManager())) {
		    return false;
	    }
	    return true;
    }

    /**
     * Returns job Title List
     * @return array
     */
    private function getJobTitleList() {
        $jobTitleList = $this->getJobTitleService()->getJobTitleList();
        $list = array("" => __('All'));
        foreach ($jobTitleList as $jobTitle) {
            $list[$jobTitle->getId()] = $jobTitle->getJobTitleName();
        }
        return $list;
    }

    /**
     * Make status List
     * @return array
     */
    private function getStatusList() {
        $list = array("" => __('All'),"progress" => __('In Progess'));
        $userObj = sfContext::getInstance()->getUser()->getAttribute('user');
        $allowedStates = $userObj->getAllAlowedRecruitmentApplicationStates(PluginWorkflowStateMachine::FLOW_RECRUITMENT);
        $uniqueStatesList = array_unique($allowedStates);
        foreach ($uniqueStatesList as $key => &$value) {
            if ($value == "INITIAL") {
                unset($uniqueStatesList[$key]);
            } else {
                $list[$value] = ucwords(strtolower($value));
            }
        }
        return $list;
    }

    /**
     * Returns HiringManager List
     * @return array
     */
    private function getHiringManagersList() {
        $list = array("" => __('All'));
        $hiringManagersList = $this->getVacancyService()->getHiringManagersList("", "", $this->allowedVacancyList);
        foreach ($hiringManagersList as $hiringManager) {
            $list[$hiringManager['id']] = $hiringManager['name'];
        }

        return $list;
    }

    /**
     * Returns Vacancy List
     * @return array
     */
    public function getVacancyList() {
        $list = array("" => __('All'));
        $vacancyProperties = array('name', 'id');
        $vacancyList = $this->getVacancyService()->getVacancyPropertyList($vacancyProperties, null);
        
        foreach ($vacancyList as $vacancy) {
            $list[$vacancy['id']] = $vacancy['name'];
        }
        return $list;
    }
    
    
    public function getVacancyListForDialogBox() {
    	$vacancyProperties = array('name', 'id');
    	$vacancyList = $this->getVacancyService()->getVacancyPropertyList($vacancyProperties, JobVacancy::ACTIVE);
    	foreach ($vacancyList as $vacancy) {
    		$list[$vacancy['id']] = $vacancy['name'];
    	}
    	return $list;
    }
    
    public function getVacancyDescription() {
    	$vacancyProperties = array('description', 'id');
    	$vacancyList = $this->getVacancyService()->getVacancyPropertyList($vacancyProperties, JobVacancy::ACTIVE);
    	foreach ($vacancyList as $vacancy) {
    		$list[$vacancy['id']] = $vacancy['description'];
    	}
    	return $list;
    }

    /**
     * Returns Action List
     * @return array
     */
    private function getActionList() {

        $list = array("" => __('All'));
        $userObj = sfContext::getInstance()->getUser()->getAttribute('user');
        $allowedActions = $userObj->getAllowedActions(PluginWorkflowStateMachine::FLOW_RECRUITMENT, "");

        foreach ($allowedActions as $action) {
            if ($action != 0) {
                $list[$action] = $this->getActionName($action);
            }
        }
        return $list;
    }

    /**
     * Returns Candidate json list
     * @return JsonCandidate List
     */
    public function getCandidateListAsJson() {

        $jsonArray = array();
        $candidateList = $this->getCandidateService()->getCandidateList($this->allowedCandidateList);
        foreach ($candidateList as $candidate) {

            $name = trim(trim($candidate['firstName'] . ' ' . $candidate['middleName']) . ' ' . $candidate['lastName']);

            $jsonArray[] = array('name' => $name, 'id' => $candidate['id']);
        }
        $jsonString = json_encode($jsonArray);
        return $jsonString;
    }
    
    /**
     * Returns the employee json list
     */
    public function getEmpListInCandidate() {
    	$jsonArray = array();
    	$empList =  $this->getCandidateService()->getEmpListInCandidate();
    	//print_r("Sunil :"+ $empList );
    	foreach($empList as $employee){
    		 $name = trim(trim($employee['emp_firstname'] . ' ' . $employee['emp_lastname']));
    		 $jsonArray[] = array('name' => $name, 'id' => $employee['emp_number']);
    	}
    	$jsonString = json_encode($jsonArray);
        return $jsonString;
    }
    
    protected function getFormLabels() {

        $labels = array(
            'jobTitle' =>__('Job Title'),
        	'vacancyStatus' => __('Vacancy Status'),
            'jobVacancy' => __('Vacancy'),
            'hiringManager' => __('Hiring Manager'),
            'status' => __('Status'),
            'candidateName' => __('Candidate Name'),
            'referralName' => __('Referral Name'),
            'keywords' => __('Keywords'),
            'modeOfApplication' => __('Method of Application'),
            'dateApplication' =>__('Date of Application'),
            
        );
//        'from_date' => __('Date of Application'),  
//            'to_date' => __('')
        return $labels;
    }
/**
     * Make vacancy status List
     * @return array
     */
    private function getVacancyStatusList() {
        $list = array("" => __('All'), JobVacancy::ACTIVE => __('Active'), JobVacancy::CLOSED => __("Closed"));
        return $list;
    }
}

