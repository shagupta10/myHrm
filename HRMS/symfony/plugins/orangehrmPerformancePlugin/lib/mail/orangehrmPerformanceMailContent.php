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

abstract class orangehrmPerformanceMailContent {

    protected $subjectTemplate;
    protected $subjectReplacements = array();
    protected $bodyTemplate;
    protected $bodyReplacements = array();
    protected $performer; // Type of Employee
    protected $recipient; // Type of Employee
    protected $employee;
    protected $review;
    protected $mailToEmployee;
    protected $reviewUpdated=null;
    protected $rejectComment;
    protected $templateDirectoryPath;
    protected $replacements = array('performerFirstName' => 'System Administrator',
                                    'performerFullName' => 'System Administrator',
                                    'recipientFirstName' => 'All',
                                    'recipientFullName' => ''
                                    );

    /* ========== Start of getters and setters ========== */

    public function setSubjectTemplate($subjectTemplate) {
        $this->subjectTemplate = $subjectTemplate;
    }

    public function setSubjectReplacements($subjectReplacements) {
        $this->subjectReplacements = $subjectReplacements;
    }

    public function setBodyTemplate($bodyTemplate) {
        $this->bodyTemplate = $bodyTemplate;
    }

    public function setBodyReplacements($bodyReplacements) {
        $this->bodyReplacements = $bodyReplacements;
    }

 
    public function getPerformer() {
        return $this->performer;
    }

    public function setPerformer($performer) {
        $this->performer = $performer;
    }

    public function getRecipient() {
        return $this->recipient;
    }

    public function setRecipient($recipient) {
        $this->recipient = $recipient;
    }
  
    public function getTemplateDirectoryPath() {
        return $this->templateDirectoryPath;
    }

    public function setTemplateDirectoryPath($templateDirectoryPath) {
        $this->templateDirectoryPath = $templateDirectoryPath;
    }

    public function getRequestType() {
        return $this->requestType;
    }

    public function setRequestType($requestType) {
        $this->requestType = $requestType;
    }

    public function getReplacements() {
        return $this->replacements;
    }

    public function setReplacements($replacements) {
        $this->replacements = $replacements;
    }

    /* ========== End of getters and setters ========== */

    public function  __construct($review,$recipient, $mailToEmployee=false,$reviewUpdated=null,$rejectComment=false) {

        $this->review = $review;
       // $this->performer = $performer;
       // $this->recipient = $recipient;
        $this->mailToEmployee = $mailToEmployee;     
      
        $this->recipient= $recipient;
        $this->reviewUpdated=$reviewUpdated;
        $this->rejectComment=$rejectComment;
       
        // TODO: Pass template path as a parameter
        $directoryPathBase = sfConfig::get('sf_root_dir')."/plugins/orangehrmPerformancePlugin/modules/performance/templates/mail/";
        $this->templateDirectoryPath = $directoryPathBase . 'en_US/';
       /* $culture = $this->getCulture($review); //enable this to maintain email content in txt file
        
        if (file_exists($directoryPathBase . $culture . '/')) {
            $this->templateDirectoryPath = $directoryPathBase . $culture . '/';
        }*/
        
        $this->populateReplacements();
        $this->_populateReviewUrl();

    }
    
   

    public function populateReplacements() {

        if ($this->performer instanceof Employee) {
            $this->replacements['performerFirstName'] = $this->performer->getFirstName();
            $this->replacements['performerFullName'] = $this->performer->getFirstAndLastNames();
            $this->replacements['performerEmail'] = $this->performer->getEmpWorkEmail();
        }

        if ($this->recipient instanceof Employee) {
            $this->replacements['recipientFirstName'] = $this->recipient->getFirstName();
            $this->replacements['recipientFullName'] = $this->recipient->getFirstAndLastNames();
        }
         if ($this->recipient instanceof EmailSubscriber) {
            $this->replacements['recipientFirstName'] = $this->recipient->getName();
           
        }
   
    }

    
    
    protected function _populateHRMSiteAddress(){
	   // add code to populate HRMS site address in mail body
    }
    
    protected function _populateReviewUrl(){
    	$url = (empty($_SERVER['HTTPS']) OR $_SERVER['HTTPS'] === 'off') ? 'http://' : 'https://';
    	
    	$page = (strtotime($this->review->getPeriodFrom()) < strtotime(PerformanceReview::KPI_GENERAL_DATE))? 'performanceReview':'reviewPerformance';
    	
        $url .= $_SERVER['HTTP_HOST']."/symfony/web/index.php/performance/".$page."/id/".$this->review->getId();
    	$this->replacements['reviewUrl']= $url;
    }
   
    public function generateSubject() {
        return $this->replaceContent($this->getSubjectTemplate(), $this->getSubjectReplacements());
    }
    
    public function generateBody() {
        return $this->replaceContent($this->getBodyTemplate(), $this->getBodyReplacements());
    }
    
    public function replaceContent($template, $replacements, $wrapper = '%') {

        $keys = array_keys($replacements);

        foreach ($keys as $value) {
            $needls[] = $wrapper . $value . $wrapper;
        }

        return str_replace($needls, $replacements, $template);
    }
    public function logResult($type = '', $logMessage = '') {
		$logPath = ROOT_PATH . '/lib/logs/email_content.log';
        if (file_exists($logPath) && !is_writable($logPath)) {
            throw new Exception("Email Notifications : Log file is not writable");
        }

        $message = '========== Message Begins ==========';
        $message .= "\r\n\n";
        $message .= 'Time : '.date("F j, Y, g:i a");
        $message .= "\r\n";
        $message .= 'Message Type : '.$type;
        $message .= "\r\n";
        $message .= 'Message : '.$logMessage;
        $message .= "\r\n\n";
        $message .= '========== Message Ends ==========';
        $message .= "\r\n\n";

        file_put_contents($logPath, $message, FILE_APPEND);

    }
    
    abstract function getSubjectTemplate();
    abstract function getSubjectReplacements();
    abstract function getBodyTemplate();
    abstract function getBodyReplacements();
    
   
}
