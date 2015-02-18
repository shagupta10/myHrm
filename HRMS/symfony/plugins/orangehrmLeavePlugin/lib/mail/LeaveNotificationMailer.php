<?php

class LeaveNotificationMailer extends orangehrmMailer {
	private $systemUserService;
	private $leaveTypeService;
	private $employeeService;
	
	private function getSystemUserService() {
		if (is_null($this->systemUserService)) {
			$this->systemUserService = new SystemUserService();
			$this->systemUserService->setSystemUserDao(new SystemUserDao());
		}
		return $this->systemUserService;
	}
	
	private function getLeaveTypeService() {
		if (!isset($this->leaveTypeService)) {
			$this->leaveTypeService = new LeaveTypeService();
		}
		return $this->leaveTypeService;
	}
	
	private function getEmployeeService() {
		if(is_null($this->employeeService)) {
			$this->employeeService = new EmployeeService();
			$this->employeeService->setEmployeeDao(new EmployeeDao());
		}
		return $this->employeeService;
	}
	
	public function  __construct() {
		parent::__construct();
	}
	
	private function getAdminEmails() {
		$adminArray = array();
		$adminUsers = $this->getSystemUserService()->getAdminSystemUsers();
		foreach ($adminUsers as $to) {
			$recipient = $to->getEmployee();
			if(trim($recipient->getEmpWorkEmail()) != "") {
				array_push($adminArray, $recipient->getEmpWorkEmail());
			}
		}
		return $adminArray;
	}
	
	private  function getRecipients($empNumber){
		$recipientsArray = array();
		$recipients = $this->getEmployeeService()->getDirectSupervisors($empNumber);
		foreach ($recipients as $recipient) {
			$emp = $this->getEmployeeService()->getEmployee($recipient['erep_sup_emp_number']);
			array_push($recipientsArray, $emp->getEmpWorkEmail());
		}
		return $recipientsArray;
	}
	
	private function getBody($employee, $entitlement) {
		$leavetype = $this->getLeaveTypeService()->readLeaveType($entitlement->getLeaveTypeId());
		$bodyString  = "Hello,\n\t".$entitlement->getNoOfDays()." \"".$leavetype."\" leave(s) entitled to ".$employee->getFirstAndLastNames();
		$bodyString.= "\n\n\nThank you\nThis is automated notification";
		return $bodyString;
	}
	
	private function getBodyForDailyCron($employee, $leaveObject, $self = false){
		if($self) {
			$bodyString = "Hi ".$employee->getFirstAndLastNames().",<br><p>Leave has been applied to you by HRMS as 
				there's short fall in working hours on <strong>".set_datepicker_date_format($leaveObject->getFromDate())."</strong>.<br><br>Thank you.<br>This is automated notification." ;
		} else {
			$bodyString = "Hi,<br><p>Leave has been applied to ".$employee->getFirstAndLastNames().
				" by HRMS as there's short fall in working hours on <strong>".set_datepicker_date_format($leaveObject->getFromDate())."</strong>.<br><br>Thank you.<br>This is automated notification." ;
		}
		return $bodyString;
	}
		
	private function getBodyForPendingLeaveApply($empNumbersToReport, $date) {
		$bodyString  = "Hello ,<br>Following employees found short in thier attendance on <strong>".set_datepicker_date_format($date)."</strong> and HRMS is unable to apply leaves due to insufficient leave balance.";
		$bodyString.= "<br><br><table border = '1' cellspacing = '0'><tbody>";
		foreach ($empNumbersToReport as $empNumber) {
			$bodyString.= "<tr><td>".$this->getEmployeeService()->getEmployee($empNumber)->getFirstAndLastNames()."</td></tr>";
		}
		$bodyString.= "</tbody></table>";
		$bodyString.= "<br><br>Thank you.<br>This is automated notification.";
		return $bodyString;
	}
	
	public function sendEntitlementEmail($employee, $entitlement) {
		$subject = "SynerzipHRM - Leaves Entitled to ".$employee->getFirstAndLastNames()." by HRMS";
		$body = $this->getBody($employee, $entitlement);
		try{
			$subject = '[Synerzip-HRMS] : Added entitlements.';
			$this->message->setFrom($this->getSystemFrom());
			$this->message->setTo($this->getAdminEmails());
			$this->message->setSubject($subject);
			$this->message->setBody($body);
			$this->mailer->send($this->message);
			$logMessage = "Sent leave entitlement related email to ".implode(', ', $this->getAdminEmails());
			$this->logResult('Success', $logMessage);
		} catch (Exception $e) {
			$logMessage = "Couldn't send leave entitlement related email to ".implode(', ', $this->getAdminEmails());
			$logMessage .= 'Reason: ' . $e->getMessage();
			$this->logResult('Failure', $logMessage);
		}
	}
	
