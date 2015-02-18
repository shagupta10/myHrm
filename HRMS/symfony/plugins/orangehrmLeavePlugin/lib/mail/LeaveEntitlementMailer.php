<?php

class LeaveEntitlementMailer extends orangehrmMailer {
	private $systemUserService;
	private $leaveTypeService;
	
	public function getSystemUserService() {
		if (is_null($this->systemUserService)) {
			$this->systemUserService = new SystemUserService();
			$this->systemUserService->setSystemUserDao(new SystemUserDao());
		}
		return $this->systemUserService;
	}
	
	public function getLeaveTypeService() {
		if (!isset($this->leaveTypeService)) {
			$this->leaveTypeService = new LeaveTypeService();
		}
		return $this->leaveTypeService;
	}
	
	public function  __construct() {
		parent::__construct();
	}
	
	public function send($employee, $entitlement) {
		$roleList = array(SystemUser::ADMIN_USER_ROLE_ID);
		$adminUsers = $this->getSystemUserService()->getUsersByRoles($roleList);	
		$subject = "SynerzipHRM - Leaves Entitled to ".$employee->getFirstAndLastNames()." by HRMS";
		foreach ($adminUsers as $adminUser) {
			$recipient = $adminUser->getEmployee();
			$body = $this->getBody($employee, $entitlement, $adminUser);
			$this->sendEmailNotification($recipient->getEmpWorkEmail(), $subject, $body);
		}
	}
	
	/**
	 * Send email notification
	 */
	public function sendEmailNotification($to, $subject, $body) {
		try{
			$this->message->setFrom($this->getSystemFrom());
			$this->message->setTo($to);
			$this->message->setSubject($subject);
			$this->message->setBody($body);
			$this->mailer->send($this->message);
			$logMessage = "Sent leave entitlement related email to $to";
			$this->logResult('Success', $logMessage);
		} catch (Exception $e) {
			$logMessage = "Couldn't send leave entitlement related email to $to";
			$logMessage .= '. Reason: ' . $e->getMessage();
			$this->logResult('Failure', $logMessage);
		}
	}
	
	public function getBody($employee, $entitlement , $adminUser) {
		$leavetype = $this->getLeaveTypeService()->readLeaveType($entitlement->getLeaveTypeId());
		$bodyString  = "Hello ".$adminUser->getEmployee()->getFirstName()." ,\n\t".$entitlement->getNoOfDays()." \"".$leavetype."\" leave(s) entitled to ".$employee->getFirstAndLastNames();
		$bodyString.= "\n\n\nThank you\nThis is automated notification";
		return $bodyString;
	}
}