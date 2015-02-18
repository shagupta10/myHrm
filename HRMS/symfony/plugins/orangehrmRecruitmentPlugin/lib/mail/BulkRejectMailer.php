<?php

class BulkRejectMailer extends orangehrmMailer {
	private  $employeeService;
	protected $systemUserService;
	//protected $logger;

	public function getEmployeeService() {
		if (is_null($this->employeeService)) {
			$this->employeeService = new EmployeeService();
			$this->employeeService->setEmployeeDao(new EmployeeDao());
		}
		return $this->employeeService;
	}

	public function getSystemUserService() {
		if (empty($this->systemUserService)) {
			$this->systemUserService = new SystemUserService();
		}
		return $this->systemUserService;
	}

	public function  __construct() {
		parent::__construct();
	}

	public function send($idMapping) {
		if (!empty($this->mailer)) {
			$this->sendEmailNotification($idMapping);
		}
	}

	/**
	 * Send email notification
	 */
	public function sendEmailNotification($idMapping){

		$subject = "[HRMS] Bulk Reject Candidate(s) Notification";
		$body = $this->getBody($idMapping);
		$this->sendToAdmin($body, $subject);
		$this->sendToRecruitmentManager($body, $subject);
	}

	private function sendToAdmin($body, $subject){
		$adminUsers = $this->getSystemUserService()->getAdminSystemUsers();
		if(count($adminUsers)>0)
		{
			try {
				foreach ($adminUsers as $to) {
					$recipient = $to->getEmployee();
					$recipientName = $recipient->getEmpWorkEmail();
					$this->message->setFrom($this->getSystemFrom());
					$this->message->setTo($recipientName);
					$this->message->setSubject($subject);
					$this->message->setBody($body, 'text/html');
					$this->mailer->send($this->message);
					$logMessage = "Bulk reject related mail has been sent to  $recipientName. ";
					$this->logResult('Success', $logMessage);
				}
			} catch (Exception $e) {
				$logMessage = "Couldn't send 'bulk reject' related email to $recipientName. ";
				$logMessage .= '. Reason: ' . $e->getMessage();
				$this->logResult('Failure', $logMessage);
			}
		}
	}

	private function sendToRecruitmentManager($body, $subject){
		$recruitmentManagerUsers = $this->getSystemUserService()->getRecruitmentManagerUsers();
		if(count($recruitmentManagerUsers)>0)
		{
			try {
				foreach ($recruitmentManagerUsers as $to) {
					$recipient = $to->getEmployee();
					$recipientName = $recipient->getEmpWorkEmail();
					$this->message->setFrom($this->getSystemFrom());
					$this->message->setTo($recipientName);
					$this->message->setSubject($subject);
					$this->message->setBody($body, 'text/html');
					$this->mailer->send($this->message);
					$logMessage = "Bulk reject mail has been sent to  $recipientName. ";
					$this->logResult('Success', $logMessage);
				}
			} catch (Exception $e) {
				$logMessage = "Couldn't send 'bulk reject' related email to $recipientName. ";
				$logMessage .= '. Reason: ' . $e->getMessage();
				$this->logResult('Failure', $logMessage);
			}
		}
	}

	/**
	 * Return the Subject as per action
	 */
	private function getBody($idMapping){
		$emp = $this->getEmployeeService()->getEmployee(sfContext::getInstance()->getUser()->getEmployeeNumber());
		$name = trim(trim($emp['firstName']) . ' ' . $emp['lastName']);
		$body = "<strong>". $name. "</strong> rejected <strong>".count($idMapping)."</strong> candidate(s) from HRMS, These candidates are as follows :<br><br><br>";
		$body .= '<table border="1" cellpadding="10" cellspacing="0"><th>Candidate Name</th><th>Vacancy Name</th>';
		foreach ($idMapping as $detail) {
			$body .= "<tr><td>".$detail['candidateName']."</td><td>".$detail['vacancyName']."</td></tr>";
		}
		$body .= "</table>";
		return $body;
	}
}