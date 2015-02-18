<?php


class WfhLeaveBalanceCalculator {
	private $leavePeriodService;
	private $leaveRequestService;
	private $leaveTypeService;
	private $leaveEntitlementService;
	private function getLeavePeriodService() {
        if (is_null($this->leavePeriodService)) {
            $leavePeriodService = new LeavePeriodService();
            $leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());
            $this->leavePeriodService = $leavePeriodService;
        }
        return $this->leavePeriodService;
    }
	private function getLeaveRequestService() {
		if (is_null($this->leaveRequestService)) {
			$leaveRequestService = new LeaveRequestService();
			$leaveRequestService->setLeaveRequestDao(new LeaveRequestDao());
			$this->leaveRequestService = $leaveRequestService;
		}
		return $this->leaveRequestService;
	}
    private function getLeaveTypeService() {
		if (!($this->leaveTypeService instanceof LeaveTypeService)) {
			$this->leaveTypeService = new LeaveTypeService();
		}
		return $this->leaveTypeService;
	}	
	
 	private function getLeaveEntitlementService() {
        if (is_null($this->leaveEntitlementService)) {
            $this->leaveEntitlementService = new LeaveEntitlementService();
        }
        return $this->leaveEntitlementService;
    }
    
    public function getWfhLeaveBalance($empNumber, $monthArray=null) {
    	$currentLeavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriodByDate(date('Y-m-d'));
    	
		if(!empty($monthArray)) {
			$first_day_this_month = $monthArray[0];
			$last_day_this_month  = $monthArray[1];
		} else {
			$first_day_this_month = date('Y-m-01');
			$last_day_this_month  = date('Y-m-t');
		}
		$entitlementList = $this->getLeaveEntitlementService()->getMatchingEntitlements($empNumber, LeaveType::LEAVE_TYPE_WFH_ID, $currentLeavePeriod[0], $currentLeavePeriod[1]);
		if(count($entitlementList) > 0){
			$wfhLeaveType = $this->getLeaveTypeService()->readLeaveTypeByName(LeaveType::LEAVE_TYPE_WFH);
			$wfhLimitDB = empty($wfhLeaveType->getDaysLimit()) ? LeaveType::WFH_LIMIT_PER_MONTH : $wfhLeaveType->getDaysLimit();
			$wfhleaveTypeId = $wfhLeaveType->getId();
			$takenWFH = $this->getLeaveRequestService()->getWFHMonthlyBalance($first_day_this_month, $last_day_this_month, $empNumber);
			$leaveBalance = $wfhLimitDB - $takenWFH;
		}else{
			$leaveBalance = 0;
		}
		return $leaveBalance;
    }
}