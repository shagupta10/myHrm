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

abstract class orangehrmRecruitmentMailContent extends orangehrmMailContent {

    protected $subjectTemplate;
    protected $subjectReplacements = array();
    protected $bodyTemplate;
    protected $bodyReplacements = array();
    protected $performer; // Type of Employee
    protected $recipient; // Type of Employee
    protected $candidate;
    protected $vacancy;
   
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

    public function  __construct($performer, $recipient, $candidate, $vacancy) {

        $this->performer = $performer;
        $this->recipient = $recipient;
       	$this->candidate=  $candidate;
       	$this->vacancy = $vacancy;
       
        // TODO: Pass template path as a parameter
        $directoryPathBase = sfConfig::get('sf_root_dir')."/plugins/orangehrmRecruitmentPlugin/modules/recruitment/templates/mail/";
        $this->templateDirectoryPath = $directoryPathBase . 'en_US/';
        $culture = sfContext::getInstance()->getUser()->getCulture();
        
        if (file_exists($directoryPathBase . $culture . '/')) {
            $this->templateDirectoryPath = $directoryPathBase . $culture . '/';
        }
        
        $this->populateReplacements();

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

        $this->_populateCandidate(); 
        $this-> _populateHRMSiteAddress();
        $this->_populateCandidateUrl();
    }

    protected function _populateCandidate() {
	    if ($this->candidate instanceof JobCandidate) {
		    $this->replacements['candidateName'] = $this->candidate->getFullName();
		    $this->replacements['vacancyName'] = $this->vacancy->getVacancyName();
		    $this->replacements['candidateEmail'] = $this->candidate->getEmail();
		    $this->replacements['candidateContactNumber'] = $this->candidate->getContactNumber();
		    $this->replacements['candidateMicroresume'] = nl2br($this->candidate->getMicroResume());
	    }      
    }    
    
    protected function _populateHRMSiteAddress(){
	    $url = (empty($_SERVER['HTTPS']) OR $_SERVER['HTTPS'] === 'off') ? 'http://' : 'https://';
	    $url .= $_SERVER['HTTP_HOST'];
	   
    	$this->replacements['synerzipHRMSite'] =  $url;
		$this->replacements['synerzipHRMVacancySite'] =  $url."/jobs.php";
    }
    
    protected function _populateCandidateUrl(){
    	$url = (empty($_SERVER['HTTPS']) OR $_SERVER['HTTPS'] === 'off') ? 'http://' : 'https://';
	    $url .= $_SERVER['HTTP_HOST'];
	    $candidteVacanyId = 0;
	    $candidateVacancy = $this->candidate->getJobCandidateVacancy();
		$candidteVacanyId = $candidateVacancy->getId();
    	$this->replacements['viewCandidate'] =   $url."/symfony/web/index.php/recruitment/addCandidate?id=".$this->candidate->getId();
    	$this->replacements['shortlistCandidate'] =   $url."/symfony/web/index.php/recruitment/changeCandidateVacancyStatus?candidateVacancyId=".$candidteVacanyId."&selectedAction=2";
    	$this->replacements['rejectCandidate'] =   $url."/symfony/web/index.php/recruitment/changeCandidateVacancyStatus?candidateVacancyId=".$candidteVacanyId."&selectedAction=3";
    	$this->replacements['downloadResume'] =   $url."/symfony/web/index.php/recruitment/viewCandidateAttachment?attachId=".$this->candidate->getJobCandidateAttachment()->getId();
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
    
    abstract function getSubjectTemplate();
    abstract function getSubjectReplacements();
    abstract function getBodyTemplate();
    abstract function getBodyReplacements();
   
}
