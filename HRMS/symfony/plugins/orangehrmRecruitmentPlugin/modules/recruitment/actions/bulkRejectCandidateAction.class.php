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
class bulkRejectCandidateAction extends sfAction {
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
		if(!($userObj->isAdmin() || $userObj->isRecruitmentManager() || $userObj->isHiringManager())) {
			$this->getUser()->setFlash('warning', __('Invalid Credentials !!'));
			$this->redirect('recruitment/viewCandidates');
		}	
		$candidateVacancyIds = $request->getParameter("chkSelectRow");
                $candidateIds = $this->getCandidateService()->processCandidatesVacancyArray($candidateVacancyIds);
		if(count($candidateIds)>0) {
                /* Modified By: Shagupta Faras
                 * Modified On: 22-07-2014
                 * DESC: preiously candidate had only one vacancy, now candidate can apply for multiple vacancy, changes made for this modification impact
                 */		
                    $idMapping = array();
		    foreach ($candidateVacancyIds as $record) {
                     $tmp=array();
                     $tmp= explode("_", $record);   
                     $id=$tmp[0];   
                     //$candidateVacancy = Doctrine::getTable('JobCandidateVacancy')->findOneByCandidateIdAndVacancyId($tmp[0],$tmp[1])->toArray();
                     $candidateVacancy = $this->getCandidateService()->getCandidateByCandidateIdAndVacancyId($tmp[0],$tmp[1])->toArray();
                     if($candidateVacancy['status'] != JobCandidateVacancy::RECRUITMENT_CANDIDATE_STATUS_REJECTED) {
					$vacancyDetails =Doctrine::getTable('JobVacancy')->findOneById($candidateVacancy['vacancyId'])->toArray();                                       
					//$candidate = Doctrine::getTable('JobCandidate')->findOneById($candidateVacancy['candidateId']);                                         
                    $candidate = $this->getCandidateService()->getCandidateById($candidateVacancy['candidateId']);
					$mapping['id'] = $id;
					$mapping['vacancyId'] = $vacancyDetails['id'];
					$mapping['vacancyName'] = $vacancyDetails['name'];
					$mapping['candidateName'] = $candidate->getFirstName()  . " " . $candidate->getLastName() ;
					array_push($idMapping, $mapping);
			}
                        
                    }        
                     
			$count = $this->getCandidateService()->performBulkReject($idMapping);
			if(!is_null($count)) {
				$this->getUser()->setFlash('success', __('Bulk Reject : ' .$count. ' candidate(s) rejected successfully.'));
				if($count>0) {
					$bulkRejectMailer = new BulkRejectMailer();
					$bulkRejectMailer->send($idMapping);
				}
			} else {
					$this->getUser()->setFlash('error', __('Candidates rejection Failed.'));
			}
		} else {
			$this->getUser()->setFlash('error', __('Bulk Reject failed : No Candidates Selected.'));
		}
		//Bug ID 222: array("candidateId" => "1") has been added to retain search parameters on the viewCandidateAction page as there is a condition on that page which requires candidate it.
		$this->redirect('recruitment/viewCandidates?candidateId=1') ;
	}
}
