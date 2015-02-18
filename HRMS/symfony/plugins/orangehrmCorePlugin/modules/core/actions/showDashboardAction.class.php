<?php
class showDashboardAction extends sfAction {
	private $candidateService;
	private $employeeService;
	private $leavePeriodService;
	private $leaveRequestService;
	private $leaveTypeService;
	private $leaveEntitlementService;
	
	public function getLeaveEntitlementService() {
		if (empty($this->leaveEntitlementService)) {
			$this->leaveEntitlementService = new LeaveEntitlementService();
		}
		return $this->leaveEntitlementService;
	}
	
	public function setLeaveEntitlementService($leaveEntitlementService) {
		$this->leaveEntitlementService = $leaveEntitlementService;
	}

	public function getEmployeeService(){
		if(is_null($this->employeeService)){
			$this->employeeService =new EmployeeService();
			$this->employeeService->getEmployeeDao(new EmployeeDao());
		}
		return $this->employeeService;
	}
	/**
	 * Get CandidateService
	 *
	 * @return s CandidateService
	 */
	public function getCandidateService() {
		if (is_null ( $this->candidateService )) {
			$this->candidateService = new CandidateService ();
			$this->candidateService->setCandidateDao ( new CandidateDao () );
		}
		return $this->candidateService;
	}
	/**
	 * Set CandidateService
	 *
	 * @param CandidateService $candidateService        	
	 */
	public function setCandidateService(CandidateService $candidateService) {
		$this->candidateService = $candidateService;
	}
	public function execute($request) {
		$empNumber = sfContext::getInstance()->getUser()->getEmployeeNumber();
		$leavePeriod = $this->getCurrentLeavePeriod();
		if (!empty($leavePeriod)) {
			$fromDate = $leavePeriod[0];
			$toDate = $leavePeriod[1];
		}
		//-------  For Todays Birthday ----------
		
		$getNames = $this->getEmployeeService()->getTodaysBirthday();
		$birthcount = count($getNames);
		$this->empBirthList = $getNames;
		$this->empBirthCount = $birthcount;
		//------   End of Todays Birthday
	
		// ------------------- for MyLeave Action -----------------
		$wflLeaveCalculator = new WfhLeaveBalanceCalculator();
		$this->WFHBalForMonth = $wflLeaveCalculator->getWfhLeaveBalance($empNumber);
		$leaveBalanceObj = $this->getLeaveEntitlementService()->getLeaveBalance($empNumber, LeaveType::LEAVE_TYPE_PAID_LEAVE_ID,
				null, null);
		$this->paidLeaveBal = $leaveBalanceObj->balance;
		// ---------------------- End of MyLeaveAction ------------------
			
		/*Added by sujata */
		$leaveMode = $this->getLeaveMode();
		$this->baseUrl = $leaveMode == LeaveListForm::MODE_MY_LEAVE_LIST ? 'leave/myCalender' : 'leave/calender';
		$employeeFilter = $this->getEmployeeFilter($leaveMode);
		$statuses = array(1,2,3);
		$searchParams = new ParameterObject(array(
				'dateRange' => new DateRange($fromDate, $toDate),
				'statuses' => $statuses,
				'employeeFilter' => $employeeFilter,
				'noOfRecordsPerPage' => sfConfig::get('app_items_per_page'),
		));
		$result = $this->searchLeaveRequests($searchParams, $page);
		$list = $result['list'];
		$list = empty($list) ? null : $list;
		
		$balanceRequest = array();
		$leaveEvent = array();
		foreach($list as $leave) {
            $leaveObject = new LeaveRequestJSON();
            $leaveObject->setDateRange($leave->getLeaveDateRange());
            $leaveObject->setAction($this->getLeaveRequestService()->getLeaveRequestActions($leave, 1));
            $leaveObject->setEmployeeName($leave->getEmployee()->getFirstAndLastNames());
            $leaveObject->setStatus($leave->getLeaveBreakdown());
            $leaveObject->setLeaveType($leave->getLeaveType()->getDescriptiveLeaveTypeName());
            
            $dates = explode(' to ', $leaveObject->dateRange);            
            $dates[1] = (!isset($dates[1]) || empty($dates[1]))? $dates[0]:$dates[1];
            
            $leaveEvent[] = array(
                'status'     =>    $leaveObject->status,
                'type'       =>    strtolower($leaveObject->leaveType), 
                'title'      =>    $leaveObject->employeeName,
                'start'      =>    date('Y-m-d', strtotime($dates[0]))."T10:00:00",
                'end'        =>    date('Y-m-d', strtotime($dates[1]))."T19:00:00",
                'id'         =>    $leave->getId()
            );
        }
		
		$this->leaveEventList = $leaveEvent;
		foreach ($this->getElegibleLeaveTypes() as $leaveType) {
			$leaveTypes[$leaveType->getId()] = $leaveType->getName();
		}
		$this->leaveTypes = $leaveTypes;
	}
	
