<?php

/**
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
class changeCandidateVacanciesAction extends sfAction {
	private $vacancyService;
	private $candidateService;
	
	/**
	 * Get CandidateService
	 * @returns CandidateService
	 */
	public function getVacancyService() {
		if (is_null($this->vacancyService)) {
			$this->vacancyService = new VacancyService();
			$this->vacancyService->setVacancyDao(new VacancyDao());
		}
		return $this->vacancyService;
	}
	
	public function getInterviewService() {
		if (is_null($this->interviewService)) {
			$this->interviewService = new JobInterviewService();
			$this->interviewService->setJobInterviewDao(new JobInterviewDao());
		}
		return $this->interviewService;
	}
    
    /**
     * Get CandidateService
     * @returns CandidateService
     */ 
    public function getCandidateService() {
        if (is_null($this->candidateService)) {
            $this->candidateService = new CandidateService();
            $this->candidateService->setCandidateDao(new CandidateDao());
        }
        return $this->candidateService;
    }

    public function execute($request) {
	    $userObj = $this->getUser()->getAttribute('user');
	    if(!($userObj->isAdmin() || $userObj->isRecruitmentManager())) {
		    $this->getUser()->setFlash('warning', __('Invalid Credentials !!'));
		    $this->redirect($request->getReferer());
	    }
	    
	    $ids = $request->getParameter("chkSelectRow");
	    $fromPage = $request->getParameter('fromPage');
	    $actionName = $request->getParameter('actionName'); 
	    $changeDetails = array();
	    //Request coming from 'Request Tracker'
	    if(!empty($fromPage)){
		    $changeVacancyRequests = $this->getCandidateService()->getRequestDetails($ids);
		    if(count($changeVacancyRequests)>0) {
			    foreach ($changeVacancyRequests as $changeRequest) {
			    	// Request to approve 'change vacancy'	
				    if(($actionName == 'changeVacancy') && ($changeRequest->getRequestStatus() != JobCandidateRequests::REQUEST_STATUS_DONE)){
					    $changeVacancyDetails['candidateId'] = $changeRequest->getCandidateId();
					    $changeVacancyDetails['candidateName'] = $changeRequest->getCandidateName();
					    // old vacancy details
					    $oldVacancy = $changeRequest->getOldJobVacancy();
					    $changeVacancyDetails['oldVacancyId'] = $oldVacancy->getId(); 
					    $changeVacancyDetails['oldVacancyName'] = $oldVacancy->getName();
					    //Get candidate vacancy details
					    $candidateVacancy = $changeRequest->getJobCandidate()->getJobCandidateVacancy();
					    $changeVacancyDetails['oldCandidateVacancy'] = $candidateVacancy;
					    //Get interview details
					    $changeVacancyDetails['oldInterviews'] = $this->getInterviewService()->getInterviewsByCandidateVacancyId($candidateVacancy);
					    
					    $newVacancy = $changeRequest->getNewJobVacancy();
					    $changeVacancyDetails['newVacancyId'] = $newVacancy->getId(); // new vacancy details
					    $changeVacancyDetails['newVacancyName'] = $newVacancy->getName(); // new vacancy details
					    $changeVacancyDetails['newVacancyFlagForResume'] = $newVacancy->getFlagForResume(); // new vacancy details
					    
					    //Push into array
					    array_push($changeDetails, $changeVacancyDetails);	
				    } else if(($actionName == 'reject') && ($changeRequest->getRequestStatus() == JobCandidateRequests::REQUEST_STATUS_PENDING)){
				    	//Request to reject 'change vacancy' request.Only Pending request can be rejected. 
					    $changeRequest->setRequestStatus(JobCandidateRequests::REQUEST_STATUS_REJECT);
					    $changeRequest->setUpdatedBy($this->getUser()->getEmployeeNumber());
						$changeRequest->setUpdatedDate(date('Y-m-d'));
					    $changeRequest->save();
					    $this->getUser()->setFlash('success', __('Request(s) rejected successfully'));
				    } else if($actionName == 'delete'){
				    	// delete request
					    $changeRequest->delete();
					    $this->getUser()->setFlash('success', __('Request(s) deleted successfully'));
				    }
			    }
			    
		    }
	    }else{ //Change vacany from 'View Candidates' page
		    $newVacancyId = $request->getParameter('id');
		    $candidateIds = $this->getCandidateService()->processCandidatesVacancyArray($ids);
		    if(count($candidateIds)>0) {
			    $newVacancy = $this->getVacancyService()->getVacancyById($newVacancyId);
			    
			    foreach ($candidateIds as $id) {
			    	$candidate = $this->getCandidateService()->getCandidateById($id);
			    	$candidateVacancy = $candidate->getJobCandidateVacancy();
					//$candidateVacancy = $existingVacancyList[0];
				    
				    if( $newVacancyId != $candidateVacancy['vacancyId'] && 
					    $candidateVacancy['status'] != JobCandidateVacancy::RECRUITMENT_CANDIDATE_STATUS_HIRED && 
						    $candidateVacancy['status'] != JobCandidateVacancy::RECRUITMENT_CANDIDATE_STATUS_JOB_OFFERED)
				    {
				    	
					    $changeVacancyDetails['candidateId'] = $id;
					    $changeVacancyDetails['candidateName'] = $candidate->getFirstName()  . " " . $candidate->getLastName();
					    // old vacancy details
					    $changeVacancyDetails['oldVacancyId'] = $candidateVacancy->getVacancyId(); 
					    $changeVacancyDetails['oldVacancyName'] = $candidateVacancy->getJobVacancy()->getName();
					    //Get candidate vacancy details
					    $changeVacancyDetails['oldCandidateVacancy'] = $candidateVacancy;
					    //Get interview details
					    $changeVacancyDetails['oldInterviews'] = $this->getInterviewService()->getInterviewsByCandidateVacancyId($candidateVacancy);
					    
					    //New vacancy details
					    $changeVacancyDetails['newVacancyId'] = $newVacancy->getId(); // new vacancy details
					    $changeVacancyDetails['newVacancyName'] = $newVacancy->getName(); // new vacancy details
					    $changeVacancyDetails['newVacancyFlagForResume'] = $newVacancy->getFlagForResume(); // new vacancy details
					    array_push($changeDetails, $changeVacancyDetails);
				    }
			    }
		    }    	
	    }
	    
	    //Only incase of 'ChangeVacancy'
	    if(count($changeDetails) > 0){
		    
		    $count = $this->getCandidateService()->changeCandidateVacancy($changeDetails,$fromPage);
		    if($count>0) {
			    $this->getUser()->setFlash('success', __('Changed vacancies of ' .$count. ' candidate(s).'));
			    //now change the status of request 'Request Tracker'
			    if(!empty($fromPage)){
				    foreach ($changeVacancyRequests as $changeRequest ) {
					    $changeRequest->setRequestStatus(JobCandidateRequests::REQUEST_STATUS_DONE);
					    $changeRequest->setUpdatedBy($this->getUser()->getEmployeeNumber());
						$changeRequest->setUpdatedDate(date('Y-m-d'));
					    $changeRequest->save();
				    }
			    }
			    
			    //TODO: No need to send mail
			    //$changeVacanciesMailer = new ChangeVacanciesMailer();
			    //$changeVacanciesMailer->send($mappingArray, $vacancyDetails['name'], $hiringManagers);
			    
		    }else{
			    $this->getUser()->setFlash('error', __('Change Vacancy : Failed!'));
		    }
	    }
	    
	    
	      	
	    $this->redirect('recruitment/viewCandidates?candidateId=1') ;
    }
    
}
