<?php
/**
 * returns attendance
 * @return Attendance
 * @package    api
 * @subpackage Action
 * @author     Abhishek Shringi
 */
class attendanceAction extends BaseActionRest {
	public function getAttendanceService() {
        if (is_null($this->attendanceService)) {
            $this->attendanceService = new AttendanceService();
        }
        return $this->attendanceService;
    }
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
			return $this->renderText(json_encode(array('response' => 'failure', 'code' => '401')));
		}
		$systemUser = $this->authentication->getEmployee()->getSystemUser();
		$empNumber = $request->getPostParameter('empNumber');
		if(!empty($empNumber)) {
			$allowedEmployees = array($empNumber);
		}
		if($systemUser[0]->getIsAdmin() == 'No') {
			$isAllowedEmployee = $this->getEmployeeService()->getSubordinateIdListBySupervisorIdIncludingSelf($this->authentication->getEmployee()->getEmpNumber(),$empNumber);
			if($empNumber != $this->authentication->getEmployee()->getEmpNumber() && !empty($empNumber) && !$isAllowedEmployee){
				$response['response'] = "failure";
				$response['code'] = 400;
				return $this->renderText(json_encode($response));
			} else {
				if(!empty($empNumber)) {
					$allowedEmployees = $empNumber;
				} else {
					$allowedEmployees = $this->getEmployeeService()->getSubordinateIdListBySupervisorIdIncludingSelf($this->authentication->getEmployee()->getEmpNumber(),$empNumber,true);
				}
			}
		} else {
			if(empty($empNumber)) {
				$allowedEmployees = null;
			}
		}
		$dateStr = $request->getParameter('id');
		if(empty($dateStr)){
			$dateStr = date("Y-m-d");
		}
		$attendanceRecords = $this->getAttendanceService()->getAttendanceRecord($allowedEmployees, $dateStr);
		$responseArray = array();
		if($attendanceRecords!=null) {
			$responseArray = $attendanceRecords->toArray();
		}
		return $this->renderText(json_encode($responseArray));
    }
}