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
class requestToChangeVacancyAction extends sfAction {
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
		$link = $request->getReferer();
		$link = substr($link, -16);
		$vacancyId = $request->getParameter('id');
		$candidateVacancyIds = $request->getParameter("chkSelectRow");
		$candidateIds = $this->getCandidateService()->processCandidatesVacancyArray($candidateVacancyIds);
               /* Added By; Shagupta Faras
                * Added On: 28-07-2014
                * DESC: To validate the vacancy change request, as agent can apply for muliple vacancy.
                */
                
		if(count($candidateVacancyIds)>0) {
			$requestDetails = array();
			foreach ($candidateVacancyIds as $ids) {                            
                            $idArray=explode("_",$ids);
                            $candidateId=$idArray[0];
                            $candidateVacancyId=$idArray[1];
                            $result=$this->buildJobCandidateRequests($candidateId,$candidateVacancyId,$vacancyId);
                          
                            if(count($result)>0)
                            $requestDetails[]=$result;
                            
				}
			if(count($requestDetails)>0) {
				
				//First save the request details (JobCandidateRequests)
				foreach ($requestDetails as $detail) {
					$jobCandidateRequests = new JobCandidateRequests();
					$jobCandidateRequests->setCandidateId($detail['candidateId']);
					$jobCandidateRequests->setOldVacancyId($detail['oldVacancyId']);
					$jobCandidateRequests->setNewVacancyId($vacancyId);
					$jobCandidateRequests->setRequestStatus(JobCandidateRequests::REQUEST_STATUS_PENDING);
					$jobCandidateRequests->setRequestType(JobCandidateRequests::REQUEST_TYPE_CHANGE_VACANCY);
					$jobCandidateRequests->setCreatedBy($this->getUser()->getEmployeeNumber());
					$jobCandidateRequests->setCreatedDate(date('Y-m-d'));
					$jobCandidateRequests->setUpdatedBy($this->getUser()->getEmployeeNumber());
					$jobCandidateRequests->setUpdatedDate(date('Y-m-d'));
					$this->getCandidateService()->saveJobCandidateRequests($jobCandidateRequests);
				}
				
				//Now send the request notification mail
				$changeVacanciesMailer = new ChangeVacanciesMailer();
				$changeVacanciesMailer->sendRequestNotification($requestDetails, $vacancyDetails['name']);
				$this->getUser()->setFlash('success', __('Your request to change vacancy of candidate(s) submitted.'));
			} else {
				$this->getUser()->setFlash('warning.nofade', __("Request not submitted. Please select 1) Candidate(s) having current status as 'Application Initiated/Screening/Rejected' 2) new vacancy which should not be same as current vacancy "));
			}
		} else {
			$this->getUser()->setFlash('error', __('Change Vacancy : No Candidates Selected'));
		}
		$this->redirect($request->getReferer());
	}
/* Added By; Shagupta Faras
 * Added On: 28-07-2014
 * DESC: To validate the vacancy change request
 */
public function buildJobCandidateRequests($candidateId,$candidateVacancyId,$vacancyId)
{ 
    $result= array();

    /*$query = Doctrine_Query::create()
    			->from('JobCandidateVacancy')
    			->where('candidateId = ?', $candidateId)
                        ->andWhere('vacancyId = ?',$candidateVacancyId)
                        ->fetchOne();*/
    $query=$this->getCandidateService()->getCandidateByCandidateIdAndVacancyId($candidateId,$candidateVacancyId);
    /*$allCandidateVacancies= Doctrine::getTable('JobCandidateVacancy')->createQuery()
            ->select('vacancyId')
            ->where('candidateId = ?', $candidateId)           
            ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR); */
    $allCandidateVacancies= $this->getCandidateService()->getVacanciesInArrayByCandidateId($candidateId);
   
    if($query)   
    {
       $vacancyStatus=$query->getStatus(); 
       if(!in_array($vacancyId,$allCandidateVacancies) && 
						($vacancyStatus == JobCandidateVacancy::RECRUITMENT_CANDIDATE_STATUS_APPLICATION_INITIATED ||
						$vacancyStatus == JobCandidateVacancy::RECRUITMENT_CANDIDATE_STATUS_SCREENING ||
						$vacancyStatus == JobCandidateVacancy::RECRUITMENT_CANDIDATE_STATUS_REJECTED))	{
             
         $result['oldVacancyId']= $candidateVacancyId;     
         $result['candidateId']= $candidateId;  
         $result['id']= $vacancyId;  
}
       
    }
    
    return $result;
}

}
