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
class viewMyCandidatesForm extends BaseForm {

    private $candidateService;
    private $vacancyService;
    private $allowedCandidateList;
    private $allowedVacancyList;
    public $allowedCandidateListToDelete;
    private $jobTitleService;
    public $mappingString;
    
    private static $recruitmentStatusTxt = array(
    	"" => 'All',
    	'SCREENING' => 'Screening' ,
	    'APPLICATION INITIATED' => 'Application Initiated' ,
		'SHORTLISTED' =>'Shortlisted',
		'REJECTED' => 'Rejected',
		'INTERVIEW SCHEDULED' => 'Interview Scheduled',
		'INTERVIEW PASSED'=> 'Interview Passed',
		'INTERVIEW FAILED'=> 'Interview Failed',
		'JOB OFFERED'=> 'Job Offered',
		'OFFER DECLINED'=> 'Offer Declined',
		'HIRED'=> 'Hired',
		'HOLD' => 'Hold',
    );

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
        $jobVacancyList = $this->getVacancyPropertyListReferred();
        $statusList = $this->getStatusList();
        $this->setWidgets(array(
            'jobVacancy' => new sfWidgetFormSelect(array('choices' => $jobVacancyList)),
        	'recordsPer_Page_Limit' => new sfWidgetFormInputHidden(array(), array()),
            'status' => new sfWidgetFormSelect(array('choices' => self::$recruitmentStatusTxt)),
            'candidateName' => new sfWidgetFormInputText(),
            'referralId'=> new sfWidgetFormInputHidden(),
            'dateApplication' => new ohrmWidgetFormDateRange(array(  
                    'from_date' => new ohrmWidgetDatePicker(array(), array('id' => 'candidateSearch_fromDate')),  
                    'to_date' => new ohrmWidgetDatePicker(array(), array('id' => 'candidateSearch_toDate')),
                    'from_label' => 'From',
                    'to_label' => 'To'
                )),
        ));
        
        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
        
        $this->setValidators(array(
            'jobVacancy' => new sfValidatorString(array('required' => false)),
        	'recordsPer_Page_Limit' => new sfValidatorString(array('required' => false)),
            'status' => new sfValidatorString(array('required' => false)),
            'candidateName' => new sfValidatorString(array('required' => false)),
            'referralId' => new sfValidatorNumber(array('required' => false, 'min' => 0)),
            'dateApplication' => new sfValidatorDateRange(array(  
                'from_date' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false)),  
                'to_date' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false)),
                'required' => false
            ), array('invalid' => 'To date should be after from date')),
        ));
        
        $this->widgetSchema->setNameFormat('candidateSearch[%s]');
        $this->getWidgetSchema()->setLabels($this->getFormLabels());
    }

    /**
     *
     * @param CandidateSearchParameters $searchParam
     * @return CandidateSearchParameters
     */
    public function getSearchParamsBindwithFormData(CandidateSearchParameters $searchParam) {
        $searchParam->setVacancyId($this->getValue('jobVacancy'));
        $searchParam->setStatus($this->getValue('status'));
        $searchParam->setCandidateId($this->getValue('selectedCandidate'));
        $searchParam->setCandidateName($this->getValue('candidateName'));
        $searchParam->setEmpNumber(sfContext::getInstance()->getUser()->getEmployeeNumber()); $dateApplication = $this->getValue('dateApplication');
        $searchParam->setFromDate($dateApplication['from']);
        $searchParam->setToDate($dateApplication['to']);
        return $searchParam;
    }

    /**
     *
     * @param CandidateSearchParameters $searchParam
     */
    public function setDefaultDataToWidgets(CandidateSearchParameters $searchParam) {
        $newSearchParam = new CandidateSearchParameters();
        $this->setDefault('jobVacancy', $searchParam->getVacancyId());
        $this->setDefault('status', $searchParam->getStatus());
        $this->setDefault('selectedCandidate', $searchParam->getCandidateId());
        $this->setDefault('candidateName', $searchParam->getCandidateName());
        $this->setDefault('referralId', $searchParam->getEmpNumber());
        $displayFromDate = ($searchParam->getFromDate() == $newSearchParam->getFromDate()) ? "" : $searchParam->getFromDate();
        $displayToDate = ($searchParam->getToDate() == $newSearchParam->getToDate()) ? "" : $searchParam->getToDate();

        $this->setDefault('from_date', ($displayFromDate));
        $this->setDefault('to_date', ($displayToDate));
    }
    

    /**
     * Make status List
     * @return array
     */
    private function getStatusList() {
        $list = array("" => __('All'));
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
     * Returns Vacancy List
     * @return array
     */
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
     * Returns Candidate json list
     * @return JsonCandidate List
     */
    public function getCandidateListAsJson() {
        $jsonArray = array();
        $candidateList = $this->getCandidateService()->getReferredCandidate(sfContext::getInstance()->getUser()->getEmployeeNumber());
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
    	foreach($empList as $employee){
    		 $name = trim(trim($employee['emp_firstname'] . ' ' . $employee['emp_lastname']));
    		 $jsonArray[] = array('name' => $name, 'id' => $employee['emp_number']);
    	}
    	$jsonString = json_encode($jsonArray);
        return $jsonString;
    }
    
    protected function getFormLabels() {

        $labels = array(
            'jobVacancy' => __('Vacancy'),
            'status' => __('Status'),
            'candidateName' => __('Candidate Name'),
            'dateApplication' =>__('Date of Application'),
        );
        return $labels;
    }
    
    public function getVacancyPropertyListReferred()
    {
    	$list = array("" => __('All'));
    	$vacancyList = $this->getVacancyService()->getVacancyPropertyListReferred(sfContext::getInstance()->getUser()->getEmployeeNumber());
    	foreach ($vacancyList as $vacancy) {
    		$list[$vacancy['id']] = $vacancy['name'];
    	}
    	return $list;
    }
  
    public function jsonEncodeStatusMapping($statusMapping)
    {
    	$jsonArray = array();
    	foreach($statusMapping as $mapping){
    		$jsonArray[] = array('statusName' => $mapping['status'], 'id' => $mapping['id'], 'vacancyId' => $mapping['vacancyId']);
    	}
    	$jsonString = json_encode($jsonArray);
    	//var_dump($jsonString);exit;
    	$this->mappingString =  $jsonString;
    }
    
    public function getStatusMapping()
    {
    	return $this->mappingString;
    }
}

