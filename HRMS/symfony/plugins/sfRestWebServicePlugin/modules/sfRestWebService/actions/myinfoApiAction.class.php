<?php
/**
 * returns attendance
 * @return Attendance
 * @package    api
 * @subpackage Action
 * @author     Abhishek Shringi
 */
class myinfoApiAction extends BaseActionRest {
    /**
     * Get EmployeeService
     * @returns EmployeeService
     */
    public function getEmployeeService() {
        if(is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }
    
    public function preExecute() {
    	parent::preExecute();
    }
    
	public function execute($request) {
		if($this->authentication == false) {
			return $this->renderText(json_encode(array('response' => 'failure', 'code' => '401'), JSON_FORCE_OBJECT));
		} 
		$empNumber = $this->authentication->getEmpNumber();
		$employee = $this->getEmployeeService()->getEmployee($empNumber);
		$responseArray['empNumber'] = $employee->getEmpNumber();
		$responseArray['name'] = $employee->getFirstAndLastNames();
		$responseArray['email'] = $employee->getEmpWorkEmail();
		$responseArray['contact'] = $employee->getEmpMobile();
		$responseArray['skype'] = $employee->getEmpSkypeId();
		$responseArray['birthday'] = $employee->getEmpBirthday();
		$responseArray['employeeId'] = $employee->getEmployeeId();
		return $this->renderText(json_encode($responseArray, JSON_FORCE_OBJECT));
    }
}