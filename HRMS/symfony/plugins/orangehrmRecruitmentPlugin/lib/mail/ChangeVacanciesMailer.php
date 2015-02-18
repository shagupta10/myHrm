<?php

class ChangeVacanciesMailer extends orangehrmMailer {
	private  $employeeService;
	protected $systemUserService;
	private $emp;
	private $name;
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
		$this->emp = $this->getEmployeeService()->getEmployee(sfContext::getInstance()->getUser()->getEmployeeNumber());
		$this->name = trim(trim($this->emp['firstName']) . ' ' . $this->emp['lastName']);
	}

	public function send($mappingArray, $vacancyName, $hiringManagers) {
		if (!empty($this->mailer)) {
			$this->sendEmailNotification($mappingArray, $vacancyName, $hiringManagers);
		}
	}
	
	
	public function sendRequestNotification($requestDetails, $vacancyName) {
		if (!empty($this->mailer)) {
			$subject = "[HRMS] Request to consider and move candidate(s) to ".$vacancyName.": ".$this->name.".";
			$body = $this->getBodyforRequest($requestDetails, $vacancyName);
			$this->sendToAdmin($body, $subject);
			$this->sendToRecruitmentManager($body, $subject);
		}
	}

	/**
	 * Send email notification
	 */
	public function sendEmailNotification($mappingArray, $vacancyName, $hiringManagers){
		
		$subject = "[HRMS] Change Multiple Vacancies.";
		$body = $this->getBody($mappingArray, $vacancyName);
		$this->sendToAdmin($body, $subject);
		$this->sendToRecruitmentManager($body, $subject);
		$this->sendToHiringManager($body, $subject, $hiringManagers);
	}
	
	private function getBodyforRequest($requestDetails, $vacancyName) {
		$url = (empty($_SERVER['HTTPS']) OR $_SERVER['HTTPS'] === 'off') ? 'http://' : 'https://';
		$url .= $_SERVER['HTTP_HOST'];
		$body = "<strong>". $this->name. "</strong> requested to change vacancies of ".count($requestDetails)." candidate(s) to <strong>". $vacancyName.
		"</strong>.<br>These candidates are as follows :<br><br><br>";
		$body .= '<table border="1" cellpadding="10" cellspacing="0"><th>Candidate Name</th><th>Previous Vacancy</th>';
		foreach ($requestDetails as $detail) {
			$linkUrl =   $url."/symfony/web/index.php/recruitment/addCandidate?id=".$detail['candidateId'];
			$body .= '<tr><td><a href="'.$linkUrl.'">'.$detail["candidateName"].'</a></td><td>'.$detail["olVacancyName"].'</td></tr>';
		}
		$body .= "</table>";
		return $body;
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
					$logMessage = "'change multiple vacancy' related mail has been sent to  $recipientName. ";
					$this->logResult('Success', $logMessage);
				}
			} catch (Exception $e) {
				$logMessage = "Couldn't send 'change multiple vacancy' related email to $recipientName. ";
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
					$logMessage = "'change multiple vacancy' related mail has been sent to  $recipientName. ";
					$this->logResult('Success', $logMessage);
				}
			} catch (Exception $e) {
				$logMessage = "Couldn't send 'change multiple vacancy' related email to $recipientName. ";
				$logMessage .= '. Reason: ' . $e->getMessage();
				$this->logResult('Failure', $logMessage);
			}
		}
	}
	
	private function sendToHiringManager($body, $subject, $hiringManagers){
		if(count($hiringManagers)>0)
		{
			try {
				foreach ($hiringManagers as $to) {
					$recipient = $to->getEmployee();
					$recipientName = $recipient->getEmpWorkEmail();
					$this->message->setFrom($this->getSystemFrom());
					$this->message->setTo($recipientName);
					$this->message->setSubject($subject);
					$this->message->setBody($body, 'text/html');
					$this->mailer->send($this->message);
					$logMessage = "'change multiple vacancy' related mail has been sent to  $recipientName. ";
					$this->logResult('Success', $logMessage);
				}
			} catch (Exception $e) {
				$logMessage = "Couldn't send 'change multiple vacancy' related email to $recipientName. ";
				$logMessage .= '. Reason: ' . $e->getMessage();
				$this->logResult('Failure', $logMessage);
			}
		}
	}

	/**
	 * Return the Subject as per action
	 */
	private function getBody($mappingArray, $vacancyName){
		$body = "<strong>". $this->name. "</strong> has changed vacancies of ".count($mappingArray)." candidate(s) to <strong>". $vacancyName.
		"</strong>.<br>These candidates are as follows :<br><br><br>";
		$body .= '<table border="1" cellpadding="10" cellspacing="0"><th>Candidate Name</th><th>Previous Vacancy</th>';
		foreach ($mappingArray as $detail) {
			$body .= "<tr><td>".$detail['candidateName']."</td><td>".$detail['name']."</td></tr>";
		}
		$body .= "</table>";
		return $body;
	}
}