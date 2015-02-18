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

class PerformanceMailer extends orangehrmPerformanceMailer {
	
	protected $employeeService;
	protected $performanceReviewService;
	protected $logger;
    protected $review;
	
    public function  __construct() {
        parent::__construct();
        $this->logger = Logger::getLogger('performance.PerformanceMailer');
        $performerId = sfContext::getInstance()->getUser()->getEmployeeNumber();
        $this->performer = $this->getEmployeeService()->getEmployee($performerId);
            
    }       
    
   /*
    * Send email notification
    */
    public function sendNotifications($reviewId){           
        $performanceReviewService = $this->getPerformanceReviewService();
        $reviewArr = array('id' => $reviewId);
		$review = $performanceReviewService->readPerformanceReview($reviewArr);
		if(!empty($review)){
            $to = array();
            $name = array();
	    $receipient = array();
	    //Send seperate mail as mail body content diffeers - Add Review Notification
	    if(PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED == $review->getState() || PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED == $review->getState()
					|| PerformanceReview::PERFORMANCE_REVIEW_STATUS_APPROVED == $review->getState() || PerformanceReview::PERFORMANCE_REVIEW_STATUS_REJECTED == $review->getState()){
                $recipient = $review->getEmployee()->getEmpWorkEmail();
                if(trim($recipient) != "") {
                  array_push($to, $recipient);
                  array_push($name, $review->getEmployee()->getFirstName());
                }
	    }                     
            if (!empty($to)) {
                try {
                    $strTo = implode(',',$name); 
                    $this->message->setFrom($this->getSystemFrom());
                    $this->message->setTo($to);                    
                    $message = new PerformanceMailContent($review,$review->getEmployee(),true);
                    $this->message->setSubject($message->generateSubject());
                    $this->message->setBody($message->generateBody());                        
                    $this->mailer->send($this->message);
                    $logMessage = "Performance mail sent to $strTo.";
                    $this->logger->info($logMessage);
                    $this->logResult('Success', $logMessage);
                    
                    //send mail to reviewer
                    $this->sendToReviewer($review,$logger); 
                    
                } catch (Exception $e) {				    
                    $logMessage = "Couldn't send performace email to $strTo.";				    
                    $logMessage .= '. Reason: ' . $e->getMessage();
                    $this->logger->info($logMessage);
                    $this->logResult('Failure', $logMessage);
                }
            }

	}
    }
    public function sendToReviewer($review,$logger){
        
        $reviewers = $this->getRecipients($review);      
        foreach($reviewers as $reviewer){                        
            if ($reviewer instanceof Employee) {
                     $to=$reviewer->getEmpWorkEmail();
                    $name=$reviewer->getFirstName();
            }        
            if ($reviewer instanceof EmailSubscriber) {
                      $to=$reviewer->getEmail();
                     $name=$reviewer->getName();
            }     
                    
            $this->message->setFrom($this->getSystemFrom());
            $this->message->setTo($to);

            $message = new PerformanceMailContent($review,$reviewer,false);
            $this->message->setSubject($message->generateSubject());
            $this->message->setBody($message->generateBody());

            $this->mailer->send($this->message); 
               $logMessage = "Performance mail sent to $name($to)";
            $this->logger->info($logMessage);
            $this->logResult('Success', $logMessage);
        }
    }
            
	
    public function sendUpdatedNotifications($reviewId,$sendEmailReviewers){
        $logger = Logger::getLogger('performance.PerformanceMailer');
        $performanceReviewService = $this->getPerformanceReviewService();

        $reviewArr = array('id' => $reviewId);
        $review = $performanceReviewService->readPerformanceReview($reviewArr);
        
        $msgStr = "\nHi {receipientName},\n\n";
        $periodMonth = date("M", strtotime($review->getPeriodFrom()))."-".date("M", strtotime($review->getPeriodTo()));
        try{
            if(!empty($review)){
               //Now send mail to newly added reviewer
               foreach($sendEmailReviewers as $newReviewerId){
                    $receipients = array();
                    $newReviewer = $this->getEmployeeService()->getEmployee($newReviewerId); 
                    $to=$newReviewer->getEmpWorkEmail();
                    $name=$newReviewer->getFirstName();
                    $this->message->setFrom($this->getSystemFrom());
                    $this->message->setTo($to);                   
                    $message = new PerformanceMailContent($review,$newReviewer,null,1);
                    $this->message->setSubject($message->generateSubject());
                    $this->message->setBody($message->generateBody());                        
                    $this->mailer->send($this->message); 
                    $logMessage = "Performance mail sent to $name.";
                    $this->logger->info($logMessage);
                    $this->logResult('Success', $logMessage);
                }          
                
                //Now send mail to employee 
        
                $to=$review->getEmployee()->getEmpWorkEmail();
                $name=$review->getEmployee()->getFirstName();
                $this->message->setFrom($this->getSystemFrom());
                $this->message->setTo($to);

                $message = new PerformanceMailContent($review,$review->getEmployee(),true,1);
                $this->message->setSubject($message->generateSubject());
                $this->message->setBody($message->generateBody());

                $this->mailer->send($this->message); 
                $logMessage = "Performance mail sent to $name.";
                $this->logger->info($logMessage);
                $this->logResult('Success', $logMessage);
            }        
        } catch (Exception $e) {				    
                    $logMessage = "Couldn't send performace email to $strTo.";				    
                    $logMessage .= '. Reason: ' . $e->getMessage();
                    $this->logger->info($logMessage);
                    $this->logResult('Failure', $logMessage);
        }       
    }
    /**
    * get the receipients list.
    */
    private function getRecipients($review){
       $recipients = array();
       //Get the reviewer list
       foreach ($review->getReviewers() as $reviewer ) {
            if($reviewer->getReviewerId() != $_SESSION['empNumber']) {
                $recipients[]=$reviewer->getReviewer();
            }
       }

       //Get the Subscriber list
       $mailNotificationService = new EmailNotificationService();
       $subscriptions = $mailNotificationService->getSubscribersByNotificationId(EmailNotification::PERFORMANCE_SUBMISSION);
       foreach ($subscriptions as $subscription) {
               if ($subscription->getEmailNotification()->getIsEnable() == EmailNotification::ENABLED) {

                $recipients[]=$subscription;
               }
       }
       return $recipients;
    }
    
