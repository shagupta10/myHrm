<?php
/**
 *
 * @param  NULL
 * @return NULL
 * @author Mayur V. Kathale<mayur.kathale@gmail.com>
 */
class EmailDigestMailer extends orangehrmMailer {
	private $vacancyName;
	private $managerEmp;
	private $detail;
	private $microResumes;
	private $adminEmailArray;
	private $hmEmailArray;
	public function  __construct($vacancyName, $detail, $microResumes, $hmEmailArray, $adminEmailArray) {
		parent::__construct();
		$this->vacancyName = $vacancyName;
		$this->managerEmp = $managerEmp;
		$this->detail = $detail;
		$this->microResumes = $microResumes;
		$this->adminEmailArray = $adminEmailArray;
		$this->hmEmailArray = $hmEmailArray;
		$this->sendEmail();
	}

	/**
	 * Send email notification
	 */
	public function sendEmail() {
			$subject = "SynerzipHRM - Recruitment Digest for $this->vacancyName";
			$content = new EmailDigestContent($this->vacancyName, $this->managerEmp, $this->detail, $this->microResumes);
			$this->message->setFrom($this->getSystemFrom());
			$this->message->setTo($this->hmEmailArray);
			$this->message->setCc($this->adminEmailArray);
			$this->message->setSubject($subject);
			$this->message->setBody($content->getBody(),'text/html');
			$this->message->setContentType("text/html");
	}
}