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

class PimMailer extends orangehrmMailer {
	protected $systemUserService;
    protected $changedBy;
	//protected $logger;
	
	public function getSystemUserService() {
		if (empty($this->systemUserService)) {
			$this->systemUserService = new SystemUserService();
		}
		return $this->systemUserService;
	}
	
	public function  __construct($changedBy='employee') {
        $this->changedBy=$changedBy;
		parent::__construct();
	}
	
	public function send($name, $id, $message) {
		if (!empty($this->mailer)) {
				$this->sendEmailNotification($name, $id, $message);
		}
	}
	
	/**
	 * Send email notification
	 */
	public function sendEmailNotification($name, $id, $messageHTML){
        if($this->changedBy=='admin')
        $subject = "[HRMS-PIM] : Admin has changed ".$name." (".$id.")'s information";
        else 
		$subject = "[HRMS-PIM] : ".$name." (".$id.") has changed information";
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
					$this->message->setBody($this->getBody($name, $id, $messageHTML), 'text/html');
					$this->mailer->send($this->message);
					$logMessage = "Pim related mail has been sent to  $recipientName. ";
					$this->logResult('Success', $logMessage);
				}
			} catch (Exception $e) {
				$logMessage = "Couldn't send Pim related email to $recipientName. ";
				$logMessage .= '. Reason: ' . $e->getMessage();
				$this->logResult('Failure', $logMessage);
			}   
		}
	}
	
	
	/**
	 * Return the Subject as per action
	 */
	private function getBody($name, $id, $messageHTML){
        if($this->changedBy=='admin')
        $body = '<br>Admin has changed '.$name. '\'s information.';
        else    
		$body = '<br>'.$name. ' has changed following information.';
		$body .= '<center><table border="1" cellpadding="10" cellspacing="0"><th>Fields</th><th>Previous Value</th><th>New Value</th>';
		$body .= $messageHTML."</table><center>";
		return $body;
	}
}