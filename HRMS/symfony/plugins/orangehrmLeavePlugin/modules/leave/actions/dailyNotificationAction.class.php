<?php
/**
 * This is action for cron job which will check for yesterday's attendance record's.
 * If employee has sufficient working hours, will apply for leave according to Working time.
 * For Example : if working hours are between 4 to 5 hours, "Half day leave" will be applied and
 * E-mail notifications will be sent to the respective
 *
 * @param  NULL
 * @return NULL
 * @author Mayur V. Kathale<mayur.kathale@gmail.com>
 */
class dailyNotificationAction extends baseLeaveNotification {
	private $user;
	private $mailer;
	private $authentication;
	private $header;
	private $paramDate;
	private $yesterday;
	const SUPPORT_USER = 285;
	const MAX_LIMIT = 999999;
	const MINIMUM_TIME_FOR_HALF_DAY = 4;
	const MINIMUM_TIME_FOR_FULL_DAY = 5;
	public function preExecute()
	{
		$this->authentication = false;
		parent::preExecute();
		$this->headers = apache_request_headers();
		if(isset($this->headers['auth-key'])) {
			if(base64_decode($this->headers['auth-key']) == 'yourAuthKey') {
				$this->authentication = true;
				$this->paramDate = $this->headers['paramDate'];
			} else {
				$this->authentication = false;
			}
		} else {
			$this->authentication = false;
		}
	}
	
