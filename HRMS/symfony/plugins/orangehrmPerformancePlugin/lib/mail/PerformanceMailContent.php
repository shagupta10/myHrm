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

class PerformanceMailContent extends orangehrmPerformanceMailContent {

    public function getSubjectTemplate() {
            
        if (empty($this->subjectTemplate)) {

            $this->subjectTemplate = trim( $this->__getSubject($this->review,$this->mailToEmployee,$this->reviewUpdated,$this->rejectComment));

        }
        return $this->subjectTemplate;
    }

   public function getSubjectReplacements() {

        if (empty($this->subjectReplacements)) {
            //add code for replacement

        }
        return $this->subjectReplacements;        
    }

    public function getBodyTemplate() {
        if (empty($this->bodyTemplate)) {
            $this->bodyTemplate = $this->__getBody($this->review, $this->mailToEmployee,$this->reviewUpdated,$this->rejectComment);
        }
        return $this->bodyTemplate;
    }

    public function getBodyReplacements() {

        if (empty($this->bodyReplacements)) {

            $this->bodyReplacements = array('recipientFirstName' => $this->replacements['recipientFirstName'],    
                                             'reviewUrl'    => $this->replacements['reviewUrl'],
                                        //    'synerzipHRMSite' => $this->replacements['synerzipHRMSite'],
                                         //   'synerzipHRMVacancySite' => $this->replacements['synerzipHRMVacancySite']
                                            );

        }
        return $this->bodyReplacements;        
    }
    
    private function __getSubject($review,$mailToEmployee=false,$reviewUpdated=null,$rejectComment=false){   
       if($reviewUpdated==null) {
            $state = $review->getState();
            $subject = "Appraisal Notification (".$review->getEmployee()->getFirstAndLastNames().") - ";
            switch ($state) {
                    case PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED:
                            $subject.=  "You have been assigned a new Performance Review ";
                    break;
                    case PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED:
                            if($rejectComment)
                            $subject.= "Appraisal Notification (".$review->getEmployee()->getFirstAndLastNames().") - Declined appraisal.";    
                            else
                            $subject.= "Performance Review submitted";
                    break;
                    case PerformanceReview::PERFORMANCE_REVIEW_STATUS_REJECTED:
                            $subject.= "Performance Review rejected";
                    break;
                    case PerformanceReview::PERFORMANCE_REVIEW_STATUS_APPROVED:
                            $subject.= "Performance Review approved";
                    break;
                    case PerformanceReview::PERFORMANCE_REVIEW_STATUS_BEING_REVIWED:
                            $subject.= "Self Review submitted";
                    break;
            }
        } else {
            if($mailToEmployee){
             $periodMonth = date("M", strtotime($review->getPeriodFrom()))."-".date("M", strtotime($review->getPeriodTo()));
             $subject = "Updated Performance Reviewer for the period of ".$periodMonth;
            } else {
             $subject= "Updated Performance Reviewer - You are added as the reviewer.";  
            }
        }
               
	return $subject;
    }
	
