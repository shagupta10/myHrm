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
 * VacancyDao for CRUD operation
 *
 */
class VacancyDao extends BaseDao {

    /**
     * Retrieve hiring managers list
     * @returns array
     * @throws DaoExceptionf
     */
    public function getHiringManagersList($jobTitle, $vacancyId, $allowedVacancyList=null) {
        try {
            $q = Doctrine_Query::create()
                            ->select('e.empNumber, e.firstName, e.middleName, e.lastName, e.termination_id')
                            ->from('Employee e')
                            ->leftJoin('e.JobVacancyHiringManager jhm')
                            ->leftJoin('jhm.JobVacancy jv');
            if ($allowedVacancyList != null) {
                $q->whereIn('jv.id', $allowedVacancyList);
            }
            if (!empty($jobTitle)) {
                $q->addWhere('jv.jobTitleCode = ?', $jobTitle);
            } if (!empty($vacancyId)) {
                $q->addWhere('jv.id = ?', $vacancyId);
            }
            $q->addWhere('e.termination_id IS NULL');
            $q->orderBy('e.firstName ASC, e.lastName ASC');
            $results = $q->fetchArray();

            $hiringManagerList = array();
            
            foreach ($results as $result) {
                $hiringManagerList[] = array('id' =>  $result['empNumber'], 
                                             'name' => trim(trim($result['firstName'] ) . ' ' .$result['lastName']));
            }
            
            return $hiringManagerList;            
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Retrieve vacancy list for a purticular job title & status
     * @returns doctrine collection
     * @throws DaoException
     */
    public function getVacancyListForJobTitle($jobTitle, $allowedVacancyList, $asArray = false, $status) {
        try {
            $hydrateMode = ($asArray) ? Doctrine :: HYDRATE_ARRAY : Doctrine :: HYDRATE_RECORD;

            $q = Doctrine_Query :: create()
                            ->select('jv.id, jv.name, jv.status')
                            ->from('JobVacancy jv');
            if ($allowedVacancyList != null) {
                $q->whereIn('jv.id', $allowedVacancyList);
            }
            if (!empty($jobTitle)) {
                $q->addWhere('jv.jobTitleCode =?', $jobTitle);
            }
            if (!empty($status)) {
                $q->addWhere('jv.status =?', $status);
            }
            $q->orderBy('jv.name ASC');
            return $q->execute(array(), $hydrateMode);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function getVacancyListForUserRole($role, $empNumber) {
        try {
            $q = Doctrine_Query :: create()
                            ->select('jv.id')
                            ->from('JobVacancy jv')
                            ->leftJoin('jv.JobVacancyHiringManager vhm');
            if ($role == HiringManagerUserRoleDecorator::HIRING_MANAGER) {
                $q->where('vhm.hiringManagerId = ?', $empNumber);
               // $q->orwhere('jv.hiringManager1Id = ?', $empNumber);
            }
            if ($role == InterviewerUserRoleDecorator::INTERVIEWER) {
                $q->leftJoin('jv.JobCandidateVacancy jcv')
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

    /**
     * Retrieve vacancy list
     * @returns doctrine collection
     * @throws DaoException
     */
    public function getAllVacancies($status = "") {
        try {
            $q = Doctrine_Query :: create()
                            ->from('JobVacancy');
            if (!empty($status)) {
                $q->addWhere('status =?', $status);
            }
            $q->orderBy('name ASC');
            
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    
    /**
     * Return an array of vacancy properties
     * 
     * @version 2.7.1
     * @param Array $properties List of Vacancy properties
     * @param Integer $status Vacancy Status
     * @returns Array Vacancy Property List
     * @throws DaoException
     */
    public function getVacancyPropertyList($properties, $status) {
        try {
            
            $q = Doctrine_Query :: create()
                            ->from('JobVacancy');
                            
            foreach ($properties as $property) {
                $q->addSelect($property);
            }
            
            if (!empty($status)) {
                $q->addWhere('status =?', $status);
            }

             $q->orderBy('name ASC');
 
            return $q->fetchArray();
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * Get list of vacancies published to web/rss
     * 
     * @return type Array of JobVacancy objects
     * @throws RecruitmentException
     */
    public function getPublishedVacancies() {
        try {
            $q = Doctrine_Query :: create()
                            ->from('JobVacancy')
                            ->where('published_in_feed = ? ', JobVacancy::PUBLISHED)
                            ->andWhere('status = ?', JobVacancy::ACTIVE)
                            ->orderBy('name ASC');
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Retrieve vacancy list
     * @returns doctrine collection
     * @throws DaoException
     */
    public function getVacancyList($status=JobVacancy::ACTIVE, $limit=50, $offset=0, $orderBy='name', $order='ASC', $publishedInFeed=JobVacancy::PUBLISHED) {
        try {
            $q = Doctrine_Query :: create()
                            ->from('JobVacancy')
                            ->where('status =?', $status)
                            ->andWhere('publishedInFeed=?', $publishedInFeed)
                            ->orderBy($orderBy . " " . $order)
                            ->offset($offset)
                            ->limit($limit);
            return $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Retrieve vacancy list
     * @returns doctrine collection
     * @throws DaoException
     */
    public function saveJobVacancy(JobVacancy $jobVacancy) {
        try {

            if ($jobVacancy->getId() == '') {
                $idGenService = new IDGeneratorService();
                $idGenService->setEntity($jobVacancy);
                $jobVacancy->setId($idGenService->getNextID());
            }

            $jobVacancy->save();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param <type> $srchParams
     * @return <type>
     */
    public function searchVacancies($srchParams) {

        $jobTitle = $srchParams['jobTitle'];
        $jobVacancy = $srchParams['jobVacancy'];

        $hiringManager = $srchParams['hiringManager'];
       // if($srchParams['hiringManager']=='')
        //	$hiringManager = 1;
        $hiringManager1 = $srchParams['hiringManager1'];
        $status = $srchParams['status'];
        $keyWords = $srchParams['keyWords'];
        $orderField = (!empty($srchParams['orderField'])) ? $srchParams['orderField'] : 'v.name';
        $orderBy = (!empty($srchParams['orderBy'])) ? $srchParams['orderBy'] : 'ASC';
        $noOfRecords = $srchParams['noOfRecords'];
        $offset = $srchParams['offset'];

        $sortQuery = "";
        if ($orderField == 'e.emp_firstname') {
            $sortQuery = 'e.emp_firstname ' . $orderBy . ', ' . 'e.emp_lastname ' . $orderBy;
        } else {
            $sortQuery = $orderField . " " . $orderBy;
        }

//        $q = Doctrine_Query::create()
//                        ->from('JobVacancy v')
//                        ->leftJoin('v.Employee e')
//                        ->leftJoin('v.JobTitle jt')
//                        ->leftJoin('v.JobVacancyHiringManager vhm');
        
        $q = Doctrine_Query::create()
                            ->from('JobVacancy v')
                            ->leftJoin('v.JobTitle jt')
                            ->leftJoin('v.JobVacancyHiringManager vhm')
                            ->leftJoin('vhm.Employee e');

        if (!empty($jobTitle)) {
            $q->addwhere('v.jobTitleCode = ?', $jobTitle);
        }
        if (!empty($jobVacancy)) {
            $q->addwhere('v.id = ?', $jobVacancy);
        }

        if (!empty($hiringManager) && $hiringManager != '') {

            $q->addwhere('vhm.hiringManagerId = ?', $hiringManager);
        }
        //var_dump($hiringManager);
      // exit;
        if ($status != "") {
            $q->addwhere('v.status = ?', $status);
        }
        if ($keyWords != "") {
            $arrKeyWords = explode(',', $keyWords);
            $string = '';
            foreach ($arrKeyWords as $keyWord){
                $string .= (!empty($keyWord) && trim($keyWord) != null) ?' v.description LIKE "%'. mysql_real_escape_string(trim($keyWord)) . '%" OR ':'';
            }
            $string = (!empty($string) && $string != '')? substr($string, 0, -3):'1';
            $q->addwhere($string);
        }
        $q->orderBy($sortQuery);
        $q->offset($offset);
        $q->limit($noOfRecords);

        $vacancies = $q->execute();
        return $vacancies;
    }

    /**
     *
     * @param <type> $srchParams
     * @return <type>
     */
    public function searchVacanciesCount($srchParams) {
        try {
            
            $jobTitle = $srchParams['jobTitle'];
            $jobVacancy = $srchParams['jobVacancy'];
            $hiringManager = $srchParams['hiringManager'];
            $status = $srchParams['status'];
            $keyWords = $srchParams['keyWords'];
            $q = Doctrine_Query::create()
                            ->from('JobVacancy v')
                            ->leftJoin('v.JobTitle jt')
                            ->leftJoin('v.JobVacancyHiringManager vhm')
                            ->leftJoin('vhm.Employee e');
                            
            if (!empty($jobTitle)) {
                $q->addwhere('v.jobTitleCode = ?', $jobTitle);
            }
            if (!empty($jobVacancy)) {
                $q->addwhere('v.id = ?', $jobVacancy);
            }
            if (!empty($hiringManager)) {
                $q->addwhere('vhm.hiringManagerId = ?', $hiringManager);
            }
            if ($status != "") {
                $q->addwhere('v.status = ?', $status);
            }
            if ($keyWords != "") {
                $arrKeyWords = explode(',', $keyWords);
                $string = '';
                foreach ($arrKeyWords as $keyWord){
                    $string .= (!empty($keyWord) && trim($keyWord) != null) ?' v.description LIKE "%'. mysql_real_escape_string(trim($keyWord)) . '%" OR ':'';
                }
                $string = (!empty($string) && $string != '')? substr($string, 0, -3):'1';
                $q->addwhere($string);
            }
    		$vacancies = $q->execute();
            return count($vacancies);
            
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }


    /**
     * Retrieve vacancy by vacancyId
     * @param int $vacancyId
     * @returns jobVacancy doctrine object
     * @throws DaoException
     */
    public function getVacancyById($vacancyId) {
        try {
            return Doctrine :: getTable('JobVacancy')->find($vacancyId);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Delete vacancies
     * @param array $toBeDeletedVacancyIds
     * @return boolean
     */
    public function deleteVacancies($toBeDeletedVacancyIds) {

        $q = Doctrine_Query::create()
                        ->from('JobInterviewInterviewer jii')
                        ->leftJoin('jii.JobInterview ji')
                        ->leftJoin('ji.JobCandidateVacancy jcv')
                        ->leftJoin('jcv.JobVacancy jv')
                        ->leftJoin('jv.JobVacancyHiringManager jvhm')
                        ->whereIn('jv.id', $toBeDeletedVacancyIds);
        $results = $q->execute();
        foreach ($results as $result) {
            $result->delete();
        }
 	$qr1 = Doctrine_Query::create()
                        ->delete()
                        ->from('JobVacancyHiringManager vhm')
                        ->whereIn('vhm.vacancy_id', $toBeDeletedVacancyIds);
        $qr1->execute();
        $qr = Doctrine_Query::create()
                        ->delete()
                        ->from('JobVacancy v')
                        ->whereIn('v.id', $toBeDeletedVacancyIds);
       $noOfAffectedRows = $qr->execute();
       

        if ($noOfAffectedRows > 0) {
            return true;
        }

        return false;
    }    
    
    /**
     *
     * @param type $empNumber 
     * @return Doctrine_Collection
     */
    public function searchInterviews($empNumber) {
        try {
            $query = Doctrine_Query::create()
                    ->from('JobInterview ji')
                    ->where('ji.JobInterviewInterviewer.interviewerId = ?', $empNumber);
            return $query->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    
    public function searchJobVacancies($empNumber){
    	 try {
            $query = Doctrine_Query::create()
                    ->from('JobVacancyHiringManager jv')
                    ->where('jv.hiringManagerId = ?', $empNumber);
                  // ->orwhere ('jv.hiringManager1Id = ?', $empNumber);
            return $query->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    
    public function getVacancyPropertyListReferred($empNo) {
    	try {
    
    		$query = "select jv.name as name ,jv.id as id from ohrm_job_vacancy jv ";
    		$query.= "left join ohrm_job_candidate_vacancy jcv on jv.id=jcv.vacancy_id ";
    		$query.= "left join ohrm_job_candidate jc on jcv.candidate_id=jc.id ";
    		$query.= "where jc.added_person = ". "'{$empNo}'";
    		$pdo = Doctrine_Manager::connection()->getDbh();
    		$res = $pdo->query($query);
    		$vacancyList = $res->fetchAll();
    		return $vacancyList;
    		// @codeCoverageIgnoreStart
    	} catch (Exception $e) {
    		throw new DaoException($e->getMessage(), $e->getCode(), $e);
    	}
    	// @codeCoverageIgnoreEnd
    }
    
    
    public function getVacancyDescriptionById($id) {
    	try {
    		$query = Doctrine_Query::create()
    		->from('JobVacancy')
    		->where('id = ?', $id);
    		return $query->execute();
    	
    		// @codeCoverageIgnoreStart
    	} catch (Exception $e) {
    		throw new DaoException($e->getMessage(), $e->getCode(), $e);
    	}
    	// @codeCoverageIgnoreEnd
    }
    
    
    public function getActiveCandidateCountByVacancyId($id) {
    	 try {
    		 $pdo = Doctrine_Manager::connection()->getDbh();
             $res = $pdo->query('SELECT COUNT(*) FROM ohrm_job_candidate_vacancy WHERE vacancy_id = '.$id.' AND  status IN ("SHORTLISTED","INTERVIEW SCHEDULED","INTERVIEW PASSED","INTERVIEW FAILED","JOB OFFERED")');
			 $counts = $res->fetch();
    		 return intval($counts[0]);
    		// @codeCoverageIgnoreStart
    	} catch (Exception $e) {
    		throw new DaoException($e->getMessage(), $e->getCode(), $e);
    	} 
    	//return null;
    	// @codeCoverageIgnoreEnd
    }
    
    
    public function getVacancyByCandidateId($id) {
    	try {
    			$query = Doctrine_Query::create()
    			->from('JobCandidateVacancy')
    			->where('candidateId = ?', $id);
    			return $query->fetchOne();
    			// @codeCoverageIgnoreStart
    	} catch (Exception $e) {
    		throw new DaoException($e->getMessage(), $e->getCode(), $e);
    	}
    	//return null;
    	// @codeCoverageIgnoreEnd
    }
}
