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
class viewJobRequestsForm extends BaseForm {
	
	private $candidateService;
	private $vacancyService;
	

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
		
		//creating widgets
		$this->setWidgets(array(
			'candidateName' => new sfWidgetFormInputText(),
			'candidateId' => new sfWidgetFormInputHidden(),
			'requesterName'=> new sfWidgetFormInputText(),
			'requesterId'=> new sfWidgetFormInputHidden(),
			'actionName'=> new sfWidgetFormInputHidden(),
			'status' => new sfWidgetFormSelect(array('choices' => array(""=>'All') + JobCandidateRequests::$requestStatus)),
			'dateApplication' => new ohrmWidgetFormDateRange(array(  
				'from_date' => new ohrmWidgetDatePicker(array(), array('id' => 'jobRequests_fromDate')),  
				'to_date' => new ohrmWidgetDatePicker(array(), array('id' => 'jobRequests_toDate')),
				'from_label' => 'From',
				'to_label' => 'To'
			)),
		));
		
		$inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
		
		//Setting validators
		$this->setValidators(array(
			'candidateName' => new sfValidatorString(array('required' => false)),
			'requesterName'=> new sfValidatorString(array('required' => false)),
			'candidateId' => new sfValidatorNumber(array('required' => false, 'min' => 0)),
			'requesterId' => new sfValidatorNumber(array('required' => false, 'min' => 0)),
			'actionName'=> new sfValidatorString(array('required' => false)),
			'status' => new sfValidatorString(array('required' => false)),
			'dateApplication' => new sfValidatorDateRange(array(  
				'from_date' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false)),  
				'to_date' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false)),
				'required' => false
			), array('invalid' => 'To date should be after from date')),
		));
		
		//$this->setDefault('status', JobCandidateRequests::REQUEST_STATUS_PENDING);
		
		$this->widgetSchema->setNameFormat('jobRequests[%s]');
		$this->getWidgetSchema()->setLabels($this->getFormLabels());
	}
	
	/**
	 *
	 * @param CandidateSearchParameters $searchParam
	 * @return CandidateSearchParameters
	 */
	public function getSearchParamsBindwithFormData(CandidateSearchParameters $searchParam) {
		$searchParam->setStatus($this->getValue('status'));
		$searchParam->setCandidateId($this->getValue('candidateId'));
		//$searchParam->setCandidateId($this->getValue('selectedCandidate'));
		$dateApplication = $this->getValue('dateApplication');
		$searchParam->setFromDate($dateApplication['from']);
		$searchParam->setToDate($dateApplication['to']);
		$searchParam->setCandidateName($this->getValue('candidateName'));
		$searchParam->setReferralName($this->getValue('requesterName'));
		$searchParam->setReferralId($this->getValue('requesterId'));
		//var_dump($searchParam);exit;
		return $searchParam;
	}
	
	/**
	 *
	 * @param CandidateSearchParameters $searchParam
	 */
	public function setDefaultDataToWidgets(CandidateSearchParameters $searchParam) {
		
		$newSearchParam = new CandidateSearchParameters();
		
		$this->setDefault('status', $searchParam->getStatus());
		
		$displayFromDate = ($searchParam->getFromDate() == $newSearchParam->getFromDate()) ? "" : $searchParam->getFromDate();
		$displayToDate = ($searchParam->getToDate() == $newSearchParam->getToDate()) ? "" : $searchParam->getToDate();
		
		$this->setDefault('from_date', ($displayFromDate));
		$this->setDefault('to_date', ($displayToDate));
		$this->setDefault('requesterId', $searchParam->getEmpNumber());
		$this->setDefault('requesterName',$searchParam->getReferralName());
	}
	

	
	/**
	 * Returns Candidate json list
	 * @return JsonCandidate List
	 */
	public function getCandidateListAsJson() {
		
		$jsonArray = array();
		$allowedCandidateList = $this->getCandidateService()->getCandidateListForRequest();
		$candidateList = $this->getCandidateService()->getCandidateList($allowedCandidateList);
		foreach ($candidateList as $candidate) {
			$name = trim(trim($candidate['firstName']) .' ' . trim($candidate['lastName']));
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
			'status' => __('Request Status'),
			'candidateName' => __('Candidate Name'),
			'requesterName' => __('Requester Name'),
			'dateApplication' =>__('Created Date'),
		);
		return $labels;
	}
	
}

