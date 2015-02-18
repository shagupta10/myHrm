<?php
/**
 * returns json array of leave type
 * @return LeaveType
 * @package    api
 * @subpackage Action
 * @author     Mayur Kathale
 */
class leaveTypeApiAction extends BaseActionRest {
    private function getEmployeeService() {
        if(is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }
 	private function getLeaveTypeService() {

        if (is_null($this->leaveTypeService)) {
            $this->leaveTypeService = new LeaveTypeService();
        }

        return $this->leaveTypeService;
    }
    private function getLeaveRequestService() {
        if (!($this->leaveRequestService instanceof LeaveRequestService)) {
            $this->leaveRequestService = new LeaveRequestService();
        }
        return $this->leaveRequestService;
    }
    public function preExecute() {
    	parent::preExecute();
    }
    
	public function execute($request) {
		if($this->authentication == false) {
			return $this->renderText(json_encode(array('response' => 'failure', 'code' => '401'), JSON_FORCE_OBJECT));
		}
		if($request->getParameter('all')!=null) {
			$responseArray = array();
			$leavetypeList = $this->getLeaveTypeService()->getLeaveTypeList();
			foreach ($leavetypeList as $leaveTypeObj){
				$responseArray[] = array('leaveTypeRestCode' => $this->leaveRestCodeMapping[$leaveTypeObj->getId()-1], 'leaveTypeId' => $leaveTypeObj->getId(), 
				'leaveTypeName' => $leaveTypeObj->getName());
			}
			return $this->renderText(json_encode($responseArray));
		}
		$systemUser = $this->authentication->getEmployee()->getSystemUser();
		$empNumber = $request->getPostParameter('empNumber');
		if($systemUser[0]->getIsAdmin()!='Yes' && isset($empNumber)) {
			$response['response'] = "failure";
			$response['code'] = 400;
			return $this->renderText(json_encode($response));
		} else {
			if($systemUser[0]->getIsAdmin()!='Yes' || ($systemUser[0]->getIsAdmin()=='Yes' && empty($empNumber))) {
				$empNumber = $this->authentication->getEmployee()->getEmpNumber();
			}
		}
		$leavetypes = $this->getElegibleLeaveTypes($empNumber);
		return $this->renderText(json_encode($leavetypes));
    }
    
    private function getElegibleLeaveTypes($empNumber) {
        $responseArray = array();
        $employeeService = $this->getEmployeeService();
        $employee = $employeeService->getEmployee($empNumber);
        $leaveRequestService = $this->getLeaveRequestService();
        $leaveTypeList = $leaveRequestService->getEmployeeAllowedToApplyLeaveTypes($employee);
        foreach ($leaveTypeList as $leaveTypeObj) {
            $responseArray[] = array('leaveTypeRestCode' => $this->leaveRestCodeMapping[$leaveTypeObj->getId()-1], 'leaveTypeId' => $leaveTypeObj->getId(), 
				'leaveTypeName' => $leaveTypeObj->getName());
        }
        return $responseArray;
    }
}