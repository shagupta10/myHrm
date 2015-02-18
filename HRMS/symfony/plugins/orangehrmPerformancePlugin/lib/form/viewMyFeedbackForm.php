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
 * 
 */
class viewMyFeedbackForm extends BaseForm {
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

    /**
     *
     */
    public function configure() {
        //creating widgets
        $this->setWidgets(array(
            'empName' => new sfWidgetFormInputText(),
            'empNumber'=> new sfWidgetFormInputHidden(),
            'performanceCycle' => new ohrmWidgetPerformancePeriod()
        ));

        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
		
        //Setting validators
		$this->setValidators(array(
			'empName' => new sfValidatorString(array('required' => false)),
			'empNumber' => new sfValidatorString(array('required' => false)),
			'performanceCycle' => new sfValidatorDateRange(array(
				'from_date' => new ohrmDateValidator(array('required' => false)),
				'to_date' => new ohrmDateValidator(array('required' => false))
			))
        ));
        $this->getReviewedEmployeesAsJson();
        $this->getMyFeedbacks();
        $this->widgetSchema->setNameFormat('viewMyFeedback[%s]');
        $this->getWidgetSchema()->setLabels($this->getFormLabels());
    }

    protected function getFormLabels() {
        $labels = array(
            'empName' =>__('Employee Name'),
            'performanceCycle' => ('Performance Cycle')
        );
        return $labels;
    }
    public function getMyFeedbacks(){
        $usrNumber = sfContext::getInstance()->getUser()->getEmployeeNumber();
        $feedbackList = $this->getPerformanceReviewService()->getAllFeedback();
        foreach ($feedbackList as $feedback) {
            if($feedback->getReviewersNumber() == $usrNumber || (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 'Yes') ){
                $data[] =  $feedback->getId();
            }
        }
        return json_encode($data);
    }
    public function getReviewedEmployeesAsJson(){
    	$usrNumber = sfContext::getInstance()->getUser()->getEmployeeNumber();
    	if(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 'Yes') {
    		$feedbackList = $this->getPerformanceReviewService()->getAllFeedback();
    		foreach ($feedbackList as $feedback) {
    			$jsonArray[$feedback->getEmpNumber()] = $feedback->getEmployee()->getFirstAndLastNames();
    		}
    		foreach ($jsonArray as $number => $name) {
    			$array[] = array('name' => $name, 'id'=>$number);
    		}
    	} else if($_SESSION['isSupervisor']) {
    		$accessibleEmployees = UserRoleManagerFactory::getUserRoleManager()->getAccessibleEntityIds('Employee');
    		array_push($accessibleEmployees, strval($usrNumber));
    		$feedbackList = $this->getPerformanceReviewService()->getAllFeedback();
    		foreach ($feedbackList as $feedback) {
    			if(in_array($feedback->getEmpNumber(), $accessibleEmployees)  || $usrNumber == $feedback->getReviewersNumber() ) {
    				$jsonArray[$feedback->getEmpNumber()] = $feedback->getEmployee()->getFirstAndLastNames();
    			}
    		}
    		foreach ($jsonArray as $number => $name) {
    				$array[] = array('name' => $name, 'id'=>$number);
    		}	
    	} else {
	    	$feedbackList = $this->getPerformanceReviewService()->getReviewedEmployees($usrNumber); // list of reviews 
	    	foreach ($feedbackList as $feedback) {
	    		$jsonArray[$feedback->getEmpNumber()] = $feedback->getEmployee()->getFirstAndLastNames();
	    	}   	
	    	foreach ($jsonArray as $number => $name) {
	    		$array[] = array('name' => $name, 'id'=>$number);
	    	}
    	}
    	$jsonString = json_encode($array);
    	return $jsonString;
    }
    
    public function setDefaultDataToWidgets(MyFeedbackSearchParameters $searchParam) {
    	$newSearchParam = new MyFeedbackSearchParameters();
    	$this->setDefault('empName', $searchParam->getEmpName());
    	$this->setDefault('empNumber', $searchParam->getEmpNumber());
    	$displayFromDate = ($searchParam->getFromDate() == $newSearchParam->getFromDate()) ? "" : $searchParam->getFromDate();
    	$displayToDate = ($searchParam->getToDate() == $newSearchParam->getToDate()) ? "" : $searchParam->getToDate();
    	$this->setDefault('from_date', ($displayFromDate));
    	$this->setDefault('to_date', ($displayToDate));
    }
    
    public function getSearchParamsBindwithFormData(MyFeedbackSearchParameters $searchParam) {
    	if(trim($this->getValue('empName'))!="") {
    		$searchParam->setEmpNumber($this->getValue('empNumber'));
    	}
    	
    	$performanceCycle = $this->getValue('performanceCycle');
    	if(!is_null($performanceCycle['from'])) {
    		$searchParam->setFromDate($performanceCycle['from']);
    	}
    	if(!is_null($performanceCycle['to'])) {
    		$searchParam->setToDate($performanceCycle['to']);
    	}

    	return $searchParam;
    }
}