    /**
     * Return the body as per action
     */
    private function __getBody($review, $mailToEmployee=false,$reviewUpdated=null,$rejectComment=false){    
        $body = "Hi %recipientFirstName%,\n\n";
        if($reviewUpdated==null){
            $state = $review->getState();
            switch ($state) {
                case PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED:
                        if($mailToEmployee){
                                $periodMonth = date("M", strtotime($review->getPeriodFrom()))."-".date("M", strtotime($review->getPeriodTo()));
                                $body.=  "Performance review for the period of ".$periodMonth. " assigned to you. Please see below the details of it .\r\n\n";
                                $body.= " Reviewer :" .$this->getReviewerNames($review)."\t\n";
                        }else{ 
                                $body.=  "You are added as the reviewer of following the performance review.\r\n\n";
                                $body.= " Employee : ". $review->getEmployee()->getFirstAndLastNames() ."\t\n";
                        }
                        $body.= " Period: ".set_datepicker_date_format($review->getPeriodFrom())." - ".set_datepicker_date_format($review->getPeriodTo()) ."\t\n";
                        $body.= " Due Date: ".set_datepicker_date_format($review->getDueDate()) ."\t\n";
                        $body.= " Review link: %reviewUrl% \r\n\n";
                        $body.= "You can locate the review by logging in and navigating to Performance > Search Review\r\n\n";
                break;

                case PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED:
                        if($mailToEmployee){
                                $body.= $this->getEmployeeName()." has submitted your performance review. ";
                        }else{
                             if($rejectComment) 
                               $body.= $this->_getRejectCommentBody($review);  
                             else    
                               $body.= $this->getEmployeeName()." has submitted performance review of ". $review->getEmployee()->getFirstAndLastNames().". ";                
                        }
                        $body.= "Below is the link for the same-\r\n";
                        $body.= " %reviewUrl% \r\n\n";
                break;

                case PerformanceReview::PERFORMANCE_REVIEW_STATUS_REJECTED:
                        if($mailToEmployee){
                                $body.= $this->getEmployeeName()." has rejected your performance review. ";
                        }else{
                        $body.= $this->getEmployeeName()." has rejected performance review of ". $review->getEmployee()->getFirstAndLastNames().". ";
                        }
                        $body.= "Below is the link for the same\r\n";
                        $body.= " %reviewUrl% \r\n\n";
                        $body.= "You can again submit the review after making changes.\r\n\n";
                break;

                case PerformanceReview::PERFORMANCE_REVIEW_STATUS_APPROVED:
                        if($mailToEmployee){
                                $body.= $this->getEmployeeName()." has approved your performance review";
                        }else{
                                $body.= $this->getEmployeeName()." has approved performance review of ".$review->getEmployee()->getFirstAndLastNames();
                        } 
                        $body.=	". Below is the link for the same\r\n";
                        $body.= " %reviewUrl% \r\n\n";
                break;

                case PerformanceReview::PERFORMANCE_REVIEW_STATUS_BEING_REVIWED:
                        $body.= $this->getEmployeeName()." has self reviewed performance review. Below is the link for the same\r\n";
                        $body.= " %reviewUrl% \r\n\n";
                break;
            }
        }
        else{ 
                $periodMonth = date("M", strtotime($review->getPeriodFrom()))."-".date("M", strtotime($review->getPeriodTo()));
                $body .=  "Performance reviewer(s) for the Employee - ". $review->getEmployee()->getFirstAndLastNames() . " are updated. Please see below the details of it .\r\n\n";                        
                $body.= " Reviewer: " .$this->getReviewerNames($review)."\t\n";
                $body.= " Period: ".set_datepicker_date_format($review->getPeriodFrom())." - ".set_datepicker_date_format($review->getPeriodTo()) ."\t\n";
                $body.= " Due Date: ".set_datepicker_date_format($review->getDueDate()) ."\t\n";
                $body.= " Review link:  %reviewUrl% \r\n\n";
        }

        $body .= "Thank You\t\n";
        $body .= "SynerzipHRM\t\n\n";
        $body .="This is an automated notification. Please do not reply";
        return $body;
    } 
	
    /**
    * get the mailer list
    */
    protected function getMailer($recreate = false) {		
            if (empty($this->mailer) || $recreate) {
                    /*Modified by Sujata */
                    $spool = new Swift_FileSpool(sfConfig::get('sf_app_dir') .DIRECTORY_SEPARATOR. "spool");
                    $transport = Swift_SpoolTransport::newInstance($spool);
                    if (!empty($transport)) {
                            $mailer = Swift_Mailer::newInstance($transport);				
                    } else {
                            $this->logger->warn('Email configuration settings not available');
                    }
            }		
            return $mailer;
    }
	
    /**
     * Return Employee Name
     */
    protected function getEmployeeName(){
            $emp = $this->getEmployeeService()->getEmployee($_SESSION['empNumber']);
            if(!empty($emp)){
                    return $emp->getFirstAndLastNames();
            }
            return;
    }
    /**	  
     * Return the reviewer names
     * @param $performanceReview
     */
    protected function getReviewerNames(PerformanceReview $performanceReview){
            $performanceReviewerList = $performanceReview->getReviewers();
            $reviewerName = "";
            foreach ($performanceReviewerList as $reviewer){
                    $reviewerName.= ($reviewerName != "") ? ", ".$reviewer->getReviewer()->getFirstAndLastNames(): $reviewer->getReviewer()->getFirstAndLastNames();
            }
            return $reviewerName;
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
    
    protected function _getRejectCommentBody($performanceReview){        
        $body = $performanceReview->getEmployee()->getFirstAndLastNames()." has expressed discrepancy on Appraisal.\n\nDetails : ";
        $body .="\nPrimary reviewer : ".$performanceReview->getPrimaryReviewer()->getReviewer()->getFirstAndLastNames();
        $secondaryReviewers = $performanceReview->getSecondaryReviewers();
        $array = array();
        foreach ($secondaryReviewers as $secondaryReviewer) {
                array_push($array, $secondaryReviewer->getReviewer()->getFirstAndLastNames());
        }
        $body .="\nSecondary reviewer : ".implode(', ', $array)."\n";
        $body .="\nComments : ".$performanceReview->getRejectComments()."\t\n";       
        return $body;
    }
}
