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

class InterviewMailer extends orangehrmRecruitmentMailer {

	protected $action;
	protected $jobInterview;
	protected $interviewerName;
	protected $selectedInterviewerArrayList;
	
    public function  __construct($performerId, $candidateId, $vacancyId, $action, $jobInterview, $selectedInterviewerArrayList) {

        parent::__construct();

        $this->performer = $this->getEmployeeService()->getEmployee($performerId);
        $this->candidate = $this->getCandidateService()->getCandidateById($candidateId);
        $this->vacancy = $this->getVacancyService()->getVacancyById($vacancyId);
        $this->action = $action;
        $this->jobInterview = $jobInterview;
        $this->selectedInterviewerArrayList = $selectedInterviewerArrayList;
       
        if (!empty($this->selectedInterviewerArrayList)) {
	        for ($i = 0; $i < count($this->selectedInterviewerArrayList); $i++) {
		        $interviewer = $this->getEmployeeService()->getEmployee($selectedInterviewerArrayList[$i]);
		        if (!empty($interviewer)) {
			        if(empty($this->interviewerName)){
				        $this->interviewerName = $interviewer->getFirstAndLastNames();
			        }else{
				        $this->interviewerName .= ", ".$interviewer->getFirstAndLastNames();
			        }
		        }
	        }
        }
       
    }
    
    public function send() {
	    if (!empty($this->mailer)) {
	    	//Temp soln
	    	set_time_limit(60);
	    	$this->sendToAdmin();
		    if($this->action != WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_ATTACH_VACANCY) {
		    	$this->sendToHiringManager();
		    }
		    
		    if (!empty($this->selectedInterviewerArrayList)) {
			    for ($i = 0; $i < count($this->selectedInterviewerArrayList); $i++) {
				    $this->sendToInterviewer($this->selectedInterviewerArrayList[$i]);
			    }
		    }
	    }
    }
    

    public function sendToAdmin() {
	    
	    $roleList = array(SystemUser::ADMIN_USER_ROLE_ID, SystemUser::RECRUITMENT_MANAGER_ROLE_ID);
	    $adminUsers = $this->getSystemUserService()->getUsersByRoles($roleList);
	    
	    if (count($adminUsers) > 0) {
		    foreach ($adminUsers as $admin) {
			    $recipient = $admin->getEmployee();
			    if (!empty($recipient)) {
				    try {
				    	$message = new InterviewMailContent($this->performer, $recipient, $this->candidate, $this->vacancy,$this->action,$this->jobInterview, $this->interviewerName);
					    if (!empty($this->selectedInterviewerArrayList) && $this->action == WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_INTERVIEW) {
					    	 $message->scheduleMeeting();
					    }else{
						    $recipientName = $recipient->getEmpWorkEmail();
						    $this->message->setFrom($this->getSystemFrom());
						    $this->message->setTo($recipientName); 
						    $this->message->setSubject($message->generateSubject());
						    $this->message->setBody($message->generateBody(), 'text/html');
						    $this->mailer->send($this->message);
					    }
					    
					    $logMessage = "Interview related mail to  $recipientName.  Action taken : $this->action ";
					    $this->logResult('Success', $logMessage);
				    } catch (Exception $e) {
					    
					    $logMessage = "Couldn't send interview related email to $recipientName. Action taken : $this->action";
					    $logMessage .= '. Reason: ' . $e->getMessage();
					    $this->logResult('Failure', $logMessage);
				    }
			    }
		    }
	    }
    }
   
    public function sendToHiringManager() {
	    $recipients = $this->vacancy->getAllJobHiringManagers();
	    if (is_array($recipients) && (count($recipients)>0)) {
		    try {
			    foreach ( $recipients as $recipient) {
				    $message = new InterviewMailContent($this->performer, $recipient, $this->candidate, $this->vacancy,$this->action,$this->jobInterview, $this->interviewerName);
				    if (!empty($this->selectedInterviewerArrayList) && $this->action == WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_INTERVIEW) {
					    $message->scheduleMeeting();
					    $logMessage = "Interview related mail to  $recipientName.  Action taken : $this->action ";
					    $this->logResult('Success', $logMessage);
				    }else{  
					    $recipientName = $recipient->getEmpWorkEmail();
					    $this->message->setFrom($this->getSystemFrom());
					    $this->message->setTo($recipientName);
					    $this->message->setSubject($message->generateSubject());
					    $this->message->setBody($message->generateBody(), 'text/html');
					    $this->mailer->send($this->message);
					    
					    $logMessage = "Interview related mail to  $recipientName.  Action taken : $this->action ";
					    $this->logResult('Success', $logMessage);
					    
				    }
			    }
		    } catch (Exception $e) {
			    
			    $logMessage = "Couldn't send interview related email to $recipientName. Action taken : $this->action";
			    $logMessage .= '. Reason: ' . $e->getMessage();
			    $this->logResult('Failure', $logMessage);
		    }
	    }
    }

   
    
    public function sendToInterviewer($interviewerId) {
	    if (!empty($this->mailer)) {
		    $recipient = $this->getEmployeeService()->getEmployee($interviewerId);
		    if (!empty($recipient)) {
			    try {
			    	$message = new InterviewMailContent($this->performer, $recipient, $this->candidate, $this->vacancy,$this->action,$this->jobInterview, $this->interviewerName);
			    	if (!empty($this->selectedInterviewerArrayList) && $this->action == WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_INTERVIEW) {
				    	$message->scheduleMeeting();
			    	}else{
				    	$recipientName = $recipient->getEmpWorkEmail();
				    	$this->message->setFrom($this->getSystemFrom());
				    	$this->message->setTo($recipientName);
				    	$this->message->setSubject($message->generateSubject());
				    	$this->message->setBody($message->generateBody());
				    	$this->mailer->send($this->message);
			    	}
				    $logMessage = "Interview related mail to  $recipientName.  Action taken : $this->action ";
				    $this->logResult('Success', $logMessage);
			    } catch (Exception $e) {
				    
				    $logMessage = "Couldn't send interview related semail to $recipientName. Action taken : $this->action";
				    $logMessage .= '. Reason: ' . $e->getMessage();
				    $this->logResult('Failure', $logMessage);
			    }
		    }
	    }
    }
    
}
