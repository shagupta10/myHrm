<?php
/**
 * This is cron Job which will calculate total working hours of employee in last week
 *
 * @param  NULL
 * @return NULL
 * @author Mayur V. Kathale<mayur.kathale@gmail.com>
 */
class weeklyNotificationAction extends baseLeaveNotification {
	private $authentication;
	private $holidays;
	private $monday;
	private $friday;
	private $unasignedLeaves;
	private $paramDate;
	private $leavesToReport = array();
	const TOTAL_HOURS_FOR_WEEK = 40;
	const TOTAL_HOURS_FOR_DAY = 8;
	const TOTAL_HOURS_FOR_HALF_DAY = 4;
	public function preExecute()
	{
		$this->authentication = false;
		parent::preExecute();
		$headers = apache_request_headers();
		if(isset($headers['auth-key'])) {
			if(base64_decode($headers['auth-key']) == 'yourAuthKey') {
				$this->authentication = true;
				$this->paramDate = $headers['paramDate'];
			} else {
				$this->authentication = false;
			}
		} else {
			$this->authentication = false;
		}
	}

	public function execute($request) {
		$logger = Logger::getLogger('weeklyNotification');
		if(!$this->authentication) {
			$logger->error('Authentication failed.');
			exit;
		}
		$logger->info('Authentication successful.');
		set_time_limit(0);
		if(!is_null($this->paramDate)) {
			if($this->validateDate($this->paramDate, 'Y-m-d')) {
				$monday = date("Y-m-d", strtotime(date( "Y-m-d", strtotime($this->getLastDay(date("Y-m-d", strtotime($this->paramDate)), "Monday")))));
				$friday = date('Y-m-d', strtotime(date( "Y-m-d", strtotime($this->getLastDay(date("Y-m-d", strtotime($this->paramDate)), "Friday")))));
			} else {
				$logger->error('unsupported date format in option -d: '.$this->paramDate.'. Date format should be yyyy-mm-dd');
			}
		} else {
			$monday = date("Y-m-d", strtotime(date( "Y-m-d", strtotime("last Monday"))));
			$friday = date('Y-m-d', strtotime(date( "Y-m-d", strtotime("last Friday"))));
		}
		$this->monday = $monday;
		$this->friday = $friday;
		$previousEmp = null;
		$empNumberArray = array();
		$consultantArray = array();
		$tosend = array();
		$total = 0;		
		
		$consultantList = $this->getSystemUserService()->getUsersByRoles(array(SystemUser::CONSULTANT_USER_ROLE_ID));
		$holidays = $this->getHolidayService()->searchHolidays($monday, $friday); // getHolidays in last week
		$holidaysMinus = 0;
		$this->holidays = array();
		foreach ($holidays as $holiday) {
			array_push($this->holidays, $holiday->getDate());
			if($holiday->getLength() == 4) {
				$holidaysMinus += self::TOTAL_HOURS_FOR_HALF_DAY;
			} else {
				$holidaysMinus += self::TOTAL_HOURS_FOR_DAY;
			}
		}
		foreach ($consultantList as $consultant) {
			array_push($consultantArray, $consultant->getEmpNumber());
		}
		$count = 0;
		$attendanceRecords = $this->getAttendanceService()->getAttendanceRecordsBetweenDays($monday, $friday);
		foreach($attendanceRecords as $attendance) {
			$empNumber = $attendance->getEmployeeId();
			$leavesCnt = $this->getLeaveRequestService()->getLeaveByDateAndEmpNumber($empNumber, date('Y-m-d', strtotime($attendance->getPunchInUserTime())));
			$flagHoliday = false;
			foreach ($this->holidays as $holiday) {
				if (strpos($attendance->getPunchInUserTime(), $holiday) !== false) {
					$flagHoliday = true;
				}
			}
			if(in_array($empNumber, $consultantArray) || count($leavesCnt) > 0 || $flagHoliday) {
				continue;
			}
			
			if($previousEmp != null && $empNumber != $previousEmp->getEmpNumber()) {
				array_push($empNumberArray, $previousEmp->getEmpNumber()); // record's empNumbers which has attendance records for a week
				$leavesCount= $this->calculateLeaves($previousEmp, $monday, $friday);
				if($total < $hoursToComplete = self::TOTAL_HOURS_FOR_WEEK - ($leavesCount + $holidaysMinus)) {
					$mailer = new LeaveNotificationMailer();
					$mailer->sendEmailForWeeklyCron($total, $hoursToComplete, $previousEmp, $this->monday, $this->friday);
				}
				$total = 0;
				$time = $this->getPunchInAndOutTimeDifference($attendance);
				$total = $total + $time;
			}
			if($previousEmp != NULL) {
				if($previousEmp->getEmpNumber() == $attendance->getEmployeeId()) {
					$time = $this->getPunchInAndOutTimeDifference($attendance);
					$total = $total + $time;
				}
			} else {
				$time = $this->getPunchInAndOutTimeDifference($attendance);
				$total = $total + $time;
			}
			$previousEmp = $attendance->getEmployee();
		}
		
		//this will apply leave to last employee in attendance-sheet
		if($previousEmp != null) {
			if(!in_array($previousEmp->getEmpNumber(), $empNumberArray)) {
				array_push($empNumberArray, $previousEmp->getEmpNumber());
			}
			$leavesCount= $this->calculateLeaves($previousEmp);
			if($total < $hoursToComplete = self::TOTAL_HOURS_FOR_WEEK -($leavesCount + $holidaysMinus)) {
				$mailer = new LeaveNotificationMailer();
				$mailer->sendEmailForWeeklyCron($total, $hoursToComplete, $previousEmp, $this->monday, $this->friday);
			}
		}
		
		$filters["termination"] = 1; // 1 is constant for Current employee 
		$searchParameters = new EmployeeSearchParameterHolder();
		$searchParameters->setFilters($filters);
		$searchParameters->setLimit(99999);
		$employees = $this->getEmployeeService()->searchEmployees($searchParameters);
		foreach ($employees as $employee) {
			if(!in_array($employee->getEmpNumber(), $empNumberArray)
				&& !in_array($employee->getEmpNumber(), $consultantArray) 
				&& !empty(trim($employee->getEmployeeId())) ) {
				$leavesCount = $this->calculateLeaves($employee);
				if($leavesCount == self::TOTAL_HOURS_FOR_WEEK) {
					$mailer = new LeaveNotificationMailer();
					$mailer->sendEmailForWeeklyCron($total, $hoursToComplete, $previousEmp, $this->monday, $this->friday);
				} 
			}
		}
		exit;
	}
	
