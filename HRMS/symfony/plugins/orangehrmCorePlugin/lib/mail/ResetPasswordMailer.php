<?php

/**
 *
* OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
* all the essential functionalities required for any enterprise.
* Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
*
* OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
* the GNU General Public License as published by the Free Software Foundation; either
* version 2 of the License, or (at your option) any later version.
*
* OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
* without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
* See the GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License along with this program;
* if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
* Boston, MA  02110-1301, USA
*
*/

class ResetPasswordMailer extends orangehrmMailer {

	protected $action;
	protected $leaveCompOff;
	protected $interviewerName;
	protected $selectedInterviewerArrayList;
	protected $employeeService;
	//protected $logger;

	public function  __construct() {
		parent::__construct();
	}

	public function send($to, $key) {
		if (!empty($this->mailer)) {
			$date = date('Y-m-d');
			//$stamp = $date . " " . date('H:i:s');
			$url = (empty($_SERVER['HTTPS']) OR $_SERVER['HTTPS'] === 'off') ? 'http://' : 'https://';
			$url .= $_SERVER['HTTP_HOST'];
			
			$body = "hi,\n\n\tYou have requested to reset password for Synerzip HRMS on ". $date . ",\nTo complete the action Click on link given below:\n";
			//$body .= "\nhttp://localhost/orangehrm-3.0.1/symfony/web/index.php/auth/resetPassword?rk=".$key;
			$body .= $url. "/symfony/web/index.php/auth/resetPassword?rk=".$key;
			$body.= "\n\n\nNote: This link will be Expired in 24-Hours";
			$subject = "SynerzipHRMS - Reset Password Link";
			$this->sendEmailNotification($to,$subject,$body);
		}
	}

	/**
	 * Send email notification
	 */
	public function sendEmailNotification($to, $subject, $body){
		try{
			$this->message->setFrom($this->getSystemFrom());
			$this->message->setTo($to);
			$this->message->setSubject($subject);
			$this->message->setBody($body);
			$this->mailer->send($this->message);
			$logMessage = "Send Password reset link email to $to";
			$this->logResult('Success', $logMessage);
		} catch (Exception $e) {
			$logMessage = "Couldn't send Password reset link to $to";
			$logMessage .= '. Reason: ' . $e->getMessage();
			$this->logResult('Failure', $logMessage);
		}
	}

	/**
	 * Returns the employee service
	 */
	public function getEmployeeService() {
		if (is_null($this->employeeService)) {
			$this->employeeService = new EmployeeService();
			$this->employeeService->setEmployeeDao(new EmployeeDao());
		}
		return $this->employeeService;
	}

}
?>