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

class AddCandidateMailer extends orangehrmRecruitmentMailer {

    public function  __construct($performerId, $candidateId, $vacancyId) {

        parent::__construct();

        $this->performer = $this->getEmployeeService()->getEmployee($performerId);
        $this->candidate = $this->getCandidateService()->getCandidateById($candidateId);
        $this->vacancy = $this->getVacancyService()->getVacancyById($vacancyId);
    }

    public function sendToAdmin() {
        $logger = Logger::getLogger('recruitment.AddCandidateMailer');
    	$roleList = array(SystemUser::ADMIN_USER_ROLE_ID, SystemUser::RECRUITMENT_MANAGER_ROLE_ID);
	    $adminUsers = $this->getSystemUserService()->getUsersByRoles($roleList);
        $to = array();
        $name = array();
        if (count($adminUsers) > 0) {
            foreach ($adminUsers as $admin) {
                $recipient = $admin->getEmployee();
                if(trim($recipient->getEmpWorkEmail()) != "") {
                    array_push($to, $recipient->getEmpWorkEmail());
                    array_push($name, $recipient->getFirstName());
                }
            }
            if (!empty($to)) {
                try {
                    $strTo = implode(',',$name); 
                    $this->message->setFrom($this->getSystemFrom());
                    $this->message->setTo($to);
                    
                    $message = new AddCandidateMailContent($this->performer, $to, $this->candidate, $this->vacancy);
                    $this->message->setSubject($message->generateSubject());
                    $this->message->setBody($message->generateBody());
                        
                    $this->mailer->send($this->message);
                        
                    $logMessage = "Candidate registration email was sent to $strTo.";
                    $logger->info($logMessage);
                    $this->logResult('Success', $logMessage);
                } catch (Exception $e) {				    
                    $logMessage = "Couldn't send candidate registration subscription email to $strTo.";				    
                    $logMessage .= '. Reason: ' . $e->getMessage();
                    $logger->info($logMessage);
                    $this->logResult('Failure', $logMessage);
                }
            }
	    }
    }

    public function sendToHiringManager() {
	    $recipient = $this->vacancy->getAllJobHiringManagers();
	    if (is_array($recipient) && (count($recipient)>0)) {
		    for($kk=0;$kk<count($recipient);$kk++)
		    {
			    try {
				    
				    $recipientName = $recipient[$kk]->getEmpWorkEmail();
				    $this->message->setFrom($this->getSystemFrom());
				    $this->message->setTo($recipientName);
				    
				    $message = new AddCandidateMailContent($this->performer, $recipient[$kk], $this->candidate, $this->vacancy);
				    
				    $this->message->setSubject($message->generateSubject());
				    $this->message->setBody($message->generateBody());
				    
				    $this->mailer->send($this->message);
				    
				    $logMessage = "Candidate registration email was sent to $recipientName";
				    $this->logResult('Success', $logMessage);
			    } catch (Exception $e) {
				    
				    $logMessage = "Couldn't send candidate registration subscription email to $recipientName";
				    $logMessage .= '. Reason: ' . $e->getMessage();
				    $this->logResult('Failure', $logMessage);
			    }
		    }
	    }
	    
    }
   
    public function send() {

        if (!empty($this->mailer)) {
        	//Temp soln
	    	set_time_limit(60);
        	$this->sendToAdmin();
        }
    }

}