	public function execute($request) {
		set_time_limit(0);
		$logger = Logger::getLogger('dailyNotification');
		$leavesApp = $request->getParameter('leaves');
		($leavesApp == "off")? $leaveApplication = false :$leaveApplication = true;
		if(!$this->authentication) {
			$logger->error('Authentication failed.');
			exit;
		}
		$logger->info('Authentication successful.');
		if(!is_null($this->paramDate)) {
			if($this->validateDate($this->paramDate, 'Y-m-d')) {
				$before3Months = date("Y-m-d", strtotime(date( "Y-m-d", strtotime($this->paramDate)). "-3 Months" ));
				$before9Months = date('Y-m-d', strtotime(date( "Y-m-d", strtotime($this->paramDate)). '-9 Months'));
				$yesterdayDate = date('Y-m-d', strtotime(date( "Y-m-d", strtotime($this->paramDate)). "-1 days"));
			} else {
				$logger->error('unsupported date format caused by option -d: '.$this->paramDate.'. Date format should be yyyy-mm-dd');
			}
		} else {
			$before3Months = date('Y-m-d', strtotime('-3 Months'));
			$before9Months = date('Y-m-d', strtotime('-9 Months'));
			$yesterdayDate = date('Y-m-d', strtotime("-1 days"));
		}
		$this->yesterday = $yesterdayDate;
		/*-------------------- IMPORT ATTENDANCE STARTS HERE ---------------------------*/
		chdir("../../leaveModule/");
		$filename = $this->getCSVFileName($yesterdayDate);
		for ($crashes = 0; $crashes < 3; $crashes++) {
		    try {
		    	$this->getAttendanceService()->deleteAttendanceRecordsByDate($yesterdayDate);
		       	$output = exec('php index.php -f '.$filename);
				$response = explode('-', $output);
				if(trim($response[0]) == 'failure' ) {
					if(trim($response[1]) == 'exception' || trim($response[1]) == 'blank') {
						$this->getAttendanceService()->deleteAttendanceRecordsByDate($yesterdayDate);
						throw new Exception($response[1]);
					} else {
						$logger->error('Attendance import failure. Reason: '.$response[1]);
					}
				} else if(trim($response[0]) == 'success') {
					break;
				}
		    } catch (Exception $e) {
		        $logger->error('Exception in record import : '.$e->getMessage());
		    	if($e->getMessage() == 'blank') {
					$mailer = new LeaveNotificationMailer();
					$mailer->sendEmailsForCronFailure($yesterdayDate);
					exit;
		    	}
		    }
		}
		if($crashes == 3){
			$mailer = new LeaveNotificationMailer();
			$mailer->sendEmailsForCronFailure($yesterdayDate);
			exit;
		}
		/*-------------------- IMPORT ATTENDANCE ENDS HERE ---------------------------*/
		$attendanceRecords = $this->getAttendanceService()->getAllAttendanceRecordsByDate($yesterdayDate);
		$empNumberArray = array();
		$consultantArray = array();
		$this->isWorkingDay = $this->checkWorkingDay($yesterdayDate);
		$this->user = $this->getEmployeeService()->getEmployee(self::SUPPORT_USER);
		$filters["termination"] = 1; // Current employee only
		$filters["location"] = Location::LOCATION_ACTIVE_FOR_ATTENDANCE_SYSTEM;
		$searchParameters = new EmployeeSearchParameterHolder();
		$searchParameters->setFilters($filters);
		$searchParameters->setLimit(self::MAX_LIMIT); // set max limit
		$employees = $this->getEmployeeService()->searchEmployees($searchParameters);
		$consultantList = $this->getSystemUserService()->getUsersByRoles(array(SystemUser::CONSULTANT_USER_ROLE_ID));
		foreach ($consultantList as $consultant) {
			array_push($consultantArray, $consultant->getEmpNumber());
		}
		/*-----------------------LEAVE APPLICAION STARTS HERE-------------------------------*/
		if($leaveApplication) {
			if(!empty($this->isWorkingDay)) {
				foreach ($attendanceRecords as $attendance) {
					$empNumber = $attendance->getEmployeeId();
					$emp = $this->getEmployeeService()->getEmployee($empNumber);
					$location = $emp->getLocations();		
					if(!in_array($attendance->getEmployeeId(), $consultantArray) && $location[0]->getId() == Location::LOCATION_ACTIVE_FOR_ATTENDANCE_SYSTEM) {
						$total = 0;
						if ($attendance->getPunchOutUserTime()) {
							$total = $total + round((strtotime($attendance->getPunchOutUserTime()) - strtotime($attendance->getPunchInUserTime())) / 3600, 2);
						}
						$attendanceDate = date('Y-m-d', strtotime($attendance->getPunchInUserTime()));
						array_push($empNumberArray, $empNumber);
						if($total >= self::MINIMUM_TIME_FOR_HALF_DAY && $total < self::MINIMUM_TIME_FOR_FULL_DAY && $this->isWorkingDay != self::IS_HALF_DAY) {
							$this->applyLeave($attendanceDate, $empNumber, Leave::LEAVE_HALF_DAY_TIME);
						} else if ($total < self::MINIMUM_TIME_FOR_HALF_DAY ) {
							$this->applyLeave($attendanceDate, $empNumber, Leave::LEAVE_FULL_DAY_TIME);
						}
					}
				}
				
				foreach ($employees as $employee) {
					if(!in_array($employee->getEmpNumber(), $empNumberArray) && !in_array($employee->getEmpNumber(), $consultantArray)){
						$empNumber = $employee->getEmpNumber();
		    			$this->applyLeave($yesterdayDate, $empNumber, Leave::LEAVE_FULL_DAY_TIME);
					}
				}
				$this->sendEmailForPendingLeaveApply($this->empNumbersToReport);
			}
		}
		/*-----------------------LEAVE APPLICAION ENDS HERE---------------------------------*/
		/*-----------------------ADD LEAVE ENTITLEMENTS STARTS HERE-------------------------*/
		foreach ($employees as $emp) {
			if($emp->getEmployeeStatus()->getId() == EmploymentStatus::STATUS_FULLTIME_PERMENANT && !in_array($emp->getEmpNumber(), $consultantArray)) {
				if(date('Y-m-d', strtotime($emp->getJoinedDate())) == $before3Months) {
					if($emp->getTotalExperience() >= 3) {
						$this->addEntitlements($emp, LeaveType::LEAVE_TYPE_WFH_ID);
					}
					if($emp->getEmpMaritalStatus() != "Married") {
						$this->addEntitlements($emp, LeaveType::LEAVE_TYPE_MARRIAGE_ID);
					}
					if($emp->getEmpGender() == Employee::GENDER_MALE && $emp->getEmpMaritalStatus() == "Married") {
						$this->addEntitlements($emp, LeaveType::LEAVE_TYPE_PATERNITY_ID);
					}
				}
				if($emp->getJoinedDate() == $before9Months && $emp->getEmpGender() == Employee::GENDER_FEMALE && $emp->getEmpMaritalStatus() == "Married") {
					$this->addEntitlements($emp, LeaveType::LEAVE_TYPE_MATERNITY_ID);
				}
			}
		}
		/*-----------------------ADD LEAVE ENTITLEMENTS ENDS HERE---------------------------*/
		exit;
	}
	
