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
*/

/**
 * Form class for Save Education
*/
class addFeedbackForm extends BaseForm {

	private $customerService;
	private $employeeService;
	private $performanceReviewService;
	
	public function getPerformanceReviewService() {
		if(is_null($this->performanceReviewService)) {
			$this->performanceReviewService = new PerformanceReviewService();
			$this->performanceReviewService->setPerformanceReviewDao(new PerformanceReviewDao());
		}
		return $this->performanceReviewService;		
	}
	
	public function getEmployeeService() {
		if (is_null($this->employeeService)) {
			$this->employeeService = new EmployeeService();
			$this->employeeService->setEmployeeDao(new EmployeeDao());
		}
		return $this->employeeService;
	}
	
	public function getCustomerService() {
		if(is_null($this->customerService)) {
			$this->customerService = new CustomerService();
			$this->customerService->setCustomerDao(new CustomerDao());
		}
		return $this->customerService;
	}

	public function configure() {
		$this->fd = $this->getOption('feedbackid');
		$this->setWidgets(array(
				'empName'=> new sfWidgetFormInputText(),
				'pros'=> new sfWidgetFormTextarea(),
				'cons'=> new sfWidgetFormTextarea(),
				'empNumber' => new sfWidgetFormInputHidden(),
				'flag' => new sfWidgetFormInputCheckbox(array(), array('value' => 'on')),
				));
		
		$inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
		
		$this->setValidators(array(
				'empName'=> new sfValidatorString(array('required' => false)),
				'pros'=> new sfValidatorString(array('required' => false)),
				'cons'=>new sfValidatorString(array('required' => false)),
				'empNumber' => new sfValidatorNumber(array('required' => true)),
				'flag' => new sfValidatorString(array('required' => false)),
				));
		
		if($this->fd != null){
			$this->setDefaultValues($this->fd);
		} else {
			$this->setDefault('flag', null);
		}
		$this->getEmployeesAsJson();
		$this->widgetSchema->setNameFormat('addFeedback[%s]');
	}
	
	public function setDefaultValues($id) {
		$feedbackDetails = $this->getPerformanceReviewService()->getFeedbackById($id);
		$this->setDefault('empNumber', $feedbackDetails->getEmpNumber());
		$this->setDefault('empName', $feedbackDetails->getEmployee()->getFirstAndLastNames());
		$this->setDefault('pros', $feedbackDetails->getPositiveFeedback());
		$this->setDefault('cons', $feedbackDetails->getNegativeFeedback());
		if ($feedbackDetails->getIsAnonymous() == EmployeeMultiSourceFeedback::IS_ANONYMOUS) {
			$this->setDefault('flag', $feedbackDetails->getIsAnonymous());
		}
	}
	
	
	public function getEmployeesAsJson() {
		$jsonArray = array();
		$empNumber = sfContext::getInstance()->getUser()->getEmployeeNumber();
		if(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 'Yes') {
			$employees = $this->getEmployeeService()->getEmployeeList();
			foreach ($employees as $emp) {
				if($emp->getEmpNumber() != $empNumber) {
					$name = $emp->getFirstAndLastNames();
					$jsonArray[] = array('name' => $name, 'num' => $emp->getEmpNumber());
				}
			}
    	} else/*  if($_SESSION['isSupervisor']) {
    		$accessibleEmployees = UserRoleManagerFactory::getUserRoleManager()->getAccessibleEntityIds('Employee');
    		foreach ($accessibleEmployees as $number) {
    			$employee = $this->getEmployeeService()->getEmployee($number);
    			$name = $employee->getFirstAndLastNames();
    			$jsonArray[] = array('name' => $name, 'num' => $employee->getEmpNumber());
    		}
    	} else  */{
    		$customers = $this->getEmployeeService()->getEmployeeCustomer($empNumber);
    		$customerIds = array();
    		foreach ($customers as $customer) {
    			$idCustomer = $customer->getCustomerId();
    			array_push($customerIds, $idCustomer);
    		}
			$q = Doctrine_Query::create()
                            ->from('Customer')
							->where('supportFunction = 1');
			$q->orderBy('name ASC');
			$customers = $q->execute();
    		foreach ($customers as $customer) {
    			$idCustomer = $customer->getCustomerId();
    			array_push($customerIds, $idCustomer);
    		}
            /* Get All team members of projects has been worked in current performance cycle period.  */
            $CurrentCycleObj=$this->getPerformanceReviewService()->getEmployeeCurrentCyclePerformance($empNumber);
            $empIdName = $this->getEmployeeService()->getCurrentProjectTeamMember($empNumber,$CurrentCycleObj);
            foreach ($empIdName as $key=>$value){
                $jsonArray[] = array('name' => $value, 'num' => $key);
            }
    	}
	    $jsonString = json_encode($jsonArray);
		return $jsonString;
	}
	
	public function save($isDraft = false, $fid = null, $isSaveDraft = false) {
		$user = sfContext::getInstance()->getUser()->getEmployeeNumber();
		$currentPerformancePeriod = $this->getPerformanceReviewService()->getCurrentPerformancePeriod();
		$feedback = new EmployeeMultiSourceFeedback();
		$feedback->empNumber = $this->getValue('empNumber');
		$feedback->reviewersNumber = $user;
		$feedback->positiveFeedback = $this->getValue('pros');
		$feedback->negativeFeedback = $this->getValue('cons');
		$feedback->fromDate = $currentPerformancePeriod->getPeriodFrom();
		$feedback->toDate = $currentPerformancePeriod->getPeriodTo();
		$feedback->createdDate = date('Y-m-d');
		$feedback->createdBy = $user;
		if($this->getValue('flag')=='on') {
			$feedback->isAnonymous = EmployeeMultiSourceFeedback::IS_ANONYMOUS;
		} else {
			$feedback->isAnonymous = EmployeeMultiSourceFeedback::IS_NOT_ANONYMOUS;
		}
		if($isDraft) {
			$feedback->isSubmitted = EmployeeMultiSourceFeedback::IS_NOT_SUBMITTED;   
			if($isSaveDraft) {
				$feedback->isSubmitted = EmployeeMultiSourceFeedback::IS_SUBMITTED;  
			}
			if(empty($fid)) {
				$id =  $this->getPerformanceReviewService()->saveFeedback($feedback);
			} else {
				$id = $this->getPerformanceReviewService()->updateFeedback($feedback, $fid);
			}
		} else {
			$feedback->isSubmitted = EmployeeMultiSourceFeedback::IS_SUBMITTED;
			$id = $this->getPerformanceReviewService()->saveFeedback($feedback);
		}
		return $id; // feedbackID
	}
	
	public function discard($fid) {
		$this->getPerformanceReviewService()->discardFeedback($fid);
	}
	
	public function getCurrentCycle(){
		return $this->getPerformanceReviewService()->getCurrentPerformancePeriod();
	}
}