	private function getPunchInAndOutTimeDifference($attendance) {
		$time = (round((strtotime($attendance->getPunchOutUserTime()) - strtotime($attendance->getPunchInUserTime())) / 3600, 2) > 0) ? 
				         round((strtotime($attendance->getPunchOutUserTime()) - strtotime($attendance->getPunchInUserTime())) / 3600, 2) : 0;
		return $time;
	}
	
	private function calculateLeaves($previousEmp, $monday, $friday) {
		$daysMinus = 0;
		$leaves = $this->getLeaveRequestService()->getLeaveByDateAndEmpNumber($previousEmp->getEmpNumber(),
			array('from' => $monday, 'to' => $friday)); // getEmployees applied leaves in lastweek
		foreach($leaves as $leave) {                               // leaves count calculations
			if($leave->getLengthHours() == Leave::LEAVE_FULL_DAY_TIME){
				$daysMinus+=8;
			}
			if($leave->getLengthHours() == Leave::LEAVE_HALF_DAY_TIME){
				$daysMinus+=4;
			}
		}
		return $daysMinus;
	}
	
	private function getAllWeekDates() {
		$step = '+1 day';
		$format = 'Y-m-d';
		$dates = array();
	    $current = strtotime($this->monday);
	    $last = strtotime($this->friday);
	    while( $current <= $last ) { 
	
	        $dates[] = date($format, $current);
	        $current = strtotime($step, $current);
	    }
		return array_reverse($dates);
	}
	
	private function getLastDay($date, $day) {
		if(date("l", strtotime($date)) == $day)  {
			$date = date("Y-m-d", strtotime($date."-1 days"));
		}
	    for($date,$i=0; date("l", strtotime($date)) != $day; $date = date("Y-m-d", strtotime($date."-1 days")),$i++) {
	    	continue;
	    }
	    return $date;
	}
}