	private function addEntitlements($employee, $leaveId) {	
		$leavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriodByDate(date('Y-m-d'));
		$from = $leavePeriod[0];
		$to = $leavePeriod[1];
		$searchParams = new LeaveEntitlementSearchParameterHolder();
		$searchParams->setFromDate($from);
		$searchParams->setToDate($to);
		$searchParams->setEmpNumber($employee->getEmpNumber());
		$searchParams->setLeaveTypeId($leaveId);
		$entitlements = $this->getLeaveEntitlementService()->searchLeaveEntitlements($searchParams);
		if(count($entitlements) < 1) {
			if($leaveId == LeaveType::LEAVE_TYPE_WFH_ID) {
				$entitlementCount = $this->calculateNoOfLeaves(date('Y-m-d'), $leaveId, $from, $to);
			} else {
				$entitlementCount = $this->getLeaveTypeService()->readLeaveType($leaveId)->getDaysLimit();
			}
		} else {	
			return ;
		}
		$leaveEntitlement = new LeaveEntitlement();
		$leaveEntitlement->setCreditedDate(date('Y-m-d'));
		$leaveEntitlement->setCreatedById($this->user->getEmpNumber());
		$leaveEntitlement->setCreatedByName($this->user->getFirstAndLastNames());
		$leaveEntitlement->setEntitlementType(LeaveEntitlement::ENTITLEMENT_TYPE_ADD);
		$leaveEntitlement->setDeleted(LeaveEntitlement::IS_NOT_DELETED);
		$leaveEntitlement->setDaysUsed(0);
		$leaveEntitlement->setNoOfDays($entitlementCount);
		$leaveEntitlement->setEmpNumber($employee->empNumber);
		$leaveEntitlement->setToDate($to);
		$leaveEntitlement->setFromDate($from);
		$leaveEntitlement->setLeaveTypeId($leaveId);
		$entitlement = $this->getLeaveEntitlementService()->saveLeaveEntitlement($leaveEntitlement);
		if(!empty($entitlement)) {
			$mailer = new LeaveNotificationMailer();
			$mailer->sendEntitlementEmail($employee, $entitlement);
		}
	}
	
	private function calculateNoOfLeaves($joinedDate, $leaveId, $from, $to)
	{
		$noOfMonth = 3;
		if(date('Y', strtotime($joinedDate)) == date('Y', strtotime($from))) {
			$noOfMonth+= (12 - intval(date('m', strtotime($joinedDate))));
		} else {
			$noOfMonth-= intval(date('m', strtotime($joinedDate)));
		}
		$doj = intval(date('d', strtotime($joinedDate)));
		$entitlementCount = $this->getLeaveTypeService()->readLeaveType($leaveId)->getDaysLimit();
		if($doj <=15){
			$noOfMonth += 1;
			$days = $noOfMonth * $entitlementCount;
		}else{
			$days = ($noOfMonth * $entitlementCount)+1;
		}
		return $days;
	}
	
	private function sendEmailForPendingLeaveApply($empNumbersToReport) {
		if(count($empNumbersToReport) > 0) {
			$mailer = new LeaveNotificationMailer();
			$mailer->sendEmailForPendingLeaveApply($empNumbersToReport, $this->yesterday);
		}
	}
	
	private function getCSVFileName($date) {
		return date('d',strtotime($date)).date('m',strtotime($date)).date('Y',strtotime($date)).'.csv';
	}
}