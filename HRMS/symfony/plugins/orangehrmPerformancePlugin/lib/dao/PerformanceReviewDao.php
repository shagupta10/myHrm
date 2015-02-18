<?php
/* 
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

/**
 * PerformanceReview Dao class 
 *
 * @author Samantha Jayasinghe
 */
class PerformanceReviewDao extends BaseDao {
	
    /**
     * Save Performance Review
     * @param PerformanceReview $performanceReview
     * @return PerformanceReview
     */
    public function savePerformanceReview(PerformanceReview $performanceReview, $performanceReviewerList=array(), $removedReviewerList=array(), $primaryReviewerId=null ) {
    	$conn = Doctrine_Manager::connection();
	    $conn->beginTransaction();
        try {
        	
        	//First save the review
            if ( $performanceReview->getId() == '') {
                $idGenService = new IDGeneratorService( );
                $idGenService->setEntity($performanceReview);
                $performanceReview->setId($idGenService->getNextID());
                $performanceReview->setIsDeleted(PerformanceReviewReviewer::IS_NOT_DELETED);
            }
            if($performanceReview->getEmployeeId()){
                $performanceReview->save();
            }else{
                return false;
                //throw new DaoException ("Employee Not Found.");
            }
            
            $primaryToUpdate = true;
            //First update Primary Reviewer
            if(!is_null($primaryReviewerId)){
                $existingPrimaryReviewer = $performanceReview->getPrimaryReviewer();
                if(!is_null($existingPrimaryReviewer)){
                    if($existingPrimaryReviewer->getReviewerId() != $primaryReviewerId) {
                    	$this->deleteReviewer($performanceReview->getId(),$existingPrimaryReviewer->getReviewerId(),true);
                   }else{
                        $primaryToUpdate = false;
                    }
                }
                
                if($primaryToUpdate){
                    $reviewID = $performanceReview->getId();
                    $reviewerID = $primaryReviewerId;
                    $isReviewerExist = $this->isReviewerExist($reviewID,$reviewerID);
                    if($isReviewerExist) {
                        $this->updateReviewer($reviewID,$reviewerID,true);
                    }else{
                        $performanceReviewer = new PerformanceReviewReviewer();
                        $performanceReviewer->setKpis($performanceReview->getKpis());
                        $performanceReviewer->setReviewId($performanceReview->getId());
                        $performanceReviewer->setReviewerId($primaryReviewerId);
                        $performanceReviewer->setIsPrimary(PerformanceReviewReviewer::IS_PRIMARY_REVIEWER);
                        $performanceReviewer->setIsDeleted(PerformanceReviewReviewer::IS_NOT_DELETED);
                        $performanceReviewer->save();
                    }
                }
            }
           	// Save Reviewers 
            if(count($performanceReviewerList) > 0){
                foreach($performanceReviewerList as $performanceReviewer){
                    $reviewID = $performanceReview->getId();
                    $reviewerID = $performanceReviewer->getReviewerId();
                    $isReviewerExist = $this->isReviewerExist($reviewID,$reviewerID);
                    
                    if($isReviewerExist) {
                        $this->updateReviewer($reviewID,$reviewerID);
                    }else{	                
                        $performanceReviewer->setReviewId($reviewID);
                        $performanceReviewer->save();
                    }
                }
            }
            //Remove reviewers
			if(count($removedReviewerList) > 0) {
	             foreach ($removedReviewerList as $id) {
	            	$this->deleteReviewer($performanceReview->getId(),$id);
	            }
			}
			
			$performanceReview->refresh(true);
            $conn->commit();
            return $performanceReview;
        } catch (Exception $e) {
        	$conn->rollback();
            throw new DaoException ( $e->getMessage () );
        }
    }
    
    public function isReviewerExist($reviewID,$reviewerID){
        $q = Doctrine_Query::create()
	    	->from('PerformanceReviewReviewer pr')
	    	->select('COUNT(pr.id) as cnt')
	    	->where('pr.reviewId = ?', $reviewID)
			->andWhere('pr.reviewerId = ?', $reviewerID);
        
		$data = $q->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
		return ($data > 0)? true : false;
    }
    
    public function updateReviewer($reviewId, $reviewerId, $updatePrimary = false){
        try{
            $qdelete = Doctrine_Query::create()
                ->update('PerformanceReviewReviewer')
                ->set('isDeleted', '?', PerformanceReviewReviewer::IS_NOT_DELETED)
                ->Where('reviewId = ?', $reviewId)
                ->andWhere('reviewerId = ?', $reviewerId);
            if($updatePrimary){
                $qdelete->set('isPrimary','?', PerformanceReviewReviewer::IS_PRIMARY_REVIEWER);
            }else{
                $qdelete->set('isPrimary', '?', PerformanceReviewReviewer::IS_NOT_PRIMARY_REVIEWER);
            }
            return $qdelete->execute();
        }catch(Exception $e){
            throw new DaoException ( $e->getMessage () );
        }
    }
    
