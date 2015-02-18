<?php
class leaveBalanceApiAction extends BaseActionRest {
	private $leavePeriodService;
	private function getLeavePeriodService() {
        if (is_null($this->leavePeriodService)) {
            $leavePeriodService = new LeavePeriodService();
            $leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());
            $this->leavePeriodService = $leavePeriodService;
        }
        return $this->leavePeriodService;
    }
	public function getEmployeeService() {
        if(is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }
    
    /**
     * @return LeaveEntitlementService
     */
    public function getLeaveEntitlementService() {
        if (is_null($this->leaveEntitlementService)) {
            $this->leaveEntitlementService = new LeaveEntitlementService();
        }
        return $this->leaveEntitlementService;
    }
	public function preExecute() {
    	parent::preExecute();
    }
	public function execute($request) {
		if($this->authentication == false) {
			return $this->renderText(json_encode(array('response' => 'failure', 'code' => '401'), JSON_FORCE_OBJECT));
		}
		$empNumber = $this->authentication->getEmployee()->getEmpNumber();
		$type = $request->getParameter('type');
		switch($type) {
			case 'wfh':
				$leaveTypeId = LeaveType::LEAVE_TYPE_WFH_ID;
				break;
			case 'paid':
				$leaveTypeId = LeaveType::LEAVE_TYPE_PAID_LEAVE_ID;
				break;
			case 'unpaid':
				$leaveTypeId = LeaveType::LEAVE_TYPE_UNPAID_ID;
				break;
			case 'paternity':
				$leaveTypeId = LeaveType::LEAVE_TYPE_PATERNITY_ID;
				break;
			case 'maternity':
				$leaveTypeId = LeaveType::LEAVE_TYPE_MATERNITY_ID;
				break;
			case 'marriage':
				$leaveTypeId = LeaveType::LEAVE_TYPE_MARRIAGE_ID;
				break;
		}
		$systemUser = $this->authentication->getEmployee()->getSystemUser();
		$employeeId = $request->getPostParameter('employeeId');
		$requestedEmployee = $this->getEmployeeService()->getEmployeeByEmployeeId($employeeId);
		if($systemUser[0]->getIsAdmin() == "No" 
			&& !empty($employeeId) 
					&& $empNumber != $requestedEmployee->getEmpNumber()) {
				$response['response'] = "failure";
				$response['code'] = 400;
				return $this->renderText(json_encode($response));
		} else {
			if(!empty($employeeId)) {	
				$empNumber = $requestedEmployee->getEmpNumber();
			}
		}
		$currentLeavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriodByDate(date('Y-m-d'));
		if($leaveTypeId != LeaveType::LEAVE_TYPE_WFH_ID) {
			$leaveBalanceObj = $this->getLeaveEntitlementService()->getLeaveBalance($empNumber,
			$leaveTypeId, $currentLeavePeriod[0], $currentLeavePeriod[1]);
			$leaveBalance = $leaveBalanceObj->getBalance();
		} else {
			$wflLeaveCalculator = new WfhLeaveBalanceCalculator();
			$leaveBalance = $wflLeaveCalculator->getWfhLeaveBalance($empNumber);
		}
		
		return $this->renderText(json_encode(array('leaveTypeRestCode'=> $this->leaveRestCodeMapping[$leaveTypeId-1] ,'leaveTypeId' => $leaveTypeId, 'balance'=> $leaveBalance),JSON_FORCE_OBJECT));
	}
}