	// ------------------ Following Functions used for MyLeaveBalance --------------------
   protected function getLeaveMode() {
	
        if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 'Yes') {
            $mode = LeaveListForm::MODE_MY_LEAVE_LIST;
        }else{
            $mode = LeaveListForm::MODE_ADMIN_LIST;
        }
        return $mode;
    }
	public function getLeavePeriodService() {
	
		if (is_null($this->leavePeriodService)) {
			$leavePeriodService = new LeavePeriodService();
			$leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());
			$this->leavePeriodService = $leavePeriodService;
		}
	
		return $this->leavePeriodService;
	}
	
	
	//Added by sujata
	protected function subordinateEmployees($empNumber) {
		return $this->getEmployeeService()->getSubordinateListForEmployee($empNumber);
	}
	
	protected function getEmployeeFilter($mode, $empNumber='') {
		$loggedInEmpNumber = $this->getUser()->getAttribute('auth.empNumber');
		// default filter to null. Will fetch all employees
		$employeeFilter = null;
		if ($mode == LeaveListForm::MODE_MY_LEAVE_LIST) {
			if($_SESSION['isSupervisor']) {
				$reportingEmployees = $this->subordinateEmployees($loggedInEmpNumber);
				foreach ($reportingEmployees as $reportingEmployee){
					$arrReportingEmployees[] = $reportingEmployee->getSubordinate()->getEmpNumber();
				}
				array_push($arrReportingEmployees,$loggedInEmpNumber);
				$employeeFilter = $arrReportingEmployees;
			}else{
				$employeeFilter = $loggedInEmpNumber;
			}
		} else {
			$manager = $this->getContext()->getUserRoleManager();
			$requiredPermissions = array(
					BasicUserRoleManager::PERMISSION_TYPE_ACTION => array('view_leave_list'));
			$accessibleEmpIds = $manager->getAccessibleEntityIds('Employee', null, null, array(), array(), $requiredPermissions);
			if (empty($empNumber)) {
				$employeeFilter = $accessibleEmpIds;
			} else {
				if (in_array($empNumber, $accessibleEmpIds)) {
					$employeeFilter = $empNumber;
				} else {
					// Requested employee is not accessible.
					$employeeFilter = array();
				}
			}
		}
		return $employeeFilter;
	}
	protected function searchLeaveRequests($searchParams, $page) {
		$result = $this->getLeaveRequestService()->searchLeaveRequests($searchParams, $page, false, false, true, true);
		return $result;
	}
	public function getLeaveRequestService() {
		if (is_null($this->leaveRequestService)) {
			$leaveRequestService = new LeaveRequestService();
			$leaveRequestService->setLeaveRequestDao(new LeaveRequestDao());
			$this->leaveRequestService = $leaveRequestService;
		}
		return $this->leaveRequestService;
	}
	
	public function setLeaveRequestService(LeaveRequestService $leaveRequestService) {
		$this->leaveRequestService = $leaveRequestService;
	}
	protected function getCurrentLeavePeriod() {
		// If leave period defined, use leave period start and end date
		$leavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriodByDate(date('Y-m-d', time()));
		return $leavePeriod;
	}
	protected function getLeaveTypeService() {
		if (!($this->leaveTypeService instanceof LeaveTypeService)) {
			$this->leaveTypeService = new LeaveTypeService();
		}
		return $this->leaveTypeService;
	}
	
	protected function setLeaveTypeService(LeaveTypeService $service) {
		$this->leaveTypeService = $service;
	}
	protected function getElegibleLeaveTypes() {
		$leaveTypeList = $this->getLeaveTypeService()->getLeaveTypeList();
		return $leaveTypeList;
	}
}