    /**
     * Get Job Service
     */
    public function getPerformanceReviewService() {
        $this->performanceReviewService = new PerformanceReviewService();
        $this->performanceReviewService->setPerformanceReviewDao(new PerformanceReviewDao());
        return $this->performanceReviewService;
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
    
    public function sendRejectCommentNotififcation($performanceReview) {
        if (!empty($this->mailer)) {
            $adminUsers = array();
            $adminUsers = $this->getSystemUserService()->getAdminSystemUsers();
            foreach ($adminUsers as $to) {
                $recipient = $to->getEmployee();
                if(trim($recipient->getEmpWorkEmail()) != "") {                  	
                    try {
                            $this->message->setFrom($this->getSystemFrom());
                            $this->message->setTo($recipient->getEmpWorkEmail());
                             $message = new PerformanceMailContent($performanceReview,$recipient,false,null,true);
                             $this->message->setSubject($message->generateSubject());
                             $this->message->setBody($message->generateBody());                            
                            $this->mailer->send($this->message);
                            $logMessage = "Appraisal related mail has been sent to  ".$recipient->getFirstAndLastNames().". ";
                            $this->logResult('Success', $logMessage);
                        } catch (Exception $e) {
                            $logMessage = "Couldn't send appraisal related email to ".$recipient->getFirstAndLastNames().". ";
                            $logMessage .= '. Reason: ' . $e->getMessage();
                            $this->logResult('Failure', $logMessage);
                        }
                }
            }


        }
     
   }
    
}