    public function deleteReviewer($reviewId, $reviewerId, $updatePrimary = false){
	    try{
		    $qdelete = Doctrine_Query::create()
			    ->update('PerformanceReviewReviewer')
				->set('isDeleted', '?', PerformanceReviewReviewer::IS_DELETED)
				->Where('reviewId = ?', $reviewId)
				->andWhere('reviewerId = ?', $reviewerId);
		    if($updatePrimary){
			    $qdelete->andWhere('isPrimary = ?', PerformanceReviewReviewer::IS_PRIMARY_REVIEWER);
		    }else{
		    	 $qdelete->andWhere('isPrimary = ?', PerformanceReviewReviewer::IS_NOT_PRIMARY_REVIEWER);
		    }
		    return $qdelete->execute();
	    }catch(Exception $e){
		    throw new DaoException ( $e->getMessage () );
	    }
    }
    
      /**
     * Delete PerformanceReview
     * @param array reviewList
     * @returns boolean
     * @throws PerformanceServiceException
     */
    public function deletePerformanceReview($reviewList) {
        try {
	        $q = Doctrine_Query::create()
		        ->update('PerformanceReview')
				->set('isDeleted', '?', PerformanceReviewReviewer::IS_DELETED)
				->whereIn('id', $reviewList);
	        $numDeleted = $q->execute();
	        if($numDeleted > 0) {
		        return true ;
	        }
	        return false;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    
    
 	/**
     * Read Performance Review
     * @param $reviewId
     * @return PerformanceReview
     */
    public function readPerformanceReview($parameters=array(), $orderby=null) {    	
        try {
        	$query = Doctrine_Query:: create()->from('PerformanceReview p');
        	$query->leftJoin("p.Employee e");
        	$query->leftJoin('p.PerformanceReviewReviewer r');
        	if (isset($parameters['reviewerId']) && $parameters['reviewerId'] > 0) {
        		$query->andWhere('r.reviewerId = ?', $parameters['reviewerId']);
        	}
            if (!empty($parameters)) {
                if (isset($parameters['id']) && $parameters['id'] > 0) {
                    $query->andWhere('id = ?', $parameters['id']);
                    return $query->fetchOne();
                } else {
                    foreach ($parameters as $key => $parameter) {
                        if (is_array($parameter) || strlen(trim($parameter)) > 0) {
                            switch ($key) {
                                case 'employeeName':
                                    $query->andWhere("CONCAT(e.emp_firstname,IF(LENGTH(e.emp_middle_name)>0,' ',''),e.emp_middle_name,' ',e.emp_lastname) LIKE ?", "%" . $parameter . "%");
                                    break;
                                case 'jobTitleCode':
                                    $query->andWhere('e.job_title_code = ?', $parameter);
                                    break;
                                case 'from':
                                    $query->andWhere('dueDate >= ?', $parameter);
                                    break;
                                case 'to':
                                    $query->andWhere('dueDate <= ?', $parameter);
                                    break;
                                case 'employeeNumber':
                                    $query->andWhere('e.empNumber = ?', $parameter);
                                    break;
                                case 'status':
                                    $query->andWhereIn('p.state', $parameter);
                                    break;
                                case 'employeeNotIn':
                                    $query->andWhereNotIn('e.empNumber', $parameter);
                                    break;
                                case 'limit':
                                	if ($parameter != null) {
                                		$query->limit($parameter);
                                	}
                                	break;
                                case 'page':
                                	if(isset($parameters['limit']) && $parameters['limit'] != null){
                                		$parameters['limit'] = 10;
                                	}
                                    $offset = ($parameter > 0) ? (($parameter - 1) * $parameters['limit']) : 0;
                                    $query->offset($offset);
                                    break;                           
                                default:
                                    break;
                            }
                        }
                    }
                }
            }
            if ($orderby['orderBy'] == null) {
            	$sortFeild = 'e.emp_firstname';
            }
            
            if ($orderby['orderBy'] == 'employeeId') {
            	$sortFeild = "e.emp_firstname";
            }
            
            if ($orderby['orderBy'] == 'dueDate') {
            	$sortFeild = "dueDate";
            }
            
            $sortBy = strcasecmp($orderby['sortOrder'], 'DESC') === 0 ? 'DESC' : 'ASC';
            
            $query->orderBy($sortFeild . ' ' . $sortBy);
            
            $performanceReview = $query->execute();
            return $performanceReview;
        } catch(Exception $e) {
            throw new DaoException ( $e->getMessage () );
        }
    }
    
   
	/**
	 * Get performance review by date.
	 */
    public function getPerformanceReviewsByDate($fromDate, $toDate, $employeeId = null){
    	try
        {	
	    	$q = Doctrine_Query::create()
		    	->from('PerformanceReview pr')
		    	->where('pr.isDeleted = 0')
				->andwhere('pr.periodFrom = ?',date('Y-m-d',strtotime($fromDate)))
				->andWhere('pr.periodTo = ?',date('Y-m-d',strtotime($toDate)));
	    	
	    	if(!is_null($employeeId)){
		    	$q->addWhere('pr.employeeId = ?', $employeeId);
	    	}
	    	
	    	$q->orderBy('pr.id');
	    	$performanceReviewList = $q->execute();
            return  $performanceReviewList ;
        }catch( Exception $e)
        {
            throw new DaoException ( $e->getMessage() );
        }
    }
    
    public function checkReviewExist($fromDate, $toDate, $employeeId = null){
    	try
        {	
	    	$pdo = Doctrine_Manager::connection()->getDbh();
	    	$qEmpStr = '';
	    	if(!is_null($employeeId)){
	    		$qEmpStr = ' AND employee_id = '.$employeeId;
	    	}
	    	
	    	$sql = 'SELECT count(id) as cnt FROM hs_hr_performance_review WHERE
                period_from = "'.date('Y-m-d',strtotime($fromDate)).'" 
                AND period_to = "'.date('Y-m-d',strtotime($toDate)).'" 
                '.$qEmpStr.' 
                AND is_deleted = 0 
                ORDER BY id';
            $res = $pdo->query($sql);
            $cycleDates = $res->fetchAll();
            if( $cycleDates[0]['cnt'] > 0)
            	return true;
            else 
            	return false;
        }catch( Exception $e){
            throw new DaoException ( $e->getMessage() );
        }
    }
    /**
     * Get Performance Review List
     * @return unknown_type
     */
    public function getPerformanceReviewList( )
    {
        try
        {
            $q = Doctrine_Query::create()
                ->from('PerformanceReview pr')
                ->where('pr.isDeleted = 0')
                ->orderBy('pr.id');

            $performanceReviewList = $q->execute();

            return  $performanceReviewList ;

        }catch( Exception $e)
        {
            throw new DaoException ( $e->getMessage() );
        }
    }
    
    /**
     * Get Performance cycle dates
     * @return unknown_type
     */
    public function getPerformanceCycleDate(){
	    try
		{
			$pdo = Doctrine_Manager::connection()->getDbh();
            $res = $pdo->query('SELECT DISTINCT period_from, period_to FROM hs_hr_performance_review');
            $cycleDates = $res->fetchAll();
		    return  $cycleDates;
		}catch( Exception $e)
		{
			throw new DaoException ( $e->getMessage() );
		}
    }
    

  
    /**
     * Builds the search query that fetches all the
     * records for given search clues
     */
    private function _getSearchReviewQuery($clues) {

        try {

            $from = $clues['from'];
            $to = $clues['to'];
            $jobCode = $clues['jobCode'];
            $divisionId = $clues['divisionId'];
            $empId = $clues['empId'];
            $reviewerId = $clues['reviewerId'];
            $state = $clues['state'];
            $customerId = $clues['customerId'];
            $directReview = $clues['directReview'];
            if (isset($clues['loggedEmpId'])) {
                $empId = $clues['loggedEmpId'];
            }
            if (isset($clues['loggedReviewerId'])) {
            	$reviewerId = $clues['loggedReviewerId'];
            }

            $q = Doctrine_Query::create()
                 ->from('PerformanceReview p')
                 ->leftJoin('p.Employee e')
                 ->leftJoin('p.JobTitle j')
                 ->leftJoin('p.PerformanceReviewReviewer r')
                 ->leftJoin('e.EmployeeProject prj');
            
            if (!empty($from)) {
                $q->andWhere("p.periodFrom >= ?", $from);
            }

            if (!empty($to)) {
                $q->andWhere("p.periodTo <= ?", $to);
            }

            if (!empty($empId)) {
                $q->andWhere("p.employeeId = ?", $empId);
            }
            
            if(!empty($customerId)){
            	$q->andWhere("prj.customerId = ?", $customerId);
            }
            
        	/*Get unique employees and only those whose reviewer is $reviewerId when login as reviewer*/
            if (!empty($directReview)) {
            	$q->andWhere("r.is_primary = ?", PerformanceReviewReviewer::IS_PRIMARY_REVIEWER);
            	if($reviewerId == 0){ // Means default admin
            		$adminId = sfContext::getInstance()->getUser()->getEmployeeNumber();
            		$q->andWhere("r.reviewerId = ?", $adminId);
            	
            	}elseif (!empty($reviewerId) && $reviewerId != 0 ) {
            		$q->andWhere("r.reviewerId = ?", $reviewerId);
            	}
            }

            $usr = sfContext::getInstance()->getUser()->getEmployeeNumber();
            $includeSelfReviews = true;
            if (!empty($reviewerId) && $reviewerId != 0 ) {
            	if($usr != $reviewerId) {
            		$includeSelfReviews = false;
            	}
            	$usr = $reviewerId;
            }
            
            $ids = $this->getReviewIdsForReviewer($usr, $includeSelfReviews, $from , $to);
 			if(!(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 'Yes') || !empty($reviewerId)) {
 				$q->andWhereIn('p.id', $ids);
 			}

            if (!empty($jobCode)) {
                $q->andWhere("e.job_title_code = ?", $jobCode);
            }
            
            if (!empty($state)) {
	            $q->andWhere("p.state = ?", $state);
            }

            if (!empty($divisionId)) {
                $q->andWhere("p.subDivisionId = ?", $divisionId);
            }
			$q->andWhere("p.isDeleted = 0 ");
			$q->andWhere("r.isDeleted = 0 ");
			return $q;

        } catch(Exception $e) {
            throw new DaoException($e->getMessage());
        }

    }

    /**
     * Returns Object based on the combination of search
     * @param array $clues
     * @param array $offset
     * @param array $limit
     * @throws DaoException
     */
     
    public function searchPerformanceReview($clues, $offset=null, $limit=null) {
        try {

            $q = $this->_getSearchReviewQuery($clues);

            if (isset($offset) && isset($limit)) {
                $q->offset($offset)->limit($limit);
            }
            
            if(isset($clues['sortBy'])){
            	$q->orderBy($clues['sortBy'].' '.$clues['Order']);
            }
            return $q->execute();

        } catch(Exception $e) {
            throw new PerformanceServiceException($e->getMessage());
        }

    }

    /**
     * Returns the count of records
     * that matched given $clues
     */

    public function countReviews($clues) {

        try {

            $q = $this->_getSearchReviewQuery($clues);

            return $q->count();

        } catch(Exception $e) {
            throw new PerformanceServiceException($e->getMessage());
        }

    }
    
     /**
     * Update status of performance review
     * @param array $clues
     * @param array $offset
     * @param array $limit
     * @throws DaoException
     */
    public function updatePerformanceReviewStatus( PerformanceReview $performanceReview , $status){
    	try {
             $q = Doctrine_Query::create()
				    ->update('PerformanceReview')
				    ->set("state='?'", $status)
				    ->where("id = ?",$performanceReview->getId());
                $q->execute();
                
                return true ;
			
        } catch(Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    

    public function saveFeedback(EmployeeMultiSourceFeedback $feedback){
    	try {
    		$feedback->save();
    		return $feedback->getId();
    	} catch(Exception $e) {
    		throw new DaoException($e->getMessage());
    	}
    }
    
    public function updateFeedback(EmployeeMultiSourceFeedback $feedback, $fid){
    	try {
    		$q = Doctrine_Query::create()
    		->update('EmployeeMultiSourceFeedback')
    		->set('positiveFeedback','?',$feedback->getPositiveFeedback())
    		->set('negativeFeedback','?',$feedback->getNegativeFeedback())
    		->set('fromDate','?',$feedback->getFromDate())
    		->set('toDate','?',$feedback->getToDate())
    		->set('isSubmitted','?',$feedback->isSubmitted)
    		->set('updatedDate','?',date('Y-m-d'))
    		->set('updatedBy','?',sfContext::getInstance()->getUser()->getEmployeeNumber())
    		->set('isAnonymous','?',$feedback->getIsAnonymous())
    		->where('id = ?', $fid);
    		$q->execute();
    		return $fid;
    	} catch(Exception $e) {
    		throw new DaoException($e->getMessage());
    	}
    }
    
    public function deleteFeedback($fidList){
    	try {
    		$q = Doctrine_Query::create()
    		->update('EmployeeMultiSourceFeedback')
    		->set('updatedDate','?',date('Y-m-d'))
    		->set('updatedBy','?',sfContext::getInstance()->getUser()->getEmployeeNumber())
    		->set('isDeleted','?',EmployeeMultiSourceFeedback::IS_DELETED)
    		->whereIn('id',$fidList);
    		$numDeleted = $q->execute();
    		return true;
    	} catch(Exception $e) {
    		throw new DaoException($e->getMessage());
    	}
    }
    
    public function discardFeedback($fid){
    	try {
    		$q = Doctrine_Query::create()
    		->update('EmployeeMultiSourceFeedback')
    		->set('isDeleted','?',EmployeeMultiSourceFeedback::IS_DELETED)
    		->where('id = ?', $fid);
    		$q->execute();
    		return $fid;
    	} catch(Exception $e) {
    		throw new DaoException($e->getMessage());
    	}
    }
    
    public function getFeedbackById($id){
    	try {
    		return Doctrine :: getTable('EmployeeMultiSourceFeedback')->find($id);
    	} catch(Exception $e) {
    		throw new DaoException($e->getMessage());
    	}
    }
    
    public function getReviewedEmployees($usrNumber){
    	try {
    		$q = Doctrine_Query::create()
    		->select('empNumber')->distinct()
    		->from('EmployeeMultiSourceFeedback')
    		->where('createdBy = ?', $usrNumber)
    		->orWhere('empNumber = ?', $usrNumber);
    		return $q->execute();
    	} catch(Exception $e) {
    		throw new DaoException($e->getMessage());
    	}
    }
    
    public function buildFeedbackSearchQuery(MyFeedbackSearchParameters $searchParam , $isCount, $currCycle){
    	$usr = sfContext::getInstance()->getUser()->getEmployeeNumber();
    	$query = "SELECT fb.id, fb.emp_number, fb.reviewers_number, fb.from_date, fb.to_date , fb.is_anonymous, fb.is_submitted, CONCAT_WS(' ', emp.emp_firstname, emp.emp_lastname) as empName,";
    	$query.= " CONCAT_WS(' ', rev.emp_firstname, rev.emp_lastname) as revName ";
    	$query.= " from hs_hr_emp_360_feedback fb";
    	$query.= " LEFT JOIN hs_hr_employee emp ON fb.emp_number = emp.emp_number";
    	$query.= " LEFT JOIN hs_hr_employee rev ON fb.reviewers_number = rev.emp_number ";
    	$query.= " WHERE ( fb.from_date >= '".$searchParam->getFromDate()."' AND fb.to_date <='".$searchParam->getToDate()."' )";
    	if(!is_null($searchParam->getEmpNumber())) {
    		$query.= " AND fb.emp_number = ".$searchParam->getEmpNumber()." ";
    	}
    	$query.= " AND fb.is_deleted = 0 ";
    	if(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 'Yes') {
    
    	} else {
    		$accessibleEmployees = UserRoleManagerFactory::getUserRoleManager()->getAccessibleEntityIds('Employee');
    		array_push($accessibleEmployees, strval($usr));
    		$empList = $this->getEmpNumbersForFeedback($currCycle);
    		foreach ($empList as $emp) {
    			array_push($accessibleEmployees, $emp[0]);
    		}
    		$employeeList = implode(',', $accessibleEmployees);
    		$query.= "AND (fb.emp_number IN (".$employeeList.") OR fb.reviewers_number = ".$usr." OR fb.emp_number = ".$usr. " ) ";
    	}
    	
     	if((isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 'Yes') && isset($_SESSION['empNumber'])) {
     		$admin = $_SESSION['empNumber'];
    		$query.=" AND is_submitted = 1 OR (fb.reviewers_number = ".$admin." AND is_submitted = 0 AND fb.is_deleted = 0 ) ";
    	} 
    	if(!(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 'Yes')) {
    		$query.=" AND is_submitted = 1 OR (fb.reviewers_number = ".$usr." AND is_submitted = 0 AND fb.is_deleted = 0 ) ";
    	}
    	$query.= " ORDER BY ".$searchParam->getSortField()." ". $searchParam->getSortOrder(). " ";
    	if(!$isCount) {
    		$query.=  " LIMIT " . $searchParam->getOffset() . ", " .$searchParam->getLimit();
    	}
    	return $query;
    }
    
    public function searchfeedback($query ,$isCount = false){
    	try {
    		$pdo = Doctrine_Manager::connection()->getDbh();
    		$res = $pdo->query($query);
    		$feedbackList = $res->fetchAll();
    		$feedbackListArray = array();
    		$usr = sfContext::getInstance()->getUser()->getEmployeeNumber();
    		foreach ($feedbackList as $feedback) {
    			$param = new MyFeedbackSearchParameters();
    			$param->setId($feedback['id']);
    			$name = $feedback['empName'];
    			if($feedback['is_submitted'] == 0) {
    				$name.= ' [Draft]';
    			}
    			$param->setEmpName($name);
    			
    			
    			if($feedback['emp_number'] == $usr){
	    			$param->setReviewerName('Anonymous');
    			}else if($feedback['reviewers_number'] == $usr){
	    			$param->setReviewerName($feedback['revName']);
    			}else{
	    			if(empty($feedback['is_anonymous'])) {
		    			$param->setReviewerName($feedback['revName']);
	    			} else {
		    			$param->setReviewerName('Anonymous');
	    			}
    			}

				if($usr == intval($feedback['emp_number'])) {
					continue;
				}
    			
    			$param->setFromDate($feedback['from_date']);
    			$param->setToDate($feedback['to_date']);
    			$param->setEmpNumber($feedback['emp_number']);
    			$param->getReviewer($feedback['reviewers_number']);
    			$param->setReviewPeriod(date('d/m/Y', strtotime($feedback['from_date'])) .' to '
    					. date('d/m/Y', strtotime($feedback['to_date'])));
    			$feedbackListArray[] = $param;
    		}
    
    		if(!$isCount) {
    			return $feedbackListArray;
    		} else {
    			return count($feedbackListArray);
    		}
    	} catch (Exception $e) {
    		throw new DaoException($e->getMessage());
    	}
    }
    
    //to be deleted
    public function getCountSearchFeedback($query) {
    	try{
    		$pdo = Doctrine_Manager::connection()->getDbh();
    		$res = $pdo->query($query);
    		$count = $res->fetchColumn(0);
    		return intval($count);
    	} catch (Exception $e) {
    		throw new DaoException($e->getMessage());
    	}
    }
    
    public function getAllFeedback($fromDate = '', $toDate = '', $employeeId = ''){
    	try {
    		$q = Doctrine_Query::create()
    		->from('EmployeeMultiSourceFeedback')
    		->where('isDeleted = ?', EmployeeMultiSourceFeedback::IS_NOT_DELETED)
    		->andWhere('isSubmitted = ?', EmployeeMultiSourceFeedback::IS_SUBMITTED);
    		if($employeeId != ''){
    		    $q->andWhere('reviewersNumber = ?',$employeeId);  
    		}
    		if($fromDate != ''){
    		    $q->andWhere('fromDate = ?' ,$fromDate);
    		}
    		if($toDate != ''){
				$q->andWhere('toDate = ?', $toDate);
    		}
    		return $q->execute();
    	} catch (Exception $e) {
    		throw new DaoException($e->getMessage());
    	}
    }
    
    
    
    public function getPerformanceAttachments($reviewId){
	    try {
		    $q = Doctrine_Query :: create()
			    ->from('PerformanceAttachment')
				->where('reviewId =?', $reviewId)
				->orderBy('fileName ASC');
		    return $q->execute();
	    } catch (Exception $e) {
		    throw new DaoException($e->getMessage());
	    }
    }
    
    /**
     *
     * @param <type> $attachId
     * @return <type>
     */
    public function getPerformanceAttachment($attachId) {
	    try {
		    $q = Doctrine_Query:: create()
			    ->from('PerformanceAttachment a')
				->where('a.id = ?', $attachId);
		    return $q->fetchOne();
	    } catch (Exception $e) {
		    throw new DaoException($e->getMessage());
	    }
    }
    
    /**
     *
     * @param JobVacancyAttachment $attachment
     * @return <type>
     */
    public function saveAttachment(PerformanceAttachment $performanceAttachment) {
	    try {
		    $performanceAttachment->save();
		    return true;
	    } catch (Exception $e) {
		    throw new DaoException($e->getMessage());
	    }
    }
    
    
    

    /**
     *
     * @param EmployeeMultiSourceFeedback $feedback
     * @return <type>
     */
    public function getMultiSourceFeedback($fromDate, $toDate, $employeeId, $reviewerId = null) {
    	try {
	    	$q = Doctrine_Query::create()
		    	->from('EmployeeMultiSourceFeedback')
				->where('fromDate = ?' ,$fromDate)
				->andWhere('toDate = ?', $toDate)
				->andWhere('empNumber = ?',$employeeId);
			
			if(!is_null($reviewerId)){
				$q->andWhere('reviewersNumber = ?',$reviewerId);
			}
	    	
	    	$q->andWhere('isDeleted = ?',EmployeeMultiSourceFeedback::IS_NOT_DELETED);
	    	$q->andWhere('isSubmitted = ?',EmployeeMultiSourceFeedback::IS_SUBMITTED);
    		return $q->execute();
    	} catch (Exception $e) {
    		throw new DaoException($e->getMessage());
    	}
    }
    
    /**
     *
     * @param EmployeeMultiSourceFeedback
     * @return <type>
     */
    public function getMultiSourceFeedbackByReviewer($fromDate, $toDate, $reviewerId) {
    	try {
    		$q = Doctrine_Query::create()
    		  ->from('EmployeeMultiSourceFeedback')
    		  ->where('fromDate = ?' ,$fromDate)
    		  ->andWhere('toDate = ?', $toDate)
    		  ->andWhere('reviewersNumber = ?',$reviewerId)
    		  ->andWhere('isDeleted = ?',EmployeeMultiSourceFeedback::IS_NOT_DELETED)
    		  ->andWhere('isSubmitted = ?',EmployeeMultiSourceFeedback::IS_SUBMITTED);    		
    		return $q->execute();
    	} catch (Exception $e) {
    		throw new DaoException($e->getMessage());
    	}
    }
    
    
    /**
     *
     * @param Array $idsToApprove
     * @return <Integer>
     */
    public function approvePerformanceReviews($idsToApprove) {
    	$cnt = 0;
    	try {
	    	foreach ($idsToApprove as $id) {
	    		$q = Doctrine_Query::create()
	    		->update('PerformanceReview')
	    		->set('state','?',PerformanceReview::PERFORMANCE_REVIEW_STATUS_APPROVED)
	    		->where('id = ?', $id);
	    		$q->execute();
	    		$cnt++;
    		}
    		return $cnt;
    	} catch (Exception $e) {
    		return null;
    		throw new DaoException($e->getMessage());
    	}
    }
    
    /**
     * Get current performance cycle
     */
    public function getCurrentPerformancePeriod(){
	    try {
		    $q = Doctrine_Query::create()
			    ->from("PerformancePeriod period")
				->addOrderBy("period.createdDate DESC")
				->addOrderBy("id DESC");
		    
		    return $q->fetchOne();
		    // @codeCoverageIgnoreStart
	    } catch (Exception $e) {
		    throw new DaoException($e->getMessage(), $e->getCode(), $e);
	    }
    }
    
    /**
     * Get all performance periods
     */
    public function getAllPerformancePeriods(){
    	try {
            $q = Doctrine_Query::create()
                    ->from("PerformancePeriod period")
                    ->addOrderBy("period.createdDate DESC")
                    ->addOrderBy("id DESC");
           
            return $q->execute();
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * Get performance period by date
     */
    public function getPerformancePeriodByDate($fromDate,$toDate){
	    try {
		    $q = Doctrine_Query::create()
			    ->from("PerformancePeriod period")
				->where("period.periodFrom = ?",$fromDate)
				->addWhere("period.periodTo = ?",$toDate);
		    
		    return $q->fetchOne();
		    // @codeCoverageIgnoreStart
	    } catch (Exception $e) {
		    throw new DaoException($e->getMessage(), $e->getCode(), $e);
	    }
    }
    
    public function getLatestPerformancePeriodCycle() {
    	try {
    		$q = Doctrine_Query::create()
    			->from('PerformancePeriod')
    			->orderBy('id desc')
    			->limit(1);
    		return $q->fetchOne();
    	} catch (Exception $e) {
    		throw new DaoException($e->getMessage(), $e->getCode(), $e);
    	}
    }
    
    public function checkForUniquePerformanceCycle($performancePeriod) {
    	try {
    		$q = Doctrine_Query::create()
    		->from('PerformancePeriod')
    		->where('period_from >= ?', $performancePeriod->getPeriodFrom())
    		->andWhere('period_to <= ?', $performancePeriod->getPeriodTo());
    		$results = $q->execute();
    		if(count($results) > 0) {
    			return false;
    		} else {
    			return true;
    		}
    	} catch (Exception $e) {
    		throw new DaoException($e->getMessage(), $e->getCode(), $e);
    	}
    }
    
    public function savePreviousObjective($reviewId, $goal) {
    	try {
    		$q = Doctrine_Query::create()
    			->update('PerformanceReview')
    			->set('previous_objective','?',$goal)
    			->where('id = ?', $reviewId);
    		$result = $q->execute();
    		if(!empty($result)) {
    			return $goal;
    		} else {
    			return 0;
    		}

    	} catch (Exception $e) {
    		throw new DaoException($e->getMessage(), $e->getCode(), $e);
    	}
    }
    public function emptyFinalRating($reviewId) {
    	try {
    		$q = Doctrine_Query::create()
    		->update('PerformanceReview')
    		->set('final_rating = null')
    		->where('id = ?', $reviewId);
    		$result = $q->execute();
    
    	} catch (Exception $e) {
    		throw new DaoException($e->getMessage(), $e->getCode(), $e);
    	}
    }
    
    
    public function getEmpNumbersForFeedback($currCycle) {
    	$usr = sfContext::getInstance()->getUser()->getEmployeeNumber();
    	$pdo = Doctrine_Manager::connection()->getDbh();
    	$query = "SELECT DISTINCT(r.employee_id) from hs_hr_performance_review r LEFT JOIN hs_hr_performance_review_reviewer rr ON r.id = rr.review_id  ";
    	$query.= "WHERE rr.is_deleted = 0 AND r.is_deleted = 0 AND r.period_from = '".$currCycle->periodFrom."' AND rr.reviewer_id = ".$usr;
    	$res = $pdo->query($query);
    	return $res->fetchAll();
    }
    
    public function getReviewIdsForReviewer($usr, $includeSelfReviews, $from , $to) {
    	$array = array();
    	$pdo = Doctrine_Manager::connection()->getDbh();
    	$query = "SELECT p.id from hs_hr_performance_review p, hs_hr_performance_review_reviewer rr where p.id = rr.review_id and (rr.reviewer_id = ".$usr;
    	if($includeSelfReviews){
    		$query.= " OR p.employee_id = ".$usr;
    	}
    	$query.= " ) AND rr.is_deleted = 0 AND p.is_deleted = 0 AND p.period_from = '".$from."' AND p.period_to = '".$to."'";
    	$dquery = $pdo->query($query);
    	$results = $dquery->fetchAll();
    	foreach ($results as $res) {
    		array_push($array, $res[0]);
    	}
    	return $array;
    }
    /* Get current cycle performace reviwer record  */
    public function getEmployeeCurrentCyclePerformance($empNumber)
    {
        $pdo = Doctrine_Manager::connection()->getDbh();       
    	$query = "SELECT r.id, rr.reviewer_id as reviewer_id,rr.id as reviwer_record,rr.is_primary as is_primary,r.period_from,r.period_to from hs_hr_performance_review r LEFT JOIN hs_hr_performance_review_reviewer rr ON r.id = rr.review_id  ";
    	$query.= "WHERE rr.is_deleted = 0 and r.is_deleted = 0 AND (DATE_FORMAT(curdate(),'%Y-%m-%d') between DATE_FORMAT(r.period_from,'%Y-%m-%d') AND  DATE_FORMAT(r.period_to,'%Y-%m-%d') )  AND r.employee_id = ".$empNumber;
        $query.= " Order By r.id";
    	$res = $pdo->query($query);       
    	$resObj=$res->fetchAll(PDO::FETCH_ASSOC);
        return $resObj;
    } 
    /* Get current cycle primary reviewer  */
    public function getEmployeeCurrentCyclePrimaryReviwer($empNumber) {  	
    	
    	$resObj=$this->getEmployeeCurrentCyclePerformance($empNumber); 
        $primaryReviwer='';       
        if(!empty($resObj)) {
            foreach($resObj as $res) 
            {
                if($res['is_primary'])
                $primaryReviwer=$res['reviewer_id'];
            }
        }    
       return $primaryReviwer;
    }
    /* DESC:- It just unset the preimary reviewer status add this in secodary reviewer list  */
    public function removePrimaryReviewerStatus($empNumber,$selectedPrimaryReviwer)
    {
        $pdo = Doctrine_Manager::connection()->getDbh();       
        $resObj=$this->getEmployeeCurrentCyclePerformance($empNumber); 
        $reviwerRecordId='';
        $primaryReviwer='';
        $id='';
        $primaryStatus=false;
        if(!empty($resObj)) {
            foreach($resObj as $res) 
            {    $id=$res['id'];
                 $reviwerRecordId=$res['reviwer_record'];
                 $primaryReviwer=$res['reviewer_id']; 
                if($res['is_primary'] && $primaryReviwer==$selectedPrimaryReviwer) {  
                    $query="UPDATE hs_hr_performance_review_reviewer SET is_primary=0 WHERE id=".$reviwerRecordId;
                    $res = $pdo->query($query);       
                    $resObj=$res->execute();
                   
                }
            } 
        }    
    }
    
    public function getPrimaryReviewerByReviewId($reviewId) {
    	$array = array();
    	$reviewers=Doctrine::getTable('PerformanceReviewReviewer')->findByReviewId($reviewId);
    	foreach($reviewers as $reviewer) {
    		//return object for multiple properties  
    		if($reviewer->isPrimary == PerformanceReviewReviewer::IS_PRIMARY_REVIEWER
    				&& $reviewer->isDeleted == PerformanceReviewReviewer::IS_NOT_DELETED ){
    			return $reviewer;
    		}
    	}
    	return null;
    }
    
    public function getSecodaryReviewerByReviewId($reviewId) {
    	$array = array();
    	$reviewers=Doctrine::getTable('PerformanceReviewReviewer')->findByReviewId($reviewId);
    	foreach($reviewers as $reviewer) {
    		if($reviewer->isPrimary == PerformanceReviewReviewer::IS_NOT_PRIMARY_REVIEWER
    				&& $reviewer->isDeleted == PerformanceReviewReviewer::IS_NOT_DELETED ){
    			array_push($array, $reviewer->getReviewer()->getFirstAndLastNames());
    		}
    	}
    	return implode(', ',$array);
    }
    
    /* Get current cycle performace reviwer record  */
    public function getEmployeePerformanceReview($empNumber,$fromDate,$toDate) {
    	$pdo = Doctrine_Manager::connection()->getDbh();
    	$query = "SELECT r.id, rr.reviewer_id as reviewer_id,rr.id as reviwer_record,rr.is_primary as is_primary,r.period_from,r.period_to from hs_hr_performance_review r 
    			LEFT JOIN hs_hr_performance_review_reviewer rr ON r.id = rr.review_id  ";
    	$query.= "WHERE  r.is_deleted = 0 AND r.period_from = '".$fromDate."' AND r.period_to = '".$toDate."' AND r.employee_id = ".$empNumber;
    	$query.= " Order By r.id";
    	$res = $pdo->query($query);
    	$resObj=$res->fetchAll(PDO::FETCH_ASSOC);
    	return $resObj;
    }
}