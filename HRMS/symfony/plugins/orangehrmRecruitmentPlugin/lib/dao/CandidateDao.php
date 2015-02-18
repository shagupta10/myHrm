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
 */

/**
 * CandidateDao for CRUD operation
 *
 */
class CandidateDao extends BaseDao {

    /**
     * Retrieve candidate by candidateId
     * @param int $candidateId
     * @returns jobCandidate doctrine object
     * @throws DaoException
     */
    public function getCandidateById($candidateId) {
        try {
            return Doctrine :: getTable('JobCandidate')->find($candidateId);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    
    /**
     * Retrive all request details.
     */
    public function getRequestDetails($requestIds){
	    try {
		    $q = Doctrine_Query :: create()
			    ->from('JobCandidateRequests jcr');
		    if ($requestIds != null) {
			    $q->whereIn('jcr.request_id', $requestIds);
		    }
		    return $q->execute();
	    } catch (Exception $e) {
		    throw new DaoException($e->getMessage());
	    }
    }

    /**
     * Retrieve all candidates
     * @returns JobCandidate doctrine collection
     * @throws DaoException
     */
    public function getCandidateList($allowedCandidateList, $status = JobCandidate::ACTIVE) {
        try {
            $q = Doctrine_Query :: create()
                    ->from('JobCandidate jc')
            		->addWhere('jc.is_deleted = ?', JobCandidate::IS_NOT_DELETED);
            if ($allowedCandidateList != null) {
                $q->whereIn('jc.id', $allowedCandidateList);
            }
            if (!empty($status)) {
                $q->addWhere('jc.status = ?', $status);
            }
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    
    
    public function getAllCandidateList(){
	    try {
		    $q = Doctrine_Query :: create()
			    ->from('JobCandidate jc')
				->addWhere('jc.status = ?', JobCandidate::ACTIVE)
		    	->addWhere('jc.is_deleted = ?', JobCandidate::IS_NOT_DELETED);
		    return $q->execute();
	    } catch (Exception $e) {
		    throw new DaoException($e->getMessage());
	    }
    }
    
    /**
     * Return an array of candidate names
     * 
     * @version 2.7.1
     * @param Array $allowedCandidateIdList Allowed candidate Id List
     * @param Integer $status Cadidate Status
     * @returns Array Candidate Name List
     * @throws DaoException
     */
    public function getCandidateNameList($allowedCandidateIdList, $status = JobCandidate::ACTIVE) {
        try {
            
            if (!empty($allowedCandidateIdList)) {
                
                $escapeString = implode(',', array_fill(0, count($allowedCandidateIdList), '?'));
                $pdo = Doctrine_Manager::connection()->getDbh();
                $q = "SELECT jc.first_name AS firstName, jc.middle_name AS middleName, jc.last_name AS lastName, jc.id
                		FROM ohrm_job_candidate jc
                		WHERE jc.id IN ({$escapeString}) AND
                		jc.status = ? AND jc.is_deleted = 0";
                
                $escapeValueArray = $allowedCandidateIdList;
                $escapeValueArray[] = $status;
                
                $query = $pdo->prepare($q); 
                $query->execute($escapeValueArray);
                $results = $query->fetchAll(PDO::FETCH_ASSOC);
            }
            
            return $results;
        
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    public function getCandidateListForUserRole($role, $empNumber) {

        try {
            $q = Doctrine_Query :: create()
                    ->select('jc.id')
                    ->from('JobCandidate jc')
            		->where('jc.is_deleted = ?', JobCandidate::IS_NOT_DELETED);
            if ($role == HiringManagerUserRoleDecorator::HIRING_MANAGER) {
                $q->leftJoin('jc.JobCandidateVacancy jcv')
                        ->leftJoin('jcv.JobVacancy jv')
                        ->leftJoin('jv.JobVacancyHiringManager jvh')
                        ->where('jvh.hiringManagerId = ?', $empNumber)
                        ->andWhere('jcv.status <> "SCREENING"')
                      //  ->orwhere('jv.hiringManager1Id = ?', $empNumber)
                        ->orWhere('jc.id NOT IN (SELECT ojcv.candidateId FROM JobCandidateVacancy ojcv) AND jc.addedPerson = ?', $empNumber);
            }
            if ($role == ConsultantUserRoleDecorator::CONSULTANT_USER) {
                $q->leftJoin('jc.JobCandidateVacancy jcv')
                        ->leftJoin('jcv.JobVacancy jv') 
                        ->andWhere('jc.addedPerson = ?', $empNumber);
             
            }
            if ($role == InterviewerUserRoleDecorator::INTERVIEWER) {
                $q->leftJoin('jc.JobCandidateVacancy jcv')
                        ->leftJoin('jcv.JobInterview ji')
                        ->leftJoin('ji.JobInterviewInterviewer jii')
                        ->where('jii.interviewerId = ?', $empNumber);
            }
            $result = $q->fetchArray();
            $idList = array();
            foreach ($result as $item) {
                $idList[] = $item['id'];
            }        
            return $idList;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    
    public function buildSearchRequestQuery(CandidateSearchParameters $paramObject, $countQuery = false) {
	    
	    try {
		    $query = ($countQuery) ? "Select count(*) " : "SELECT jcr.id, jc.id, jc.first_name, jc.middle_name, jc.last_name," .
		    		" jv.name, concat(ref.emp_firstname,' ' ,ref.emp_lastname) as requester_name, " .
		    		"jv.id as vacancyId, ";
		    $query .= "  FROM ohrm_job_candidate_requests jcr";
		    $query .= " LEFT JOIN ohrm_job_candidate_vacancy jcv ON jc.id = jcv.candidate_id";
		    $query .= " LEFT JOIN ohrm_job_vacancy jv ON jcv.vacancy_id = jv.id";
		    $query .= " LEFT JOIN ohrm_job_vacancy_hiring_manager jvhm ON jv.id = jvhm.vacancy_id";
		    $query .= " LEFT JOIN hs_hr_employee e ON jvhm.hiring_manager_id = e.emp_number";
		    $query .= " LEFT JOIN hs_hr_employee ref ON jc.added_person = ref.emp_number";
		    $query .= " LEFT JOIN ohrm_job_candidate_attachment ca ON jc.id = ca.candidate_id";
		    $query .= ' WHERE jc.is_deleted = 0 AND jc.date_of_application  BETWEEN ' . "'{$paramObject->getFromDate()}'" . ' AND ' . "'{$paramObject->getToDate()}'";
		    
		    $candidateStatuses = $paramObject->getCandidateStatus();
		    if (!empty($candidateStatuses)) {
			    $query .= " AND jc.status IN (" . implode(",", $candidateStatuses) . ")";
		    }
		    
		    $query .= $this->_buildKeywordsQueryClause($paramObject->getKeywords());
		    $query .= $this->_buildAdditionalWhereClauses($paramObject);
		    $query .= " group by jc.id,jv.id ORDER BY " . $this->_buildSortQueryClause($paramObject->getSortField(), $paramObject->getSortOrder());
		    if (!$countQuery) {
			    $query .= " LIMIT " . $paramObject->getOffset() . ", " . $paramObject->getLimit();
		    }
		    
		    return $query;
	    } catch (Exception $e) {
		    throw new DaoException($e->getMessage());
	    }
    }
    
     public function getCandidateListForRequest() {
        try {
            $q = Doctrine_Query :: create()
                    ->select('jcr.candidateId')
                    ->from('JobCandidateRequests jcr');
            $result = $q->fetchArray();
            $idList = array();
            foreach ($result as $item) {
                $idList[] = $item['candidateId'];
            }
            return $idList;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    
     /**
      * Search jobcandidate requests
      */
     public function searchJobCandidateRequests(CandidateSearchParameters $paramObject){
	     try {
		     
		     $q = Doctrine_Query :: create()
			     ->from('JobCandidateRequests jcr');
		     $candidteId = $paramObject->getCandidateId();
		     if (!empty($candidteId)) {
			     $q->addWhere('jcr.candidateId = ?', $candidteId);
		     }
		     $referralID = $paramObject->getReferralId();
		     if (!empty($referralID)) {
			     $q->addWhere('jcr.createdBy = ?', $referralID);
		     }
		     $status = $paramObject->getStatus();
		     if (!empty($status)) {
			     $q->addWhere('jcr.request_status = ?',$status);
		     }
		     $q->addWhere("jcr.createdDate BETWEEN ? AND ?", array($paramObject->getFromDate(),$paramObject->getToDate()));
		     $q->offset($paramObject->getOffset());
			 $q->limit($paramObject->getLimit());
		     return $q->execute();
	     } catch (Exception $e) {
		     throw new DaoException($e->getMessage());
	     }
     }
     
     public function getCandidateRequestRecordsCount(CandidateSearchParameters $paramObject){
	     try {
		     $q = Doctrine_Query :: create()
			     ->from('JobCandidateRequests jcr');
		     $candidteId = $paramObject->getCandidateId();
		     if (!empty($candidteId)) {
			     $q->addWhere('jcr.candidateId = ?', $candidteId);
		     }
		     $referralID = $paramObject->getReferralId();
		     if (!empty($referralID)) {
			     $q->addWhere('jcr.createdBy = ?', $referralID);
		     }
		     $status = $paramObject->getStatus();
		     if (!empty($status)) {
			     $q->addWhere('jcr.request_status = ?',$status);
		     }
		     $q->addWhere("jcr.createdDate BETWEEN ? AND ?", array($paramObject->getFromDate(),$paramObject->getToDate()));
		     $candidateRequest=  $q->execute();
		     return count($candidateRequest);
	     } catch (Exception $e) {
		     throw new DaoException($e->getMessage());
	     }
     }

    /**
     * Retriving candidates based on the search criteria
     * @param CandidateSearchParameters $searchParam
     * @return CandidateSearchParameters
     */
    public function searchCandidates($searchCandidateQuery) {

        try {
           
            $pdo = Doctrine_Manager::connection()->getDbh();
            $res = $pdo->query($searchCandidateQuery);

            $candidateList = $res->fetchAll();

            $candidatesList = array();
            foreach ($candidateList as $candidate) {
                $param = new CandidateSearchParameters();
                $param->setVacancyName($candidate['name']);
                $param->setVacancyStatus($candidate['vacancyStatus']);
                $param->setCandidateId($candidate['id']);
                $param->setVacancyId($candidate['vacancyId']);
                $param->setCandidateName($candidate['first_name'] . " " . $candidate['middle_name'] . " " . $candidate['last_name'] . $this->_getCandidateNameSuffix($candidate['candidateStatus']));
                $param->setCandidateEmail($candidate['email']);
                $param->setCandidateContactNumber($candidate['contact_number']);
                $param->setHiringManagerName($candidate['employee_name']);
                $param->setDateOfApplication($candidate['date_of_application']);
                $param->setAttachmentId($candidate['attachmentId']);
                $param->setCandidateVacancyId($candidate['candidateVacancyId']);
 				
 				if($candidate['status'] == "HOLD1" || $candidate['status'] == "HOLD2" || $candidate['status'] == "HOLD3")
					$candidate['status'] = "HOLD";
				$param->setStatusName(ucwords(strtolower($candidate['status'])));

                $referralName = $candidate['ref_firstname'] . " " . $candidate['ref_lastname'];
                $param->setReferralName($referralName);
                $onlyInterviewer=$this->isOnlyInterviewer($candidate['id']);
                if(!$onlyInterviewer)
                $param->setMicroResume($candidate['microResume']);
                $candidatesList[] = $param;
            }
            return $candidatesList;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param CandidateSearchParameters $searchParam
     * @return <type>
     */
    public function getCandidateRecordsCount($countQuery) {

        try {
            $pdo = Doctrine_Manager::connection()->getDbh();
            $res = $pdo->query($countQuery);
            $count = $res->fetch();
            return $count[0];
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param JobCandidate $candidate
     * @return <type>
     */
    public function saveCandidate(JobCandidate $candidate) {
        try {
            if ($candidate->getId() == "") {
                $idGenService = new IDGeneratorService();
                $idGenService->setEntity($candidate);
                $candidate->setId($idGenService->getNextID());
            }
            $candidate->save();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    
    /**
     *
     * @param JobCandidate $candidate
     * @return <type>
     */
    public function saveJobCandidateRequests(JobCandidateRequests $jobCandidateRequests) {
	    try {
		  
		    $jobCandidateRequests->save();
		    return $jobCandidateRequests;
	    } catch (Exception $e) {
		    throw new DaoException($e->getMessage());
	    }
    }
    
    /**
     *
     * @param <type> $candidate
     * @return <type>
     */
    public function updateJobCandidateRequests($jobCandidateRequests) {
	    $q = Doctrine_Query:: create()->update('JobCandidateRequests')
		    ->set('$request_status', '?', $jobCandidateRequests->request_status)
			->set('$updatedBy', '?', $jobCandidateRequests->updatedBy)
			->set('$updatedDate', '?', $jobCandidateRequests->updatedDate);
	    $q->where('request_id = ?', $jobCandidateRequests->request_id);
	    return $q->execute();
    }
    
    
    /**
     * Delete JobCandidateRequests
     * @param array $toBeDeletedJobCandidateRequestIds
     * @return boolean
     */
    public function deleteJobCandidateRequests($toBeDeletedJobCandidateRequestIds) {
	    
	    try {
		    $q = Doctrine_Query:: create()
			    ->delete()
				->from('JobCandidateRequests')
				->whereIn('id', $toBeDeletedJobCandidateRequestIds);
		    
		    $result = $q->execute();
		    return true;
	    } catch (Exception $e) {
		    throw new DaoException($e->getMessage());
	    }
    }
    

    /**
     *
     * @param JobCandidateVacancy $candidateVacancy
     * @return <type>
     */
    public function saveCandidateVacancy(JobCandidateVacancy $candidateVacancy) {
        try {
        	
            if ($candidateVacancy->getId() == '') {
                $idGenService = new IDGeneratorService();
                $idGenService->setEntity($candidateVacancy);
                $candidateVacancy->setId($idGenService->getNextID());
            }
            $candidateVacancy->save();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param JobCandidate $candidate
     * @return <type>
     */
    public function updateCandidate(JobCandidate $candidate) {
        try {
        	 //$logger = Logger::getLogger('dao.CandidateDao');
        	//$logger->error('dao.CandidateDao: ' . $candidate->addedPerson);
            $q = Doctrine_Query:: create()->update('JobCandidate')
                    ->set('firstName', '?' , $candidate->firstName)
                    ->set('lastName', '?' , $candidate->lastName)
                    ->set('middleName','?' ,$candidate->middleName)
                    ->set('email', '?' ,$candidate->email)
                    ->set('alternateEmail','?', empty($candidate->alternateEmail)? '' : $candidate->alternateEmail)
                    ->set('contactNumber','?', $candidate->contactNumber)
                    ->set('alternateNumber','?', empty($candidate->alternateNumber)? '' : $candidate->alternateNumber)
                    ->set('keywords','?', empty($candidate->keywords)? '' : $candidate->keywords)
                    ->set('keySkills','?', empty($candidate->keySkills)? '' : $candidate->keySkills)
                    ->set('expectedDoj', '?', empty($candidate->expectedDoj)? '' : $candidate->expectedDoj)
                    ->set('originalLocation', '?',empty($candidate->originalLocation)? '' : $candidate->originalLocation)
                    ->set('visaStatus', '?',empty($candidate->visaStatus)? '' : $candidate->visaStatus)
                    ->set('microResume','?',empty($candidate->microResume)? '' : $candidate->microResume)
                    ->set('preferredLocation','?', empty($candidate->preferredLocation)? '' : $candidate->preferredLocation)
                    ->set('educationDetailDegree','?', empty($candidate->educationDetailDegree)? '' : $candidate->educationDetailDegree)
                    ->set('educationDetailSpec','?', empty($candidate->educationDetailSpec)? '' : $candidate->educationDetailSpec)
                    ->set('currentCompany','?', empty($candidate->currentCompany)? '' : $candidate->currentCompany)
                    ->set('designation','?', empty($candidate->designation)? '' : $candidate->designation)
                    ->set('stability','?', empty($candidate->stability)? '' : $candidate->stability)
                    ->set('projectDetails','?', empty($candidate->projectDetails)? '' : $candidate->projectDetails)
                    ->set('comment', '?', empty($candidate->comment)? '' : $candidate->comment)
                    ->set('communicationSkills','?', empty($candidate->communicationSkills)? '' : $candidate->communicationSkills)
                    ->set('employmentType','?', empty($candidate->employmentType)? '' : $candidate->employmentType)
                    ->set('dateOfApplication', '?', empty($candidate->dateOfApplication)? '' : $candidate->dateOfApplication)
                    ->set('currentCtc', '?', $ctc=empty($candidate->currentCtc) ? 0: $candidate->currentCtc)
                    ->set('expectedCtc', '?', $etc=empty($candidate->expectedCtc) ? 0: $candidate->expectedCtc)
                    ->set('noticePeriod', '?', $noticePeriod= empty($candidate->noticePeriod) ? 0 : $candidate->noticePeriod)
                    ->set('educationDetailPerc', '?', $educationDetailPerc= empty($candidate->educationDetailPerc) ? 0 : $candidate->educationDetailPerc)
                    ->set('totalExperience','?',  $totalExperience=empty($candidate->totalExperience) ? 0 : $candidate->totalExperience)
                    ->set('relevantExperience','?', $relevantExperience=empty($candidate->relevantExperience) ? 0 : $candidate->relevantExperience)
                    ->set('educationGap','?', $educationGap=empty($candidate->educationGap) ? 0 : $candidate->educationGap)
                    ->set('workGap', '?',$workGap=empty($candidate->workGap) ? 0 : $candidate->workGap);
                    
                   if(empty($candidate->addedPerson)){
                    	$q->set('addedPerson', 'NULL');
                    }else{
                    	$q->set('addedPerson', '?',$candidate->addedPerson);
                    }
                    
                   $q->where('id = ?', $candidate->id);

            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param JobCandidate $candidate
     * @return <type>
     */
    public function updateCandidateHistory(CandidateHistory $candidateHistory) {
        try {
            $q = Doctrine_Query:: create()->update('CandidateHistory')
                    ->set('interviewers', '?', $candidateHistory->interviewers)
                    ->where('id = ?', $candidateHistory->id);

            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param <type> $candidateVacancyId
     * @return <type>
     */
    public function getCandidateVacancyById($candidateVacancyId) {
        try {
            $q = Doctrine_Query :: create()
                    ->from('JobCandidateVacancy jcv')
                   // ->leftJoin('jcv.JobVacancy jv')
                    //->leftJoin('jv.JobVacancyHiringManager jvhm')
                    ->where('jcv.id = ?', $candidateVacancyId);
            return $q->fetchOne();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param JobCandidateVacancy $candidateVacancy
     * @return <type>
     */
    public function updateCandidateVacancy(JobCandidateVacancy $candidateVacancy) {
        try {
            $q = Doctrine_Query:: create()->update('JobCandidateVacancy')
                    ->set('status', '?', $candidateVacancy->status)
                    ->where('id = ?', $candidateVacancy->id);
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param CandidateHistory $candidateHistory
     * @return <type>
     */
    public function saveCandidateHistory(CandidateHistory $candidateHistory) {
        try {
            if ($candidateHistory->getId() == '') {
                $idGenService = new IDGeneratorService();
                $idGenService->setEntity($candidateHistory);
                $candidateHistory->setId($idGenService->getNextID());
            }
            $candidateHistory->save();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param <type> $candidateId
     * @return <type>
     */
    public function getCandidateHistoryForCandidateId($candidateId, $allowedHistoryList) {
        try {
            $q = Doctrine_Query:: create()
                    ->from('CandidateHistory ch')
                    ->whereIn('ch.id', $allowedHistoryList)
                    ->andWhere('ch.candidateId = ?', $candidateId)
                    ->orderBy('ch.performedDate DESC,ch.id DESC');
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param <type> $id
     * @return <type>
     */
    public function getCandidateHistoryById($id) {
        try {
            $q = Doctrine_Query:: create()
                    ->from('CandidateHistory')
                    ->where('id = ?', $id);
            return $q->fetchOne();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Return an array of Candidate History Ids based on user role
     * 
     * @version 2.7.1
     * @param String $role User Role
     * @param Integer $empNumber Employee Number
     * @param Integer $candidateId Candidate Id
     * @return Array of Candidate History Ids
     * @throws DaoException
     */
    public function getCanidateHistoryForUserRole($role, $empNumber, $candidateId) {
        try {
            $q = Doctrine_Query :: create()
                    ->select('ch.id')
                    ->from('CandidateHistory ch');
            if ($role == HiringManagerUserRoleDecorator::HIRING_MANAGER) {
                 $q->leftJoin('ch.JobVacancy jv')
                 		->leftJoin('jv.JobVacancyHiringManager jvhm')
                        ->leftJoin('ch.JobCandidate jc')
                        ->where('ch.candidateId = ?', $candidateId)
                        ->andWhere('jvhm.hiringManagerId = ? OR ( ch.action IN (?) OR (ch.candidateId NOT IN (SELECT ojcv.candidateId FROM JobCandidateVacancy ojcv) AND jc.addedPerson = ?) OR ch.performedBy = ? )', array($empNumber, WorkflowStateMachine::RECRUITMENT_CANDIDATE_ACTION_ADD, $empNumber, $empNumber));
            }
            if ($role == InterviewerUserRoleDecorator::INTERVIEWER) {
                $q->leftJoin('ch.JobInterview ji ON ji.id = ch.interview_id')
                        ->leftJoin('ji.JobInterviewInterviewer jii')
                        ->where('ch.candidateId = ?', $candidateId)
                        ->andWhere('jii.interviewerId = ? OR (ch.performedBy = ? OR ch.action IN (?, ?, ?, ?, ?, ?))',  array($empNumber, $empNumber, WorkflowStateMachine::RECRUITMENT_CANDIDATE_ACTION_ADD, WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_ATTACH_VACANCY, WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHORTLIST, WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_INTERVIEW, WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_FAILED, WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_PASSED));
            }
            if ($role == AdminUserRoleDecorator::ADMIN_USER) {
                $q->where('ch.candidateId = ?', $candidateId);
            }
            $result = $q->fetchArray();
            $idList = array();
            foreach ($result as $item) {
                $idList[] = $item['id'];
            }
            return $idList;

        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Get all vacancy Ids for relevent candidate
     * @param int $candidateId
     * @return array $vacancies
     */
    public function getAllVacancyIdsForCandidate($candidateId) {

        try {

            $q = Doctrine_Query:: create()
                    ->from('JobCandidateVacancy v')
                    ->where('v.candidateId = ?', $candidateId);
            $vacancies = $q->execute();

            $vacancyIdsForCandidate = array();
            foreach ($vacancies as $value) {
                $vacancyIdsForCandidate[] = $value->getVacancyId();
            }
            return $vacancyIdsForCandidate;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Delete Candidate
     * @param array $toBeDeletedCandidateIds
     * @return boolean
     */
    public function deleteCandidates($toBeDeletedCandidateIds) {
		$user = sfContext::getInstance()->getUser()->getEmployeeNumber(); // current users empNumber
        try {
        	foreach ($toBeDeletedCandidateIds as $candidateId) {
	            $q = Doctrine_Query:: create()
	                    ->update('JobCandidate')
	                    ->set('isDeleted','?',JobCandidate::IS_DELETED)
	                    ->where('id = ?', $candidateId);
	            $result = $q->execute();
	            
	            $candidate = $this->getCandidateById($candidateId);
	            $cv = $candidate->getJobCandidateVacancy();
	            $candidateHistory = new CandidateHistory();
	            $candidateHistory->setCandidateId($candidateId);
	            $candidateHistory->setVacancyId($cv->getVacancyId());
	            $candidateHistory->setAction(WorkflowStateMachine::RECRUITMENT_CANDIDATE_ACTION_DELETE);
	            $candidateHistory->setCandidateVacancyName($cv->getVacancyName());
	            $candidateHistory->setPerformedBy($user);
	            $candidateHistory->setPerformedDate(date('Y-m-d') . " " . date('H:i:s'));
	            $this->saveCandidateHistory($candidateHistory);
	            
				// Delete requests to change vacancy
	            $q3 = Doctrine_Query::create()
	            	->delete('JobCandidateRequests')
	            	->where('candidateId = ?', $candidateId);
	            $q3->execute();
        	}
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Delete Candidate-Vacancy Relations
     * @param array $toBeDeletedRecords
     * @return boolean
     */
    public function deleteCandidateVacancies($toBeDeletedRecords) {

        try {
            $q = Doctrine_Query:: create()
                    ->delete()
                    ->from('JobCandidateVacancy cv')
                    ->where('candidateId = ? AND vacancyId = ?', $toBeDeletedRecords[0]);
            for ($i = 1; $i < count($toBeDeletedRecords); $i++) {
                $q->orWhere('candidateId = ? AND vacancyId = ?', $toBeDeletedRecords[$i]);
            }

            $deleted = $q->execute();
            if ($deleted > 0) {
                return true;
            }
            return false;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    
    /**
     * Returns all employee who registered candidate for specific vacancy
     */
    public function getEmpListInCandidate(){
		 try {
		 	
		 	$query = "SELECT emp_number, employee_id, emp_firstname, emp_lastname FROM hs_hr_employee";
			$query .= " WHERE emp_number IN (SELECT added_person FROM ohrm_job_candidate)";
		
	        $pdo = Doctrine_Manager::connection()->getDbh();
	        $res = $pdo->query($query);
	        $empList = $res->fetchAll();            
	        return $empList;
	    } catch (Exception $e) {
	        throw new DaoException($e->getMessage());
	    }
    }

    public function buildSearchQuery(CandidateSearchParameters $paramObject, $countQuery = false) {

        try {
            $query = ($countQuery) ? "SELECT COUNT(candidateCount) From (Select count(*) AS candidateCount" : "SELECT jc.id, jc.first_name, jc.middle_name, jc.last_name, jc.email, jc.contact_number, jc.date_of_application, jc.microResume, jcv.id as candidateVacancyId, jcv.status, jv.name, group_concat(concat(e.emp_firstname,' ' ,e.emp_lastname)) as employee_name,  ref.emp_firstname AS ref_firstname, ref.emp_middle_name AS ref_middle_name, ref.emp_lastname AS ref_lastname, jv.status as vacancyStatus, jv.id as vacancyId, ca.id as attachmentId, jc.status as candidateStatus";
            $query .= "  FROM ohrm_job_candidate jc";
            $query .= " LEFT JOIN ohrm_job_candidate_vacancy jcv ON jc.id = jcv.candidate_id";
            $query .= " LEFT JOIN ohrm_job_vacancy jv ON jcv.vacancy_id = jv.id";
            $query .= " LEFT JOIN ohrm_job_vacancy_hiring_manager jvhm ON jv.id = jvhm.vacancy_id";
            $query .= " LEFT JOIN hs_hr_employee e ON jvhm.hiring_manager_id = e.emp_number";
            $query .= " LEFT JOIN hs_hr_employee ref ON jc.added_person = ref.emp_number";
            $query .= " LEFT JOIN ohrm_job_candidate_attachment ca ON jc.id = ca.candidate_id";
            $query .= ' WHERE jc.is_deleted = 0 AND jc.date_of_application  BETWEEN ' . "'{$paramObject->getFromDate()}'" . ' AND ' . "'{$paramObject->getToDate()}'";

            $candidateStatuses = $paramObject->getCandidateStatus();
            if (!empty($candidateStatuses)) {
                $query .= " AND jc.status IN (" . implode(",", $candidateStatuses) . ")";
            }

            $query .= $this->_buildKeywordsQueryClause($paramObject->getKeywords());
            $query .= $this->_buildAdditionalWhereClauses($paramObject);
            $query .= " group by jc.id,jv.id ORDER BY " . $this->_buildSortQueryClause($paramObject->getSortField(), $paramObject->getSortOrder());
            if (!$countQuery) {
                $query .= " LIMIT " . $paramObject->getOffset() . ", " . $paramObject->getLimit();
            }else{
            	 $query .= ") AS countQuery";
            }

            return $query;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param array $keywords
     * @return string
     */
    private function _buildKeywordsQueryClause($keywords) {
        $keywordsQueryClause = '';
        if (!empty($keywords)) {
            $keywords = str_replace("'", "\'", $keywords);
            $words = explode(',', $keywords);
            $length = count($words);
            for ($i = 0; $i < $length; $i++) {
                $keywordsQueryClause .= ' AND jc.keywords LIKE ' . "'" . '%' . trim($words[$i]) . '%' . "'";
            }
        }

        return $keywordsQueryClause;
    }

    /**
     *
     * @param string $sortField
     * @param string $sortOrder
     * @return string
     */
    private function _buildSortQueryClause($sortField, $sortOrder) {
        $sortQuery = '';

        if ($sortField == 'jc.first_name') {
            $sortQuery = 'jc.last_name ' . $sortOrder . ', ' . 'jc.first_name ' . $sortOrder;
        } elseif ($sortField == 'e.emp_firstname') {
            $sortQuery = 'e.emp_lastname ' . $sortOrder . ', ' . 'e.emp_firstname ' . $sortOrder;
        } elseif ($sortField == 'jc.date_of_application') {
            $sortQuery = 'jc.date_of_application ' . $sortOrder . ', ' . 'jc.last_name ASC, jc.first_name ASC';
        } else {
            $sortQuery = $sortField . " " . $sortOrder;
        }

        return $sortQuery;
    }

    /**
     * @param CandidateSearchParameters $paramObject
     * @return string
     */
    private function _buildAdditionalWhereClauses(CandidateSearchParameters $paramObject) {

        $allowedCandidateList = $paramObject->getAllowedCandidateList();
        $jobTitleCode = $paramObject->getJobTitleCode();
        $jobVacancyId = $paramObject->getVacancyId();
        $hiringManagerId = $paramObject->getHiringManagerId();
        $status = $paramObject->getStatus();
        $allowedVacancyList = $paramObject->getAllowedVacancyList();
        $isAdmin = $paramObject->getIsAdmin();
        $empNumber = $paramObject->getEmpNumber();

        $whereClause = '';
        $whereFilters = array();
        if ($allowedVacancyList != null && !$isAdmin) {
            $this->_addAdditionalWhereClause($whereFilters, 'jv.id', '(' . implode(',', $allowedVacancyList) . ')', 'IN');
        }
        if ($allowedCandidateList != null && !$isAdmin) {
            $this->_addAdditionalWhereClause($whereFilters, 'jc.id', '(' . implode(',', $allowedCandidateList) . ')', 'IN');
        }
        if (!empty($jobTitleCode) || !empty($jobVacancyId) || !empty($hiringManagerId) || !empty($status)) {
            $this->_addAdditionalWhereClause($whereFilters, 'jv.status', $paramObject->getVacancyStatus());
        }


        $this->_addAdditionalWhereClause($whereFilters, 'jv.job_title_code', $paramObject->getJobTitleCode());
        $this->_addAdditionalWhereClause($whereFilters, 'jv.id', $paramObject->getVacancyId());
        $this->_addAdditionalWhereClause($whereFilters, 'jvhm.hiring_manager_id', $paramObject->getHiringManagerId());
        //$this->_addAdditionalWhereClause($whereFilters, 'jcv.status', $paramObject->getStatus());
        if(!empty($status))
        $containsNonEmpty = count(array_filter($status, "strlen"));
        else
        $containsNonEmpty=null;
        $statusStr = '';
        $status1 = array();
        $status2 = array();
        if($containsNonEmpty){
            if(in_array("HOLD", $status)){
                $status1 = array("HOLD1","HOLD2","HOLD3");
            }
            if(in_array("progress", $status)) {
            	$status2 = array("SHORTLISTED","APPLICATION INITIATED","INTERVIEW SCHEDULED");
            }
            $status3 = array_diff($status,array("progress"),array("HOLD"),array(""));
            
            $status = array_unique(array_merge($status1,$status2,$status3));
            
            if(!empty($status)){
                $statusStr = '"'.implode('","',$status).'"';
            }
            $this->_addAdditionalWhereClause($whereFilters, 'jcv.status', '('.trim($statusStr,',').') ','IN');
        }

        $this->_addCandidateNameClause($whereFilters, $paramObject);

        $this->_addAdditionalWhereClause($whereFilters, 'jc.mode_of_application', $paramObject->getModeOfApplication());
		$this->_addAdditionalWhereClause($whereFilters, 'jc.added_person', $paramObject->getReferralId());

        $whereClause .= ( count($whereFilters) > 0) ? (' AND ' . implode('AND ', $whereFilters)) : '';

        if ($empNumber != null) {
            $whereClause .= " OR jc.id NOT IN (SELECT ojcv.candidate_id FROM ohrm_job_candidate_vacancy ojcv) " ;
        }
        if(!empty($status)){
            $whereClause .=" AND NOT ISNULL(jcv.status)";
        }
        return $whereClause;
    }

    /**
     *
     * @param array_pointer $where
     * @param string $field
     * @param mixed $value
     * @param string $operator
     */
    private function _addAdditionalWhereClause(&$where, $field, $value, $operator = '=') {
        if (!empty($value)) {
            if ($operator === '=') {
                $value = "'{$value}'";
            }
            $where[] = "{$field}  {$operator} {$value}";
        }
    }

    /**
     * Add where clause to search by candidate name.
     * 
     * @param type $where Where Clause
     * @param type $paramObject Search Parameter object
     */
    private function _addCandidateNameClause(&$where, $paramObject) {

        // Search by Name
        $candidateName = $paramObject->getCandidateName();

        if (!empty($candidateName)) {

            $candidateFullNameClause = "concat_ws(' ', TRIM(jc.first_name), " .
                    "IF(jc.middle_name <> '', TRIM(jc.middle_name), NULL), " .
                    "TRIM(jc.last_name))";

            // Replace multiple spaces in string with single space
            $candidateName = preg_replace('!\s+!', ' ', $candidateName);
            $candidateName = "'%" . $candidateName . "%'";

            $this->_addAdditionalWhereClause($where, $candidateFullNameClause, $candidateName, 'LIKE');
        }
    }

    public function isHiringManager($candidateVacancyId, $empNumber) {
        try {
            $q = Doctrine_Query :: create()
                    ->select('COUNT(*)')
                    ->from('JobCandidateVacancy jcv')
                    ->leftJoin('jcv.JobVacancy jv')
                     ->leftJoin('jv.JobVacancyHiringManager jvhm')
                    ->where('jcv.id = ?', $candidateVacancyId)
                    ->andWhere('jvhm.hiringManagerId = ?', $empNumber);

            $count = $q->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
            return ($count > 0);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function isInterviewer($candidateVacancyId, $empNumber) {
        try {
            $q = Doctrine_Query :: create()
                    ->select('COUNT(*)')
                    ->from('JobInterviewInterviewer jii')
                    ->leftJoin('jii.JobInterview ji')
                    ->leftJoin('ji.JobCandidateVacancy jcv')
                    ->where('jcv.id = ?', $candidateVacancyId)
                    ->andWhere('jii.interviewerId = ?', $empNumber);

            $count = $q->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
            return ($count > 0);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Get candidate name suffix according to the candidate status
     * @param integer $statusCode
     * return string $suffix
     */
    private function _getCandidateNameSuffix($statusCode) {

        $suffix = "";

        if ($statusCode == JobCandidate::ARCHIVED) {
            $suffix = " (" . __('Archived') . ")";
        }

        return $suffix;
    }

    public function getCandidateVacancyByCandidateIdAndVacancyId($candidateId, $vacancyId) {
        try {
            $q = Doctrine_Query :: create()
                    ->from('JobCandidateVacancy jcv')
                    ->where('jcv.candidateId = ?', $candidateId)
                    ->andWhere('jcv.vacancyId = ?', $vacancyId);
            return $q->fetchOne();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    
    public function getReferredCandidate($referral_id, $status = JobCandidate::ACTIVE)
    {
    	try{
    		$q= Doctrine_Query::create()
    		->from('JobCandidate jc')
    		->where('jc.addedPerson = ?', $referral_id)
    		->andWhere('jc.is_deleted = ?', JobCandidate::IS_NOT_DELETED)
    		->andWhere('jc.status = ?', $status);
    		return  $q->fetchArray();
    		 
    	} catch (Exception $e){
    		throw new DaoException($e->getMessage());
    	}
    }
    
    public function searchCandidatesReferred($searchCandidateQuery) {
    
    	try {
    		$pdo = Doctrine_Manager::connection()->getDbh();
    		$res = $pdo->query($searchCandidateQuery);
    
    		$candidateList = $res->fetchAll();
    
    		$candidatesList = array();
    		foreach ($candidateList as $candidate) {
    
    			$param = new CandidateSearchParameters();
    			$param->setVacancyName($candidate['name']);
    			$param->setVacancyStatus($candidate['vacancyStatus']);
    			$param->setCandidateId($candidate['id']);
    			$param->setVacancyId($candidate['vacancyId']);
    			$param->setCandidateName($candidate['first_name'] . " " . $candidate['middle_name'] . " " . $candidate['last_name'] . $this->_getCandidateNameSuffix($candidate['candidateStatus']));
    			$param->setDateOfApplication($candidate['date_of_application']);
    			$param->setAttachmentId($candidate['attachmentId']);
    			if($candidate['status'] == "HOLD1" || $candidate['status'] == "HOLD2" || $candidate['status'] == "HOLD3")
					$candidate['status'] = "HOLD";
				
                $param->setStatusName(ucwords(strtolower($candidate['status'])));

    			$referralName = $candidate['ref_firstname'] . " " . $candidate['ref_lastname'];
    			$param->setReferralName($referralName);
    			$candidatesList[] = $param;
    		}
    		return $candidatesList;
    	} catch (Exception $e) {
    		throw new DaoException($e->getMessage());
    	}
    }
    
    public function buildSearchQueryForReferrals(CandidateSearchParameters $paramObject, $countQuery = false) {
    
    	try {
    		$query = ($countQuery) ? "SELECT COUNT(*)" : "SELECT jc.id, jc.first_name, jc.middle_name, jc.last_name, jc.date_of_application, jcv.status, jv.name, ref.emp_firstname AS ref_firstname, ref.emp_middle_name AS ref_middle_name, ref.emp_lastname AS ref_lastname, jv.status as vacancyStatus, jv.id as vacancyId, ca.id as attachmentId, jc.status as candidateStatus";
    		$query .= "  FROM ohrm_job_candidate jc";
    		$query .= " LEFT JOIN ohrm_job_candidate_vacancy jcv ON jc.id = jcv.candidate_id";
    		$query .= " LEFT JOIN ohrm_job_vacancy jv ON jcv.vacancy_id = jv.id";
    		//$query .= " LEFT JOIN hs_hr_employee e ON jv.hiring_manager_id = e.emp_number";
    		$query .= " LEFT JOIN hs_hr_employee ref ON jc.added_person = ref.emp_number";
    		$query .= " LEFT JOIN ohrm_job_candidate_attachment ca ON jc.id = ca.candidate_id";
    		$query .= ' WHERE jc.is_deleted = 0 AND jc.date_of_application  BETWEEN ' . "'{$paramObject->getFromDate()}'" . ' AND ' . "'{$paramObject->getToDate()}'";

    		
    		 $candidateStatuses = $paramObject->getCandidateStatus();
            if (!empty($candidateStatuses)) {
                $query .= " AND jc.status IN (" . implode(",", $candidateStatuses) . ")";
            }
    
    		$query .= $this->_buildKeywordsQueryClause($paramObject->getKeywords());
    		$query .= $this->_buildAdditionalWhereClausesReferred($paramObject);
    		$query .= " ORDER BY " . $this->_buildSortQueryClause($paramObject->getSortField(), $paramObject->getSortOrder());
    		if (!$countQuery) {
    			$query .= " LIMIT " . $paramObject->getOffset() . ", " . $paramObject->getLimit();
    		}
    		return $query;
    	} catch (Exception $e) {
    		throw new DaoException($e->getMessage());
    	}
    }
    
    private function _buildAdditionalWhereClausesReferred(CandidateSearchParameters $paramObject) {
    	$status = $paramObject->getStatus();
    	$empNumber = $paramObject->getEmpNumber();
    	$whereClause = '';
    	$whereFilters = array();
    
    	$this->_addAdditionalWhereClause($whereFilters, 'jv.id', $paramObject->getVacancyId());
    	if(!empty($status)){
	    	if($status == "HOLD"){
		    	$status = '"HOLD1","HOLD2","HOLD3"';
	    	}else{
		    	$status = '"'.$status.'"';
	    	}		
	    	$this->_addAdditionalWhereClause($whereFilters, 'jcv.status', '('.$status.')','IN');
    	}
    	$this->_addCandidateNameClause($whereFilters, $paramObject);
    	$this->_addAdditionalWhereClause($whereFilters, 'jc.added_person', $paramObject->getEmpNumber());
    
    
    	$whereClause .= ( count($whereFilters) > 0) ? (' AND ' . implode('AND ', $whereFilters)) : '';
   
    	if(!empty($status)){
    		$whereClause .=" AND NOT ISNULL(jcv.status)";
    	}
    	return $whereClause;
    }
    
    public function getLatestScheduledInterview($id) {
    	try {
    		$pdo = Doctrine_Manager::connection()->getDbh();
    		$res = $pdo->query('select interview_date as date, interview_time as time, interview_name as name from ohrm_job_interview WHERE id IN (select max(id) from ohrm_job_interview where candidate_id = "'.$id.'")');
    		$interviewDetails = $res->fetch();
    		return $interviewDetails;
    	} catch (Exception $e) {
    		throw new DaoException($e->getMessage());
    	}
    }
    
    public function getCurrentScheduledInterviewHiringMgr($empNumber,$convertDate){
    	try{
    		$qry ="SELECT vhm.hiring_manager_id, vhm.vacancy_id, jv.name, jc.first_name, jc.last_name, ji.interview_date, ji.interview_time";
    		$qry .=" FROM `ohrm_job_vacancy_hiring_manager` AS vhm";
    		$qry .=" LEFT JOIN `ohrm_job_candidate_vacancy` AS jcv ON vhm.vacancy_id = jcv.vacancy_id";
    		$qry .=" LEFT JOIN `ohrm_job_vacancy` AS jv ON jcv.vacancy_id = jv.id";
    		$qry .=" LEFT JOIN `ohrm_job_candidate` AS jc ON jcv.candidate_id = jc.id";
    		$qry .=" LEFT JOIN `ohrm_job_interview` AS ji ON jc.id = ji.candidate_id";
    		$qry .=" WHERE jcv.status = 'INTERVIEW SCHEDULED' AND vhm.hiring_manager_id = ".$empNumber." AND jc.is_deleted = 0";
    		$qry .=" AND CONCAT(ji.interview_date,' ',ji.interview_time) BETWEEN '".$convertDate."' AND DATE_ADD('".$convertDate."', INTERVAL 2 DAY) ORDER BY ji.interview_date";
    		$pdo = Doctrine_Manager::connection()->getDbh();
    		$res = $pdo->query($qry);
    		$currentinterviewDetails = $res->fetchAll();
    		return $currentinterviewDetails;
    	}catch (Exception $e) {
    		throw new DaoException($e->getMessage());
    	}
    }
    
    public function getCurrentScheduledInterviewInterviewer($empNumber,$convertDate){
    	try{
    		$qry ="SELECT jii.interviewer_id, ji.interview_date, ji.interview_time, jc.first_name, jc.last_name, jv.name";
    		$qry .=" FROM `ohrm_job_interview_interviewer` AS jii";
    		$qry .=" LEFT JOIN `ohrm_job_interview` AS ji ON jii.interview_id = ji.id";
    		$qry .=" LEFT JOIN `ohrm_job_candidate` AS jc ON ji.candidate_id = jc.id";
    		$qry .=" LEFT JOIN `ohrm_job_candidate_vacancy` AS jcv ON jc.id = jcv.candidate_id";
    		$qry .=" LEFT JOIN `ohrm_job_vacancy` AS jv ON jcv.vacancy_id = jv.id";
    		$qry .=" WHERE interviewer_id = ".$empNumber." AND jc.is_deleted = 0 AND jcv.status = 'INTERVIEW SCHEDULED'";
    		$qry .=" AND CONCAT(ji.interview_date,' ',ji.interview_time) BETWEEN '".$convertDate."' AND DATE_ADD('".$convertDate."', INTERVAL 2 DAY) ORDER BY ji.interview_date";
    		$pdo = Doctrine_Manager::connection()->getDbh();
    		$res = $pdo->query($qry);
    		$currentinterviewDetails = $res->fetchAll();
    		return $currentinterviewDetails;
    	}catch (Exception $e) {
    		throw new DaoException($e->getMessage());
    	}
    }
    
    public function getPendingFeedbackHiringMgr($empNo,$convertDate){
    	try{
    		$qry ="SELECT  vhm.hiring_manager_id, jc.id, jv.name, jc.first_name, jc.last_name";
    		$qry .=" FROM `ohrm_job_vacancy_hiring_manager` AS vhm";
    		$qry .=" LEFT JOIN `ohrm_job_candidate_vacancy` AS jcv ON jcv.vacancy_id = vhm.vacancy_id";
    		$qry .=" LEFT JOIN `ohrm_job_vacancy` AS jv ON jcv.vacancy_id = jv.id";
    		$qry .=" LEFT JOIN `ohrm_job_candidate` AS jc ON jcv.candidate_id = jc.id";
    		$qry .=" LEFT JOIN `ohrm_job_interview` AS ji ON jc.id = ji.candidate_id";
    		$qry .=" WHERE  vhm.hiring_manager_id = ".$empNo." AND jcv.status = 'INTERVIEW SCHEDULED'";
    		$qry .=" AND  jc.is_deleted = 0 AND jv.status = 1";
    		$qry .=" AND CONCAT(ji.interview_date,' ',ji.interview_time) BETWEEN DATE_ADD('".$convertDate."', INTERVAL -1 MONTH) AND '".$convertDate."' GROUP BY jc.id ORDER BY ji.interview_date";
    		$pdo = Doctrine_Manager::connection()->getDbh();
    		$res = $pdo->query($qry);
    		$pendingFeedback = $res->fetchAll();
    		return $pendingFeedback;
    	}catch (Exception $e) {
    		throw new DaoException($e->getMessage());
    	}
    }
    
    public function getPendingFeedbackInterviewer($empNo,$convertDate){
    	try{ 
    		$qry  ="SELECT jii.interviewer_id, jc.id, jc.first_name, jc.last_name, jv.name";
    		$qry .=" FROM `ohrm_job_interview_interviewer` AS jii";
    		$qry .=" LEFT JOIN `ohrm_job_interview` AS ji ON jii.interview_id = ji.id";
    		$qry .=" LEFT JOIN `ohrm_job_candidate` AS jc ON ji.candidate_id = jc.id";
    		$qry .=" LEFT JOIN `ohrm_job_candidate_vacancy` AS jcv ON  jc.id = jcv.candidate_id";
    		$qry .=" LEFT JOIN `ohrm_job_vacancy` AS jv ON jcv.vacancy_id = jv.id";
    		$qry .=" WHERE jii.interviewer_id = ".$empNo." AND jcv.status = 'INTERVIEW SCHEDULED'";
    		$qry .=" AND  jc.is_deleted = 0 AND jv.status = 1";
    		$qry .=" AND CONCAT(ji.interview_date,' ',ji.interview_time) BETWEEN DATE_ADD('".$convertDate."', INTERVAL -1 MONTH) AND '".$convertDate."' GROUP BY jc.id ORDER BY ji.interview_date";
    		$pdo = Doctrine_Manager::connection()->getDbh();
    		$res = $pdo->query($qry);
    		$pendingFeedback = $res->fetchAll();
    		return $pendingFeedback;
    	}catch (Exception $e) {
    		throw new DaoException($e->getMessage());
    	}
    }
    
    public function performBulkReject($ids) {
    	try {
    		$count=0;
    		$date = date('Y-m-d');
    		$conn = Doctrine_Manager::connection();
    		$conn->beginTransaction();
    		$empNumber = sfContext::getInstance()->getUser()->getEmployeeNumber();
    		foreach ($ids as $candidateId) {
	    			$q= Doctrine_Query::create()
	    			->update('JobCandidateVacancy')
	    			->set('status','?','REJECTED')
	    			->where('candidateId = ?', $candidateId['id'])
                                ->andWhere('vacancyId= ?',$candidateId['vacancyId']);//added by Shagupta
	    			$q->execute();
	    			
	    			$candidateHistory = new CandidateHistory();
	    			$candidateHistory->setCandidateId($candidateId['id']);
	    			$candidateHistory->setVacancyId($candidateId['vacancyId']);
	    			$candidateHistory->setAction(WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_REJECT);
	    			$candidateHistory->setCandidateVacancyName($candidateId['vacancyName']);
	    			
	    			if ($empNumber == 0) {
	    				$empNumber = null;
	    			}
	    			$candidateHistory->setPerformedBy($empNumber);
	    			$candidateHistory->setPerformedDate($date . " " . date('H:i:s'));
	    			$this->saveCandidateHistory($candidateHistory);
	    			$count++;
    		}
    		$conn->commit();
    		return $count;
    	} catch (Exception $e) {
    		$conn->rollback();
    		return null;
    	}
    }
	
//    public function changeCandidateVacancy($mappingArray, $vacancyDetails, $interviewerArray) {
//    	try { 
//    		$addedby = sfContext::getInstance()->getUser()->getEmployeeNumber();
//    		$count=0;
//    		$flag = intval($vacancyDetails["flagForResume"]);
//    		if($flag)
//    			$status = JobCandidateVacancy::RECRUITMENT_CANDIDATE_STATUS_SCREENING;
//    		else
//    			$status = JobCandidateVacancy::RECRUITMENT_CANDIDATE_STATUS_APPLICATION_INITIATED;
//    		$date = date('Y-m-d')." ".date('H:i:s');
//    		$conn = Doctrine_Manager::connection();
//    		$conn->beginTransaction();
//    		
//    		foreach ($interviewerArray as $interviewer) {
//    			$query = Doctrine_Query::create()
//    			->delete('JobInterviewInterviewer')
//    			->where('interviewId = ?',$interviewer['interviewId']);
//    			$query->execute();
//    		}
//    		
//    		foreach ($mappingArray as $details) {
//    			$q = Doctrine_Query::create()
//    				->delete('JobCandidateVacancy')
//    				->where('candidateId = ?',$details['id']);
//    			$q->execute();
//    			
//    			$candidateHistory = new CandidateHistory();
//    			$candidateHistory->candidateId = $details['id'];
//    			$candidateHistory->action = WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_ATTACH_VACANCY;
//    			$candidateHistory->performedBy = $addedby;
//    			$date = date('Y-m-d');
//    			$candidateHistory->performedDate = $date . " " . date('H:i:s');
//    			$candidateHistory->candidateVacancyName = $vacancyDetails['name'];
//    			$candidateHistory->vacancyId = $vacancyDetails['id'];
//    			$this->saveCandidateHistory($candidateHistory);
//    			
//    			$candidateHistoryRemoved = new CandidateHistory();
//    			$candidateHistoryRemoved->candidateId = $details['id'];
//    			$candidateHistoryRemoved->action = CandidateHistory::RECRUITMENT_CANDIDATE_ACTION_REMOVE;
//    			$candidateHistoryRemoved->performedBy = $addedby;
//    			$date = date('Y-m-d');
//    			$candidateHistoryRemoved->performedDate = $date . " " . date('H:i:s');
//    			$candidateHistoryRemoved->candidateVacancyName = $details['name'];
//    			$candidateHistoryRemoved->vacancyId = $details['vacancyId'];
//    			$this->saveCandidateHistory($candidateHistoryRemoved);
//    			
//    			$candidateVacancy = new JobCandidateVacancy();
//    			$candidateVacancy->candidateId = $details['id'];
//    			$candidateVacancy->vacancyId = $vacancyDetails['id'];
//    			$candidateVacancy->status = $status;
//    			$candidateVacancy->appliedDate = date('Y-m-d');
//    			$this->saveCandidateVacancy($candidateVacancy);
//    			$count++;
//    		}
//    		
//    		$conn->commit();
//    		return $count;
//    	} catch (Exception $e) {
//    		$conn->rollback();
//    		return null;
//    	}
//    }
    
    public function changeCandidateVacancy($changeVacancyDetails, $fromPage) {
	    try {
	    	
	    	$count=0;
	    	 
		    $addedby = sfContext::getInstance()->getUser()->getEmployeeNumber();
		    $date = date('Y-m-d')." ".date('H:i:s');
		    
		    //Open the connection
		    $conn = Doctrine_Manager::connection();
		    $conn->beginTransaction();
		    
		    foreach ( $changeVacancyDetails as $changeDetails ) {
			    $flag = intval($changeDetails["newVacancyFlagForResume"]);
			    if($flag)
				    $status = JobCandidateVacancy::RECRUITMENT_CANDIDATE_STATUS_SCREENING;
			    else
				    $status = JobCandidateVacancy::RECRUITMENT_CANDIDATE_STATUS_APPLICATION_INITIATED;
			    
			    $candidateVacancy = $changeDetails['oldCandidateVacancy'];//'$this->getVacancyService()->getVacancyByCandidateId( $request->getCandidateId(), true);
			    $interviews = $changeDetails['oldInterviews'];//$this->getInterviewService()->getInterviewsByCandidateVacancyId($candidateVacancy);
			    foreach ($interviews as $interview) {
				    $interviewers = $interview->getJobInterviewInterviewer();
				    foreach ($interviewers as $interviewer) {
					    $interviewer->delete();
				    }
			    }

			    $q = Doctrine_Query::create()
				    ->delete('JobCandidateVacancy')
					->where('candidateId = ?',$changeDetails['candidateId']);
			    $q->execute();
			  	
			  	//Now save Added new vacancy History
			    $candidateHistory = new CandidateHistory();
			    $candidateHistory->candidateId = $changeDetails['candidateId'];
			    $candidateHistory->action = WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_ATTACH_VACANCY;
			    $candidateHistory->performedBy = $addedby;
			    $candidateHistory->performedDate = $date;
			    $candidateHistory->candidateVacancyName = $changeDetails['newVacancyName'];
			    $candidateHistory->vacancyId = $changeDetails['newVacancyId'];
			    $this->saveCandidateHistory($candidateHistory);
			    
			    //First save remove vacancy history
			  	$candidateHistoryRemoved = new CandidateHistory();
			    $candidateHistoryRemoved->candidateId = $changeDetails['candidateId'];
			    $candidateHistoryRemoved->action = WorkflowStateMachine::RECRUITMENT_CANDIDATE_ACTION_REMOVE;
			    $candidateHistoryRemoved->performedBy = $addedby;
			    $candidateHistoryRemoved->performedDate = $date;
			    $candidateHistoryRemoved->candidateVacancyName = $changeDetails['oldVacancyName'];
			    $candidateHistoryRemoved->vacancyId = $changeDetails['oldVacancyId'];
			    $this->saveCandidateHistory($candidateHistoryRemoved);  
			  
			   	// Now save candidate vacancy details.
			    $candidateVacancy = new JobCandidateVacancy();
			    $candidateVacancy->candidateId = $changeDetails['candidateId'];
			    $candidateVacancy->vacancyId = $changeDetails['newVacancyId'];
			    $candidateVacancy->status = $status;
			    $candidateVacancy->appliedDate = date('Y-m-d');
			    $this->saveCandidateVacancy($candidateVacancy);
			    
			    $count++;
		    }
		    $conn->commit();
		    return $count;
	    } catch (Exception $e) {
		    $conn->rollback();
		    throw new DaoException($e->getMessage());
		    
	    }
    }

    
    /**
     * 
     */
    public function searchAgencyCandidatesReferred() {
		try {$empNumber=sfContext::getInstance()->getUser()->getEmployeeNumber();
    	    //print_r($empNumber);
    		$query = "SELECT ojv.name, ojcv.applied_date,";
    		$query .= " SUM(IF(ojcv.status = 'REJECTED', 1, 0)) AS countRejected,SUM(IF(ojcv.status = 'APPLICATION INITIATED', 1, 0)) AS countAI, SUM(IF(ojcv.status = 'SCREENING', 1, 0)) AS countScreening,";
    		$query .=" SUM(IF(ojcv.status = 'INTERVIEW SCHEDULED', 1, 0)) AS countIS, SUM(IF(ojcv.status = 'SHORTLISTED', 1, 0)) AS countShortlisted, SUM(IF(ojcv.status LIKE '%HOLD%', 1, 0)) AS countHold, SUM(IF(ojcv.status = 'INTERVIEW FAILED', 1, 0)) AS countIF,";
    		$query .="SUM(IF(ojcv.status = 'INTERVIEW PASSED', 1, 0)) AS countIP, SUM(IF(ojcv.status = 'JOB OFFERED', 1, 0)) AS countJobOffered";
    		$query .=" FROM ohrm_job_vacancy ojv, ohrm_job_candidate_vacancy ojcv, ohrm_job_candidate ojc WHERE ojc.added_person IN (".$empNumber.") AND ojc.id =ojcv.candidate_id AND  ojcv.vacancy_id= ojv.id AND ojv.status = 1 GROUP BY ojv.name";
    	
    		$pdo = Doctrine_Manager::connection()->getDbh();
    		$res1 = $pdo->query($query);
    		$empVacancyList = $res1->fetchAll();
    		  		
    		return $empVacancyList;
		} catch ( Exception $e ) {
			throw new DaoException ( $e->getMessage () );
		}
	}
    
	/**
	 *
	 */
	public function getCountsSarchAgencyCandidatesReferred() {
		try {
			$empNumber = sfContext::getInstance ()->getUser ()->getEmployeeNumber ();
			$query = "SELECT count(ojv.name) as countTotal";
			$query .= " FROM ohrm_job_vacancy ojv, ohrm_job_candidate_vacancy ojcv, ohrm_job_candidate ojc WHERE ojc.added_person IN (" . $empNumber . ") AND ojc.id =ojcv.candidate_id AND  ojcv.vacancy_id= ojv.id AND ojv.status = 1 GROUP BY ojv.name";
			$pdo = Doctrine_Manager::connection ()->getDbh ();
			$res1 = $pdo->query ( $query );
			$empVacancyList = $res1->fetchAll ();
			$count = count ( $empVacancyList );
			return $count;
		} catch ( Exception $e ) {
			throw new DaoException ( $e->getMessage () );
		}
	}

    /**
     * Retrieve candidate by email
     * @param string email
     * @returns jobCandidate doctrine object
     * @throws DaoException
     */
    public function getCandidateByEmail($email,$id=null) {
        try {
            if($id == null) {
              return Doctrine::getTable('JobCandidate')->findByEmailAndIsDeleted($email,JobCandidate::IS_NOT_DELETED)->toArray();   
            }                
            else 
            {
             return Doctrine::getTable('JobCandidate')->createQuery()
            ->select('id')
            ->where('id != ?', $id)      
            ->andWhere('email = ?',$email)
            ->andWhere('isDeleted = ?',JobCandidate::IS_NOT_DELETED)
            ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);             
            }           
            
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
   /* Added By : Shagupta Faras
    * Added On :14-07-2014
    * DESC: function to check newly added vacancy is present on db
    */
    public function isExistCandidateVacancy($candidate,$vacancyId) {
    $candidateVacancyObj=Doctrine::getTable('JobCandidateVacancy')->createQuery()->where('candidate_id='.$candidate->getId())->andWhere('vacancy_id='.$vacancyId)->execute();  
    $candidateVacancyObj->count();
    if($candidateVacancyObj->count()>0)
       return true;
    else 
       return false;
    }
    
    /**
     * Retrieve candidateAttachment by candidateId
     * @param int $candidateId
     * @returns jobCandidate doctrine object
     * @throws DaoException
     */
    public function getCandidateAttachment($candidateId) {
        try {
            return Doctrine :: getTable('JobCandidateAttachment')->findOneByCandidateId($candidateId);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    /**
     * Retrieve candidate Vacancies by candidateId
     * @param int $candidateId
     * @returns jobCandidate doctrine object
     * @throws DaoException
     */
    public function getCandidateVacancy($candidateId) {
        try {
            return Doctrine :: getTable('JobCandidateVacancy')->findByCandidateId($candidateId);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    /**
     * Retrieve candidate object by candidateId And vacanchyId
     * @param int $candidateId
     * @returns jobCandidate doctrine object
     * @throws DaoException
     */
    public function getCandidateByCandidateIdAndVacancyId($candidateId,$vacancyId){
        try {
            return Doctrine::getTable('JobCandidateVacancy')->findOneByCandidateIdAndVacancyId($candidateId,$vacancyId);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    /**
     * Retrieve only vacancies by candidateId in singular array formate
     * @param int $candidateId
     * @returns jobCandidate doctrine object
     * @throws DaoException
     */
    public function getVacanciesInArrayByCandidateId($candidateId) {
        try {
            return Doctrine::getTable('JobCandidateVacancy')->createQuery()
            ->select('vacancyId')
            ->where('candidateId = ?', $candidateId)           
            ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);  
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    } 
    /**
     *
     * @param JobCandidateVacancy $candidateVacancy
     * @return <type>
     */
    public function updateCandidatesOtherVacancies(JobCandidateVacancy $candidateVacancy,$status,$action,$noEffectOnRejected=false) {
        try {
           $q=Doctrine::getTable('JobCandidateVacancy')->createQuery()
                   ->select('*')
                    ->where('id !=?', $candidateVacancy->id)
                    ->andWhere('candidateId =?',$candidateVacancy->candidateId);
                    if($noEffectOnRejected){
                    $q->andWhere('status !=?',PluginJobCandidateVacancy::RECRUITMENT_CANDIDATE_STATUS_REJECTED);
                    }
            $obj= $q->execute(array());
            
            $qUpdate = Doctrine_Query:: create()->update('JobCandidateVacancy')
                    ->set('status', '?', $status)
                    ->where('id !=?', $candidateVacancy->id)
                    ->andWhere('candidateId =?',$candidateVacancy->candidateId);
                    if($noEffectOnRejected){
                    $qUpdate->andWhere('status !=?',PluginJobCandidateVacancy::RECRUITMENT_CANDIDATE_STATUS_REJECTED);
                    }
            $final= $qUpdate->execute();
            if(!empty($obj))
               {
                   foreach($obj as $result){
                    $candidateHistory = new CandidateHistory();
                    $candidateHistory->setCandidateId($result->candidateId);
                    $candidateHistory->setVacancyId($result->vacancyId);
                    $candidateHistory->setAction($action);
                    $candidateHistory->setCandidateVacancyName($result->getVacancyName());
                    $empNumber = sfContext::getInstance()->getUser()->getEmployeeNumber();
                    if ($empNumber == 0) {
                        $empNumber = null;
                    }
                    $candidateHistory->setPerformedBy($empNumber);
                    $date = date('Y-m-d');
                    $candidateHistory->setPerformedDate($date . " " . date('H:i:s'));
                    $candidateHistory->setNote('SYSTEM GENERATED EFFECT['.$status.'] DUE TO MULTIPLE VACANCY');
                    $resultHistory = $this->saveCandidateHistory($candidateHistory);
                    
                   }
               }
            return true;
		} catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    /**
     * Retrieve candidate by contactNumber
     * @param string contactNumber
     * @returns jobCandidate doctrine object
     * @throws DaoException
     */
    public function getCandidateByContactNumber($contactNumber,$id=null) {
        try {
            if($id == null) {
              return Doctrine::getTable('JobCandidate')->findByContactNumberAndIsDeleted($contactNumber,JobCandidate::IS_NOT_DELETED)->toArray();   
            }                
            else 
            {
             return Doctrine::getTable('JobCandidate')->createQuery()
            ->select('id')
            ->where('id != ?', $id)      
            ->andWhere('contactNumber = ?',$contactNumber)      
            ->andWhere('isDeleted = ?',JobCandidate::IS_NOT_DELETED)         
            ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
            }           
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function isOnlyInterviewer($candidateId)
    { 
       $isOnlyInterviewer=false;       
       if($_SESSION['isAdmin']=='Yes'){
         return $isOnlyInterviewer;        
        } else {
             if(empty($_SESSION['isHiringManager']) && $_SESSION['isInterviewer']==1)
                 return true; 
        } 
       
        if(!empty($_SESSION['isHiringManager'])){            
         $vacancy=$this->getVacanciesInArrayByCandidateId($candidateId);
         if(!empty($vacancy)) {
          $loggedInId=$_SESSION['empNumber'];
          
          if(count($vacancy)>1)
           $vacancies=implode(",",$vacancy);     
          else
            $vacancies=$vacancy;
           try {
                $obj=Doctrine::getTable('JobVacancyHiringManager')
                    ->createQuery()
                    ->select('vacancyId')
                    ->where('vacancyId in ('.$vacancies.')') 
                    ->andWhere('hiringManagerId=?',$loggedInId)
                    ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
                   if(count($obj)>0)
                    {
                     $isOnlyInterviewer=false;
                    }
                    else
                    {
                     $isOnlyInterviewer=true;
                    }
                } catch (Exception $e) {
                  throw new DaoException($e->getMessage());
                }
                return $isOnlyInterviewer;
         
       }
     }//hiring
    }
}