	public function sendEmailForPendingLeaveApply($empNumbersToReport, $date) {
		try{
			$subject = '[Synerzip-HRMS] : Unable to apply leaves due to insufficient balance.';
			$this->message->setFrom($this->getSystemFrom());
			$this->message->setTo($this->getAdminEmails());
			$this->message->setSubject($subject);
			$this->message->setBody($this->getBodyForPendingLeaveApply($empNumbersToReport, $date),'text/html');
			$this->mailer->send($this->message);
			$logMessage = "Sent leave failure related email to ".implode(', ', $this->getAdminEmails());
			$this->logResult('Success', $logMessage);
		} catch (Exception $e) {
			$logMessage = "Couldn't send leave failure related email to ".implode(', ', $this->getAdminEmails());
			$logMessage .= 'Reason: ' . $e->getMessage();
			$this->logResult('Failure', $logMessage);
		}
	}
	
	private function sendEmailsForDailyCronToSelf($employee, $leaveObject) {
		try{
			$subject = '[Synerzip-HRMS] : Leave has been applied to you by HRMS.';
			$this->message->setFrom($this->getSystemFrom());
			$this->message->setCc($this->getRecipients($leaveObject->getEmployeeNumber()));
			$this->message->setTo($employee->getEmpWorkEmail());
			$this->message->setSubject($subject);
			$this->message->setBody($this->getBodyForDailyCron($employee, $leaveObject, true),'text/html');
			$this->mailer->send($this->message);
			$logMessage = "Sent leave apply by cron related email to ".implode(', ', $to);
			$this->logResult('Success', $logMessage);
		} catch (Exception $e) {
			$logMessage = "Couldn't send leave apply by cron related email to ".implode(', ', $to);
			$logMessage .= 'Reason: ' . $e->getMessage();
			$this->logResult('Failure', $logMessage);
		}
	}
	
	public function sendEmailsForDailyCron(LeaveParameterObject $leaveObject) {
		$employee = $this->getEmployeeService()->getEmployee($leaveObject->getEmployeeNumber());
		$this->sendEmailsForDailyCronToSelf($employee, $leaveObject);
	}
	/*----- DAILY CRON FAILURE STARTS HERE -------------*/
	private function getBodyForDailyCronFailure($name, $date) {
		return 'Hi '.$name.', <br>&nbsp;&nbsp;&nbsp;&nbsp; This email serves as a notification that execution of <strong>daily leave cron</strong> has failed for '
			.date('D, d M Y',strtotime($date)).'.<br><br>Thank you.<br>This is automated notification. You are recieving this notification as you are subscribed for cron job notifications in HRMS';
	}
	
	public function sendEmailsForCronFailure($date) {
		$mailNotificationService = new EmailNotificationService();
		$recipients = array();
		$subscriptions = $mailNotificationService->getSubscribersByNotificationId(EmailNotification::LEAVE_CRON);
		foreach ($subscriptions as $subscription) {
			if ($subscription->getEmailNotification()->getIsEnable() == EmailNotification::ENABLED) {
				if ($subscription->getEmailNotification()->getIsEnable() == EmailNotification::ENABLED) {
					array_push($recipients, array('name' => $subscription->getName(), 'email' => $subscription->getEmail()));
				}
			}
		}
		try{
			foreach ($recipients as $recipient) {
				$subject = '[Synerzip-HRMS] : Daily cron for attendance and leave failed.';
				$this->message->setFrom($this->getSystemFrom());
				$this->message->setTo($recipient['email']);
				$this->message->setSubject($subject);
				$this->message->setBody($this->getBodyForDailyCronFailure($recipient['name'],$date),'text/html');
				$this->mailer->send($this->message);
				$logMessage = "Sent cron related email to ".$recipient['email'];
				$this->logResult('Success', $logMessage);
			}
		} catch (Exception $e) {
			$logMessage = "Couldn't send cron related email to ".$recipient['email'];
			$logMessage .= 'Reason: ' . $e->getMessage();
			$this->logResult('Failure', $logMessage);
		}		
	}
	/*----- DAILY CRON FAILURE ENDS HERE -------------*/
	/*----- WEEKLY NOTIFICATION STARTS HERE -------------*/
	private function setEmailContentForWeeklyLeaves($total, $hoursToComplete, $emp, $start, $end) {
		$this->message->setTo($emp->getEmpWorkEmail());
		$this->message->setCc($this->getRecipients($emp->getEmpNumber()));
		$subject = '[Synerzip-HRMS] : Weekly Attendance notification.';
		$bodyString = "Hi ".$emp->getFirstAndLastNames().", <br><p>This email serves as a notification that your working hours has fallen short for Week [<strong>".
		set_datepicker_date_format($start)." to ".set_datepicker_date_format($end)."</strong>].<br><br>Thank you,<br>This is an automated notification, Please do not reply.";
		$this->message->setSubject($subject);
		$this->message->setBody($bodyString,'text/html');
	}
	
	public function sendEmailForWeeklyCron($total, $hoursToComplete, $emp, $start, $end) { // weekly cron
		try{
			$this->message->setFrom($this->getSystemFrom());
			$this->setEmailContentForWeeklyLeaves($total, $hoursToComplete, $emp, $start, $end );
			$this->mailer->send($this->message);
			$logMessage = "Sent weekly cron related email";
			$this->logResult('Success', $logMessage);
		} catch (Exception $e) {
			$logMessage = "Couldn't weekly cron related email";
			$logMessage .= 'Reason: ' . $e->getMessage();
			$this->logResult('Failure', $logMessage);
		}		
	}
	/*----- WEEKLY NOTIFICATION ENDS HERE -------------*/
}