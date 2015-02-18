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

class LeaveCompOffMailer extends orangehrmMailer {
	
	protected $action;
	protected $leaveCompOff;
	protected $interviewerName;
	protected $selectedInterviewerArrayList;
	protected $employeeService;
	//protected $logger;
	
	public function  __construct() {
		parent::__construct();
	}
	
	public function send($performerId,$action, $leaveCompOff) {
		if (!empty($this->mailer)) {
			$performer = $this->getEmployeeService()->getEmployee($performerId); 
			if(!is_null($performer)){
				//$this->logger = Logger::getLogger('mail.LeaveCompOffMailer');
				if ($_SESSION['empNumber'] == $leaveCompOff->getEmpNumber()){
					$supervisors = $performer->getSupervisors();
					if (count($supervisors) > 0) {
						foreach ($supervisors as $supervisor) {
							$to = $supervisor->getEmpWorkEmail();
							//$this->logger->error('mail.LeaveCompOffMailer: Self Receipent ' . $to);
							if (!empty($to)) {
								$subject = $this->getSubject($action,$performer,$leaveCompOff,true);
								$body = $this->getBody($action,$performer,$supervisor->getFirstName(), $leaveCompOff,true);
								$this->sendEmailNotification($to,$subject,$body);
							}
						}
					}
				}else{
					$subordinate = $this->getEmployeeService()->getEmployee($leaveCompOff->getEmpNumber()); 
					$to = $subordinate->getEmpWorkEmail();
					//$this->logger->error('mail.LeaveCompOffMailer: $subordinate' . $to);
					if (!empty($to)) {
						$subject = $this->getSubject($action,$performer,$leaveCompOff,false);
						$body = $this->getBody($action,$performer,$subordinate->getFirstName(), $leaveCompOff,false);
						$this->sendEmailNotification($to,$subject,$body);
					}
				}
			}
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
			$logMessage = "Send Compoff related email to $to";
			$this->logResult('Success', $logMessage);
		} catch (Exception $e) {
			$logMessage = "Couldn't send Compoff related email to $to";
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
	
	
	/**
	 * Return the Subject as per action
	 */
	private function getSubject($action, $performer, $leaveCompoff, $self){
		$subject = "Comp Off Notification - ";
		switch ($action) {
			case Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL://Pending Approval
				$subject.=  $performer->getFirstAndLastNames()." Applied for ".$leaveCompoff->getNumberOfDays()." Day(s) of Comp Off";
			break;
			case Leave::LEAVE_STATUS_LEAVE_APPROVED: //Approved
				$subject.= $performer->getFirstAndLastNames()." Has Approved Your Comp Off Leave";
			break;
			case Leave::LEAVE_STATUS_LEAVE_REJECTED://Rejected
				$subject.= $performer->getFirstAndLastNames()." Has Rejected Your Comp Off Leave";
			break;
			case Leave::LEAVE_STATUS_LEAVE_CANCELLED://Leave Cancelled
				if($self){
					$subject.= $performer->getFirstAndLastNames()." Has Cancelled Comp Off";
				}else{
					$subject.= $performer->getFirstAndLastNames()." Has Cancelled Your Comp Off";	
				}
			break;
		}
		return $subject;
	}
	
	/**
	 * Return the body as per action
	 */
	private function getBody($action, $performer,$recipientFirstName, $leaveCompoff, $self){
		$body = "Hi ".$recipientFirstName.",\n";
		switch ($action) {
			case Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL://Pending Approval
				$body .= $performer->getFirstAndLastNames()." has applied for Comp Off. The Comp Off details:\n\n";
				$body .= $this->getCompOffDetails($leaveCompoff);
				$body .= "You were sent this mail, as you are a supervisor assigned for ".$performer->getFirstName().".\n";
			break;
			case Leave::LEAVE_STATUS_LEAVE_APPROVED: //Approved
				$body .= $performer->getFirstAndLastNames()." has approved your applied Comp Offs:\n\n";
				$body .= $this->getCompOffDetails($leaveCompoff);
			break;
			case Leave::LEAVE_STATUS_LEAVE_REJECTED://Rejected
				$body .= $performer->getFirstAndLastNames()." has rejected your Comp Off. The Comp Off details:\n\n";
				$body .= $this->getCompOffDetails($leaveCompoff);
			break;
			case Leave::LEAVE_STATUS_LEAVE_CANCELLED://Leave Cancelled
				if($self){
					$body .= $performer->getFirstAndLastNames()." has cancelled Comp Off. The Comp Off details:\n\n";	
				}else{
					$body .= $performer->getFirstAndLastNames()." has cancelled your Comp Off. The Comp Off details:\n\n";
				}
				$body .= $this->getCompOffDetails($leaveCompoff);
			break;
		}
		$body .= "Thank You\n\n";
		$body .="This is an automated notification.\n";
		return $body;
	}
	
	private function getCompOffDetails($leaveCompoff){
		$compOffDetails = "Number Of Day(s)       CompOff Details \n";
		$compOffDetails .="================================================\n";
		$compOffDetails .= $leaveCompoff->getNumberOfDays()."           " .$leaveCompoff->getCompoffDetails().  "\n\n";
		return $compOffDetails;
	}
	
	
	
	
}