<?php

abstract class baseLeaveNotification extends sfAction {

	protected $leaveTypeService;
	protected $leaveRequestService;
	protected $attendanceService;
	protected $leaveApplicationService;
	protected $leaveEntitlementService;
	protected $employeeService;
	protected $systemUserService;
	protected $holidayService;
	protected $leavePeriodService;
	protected $empNumbersToReport = array();
	protected $isWorkingDay;
	const IS_HALF_DAY = 0.5;
	
	/**
	 *
	 * @return LeaveTypeService
	 */
	protected function getLeaveTypeService() {
		if (!($this->leaveTypeService instanceof LeaveTypeService)) {
			$this->leaveTypeService = new LeaveTypeService();
		}
		return $this->leaveTypeService;
	}

	/**
	 *
	 * @param LeaveTypeService $service
	 */
	protected function setLeaveTypeService(LeaveTypeService $service) {
		$this->leaveTypeService = $service;
	}
	
	public function getHolidayService() {
		if (is_null($this->holidayService)) {
			$this->holidayService = new HolidayService();
		}
		return $this->holidayService;
	}
	
	public function getSystemUserService(){
		if (is_null($this->systemUserService)) {
			$this->systemUserService = new SystemUserService();
			$this->systemUserService->setSystemUserDao(new SystemUserDao());
		}
		return $this->systemUserService;
	}
	
	public function getAttendanceService() {
		if (is_null($this->attendanceService)) {
			$this->attendanceService = new AttendanceService();
		}
		return $this->attendanceService;
	}
	
	/**
	 *
	 * @return LeaveRequestService
	 */
	public function getLeaveRequestService() {
		if (is_null($this->leaveRequestService)) {
			$leaveRequestService = new LeaveRequestService();
			$leaveRequestService->setLeaveRequestDao(new LeaveRequestDao());
			$this->leaveRequestService = $leaveRequestService;
		}
	
		return $this->leaveRequestService;
	}
	
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
	
	//--------
	public function getLeavePeriodService() {
	
		if (is_null($this->leavePeriodService)) {
			$leavePeriodService = new LeavePeriodService();
			$leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());
			$this->leavePeriodService = $leavePeriodService;
		}
	
		return $this->leavePeriodService;
	}
	
	public function getLeaveEntitlementService() {
		if (!($this->leaveEntitlementService instanceof LeaveEntitlementService)) {
			$this->leaveEntitlementService = new LeaveEntitlementService();
		}
		return $this->leaveEntitlementService;
	}
	
	public function getLeaveApplicationService() {
		if (!($this->leaveApplicationService instanceof LeaveApplicationService)) {
			$this->leaveApplicationService = new LeaveApplicationService();
		}
		return $this->leaveApplicationService;
	}
	
	protected function applyLeave($date , $empNumber, $leaveTotalTime) {
		if($this->isWorkingDay == self::IS_HALF_DAY) {
			$leaveTotalTime = Leave::LEAVE_HALF_DAY_TIME;
		}
		$workShiftLength = WorkShift::DEFAULT_WORK_SHIFT_LENGTH;
		$leaves = $this->getLeaveRequestService()->getLeaveByDateAndEmpNumber($empNumber, $date);
		if(count($leaves) == 0) {
			$leaveBalance = $this->getLeaveEntitlementService()->getLeaveBalance($empNumber, LeaveType::LEAVE_TYPE_PAID_LEAVE_ID, $attendanceDate);
			if($leaveBalance->getBalance() >= ($leaveTotalTime/$workShiftLength)) {
				$leaveType = LeaveType::LEAVE_TYPE_PAID_LEAVE_ID;
			} else {
				array_push($this->empNumbersToReport, $empNumber);
				return;
			}
		} else {
			return; // If leave is already applied for $attendanceDate.
		}
		$formParameters = array('txtFromDate' => $date, 'txtToDate' => $date ,'txtEmpID'=> $empNumber,'txtLeaveTotalTime' => $leaveTotalTime,
				'txtComment' => 'HRMS Assigned Leave', 'txtLeaveType'=> $leaveType, 'txtEmpWorkShift' => $workShiftLength,
				'txtFromTime' => NULL, 'txtToTime' => NULL, 'leaveDuration' => $leaveTotalTime, 'leaveApplyBy' => 'cronJob');
		$leaveObject = new LeaveParameterObject($formParameters);
		$leaveAppService = new LeaveApplicationService();
		$leaveAppService->applyLeave($leaveObject);
		$mailer = new LeaveNotificationMailer();
		$mailer->sendEmailsForDailyCron($leaveObject);
	}
	
	protected function checkWorkingDay($yesterdayDate) {
		$isWorkingDay = 1;
		if(date('l', strtotime($yesterdayDate)) == 'Saturday' || date('l', strtotime($yesterdayDate)) == 'Sunday') {
			$isWorkingDay = 0;
		} else {
			$holidays = $this->getHolidayService()->searchHolidays($yesterdayDate, $yesterdayDate);
			foreach ($holidays as $holiday) {
				if($holiday->getLength() != Holiday::HOLIDAY_HALF_DAY_LENGTH) {
					$isWorkingDay = 0;
				} else {
					$isWorkingDay = 0.5;
				}
			}
		}
		return $isWorkingDay;
	}
	
	protected function validateDate($date, $format = 'Y-m-d')
	{
		$d = DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}
}